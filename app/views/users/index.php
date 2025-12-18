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

        /* Skala font konten utama sama seperti halaman Master STO */
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

        /* Isi modal sedikit lebih kecil, judul mengikuti default Bootstrap */
        .modal-body,
        .modal-footer {
            font-size: .88rem;
        }
    </style>

    <style>
    /* Skala font di dalam modal user sedikit lebih kecil */
    #userModal .modal-content {
        font-size: 0.9rem;
    }

    #userModal .modal-title {
        font-size: 0.8rem;
        font-weight: 600;
    }

    #userModal .form-label,
    #userModal .form-text {
        font-size: 0.8rem;
    }

    #userModal .form-control,
    #userModal .form-select {
        font-size: 0.8rem;
    }

    #userModal .btn {
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

        <!-- KARTU UTAMA + TOMBOL TAMBAH -->
        <div class="master-card mb-3">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Master User</h5>
                    <p class="master-card-subtitle">
                        Pengelompokan user berdasarkan role, termasuk Admin Gudang, Kepala Gudang, dan role lainnya.
                    </p>
                </div>
                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="openUserModal('tambah')">
                    Tambah User
                </button>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- ROLE: ADMIN GUDANG -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Role: Admin Gudang</h5>
                    <p class="master-card-subtitle">
                        User yang mengelola input STO dan operasional gudang harian.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 sto-table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Gudang</th>
                            <th style="width:160px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($admin_gudangList) && is_array($admin_gudangList)): ?>
                            <?php foreach ($admin_gudangList as $i => $g): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($g['nama']) ?></td>
                                    <td><?= htmlspecialchars($g['username']) ?></td>
                                    <td><?= htmlspecialchars($g['role']) ?></td>
                                    <td><?= htmlspecialchars($g['nama_gudang'] ?? "-") ?></td>
                                    <td class="text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning me-1"
                                            onclick="openEditModal(
                                                <?= (int)$g['id'] ?>,
                                                '<?= htmlspecialchars($g['nama'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['username'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['role'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['id_gudang'] ?? '', ENT_QUOTES) ?>',
                                                ''
                                            )">
                                            Edit
                                        </button>
                                        <a href="index.php?page=users&action=deleteUser&delete=<?= (int)$g['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Hapus user ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data admin gudang.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- ROLE: KEPALA GUDANG -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Role: Kepala Gudang</h5>
                    <p class="master-card-subtitle">
                        Penanggung jawab gudang yang memverifikasi dan mengesahkan laporan.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 sto-table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Gudang</th>
                            <th style="width:160px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kepala_gudangList) && is_array($kepala_gudangList)): ?>
                            <?php foreach ($kepala_gudangList as $i => $g): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($g['nama']) ?></td>
                                    <td><?= htmlspecialchars($g['username']) ?></td>
                                    <td><?= htmlspecialchars($g['role']) ?></td>
                                    <td><?= htmlspecialchars($g['nama_gudang'] ?? "-") ?></td>
                                    <td class="text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning me-1"
                                            onclick="openEditModal(
                                                <?= (int)$g['id'] ?>,
                                                '<?= htmlspecialchars($g['nama'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['username'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['role'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['id_gudang'] ?? '', ENT_QUOTES) ?>',
                                                ''
                                            )">
                                            Edit
                                        </button>
                                        <a href="index.php?page=users&action=deleteUser&delete=<?= (int)$g['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Hapus user ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data kepala gudang.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- ROLE: LAINNYA -->
        <!-- ===================================== -->
        <div class="master-card">
            <div class="master-card-header">
                <div>
                    <h5 class="master-card-title mb-1" style="font-weight:700;">Role: Lainnya</h5>
                    <p class="master-card-subtitle">
                        Termasuk Admin Wilayah, Perwakilan PI, Admin PCS, Keuangan, dan Superadmin.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 sto-table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th style="width:160px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($admin_wilayahList) && is_array($admin_wilayahList)): ?>
                            <?php foreach ($admin_wilayahList as $i => $g): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($g['nama']) ?></td>
                                    <td><?= htmlspecialchars($g['username']) ?></td>
                                    <td><?= htmlspecialchars($g['role']) ?></td>
                                    <td class="text-nowrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning me-1"
                                            onclick="openEditModal(
                                                <?= (int)$g['id'] ?>,
                                                '<?= htmlspecialchars($g['nama'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['username'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($g['role'], ENT_QUOTES) ?>',
                                                '',
                                                '<?= htmlspecialchars($g['id_wilayah_ditangani'] ?? '', ENT_QUOTES) ?>'
                                            )">
                                            Edit
                                        </button>
                                        <a href="index.php?page=users&action=deleteUser&delete=<?= (int)$g['id'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Hapus user ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada data admin wilayah / role lainnya.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /section-pad section-body -->
</div><!-- /dashboard-canvas -->

