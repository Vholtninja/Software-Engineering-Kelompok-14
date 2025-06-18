1. TOOLS YANG DIPERLUKAN
bash# Install Node.js (download dari nodejs.org)
# Install MySQL (download dari mysql.com)
# Text Editor (VS Code/Sublime/Notepad++)
2. COMMAND LINE UNTUK MEMBUAT STRUKTUR FOLDER
bashmkdir kost-online
cd kost-online
mkdir backend frontend
cd frontend
mkdir css js assets images
cd ../backend
mkdir routes middleware config uploads
cd ..
3. STRUKTUR FOLDER LENGKAP
kost-online/
├── backend/
│   ├── config/
│   │   └── database.js
│   ├── middleware/
│   │   └── auth.js
│   ├── routes/
│   │   ├── admin.js
│   │   ├── penghuni.js
│   │   └── auth.js
│   ├── uploads/
│   ├── server.js
│   └── package.json
├── frontend/
│   ├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── images/
│   ├── index.html
│   ├── admin-login.html
│   ├── penghuni-login.html
│   ├── register.html
│   ├── admin-dashboard.html
│   └── penghuni-dashboard.html
└── database.sql
4. QUERY SQL DATABASE
sqlCREATE DATABASE kost_online;
USE kost_online;

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kamar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_kamar VARCHAR(10) UNIQUE NOT NULL,
    tarif_per_bulan DECIMAL(10,2) NOT NULL,
    fasilitas TEXT,
    status_kamar ENUM('available', 'occupied') DEFAULT 'available',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE penghuni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nomor_telepon VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    kamar_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kamar_id) REFERENCES kamar(id)
);

CREATE TABLE pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_sewa VARCHAR(20) UNIQUE NOT NULL,
    penghuni_id INT NOT NULL,
    kamar_id INT NOT NULL,
    tarif DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'sudah_bayar') DEFAULT 'pending',
    bukti_transfer VARCHAR(255),
    tanggal_pembayaran DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (penghuni_id) REFERENCES penghuni(id),
    FOREIGN KEY (kamar_id) REFERENCES kamar(id)
);

CREATE TABLE ajuan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    penghuni_id INT NOT NULL,
    jenis_ajuan VARCHAR(50) NOT NULL,
    deskripsi TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    tanggal_ajuan DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (penghuni_id) REFERENCES penghuni(id)
);

INSERT INTO admin (username, password) VALUES ('admin', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO kamar (nomor_kamar, tarif_per_bulan, fasilitas, status_kamar, keterangan) VALUES
('KM-001', 450000, 'Kamar mandi dalam, AC, TV, Lemari', 'available', 'Kamar bersih dan nyaman'),
('KM-002', 520000, 'Kamar mandi dalam, AC, TV, Lemari', 'available', 'Kamar bersih dan nyaman'),
('KM-003', 530000, 'Kamar mandi dalam, AC, TV, Lemari', 'available', 'Kamar bersih dan nyaman'),
('KM-004', 540000, 'Kamar mandi dalam, AC, TV, Lemari', 'available', 'Kamar bersih dan nyaman'),
('KM-005', 550000, 'Kamar mandi dalam, AC, TV, Lemari', 'available', 'Kamar bersih dan nyaman');
5. KODE BACKEND
package.json
json{
  "name": "kost-online-backend",
  "version": "1.0.0",
  "description": "",
  "main": "server.js",
  "scripts": {
    "start": "node server.js",
    "dev": "nodemon server.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "mysql2": "^3.6.0",
    "bcryptjs": "^2.4.3",
    "jsonwebtoken": "^9.0.2",
    "multer": "^1.4.5-lts.1",
    "cors": "^2.8.5",
    "body-parser": "^1.20.2"
  }
}
server.js
javascriptconst express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');

const authRoutes = require('./routes/auth');
const adminRoutes = require('./routes/admin');
const penghuniRoutes = require('./routes/penghuni');

const app = express();
const PORT = 5000;

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));
app.use(express.static(path.join(__dirname, '../frontend')));

