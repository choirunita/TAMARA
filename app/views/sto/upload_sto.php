<?php
// app/views/sto/upload.php

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];

require_once __DIR__ . '/../layout/header.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="dashboard-canvas">
  <div class="dashboard-grid" aria-hidden="true"></div>
  
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

<div class="dashboard-canvas">
  <div class="dashboard-grid" aria-hidden="true"></div>

  <!-- Top bar (jadi acuan layout halaman lain) -->
  <div class="dash-bar section-pad py-3">
    <div class="d-flex align-items-center justify-content-between">
      <!-- Brand TAMARA seperti halaman auth -->
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

      <!-- Kanan: hanya role + Logout, tanpa "Halo, superadmin" -->
      <div class="d-flex align-items-center gap-3">
        <span class="role-chip-compact d-none d-md-inline-flex">
          <span class="role-chip-dot"></span>
          <span><?= htmlspecialchars($role) ?></span>
        </span>
        <a href="index.php?page=dashboard" class="btn btn btn-warning">Kembali</a>
      </div>
    </div>
  </div>

<div class="container mt-5" style="max-width: 700px;">

    <h5 class="mb-4">Upload STO PDF</h5>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="index.php?page=sto_upload_process" method="POST" enctype="multipart/form-data">

                <small class="mb-4">
                    <label class="form-label">Silakan Upload STO hanya dalam format pdf bukan dalam bentuk scan / gambar</label>
                    <input 
                        type="file" 
                        name="sto_pdf" 
                        accept="application/pdf" 
                        class="form-control" 
                        required>
                    <small class="text-muted">
                        * File boleh 1 halaman atau banyak halaman. Sistem akan mendeteksi otomatis.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Upload & Proses STO
                </button>

            </form>

        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
