<?php
// app/views/report/invoice_view_partial.php
// tersedia setelah controller:
//   $inv, $lines, $totalNorm, $totalLemb, $totalAll, $qrImage
?>
<style>
  .invoice-view-shell {
    padding-top: .75rem;
    font-size: 12px;
  }

  .invoice-view-shell .inv-header-card {
    position: relative;
    border-radius: 16px;
    background: var(--surface-soft, #f8fafc);
    border: 1px solid var(--border-subtle, #e2e8f0);
    padding: 1.1rem 1.1rem 1rem 1.1rem;
    margin-bottom: 1rem;
  }

  /* blok atas: logo + judul */
  .invoice-view-shell .inv-header-top {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: .4rem;
  }

  .invoice-view-shell .inv-logo-wrap {
    flex: 0 0 auto;
  }

  .invoice-view-shell .inv-logo {
    height: 42px;
    width: auto;
    display: block;
  }

  .invoice-view-shell .inv-title-block {
    flex: 1 1 auto;
  }

  .invoice-view-shell .inv-company {
    font-size: .78rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #0f172a;
    margin-bottom: .1rem;
  }

  .invoice-view-shell .inv-title {
    font-size: .95rem;
    font-weight: 600;
    color: var(--text, #0f172a);
    margin-bottom: .15rem;
  }

  .invoice-view-shell .inv-subtitle {
    font-size: .75rem;
    color: var(--text-muted, #64748b);
  }

  .invoice-view-shell .qr-code {
    position: absolute;
    top: .9rem;
    right: 1rem;
    width: 96px;
    height: 96px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(148, 163, 184, .5);
    padding: 4px;
    object-fit: contain;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .15);
  }

  .invoice-view-shell .info-header {
    width: 100%;
    border-collapse: collapse;
    margin-top: .45rem;
  }

  .invoice-view-shell .info-header td {
    vertical-align: top;
    padding: .15rem 1.1rem .15rem 0;
    font-size: .78rem;
    color: #0f172a;
  }

  .invoice-view-shell .info-header strong {
    font-weight: 600;
    color: #111827;
  }

  .invoice-view-shell .info-header td:nth-child(2) {
    padding-left: 1.5rem;
    border-left: 1px dashed rgba(148, 163, 184, .7);
  }

  .invoice-view-shell .table-wrapper {
    border-radius: 14px;
    border: 1px solid var(--border-subtle, #e2e8f0);
    overflow-x: auto; /* mobile bisa scroll horizontal */
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
    margin-top: 1.1rem;
  }

  .invoice-view-shell .table-print {
    width: 100%;
    border-collapse: collapse;
    font-size: .76rem;
    min-width: 720px; /* agar kolom tidak terlalu sempit di mobile */
  }

  .invoice-view-shell .table-print th,
  .invoice-view-shell .table-print td {
    border: 1px solid rgba(148, 163, 184, .9);
    padding: 6px 8px;
  }

  .invoice-view-shell .table-print thead th {
    background: #f1f5f9;
    font-weight: 600;
    text-align: center;
    color: #0f172a;
  }

  .invoice-view-shell .table-print .c { text-align: center; }
  .invoice-view-shell .table-print .r { text-align: right; }

  .invoice-view-shell .table-print tr.tot td {
    border-top: 2px solid #0f172a;
    font-weight: 600;
    background: #f8fafc;
  }

  .invoice-view-shell .footer-totals {
    width: 100%;
    font-size: .78rem;
    margin-top: 1rem;
  }

  .invoice-view-shell .footer-totals td {
    padding: 4px 6px;
    color: #0f172a;
  }

  .invoice-view-shell .footer-totals .r {
    text-align: right;
    font-weight: 600;
  }

  .invoice-view-shell .footer-totals tr:last-child td {
    border-top: 1px dashed rgba(148, 163, 184, .9);
    padding-top: .6rem;
  }

  /* ===== Responsif mobile ===== */
  @media (max-width: 768px) {
    .invoice-view-shell {
      padding-top: .5rem;
      font-size: 11px;
    }

    .invoice-view-shell .inv-header-card {
      padding: .75rem .75rem .7rem .75rem;
      margin-bottom: .75rem;
    }

    .invoice-view-shell .inv-logo {
      height: 34px;
    }

    .invoice-view-shell .qr-code {
      width: 72px;
      height: 72px;
      top: .7rem;
      right: .7rem;
      box-shadow: 0 6px 16px rgba(15, 23, 42, .12);
    }

    .invoice-view-shell .info-header td {
      display: block;
      padding-right: 0;
      border-left: none;
    }

    .invoice-view-shell .info-header td:nth-child(2) {
      padding-left: 0;
      border-left: none;
      margin-top: .4rem;
    }

    .invoice-view-shell .table-wrapper {
      margin-top: .75rem;
      border-radius: 10px;
    }

    .invoice-view-shell .table-print {
      font-size: .7rem;
    }
  }

  /* ===== Mode cetak ===== */
  @media print {
    @page {
      size: A4 landscape;
      margin: 8mm;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: auto;
    }

    body.printing-invoice {
      background: #ffffff !important;
    }

    body.printing-invoice > *:not(#modalInvoice) {
      display: none !important;
    }

    #modalInvoice {
      position: static !important;
      display: block !important;
      overflow: visible !important;
    }

    #modalInvoice .modal-dialog {
      max-width: 100% !important;
      width: 100% !important;
      margin: 0 !important;
      transform: none !important;
    }

    #modalInvoice .modal-content {
      border: none !important;
      box-shadow: none !important;
    }

    .modal-backdrop,
    .btn-close,
    .modal-footer {
      display: none !important;
    }

    .invoice-view-shell {
      padding-top: 0;
      font-size: 10px;
    }

    .invoice-view-shell .inv-header-card {
      box-shadow: none !important;
      margin-bottom: .4rem;
      padding: .6rem .7rem .5rem .7rem;
    }

    .invoice-view-shell .inv-logo {
      height: 34px;
    }

    .invoice-view-shell .table-wrapper {
      box-shadow: none !important;
      margin-top: .5rem;
      overflow: visible !important;
    }

    .invoice-view-shell .table-print {
      font-size: .7rem;
      page-break-inside: auto;
    }

    .invoice-view-shell .table-print th,
    .invoice-view-shell .table-print td {
      padding: 3px 5px;
    }

    .invoice-view-shell .table-print thead {
      display: table-header-group;
    }

    .invoice-view-shell .table-print tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    .invoice-view-shell .footer-totals {
      margin-top: .35rem;
      font-size: .7rem;
    }
  }
</style>

<div class="modal-header border-0 pb-0">
  <h5 class="modal-title">Detail Tagihan #<?= $inv['id'] ?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-2">
  <div class="invoice-view-shell">

    <!-- Header invoice: logo + judul + QR -->
    <div class="inv-header-card">
      <img src="<?= htmlspecialchars($qrImage) ?>" class="qr-code" alt="QR Invoice">

      <div class="inv-header-top">
        <div class="inv-logo-wrap">
          <img src="assets/img/pcs.png"
               alt="Logo PT Petrokopindo Cipta Selaras"
               class="inv-logo">
        </div>
        <div class="inv-title-block">
          <div class="inv-title">Tagihan</div>
          <div class="inv-subtitle">
            Ringkasan detail tagihan dan tonase bongkar per STO.
          </div>
        </div>
      </div>
      
      <table class="info-header">
        <tr>
          <td>
            <strong>BULAN</strong> : <?= htmlspecialchars($inv['bulan']) ?><br>
            <strong>JENIS KEGIATAN</strong> : <?= htmlspecialchars($inv['jenis_transaksi']) ?><br>
            <strong>TARIF NORMAL</strong> : Rp <?= number_format($inv['tarif_normal'], 0, ',', '.') ?><br>
            <strong>TARIF LEMBUR</strong> : Rp <?= number_format($inv['tarif_lembur'], 0, ',', '.') ?>
          </td>
          <td>
            <strong>JENIS PUPUK</strong> : <?= htmlspecialchars($inv['jenis_pupuk']) ?><br>
            <strong>GUDANG</strong> : <?= htmlspecialchars($inv['nama_gudang']) ?><br>
            <strong>URAIAN PEKERJAAN</strong> : <?= htmlspecialchars($inv['uraian_pekerjaan']) ?><br>
            <strong>DIBUAT PADA</strong> : <?= $inv['created_at'] ?>
          </td>
        </tr>
      </table>
    </div>

    <!-- Tabel detail -->
    <div class="table-wrapper">
      <table class="table-print">
        <thead>
          <tr>
            <th rowspan="2">NO</th>
            <th rowspan="2">NOMOR<br>SALES TRANSPORT ORDER</th>
            <th rowspan="2">TANGGAL<br>TERBIT</th>
            <th rowspan="2">TRANSPORTIR</th>
            <th colspan="3">TONASE BONGKAR</th>
          </tr>
          <tr>
            <th>NORMAL (TON)</th>
            <th>LEMBUR (TON)</th>
            <th>JUMLAH (RP)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lines as $i => $ln):
            $sub = $ln['tonase_normal'] * $inv['tarif_normal']
                 + $ln['tonase_lembur'] * $inv['tarif_lembur'];
          ?>
            <tr>
              <td class="c"><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($ln['nomor_sto']) ?></td>
              <td class="c"><?= date('d-m-Y', strtotime($ln['tanggal_terbit'])) ?></td>
              <td><?= htmlspecialchars($ln['transportir']) ?></td>
              <td class="r"><?= number_format($ln['tonase_normal'], 0, ',', '.') ?></td>
              <td class="r"><?= number_format($ln['tonase_lembur'], 0, ',', '.') ?></td>
              <td class="r"><?= number_format($sub, 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>

          <?php for ($j = count($lines) + 1; $j <= 10; $j++): ?>
            <tr>
              <td class="c"><?= $j ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          <?php endfor; ?>

          <tr class="tot">
            <td colspan="4" class="r">TOTAL (RP)</td>
            <td class="r"><?= number_format($totalNorm, 0, ',', '.') ?></td>
            <td class="r"><?= number_format($totalLemb, 0, ',', '.') ?></td>
            <td class="r"><?= number_format($totalAll, 0, ',', '.') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Ringkasan total rupiah -->
    <table class="footer-totals">
      <tr>
        <td>TOTAL BONGKAR NORMAL :</td>
        <td class="r">Rp <?= number_format($totalNorm, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <td>TOTAL BONGKAR LEMBUR :</td>
        <td class="r">Rp <?= number_format($totalLemb, 0, ',', '.') ?></td>
      </tr>
      <tr>
        <td>TOTAL :</td>
        <td class="r">Rp <?= number_format($totalAll, 0, ',', '.') ?></td>
      </tr>
    </table>

  </div>
</div>

<div class="modal-footer border-0 pt-0">
  <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Tutup</button>
  <button type="button" class="btn btn-sm btn-success" onclick="printInvoice()">Print</button>
</div>

<script>
  function printInvoice() {
    window.scrollTo(0, 0);
    document.body.classList.add('printing-invoice');

    setTimeout(function () {
      window.print();
      setTimeout(function () {
        document.body.classList.remove('printing-invoice');
      }, 0);
    }, 50);
  }
</script>
