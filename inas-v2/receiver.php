<?php
// ==========================================================
// 1. PENGATURAN DAN KEAMANAN SERVER
// ==========================================================

// Tingkatkan batas waktu eksekusi agar tidak terjadi HTTP Timeout (-11)
set_time_limit(60); 

// Atur lokasi folder upload (harus dapat ditulis oleh user web server)
// Path Absolut di Server (tempat file akan disimpan)
$baseUploadDir = 'D:/laragon1/www/inas/inas-v2/public/storage/images/'; 

// Path yang akan disimpan di database (URL Relatif)
$dbStoragePath = 'storage/images/'; 

// Pastikan folder upload ada
if (!is_dir($baseUploadDir)) {
    // Di lingkungan produksi, Anda mungkin ingin membuat folder ini atau menampilkan error 500
    http_response_code(500);
    die("ERROR|Server: Direktori unggahan tidak ditemukan atau tidak dapat ditulis.");
}

// ==========================================================
// 2. PEMROSESAN UNGGAHAN
// ==========================================================

// Pastikan methodnya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("ERROR|Method Not Allowed. Use POST.");
}

// 1. Ambil Data UID (Dikirim sebagai field POST 'uid')
$uid = isset($_POST['uid']) ? filter_var($_POST['uid'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : 'UNKNOWN';

// 2. Ambil File Gambar (Dikirim sebagai field 'image_file')
if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image_file']['tmp_name'];
    
    // Buat nama file unik: UID_YYYYMMDDHHmmss.jpg
    $timestamp = date('YmdHis');
    $fileName = $uid . '_' . $timestamp . '.jpg';
    $destPathAbsolut = $baseUploadDir . $fileName;

    // Pindahkan file sementara ke lokasi permanen
    if (move_uploaded_file($fileTmpPath, $destPathAbsolut)) {
        
        // Path yang akan disimpan di kolom 'image_path'
        $finalImagePathForDB = $dbStoragePath . $fileName;

        // =================================================================
        // 3. SIMPAN KE DATABASE (LOGIKA PALSU)
        // Lakukan INSERT INTO table (uid, image_path, created_at) VALUES ...
        // =================================================================
        $dbSuccess = true; // Anggap operasi DB sukses untuk demonstrasi

        if ($dbSuccess) {
            // Output respons SUKSES ke ESP32-CAM
            http_response_code(200); 
            // Respons yang dibaca ESP32-CAM harus cepat dan informatif
            echo "SUCCESS|Path:$finalImagePathForDB|UID:$uid";
        } else {
            http_response_code(500);
            echo "ERROR|DB_SAVE: Gagal menyimpan path ke database.";
        }
        
    } else {
        http_response_code(500);
        die("ERROR|MOVE_FILE: Gagal memindahkan file ke direktori permanen.");
    }
} else {
    // Tangani error unggahan (misal: file terlalu besar, path salah)
    $errorCode = isset($_FILES['image_file']['error']) ? $_FILES['image_file']['error'] : 'N/A';
    http_response_code(400);
    die("ERROR|UPLOAD_FAIL: Tidak ada file atau error kode: " . $errorCode);
}
?>