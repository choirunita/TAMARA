<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/StoPdfModel.php';

function pdf_to_text_poppler($pdfPath)
{
    $poppler = "C:\\poppler\\Library\\bin\\pdftotext.exe";

    if (!file_exists($poppler)) {
        return ["error" => "pdftotext tidak ditemukan di: $poppler"];
    }

    $txtOut = $pdfPath . ".txt";
    $cmd = "\"$poppler\" -layout \"$pdfPath\" \"$txtOut\" 2>&1";
    shell_exec($cmd);

    if (!file_exists($txtOut)) {
        return ["error" => "Gagal mengekstrak PDF"];
    }

    $text = file_get_contents($txtOut);
    unlink($txtOut);

    return ["text" => $text];
}

// ================== 1. VALIDASI FILE =====================
if (!isset($_FILES['sto_pdf']) || $_FILES['sto_pdf']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "File PDF tidak ditemukan.";
    header("Location: index.php?page=upload_sto");
    exit;
}

$fileTmp  = $_FILES['sto_pdf']['tmp_name'];
$fileName = $_FILES['sto_pdf']['name'];
$fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($fileExt !== 'pdf') {
    $_SESSION['error'] = "File harus PDF.";
    header("Location: index.php?page=upload_sto");
    exit;
}

// ================== 2. SIMPAN FILE =====================
$uploadDir = __DIR__ . '/../../uploads/sto_pdf/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$newFile = time() . "_" . preg_replace('/\s+/', '_', $fileName);
$savePath = $uploadDir . $newFile;

move_uploaded_file($fileTmp, $savePath);

// ================== 3. EXTRACT TEXT =====================
$result = pdf_to_text_poppler($savePath);
if (isset($result['error'])) {
    $_SESSION['error'] = $result['error'];
    header("Location: index.php?page=upload_sto");
    exit;
}

$text = str_replace("\r", "", $result['text']);

// ================== 4. SPLIT PER STO =====================
$blocks = preg_split('/(?=No\.\s*Po\s*:)/i', $text);
$model = new StoPdfModel();
$count = 0;