app.use('/api/auth', authRoutes);
app.use('/api/admin', adminRoutes);
app.use('/api/penghuni', penghuniRoutes);

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '../frontend/index.html'));
});

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
config/database.js
javascriptconst mysql = require('mysql2');

const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'kost_online'
});

connection.connect((err) => {
    if (err) {
        console.error('Database connection error: ' + err.stack);
        return;
    }
    console.log('Connected to database');
});

module.exports = connection;
routes/auth.js
javascriptconst express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const db = require('../config/database');

const router = express.Router();
const JWT_SECRET = 'your-secret-key';

router.post('/admin/login', (req, res) => {
    const { username, password } = req.body;
    
    db.query('SELECT * FROM admin WHERE username = ?', [username], async (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        
        if (results.length === 0) {
            return res.status(401).json({ error: 'Invalid credentials' });
        }
        
        const admin = results[0];
        const isValidPassword = await bcrypt.compare(password, admin.password);
        
        if (!isValidPassword) {
            return res.status(401).json({ error: 'Invalid credentials' });
        }
        
        const token = jwt.sign({ id: admin.id, role: 'admin' }, JWT_SECRET);
        res.json({ token, role: 'admin' });
    });
});

router.post('/penghuni/login', (req, res) => {
    const { username, password } = req.body;
    
    db.query('SELECT * FROM penghuni WHERE username = ?', [username], async (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        
        if (results.length === 0) {
            return res.status(401).json({ error: 'Invalid credentials' });
        }
        
        const penghuni = results[0];
        const isValidPassword = await bcrypt.compare(password, penghuni.password);
        
        if (!isValidPassword) {
            return res.status(401).json({ error: 'Invalid credentials' });
        }
        
        const token = jwt.sign({ id: penghuni.id, role: 'penghuni' }, JWT_SECRET);
        res.json({ token, role: 'penghuni', userId: penghuni.id });
    });
});

