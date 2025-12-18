<?php
$role_session = $_SESSION['role'] ?? '';
?>

<div class="dashboard-canvas">
    <div class="dashboard-grid" aria-hidden="true"></div>

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

        /* Skala font isi konten sama seperti Master STO */
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

        .master-card {
            border-radius: 18px;
            border: 1px solid var(--border-subtle, #e2e8f0);
            background: #ffffff;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .06);
            padding: 1.4rem 1.6rem 1.3rem 1.6rem;
            font-size: .88rem;
        }
        .master-card + .master-card {
            margin-top: 1.5rem;
        }
        .master-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }
        .master-card-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }
        .master-card-subtitle {
            font-size: .78rem;
            color: #64748b;
            margin: 0;
        }

        .form-section-label {
            font-size: .8rem;
            color: #64748b;
        }

        /* Style tabel mengikuti Master STO */
        .table thead th {
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            background: #f8fafc;
        }
        .table td,
        .table th {
            vertical-align: middle;
            font-size: .78rem;
        }
    </style>

    <style>
    /* Skala umum isi modal sedikit lebih kecil */
    #wilayahModal .modal-content,
    #gudangModal .modal-content,
    #tarifModal .modal-content {
        font-size: 0.9rem;
    }

    /* Judul modal */
    #wilayahModal .modal-title,
    #gudangModal .modal-title,
    #tarifModal .modal-title {
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Label dan teks kecil */
    #wilayahModal .form-label,
    #wilayahModal .form-text,
    #gudangModal .form-label,
    #gudangModal .form-text,
    #tarifModal .form-label,
    #tarifModal .form-text {
        font-size: 0.8rem;
    }

    /* Input & select */
    #wilayahModal .form-control,
    #wilayahModal .form-select,
    #gudangModal .form-control,
    #gudangModal .form-select,
    #tarifModal .form-control,
    #tarifModal .form-select {
        font-size: 0.8rem;
    }

    /* Tombol footer */
    #wilayahModal .btn,
    #gudangModal .btn,
    #tarifModal .btn {
        font-size: 0.8rem;
    }
</style>

    <!-- TOP BAR -->
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
                    <span><?= htmlspecialchars($role_session) ?></span>
                </span>
                <a href="index.php?page=dashboard" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>

    <!-- KONTEN -->
    <div class="section-pad section-body pb-5">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ===================================== -->
        <!-- 1. KELOLA WILAYAH -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Kelola Nama Wilayah</h5>
                    <p class="master-card-subtitle">
                        Tambah atau ubah daftar wilayah beserta penanggung jawab admin wilayah.
                    </p>
                </div>
                <button type="button"
                        class="btn btn-sm btn-primary"
                        onclick="openWilayahModal('tambah')">
                    Tambah Wilayah
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Wilayah</th>
                        <th>Nama Admin Wilayah</th>
                        <th>Role</th>
                        <th style="width:160px;">Opsi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($user_admin_wilayah) && is_array($user_admin_wilayah)): ?>
                        <?php foreach ($user_admin_wilayah as $i => $g): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($g['wilayah'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($g['nama'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($g['role'] ?? '-') ?></td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning me-1"
                                        onclick='openWilayahModal(
                                            "edit",
                                            <?= json_encode($g["id_wilayah"]) ?>,
                                            <?= json_encode($g["wilayah"]) ?>,
                                            <?= json_encode($g["id_user"]) ?>
                                        )'>
                                        Edit
                                    </button>
                                    <a href="index.php?page=gudang&action=admin_wilayah&delete_wilayah=<?= (int)$g['id_wilayah'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus wilayah dan relasinya?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Belum ada data admin wilayah.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- 2. KELOLA NAMA GUDANG -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Kelola Nama Gudang</h5>
                    <p class="master-card-subtitle">
                        Mengelola daftar gudang per wilayah untuk kebutuhan transaksi STO dan tarif.
                    </p>
                </div>
                <button type="button"
                        class="btn btn-sm btn-primary"
                        onclick="openGudangModal('tambah')">
                    Tambah Gudang
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Gudang</th>
                        <th>Wilayah</th>
                        <th style="width:140px;">Opsi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($gudangList) && is_array($gudangList)): ?>
                        <?php foreach ($gudangList as $i => $g): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($g['nama_gudang']) ?></td>
                                <td><?= htmlspecialchars($g['wilayah'] ?? '-') ?></td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning me-1"
                                        onclick='openGudangModal(
                                            "edit",
                                            <?= json_encode($g["id"]) ?>,
                                            <?= json_encode($g["nama_gudang"]) ?>,
                                            <?= json_encode($g["id_wilayah"] ?? "") ?>
                                        )'>
                                        Edit
                                    </button>
                                    <a href="index.php?page=gudang&action=nama&delete=<?= (int)($g['id'] ?? 0) ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus nama gudang ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada data gudang.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- 3. KELOLA TARIF GUDANG -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Kelola Tarif Gudang</h5>
                    <p class="master-card-subtitle">
                        Atur tarif bongkar/muat per gudang sebagai dasar perhitungan invoice.
                    </p>
                </div>
                <button type="button"
                        class="btn btn-sm btn-primary"
                        onclick="openTarifModal('tambah')">
                    Tambah Tarif
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Gudang</th>
                        <th>Jenis Transaksi</th>
                        <th>Tarif Normal (Rp/ton)</th>      <!-- ganti -->
                        <th>Tarif Lembur (Rp/ton)</th>      <!-- ganti -->
                        <th style="width:160px;">Opsi</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tarifList) && is_array($tarifList)): ?>
                            <?php foreach ($tarifList as $i => $t): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($t['nama_gudang']) ?></td>
                                    <td><?= htmlspecialchars($t['jenis_transaksi']) ?></td>
                                    <td><?= 'Rp ' . number_format($t['tarif_normal'], 0, ',', '.') ?></td>
                                    <td><?= 'Rp ' . number_format($t['tarif_lembur'], 0, ',', '.') ?></td>
                                    <td class="text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning me-1"
                                            onclick="openTarifModal(
                                                'edit',
                                                <?= (int)$t['id'] ?>,
                                                <?= (int)$t['gudang_id'] ?>,
                                                '<?= $t['jenis_transaksi'] ?>',
                                                <?= $t['tarif_normal'] ?>,
                                                <?= $t['tarif_lembur'] ?>
                                            )">
                                            Edit
                                        </button>
                                        <a href="index.php?page=gudang&action=tarif&delete=<?= (int)$t['id'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus tarif ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada tarif gudang.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                </table>
            </div>
        </div>

    </div> <!-- /section-body -->
