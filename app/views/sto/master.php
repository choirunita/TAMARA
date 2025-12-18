<?php
// variabel yang dipakai di view ini (datang dari controller):
// $gudangs, $stoList, $filesBySto, $filesBaseUrl, $nama_gudang

// Safe default untuk menghindari undefined/null
$nama_gudang_safe  = isset($nama_gudang) && $nama_gudang !== null ? $nama_gudang : '-';
$filesBaseUrl_safe = isset($filesBaseUrl) && $filesBaseUrl !== null ? $filesBaseUrl : '';
$role_session      = $_SESSION['role'] ?? '';
?>

<div class="dashboard-canvas">
  <div class="dashboard-grid" aria-hidden="true"></div>

  <!-- ================= TOP BAR ================= -->
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

  <!-- ================= KONTEN UTAMA ================= -->
  <div class="section-pad">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h5 class="master-card-title mb-1" style="font-weight:700;">Master STO</h5>
        <p class="master-card-subtitle">
          Manage dan monitoring Sales Transport Order.
        </p>
      </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['warning'])): ?>
      <div class="alert alert-warning"><?= $_SESSION['warning']; unset($_SESSION['warning']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- ================= 1) Form Registrasi ================= -->
    <form id="stoForm" class="row g-3 mb-5" method="POST" enctype="multipart/form-data">
      <div class="col-md-4">
        <label class="form-label">Nomor STO</label>
        <input type="text" name="nomor_sto" class="form-control" placeholder="Masukkan Nomor STO" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Nama Gudang</label>
        <input
          type="text"
          name="nama_gudang"
          id="edit-nama-gudang"
          class="form-control"
          placeholder="Nama Gudang"
          value="<?= htmlspecialchars($nama_gudang_safe, ENT_QUOTES, 'UTF-8') ?>"
          readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Jenis Kegiatan</label>
        <select name="jenis_transaksi" class="form-control" required>
          <option value="">-- Pilih --</option>
          <option value="BONGKAR">BONGKAR</option>
          <option value="MUAT">MUAT</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Tonase Normal (Ton)</label>
        <input type="number" step="0.01" name="tonase_normal" id="tonase_normal" class="form-control" value="0">
      </div>
      <div class="col-md-4">
        <label class="form-label">Tonase Lembur (Ton)</label>
        <input type="number" step="0.01" name="tonase_lembur" id="tonase_lembur" class="form-control" value="0">
      </div>
      <div class="col-md-4">
        <label class="form-label">Total Tonase</label>
        <input type="text" id="total_tonase" class="form-control" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Transportir</label>
        <input type="text" name="transportir" class="form-control" placeholder="Nama Transportir" required>
      </div>
      <div class="col-md-8">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
      </div>

      <!-- =========== Upload Multi File (baru) =========== -->
      <div class="col-12">
        <label class="form-label">Lampiran (boleh banyak)</label>

        <div id="dz-create" class="uploader p-4 text-center mb-2">
          <div class="cloud mb-2">☁️⬆️</div>
          <div class="cta">Click To Upload</div>
          <small class="text-muted d-block mt-1">
            atau drag & drop file ke sini • Maks 10MB/file • pdf, jpg, png, xls, xlsx
          </small>
        </div>

        <input id="files-create" type="file" name="files[]" class="d-none" multiple
               accept=".pdf,.png,.jpg,.jpeg,.xls,.xlsx">

        <ul id="list-create" class="file-list"></ul>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-success">Input STO</button>
      </div>
    </form>

    <!-- ================= 2) Header + SEARCH GLOBAL ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
      <div>
        <h5 class="master-card-title mb-1" style="font-weight:700;">Daftar STO</h5>
        <p class="master-card-subtitle">
          Ketik di kolom pencarian untuk mencari berdasarkan nomor, gudang, transaksi, dan kolom lain.
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
        <input type="search" id="globalSearch" class="form-control border-start-0"
               placeholder="Cari STO...">
      </div>
    </div>

        <!-- ================= 3) Tabel Master ================= -->
