<?php 
// app/views/report/index.php

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Ambil data user dari session
$username = $_SESSION['username'] ?? '';
$role     = $_SESSION['role']     ?? '';

$months        = $months        ?? [];
$types         = $types         ?? [];
$gudangs       = $gudangs       ?? [];
$stoList       = $stoList       ?? [];
$invoices      = $invoices      ?? [];
$invoiceData   = $invoiceData   ?? [];
$invoiceLines  = $invoiceLines  ?? [];
$invoiceLineDetails = $invoiceLineDetails ?? [];

$nama_gudang = $nama_gudang ?? '-';
$gudang_id   = $gudang_id   ?? null;

// Siapkan data STO untuk pengisian kolom (map id -> detail) dan opsi select2
$stoDataPHP = [];
$stoOpts    = [];
foreach ($stoList as $s) {
    $id   = (int)$s['id'];
    $stoDataPHP[$id] = [
        'id'              => $id,
        'nomor_sto'       => $s['nomor_sto'] ?? '',
        'tanggal_terbit'  => $s['tanggal_terbit'] ?? '',
        'nama_gudang'     => $s['nama_gudang'] ?? '',
        'transportir'     => $s['transportir'] ?? '',
        'tonase_normal'   => $s['tonase_normal'] ?? 0,
        'tonase_lembur'   => $s['tonase_lembur'] ?? 0,
        'keterangan'      => $s['keterangan'] ?? '',
        'jenis_transaksi' => $s['jenis_transaksi'] ?? '',
        'gudang_id'       => $s['gudang_id'] ?? '',
    ];
    $stoOpts[] = [
        'id'              => $id,
        'text'            => $s['nomor_sto'],
        'jenis_transaksi' => $s['jenis_transaksi'] ?? '',
        'gudang_id'       => $s['gudang_id'] ?? '',
    ];
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="dashboard-canvas">
  <div class="dashboard-grid" aria-hidden="true"></div>

  <!-- ============ CSS brand + skala font ============ -->
  <style>
    .app-brand {
      display: flex;
      align-items: center;
      gap: .75rem;
    }

    .app-brand-logo {
      width: 44px;
      height: 44px;
      border-radius: 16px;
      background: linear-gradient(145deg, var(--blue), var(--green));
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 24px rgba(15, 23, 42, .16);
      overflow: hidden;
    }

    .app-brand-title {
      font-weight: 600;
      letter-spacing: .04em;
      text-transform: uppercase;
      font-size: .8rem;
    }

    .app-brand-subtitle {
      font-size: .76rem;
    }

    .role-chip-compact {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .2rem .55rem;
      border-radius: 999px;
      font-size: .72rem;
      text-transform: uppercase;
      letter-spacing: .06em;
      background: rgba(248, 250, 252, .85);
      border: 1px solid rgba(148, 163, 184, .35);
      color: #0f172a;
    }

    .role-chip-dot {
      width: 7px;
      height: 7px;
      border-radius: 999px;
      background: var(--green);
    }

    /* Skala font konten utama (seragam dengan Master STO) */
    .section-body p,
    .section-body .form-label,
    .section-body .form-control,
    .section-body .form-select,
    .section-body .input-group-text,
    .section-body .form-text,
    .section-body .alert,
    .section-body table,
    .section-body .table,
    .section-body .btn,
    .section-body .badge,
    .section-body .pagination .page-link,
    .section-body .list-group-item,
    .section-body small,
    .section-body .text-muted {
      font-size: .9rem;
    }

    /* Modal detail & edit – sedikit diperkecil tapi tetap nyaman dibaca */
    #modalInvoice .modal-content,
    #modalEdit .modal-content {
      font-size: .85rem;
    }

    #modalEdit .form-label,
    #modalEdit .form-control,
    #modalEdit .form-select,
    #modalEdit .table,
    #modalEdit .table th,
    #modalEdit .table td,
    #modalEdit .btn,
    #modalEdit .form-text,
    #modalEdit .input-group-text,
    #modalEdit small,
    #modalEdit .text-muted {
      font-size: .85rem;
    }
  </style>

  <!-- ============ TOP BAR ============ -->
  <div class="dash-bar section-pad py-3 mb-3">
    <div class="d-flex align-items-center justify-content-between">
      <div class="app-brand">
        <div class="app-brand-logo" aria-hidden="true" style="transform: scale(0.9);">
          <img src="assets/img/tamara-logo.svg" alt="Tamara" style="transform: scale(0.7);">
        </div>
        <div>
          <div class="app-brand-title"
              style="color:var(--text); font-size:1rem; font-weight:700;">
            TAMARA
          </div>
          <div class="app-brand-subtitle text-muted"
              style="font-size:0.8rem; font-weight:500;">
            Tagihan Termonitor, Aman dan Rapi
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <span class="role-chip-compact d-none d-md-inline-flex">
          <span class="role-chip-dot"></span>
          <span><?= htmlspecialchars($role) ?></span>
        </span>
        <a href="index.php?page=dashboard" class="btn btn btn-warning">Kembali</a>
      </div>
    </div>
  </div>

  <!-- ============ KONTEN UTAMA ============ -->
  <div class="section-pad section-body">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="master-card-title mb-1" style="font-weight:700;">Rekap Invoice</h5>
        <p class="master-card-subtitle">
          Rangkuman detail invoide dalam periode tertentu.
        </p>
      </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- ============ FORM BUAT INVOICE BARU ============ -->
    <?php if ($role === 'KEPALA_GUDANG' || $role === 'SUPERADMIN'): ?>
    <form id="frm-create" method="POST" action="index.php?page=report_generate" class="mb-5">
      <div class="col-md-6">
    <label>Tanggal</label>
    <div class="d-flex gap-2 align-items-center">
        <input type="date" name="tanggal_from" class="form-control" required>
        <span>To:</span>
        <input type="date" name="tanggal_to" class="form-control" required>
    </div>  
    </div>

        <div class="col-md-3">
          <label class="form-label">Wilayah</label>
          <input type="hidden" name="gudang_id" id="gudang_id" value="<?= htmlspecialchars((string)$gudang_id) ?>">
          <input
            type="text"
            name="gudang_nama"
            id="edit-nama-gudang"
            class="form-control"
            placeholder="Nama Gudang"
            value="<?= htmlspecialchars($nama_gudang) ?: '-' ?>"
            readonly>
        </div>

        <div class="col-md-3">
          <label class="form-label">Gudang</label>
          <input type="hidden" name="gudang_id" id="gudang_id" value="<?= htmlspecialchars((string)$gudang_id) ?>">
          <input
            type="text"
            name="gudang_nama"
            id="edit-nama-gudang"
            class="form-control"
            placeholder="Nama Gudang"
            value="<?= htmlspecialchars($nama_gudang) ?: '-' ?>"
            readonly>
        </div>

        <div class="col-md-3">
          <label class="form-label">Kegiatan</label>
          <input type="hidden" name="gudang_id" id="gudang_id" value="<?= htmlspecialchars((string)$gudang_id) ?>">
          <input
            type="text"
            name="gudang_nama"
            id="edit-nama-gudang"
            class="form-control"
            placeholder="Nama Gudang"
            value="<?= htmlspecialchars($nama_gudang) ?: '-' ?>"
            readonly>
        </div>   

      <!-- Kontrol sejajar dengan lebar tabel -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <button type="submit" class="btn btn-success">
          SUBMIT
        </button>
      </div>
    </form>
    <?php endif; ?>

    
</script>
