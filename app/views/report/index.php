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

    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['KEPALA_GUDANG', 'SUPERADMIN'], true)): ?>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="master-card-title mb-1" style="font-weight:700;">Laporan STO</h5>
          <p class="master-card-subtitle">
            Pembuatan invoice dan monitoring daftar invoice STO.
          </p>
        </div>
      </div>
    <?php endif; ?>

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
      <div class="row gy-3">
        <div class="col-md-3">
          <label class="form-label">Bulan</label>
          <select name="bulan" class="form-control" required>
            <?php foreach ($months as $m): ?>
              <option value="<?= $m ?>"><?= $m ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Jenis Pupuk</label>
          <input name="jenis_pupuk" class="form-control" placeholder="Masukkan jenis pupuk..." required>
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
          <label class="form-label">Jenis Kegiatan</label>
          <select id="sel-trans-new" name="jenis_transaksi" class="form-control" required>
            <option value="">-- Pilih --</option>
            <?php foreach ($types as $t): ?>
              <option value="<?= $t ?>"><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Uraian Pekerjaan</label>
          <input name="uraian_pekerjaan" class="form-control" placeholder="Misal: Bongkar Pupuk" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tarif Normal (Rp)</label>
          <input id="fld-normal-new" name="tarif_normal" class="form-control" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tarif Lembur (Rp)</label>
          <input id="fld-lembur-new" name="tarif_lembur" class="form-control" readonly>
        </div>
      </div>

      <hr>

      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="tbl-sto-new">
          <thead class="table-light">
            <tr>
              <th style="width:40px;">No</th>
              <th>Nomor STO</th>
              <th>Tanggal Terbit</th>
              <th>Nama Gudang</th>
              <th>Transportir</th>
              <th>Tonase Normal</th>
              <th>Tonase Lembur</th>
              <th>Jumlah</th>
              <th>Keterangan</th>
              <th style="width:40px;"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Kontrol sejajar dengan lebar tabel -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <button type="button" id="btn-add-new" class="btn btn btn-primary">
          Tambah Baris
        </button>

        <button type="submit" class="btn btn-success">
          Generate Invoice &amp; QR
        </button>
      </div>
    </form>
    <?php endif; ?>

    <!-- ============ DAFTAR INVOICE ============ -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
      <div>
        <h5 class="master-card-title mb-1" style="font-weight:700;">Daftar Invoice</h5>
        <p class="master-card-subtitle">
          Cari berdasarkan bulan, gudang, pupuk, status, atau kolom lain.
        </p>
      </div>
      <div class="input-group input-group-sm" style="max-width:260px;">
        <span class="input-group-text bg-white border-end-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </span>
        <input type="search" id="invoiceSearch" class="form-control border-start-0"
               placeholder="Cari invoice...">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle" id="invoice-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Bulan</th>
            <th>Pupuk</th>
            <th>Gudang</th>
            <th>Transaksi</th>
            <th>Uraian Pekerjaan</th>
            <th>Dibuat Pada</th>
            <th>Status Persetujuan</th>
            <th>Opsi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($invoices): ?>
            <?php foreach ($invoices as $i => $inv): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($inv['bulan']) ?></td>
                <td><?= htmlspecialchars($inv['jenis_pupuk']) ?></td>
                <td><?= htmlspecialchars($inv['nama_gudang']) ?></td>
                <td><?= htmlspecialchars($inv['jenis_transaksi']) ?></td>
                <td><?= htmlspecialchars($inv['uraian_pekerjaan']) ?></td>
                <td><?= htmlspecialchars($inv['created_at']) ?></td>
                <td><?= htmlspecialchars($inv['current_role']) ?></td>
                <td class="text-nowrap">
                  <!-- Tombol Detail: sama gaya dengan Master STO -->
                  <button type="button"
                          class="btn btn-sm btn-primary btn-view"
                          data-id="<?= $inv['id'] ?>">
                    Detail
                  </button>

                  <?php if (
                    $role === 'SUPERADMIN' ||
                    ($role === 'KEPALA_GUDANG' && $inv['current_role'] === 'KEPALA_GUDANG')
                  ): ?>
                    <button type="button"
                            class="btn btn-sm btn-warning btn-edit ms-1"
                            data-id="<?= $inv['id'] ?>">
                      Edit
                    </button>
                    <a href="index.php?page=invoice_delete&id=<?= $inv['id'] ?>"
                       class="btn btn-sm btn-danger ms-1"
                       onclick="return confirm('Hapus invoice #<?= $inv['id'] ?>?')">
                      Hapus
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted">Belum ada invoice yang dibuat.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
        <div>Menampilkan <span id="count-showing">0</span> dari <span id="count-total">0</span> data</div>
        <div>
          <label class="me-2">Tampilkan</label>
          <select id="rowsPerPage" class="form-select form-select-sm d-inline-block" style="width:80px;">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          <span>data per halaman</span>
        </div>
      </div>
    </div>

  </div> <!-- /section-pad -->
