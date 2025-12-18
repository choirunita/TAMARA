<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/StoPdfModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$model = new StoPdfModel();
$dataSto = $model->getAll();

/**
 * 🔑 SATU-SATUNYA BASE URL
 */
//$BASE_URL = '/tamara-main/public';

require __DIR__ . '/../views/sto/list.php';