</div> <!-- /dashboard-canvas -->

<!-- ===================================================== -->
<!-- MODAL WILAYAH (ADD / EDIT) -->
<!-- ===================================================== -->
<div class="modal fade" id="wilayahModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="wilayahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wilayahModalLabel">Tambah Wilayah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <form id="wilayahForm" method="POST" action="index.php?page=gudang&action=wilayah">
                <input type="hidden" name="id" id="wilayah-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Wilayah</label>
                            <input type="text" name="wilayah" id="wilayah-nama"
                                   class="form-control" placeholder="Masukkan wilayah" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Wilayah (opsional)</label>
                            <select name="id_user" id="wilayah-admin" class="form-select">
                                <option value="">-- Pilih Admin Wilayah --</option>
                                <?php foreach ($admin_wilayahList as $w): ?>
                                    <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Kosongkan jika admin wilayah belum dibuat.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================== -->
<!-- MODAL GUDANG (ADD / EDIT) -->
<!-- ===================================================== -->
<div class="modal fade" id="gudangModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="gudangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gudangModalLabel">Tambah Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <form id="gudangForm" method="POST" action="index.php?page=gudang&action=nama">
                <input type="hidden" name="id" id="gudang-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Gudang</label>
                            <input type="text" name="nama_gudang" id="gudang-nama"
                                   class="form-control" placeholder="Nama Gudang" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Wilayah</label>
                            <select name="id_wilayah" id="gudang-wilayah" class="form-select" required>
                                <option value="">-- Pilih Wilayah --</option>
                                <?php foreach ($wilayahList as $w): ?>
                                    <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['wilayah']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================== -->