</div> <!-- /dashboard-canvas -->

<!-- Modal detail -->
<div class="modal fade" id="modalInvoice" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" id="modalInvoiceContent"></div>
  </div>
</div>

<!-- Modal EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <form id="frm-edit" class="modal-content" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Edit Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-invoice-id" name="invoice_id" />
        <div class="row gy-3">
          <div class="col-md-3">
            <label class="form-label">Bulan</label>
            <select id="edit-bulan" name="bulan" class="form-control" required>
              <?php foreach ($months as $m): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis Pupuk</label>
            <input id="edit-jp" name="jenis_pupuk" class="form-control" required>
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
            <label class="form-label">Jenis Transaksi</label>
            <select id="edit-trans" name="jenis_transaksi" class="form-control" required>
              <option value="">-- Pilih --</option>
              <?php foreach ($types as $t): ?>
                <option value="<?= $t ?>"><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Uraian Pekerjaan</label>
            <input id="edit-uraian" name="uraian_pekerjaan" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tarif Normal (Rp)</label>
            <input id="edit-tn" name="tarif_normal" class="form-control" readonly>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tarif Lembur (Rp)</label>
            <input id="edit-tl" name="tarif_lembur" class="form-control" readonly>
          </div>
        </div>

        <hr>

        <div class="table-responsive mb-3">
          <table class="table table-bordered align-middle" id="tbl-sto-edit">
            <thead class="table-light">
              <tr>
                <th style="width:40px;">No.</th>
                <th>Nomor STO</th>
                <th>Tanggal Terbit</th>
                <th>Nama Gudang</th>
                <th>Transportir</th>
                <th>Tonase Normal</th>
                <th>Tonase Lembur</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th style="width:40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <button type="button" id="btn-add-edit" class="btn btn-sm btn-primary">+ Tambah Baris</button>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-success" type="submit">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  const currentGudangId = <?= json_encode($user['id_gudang'] ?? null) ?>;
</script>

<script>
'use strict';

function formatRupiahNumber(num) {
  if (num === null || num === undefined || num === '') return '';
  const number = Number(num);
  if (!Number.isFinite(number)) return '';
  // hasil: "Rp 22.400"
  return number.toLocaleString('id-ID');
}

function cleanRupiahString(str) {
  // "Rp 22.400" -> "22400"
  return String(str || '').replace(/[^0-9]/g, '');
}


