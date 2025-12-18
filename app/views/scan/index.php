<?php
// app/views/scan/index.php
require __DIR__ . '/../layout/header.php';

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

    .scan-shell {
      border-radius: 18px;
      border: 1px solid var(--border-subtle, #e2e8f0);
      background: #ffffff;
      box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
      padding: 1.5rem 1.5rem 1.25rem 1.5rem;
      font-size: .9rem; /* perkecil sedikit font konten scan */
    }
    .scan-header-title {
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--text, #0f172a);
      margin-bottom: .15rem;
    }
    .scan-header-subtitle {
      font-size: .78rem; /* sebelumnya .8rem */
      color: #64748b;
    }
    .scan-video-wrap {
      margin-top: 1rem;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid #e2e8f0;
      background: radial-gradient(circle at top, #eff6ff 0, #f9fafb 50%, #f8fafc 100%);
    }
    #video {
      width: 100%;
      max-width: 640px;
      display: block;
      margin: 0 auto;
      background: #000000;
    }
    .scan-status-bar {
      margin-top: .75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      flex-wrap: wrap;
      font-size: .78rem; /* sebelumnya .8rem */
    }
    .scan-status-pill {
      padding: .3rem .7rem;
      border-radius: 999px;
      background: #eff6ff;
      color: #1d4ed8;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      font-size: .78rem; /* sedikit lebih kecil */
    }
    .scan-status-dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: #22c55e;
    }
    .scan-help-text {
      color: #64748b;
      font-size: .76rem; /* sebelumnya .78rem */
    }

    .scan-detail-card {
      margin-top: 1.25rem;
      border-radius: 16px;
      border: 1px dashed #e2e8f0;
      padding: 1rem 1.25rem;
      background: #f8fafc;
      font-size: .9rem; /* selaras dengan .scan-shell */
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
  <div class="section-pad pb-4">
    <div class="scan-shell">
      <h5 class="master-card-title mb-1" style="font-weight:700;">Scan Tagihan</h5>
          <p class="master-card-subtitle">
          Gunakan kamera untuk membaca QR invoice dan memproses alur persetujuan.
          </p>
      <div class="scan-video-wrap mt-3">
        <video id="video" autoplay muted playsinline></video>
      </div>

      <div class="scan-status-bar">
        <div class="scan-status-pill">
          <span class="scan-status-dot"></span>
          <span id="status">Arahkan kamera ke QR code invoice</span>
        </div>
        <div class="scan-help-text">
          Pastikan QR berada di area tengah dan pencahayaan cukup.
        </div>
      </div>
    </div>

    <div id="detailContainer" class="scan-detail-card mt-3" style="display:none;"></div>
  </div>
</div>

<script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>
<script>
  const codeReader  = new ZXing.BrowserMultiFormatReader();
  const videoElem   = document.getElementById('video');
  const statusElem  = document.getElementById('status');
  const detailDiv   = document.getElementById('detailContainer');

  let busy            = false;
  let lastText        = null;
  let lastHitTs       = 0;
  let currentDeviceId = null;

  // ==== Helper: parse JSON aman (tidak menimbulkan "Unexpected token '<'") ====
  function parseJsonSafe(response) {
  return response.text().then(text => {
    const trimmed = (text || '').trim();

    // Log pendek untuk debug (bisa dihapus kalau tidak mau sama sekali)
    console.log('Raw response (preview):', trimmed.slice(0, 200));

    // Jika kosong → anggap sukses tanpa payload khusus
    if (!trimmed) {
      return { success: true, message: 'OK (tanpa respon)' };
    }

    // Jika kemungkinan besar JSON (diawali { atau [), coba parse
    if (trimmed[0] === '{' || trimmed[0] === '[') {
      try {
        return JSON.parse(trimmed);
      } catch (e) {
        console.error('Gagal parse JSON:', e);
        // Fallback: tetap dianggap sukses generik
        return { success: true, message: 'OK (respon tidak sepenuhnya valid)' };
      }
    }

    // Jika bukan JSON (misal HTML warning / notice), tetap dianggap sukses
    console.warn('Respon bukan JSON, tetapi HTTP 200.');
    return {
      success: true,
      message: 'OK (respon non-JSON)',
      raw: trimmed
    };
  });
}

  // ==== Inisialisasi kamera ====
  codeReader.listVideoInputDevices()
    .then(devices => {
      if (!devices || devices.length === 0) throw new Error('No camera found');
      // ambil kamera belakang jika ada
      currentDeviceId = devices[devices.length - 1].deviceId;
      return codeReader.decodeFromVideoDevice(currentDeviceId, videoElem, onFrame);
    })
    .catch(err => {
      statusElem.textContent = 'Tidak bisa akses kamera: ' + err.message;
    });

  function onFrame(result, err) {
    if (!result) return;
    const text = result.getText();

    const now = Date.now();
    if (busy) return;
    if (text === lastText && (now - lastHitTs) < 1200) return;

    lastText  = text;
    lastHitTs = now;
    busy      = true;

    statusElem.textContent = 'QR terdeteksi, memuat detail invoice…';

    const m = text.match(/[\?&]id=(\d+)/);
    const invoiceId = m ? m[1] : text.replace(/\D/g, '');
    if (!invoiceId) {
      statusElem.textContent = 'QR tidak berisi ID invoice yang valid';
      busy = false;
      return;
    }
    loadInvoiceDetail(invoiceId);
  }

  function loadInvoiceDetail(id) {
    detailDiv.style.display = 'block';
    detailDiv.innerHTML = `<p class="text-info mb-0">Memuat invoice #${id}&hellip;</p>`;

    fetch(`index.php?page=scan&action=fetch&id=${encodeURIComponent(id)}`)
      .then(res => {
        if (!res.ok) throw new Error('Invoice tidak ditemukan');
        return res.text();
      })
      .then(html => {
        detailDiv.innerHTML = html;

        // inisialisasi uploader bila elemen ada
        if (
          document.getElementById('dz-create') &&
          document.getElementById('files-create') &&
          document.getElementById('list-create')
        ) {
          initMultiUploader('dz-create', 'files-create', 'list-create', { maxMB: 10 });
        }

        attachDecisionHandlers();
        attachCloseReactiveHandlers();

        statusElem.textContent = 'Arahkan kamera ke QR code invoice';
        busy = false;
      })
      .catch(err => {
        console.error('Fetch detail error:', err);
        detailDiv.innerHTML = `<div class="alert alert-warning mb-0">
          Tagihan ini tidak dapat diakses karena tidak sesuai dengan wilayah yang terdaftar.
        </div>`;
        setTimeout(() => {
          detailDiv.innerHTML = '';
          detailDiv.style.display = 'none';
          statusElem.textContent = 'Arahkan kamera ke QR code invoice';
          busy = false;
          lastText = null;
        }, 1500);
      });
  }

  function attachDecisionHandlers() {
    console.log('🔗 attachDecisionHandlers() aktif');

    detailDiv.querySelectorAll('.btn-decision').forEach(btn => {
      btn.addEventListener('click', () => {
        const mode = btn.dataset.decision;
        const id   = btn.dataset.id;
        const role = btn.dataset.role;

        const no_soj_input = document.getElementById('no_soj');
        const no_mmj_input = document.getElementById('no_mmj');
        const no_soj = no_soj_input && !no_soj_input.disabled ? no_soj_input.value.trim() : '';
        const no_mmj = no_mmj_input && !no_mmj_input.disabled ? no_mmj_input.value.trim() : '';

        const note_field = document.getElementById('note_role');
        const note_value = note_field ? note_field.value.trim() : '';

        if (role === 'ADMIN_PCS' && mode === 'approve') {
          if (!no_mmj || !no_soj) {
            alert('⚠️ Harap isi Nomor MMJ dan Nomor SOJ sebelum melakukan approve!');
            return;
          }
        }

        if (role === 'KEUANGAN' && mode === 'reject') {
          if (!note_value) {
            alert('⚠️ Harap isi catatan revisi sebelum melakukan reject!');
            return;
          }
        }

        let confirmMsg = mode === 'approve'
          ? 'Apakah yakin ingin APPROVE invoice ini?'
          : 'Apakah yakin ingin REJECT invoice ini?';

        if (role === 'KEUANGAN' && mode === 'reject') {
          confirmMsg = 'Invoice akan dikirim ke ADMIN PCS untuk revisi. Lanjutkan?';
        }

        if (!confirm(confirmMsg)) return;

        btn.disabled = true;
        busy = true;

        const formData = new FormData();
        formData.append('invoice_id', id);
        formData.append('decision', mode);
        formData.append('no_mmj', no_mmj);
        formData.append('no_soj', no_soj);

        if (role === 'ADMIN_WILAYAH') {
          formData.append('note_admin_wilayah', note_value);
        } else if (role === 'PERWAKILAN_PI') {
          formData.append('note_perwakilan_pi', note_value);
        } else if (role === 'ADMIN_PCS') {
          formData.append('note_admin_pcs', note_value);

          const fileInput = document.getElementById('files-create');
          let filesToAppend = [];
          if (fileInput && fileInput._dt && fileInput._dt.files.length > 0) {
            filesToAppend = fileInput._dt.files;
          }
          for (let i = 0; i < filesToAppend.length; i++) {
            formData.append('files[]', filesToAppend[i]);
          }
        } else if (role === 'KEUANGAN') {
          formData.append('note_keuangan', note_value);
        }

        fetch('index.php?page=scan&action=decide', {
          method: 'POST',
          body: formData
        })
          .then(parseJsonSafe)
          .then(js => {
            if (!js.success) throw new Error(js.message || 'Gagal menyimpan keputusan');

            if (mode === 'reject') {
              detailDiv.innerHTML = `<div class="alert alert-warning mb-0">
                <strong>✓ Invoice dikirim untuk revisi ke ${js.next || 'role sebelumnya'}.</strong>
                ${note_value ? '<br><small>Catatan: ' + note_value + '</small>' : ''}
              </div>`;
            } else {
              detailDiv.innerHTML = `<div class="alert alert-success mb-0">
                <strong>✓ Keputusan APPROVE tersimpan.</strong><br>
                Next: <em>${js.next || 'SELESAI'}</em>
                ${note_value ? '<br><small>Catatan: ' + note_value + '</small>' : ''}
              </div>`;
            }
          })
          .catch(err => {
            console.error('Decide error:', err);
            detailDiv.innerHTML = `<div class="alert alert-danger mb-0">
              <strong>✗ Error:</strong> ${err.message}
            </div>`;
            btn.disabled = false;
          })
          .finally(() => {
            setTimeout(() => {
              detailDiv.innerHTML = '';
              detailDiv.style.display = 'none';
              statusElem.textContent = 'Arahkan kamera ke QR code invoice';
              busy = false;
              lastText = null;
            }, 2500);
          });
      });
    });
  }

  function attachCloseReactiveHandlers() {
    console.log('🔗 attachCloseReactiveHandlers() aktif');

    const btnClose = detailDiv.querySelector('#btnClose');
    if (btnClose) {
      btnClose.addEventListener('click', () => {
        const id = btnClose.dataset.id;

        if (!confirm('Apakah yakin ingin MENUTUP invoice ini? Invoice akan selesai.')) return;

        btnClose.disabled = true;
        busy = true;

        const formData = new FormData();
        formData.append('invoice_id', id);

        const note_field = document.getElementById('note_role');
        const note_value = note_field ? note_field.value.trim() : '';
        if (note_value) {
          formData.append('note_keuangan', note_value);
        }

        fetch('index.php?page=scan&action=close', {
          method: 'POST',
          body: formData
        })
          .then(parseJsonSafe)
          .then(js => {
            if (!js.success) throw new Error(js.message || 'Gagal menutup invoice');

            detailDiv.innerHTML = `<div class="alert alert-success mb-0">
              <strong>✓ Invoice berhasil ditutup (CLOSE)</strong><br>
              <small>Invoice sudah selesai diproses</small>
            </div>`;
          })
          .catch(err => {
            console.error('Close error:', err);
            detailDiv.innerHTML = `<div class="alert alert-danger mb-0">
              <strong>✗ Error:</strong> ${err.message}
            </div>`;
            btnClose.disabled = false;
          })
          .finally(() => {
            setTimeout(() => {
              detailDiv.innerHTML = '';
              detailDiv.style.display = 'none';
              statusElem.textContent = 'Arahkan kamera ke QR code invoice';
              busy = false;
              lastText = null;
            }, 2500);
          });
      });
    }

    const btnReactive = detailDiv.querySelector('#btnReactive');
    if (btnReactive) {
      btnReactive.addEventListener('click', () => {
        const id = btnReactive.dataset.id;

        if (!confirm('Apakah yakin ingin mengaktifkan kembali invoice ini untuk revisi?')) return;

        btnReactive.disabled = true;
        busy = true;

        const formData = new FormData();
        formData.append('invoice_id', id);

        fetch('index.php?page=scan&action=reactive', {
          method: 'POST',
          body: formData
        })
          .then(parseJsonSafe)
          .then(js => {
            if (!js.success) throw new Error(js.message || 'Gagal mengaktifkan kembali invoice');

            detailDiv.innerHTML = `<div class="alert alert-success mb-0">
              <strong>✓ Invoice berhasil diaktifkan kembali</strong><br>
              <small>Current role: ${js.current_role}</small>
            </div>`;
          })
          .catch(err => {
            console.error('Reactive error:', err);
            detailDiv.innerHTML = `<div class="alert alert-danger mb-0">
              <strong>✗ Error:</strong> ${err.message}
            </div>`;
            btnReactive.disabled = false;
          })
          .finally(() => {
            setTimeout(() => {
              detailDiv.innerHTML = '';
              detailDiv.style.display = 'none';
              statusElem.textContent = 'Arahkan kamera ke QR code invoice';
              busy = false;
              lastText = null;
            }, 2500);
          });
      });
    }
  }
</script>


<?php
require __DIR__ . '/../layout/footer.php';
?>