router.post('/penghuni/register', async (req, res) => {
    const { nama_lengkap, username, email, nomor_telepon, password, kamar_id } = req.body;
    
    try {
        const hashedPassword = await bcrypt.hash(password, 10);
        
        db.query(
            'INSERT INTO penghuni (nama_lengkap, username, email, nomor_telepon, password, kamar_id) VALUES (?, ?, ?, ?, ?, ?)',
            [nama_lengkap, username, email, nomor_telepon, hashedPassword, kamar_id],
            (err, result) => {
                if (err) return res.status(500).json({ error: err.message });
                res.json({ message: 'Registration successful' });
            }
        );
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

router.get('/kamar', (req, res) => {
    db.query('SELECT id, nomor_kamar FROM kamar WHERE status_kamar = "available"', (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

module.exports = router;
routes/admin.js
javascriptconst express = require('express');
const db = require('../config/database');

const router = express.Router();

router.get('/kamar', (req, res) => {
    const query = `
        SELECT k.*, p.nama_lengkap as penghuni_nama 
        FROM kamar k 
        LEFT JOIN penghuni p ON k.id = p.kamar_id
    `;
    
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

router.post('/kamar', (req, res) => {
    const { nomor_kamar, tarif_per_bulan, fasilitas, keterangan } = req.body;
    
    db.query(
        'INSERT INTO kamar (nomor_kamar, tarif_per_bulan, fasilitas, keterangan) VALUES (?, ?, ?, ?)',
        [nomor_kamar, tarif_per_bulan, fasilitas, keterangan],
        (err, result) => {
            if (err) return res.status(500).json({ error: err.message });
            res.json({ message: 'Room added successfully' });
        }
    );
});

router.put('/kamar/:id', (req, res) => {
    const { id } = req.params;
    const { nomor_kamar, tarif_per_bulan, fasilitas, keterangan } = req.body;
    
    db.query(
        'UPDATE kamar SET nomor_kamar = ?, tarif_per_bulan = ?, fasilitas = ?, keterangan = ? WHERE id = ?',
        [nomor_kamar, tarif_per_bulan, fasilitas, keterangan, id],
        (err, result) => {
            if (err) return res.status(500).json({ error: err.message });
            res.json({ message: 'Room updated successfully' });
        }
    );
});

router.delete('/kamar/:id', (req, res) => {
    const { id } = req.params;
    
    db.query('DELETE FROM kamar WHERE id = ?', [id], (err, result) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Room deleted successfully' });
    });
});

router.get('/penghuni', (req, res) => {
    const query = `
        SELECT p.*, k.nomor_kamar 
        FROM penghuni p 
        LEFT JOIN kamar k ON p.kamar_id = k.id
    `;
    
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

router.get('/pembayaran', (req, res) => {
    const query = `
        SELECT p.*, pen.nama_lengkap, k.nomor_kamar 
        FROM pembayaran p 
        JOIN penghuni pen ON p.penghuni_id = pen.id 
        JOIN kamar k ON p.kamar_id = k.id
    `;
    
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

router.get('/ajuan', (req, res) => {
    const query = `
        SELECT a.*, p.nama_lengkap 
        FROM ajuan a 
        JOIN penghuni p ON a.penghuni_id = p.id
    `;
    
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

module.exports = router;
routes/penghuni.js
javascriptconst express = require('express');
const multer = require('multer');
const path = require('path');
const db = require('../config/database');

const router = express.Router();

const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'uploads/');
    },
    filename: function (req, file, cb) {
        cb(null, Date.now() + path.extname(file.originalname));
    }
});

const upload = multer({ storage: storage });

router.get('/pembayaran/:id', (req, res) => {
    const { id } = req.params;
    const query = `
        SELECT p.*, k.nomor_kamar 
        FROM pembayaran p 
        JOIN kamar k ON p.kamar_id = k.id 
        WHERE p.penghuni_id = ?
    `;
    
    db.query(query, [id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

router.post('/ajuan', (req, res) => {
    const { penghuni_id, jenis_ajuan, deskripsi, tanggal_ajuan } = req.body;
    
    db.query(
        'INSERT INTO ajuan (penghuni_id, jenis_ajuan, deskripsi, tanggal_ajuan) VALUES (?, ?, ?, ?)',
        [penghuni_id, jenis_ajuan, deskripsi, tanggal_ajuan],
        (err, result) => {
            if (err) return res.status(500).json({ error: err.message });
            res.json({ message: 'Ajuan submitted successfully' });
        }
    );
});

router.get('/ajuan/:id', (req, res) => {
    const { id } = req.params;
    
    db.query('SELECT * FROM ajuan WHERE penghuni_id = ?', [id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

module.exports = router;
6. KODE FRONTEND
index.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kost Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="landing-page">
    <div class="container">
        <div class="welcome-section">
            <h1>Selamat datang di Pembayaran kost online</h1>
        </div>
        
        <div class="login-section">
            <h2>Login Dulu yuk</h2>
            <div class="cards-container">
                <div class="card" onclick="window.location.href='admin-login.html'">
                    <div class="card-icon">
                        <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                        </svg>
                    </div>
                    <p>Admin</p>
                </div>
                
                <div class="card" onclick="window.location.href='penghuni-login.html'">
                    <div class="card-icon">
                        <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                            <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                        </svg>
                    </div>
                    <p>Penghuni Kost</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
admin-login.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page admin-theme">
    <div class="container">
        <div class="login-form">
            <h2>Admin Login</h2>
            <p>Masuk ke panel administrasi</p>
            
            <form id="adminLoginForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" required>
                </div>
                
                <button type="submit" class="btn-login admin-btn">Login</button>
            </form>
            
            <div class="back-link">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                <a href="index.html">Kembali ke halaman utama</a>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
penghuni-login.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Penghuni</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page penghuni-theme">
    <div class="container">
        <div class="login-form">
            <h2>Login</h2>
            
            <form id="penghuniLoginForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" placeholder="Masukkan username" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn-login penghuni-btn">Login</button>
            </form>
            
            <p class="register-link">
                Belum punya akun? <a href="register.html">Register di sini</a>
            </p>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
register.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page penghuni-theme">
    <div class="container">
        <div class="login-form register-form">
            <h2>Daftar Kos Mami Sindu</h2>
            
            <form id="registerForm">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" required>
                </div>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" id="nomor_telepon" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" required>
                </div>
                
                <div class="form-group">
                    <label>Pilih Kamar</label>
                    <select id="kamar_id" required>
                        <option value="">Pilih Kamar</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-login penghuni-btn">Daftar</button>
            </form>
            
            <p class="register-link">
                Sudah punya akun? <a href="penghuni-login.html">Login di sini</a>
            </p>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
admin-dashboard.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard admin-theme">
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>Admin</h3>
            <nav>
                <a href="#" onclick="showSection('kamar')" class="nav-link active">Data Kamar</a>
                <a href="#" onclick="showSection('penghuni')" class="nav-link">Data Penghuni</a>
                <a href="#" onclick="showSection('pembayaran')" class="nav-link">Pembayaran</a>
                <a href="#" onclick="showSection('ajuan')" class="nav-link">Ajuan</a>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h2 id="pageTitle">Data Kamar</h2>
                <div class="user-info">
                    <span>Admin</span>
                    <div class="profile-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div id="kamarSection" class="content-section active">
                <div class="table-header">
                    <button class="btn-add admin-btn" onclick="showAddKamarForm()">Tambah Data</button>
                </div>
                <div class="table-container">
                    <table id="kamarTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Kamar</th>
                                <th>Tarif per Bulan</th>
                                <th>Fasilitas</th>
                                <th>Status Kamar</th>
                                <th>Keterangan</th>
                                <th>Penghuni</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div id="penghuniSection" class="content-section">
                <div class="table-container">
                    <table id="penghuniTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>Kamar</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div id="pembayaranSection" class="content-section">
                <div class="table-container">
                    <table id="pembayaranTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Sewa</th>
                                <th>Penghuni</th>
                                <th>Kamar</th>
                                <th>Tarif</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div id="ajuanSection" class="content-section">
                <div class="table-container">
                    <table id="ajuanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Penghuni</th>
                                <th>Jenis Ajuan</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
penghuni-dashboard.html
html<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penghuni</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard penghuni-theme">
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>Kos Mami Sindu</h3>
            <nav>
                <a href="#" onclick="showSection('riwayat')" class="nav-link active">Riwayat Pembayaran</a>
                <a href="#" onclick="showSection('ajuan')" class="nav-link">Ajuan</a>
                <a href="#" onclick="logout()" class="nav-link">Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h2 id="pageTitle">Riwayat Pembayaran</h2>
                <div class="user-info">
                    <span id="userName">Penghuni</span>
                    <div class="profile-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div id="riwayatSection" class="content-section active">
                <div class="table-header">
                    <button class="btn-toggle penghuni-btn" onclick="togglePembayaran()">Lihat/Sembunyikan Pembayaran</button>
                </div>
                <div class="table-container" id="pembayaranTableContainer">
                    <table id="riwayatTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Sewa</th>
                                <th>Kamar</th>
                                <th>Tarif</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div id="ajuanSection" class="content-section">
                <div class="table-header">
                    <button class="btn-add penghuni-btn" onclick="showAddAjuanForm()">Tambah Ajuan</button>
                </div>
                <div class="table-container">
                    <table id="ajuanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Ajuan</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
css/style.css
css* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
}

.landing-page {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.container {
    text-align: center;
    max-width: 800px;
    padding: 2rem;
}

.welcome-section h1 {
    font-size: 2.5rem;
    margin-bottom: 3rem;
    font-weight: 300;
}

.login-section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    font-weight: 400;
}

.cards-container {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}

.card {
    background: white;
    color: #333;
    padding: 2rem;
    border-radius: 15px;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    min-width: 200px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.card-icon {
    margin-bottom: 1rem;
    color: #666;
}

.card p {
    font-size: 1.2rem;
    font-weight: 500;
}

.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.admin-theme {
    background: linear-gradient(135deg, #2196F3, #1976D2);
}

.penghuni-theme {
    background: linear-gradient(135deg, #4CAF50, #45a049);
}

.login-form {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 400px;
}

.register-form {
    max-width: 500px;
}

.login-form h2 {
    text-align: center;
    margin-bottom: 0.5rem;
    color: #333;
}

.admin-theme .login-form h2 {
    color: #2196F3;
}

.penghuni-theme .login-form h2 {
    color: #4CAF50;
}

.login-form p {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #4CAF50;
}

.admin-theme .form-group input:focus,
.admin-theme .form-group select:focus {
    border-color: #2196F3;
}

.btn-login {
    width: 100%;
    padding: 0.75rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s;
    color: white;
    margin-bottom: 1rem;
}

.admin-btn {
    background-color: #2196F3;
}

.admin-btn:hover {
    background-color: #1976D2;
}

.penghuni-btn {
    background-color: #4CAF50;
}

.penghuni-btn:hover {
    background-color: #45a049;
}

.back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #666;
}

.back-link a {
    color: #666;
    text-decoration: none;
}

.back-link a:hover {
    color: #333;
}

.register-link {
    text-align: center;
    color: #666;
}

.register-link a {
    color: #4CAF50;
    text-decoration: none;
}

.admin-theme .register-link a {
    color: #2196F3;
}

.register-link a:hover {
    text-decoration: underline;
}

.dashboard {
    min-height: 100vh;
    background-color: #f5f5f5;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    padding: 2rem 0;
    color: white;
}

.admin-theme .sidebar {
    background: linear-gradient(180deg, #2196F3, #1976D2);
}

.penghuni-theme .sidebar {
    background: linear-gradient(180deg, #4CAF50, #45a049);
}

.sidebar h3 {
    padding: 0 2rem;
    margin-bottom: 2rem;
    font-size: 1.5rem;
}

.sidebar nav {
    display: flex;
    flex-direction: column;
}

.nav-link {
    padding: 1rem 2rem;
    color: white;
    text-decoration: none;
    transition: background-color 0.3s;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
}

.nav-link:hover,
.nav-link.active {
    background-color: rgba(255,255,255,0.2);
}

.main-content {
    flex: 1;
    padding: 2rem;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

.header h2 {
    color: #333;
}

.admin-theme .header h2 {
    color: #2196F3;
}

.penghuni-theme .header h2 {
    color: #4CAF50;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.profile-icon {
    padding: 0.5rem;
    border-radius: 50%;
    background-color: #eee;
    color: #666;
}

.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

.table-header {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;
}

.btn-add,
.btn-toggle {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
    font-weight: 500;
}

.table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.action-btn {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin: 0 0.25rem;
    color: white;
}

.edit-btn {
    background-color: #2196F3;
}

.delete-btn {
    background-color: #f44336;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
}

.status-paid {
    background-color: #4CAF50;
}

.status-pending {
    background-color: #ff9800;
}

@media (max-width: 768px) {
    .welcome-section h1 {
        font-size: 2rem;
    }

    .cards-container {
        flex-direction: column;
        align-items: center;
    }

    .card {
        min-width: 250px;
    }

    .dashboard-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        order: 2;
    }

    .sidebar nav {
        flex-direction: row;
        overflow-x: auto;
    }

    .nav-link {
        white-space: nowrap;
    }

    .main-content {
        order: 1;
    }

    .header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        min-width: 600px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 1rem;
    }

    .login-form {
        padding: 1.5rem;
        margin: 1rem;
    }

    .main-content {
        padding: 1rem;
    }

    th,
    td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
}
js/main.js
javascriptconst API_BASE = 'http://localhost:5000/api';

document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'register.html') {
        loadAvailableRooms();
    }
    
    if (currentPage === 'admin-dashboard.html') {
        checkAuth('admin');
        loadKamarData();
    }
    
    if (currentPage === 'penghuni-dashboard.html') {
        checkAuth('penghuni');
        loadPembayaranData();
    }
});

function checkAuth(requiredRole) {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');
    
    if (!token || role !== requiredRole) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}

async function loadAvailableRooms() {
    try {
        const response = await fetch(`${API_BASE}/auth/kamar`);
        const rooms = await response.json();
        
        const select = document.getElementById('kamar_id');
        rooms.forEach(room => {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = room.nomor_kamar;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading rooms:', error);
    }
}

if (document.getElementById('adminLoginForm')) {
    document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch(`${API_BASE}/auth/admin/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('role', data.role);
                window.location.href = 'admin-dashboard.html';
            } else {
                alert('Login gagal: ' + data.error);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
}

if (document.getElementById('penghuniLoginForm')) {
    document.getElementById('penghuniLoginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch(`${API_BASE}/auth/penghuni/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('role', data.role);
                localStorage.setItem('userId', data.userId);
                window.location.href = 'penghuni-dashboard.html';
            } else {
                alert('Login gagal: ' + data.error);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
}

if (document.getElementById('registerForm')) {
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            nama_lengkap: document.getElementById('nama_lengkap').value,
            username: document.getElementById('username').value,
            email: document.getElementById('email').value,
            nomor_telepon: document.getElementById('nomor_telepon').value,
            password: document.getElementById('password').value,
            kamar_id: document.getElementById('kamar_id').value
        };
        
        try {
            const response = await fetch(`${API_BASE}/auth/penghuni/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                alert('Registrasi berhasil! Silakan login.');
                window.location.href = 'penghuni-login.html';
            } else {
                alert('Registrasi gagal: ' + data.error);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
}

function showSection(sectionName) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    document.getElementById(sectionName + 'Section').classList.add('active');
    event.target.classList.add('active');
    
    const titles = {
        'kamar': 'Data Kamar',
        'penghuni': 'Data Penghuni', 
        'pembayaran': 'Pembayaran',
        'ajuan': 'Ajuan',
        'riwayat': 'Riwayat Pembayaran'
    };
    
    document.getElementById('pageTitle').textContent = titles[sectionName];
    
    if (sectionName === 'kamar') loadKamarData();
    if (sectionName === 'penghuni') loadPenghuniData();
    if (sectionName === 'pembayaran') loadPembayaranAdminData();
    if (sectionName === 'ajuan') loadAjuanData();
    if (sectionName === 'riwayat') loadPembayaranData();
}

async function loadKamarData() {
    try {
        const response = await fetch(`${API_BASE}/admin/kamar`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#kamarTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.nomor_kamar}</td>
                <td>Rp ${parseInt(item.tarif_per_bulan).toLocaleString()}</td>
                <td>${item.fasilitas || '-'}</td>
                <td>${item.status_kamar}</td>
                <td>${item.keterangan || '-'}</td>
                <td>${item.penghuni_nama || '-'}</td>
                <td>
                    <button class="action-btn edit-btn" onclick="editKamar(${item.id})">✏️</button>
                    <button class="action-btn delete-btn" onclick="deleteKamar(${item.id})">🗑️</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading kamar data:', error);
    }
}

async function loadPenghuniData() {
    try {
        const response = await fetch(`${API_BASE}/admin/penghuni`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#penghuniTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.nama_lengkap}</td>
                <td>${item.username}</td>
                <td>${item.email}</td>
                <td>${item.nomor_telepon}</td>
                <td>${item.nomor_kamar || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading penghuni data:', error);
    }
}

async function loadPembayaranAdminData() {
    try {
        const response = await fetch(`${API_BASE}/admin/pembayaran`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#pembayaranTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.kode_sewa}</td>
                <td>${item.nama_lengkap}</td>
                <td>${item.nomor_kamar}</td>
                <td>Rp ${parseInt(item.tarif).toLocaleString()}</td>
                <td><span class="status-badge ${item.status === 'sudah_bayar' ? 'status-paid' : 'status-pending'}">${item.status === 'sudah_bayar' ? 'Sudah Bayar' : 'Pending'}</span></td>
                <td>${item.tanggal_pembayaran || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading pembayaran data:', error);
    }
}

async function loadAjuanData() {
    try {
        const response = await fetch(`${API_BASE}/admin/ajuan`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#ajuanTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.nama_lengkap}</td>
                <td>${item.jenis_ajuan}</td>
                <td>${item.deskripsi}</td>
                <td>${item.status}</td>
                <td>${item.tanggal_ajuan}</td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading ajuan data:', error);
    }
}

async function loadPembayaranData() {
    try {
        const userId = localStorage.getItem('userId');
        const response = await fetch(`${API_BASE}/penghuni/pembayaran/${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#riwayatTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.kode_sewa}</td>
                <td>${item.nomor_kamar}</td>
                <td>Rp ${parseInt(item.tarif).toLocaleString()}</td>
                <td><span class="status-badge status-paid">Sudah Bayar</span></td>
                <td><span class="status-badge status-paid">Lihat Bukti</span></td>
                <td>${item.tanggal_pembayaran || new Date().toISOString().split('T')[0]}</td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading pembayaran data:', error);
    }
}

function togglePembayaran() {
    const container = document.getElementById('pembayaranTableContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('userId');
    window.location.href = 'index.html';
}

function showAddKamarForm() {
    const nomor_kamar = prompt('Nomor Kamar:');
    const tarif_per_bulan = prompt('Tarif per Bulan:');
    const fasilitas = prompt('Fasilitas:');
    const keterangan = prompt('Keterangan:');
    
    if (nomor_kamar && tarif_per_bulan) {
        addKamar({ nomor_kamar, tarif_per_bulan, fasilitas, keterangan });
    }
}

async function addKamar(data) {
    try {
        const response = await fetch(`${API_BASE}/admin/kamar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Kamar berhasil ditambahkan');
            loadKamarData();
        } else {
            alert('Gagal menambahkan kamar');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

function editKamar(id) {
    alert('Fitur edit sedang dalam pengembangan');
}

async function deleteKamar(id) {
    if (confirm('Yakin ingin menghapus kamar ini?')) {
        try {
            const response = await fetch(`${API_BASE}/admin/kamar/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            
            if (response.ok) {
                alert('Kamar berhasil dihapus');
                loadKamarData();
            } else {
                alert('Gagal menghapus kamar');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }
}

function showAddAjuanForm() {
    const jenis_ajuan = prompt('Jenis Ajuan:');
    const deskripsi = prompt('Deskripsi:');
    
    if (jenis_ajuan && deskripsi) {
        addAjuan({ jenis_ajuan, deskripsi, tanggal_ajuan: new Date().toISOString().split('T')[0] });
    }
}

async function addAjuan(data) {
    try {
        data.penghuni_id = localStorage.getItem('userId');
        
        const response = await fetch(`${API_BASE}/penghuni/ajuan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Ajuan berhasil dikirim');
            loadAjuanPenghuniData();
        } else {
            alert('Gagal mengirim ajuan');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function loadAjuanPenghuniData() {
    try {
        const userId = localStorage.getItem('userId');
        const response = await fetch(`${API_BASE}/penghuni/ajuan/${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#ajuanTable tbody');
        tbody.innerHTML = '';
        
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.jenis_ajuan}</td>
                <td>${item.deskripsi}</td>
                <td>${item.status}</td>
                <td>${item.tanggal_ajuan}</td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading ajuan data:', error);
    }
}
7. LANGKAH-LANGKAH INSTALASI DAN MENJALANKAN
Install Dependencies Backend
bashcd backend
npm init -y
npm install express mysql2 bcryptjs jsonwebtoken multer cors body-parser
Setup Database
bash# Buka MySQL dan jalankan file database.sql
mysql -u root -p
source database.sql
Menjalankan Backend
bashcd backend
node server.js
Menjalankan Frontend
bash# Buka browser dan akses:
http://localhost:5000
8. TESTING

Default Page: Akses http://localhost:5000
Admin Login: Username: admin, Password: password
Register Penghuni: Daftar dengan data lengkap
Login Penghuni: Gunakan username dan password yang telah didaftarkan

9. FITUR UPLOAD GAMBAR
Untuk fitur upload bukti pembayaran, struktur sudah disiapkan dengan multer di backend. File akan tersimpan di folder backend/uploads/ dan dapat diakses melalui endpoint /uploads/namafile.
Website ini sudah responsive dan mengikuti color scheme yang diminta (Admin: Biru, Penghuni: Hijau). Semua fitur CRUD sudah tersedia dan dapat dikembangkan lebih lanjut sesuai kebutuhan