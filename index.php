<?php
// index.php – Form Estimasi Effort COCOMO Intermediate
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COCOMO Estimator – Form Estimasi</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bs-body-bg: #0f1117;
      --bs-body-color: #e2e8f0;
      --sidebar-bg: #161b27;
      --card-bg: #1e2535;
      --card-border: #2a3348;
      --input-bg: #111827;
      --input-border: #2a3348;
      --accent: #4f8ef7;
      --accent-hover: #3b7de8;
    }
    body { font-family: 'Inter', sans-serif; background: var(--bs-body-bg); color: var(--bs-body-color); }

    /* ── Navbar ── */
    .navbar-custom { background: #161b27; border-bottom: 1px solid #2a3348; }
    .navbar-custom .navbar-brand { font-weight: 700; color: #e2e8f0 !important; letter-spacing: -0.3px; }
    .navbar-custom .nav-link { color: #94a3b8 !important; font-size: .875rem; }
    .navbar-custom .nav-link:hover,
    .navbar-custom .nav-link.active { color: #e2e8f0 !important; }
    .navbar-custom .nav-link.active { border-bottom: 2px solid var(--accent); }

    /* ── Page header ── */
    .page-header { background: linear-gradient(135deg, #1a2240 0%, #0f1117 100%);
      border-bottom: 1px solid #2a3348; padding: 2rem 0; }
    .page-header h1 { font-size: 1.6rem; font-weight: 700; color: #e2e8f0; margin: 0; }
    .page-header p { color: #94a3b8; margin: .4rem 0 0; font-size: .875rem; }
    .badge-method { background: rgba(79,142,247,.15); color: #7cb3ff;
      border: 1px solid rgba(79,142,247,.3); font-size: .75rem; font-weight: 500; }

    /* ── Cards ── */
    .card-custom { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; }

    /* ── Form inputs ── */
    .form-control, .form-select {
      background: var(--input-bg); border: 1px solid var(--input-border);
      color: #e2e8f0; border-radius: 8px; font-size: .875rem; padding: .55rem .9rem;
    }
    .form-control:focus, .form-select:focus {
      background: var(--input-bg); color: #e2e8f0;
      border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,142,247,.15);
    }
    .form-label { font-size: .8rem; font-weight: 500; color: #94a3b8; margin-bottom: .35rem; }
    .form-select option { background: #1e2535; }

    /* ── Group headers ── */
    .group-header {
      display: flex; align-items: center; gap: .6rem;
      background: #1a2240; border-left: 3px solid var(--accent);
      padding: .6rem 1rem; border-radius: 0 8px 8px 0;
      font-weight: 600; font-size: .85rem; color: #7cb3ff;
    }

    /* ── Driver rows ── */
    .driver-card {
      background: #131929; border: 1px solid #232d42;
      border-radius: 8px; padding: .75rem 1rem;
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .driver-card:hover { border-color: #3a4f6e; }
    .driver-code { font-weight: 700; font-size: .75rem; color: var(--accent);
      background: rgba(79,142,247,.12); padding: 2px 8px; border-radius: 5px; }
    .driver-label { font-size: .82rem; color: #c4cfde; }
    .driver-card .form-select { width: 155px; flex-shrink: 0; font-size: .8rem; padding: .4rem .7rem; }

    /* ── Stat cards ── */
    .info-card { background: #131929; border: 1px solid #232d42; border-radius: 10px; padding: 1rem 1.25rem; }
    .info-card .ic-label { font-size: .72rem; color: #64748b; text-transform: uppercase; letter-spacing: .8px; }
    .info-card .ic-value { font-size: 1.5rem; font-weight: 700; color: #e2e8f0; line-height: 1.2; }
    .info-card .ic-unit { font-size: .78rem; color: #94a3b8; }
    .info-card.accent-blue  { border-color: rgba(79,142,247,.4); }
    .info-card.accent-green { border-color: rgba(52,211,153,.4); }
    .info-card.accent-amber { border-color: rgba(251,191,36,.4);  }

    /* ── Buttons ── */
    .btn-primary-custom {
      background: var(--accent); border: none; color: #fff;
      font-weight: 600; border-radius: 8px; padding: .6rem 1.5rem; font-size: .875rem;
    }
    .btn-primary-custom:hover { background: var(--accent-hover); color: #fff; }
    .btn-outline-custom {
      background: transparent; border: 1px solid #2a3348; color: #94a3b8;
      border-radius: 8px; padding: .6rem 1.2rem; font-size: .875rem;
    }
    .btn-outline-custom:hover { background: #1e2535; color: #e2e8f0; border-color: #3a4f6e; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #0f1117; }
    ::-webkit-scrollbar-thumb { background: #2a3348; border-radius: 3px; }
  </style>
</head>
<body>

<!-- ═══════════════════════════ NAVBAR ═══════════════════════════ -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-calculator-fill me-2 text-primary"></i>COCOMO Estimator
    </a>
    <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <i class="bi bi-list text-light"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto gap-1">
        <li class="nav-item">
          <a class="nav-link active px-3" href="index.php"><i class="bi bi-pencil-square me-1"></i>Form Estimasi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3" href="result.php"><i class="bi bi-table me-1"></i>Hasil &amp; Riwayat</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3" href="download.php"><i class="bi bi-download me-1"></i>Export CSV</a>
        </li>
        <li class="nav-item ms-2">
          <a class="nav-link px-3 text-danger" href="clear.php"
             onclick="return confirm('Hapus semua data estimasi? Tindakan ini tidak dapat dibatalkan.')">
            <i class="bi bi-trash3 me-1"></i>Hapus Data
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ═══════════════════════════ PAGE HEADER ═══════════════════════════ -->
<div class="page-header">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="badge badge-method rounded-pill px-3 py-1">
            <i class="bi bi-cpu me-1"></i>Intermediate COCOMO
          </span>
          <span class="badge badge-method rounded-pill px-3 py-1">
            <i class="bi bi-diagram-3 me-1"></i>CFPR Weighting
          </span>
        </div>
        <h1>Form Estimasi Effort Perangkat Lunak</h1>
        <p>Isi data proyek dan 15 cost driver untuk menghitung Effort, Waktu, dan Tim yang dibutuhkan.</p>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ -->
<div class="container-fluid px-4 py-4">
  <form action="process.php" method="POST">

    <!-- ── Error alert ── -->
    <?php if (isset($_GET['error'])): ?>
    <div class="alert border-0 mb-3 p-3 rounded-3" style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3)!important;color:#fca5a5;">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Input tidak valid:</strong>
      <?= htmlspecialchars($_GET['msg'] ?? 'Pastikan semua field diisi dengan benar.') ?>
    </div>
    <?php endif; ?>

    <!-- ── Info cards ── -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="info-card accent-blue">
          <div class="ic-label">Metode Estimasi</div>
          <div class="ic-value" style="font-size:1.1rem;">Intermediate COCOMO</div>
          <div class="ic-unit">Effort = a × KLOC<sup>b</sup> × EAF</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-card accent-green">
          <div class="ic-label">Pembobotan</div>
          <div class="ic-value" style="font-size:1.1rem;">CFPR</div>
          <div class="ic-unit">Consistent Fuzzy Preference Relation</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-card accent-amber">
          <div class="ic-label">Cost Drivers</div>
          <div class="ic-value">15</div>
          <div class="ic-unit">4 kelompok atribut · 6 rating tiap driver</div>
        </div>
      </div>
    </div>

    <!-- ── Data Proyek ── -->
    <div class="card-custom p-4 mb-4">
      <h5 class="fw-semibold mb-3" style="color:#e2e8f0;">
        <i class="bi bi-folder2-open me-2 text-primary"></i>Data Proyek
      </h5>
      <div class="row g-3">
        <div class="col-md-5">
          <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
          <input type="text" name="project_name" class="form-control"
                 placeholder="Contoh: Sistem Informasi Akademik" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">LOC – Jumlah Baris Kode <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="number" name="loc" class="form-control"
                   placeholder="Contoh: 50000" min="1000" required>
            <span class="input-group-text" style="background:#1a2240;border-color:#2a3348;color:#94a3b8;">lines</span>
          </div>
          <div class="form-text" style="color:#64748b;font-size:.75rem;">
            <i class="bi bi-info-circle me-1"></i>
            Minimal 1.000 baris. Contoh: proyek kecil ~10.000, menengah ~50.000, besar ~300.000+
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tipe Proyek <span class="text-danger">*</span></label>
          <select name="project_type" class="form-select" required>
            <option value="sederhana">Organic (Sederhana)</option>
            <option value="menengah">Semi-detached (Menengah)</option>
            <option value="sulit">Embedded (Sulit)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ── 15 Cost Drivers ── -->
    <div class="card-custom p-4 mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="fw-semibold mb-0" style="color:#e2e8f0;">
          <i class="bi bi-sliders2 me-2 text-primary"></i>15 Cost Driver COCOMO Intermediate
        </h5>
        <span class="text-muted" style="font-size:.8rem;">
          <i class="bi bi-info-circle me-1"></i>Default: <strong class="text-light">Nominal</strong>
          &nbsp;·&nbsp; EAF = produk seluruh multiplier rating
        </span>
      </div>

      <?php
      $groups = [
        'Product Attributes' => [
          'icon' => 'bi-box-seam', 'color' => '#4f8ef7',
          'drivers' => [
            'RELY' => ['label' => 'Required Software Reliability', 'weight' => '0.1674'],
            'DATA' => ['label' => 'Database Size',                 'weight' => '0.0662'],
            'CPLX' => ['label' => 'Product Complexity',            'weight' => '0.1384'],
          ],
        ],
        'Computer Attributes' => [
          'icon' => 'bi-cpu', 'color' => '#34d399',
          'drivers' => [
            'TIME' => ['label' => 'Execution Time Constraint',   'weight' => '0.1050'],
            'STOR' => ['label' => 'Main Storage Constraint',     'weight' => '0.0547'],
            'VIRT' => ['label' => 'Virtual Machine Volatility',  'weight' => '0.0399'],
            'TURN' => ['label' => 'Computer Turnaround Time',    'weight' => '0.0754'],
          ],
        ],
        'Personnel Attributes' => [
          'icon' => 'bi-people', 'color' => '#a78bfa',
          'drivers' => [
            'ACAP' => ['label' => 'Analyst Capability',              'weight' => '0.0732'],
            'AEXP' => ['label' => 'Application Experience',          'weight' => '0.0578'],
            'PCAP' => ['label' => 'Programmer Capability',           'weight' => '0.0434'],
            'VEXP' => ['label' => 'Virtual Machine Experience',      'weight' => '0.0296'],
            'LEXP' => ['label' => 'Programming Language Experience', 'weight' => '0.0270'],
          ],
        ],
        'Project Attributes' => [
          'icon' => 'bi-kanban', 'color' => '#fbbf24',
          'drivers' => [
            'MODP' => ['label' => 'Modern Programming Practice',   'weight' => '0.0578'],
            'TOOL' => ['label' => 'Use of Software Tools',         'weight' => '0.0339'],
            'SCED' => ['label' => 'Required Development Schedule', 'weight' => '0.0303'],
          ],
        ],
      ];
      $ratings = ['vl'=>'Very Low','l'=>'Low','n'=>'Nominal','h'=>'High','vh'=>'Very High','xh'=>'Extra High'];

      foreach ($groups as $gName => $gData): ?>
      <div class="mb-4">
        <div class="group-header mb-3" style="border-color:<?= $gData['color'] ?>;color:<?= $gData['color'] ?>;">
          <i class="bi <?= $gData['icon'] ?>"></i>
          <?= $gName ?>
          <span class="ms-auto text-muted" style="font-size:.72rem;font-weight:400;">
            <?= count($gData['drivers']) ?> cost driver
          </span>
        </div>
        <div class="row g-2">
          <?php foreach ($gData['drivers'] as $code => $info): ?>
          <div class="col-md-6 col-lg-4">
            <div class="driver-card">
              <div>
                <span class="driver-code d-inline-block mb-1"><?= $code ?></span>
                <div class="driver-label"><?= htmlspecialchars($info['label']) ?></div>
                <div style="font-size:.7rem;color:#4a5568;margin-top:2px;">
                  Bobot CFPR: <span style="color:#6b7fa3;"><?= $info['weight'] ?></span>
                </div>
              </div>
              <select name="<?= strtolower($code) ?>" class="form-select">
                <?php foreach ($ratings as $val => $text): ?>
                <option value="<?= $val ?>"<?= $val === 'n' ? ' selected' : '' ?>><?= $text ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Actions ── -->
    <div class="d-flex justify-content-end gap-2 mb-5">
      <button type="reset" class="btn btn-outline-custom">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
      <button type="submit" class="btn btn-primary-custom px-4">
        <i class="bi bi-lightning-charge-fill me-1"></i>Hitung Estimasi
      </button>
    </div>

  </form>
</div>

<!-- Footer -->
<footer class="text-center py-3 border-top" style="border-color:#2a3348!important;color:#475569;font-size:.78rem;">
  COCOMO Effort Estimator &nbsp;·&nbsp; Metode: Intermediate COCOMO + CFPR &nbsp;·&nbsp;
  Ref: <em>Lestari et al., JUTIF 2025</em>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