<!-- ===================================== -->
<!-- MODAL TAMBAH / EDIT USER (DI LUAR CANVAS) -->
<!-- ===================================== -->
<div class="modal fade" id="userModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <form id="userForm" method="POST" action="index.php?page=users&action=tambah_user">
                <input type="hidden" name="id" id="user_id">
                <input type="hidden" name="mode" id="mode" value="tambah">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama"
                                   placeholder="Masukkan Nama" required>
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username"
                                   placeholder="Masukkan Username" required>
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" id="role" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="ADMIN_GUDANG">Admin Gudang</option>
                                <option value="KEPALA_GUDANG">Kepala Gudang</option>
                                <option value="ADMIN_WILAYAH">Admin Wilayah</option>
                                <option value="PERWAKILAN_PI">Perwakilan PI</option>
                                <option value="ADMIN_PCS">Admin PCS</option>
                                <option value="KEUANGAN">Keuangan</option>
                                <option value="SUPERADMIN">Superadmin</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="id_gudang" class="form-label">Gudang</label>
                            <select name="id_gudang" id="id_gudang" class="form-select">
                                <option value="">-- Pilih Gudang --</option>
                                <?php foreach ($allGudangList as $g): ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= htmlspecialchars($g['nama_gudang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="id_wilayah" class="form-label">Wilayah (khusus Admin Wilayah)</label>
                            <select class="form-select" name="id_wilayah[]" id="id_wilayah" multiple>
                                <?php foreach ($wilayahList as $w): ?>
                                    <option value="<?= $w['id'] ?>">
                                        <?= htmlspecialchars($w['wilayah']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password"
                                   placeholder="Masukkan Password">
                            <div class="form-text">
                                Kosongkan jika tidak ingin mengubah password (saat edit).
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" name="tambah_user">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Referensi global modal
    let userModalInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        const userModalEl   = document.getElementById('userModal');
        const roleSelect    = document.getElementById('role');
        const gudangField   = document.getElementById('id_gudang').closest('.col-md-6');
        const wilayahField  = document.getElementById('id_wilayah').closest('.col-md-6');
        const gudangSelect  = document.getElementById('id_gudang');
        const wilayahSelect = document.getElementById('id_wilayah');

        userModalInstance = new bootstrap.Modal(userModalEl);

        // Fungsi helper untuk atur visibilitas field berdasarkan role
        function applyRoleVisibility(roleVal) {
            gudangField.style.display  = 'none';
            wilayahField.style.display = 'none';
            gudangSelect.required      = false;
            wilayahSelect.required     = false;

            if (roleVal === 'ADMIN_GUDANG' || roleVal === 'KEPALA_GUDANG') {
                gudangField.style.display = 'block';
                gudangSelect.required     = true;
            } else if (roleVal === 'ADMIN_WILAYAH') {
                wilayahField.style.display = 'block';
                wilayahSelect.required     = true;
            }
        }

        // Inisialisasi awal
        gudangField.style.display  = 'none';
        wilayahField.style.display = 'none';

        roleSelect.addEventListener('change', function () {
            applyRoleVisibility(this.value);
        });

        // Reset form setiap modal ditutup
        userModalEl.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('userForm');
            form.reset();

            document.getElementById('userModalLabel').textContent = 'Tambah User Baru';
            document.getElementById('user_id').value = '';
            document.getElementById('mode').value    = 'tambah';

            for (let opt of wilayahSelect.options) {
                opt.selected = false;
            }

            applyRoleVisibility('');
        });

        // Simpan fungsi di global scope
        window._applyRoleVisibility = applyRoleVisibility;
    });

    // Buka modal mode tambah
    function openUserModal(mode) {
        const form          = document.getElementById('userForm');
        const label         = document.getElementById('userModalLabel');
        const modeInput     = document.getElementById('mode');
        const userIdInput   = document.getElementById('user_id');
        const namaInput     = document.getElementById('nama');
        const userInput     = document.getElementById('username');
        const roleSelect    = document.getElementById('role');
        const gudangSelect  = document.getElementById('id_gudang');
        const wilayahSelect = document.getElementById('id_wilayah');
        const passwordInput = document.getElementById('password');

        form.reset();
        for (let opt of wilayahSelect.options) {
            opt.selected = false;
        }

        userIdInput.value   = '';
        passwordInput.value = '';

        if (mode === 'tambah') {
            label.textContent = 'Tambah User Baru';
            modeInput.value   = 'tambah';
            roleSelect.value  = '';
            gudangSelect.value = '';
            window._applyRoleVisibility('');
        }

        if (userModalInstance) {
            userModalInstance.show();
        } else {
            const el = document.getElementById('userModal');
            userModalInstance = new bootstrap.Modal(el);
            userModalInstance.show();
        }
    }

    // Buka modal mode edit dari tombol di tabel
    function openEditModal(id, nama, username, role, idGudang = '', idWilayahList = '') {
        const form          = document.getElementById('userForm');
        const label         = document.getElementById('userModalLabel');
        const modeInput     = document.getElementById('mode');
        const userIdInput   = document.getElementById('user_id');
        const namaInput     = document.getElementById('nama');
        const userInput     = document.getElementById('username');
        const roleSelect    = document.getElementById('role');
        const gudangSelect  = document.getElementById('id_gudang');
        const wilayahSelect = document.getElementById('id_wilayah');
        const passwordInput = document.getElementById('password');

        form.reset();
        for (let opt of wilayahSelect.options) {
            opt.selected = false;
        }

        label.textContent   = 'Edit User';
        modeInput.value     = 'edit';
        userIdInput.value   = id;
        namaInput.value     = nama;
        userInput.value     = username;
        roleSelect.value    = role;
        passwordInput.value = '';

        // Atur field berdasarkan role
        if (typeof window._applyRoleVisibility === 'function') {
            window._applyRoleVisibility(role);
        }

        // Isi gudang jika ada
        if (idGudang) {
            gudangSelect.value = idGudang;
        } else {
            gudangSelect.value = '';
        }

        // Isi wilayah jika role ADMIN_WILAYAH
        if (role === 'ADMIN_WILAYAH' && idWilayahList) {
            const ids = idWilayahList.split(',');
            ids.forEach(function (idw) {
                const opt = wilayahSelect.querySelector('option[value="' + idw.trim() + '"]');
                if (opt) opt.selected = true;
            });
        }

        if (userModalInstance) {
            userModalInstance.show();
        } else {
            const el = document.getElementById('userModal');
            userModalInstance = new bootstrap.Modal(el);
            userModalInstance.show();
        }
    }
</script>