<!-- MODAL TARIF (ADD / EDIT) -->
<!-- ===================================================== -->
<div class="modal fade" id="tarifModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="tarifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tarifModalLabel">Tambah Tarif Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <form id="tarifForm" method="POST" action="index.php?page=gudang&action=tarif">
                <input type="hidden" name="id" id="tarif-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Gudang</label>
                            <select name="gudang_id" id="tarif-gudang" class="form-select" required>
                                <option value="">-- Pilih Gudang --</option>
                                <?php if (!empty($gudangList) && is_array($gudangList)): ?>
                                    <?php foreach ($gudangList as $g): ?>
                                        <option value="<?= $g['id'] ?>">
                                            <?= htmlspecialchars($g['nama_gudang']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="jenis_transaksi" id="tarif-jenis" class="form-select" required>
                                <option value="BONGKAR">Bongkar</option>
                                <option value="MUAT">Muat</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tarif Normal (Rp/ton)</label> <!-- revised -->
                            <input type="number" step="0.01" name="tarif_normal" id="tarif-normal"
                                class="form-control" placeholder="Contoh: 22400" required> <!-- revised -->
                            <div class="form-text">Isi angka rupiah per ton, tanpa titik/koma.</div> <!-- tambahan -->
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tarif Lembur (Rp/ton)</label> <!-- revised -->
                            <input type="number" step="0.01" name="tarif_lembur" id="tarif-lembur"
                                class="form-control" placeholder="Contoh: 32600" required> <!-- revised -->
                            <div class="form-text">Isi angka rupiah per ton, tanpa titik/koma.</div> <!-- tambahan -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let wilayahModalInstance = null;
    let gudangModalInstance  = null;
    let tarifModalInstance   = null;

    document.addEventListener('DOMContentLoaded', function () {
        wilayahModalInstance = new bootstrap.Modal(document.getElementById('wilayahModal'));
        gudangModalInstance  = new bootstrap.Modal(document.getElementById('gudangModal'));
        tarifModalInstance   = new bootstrap.Modal(document.getElementById('tarifModal'));

        // Reset form saat modal ditutup
        document.getElementById('wilayahModal')
            .addEventListener('hidden.bs.modal', function () {
                document.getElementById('wilayahForm').reset();
                document.getElementById('wilayah-id').value = '';
                document.getElementById('wilayahModalLabel').textContent = 'Tambah Wilayah';
            });

        document.getElementById('gudangModal')
            .addEventListener('hidden.bs.modal', function () {
                document.getElementById('gudangForm').reset();
                document.getElementById('gudang-id').value = '';
                document.getElementById('gudangModalLabel').textContent = 'Tambah Gudang';
            });

        document.getElementById('tarifModal')
            .addEventListener('hidden.bs.modal', function () {
                document.getElementById('tarifForm').reset();
                document.getElementById('tarif-id').value = '';
                document.getElementById('tarifModalLabel').textContent = 'Tambah Tarif Gudang';
            });
    });

    // ==== WILAYAH ====
    function openWilayahModal(mode, id = '', wilayah = '', adminId = '') {
        const idField      = document.getElementById('wilayah-id');
        const namaField    = document.getElementById('wilayah-nama');
        const adminField   = document.getElementById('wilayah-admin');
        const title        = document.getElementById('wilayahModalLabel');

        document.getElementById('wilayahForm').reset();
        idField.value    = '';
        namaField.value  = '';
        adminField.value = '';

        if (mode === 'edit') {
            title.textContent = 'Edit Wilayah';
            idField.value     = id || '';
            namaField.value   = wilayah || '';
            if (adminId) adminField.value = adminId;
        } else {
            title.textContent = 'Tambah Wilayah';
        }

        wilayahModalInstance.show();
    }

    // ==== GUDANG ====
    function openGudangModal(mode, id = '', nama = '', idWilayah = '') {
        const idField    = document.getElementById('gudang-id');
        const namaField  = document.getElementById('gudang-nama');
        const wilField   = document.getElementById('gudang-wilayah');
        const title      = document.getElementById('gudangModalLabel');

        document.getElementById('gudangForm').reset();
        idField.value   = '';
        namaField.value = '';
        wilField.value  = '';

        if (mode === 'edit') {
            title.textContent = 'Edit Gudang';
            idField.value     = id || '';
            namaField.value   = nama || '';
            wilField.value    = idWilayah || '';
        } else {
            title.textContent = 'Tambah Gudang';
        }

        gudangModalInstance.show();
    }

    // ==== TARIF ====
    function openTarifModal(mode, id = '', gudangId = '', jenis = 'BONGKAR', normal = '', lembur = '') {
        const idField     = document.getElementById('tarif-id');
        const gField      = document.getElementById('tarif-gudang');
        const jenisField  = document.getElementById('tarif-jenis');
        const normalField = document.getElementById('tarif-normal');
        const lemburField = document.getElementById('tarif-lembur');
        const title       = document.getElementById('tarifModalLabel');

        document.getElementById('tarifForm').reset();
        idField.value      = '';
        gField.value       = '';
        jenisField.value   = 'BONGKAR';
        normalField.value  = '';
        lemburField.value  = '';

        if (mode === 'edit') {
            title.textContent   = 'Edit Tarif Gudang';
            idField.value       = id || '';
            gField.value        = gudangId || '';
            jenisField.value    = jenis || 'BONGKAR';
            normalField.value   = normal;
            lemburField.value   = lembur;
        } else {
            title.textContent   = 'Tambah Tarif Gudang';
        }

        tarifModalInstance.show();
    }
</script>
