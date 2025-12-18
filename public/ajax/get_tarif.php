<?php
// public/ajax/get_tarif.php
require __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

// Terima parameter dari GET maupun POST, dan dua versi nama
$g  = $_GET['gudang_id']       ?? $_POST['gudang_id']       ?? $_POST['gudang']       ?? null;
$jt = $_GET['jenis_transaksi'] ?? $_POST['jenis_transaksi'] ?? $_POST['jenis']        ?? null;

if (!$g || !$jt) {
    echo json_encode([
        'success' => false,
        'message' => 'Parameter tidak lengkap'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT tarif_normal, tarif_lembur
    FROM gudang_tarif
    WHERE gudang_id       = :g
      AND jenis_transaksi = :jt
    LIMIT 1
");
$stmt->execute([
    'g'  => $g,
    'jt' => $jt,
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode([
        'success'      => true,
        'tarif_normal' => (float) $row['tarif_normal'],
        'tarif_lembur' => (float) $row['tarif_lembur'],
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Tarif tidak ditemukan'
    ]);
}