foreach ($blocks as $block) {

    $block = trim($block);
    if ($block === "") continue;

    // NOMOR STO
    if (!preg_match('/No\.\s*Po\s*:\s*(\d+)/i', $block, $m)) continue;
    $nomor_sto = trim($m[1]);

    // TANGGAL CETAK
    $tanggal_cetak = null;
    if (preg_match('/Tanggal\s+Cetak\s*:\s*([0-9.]+)/', $block, $m)) {
        $tgl = str_replace(".", "-", $m[1]);
        $tanggal_cetak = date('Y-m-d', strtotime($tgl));
    }

    // TANGGAL DOKUMEN
    $tanggal_dokumen = null;
    if (preg_match('/Tanggal\s*:\s*([0-9.]+)/', $block, $m)) {
        $tgl = str_replace(".", "-", $m[1]);
        $tanggal_dokumen = date('Y-m-d', strtotime($tgl));
    }

    // JENIS KEGIATAN
    // ================== JENIS KEGIATAN (WAJIB) ==================
$jenis_kegiatan = null;

// cari di seluruh block
if (preg_match('/\bBONGKAR\b/i', $block)) {
    $jenis_kegiatan = 'BONGKAR';
} elseif (preg_match('/\bMUAT\b/i', $block)) {
    $jenis_kegiatan = 'MUAT';
}

// FALLBACK WAJIB (ANTI NULL)
if ($jenis_kegiatan === null) {
    // opsional: deteksi dari nama file
    if (preg_match('/bongkar/i', $fileName)) {
        $jenis_kegiatan = 'BONGKAR';
    } elseif (preg_match('/muat/i', $fileName)) {
        $jenis_kegiatan = 'MUAT';
    } else {
        // default TERAMAN
        $jenis_kegiatan = 'BONGKAR';
    }
}


    // ASAL BARANG
    $asal_barang = null;
    if (preg_match('/Asal\s+Barang\s*:\s*.*?\n(.*?)\n/si', $block, $m)) {
        $asal_barang = trim($m[1]);
    }

    // TUJUAN BARANG
    $tujuan_barang = null;
    if (preg_match('/Tujuan\s+Barang\s*:\s*.*?\n(.*?)\n/si', $block, $m)) {
        $tujuan_barang = trim($m[1]);
    }

    // MODA
    $moda = null;
    if (preg_match('/Moda\s*:\s*(.+)/i', $block, $m)) {
    $moda = trim($m[1]);
    }


    // REFERENSI
    $referensi = null;
    if (preg_match('/Referensi\s*:\s*(.+)/i', $block, $m)) {
    $referensi = trim($m[1]);
    }

    // PEMILIK BARANG
    $pemilik_barang = null;
    if (preg_match('/Pemilik\s+Barang\s*:\s*(.+)/i', $block, $m)) {
        $pemilik_barang = trim($m[1]);
    }

    // NAMA BARANG
    $nama_barang = null;
    if (preg_match('/(UREA[^\n]*|NPK[^\n]*|DAP[^\n]*|ZA[^\n]*|PHONSKA[^\n]*|SP36[^\n]*|SACK[^\n]*)(?:\s*\n\s*([^\n]+))?/i', 
    $block, $m)) {
    // Ambil baris pertama
    $nama_barang = trim($m[1]);
    // Gabung baris kedua jika ada
    if (!empty($m[2])) {
        $nama_barang .= " " . trim($m[2]);
    }
    // Rapikan spasi
    $nama_barang = preg_replace('/\s+/', ' ', $nama_barang);
    // ✔ Batasi maksimal 30 karakter
    $nama_barang = mb_substr($nama_barang, 0, 30, 'UTF-8');
    }

  // QTY sesuai PDF
    $qty = null;
    $lines = array_map('trim', explode("\n", $block));
    foreach ($lines as $line) {
    if (preg_match('/([\d.,]+)\s+TON\b/i', $line, $m)) {
        $qty_raw = $m[1];   // bisa "84.600" atau "84,600"

        // NORMALISASI: pastikan koma
        if (strpos($qty_raw, '.') !== false && strpos($qty_raw, ',') === false) {
            $qty = str_replace('.', ',', $qty_raw);
        } else {
            $qty = $qty_raw;
        }

        break;
    }
}

    // SATUAN
    $satuan = null;
    $lines = array_map('trim', explode("\n", $block));
    foreach ($lines as $line) {
    // Contoh baris:
    // 84.600 TON 13.100 1.108.260
    if (preg_match('/([\d.,]+)\s+(TON|PCS|EA|KG|UNIT)\s+([\d.,]+)\s+([\d.,]+)/i', $line, $m)) {
        $qty_raw = $m[1];   // 84,600 / 84.600
        $satuan  = $m[2];   // TON
        $tarif   = $m[3];   // 13.100
        $biaya   = $m[4];   // 1.108.260
        break;
    }
}

    // TARIF
    $tarif = null;
    $lines = array_map('trim', explode("\n", $block));
    foreach ($lines as $line) {
    // Contoh baris:
    // 84.600 TON 13.100 1.108.260
    if (preg_match('/([\d.,]+)\s+(TON|PCS|EA|KG|UNIT)\s+([\d.,]+)\s+([\d.,]+)/i', $line, $m)) {

        $qty_raw = $m[1];   // 84,600 / 84.600
        $satuan  = $m[2];   // TON
        $tarif   = $m[3];   // 13.100
        $biaya   = $m[4];   // 1.108.260

        break;
    }
}

// BIAYA
    $biaya = null;

$lines = array_map('trim', explode("\n", $block));

foreach ($lines as $line) {

    // Contoh baris:
    // 84.600 TON 13.100 1.108.260
    if (preg_match(
        '/([\d.,]+)\s+(TON|PCS|EA|KG|UNIT)\s+([\d.,]+)\s+([\d.,]+)/i',
        $line,
        $m
    )) {

        $biaya_raw = $m[4]; // "1.108.260"

        // Simpan APA ADANYA (sesuai PDF)
        $biaya = $biaya_raw;

        break;
    }
}

    // QTY string
    $qty_string = null;
    $qty_decimal = null;
    if (preg_match('/Qty\s*([0-9\.,]+)\s*(TON|EA)/i', $block, $m)) {
        $qty_string = trim($m[1]);

        $tmp = str_replace(".", "", $qty_string);
        $tmp = str_replace(",", ".", $tmp);

        $qty_decimal = floatval($tmp);
    }

    // TOTAL
    $total = null;
    if (preg_match('/Total\s*([0-9\.,]+)/i', $block, $m)) {
        $tmp = str_replace(".", "", $m[1]);
        $tmp = str_replace(",", ".", $tmp);
        $total = floatval($tmp);
    }

    // SIMPAN
    $model->insert([
        'nomor_sto'      => $nomor_sto,
        'tanggal_dokumen'=> $tanggal_dokumen,
        'tanggal_cetak'  => $tanggal_cetak,
        'jenis_kegiatan' => $jenis_kegiatan,
        'asal_barang'    => $asal_barang,
        'tujuan_barang'  => $tujuan_barang,
        'moda'           => $moda,
        'referensi'      => $referensi,
        'pemilik_barang' => $pemilik_barang,
        'nama_barang'    => $nama_barang,
        'qty'            => $qty,
        'satuan'         => $satuan,
        'tarif'          => $tarif,
        'biaya'          => $biaya,
        'total'          => $biaya,
        'halaman'        => null,
        'pdf_file'       => $newFile
    ]);

    $count++;
}

$_SESSION['success'] = "$count STO berhasil diproses.";
header("Location: index.php?page=upload_sto");
exit;

