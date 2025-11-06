<?php
date_default_timezone_set('Asia/Jakarta');
// ==========================================================
// 1. PENGATURAN DAN KEAMANAN SERVER
// ==========================================================

// Tingkatkan batas waktu eksekusi agar tidak terjadi HTTP Timeout
set_time_limit(60);
// Menggunakan path yang sesuai dengan struktur file Anda
$baseUploadDir = 'D:/laragon1/www/inas/inas-v2/public/storage/images/';
$dbStoragePath = 'storage/images/'; // Diperbaiki ke 'images/' agar sinkron dengan path DB

if (!is_dir($baseUploadDir)) {
    http_response_code(500);
    die("ERROR|Server: Direktori unggahan tidak ditemukan atau tidak dapat ditulis.");
}

// ==========================================================
// 2. KONEKSI DATABASE (Menggunakan mysqli - Dipertahankan)
// ==========================================================
$host = "127.0.0.1";
$user = "root"; // default laragon
$pass = ""; // default laragon
$db   = "inas"; 

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    http_response_code(500);
    die("ERROR|DB_CONNECT: " . $mysqli->connect_error);
}

// ==========================================================
// 3. PEMROSESAN POST DATA & GAMBAR (Dipertahankan)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("ERROR|Method Not Allowed. Use POST.");
}

$uid = isset($_POST['uid']) ? filter_var($_POST['uid'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : 'UNKNOWN';

// Blok utama untuk memproses jika file gambar diterima
if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    $currentTime = strtotime($now); 

    // Definisi Batas Waktu WIB (Ubah sesuai jam yang Anda inginkan jika perlu)
    $timeStartMasuk = strtotime($today . ' 06:00:00');
    $timeEndMasuk = strtotime($today . ' 13:00:00');
    $timeStartPulang = strtotime($today . ' 13:300:00');
    $timeEndPulang = strtotime($today . ' 22:55:00');

    $status = null;
    $studentId = null;

    // A. Cari Siswa di Master Data (Tolak jika tidak ditemukan)
    $stmt_student = $mysqli->prepare("SELECT id FROM students WHERE uid = ?");
    $stmt_student->bind_param("s", $uid);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();
    
    if ($result_student->num_rows > 0) {
        $student = $result_student->fetch_assoc();
        $studentId = $student['id'];
        $stmt_student->close();
    } else {
        $stmt_student->close();
        http_response_code(404);
        die("ERROR|UID_NOT_FOUND: UID $uid tidak terdaftar di Master Data Siswa.");
    }

    // B. Tentukan Sesi Waktu Absensi
    if ($currentTime >= $timeStartMasuk && $currentTime <= $timeEndMasuk) {
        $status = 'Masuk';
    } elseif ($currentTime >= $timeStartPulang && $currentTime <= $timeEndPulang) {
        $status = 'Pulang';
    } else {
        http_response_code(403);
        die("ERROR|TIME_DENIED: Absen ditolak. Waktu absen hanya diperbolehkan pukul 06:00-10:00 atau 13:00-17:00 WIB.");
    }
    
    // C. CEK ABSENSI GANDA SESUAI STATUS (Masuk/Pulang hanya boleh 1x/hari)
    $stmt_check_ganda = $mysqli->prepare("
        SELECT id FROM attendances 
        WHERE student_id = ? AND DATE(check_in_time) = ? AND status = ?
    ");
    $stmt_check_ganda->bind_param("iss", $studentId, $today, $status);
    $stmt_check_ganda->execute();
    
    if ($stmt_check_ganda->get_result()->num_rows > 0) {
        $stmt_check_ganda->close();
        http_response_code(403);
        die("ERROR|DUPLICATE_DENIED: Absen $status ditolak. Siswa sudah absen $status hari ini.");
    }
    $stmt_check_ganda->close();

    // D. VALIDASI ALUR: Pastikan Masuk terjadi sebelum Pulang (Perbaikan Logika)
    if ($status === 'Pulang') {
        $stmt_check_masuk = $mysqli->prepare("
            SELECT id FROM attendances 
            WHERE student_id = ? AND DATE(check_in_time) = ? AND status = 'Masuk'
        ");
        // Kunci perbaikan: Hanya cek apakah record MASUK ada untuk hari ini
        $stmt_check_masuk->bind_param("is", $studentId, $today);
        $stmt_check_masuk->execute();
        
        if ($stmt_check_masuk->get_result()->num_rows === 0) {
            $stmt_check_masuk->close();
            http_response_code(403);
            die("ERROR|ALUR_DENIED: Absen Pulang ditolak. Siswa belum Check-in hari ini.");
        }
        $stmt_check_masuk->close();
    }
    
    // E. Simpan File Gambar (SELALU SIMPAN UNTUK MASUK DAN PULANG)
    $fileTmpPath = $_FILES['image_file']['tmp_name'];
    $timestamp = date('YmdHis');
    $fileName = $uid . '_' . $timestamp . '.jpg';
    $destPathAbsolut = $baseUploadDir . $fileName;
    $finalImagePathForDB = $dbStoragePath . $fileName; // Path Gambar untuk DB
    
    // Pindahkan file ke storage
    if (!move_uploaded_file($fileTmpPath, $destPathAbsolut)) {
        http_response_code(500);
        die("ERROR|MOVE_FILE: Gagal memindahkan file ke direktori permanen.");
    }

    
    // ==========================================================
    // 5. SIMPAN KE DATABASE (SELALU INSERT BARU)
    // ==========================================================

    // Setiap tap (Masuk atau Pulang) di-INSERT sebagai record baru di attendances
    $stmt = $mysqli->prepare("
        INSERT INTO attendances 
            (uid, student_id, image_path, check_in_time, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    // Kunci Perbaikan Binding: Menggunakan 's' untuk semua string
    // Tipe data: s (uid), i (studentId), s (path), s (check_in_time), s (status), s (created_at), s (updated_at)
    $stmt->bind_param("sisssss", $uid, $studentId, $finalImagePathForDB, $now, $status, $now, $now);
    $message = "Absen $status jam $now berhasil dicatat.";


    if ($stmt->execute()) {
        http_response_code(200);
        echo "SUCCESS|DB_OK|$message|UID:$uid";
    } else {
        http_response_code(500);
        echo "ERROR|DB_FAIL: Gagal mengeksekusi query absensi. " . $mysqli->error;
    }

    $stmt->close();
    $mysqli->close();

} else {
    // Error jika tidak ada file yang diupload
    $errorCode = isset($_FILES['image_file']['error']) ? $_FILES['image_file']['error'] : 'N/A';
    http_response_code(400);
    die("ERROR|UPLOAD_FAIL: Tidak ada file atau error kode: " . $errorCode);
}
?>