document.addEventListener('DOMContentLoaded', function() {
  // Data dari PHP
  const stoData            = <?= json_encode($stoDataPHP         ?? [], JSON_UNESCAPED_UNICODE) ?>;
  const stoOpts            = <?= json_encode($stoOpts            ?? [], JSON_UNESCAPED_UNICODE) ?>;
  const invoiceData        = <?= json_encode($invoiceData        ?? [], JSON_UNESCAPED_UNICODE) ?>;
  const invoiceLines       = <?= json_encode($invoiceLines       ?? [], JSON_UNESCAPED_UNICODE) ?>;
  const invoiceLineDetails = <?= json_encode($invoiceLineDetails ?? [], JSON_UNESCAPED_UNICODE) ?>;

  const tarifData = {
    normal: <?= json_encode($tarif_normal ?? null) ?>,
    lembur: <?= json_encode($tarif_lembur ?? null) ?>,
  };

  const selTransNew = document.getElementById('sel-trans-new');
if (selTransNew) {
  selTransNew.addEventListener('change', function () {
    const jenis        = this.value;
    const normalField  = document.getElementById('fld-normal-new');
    const lemburField  = document.getElementById('fld-lembur-new');

        if (jenis === 'BONGKAR' || jenis === 'MUAT') {
      normalField.value = formatRupiahNumber(tarifData.normal);
      lemburField.value = formatRupiahNumber(tarifData.lembur);
    } else {
      normalField.value = '';
      lemburField.value = '';
    }

  });
}

const frmCreate = document.getElementById('frm-create');
  if (frmCreate) {
    frmCreate.addEventListener('submit', function () {
      const normalInput = document.getElementById('fld-normal-new');
      const lemburInput = document.getElementById('fld-lembur-new');

      if (normalInput) {
        normalInput.value = cleanRupiahString(normalInput.value);
      }
      if (lemburInput) {
        lemburInput.value = cleanRupiahString(lemburInput.value);
      }
    });
  }

  // === FILTER STO BERDASARKAN GUDANG & JENIS TRANSAKSI ===
  function getFilteredSTOOpts(context = 'create') {
    let selectedGudang, selectedJenis;

    if (context === 'edit') {
      selectedGudang = $('#edit-gdg').val() || currentGudangId;
      selectedJenis  = $('#edit-trans').val();
    } else {
      selectedGudang = $('#gudang_id').val() || currentGudangId;
      selectedJenis  = $('#sel-trans-new').val();
    }

    if (!selectedGudang || !selectedJenis) return [];
    return stoOpts.filter(opt =>
      String(opt.gudang_id) === String(selectedGudang) &&
      opt.jenis_transaksi === selectedJenis
    );
  }

  function buildSelectData(extraRows, context = 'create') {
    const filtered = getFilteredSTOOpts(context);
    const base     = filtered.slice();
    const has      = new Set(base.map(x => x.id));
    (extraRows || []).forEach(r => {
      const rid = parseInt(r.id);
      if (!has.has(rid)) {
        base.push({ id: rid, text: (r.nomor_sto || r.text || '') });
        if (!stoData[rid]) stoData[rid] = r;
      }
    });
    return base;
  }

  function renumber($tbody) {
    $tbody.find('tr').each((i, tr) => {
      $(tr).find('td.no').text(i + 1);
      $(tr).find('.rm').toggle(i > 0);
    });
  }

  function refreshCreateTable() {
    const $tbody = $('#tbl-sto-new tbody');
    $tbody.find('tr').each(function () {
      const $sel = $(this).find('.sto-sel');
      if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
      }
    });
    $tbody.empty();
    addRowCreate($tbody, buildSelectData([]));
  }

  // === ROW CREATE ===
  function addRowCreate($tbody, selectData, selected = null, detail = null) {
    const $r = $(`
      <tr>
        <td class="no"></td>
        <td><select name="sto_ids[]" class="form-control sto-sel" required><option></option></select></td>
        <td class="tgl"></td><td class="gdg"></td><td class="trp"></td>
        <td class="norm"></td><td class="lemb"></td><td class="jml"></td><td class="ket"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger rm">–</button></td>
      </tr>
    `);
    $tbody.append($r);
    renumber($tbody);

    const sel = $r.find('.sto-sel').select2({
      data: selectData,
      placeholder: 'Cari Nomor STO…',
      allowClear: true,
      width: '100%'
    })
    .on('select2:select', e => {
      const id = e.params.data.id;
      const d  = stoData[id] || {};
      $r.find('.tgl').text(d.tanggal_terbit || d.tanggal || '');
      $r.find('.gdg').text(d.nama_gudang || '');
      $r.find('.trp').text(d.transportir || '');
      $r.find('.norm').text(d.tonase_normal || '');
      $r.find('.lemb').text(d.tonase_lembur || '');
      const j = (parseFloat(d.tonase_normal || 0) + parseFloat(d.tonase_lembur || 0));
      $r.find('.jml').text(isNaN(j) ? '' : j);
      $r.find('.ket').text(d.keterangan || '');
    })
    .on('select2:clear change', () => {
      if (!sel.val()) {
        $r.find('.tgl,.gdg,.trp,.norm,.lemb,.jml,.ket').text('');
      }
    });

    if (selected) {
      sel.val(String(selected)).trigger('change');
      const d = detail || stoData[selected] || {};
      $r.find('.tgl').text(d.tanggal_terbit || d.tanggal || '');
      $r.find('.gdg').text(d.nama_gudang || '');
      $r.find('.trp').text(d.transportir || '');
      $r.find('.norm').text(d.tonase_normal || '');
      $r.find('.lemb').text(d.tonase_lembur || '');
      const j = (parseFloat(d.tonase_normal || 0) + parseFloat(d.tonase_lembur || 0));
      $r.find('.jml').text(isNaN(j) ? '' : j);
      $r.find('.ket').text(d.keterangan || '');
    }

    $r.find('.rm').click(() => {
      $r.remove();
      renumber($tbody);
    });
  }

  function bindTarifNewAjax() {
  const gudang = $('#gudang_id').val();
  const jenis  = $('#sel-trans-new').val();
  if (!gudang || !jenis) return;

  $.ajax({
    url: 'ajax/get_tarif.php',
    method: 'GET',             // sesuaikan dengan get_tarif.php
    data: { gudang_id: gudang, jenis_transaksi: jenis },
    dataType: 'json',
    success: function(data) {
     const tn = data?.tarif_normal || '';
const tl = data?.tarif_lembur || '';

$('#fld-normal-new').val(formatRupiahNumber(tn));
$('#fld-lembur-new').val(formatRupiahNumber(tl));

    },
    error: function() {
      $('#fld-normal-new').val('');
      $('#fld-lembur-new').val('');
    }
  });
}


  $('#sel-trans-new').on('change', function () {
    bindTarifNewAjax();
    refreshCreateTable();
  });

  $('#tbl-sto-new tbody').empty();
  $('#btn-add-new').click(() => {
    const selectedGudang = $('#gudang_id').val();
    const selectedJenis  = $('#sel-trans-new').val();

    if (!selectedGudang || !selectedJenis) {
      alert('Silakan pilih Gudang dan Jenis Kegiatan terlebih dahulu!');
      return;
    }
    addRowCreate($('#tbl-sto-new tbody'), buildSelectData([]));
  });

  // VIEW
  $(document).on('click', '.btn-view', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    $.get('index.php?page=invoice_view_partial&id=' + id, html => {
      $('#modalInvoiceContent').html(html);
      const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalInvoice'));
      m.show();
    });
  });

  // ===== EDIT =====
  function getSelectedIds($tbody) {
    const s = new Set();
    $tbody.find('select.sto-sel').each(function () {
      const v = $(this).val();
      if (v) s.add(parseInt(v));
    });
    return s;
  }

  function rebuildOneSelect($sel, baseData, selectedIds) {
    const keep = $sel.val() ? parseInt($sel.val()) : null;

    const data = baseData.map(o => ({
      id: o.id,
      text: o.text,
      disabled: (selectedIds.has(o.id) && o.id !== keep)
    }));

    $sel.off('select2:select select2:clear change');
    if ($sel.hasClass('select2-hidden-accessible')) {
      $sel.select2('destroy');
    }

    $sel.empty().append('<option></option>');
    $sel.select2({
      data,
      placeholder: 'Cari Nomor STO…',
      allowClear: true,
      width: '100%',
      dropdownParent: $('#modalEdit')
    });

    if (keep) $sel.val(String(keep)).trigger('change');

    const $row = $sel.closest('tr');

    $sel.on('select2:select', e => {
      const id = e.params.data.id;
      const d  = stoData[id] || {};
      $row.find('.tgl').text(d.tanggal_terbit || d.tanggal || '');
      $row.find('.gdg').text(d.nama_gudang || '');
      $row.find('.trp').text(d.transportir || '');
      $row.find('.norm').text(d.tonase_normal || '');
      $row.find('.lemb').text(d.tonase_lembur || '');
      const j = (parseFloat(d.tonase_normal || 0) + parseFloat(d.tonase_lembur || 0));
      $row.find('.jml').text(isNaN(j) ? '' : j);
      $row.find('.ket').text(d.keterangan || '');
      refreshAll($row.closest('tbody'), baseData);
    }).on('select2:clear change', () => {
      if (!$sel.val()) {
        $row.find('.tgl,.gdg,.trp,.norm,.lemb,.jml,.ket').text('');
        refreshAll($row.closest('tbody'), baseData);
        setTimeout(() => { $sel.select2('open'); }, 0);
      }
    });
  }

  function refreshAll($tbody, baseData) {
    const selectedIds = getSelectedIds($tbody);
    $tbody.find('select.sto-sel').each(function () {
      rebuildOneSelect($(this), baseData, selectedIds);
    });
  }

  function addRowEdit($tbody, baseData, selected = null, detail = null) {
    const $r = $(`
      <tr>
        <td class="no"></td>
        <td><select name="sto_ids[]" class="form-control sto-sel"><option></option></select></td>
        <td class="tgl"></td><td class="gdg"></td><td class="trp"></td>
        <td class="norm"></td><td class="lemb"></td><td class="jml"></td><td class="ket"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger rm">–</button></td>
      </tr>
    `);
    $tbody.append($r);
    renumber($tbody);

    rebuildOneSelect($r.find('.sto-sel'), baseData, getSelectedIds($tbody));

    if (selected) {
      $r.find('.sto-sel').val(String(selected)).trigger('change');
      const d = detail || stoData[selected] || {};
      $r.find('.tgl').text(d.tanggal_terbit || d.tanggal || '');
      $r.find('.gdg').text(d.nama_gudang || '');
      $r.find('.trp').text(d.transportir || '');
      $r.find('.norm').text(d.tonase_normal || '');
      $r.find('.lemb').text(d.tonase_lembur || '');
      const j = (parseFloat(d.tonase_normal || 0) + parseFloat(d.tonase_lembur || 0));
      $r.find('.jml').text(isNaN(j) ? '' : j);
      $r.find('.ket').text(d.keterangan || '');
      refreshAll($tbody, baseData);
    }

    $r.find('.rm').on('click', () => {
      $r.remove();
      renumber($tbody);
      refreshAll($tbody, baseData);
    });
  }

  $(document).on('click', '.btn-edit', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const id   = Number($(this).data('id'));
    const hdr  = (invoiceData && (invoiceData[id] || invoiceData[String(id)])) || null;
    if (!hdr) {
      alert('Data invoice tidak ditemukan. Silakan reload halaman.');
      return;
    }

    $('#edit-invoice-id').val(id);
    $('#edit-bulan').val(hdr.bulan || '');
    $('#edit-jp').val(hdr.jenis_pupuk || '');
    $('#edit-gdg').val(hdr.gudang_id || '').trigger('change');
    $('#edit-trans').val(hdr.jenis_transaksi || '').trigger('change');
    $('#edit-uraian').val(hdr.uraian_pekerjaan || '');
    $('#edit-tn').val(hdr.tarif_normal || '');
    $('#edit-tl').val(hdr.tarif_lembur || '');

    const lines  = (invoiceLines && (invoiceLines[id] || invoiceLines[String(id)])) || [];
    const detail = (invoiceLineDetails && (invoiceLineDetails[id] || invoiceLineDetails[String(id)])) || [];

    const $tb      = $('#tbl-sto-edit tbody');
    $tb.empty();
    const baseData = buildSelectData(detail, 'edit');

    if (detail.length) {
      detail.forEach(d => addRowEdit($tb, baseData, d.id, d));
    } else if (lines.length) {
      lines.forEach(sid => addRowEdit($tb, baseData, sid, {}));
    } else {
      addRowEdit($tb, baseData);
    }

    $('#btn-add-edit').off('click').on('click', () => addRowEdit($tb, baseData));
    $('#frm-edit').attr('action', 'index.php?page=report_update&id=' + id);

    const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit'));
    m.show();
  });

  function bindTarifEdit() {
    const g = $('#edit-gdg').val(),
          t = $('#edit-trans').val();
    if (!g || !t) return;
    $.getJSON('ajax/get_tarif.php', { gudang_id: g, jenis_transaksi: t })
      .done(d => {
        $('#edit-tn').val(d.tarif_normal);
        $('#edit-tl').val(d.tarif_lembur);
      });
  }
  $('#edit-gdg,#edit-trans').on('change', bindTarifEdit);
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const table          = document.querySelector("#invoice-table");
  const tbody          = table.querySelector("tbody");
  const allRowsRaw     = Array.from(tbody.querySelectorAll("tr"));
  const emptyRow       = allRowsRaw.find(r => r.textContent.includes("Belum ada invoice")) || null;
  const allRows        = emptyRow ? allRowsRaw.filter(r => r !== emptyRow) : allRowsRaw;

  const countShowing   = document.getElementById("count-showing");
  const countTotal     = document.getElementById("count-total");
  const rowsPerPageSel = document.getElementById("rowsPerPage");
  const searchInput    = document.getElementById("invoiceSearch");

  let filteredRows = [...allRows];
  let totalRows    = filteredRows.length;
  let rowsPerPage  = parseInt(rowsPerPageSel.value);
  let totalPages   = totalRows === 0 ? 0 : Math.ceil(totalRows / rowsPerPage);
  let currentPage  = 1;
  let pagination   = null;
  let debounceId   = null;

  function showPage(page) {
    currentPage = page;
    const start = (page - 1) * rowsPerPage;
    const end   = start + rowsPerPage;

    allRows.forEach(r => r.style.display = "none");
    if (emptyRow) emptyRow.style.display = "none";

    if (totalRows === 0) {
      if (emptyRow) emptyRow.style.display = "";
      countShowing.textContent = 0;
      countTotal.textContent   = 0;
    } else {
      filteredRows.forEach((row, idx) => {
        row.style.display = (idx >= start && idx < end) ? "" : "none";
      });
      const showing = Math.min(end, totalRows);
      countShowing.textContent = showing;
      countTotal.textContent   = totalRows;
    }

    if (pagination) {
      document.querySelectorAll(".pagination .page-item").forEach(btn => btn.classList.remove("active"));
      const activeBtn = document.querySelector(`.pagination .page-item[data-page="${page}"]`);
      if (activeBtn) activeBtn.classList.add("active");

      const prev = document.getElementById("prevPage");
      const next = document.getElementById("nextPage");
      if (prev && next) {
        prev.classList.toggle("disabled", currentPage === 1);
        next.classList.toggle("disabled", currentPage === totalPages || totalPages === 0);
      }
    }
  }

  function buildPagination() {
    if (pagination) pagination.remove();

    totalRows   = filteredRows.length;
    rowsPerPage = parseInt(rowsPerPageSel.value);
    totalPages  = totalRows === 0 ? 0 : Math.ceil(totalRows / rowsPerPage);

    if (totalRows === 0) {
      countShowing.textContent = 0;
      countTotal.textContent   = 0;
      if (emptyRow) emptyRow.style.display = "";
      return;
    }

    pagination = document.createElement("ul");
    pagination.className = "pagination justify-content-center mt-3";

    const prevLi = document.createElement("li");
    prevLi.className = "page-item disabled";
    prevLi.id        = "prevPage";
    prevLi.innerHTML = `<a class="page-link" href="#">← Prev</a>`;
    pagination.appendChild(prevLi);

    for (let i = 1; i <= totalPages; i++) {
      const li   = document.createElement("li");
      li.className   = "page-item";
      li.dataset.page= i;
      li.innerHTML   = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener("click", e => {
        e.preventDefault();
        showPage(i);
      });
      pagination.appendChild(li);
    }

    const nextLi = document.createElement("li");
    nextLi.className = "page-item";
    nextLi.id        = "nextPage";
    nextLi.innerHTML = `<a class="page-link" href="#">Next →</a>`;
    pagination.appendChild(nextLi);

    prevLi.addEventListener("click", e => {
      e.preventDefault();
      if (currentPage > 1) showPage(currentPage - 1);
    });
    nextLi.addEventListener("click", e => {
      e.preventDefault();
      if (currentPage < totalPages) showPage(currentPage + 1);
    });

    table.insertAdjacentElement("afterend", pagination);
    showPage(1);
  }

  function applySearch() {
    const q = searchInput.value.toLowerCase().trim();
    if (!q) {
      filteredRows = [...allRows];
    } else {
      filteredRows = allRows.filter(row =>
        row.textContent.toLowerCase().includes(q)
      );
    }
    currentPage = 1;
    buildPagination();
  }

  if (allRows.length === 0) {
    countShowing.textContent = 0;
    countTotal.textContent   = 0;
  } else {
    buildPagination();
  }

  rowsPerPageSel.addEventListener("change", () => {
    buildPagination();
  });

  searchInput.addEventListener("input", () => {
    if (debounceId) clearTimeout(debounceId);
    debounceId = setTimeout(applySearch, 180);
  });
});
</script>
