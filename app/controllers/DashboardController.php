<?php
// app/controllers/DashboardController.php

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Ambil data user dari session
$username = $_SESSION['username'];
$role     = $_SESSION['role'];

/**
 * ==========================
 *  KONEKSI DATABASE (PDO)
 * ==========================
 */
if (!isset($pdo)) {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=tamara;charset=utf8mb4',
            'root',
            ''
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Koneksi database gagal: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/../views/layout/header.php';
?>

<style>
  /* Brand global, bisa dipakai ulang di halaman lain */
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
  }

  .app-brand-logo-glyph {
    width: 20px;
    height: 20px;
    border-radius: 7px;
    background: rgba(255, 255, 255, .92);
    display: block;
    position: relative;
    overflow: hidden;
  }

  .app-brand-logo-glyph::before,
  .app-brand-logo-glyph::after {
    content: "";
    position: absolute;
    inset: 3px;
    border-radius: 6px;
    border: 1px solid rgba(37, 99, 235, .2);
  }

  .app-brand-logo-glyph::after {
    inset: 6px 5px 4px 5px;
    border-radius: 4px;
    border-color: rgba(16, 185, 129, .45);
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
        <a href="index.php?page=logout" class="btn btn-sm btn-danger">Logout</a>
      </div>
    </div>
  </div>

  <!-- Tiles menu utama -->
  <div class="section-pad tile-wrap">
    <h5 class="master-card-title mb-1" style="font-weight:700;">Menu Utama</h5>
        <p class="master-card-subtitle">
          Kumpulan menu utama untuk mengakses fitur-fitur inti aplikasi.
        </p>
    <div class="tile-grid">
      <?php if ($role === 'ADMIN_GUDANG' || $role === 'KEPALA_GUDANG' || $role === 'SUPERADMIN') : ?>
        <!-- Master STO -->
        <a href="index.php?page=sto_pdf" class="tile" style="text-decoration:none; color:inherit;">
          <div class="tile-body">
            <div class="tile-icon mb-2" aria-hidden="true">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <ellipse cx="12" cy="5" rx="8" ry="3" stroke="currentColor" stroke-width="1.6"></ellipse>
                <path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5" stroke="currentColor" stroke-width="1.6"></path>
                <path d="M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6" stroke="currentColor" stroke-width="1.6"></path>
              </svg>
            </div>
            <h5>Master STO</h5>
            <p>Manage dan monitoring Sales Transport Order.</p>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($role === 'ADMIN_GUDANG' || $role === 'KEPALA_GUDANG' || $role === 'SUPERADMIN') : ?>
      <!-- UPLOAD STO -->
        <a href="index.php?page=upload_sto" class="tile" style="text-decoration:none; color:inherit;">
          <div class="tile-body">
            <div class="tile-icon mb-2" aria-hidden="true">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <ellipse cx="12" cy="5" rx="8" ry="3" stroke="currentColor" stroke-width="1.6"></ellipse>
                <path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5" stroke="currentColor" stroke-width="1.6"></path>
                <path d="M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6" stroke="currentColor" stroke-width="1.6"></path>
              </svg>
            </div>
            <h5>UPLOAD STO</h5>
            <p>Manage dan monitoring Sales Transport Order.</p>
          </div>
        </a>
      <?php endif; ?>

      <!-- Laporan STO -->
      <a href="index.php?page=report" class="tile" style="text-decoration:none; color:inherit;">
        <div class="tile-body">
          <div class="tile-icon mb-2" aria-hidden="true"
               style="background:linear-gradient(140deg, var(--blue), var(--green))">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
              <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/>
              <path d="M8 8h8M8 12h8M8 16h6" stroke="currentColor" stroke-width="1.6" />
            </svg>
          </div>
          <h5>Laporan STO</h5>
          <p>Penyusunan dan rekap laporan tagihan berbasis STO.</p>
        </div>
      </a>

      <?php if ($role !== 'ADMIN_GUDANG' && $role !== 'KEPALA_GUDANG'): ?>
        <!-- Verifikasi Tagihan -->
        <a href="index.php?page=scan" class="tile" style="text-decoration:none; color:inherit;">
          <div class="tile-body">
            <div class="tile-icon mb-2" aria-hidden="true"
                 style="background:linear-gradient(140deg, var(--green), var(--blue))">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M3 3h6v6H3V3Z M15 3h6v6h-6V3Z M3 15h6v6H3v-6Z"
                      stroke="currentColor" stroke-width="1.6"></path>
                <path d="M15 15h3m3 0v6m-6 0h3m0-6v3"
                      stroke="currentColor" stroke-width="1.6"></path>
              </svg>
            </div>
            <h5>Verifikasi Tagihan</h5>
            <p>Pemeriksaan detail tagihan melalui pemindaian QR.</p>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($role === 'SUPERADMIN'): ?>
        <!-- Master Gudang -->
        <a href="index.php?page=gudang" class="tile" style="text-decoration:none; color:inherit;">
          <div class="tile-body">
            <div class="tile-icon mb-2" aria-hidden="true"
                 style="background:linear-gradient(140deg, var(--green), var(--amber))">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <path d="M3 10l9-6 9 6v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Z"
                      stroke="currentColor" stroke-width="1.6"/>
                <path d="M7 20v-6h10v6" stroke="currentColor" stroke-width="1.6"/>
              </svg>
            </div>
            <h5>Master Gudang</h5>
            <p>Pengaturan data gudang dan struktur tarif.</p>
          </div>
        </a>

        <!-- Master User -->
        <a href="index.php?page=users" class="tile" style="text-decoration:none; color:inherit;">
          <div class="tile-body">
            <div class="tile-icon mb-2" aria-hidden="true"
                 style="background:linear-gradient(140deg, var(--amber), var(--green))">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                   viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
              </svg>
            </div>
            <h5>Master User</h5>
            <p>Pengelolaan akun dan hak akses pengguna.</p>
          </div>
        </a>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php
require_once __DIR__ . '/../views/layout/footer.php';
