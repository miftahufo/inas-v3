<?php
// D:\laragon1\www\inas\receiver.php

// --- KONFIGURASI DATABASE (GANTI DENGAN KREDENSIAL ANDA) ---
$host = '127.0.0.1'; 
$dbname = 'inas'; // NAMA DATABASE
$user = 'root'; 
$password = ''; 

// --- RESPON AWAL UNTUK ESP32 ---
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


// --- LOGIKA UTAMA ---
try {
    $uid = $_POST['uid'] ?? null;
    $is_file_received = isset($_FILES['image_file']);

    // Lokasi upload gambar di dalam Laravel project
    $uploadDir = __DIR__ . '/inas-v2/public/storage/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = null;

    if (!empty($uid) && $is_file_received) {
        // Jika file terkirim normal via $_FILES
        $newFileName = $uid . '_' . date('YmdHis') . '.jpg';
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file upload.']);
            exit;
        }

    } else {
        // --- Fallback parsing manual ---
        $rawContent = file_get_contents('php://input');
        $boundary = "----WebKitFormBoundary7MA4YWqATmY97d74"; 
        $separator = '--' . $boundary;
        $parts = explode($separator, $rawContent);

        $uid_manual = null;
        $imageData = null;

        foreach ($parts as $part) {
            if (str_contains($part, 'name="uid"')) {
                $uid_manual = trim(substr($part, strpos($part, "\r\n\r\n") + 4));
            }
            if (str_contains($part, 'name="image_file"')) {
                $imageData = substr($part, strpos($part, "\r\n\r\n") + 4);
                $imageData = rtrim($imageData, "\r\n");
            }
        }

        if (empty($uid_manual) || empty($imageData)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'UID atau gambar tidak ditemukan.']);
            exit;
        }

        $uid = $uid_manual;
        $newFileName = $uid . '_' . date('YmdHis') . '.jpg';
        $uploadPath = $uploadDir . $newFileName;

        if (!file_put_contents($uploadPath, $imageData)) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file (manual).']);
            exit;
        }
    }

    // Path untuk disimpan di DB (relatif terhadap public)
    $dbImagePath = 'storage/images/' . $newFileName;

    // Simpan ke database
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO attendances (uid, image_path, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute([$uid, $dbImagePath]);

    // Respon sukses
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Data tersimpan.', 'file' => $dbImagePath]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
}