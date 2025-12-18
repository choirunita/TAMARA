<?php
// ================== SAFE DEFAULT ==================
$role_session = $_SESSION['role'] ?? '';
$dataSto = $dataSto ?? [];
?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-canvas">
<div class="dashboard-grid" aria-hidden="true"></div>

<div class="dash-bar section-pad py-3 mb-3">
  <div class="d-flex align-items-center justify-content-between">
    <div class="app-brand d-flex align-items-center gap-2">
      <img src="/tamara-main/public/assets/img/tamara-logo.svg" alt="Tamara" style="height:40px;">
      <div>
        <div class="fw-bold">TAMARA</div>
        <div class="text-muted small">Tagihan Termonitor, Aman dan Rapi</div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-primary"><?= htmlspecialchars($role_session) ?></span>
      <a href="index.php?page=dashboard" class="btn btn-warning btn-sm">Kembali</a>
    </div>
  </div>
</div>

<div class="section-pad">

  <h5 class="fw-bold mb-2">Daftar STO (PDF)</h5>
  <p class="text-muted mb-3">Hasil Extract File PDF yang telah diupload</p>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead style="background:#1e88e5;color:#fff">
        <tr>
          <th>No</th>
          <th>Nomor STO</th>
          <th>Tanggal Dokumen</th>
          <th>Jenis Kegiatan</th>
          <th>Asal Barang</th>
          <th>Tujuan Barang</th>
          <th>Nama Barang</th>
          <th>Qty</th>
          <th>Satuan</th>
          <th>Tarif</th>
          <th>Biaya</th>
          <th>PDF</th>
        </tr>
      </thead>
      <tbody>

      <?php if (empty($dataSto)): ?>
        <tr>
          <td colspan="11" class="text-center text-muted">
            Belum ada data STO
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($dataSto as $i => $row): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['nomor_sto']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_dokumen']) ?></td>
            <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
            <td><?= htmlspecialchars($row['asal_barang']) ?></td>
            <td><?= htmlspecialchars($row['tujuan_barang']) ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= htmlspecialchars($row['qty']) ?></td>
            <td><?= htmlspecialchars($row['satuan']) ?></td>
            <td><?= htmlspecialchars($row['tarif']) ?></td>
            <td><?= htmlspecialchars($row['biaya']) ?></td>
            <td class="text-center">
              <?php if (!empty($row['pdf_file'])): ?>
                <a
                  href="/tamara-main/public/view_pdf.php?file=<?= urlencode($row['pdf_file']) ?>"
                  target="_blank"
                  class="btn btn-sm btn-outline-primary">
                  View
                </a>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>

      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
