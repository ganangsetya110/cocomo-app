<?php
require_once "db.php";

// Ambil data COCOMO81
$stmt_ds = $pdo->query("SELECT * FROM cocomo_dataset ORDER BY id ASC");
$dataset = $stmt_ds->fetchAll(PDO::FETCH_ASSOC);

// Ambil data prediksi LSSVM
$stmt_pred = $pdo->query("SELECT id, actual_effort, predicted_effort, mre,
    ROUND(mre*100, 4) as mre_pct FROM lssvm_predictions ORDER BY id ASC");
$predictions = $stmt_pred->fetchAll(PDO::FETCH_ASSOC);

// Hitung MMRE dan RMSE dari DB
$stmt_metrics = $pdo->query("SELECT
    AVG(mre) as mmre,
    SQRT(AVG(POW(actual_effort - predicted_effort, 2))) as rmse,
    COUNT(*) as n
    FROM lssvm_predictions");
$metrics = $stmt_metrics->fetch(PDO::FETCH_ASSOC);

$mmre = round($metrics['mmre'] * 100, 4);
$rmse = round($metrics['rmse'], 4);
$n    = $metrics['n'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>COCOMO Estimator – Dataset &amp; Validasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family:'Inter',sans-serif; background:#0f1117; color:#e2e8f0; }
    .navbar-custom { background:#161b27; border-bottom:1px solid #2a3348; }
    .navbar-custom .navbar-brand { font-weight:700; color:#e2e8f0 !important; }
    .navbar-custom .nav-link { color:#94a3b8 !important; font-size:.875rem; }
    .navbar-custom .nav-link:hover,
    .navbar-custom .nav-link.active { color:#e2e8f0 !important; }
    .navbar-custom .nav-link.active { border-bottom:2px solid #4f8ef7; }
    .page-header { background:linear-gradient(135deg,#1a2240 0%,#0f1117 100%);
      border-bottom:1px solid #2a3348; padding:2rem 0; }
    .page-header h1 { font-size:1.6rem; font-weight:700; color:#e2e8f0; margin:0; }
    .page-header p  { color:#94a3b8; margin:.4rem 0 0; font-size:.875rem; }
    .card-custom { background:#1e2535; border:1px solid #2a3348; border-radius:12px; }
    .stat-card { background:#131929; border:1px solid #232d42; border-radius:10px; padding:1.1rem 1.25rem; }
    .stat-card .sc-label { font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.8px; }
    .stat-card .sc-value { font-size:2rem; font-weight:700; line-height:1.1; }
    .stat-card .sc-unit  { font-size:.75rem; color:#94a3b8; margin-top:.2rem; }
    .table-dark-custom { --bs-table-bg:#1e2535; --bs-table-striped-bg:#1a2040;
      --bs-table-hover-bg:#22294a; --bs-table-border-color:#2a3348;
      --bs-table-color:#c4cfde; font-size:.82rem; }
    .table-dark-custom thead th { background:#161b27; color:#7cb3ff; font-weight:600;
      font-size:.75rem; text-transform:uppercase; letter-spacing:.5px;
      border-bottom:2px solid #2a3348; white-space:nowrap; padding:.75rem .6rem; }
    .table-dark-custom tbody td { padding:.55rem .6rem; }
    .mre-low  { color:#34d399; font-weight:600; }
    .mre-mid  { color:#fbbf24; }
    .mre-high { color:#f87171; }
    .accuracy-bar { height:5px; border-radius:3px; background:#232d42; overflow:hidden; }
    .accuracy-fill { height:100%; border-radius:3px; }
    ::-webkit-scrollbar { width:6px; height:6px; }
    ::-webkit-scrollbar-track { background:#0f1117; }
    ::-webkit-scrollbar-thumb { background:#2a3348; border-radius:3px; }
  </style>
</head>
<body>

<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link px-3" href="index.php"><i class="bi bi-pencil-square me-1"></i>Form Estimasi</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="result.php"><i class="bi bi-table me-1"></i>Hasil &amp; Riwayat</a></li>
        <li class="nav-item"><a class="nav-link active px-3" href="dataset.php"><i class="bi bi-database me-1"></i>Dataset &amp; Validasi</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="download.php"><i class="bi bi-download me-1"></i>Export CSV</a></li>
        <li class="nav-item ms-2">
          <a class="nav-link px-3 text-danger" href="clear.php"
             onclick="return confirm('Hapus semua data estimasi?')">
            <i class="bi bi-trash3 me-1"></i>Hapus Data
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container-fluid px-4">
    <h1><i class="bi bi-database-fill me-2 text-primary"></i>Dataset &amp; Validasi Model LSSVM</h1>
    <p>Dataset COCOMO81 (63 proyek) yang digunakan dalam penelitian beserta hasil validasi model CFPR-LSSVM.</p>
  </div>
</div>

<div class="container-fluid px-4 py-4">

  <!-- ── METRICS CARDS ── -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-color:rgba(52,211,153,.4);">
        <div class="sc-label">MMRE</div>
        <div class="sc-value" style="color:#34d399; font-size:1.6rem;"><?= $mmre ?>%</div>
        <div class="sc-unit">Mean Magnitude of Relative Error</div>
        <div class="mt-2">
          <?php
          $mmre_interp = $mmre < 25 ? ['success','Highly Accurate'] :
                        ($mmre < 50 ? ['warning','Acceptable'] :
                        ($mmre < 75 ? ['warning','Moderate'] : ['danger','Low Accuracy']));
          ?>
          <span class="badge bg-<?= $mmre_interp[0] ?> bg-opacity-25 text-<?= $mmre_interp[0] ?> border border-<?= $mmre_interp[0] ?> border-opacity-25">
            <?= $mmre_interp[1] ?>
          </span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-color:rgba(79,142,247,.4);">
        <div class="sc-label">RMSE</div>
        <div class="sc-value text-primary" style="font-size:1.6rem;"><?= $rmse ?></div>
        <div class="sc-unit">Root Mean Square Error</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-color:rgba(251,191,36,.4);">
        <div class="sc-label">Data Pengujian</div>
        <div class="sc-value" style="color:#fbbf24;"><?= $n ?></div>
        <div class="sc-unit">Sampel uji LSSVM (testing set)</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card" style="border-color:rgba(167,139,250,.4);">
        <div class="sc-label">Total Dataset</div>
        <div class="sc-value" style="color:#a78bfa;"><?= count($dataset) ?></div>
        <div class="sc-unit">Proyek COCOMO81</div>
      </div>
    </div>
  </div>

  <!-- ── HASIL PREDIKSI LSSVM ── -->
  <div class="card-custom p-4 mb-4">
    <div class="mb-3">
      <h5 class="fw-semibold mb-1" style="color:#e2e8f0;">
        <i class="bi bi-graph-up me-2 text-primary"></i>Hasil Prediksi LSSVM vs Aktual
      </h5>
      <p class="text-muted mb-0" style="font-size:.82rem;">
        Perbandingan nilai effort aktual dengan prediksi model <strong class="text-light">CFPR-RBF-LSSVM</strong>
        dari dataset COCOMO81. Sumber: Google Colab.
      </p>
    </div>

    <!-- Interpretasi MMRE legend -->
    <div class="d-flex flex-wrap gap-2 mb-3">
      <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-3">MRE &lt; 10% — Sangat Akurat</span>
      <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 px-3">MRE 10–50% — Cukup Akurat</span>
      <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25 px-3">MRE &gt; 50% — Kurang Akurat</span>
    </div>

    <div class="table-responsive">
      <table class="table table-dark-custom table-striped table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Actual Effort (PM)</th>
            <th>Predicted Effort (PM)</th>
            <th>Selisih</th>
            <th>MRE (%)</th>
            <th style="min-width:120px;">Akurasi Visual</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($predictions as $i => $p):
            $mre_val  = (float)$p['mre_pct'];
            $diff     = round((float)$p['predicted_effort'] - (float)$p['actual_effort'], 2);
            $mre_cls  = $mre_val < 10 ? 'mre-low' : ($mre_val < 50 ? 'mre-mid' : 'mre-high');
            // bar: akurasi = max(0, 100 - mre), cap di 100
            $acc_pct  = max(0, min(100, 100 - $mre_val));
            $bar_color = $mre_val < 10 ? '#34d399' : ($mre_val < 50 ? '#fbbf24' : '#f87171');
          ?>
          <tr>
            <td><span class="text-muted"><?= $i+1 ?></span></td>
            <td><strong style="color:#e2e8f0;"><?= number_format((float)$p['actual_effort'], 2) ?></strong></td>
            <td style="color:#7cb3ff;"><?= number_format((float)$p['predicted_effort'], 4) ?></td>
            <td>
              <span style="color:<?= $diff >= 0 ? '#f87171' : '#34d399' ?>">
                <?= $diff >= 0 ? '+' : '' ?><?= number_format($diff, 2) ?>
              </span>
            </td>
            <td><span class="<?= $mre_cls ?>"><?= number_format($mre_val, 2) ?>%</span></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="accuracy-bar flex-grow-1">
                  <div class="accuracy-fill" style="width:<?= $acc_pct ?>%;background:<?= $bar_color ?>;"></div>
                </div>
                <span style="font-size:.7rem;color:#64748b;width:30px;"><?= round($acc_pct) ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr style="background:#161b27;">
            <td colspan="4" class="text-end fw-semibold" style="color:#94a3b8;">
              Rata-rata (MMRE):
            </td>
            <td><strong class="<?= $mmre < 25 ? 'text-success' : ($mmre < 50 ? 'text-warning' : 'text-danger') ?>"><?= $mmre ?>%</strong></td>
            <td><code style="color:#7cb3ff;font-size:.78rem;">RMSE = <?= $rmse ?></code></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- ── DATASET COCOMO81 ── -->
  <div class="card-custom p-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
      <div>
        <h5 class="fw-semibold mb-1" style="color:#e2e8f0;">
          <i class="bi bi-table me-2 text-primary"></i>Dataset COCOMO81
        </h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">
          63 proyek historis dengan 15 cost driver beserta nilai multiplier dan EAF.
          Sumber: Boehm (1981), dimodifikasi dengan bobot CFPR.
        </p>
      </div>
      <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25 px-3 py-2">
        <?= count($dataset) ?> records
      </span>
    </div>
    <div class="table-responsive">
      <table class="table table-dark-custom table-striped table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>LOC</th>
            <th>Actual (PM)</th>
            <th>EAF</th>
            <th title="Required Reliability">RELY</th>
            <th title="Database Size">DATA</th>
            <th title="Product Complexity">CPLX</th>
            <th title="Execution Time">TIME</th>
            <th title="Main Storage">STOR</th>
            <th title="VM Volatility">VIRT</th>
            <th title="Turnaround">TURN</th>
            <th title="Analyst Cap.">ACAP</th>
            <th title="App. Exp.">AEXP</th>
            <th title="Programmer Cap.">PCAP</th>
            <th title="VM Exp.">VEXP</th>
            <th title="Lang. Exp.">LEXP</th>
            <th title="Modern Prog.">MODP</th>
            <th title="SW Tools">TOOL</th>
            <th title="Schedule">SCED</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dataset as $i => $row): ?>
          <tr>
            <td><span class="text-muted"><?= $i+1 ?></span></td>
            <td><strong style="color:#e2e8f0;"><?= number_format((float)$row['loc'], 1) ?></strong></td>
            <td><strong style="color:#34d399;"><?= number_format((float)$row['actual_effort']) ?></strong></td>
            <td><code style="color:#7cb3ff;font-size:.78rem;"><?= number_format((float)$row['eaf'], 4) ?></code></td>
            <?php
            $drivers = ['rely','data_size','cplx','time_c','stor','virt','turn','acap','aexp','pcap','vexp','lexp','modp','tool','sced'];
            foreach ($drivers as $d):
              $v = (float)$row[$d];
              $col = $v < 1 ? 'color:#34d399' : ($v > 1 ? 'color:#f87171' : 'color:#94a3b8');
            ?>
            <td style="<?= $col ?>;font-size:.78rem;"><?= number_format($v, 2) ?></td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3 d-flex flex-wrap gap-3" style="font-size:.75rem;color:#4a5568;">
      <span style="color:#34d399;">■</span> Nilai &lt; 1 (menurunkan effort)
      <span style="color:#f87171;margin-left:.5rem;">■</span> Nilai &gt; 1 (menaikkan effort)
      <span style="color:#94a3b8;margin-left:.5rem;">■</span> Nominal = 1.0
    </div>
  </div>

</div>

<footer class="text-center py-3 border-top" style="border-color:#2a3348!important;color:#475569;font-size:.78rem;">
  COCOMO Effort Estimator &nbsp;·&nbsp; Intermediate COCOMO + CFPR &nbsp;·&nbsp;
  Ref: <em>Lestari et al., JUTIF Vol.6 No.6, 2025</em>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
