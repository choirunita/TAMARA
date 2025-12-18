<?php
// app/views/scan/invoice_detail.php
?>
<style>
    .invoice-detail-card {
        font-size: .9rem;
        border-radius: 16px;
    }

    .invoice-detail-card h5 {
        font-size: 1rem;
        margin-bottom: .4rem;
    }

    .detail-header-flow {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-bottom: .75rem;
    }

    .invoice-meta {
        background: #f8fafc;
        border-radius: 12px;
        padding: .65rem .75rem;
        margin-bottom: .9rem;
    }

    .invoice-meta-row {
        display: flex;
        gap: .35rem;
        font-size: .86rem;
        line-height: 1.35;
    }

    .invoice-meta-row + .invoice-meta-row {
        margin-top: .15rem;
    }

    .invoice-meta-label {
        min-width: 80px;
        font-weight: 600;
    }

    .detail-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .detail-table {
        min-width: 540px;
        font-size: .85rem;
        margin-bottom: 0;
    }

    .detail-table thead th {
        background: #f1f5f9;
        border-bottom-width: 1px;
    }

    .section-title {
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: .6rem;
    }

    .section-block {
        padding-top: .6rem;
        margin-top: .4rem;
        border-top: 1px solid #edf2f7;
    }

    .role-notes-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
    }

    .role-note-card {
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .role-note-card .card-body {
        padding: .55rem .75rem .65rem;
    }

    .role-note-card h6 {
        font-size: .86rem;
        margin-bottom: .25rem;
    }

    .uploader {
        border: 2px dashed #cfe3ff;
        border-radius: 12px;
        background: #f7fbff;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        font-size: .88rem;
    }

    .uploader:hover {
        background: #f1f8ff;
        border-color: #4299e1;
    }

    .uploader .cloud {
        font-size: 36px;
        line-height: 1;
        color: #a0aec0;
    }

    .uploader .cta {
        color: #1976d2;
        font-weight: 600;
        font-size: .9rem;
    }

    .upload-label-overlay {
        position: absolute;
        inset: 0;
        cursor: pointer;
    }

    .file-pill {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .45rem .6rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #edf2f7;
        font-size: .82rem;
    }

    .file-badge {
        font-weight: 700;
        font-size: .72rem;
        padding: .15rem .45rem;
        border-radius: 6px;
        background: #e8f1ff;
        color: #0b5ed7;
        text-transform: uppercase;
    }

    .file-remove {
        border: none;
        background: #f8d7da;
        color: #a61b2b;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        font-weight: 700;
        line-height: 1;
        margin-left: auto;
        cursor: pointer;
        transition: background 0.2s;
        font-size: .78rem;
    }

    .file-remove:hover {
        filter: brightness(.95);
        background: #fc8181;
    }

    .file-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        font-size: .82rem;
    }

    @media (max-width: 576px) {
        .invoice-detail-card {
            padding: 0.9rem !important;
            font-size: .85rem;
        }

        .invoice-detail-card h5 {
            font-size: .95rem;
        }

        .detail-table {
            font-size: .8rem;
        }

        .role-notes-grid {
            gap: .65rem;
        }

        .list-group-item.flex-sm-row {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .list-group-item .btn {
            margin-top: .35rem;
        }
    }

    @media (min-width: 768px) {
        .role-notes-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
<?php
// tersedia dari controller: $inv, $lines, $logs, $flow, $role, $userIdView, $invoiceFiles, $uploadUrl
$approved = array_column(array_filter($logs, fn($l) => $l['decision'] === 'APPROVED'), 'role');
$rejected = array_column(array_filter($logs, fn($l) => $l['decision'] === 'REJECTED'), 'role');
$lastLog = $logs ? end($logs) : null;
$lastDecision = $lastLog['decision'] ?? null;
$lastRole     = $lastLog['role'] ?? null;
$current = $inv['current_role'];

// ✅ Cek apakah Admin PCS sudah approve
$adminPcsApproved = in_array('ADMIN_PCS', $approved, true);

// ✅ Cek apakah sedang dalam status REACTIVE atau CLOSE
$isReactive = ($lastDecision === 'REACTIVE' && $current === 'KEUANGAN');
$isClosed = ($lastDecision === 'CLOSE' && $current === 'KEUANGAN');

$isRevision = !empty($inv['is_revised']) || ($lastDecision === 'REJECTED');
$revisedBy = $inv['revised_by'] ?? $lastRole ?? null;
$canEdit = $isRevision && ($current === $role);

// Reset status hasDecided jika ada revisi atau reactive
$hasDecided = false;
if ($logs && !$isReactive) {
    $lastCycleStartIndex = count($logs) - 1;
    if ($isRevision) {
        for ($i = count($logs) - 1; $i >= 0; $i--) {
            if ($logs[$i]['decision'] === 'REJECTED') {
                $lastCycleStartIndex = $i;
                break;
            }
        }
    }
    for ($i = $lastCycleStartIndex; $i < count($logs); $i++) {
        if (
            (int)$logs[$i]['created_by'] === (int)($userIdView ?? 0) &&
            $logs[$i]['role'] === $role &&
            !in_array($logs[$i]['decision'], ['REACTIVE', 'CLOSE'])
        ) {
            $hasDecided = true;
            break;
        }
    }
}

// Role bisa decide jika:
// 1. Ini giliran mereka (current === role) dan belum decide di siklus ini
// 2. Atau jika sedang dalam status REACTIVE (KEUANGAN bisa decide lagi)
$canDecide = ($current === $role && !$hasDecided) || $isReactive;

// ✅ Status pesan untuk user
if ($isClosed && !$isReactive && $role === 'KEUANGAN') {
    echo '<div class="alert alert-success mb-3">
        <i class="bi bi-check-circle"></i> <strong>Invoice sudah ditutup (CLOSE).</strong> 
        Anda dapat mengaktifkan kembali jika perlu revisi.
    </div>';
} elseif ($isReactive && $role === 'KEUANGAN') {
    echo '<div class="alert alert-warning mb-3">
        <i class="bi bi-arrow-clockwise"></i> <strong>Invoice dalam status REACTIVE.</strong> 
        Anda dapat melakukan revisi (reject) atau menutup (close) invoice ini.
    </div>';
} elseif ($hasDecided && !$isReactive) {
    echo '<p class="text-warning mb-2">Anda sudah memberikan keputusan untuk siklus ini.</p>';
} elseif (!empty($current)) {
    if ($current === $role) {
        echo '<p class="text-primary mb-2"><strong>Giliran Anda untuk memberikan keputusan.</strong></p>';
    } else {
        echo '<p class="text-muted mb-2">Menunggu keputusan <strong>' . htmlspecialchars($current) . '</strong>.</p>';
    }
} else {
    echo '<p class="text-success mb-2"><strong>PROSES SELESAI.</strong></p>';
}
?>
<div class="card mb-3 p-3 invoice-detail-card">
    <h5>
        Invoice #<?= htmlspecialchars($inv['id']) ?>
        &mdash; posisi:
        <strong><?= htmlspecialchars($current ?? 'SELESAI') ?></strong>
    </h5>

    <!-- indikator flow -->
    <div class="detail-header-flow">
        <?php foreach ($flow as $r): ?>
            <?php if ($isReactive && $r === 'KEUANGAN'): ?>
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-arrow-clockwise"></i> <?= htmlspecialchars($r) ?> (REACTIVE)
                </span>
            <?php elseif ($isClosed && $r === 'KEUANGAN'): ?>
                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($r) ?> (CLOSED)
                </span>
            <?php elseif ($isRevision): ?>
                <span class="badge bg-warning"><?= htmlspecialchars($r) ?></span>
            <?php else: ?>
                <?php if (in_array($r, $approved, true)): ?>
                    <span class="badge bg-success"><?= htmlspecialchars($r) ?></span>
                <?php elseif ($r === $current): ?>
                    <span class="badge bg-primary"><?= htmlspecialchars($r) ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($r) ?></span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <?php if ($isRevision): ?>
        <div class="alert alert-warning mb-3">
            <div><i class="bi bi-exclamation-triangle"></i> Dokumen direvisi oleh
                <strong><?= htmlspecialchars($revisedBy) ?></strong>
            </div>
            <?php if ($canEdit): ?>
                <div class="mt-2">
                    <strong>Petunjuk:</strong>
                    <ol class="mb-0">
                        <li>Periksa catatan revisi dari <?= htmlspecialchars($revisedBy) ?></li>
                        <li>Lakukan perbaikan yang diperlukan</li>
                        <li>Klik "Approve" untuk melanjutkan ke tahap berikutnya</li>
                    </ol>
                </div>
            <?php else: ?>
                <div>Menunggu revisi dari <strong><?= htmlspecialchars($current) ?></strong></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- meta invoice -->
    <div class="invoice-meta">
        <div class="invoice-meta-row">
            <div class="invoice-meta-label">Bulan</div>
            <div>: <?= htmlspecialchars($inv['bulan']) ?></div>
        </div>
        <div class="invoice-meta-row">
            <div class="invoice-meta-label">Jenis</div>
            <div>: <?= htmlspecialchars($inv['jenis_transaksi']) ?></div>
        </div>
        <div class="invoice-meta-row">
            <div class="invoice-meta-label">Pupuk</div>
            <div>: <?= htmlspecialchars($inv['jenis_pupuk']) ?></div>
        </div>
        <div class="invoice-meta-row">
            <div class="invoice-meta-label">Gudang</div>
            <div>: <?= htmlspecialchars($inv['nama_gudang']) ?></div>
        </div>
        <div class="invoice-meta-row">
            <div class="invoice-meta-label">Dibuat</div>
            <div>: <?= htmlspecialchars($inv['created_at']) ?></div>
        </div>
    </div>

    <!-- tabel STO -->
    <div class="detail-table-wrapper">
        <table class="table table-sm table-bordered detail-table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>STO</th>
                    <th>Tanggal</th>
                    <th class="text-end">Normal</th>
                    <th class="text-end">Lembur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lines as $i => $ln): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($ln['nomor_sto']) ?></td>
                        <td><?= htmlspecialchars($ln['tanggal_terbit']) ?></td>
                        <td class="text-end"><?= number_format($ln['tonase_normal']) ?></td>
                        <td class="text-end"><?= number_format($ln['tonase_lembur']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- SECTION CATATAN ROLE -->
    <div class="section-block mb-3">
        <div class="section-title">Catatan dari Setiap Role</div>
        <div class="role-notes-grid">
            <!-- Admin Wilayah -->
            <div class="role-note-card card">
                <div class="card-body">
                    <h6 class="text-primary">
                        <i class="bi bi-person-badge"></i> Admin Wilayah
                    </h6>
                    <p class="card-text small mb-0">
                        <?= !empty($inv['note_admin_wilayah'])
                            ? nl2br(htmlspecialchars($inv['note_admin_wilayah']))
                            : '<em class="text-muted">Belum ada catatan</em>' ?>
                    </p>
                </div>
            </div>
            <!-- Perwakilan PI -->
            <div class="role-note-card card">
                <div class="card-body">
                    <h6 class="text-info">
                        <i class="bi bi-person-check"></i> Perwakilan PI
                    </h6>
                    <p class="card-text small mb-0">
                        <?= !empty($inv['note_perwakilan_pi'])
                            ? nl2br(htmlspecialchars($inv['note_perwakilan_pi']))
                            : '<em class="text-muted">Belum ada catatan</em>' ?>
                    </p>
                </div>
            </div>
            <!-- Admin PCS -->
            <div class="role-note-card card">
                <div class="card-body">
                    <h6 class="text-warning">
                        <i class="bi bi-clipboard-check"></i> Admin PCS
                    </h6>
                    <p class="card-text small mb-0">
                        <?= !empty($inv['note_admin_pcs'])
                            ? nl2br(htmlspecialchars($inv['note_admin_pcs']))
                            : '<em class="text-muted">Belum ada catatan</em>' ?>
                    </p>
                </div>
            </div>
            <!-- Keuangan -->
            <div class="role-note-card card">
                <div class="card-body">
                    <h6 class="text-success">
                        <i class="bi bi-cash-stack"></i> Keuangan
                    </h6>
                    <p class="card-text small mb-0">
                        <?= !empty($inv['note_keuangan'])
                            ? nl2br(htmlspecialchars($inv['note_keuangan']))
                            : '<em class="text-muted">Belum ada catatan</em>' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION FILE LAMPIRAN -->
    <?php if (!empty($invoiceFiles ?? [])): ?>
        <div class="section-block mb-3">
            <div class="section-title">File Lampiran</div>
            <div class="list-group">
                <?php foreach ($invoiceFiles as $file): ?>
                    <div class="list-group-item d-flex flex-sm-row flex-column justify-content-between align-items-sm-center align-items-start">
                        <div class="d-flex align-items-center gap-2 mb-2 mb-sm-0">
                            <i class="bi bi-file-earmark"></i>
                            <span><?= htmlspecialchars($file['filename']) ?></span>
                            <small class="text-muted">
                                (<?= number_format($file['size_bytes'] / 1024, 2) ?> KB)
                            </small>
                        </div>
                        <?php
                        $baseUrl = rtrim($uploadUrl ?? 'uploads/invoice/', '/') . '/';
                        $fileUrl = htmlspecialchars($baseUrl . $file['stored_name']);
                        ?>
                        <div class="d-flex gap-2">
                            <a href="download/download.php?file=<?= urlencode($file['stored_name']) ?>"
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- FORM INPUT & TOMBOL ACTION -->
    <?php if ($role === 'KEUANGAN'): ?>

        <?php if ($isClosed && !$isReactive): ?>
            <div class="section-block mb-3">
                <button class="btn btn-warning w-100 w-sm-auto"
                        id="btnReactive"
                        data-id="<?= (int)$inv['id'] ?>">
                    <i class="bi bi-arrow-clockwise"></i> Aktifkan Kembali (Reactive)
                </button>
            </div>

        <?php elseif ($current === 'KEUANGAN' && !$hasDecided): ?>
            <div class="section-block mb-3">
                <?php if ($adminPcsApproved): ?>
                    <div class="row mb-3 g-2">
                        <div class="col-md-6">
                            <label class="form-label">Nomor MMJ</label>
                            <input type="text" id="no_mmj" class="form-control"
                                   value="<?= htmlspecialchars($inv['no_mmj'] ?? '') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor SOJ</label>
                            <input type="text" id="no_soj" class="form-control"
                                   value="<?= htmlspecialchars($inv['no_soj'] ?? '') ?>" disabled>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> Nomor SOJ dan MMJ akan ditampilkan setelah Admin PCS melakukan approve.
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Catatan Keuangan
                        <?php if ($isReactive || $isRevision): ?>
                            <small class="text-warning">(Revisi - Wajib diisi jika reject)</small>
                        <?php endif; ?>
                    </label>
                    <textarea id="note_role" class="form-control" rows="3"
                              placeholder="<?= ($isReactive || $isRevision) ? 'Jelaskan alasan revisi jika akan reject...' : 'Masukkan catatan Anda...' ?>"><?= htmlspecialchars($inv['note_keuangan'] ?? '') ?></textarea>
                    <?php if ($isReactive || $isRevision): ?>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Catatan ini akan dikirim ke ADMIN PCS jika tombol REJECT dipilih.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-2">
                    <button class="btn btn-danger btn-decision"
                            data-decision="reject"
                            data-id="<?= (int)$inv['id'] ?>"
                            data-role="KEUANGAN">
                        <i class="bi bi-x-circle"></i> Reject (Turun ke Admin PCS)
                    </button>

                    <button class="btn btn-success"
                            id="btnClose"
                            data-id="<?= (int)$inv['id'] ?>">
                        <i class="bi bi-check-circle"></i> Close (Selesai)
                    </button>
                </div>
            </div>

        <?php else: ?>
            <div class="section-block mb-2">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i>
                    <?php if ($hasDecided): ?>
                        Anda sudah memberikan keputusan untuk siklus ini.
                    <?php else: ?>
                        Invoice sedang diproses oleh: <strong><?= htmlspecialchars($current) ?></strong>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php elseif ($canDecide || $canEdit || ($role === 'ADMIN_PCS' && $adminPcsApproved)): ?>

        <div class="section-block mb-3">
            <?php if ($role === 'ADMIN_PCS'): ?>
                <div class="row mb-3 g-2">
                    <div class="col-md-6">
                        <label class="form-label">Nomor MMJ</label>
                        <input type="text" id="no_mmj" class="form-control"
                               value="<?= htmlspecialchars($inv['no_mmj'] ?? '') ?>"
                               <?= (!$canEdit && !$canDecide) ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor SOJ</label>
                        <input type="text" id="no_soj" class="form-control"
                               value="<?= htmlspecialchars($inv['no_soj'] ?? '') ?>"
                               <?= (!$canEdit && !$canDecide) ? 'disabled' : '' ?>>
                    </div>
                </div>

                <?php if ($canDecide): ?>
                    <div class="mb-3">
                        <label class="form-label">Lampiran (boleh banyak)</label>
                        <div id="dz-create" class="uploader p-4 text-center mb-2" role="button" tabindex="0"
                             aria-label="Unggah lampiran">
                            <div class="cloud mb-2">☁⬆</div>
                            <div class="cta">Click To Upload</div>
                            <small class="text-muted d-block mt-1">
                                atau drag & drop file ke sini • Maks 10MB/file • pdf, jpg, png, xls, xlsx
                            </small>
                            <div class="upload-label-overlay"></div>
                        </div>
                        <input id="files-create" type="file" name="files[]" class="d-none" multiple
                               accept=".pdf,.png,.jpg,.jpeg,.xls,.xlsx">
                        <ul id="list-create" class="file-list"></ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mb-3">
                <?php if ($role === 'ADMIN_WILAYAH'): ?>
                    <label class="form-label fw-bold">
                        Catatan Admin Wilayah
                        <?php if ($canEdit): ?><small class="text-muted">(Revisi)</small><?php endif; ?>
                    </label>
                    <textarea id="note_role" class="form-control" rows="3"
                              placeholder="<?= $canEdit ? 'Tambahkan catatan revisi...' : 'Masukkan catatan...' ?>"><?= htmlspecialchars($inv['note_admin_wilayah'] ?? '') ?></textarea>
                <?php elseif ($role === 'PERWAKILAN_PI'): ?>
                    <label class="form-label fw-bold">
                        Catatan Perwakilan PI
                        <?php if ($canEdit): ?><small class="text-muted">(Revisi)</small><?php endif; ?>
                    </label>
                    <textarea id="note_role" class="form-control" rows="3"
                              placeholder="<?= $canEdit ? 'Tambahkan catatan revisi...' : 'Masukkan catatan...' ?>"><?= htmlspecialchars($inv['note_perwakilan_pi'] ?? '') ?></textarea>
                <?php elseif ($role === 'ADMIN_PCS'): ?>
                    <label class="form-label fw-bold">
                        Catatan Admin PCS
                        <?php if ($canEdit): ?><small class="text-muted">(Revisi)</small><?php endif; ?>
                    </label>
                    <textarea id="note_role" class="form-control" rows="3"
                              placeholder="<?= $canEdit ? 'Tambahkan catatan revisi...' : 'Masukkan catatan...' ?>"><?= htmlspecialchars($inv['note_admin_pcs'] ?? '') ?></textarea>
                <?php endif; ?>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <?php if ($canDecide): ?>
                    <button class="btn btn-success btn-decision"
                            data-decision="approve"
                            data-id="<?= (int)$inv['id'] ?>"
                            data-role="<?= htmlspecialchars($role) ?>">
                        <i class="bi bi-check-circle"></i>
                        <?= $isRevision && $current === $role ? 'Approve Revisi' : 'Approve' ?>
                    </button>
                    <?php if ($role !== 'ADMIN_WILAYAH'): ?>
                        <button class="btn btn-danger btn-decision"
                                data-decision="reject"
                                data-id="<?= (int)$inv['id'] ?>"
                                data-role="<?= htmlspecialchars($role) ?>">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Riwayat -->
    <?php if ($logs): ?>
        <div class="section-block">
            <div class="section-title mb-2">Riwayat Keputusan</div>
            <ul class="list-group">
                <?php foreach ($logs as $log): ?>
                    <li class="list-group-item d-flex flex-sm-row flex-column justify-content-between align-items-sm-center align-items-start">
                        <span class="mb-1 mb-sm-0">
                            <strong><?= htmlspecialchars($log['role']) ?></strong>
                            &mdash;
                            <?php if ($log['decision'] === 'APPROVED'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($log['decision'] === 'REJECTED'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php elseif ($log['decision'] === 'CLOSE'): ?>
                                <span class="badge bg-primary">Closed</span>
                            <?php elseif ($log['decision'] === 'REACTIVE'): ?>
                                <span class="badge bg-warning">Reactivated</span>
                            <?php endif; ?>
                        </span>
                        <small class="text-muted"><?= htmlspecialchars($log['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
