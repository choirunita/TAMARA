<?php
session_start();

// proteksi login
if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$filename = $_GET['file'] ?? '';

if ($filename === '') {
    die('File tidak valid');
}

$filepath = __DIR__ . '/../uploads/sto_pdf/' . basename($filename);

if (!file_exists($filepath)) {
    die('File tidak ditemukan');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