<div class="table-responsive">
  <table id="sto-table" class="table table-bordered align-middle table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Nomor STO</th>
        <th>Tanggal Terbit</th>
        <th>Gudang</th>
        <th>Transaksi</th>
        <th>Transportir</th>
        <th>Opsi</th>
        <th>Status Pemakaian</th>
        <th>Verifikasi Kepala Gudang</th>
      </tr>
    </thead>
    <tbody>
<?php if (empty($stoList)): ?>
  <tr>
    <td colspan="9" class="text-center text-muted">Belum ada STO</td>
  </tr>
<?php else: ?>
  <?php foreach ($stoList as $i => $s): ?>
    <?php
      $fcount   = isset($filesBySto[$s['id']]) ? count($filesBySto[$s['id']]) : 0;
      $role     = $_SESSION['role'] ?? null;
      $status   = $s['status']  ?? '';       // NOT_USED / USED
      $pilihan  = $s['pilihan'] ?? '';       // BELUM_DIPILIH / DIPILIH
      $locked   = ($status === 'USED' && $pilihan === 'DIPILIH'); // sudah dipakai & terverifikasi

      // Hapus hanya boleh jika tidak locked
      $bolehHapus = false;
      if (!$locked) {
        if ($role === 'ADMIN_GUDANG' && $pilihan !== 'DIPILIH') {
          $bolehHapus = true;
        }
        if ($role === 'KEPALA_GUDANG') {
          $bolehHapus = true;
        }
        if ($role === 'SUPERADMIN') {
          $bolehHapus = true;
        }
      }
    ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= htmlspecialchars($s['nomor_sto'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($s['tanggal_terbit'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($s['nama_gudang'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($s['jenis_transaksi'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($s['transportir'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

      <!-- Opsi: jika locked → hanya Detail -->
      <td class="text-nowrap">
        <button
          data-id="<?= $s['id'] ?>"
          class="btn btn-sm btn-primary btn-detail">
          Detail
        </button>

        <?php if (!$locked): ?>
          <button
            data-id="<?= $s['id'] ?>"
            class="btn btn-sm btn-warning btn-edit ms-1">
            Edit
          </button>

          <?php if ($bolehHapus): ?>
            <a
              href="?page=master_sto&delete=<?= $s['id'] ?>"
              class="btn btn-sm btn-danger ms-1"
              onclick="return confirm('Hapus STO ini beserta lampiran?')">
              Hapus
            </a>
          <?php endif; ?>
        <?php endif; ?>
      </td>

      <!-- Status Pemakaian -->
      <td>
        <?php if ($status === 'USED'): ?>
          <span class="btn btn-sm btn-outline-danger">
            Sudah Dipakai
          </span>
        <?php else: ?>
          <span class="btn btn-sm btn-outline-success">
            Belum Dipakai
          </span>
        <?php endif; ?>
      </td>

      <!-- Verifikasi Kepala Gudang -->
      <td>
        <?php if ($pilihan === 'DIPILIH'): ?>
          <?php if (!$locked && ($role === 'KEPALA_GUDANG' || $role === 'SUPERADMIN')): ?>
            <!-- Masih boleh di-toggle jika belum dipakai -->
            <button
              class="btn btn-sm btn-success toggle-pilih"
              data-id="<?= $s['id'] ?>"
              data-next="BELUM_DIPILIH">
              Terverifikasi
            </button>
          <?php else: ?>
            <!-- Sudah terpakai atau bukan kewenangan → badge saja -->
            <span class="btn btn-sm btn-success">
              Terverifikasi
            </span>
          <?php endif; ?>
        <?php else: ?>
          <?php if (!$locked && ($role === 'KEPALA_GUDANG' || $role === 'SUPERADMIN')): ?>
            <button
              class="btn btn-sm btn-warning toggle-pilih"
              data-id="<?= $s['id'] ?>"
              data-next="DIPILIH">
              Belum Diverifikasi
            </button>
          <?php else: ?>
            <span class="btn btn-sm btn-warning">
              Belum Diverifikasi
            </span>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>
  </table>

  <!-- ================= Kontrol Pagination ================= -->
  <div class="d-flex justify-content-between align-items-center mb-2">
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

    <!-- ================= Script ================= -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const n = document.getElementById('tonase_normal'),
              l = document.getElementById('tonase_lembur'),
              t = document.getElementById('total_tonase');

        function updTotal() {
          t.value = ((parseFloat(n.value) || 0) + (parseFloat(l.value) || 0)).toFixed(2);
        }
        n.addEventListener('input', updTotal);
        l.addEventListener('input', updTotal);
        updTotal();

        // ========= Helper parse JSON toleran (ada warning PHP dsb.) =========
        function parseJsonWithTolerance(response) {
          return response.text().then(txt => {
            try {
              const start = txt.indexOf('{');
              const end   = txt.lastIndexOf('}');
              if (start !== -1 && end !== -1 && end > start) {
                const jsonStr = txt.slice(start, end + 1);
                return JSON.parse(jsonStr);
              }
              console.error('Respon bukan JSON:', txt);
              throw new Error('Respon bukan JSON valid');
            } catch (e) {
              console.error('Gagal parse JSON:', e);
              console.error('Payload:', txt);
              throw e;
            }
          });
        }

        // ========= Widget multi uploader =========
        function initMultiUploader(zoneId, inputId, listId, options = {}) {
          const zone = document.getElementById(zoneId);
          const input = document.getElementById(inputId);
          const list  = document.getElementById(listId);
          if (!zone || !input || !list) return;

          const MAX_BYTES = (options.maxMB || 10) * 1024 * 1024;
          const ALLOWED = (options.allowed || ['pdf', 'png', 'jpg', 'jpeg', 'xls', 'xlsx']).map(x => x.toLowerCase());

          const dt = new DataTransfer();

          const extOf = (name) => (name.split('.').pop() || '').toLowerCase();
          const labelOf = (name) => {
            const ext = extOf(name);
            if (ext === 'pdf') return 'Pdf';
            if (ext === 'doc' || ext === 'docx') return 'Docx';
            if (ext === 'xls' || ext === 'xlsx') return 'Xls';
            if (ext === 'jpg' || ext === 'jpeg') return 'Jpg';
            if (ext === 'png') return 'Png';
            return ext || 'File';
          };

          function renderList() {
            list.innerHTML = '';
            Array.from(dt.files).forEach((f, idx) => {
              const li = document.createElement('li');
              li.className = 'file-pill';
              li.innerHTML = `
                <span class="file-badge">${labelOf(f.name)}</span>
                <span class="flex-grow-1 text-truncate">${f.name}</span>
                <button type="button" class="file-remove" title="Hapus">&times;</button>
              `;
              li.querySelector('.file-remove').addEventListener('click', () => {
                const newDt = new DataTransfer();
                Array.from(dt.files).forEach((ff, i) => {
                  if (i !== idx) newDt.items.add(ff);
                });
                input.files = newDt.files;
                dt.items.clear();
                Array.from(newDt.files).forEach(ff => dt.items.add(ff));
                renderList();
              });
              list.appendChild(li);
            });
          }

          function acceptFiles(files) {
            Array.from(files).forEach(f => {
              const ext = extOf(f.name);
              if (!ALLOWED.includes(ext)) {
                console.warn('Tipe tidak diizinkan:', f.name);
                return;
              }
              if (f.size > MAX_BYTES) {
                console.warn('Kebesaran:', f.name);
                return;
              }
              dt.items.add(f);
            });
            input.files = dt.files;
            renderList();
          }

          zone.addEventListener('click', () => input.click());
          input.addEventListener('change', (e) => acceptFiles(e.target.files));

          ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev =>
            zone.addEventListener(ev, e => {
              e.preventDefault();
              e.stopPropagation();
            })
          );
          zone.addEventListener('drop', e => acceptFiles(e.dataTransfer.files));
        }

        initMultiUploader('dz-create', 'files-create', 'list-create', { maxMB: 10 });
        initMultiUploader('dz-edit', 'files-edit', 'list-edit', { maxMB: 10 });

        // ========= Modal Detail & Edit =========
        const editModal   = new bootstrap.Modal(document.getElementById('editModal'));
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        const stoTable    = document.getElementById('sto-table');

        stoTable.addEventListener('click', e => {
          const btnDetail = e.target.closest('.btn-detail');
          const btnEdit   = e.target.closest('.btn-edit');

          if (!btnDetail && !btnEdit) return;

          const id = (btnDetail || btnEdit).dataset.id;

          fetch(`index.php?page=master_sto&action=get&id=${id}`)
            .then(r => parseJsonWithTolerance(r))
            .then(s => {
              if (btnDetail) {
                document.getElementById('d-nomor').textContent       = s.nomor_sto || '';
                document.getElementById('d-tanggal').textContent     = s.tanggal_terbit || '';
                document.getElementById('d-gudang').textContent      = s.nama_gudang || '';
                document.getElementById('d-jenis').textContent       = s.jenis_transaksi || '';
                document.getElementById('d-transportir').textContent = s.transportir || '';
                document.getElementById('d-normal').textContent      = s.tonase_normal || '0';
                document.getElementById('d-lembur').textContent      = s.tonase_lembur || '0';
                document.getElementById('d-jumlah').textContent      = s.jumlah || '0';
                document.getElementById('d-keterangan').textContent  = s.keterangan || '-';
                document.getElementById('d-created').textContent = s.created_at || '';

                const statusPemakaian = (s.status === 'USED')
                  ? 'Sudah Dipakai'
                  : 'Belum Dipakai';

                const statusVerifikasi = (s.pilihan === 'DIPILIH')
                  ? 'Terverifikasi'
                  : 'Belum Diverifikasi';

                document.getElementById('d-status').textContent  = statusPemakaian;
                document.getElementById('d-pilihan').textContent = statusVerifikasi;

                const ulDetail = document.getElementById('detail-files');
                ulDetail.innerHTML = '';
                (s.files || []).forEach(f => {
                  const li = document.createElement('li');
                  li.className = 'list-group-item d-flex justify-content-between align-items-center';
                  li.innerHTML = `
                    <span>${f.filename} <small class="text-muted">(${(f.size_bytes/1024).toFixed(1)} KB)</small></span>
                    <a class="btn btn-sm btn-outline-primary" target="_blank"
                       href="<?= htmlspecialchars($filesBaseUrl_safe, ENT_QUOTES, 'UTF-8') ?>${f.stored_name}">Lihat</a>
                  `;
                  ulDetail.appendChild(li);
                });

                detailModal.show();
              }

              if (btnEdit) {
                document.getElementById('edit-id').value          = s.id;
                document.getElementById('edit-nomor').value       = s.nomor_sto || '';
                document.getElementById('edit-tanggal').value     = s.tanggal_terbit || '';
                document.getElementById('edit-gudang').value      = s.nama_gudang || '';
                document.getElementById('edit-jenis').value       = s.jenis_transaksi || '';
                document.getElementById('edit-normal').value      = s.tonase_normal || 0;
                document.getElementById('edit-lembur').value      = s.tonase_lembur || 0;
                document.getElementById('edit-transportir').value = s.transportir || '';
                document.getElementById('edit-keterangan').value  = s.keterangan || '';
                document.getElementById('edit-pilihan').value     = s.pilihan || '';

                const ul = document.getElementById('current-files');
                ul.innerHTML = '';

                (s.files || []).forEach(f => {
                  const li = document.createElement('li');
                  li.className = 'list-group-item d-flex justify-content-between align-items-center';

                  li.innerHTML = `
                    <span>${f.filename} <small class="text-muted">(${(f.size_bytes/1024).toFixed(1)} KB)</small></span>
                    <span>
                      <a class="btn btn-sm btn-outline-primary me-2" target="_blank"
                         href="<?= htmlspecialchars($filesBaseUrl_safe, ENT_QUOTES, 'UTF-8') ?>${f.stored_name}">Lihat</a>
                      <a class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('Hapus lampiran ini?')"
                         href="index.php?page=master_sto&action=del_file&file_id=${f.id}">Hapus</a>
                    </span>`;
                  ul.appendChild(li);
                });

                editModal.show();
              }
            })
            .catch(err => {
              console.error('Gagal memuat data STO:', err);
              alert('Gagal memuat data STO. Silakan cek console (F12) untuk detail.');
            });
        });

        // submit edit (AJAX)
        document.getElementById('editForm').addEventListener('submit', e => {
          e.preventDefault();

          const id   = document.getElementById('edit-id').value;
          const data = new FormData(e.target);

          fetch(`index.php?page=master_sto&id=${id}`, {
            method: 'POST',
            body: data
          })
          .then(r => parseJsonWithTolerance(r))
          .then(res => {
            if (res.success) {
              editModal.hide();
              alert('STO berhasil diperbarui.');
              window.location.reload();
            } else {
              alert('Gagal update: ' + (res.message || 'Unknown error'));
            }
          })
          .catch(err => {
            console.error('Terjadi error saat update STO:', err);
            alert('Terjadi error saat menyimpan perubahan. Silakan cek console (F12) untuk detail.');
          });
        });

        // ========= Tombol Pilih / Belum Dipilih =========
        document.querySelectorAll('.toggle-pilih').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id   = btn.dataset.id;
            const next = btn.dataset.next;

            try {
              const res = await fetch('index.php?page=master_sto&action=toggle_pilih', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `sto_id=${encodeURIComponent(id)}&pilihan=${encodeURIComponent(next)}`
              });

              const data = await parseJsonWithTolerance(res);
              if (data.success) {
                location.reload();
              } else {
                alert('Gagal memperbarui status pilihan!');
              }
            } catch (e) {
              console.error('Error toggle pilihan:', e);
              alert('Terjadi error saat memperbarui status pilihan.');
            }
          });
        });

        // ========= SEARCH GLOBAL + PAGINATION CLIENT-SIDE =========
        const table            = document.querySelector("#sto-table");
        const tbody            = table.querySelector("tbody");
        const allRowsRaw       = Array.from(tbody.querySelectorAll("tr"));
        const emptyRow         = allRowsRaw.find(row => row.textContent.includes("Belum ada STO")) || null;
        const allRows          = emptyRow
                                  ? allRowsRaw.filter(r => r !== emptyRow)
                                  : allRowsRaw;

        const countShowing     = document.getElementById("count-showing");
        const countTotal       = document.getElementById("count-total");
        const rowsPerPageSelect= document.getElementById("rowsPerPage");
        const globalSearch     = document.getElementById("globalSearch");

        let filteredRows = [...allRows];
        let totalRows    = filteredRows.length;
        let rowsPerPage  = parseInt(rowsPerPageSelect.value);
        let totalPages   = Math.ceil(totalRows / rowsPerPage);
        let currentPage  = 1;
        let pagination   = null;
        let debounceId   = null;

        function clearAllRowDisplay() {
          allRows.forEach(r => r.style.display = "none");
          if (emptyRow) emptyRow.style.display = "";
        }

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
            filteredRows.forEach((row, index) => {
              row.style.display = (index >= start && index < end) ? "" : "none";
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

          totalRows  = filteredRows.length;
          rowsPerPage= parseInt(rowsPerPageSelect.value);
          totalPages = totalRows === 0 ? 0 : Math.ceil(totalRows / rowsPerPage);

          if (totalRows === 0) {
            countTotal.textContent   = 0;
            countShowing.textContent = 0;
            clearAllRowDisplay();
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
            li.addEventListener("click", (e) => {
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
          const q = globalSearch.value.toLowerCase().trim();

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

        rowsPerPageSelect.addEventListener("change", () => {
          buildPagination();
        });

        globalSearch.addEventListener("input", () => {
          if (debounceId) clearTimeout(debounceId);
          debounceId = setTimeout(applySearch, 180);
        });
      });
    </script>

  </div> <!-- /section-pad -->
</div> <!-- /dashboard-canvas -->

<!-- =========================================================
     MODAL DETAIL & EDIT — di luar .dashboard-canvas
     ========================================================= -->

<!-- ============ MODAL DETAIL (READ ONLY) ============ -->
<div class="modal fade sto-modal" id="detailModal" tabindex="-1" aria-hidden="true" data-bs-scroll="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail STO</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Nomor STO</dt>
          <dd class="col-sm-8" id="d-nomor"></dd>

          <dt class="col-sm-4">Tanggal Terbit</dt>
          <dd class="col-sm-8" id="d-tanggal"></dd>

          <dt class="col-sm-4">Gudang</dt>
          <dd class="col-sm-8" id="d-gudang"></dd>

          <dt class="col-sm-4">Jenis Transaksi</dt>
          <dd class="col-sm-8" id="d-jenis"></dd>

          <dt class="col-sm-4">Transportir</dt>
          <dd class="col-sm-8" id="d-transportir"></dd>

          <dt class="col-sm-4">Tonase Normal</dt>
          <dd class="col-sm-8" id="d-normal"></dd>

          <dt class="col-sm-4">Tonase Lembur</dt>
          <dd class="col-sm-8" id="d-lembur"></dd>

          <dt class="col-sm-4">Jumlah Tonase</dt>
          <dd class="col-sm-8" id="d-jumlah"></dd>

          <dt class="col-sm-4">Keterangan</dt>
          <dd class="col-sm-8" id="d-keterangan"></dd>

          <dt class="col-sm-4">Dibuat Pada</dt>
          <dd class="col-sm-8" id="d-created"></dd>

          <dt class="col-sm-4">Status Pemakaian</dt>
          <dd class="col-sm-8" id="d-status"></dd>

          <dt class="col-sm-4">Verifikasi Kepala Gudang</dt>
          <dd class="col-sm-8" id="d-pilihan"></dd>
        </dl>

        <hr>
        <h6 class="mb-2">Lampiran</h6>
        <ul id="detail-files" class="list-group small"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade sto-modal" id="editModal" tabindex="-1" aria-hidden="true" data-bs-scroll="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form id="editForm" class="modal-content" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="pilihan" id="edit-pilihan">

      <div class="modal-header">
        <h5 class="modal-title">Edit STO</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nomor STO</label>
            <input type="text" name="nomor_sto" id="edit-nomor" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tanggal Terbit</label>
            <input type="date" name="tanggal_terbit" id="edit-tanggal" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Nama Gudang</label>
            <input
              type="text"
              name="nama_gudang"
              id="edit-gudang"
              class="form-control"
              placeholder="Nama Gudang"
              value="<?= htmlspecialchars($nama_gudang_safe, ENT_QUOTES, 'UTF-8') ?>"
              readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label">Jenis Transaksi</label>
            <select name="jenis_transaksi" id="edit-jenis" class="form-select" required>
              <option value="BONGKAR">BONGKAR</option>
              <option value="MUAT">MUAT</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Tonase Normal</label>
            <input type="number" step="0.01" name="tonase_normal" id="edit-normal" class="form-control">
          </div>

          <div class="col-md-4">
            <label class="form-label">Tonase Lembur</label>
            <input type="number" step="0.01" name="tonase_lembur" id="edit-lembur" class="form-control">
          </div>

          <div class="col-md-4">
            <label class="form-label">Transportir</label>
            <input type="text" name="transportir" id="edit-transportir" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="edit-keterangan" class="form-control">
          </div>

          <!-- Upload tambahan di EDIT -->
          <div class="col-12">
            <label class="form-label">Tambah Lampiran</label>

            <div id="dz-edit" class="uploader p-4 text-center mb-2">
              <div class="cloud mb-2">☁️⬆️</div>
              <div class="cta">Click To Upload</div>
              <small class="text-muted d-block mt-1">
                atau drag &amp; drop file ke sini • Maks 10MB/file • pdf, jpg, png, xls, xlsx
              </small>
            </div>

            <input
              id="files-edit"
              type="file"
              name="edit_files[]"
              class="d-none"
              multiple
              accept=".pdf,.png,.jpg,.jpeg,.xls,.xlsx">

            <ul id="list-edit" class="file-list"></ul>
            <div class="form-text">Lampiran baru akan ditambahkan. Lampiran lama ada di bawah.</div>
          </div>

          <!-- Daftar lampiran yang sudah ada -->
          <div class="col-12">
            <label class="form-label d-block">Lampiran Saat Ini</label>
            <ul id="current-files" class="list-group small"></ul>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
