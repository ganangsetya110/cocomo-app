<?php
require_once "db.php";

$saved  = isset($_GET['saved']) && $_GET['saved'] === '1';
$effort = isset($_GET['effort']) ? htmlspecialchars($_GET['effort']) : null;
$time   = isset($_GET['time'])   ? htmlspecialchars($_GET['time'])   : null;
$team   = isset($_GET['team'])   ? htmlspecialchars($_GET['team'])   : null;
$eaf    = isset($_GET['eaf'])    ? htmlspecialchars($_GET['eaf'])    : null;

$stmt = $pdo->query("SELECT * FROM estimations ORDER BY created_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cfpr_data = [
  ['group'=>'Product',  'group_color'=>'primary', 'code'=>'RELY','label'=>'Required Software Reliability', 'weight'=>0.1674],
  ['group'=>'Product',  'group_color'=>'primary', 'code'=>'CPLX','label'=>'Product Complexity',            'weight'=>0.1384],
  ['group'=>'Computer', 'group_color'=>'success', 'code'=>'TIME','label'=>'Execution Time Constraint',     'weight'=>0.1050],
  ['group'=>'Computer', 'group_color'=>'success', 'code'=>'TURN','label'=>'Computer Turnaround Time',      'weight'=>0.0754],
  ['group'=>'Personnel','group_color'=>'purple',  'code'=>'ACAP','label'=>'Analyst Capability',            'weight'=>0.0732],
  ['group'=>'Product',  'group_color'=>'primary', 'code'=>'DATA','label'=>'Database Size',                 'weight'=>0.0662],
  ['group'=>'Personnel','group_color'=>'purple',  'code'=>'AEXP','label'=>'Application Experience',        'weight'=>0.0578],
  ['group'=>'Project',  'group_color'=>'warning', 'code'=>'MODP','label'=>'Modern Programming Practice',   'weight'=>0.0578],
  ['group'=>'Computer', 'group_color'=>'success', 'code'=>'STOR','label'=>'Main Storage Constraint',       'weight'=>0.0547],
  ['group'=>'Personnel','group_color'=>'purple',  'code'=>'PCAP','label'=>'Programmer Capability',         'weight'=>0.0434],
  ['group'=>'Computer', 'group_color'=>'success', 'code'=>'VIRT','label'=>'Virtual Machine Volatility',    'weight'=>0.0399],
  ['group'=>'Project',  'group_color'=>'warning', 'code'=>'TOOL','label'=>'Use of Software Tools',         'weight'=>0.0339],
  ['group'=>'Project',  'group_color'=>'warning', 'code'=>'SCED','label'=>'Required Development Schedule', 'weight'=>0.0303],
  ['group'=>'Personnel','group_color'=>'purple',  'code'=>'VEXP','label'=>'Virtual Machine Experience',    'weight'=>0.0296],
  ['group'=>'Personnel','group_color'=>'purple',  'code'=>'LEXP','label'=>'Programming Language Experience','weight'=>0.0270],
];

function type_badge(string $t): string {
  $m = ['sederhana'=>['success','Organic'],'menengah'=>['warning','Semi-detached'],'sulit'=>['danger','Embedded']];
  [$c,$l] = $m[$t] ?? ['secondary',$t];
  return '<span class="badge bg-'.$c.' bg-opacity-25 text-'.$c.' border border-'.$c.' border-opacity-25">'.htmlspecialchars($l).'</span>';
}
function rating_label(string $r): string {
  return ['vl'=>'Very Low','l'=>'Low','n'=>'Nominal','h'=>'High','vh'=>'Very High','xh'=>'Extra High'][$r] ?? $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>COCOMO Estimator – Hasil Estimasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --bs-body-bg:#0f1117; --card-bg:#1e2535; --card-border:#2a3348; --input-bg:#111827; --accent:#4f8ef7; }
    body { font-family:'Inter',sans-serif; background:#0f1117; color:#e2e8f0; }
    .navbar-custom { background:#161b27; border-bottom:1px solid #2a3348; }
    .navbar-custom .navbar-brand { font-weight:700; color:#e2e8f0 !important; }
    .navbar-custom .nav-link { color:#94a3b8 !important; font-size:.875rem; }
    .navbar-custom .nav-link:hover,.navbar-custom .nav-link.active { color:#e2e8f0 !important; }
    .navbar-custom .nav-link.active { border-bottom:2px solid var(--accent); }
    .page-header { background:linear-gradient(135deg,#1a2240 0%,#0f1117 100%); border-bottom:1px solid #2a3348; padding:2rem 0; }
    .page-header h1 { font-size:1.6rem; font-weight:700; color:#e2e8f0; margin:0; }
    .page-header p  { color:#94a3b8; margin:.4rem 0 0; font-size:.875rem; }
    .card-custom { background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px; }
    .stat-card { background:#131929; border:1px solid #232d42; border-radius:10px; padding:1.1rem 1.25rem; }
    .stat-card .sc-label { font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.8px; }
    .stat-card .sc-value { font-size:1.8rem; font-weight:700; line-height:1.1; }
    .stat-card .sc-unit  { font-size:.75rem; color:#94a3b8; margin-top:.15rem; }
    .table-dark-custom { --bs-table-bg:#1e2535; --bs-table-striped-bg:#1a2040; --bs-table-hover-bg:#22294a;
      --bs-table-border-color:#2a3348; --bs-table-color:#c4cfde; font-size:.845rem; }
    .table-dark-custom thead th { background:#161b27; color:#7cb3ff; font-weight:600;
      font-size:.78rem; text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid #2a3348; white-space:nowrap; }
    .progress-bar-custom { background:#232d42; border-radius:20px; height:6px; overflow:hidden; }
    .progress-fill { height:100%; border-radius:20px; background:linear-gradient(90deg,#4f8ef7,#34d399); }
    .rank-badge { width:28px; height:28px; border-radius:50%; display:inline-flex;
      align-items:center; justify-content:center; font-weight:700; font-size:.75rem; }
    .rank-1 { background:rgba(251,191,36,.2); color:#fbbf24; border:1px solid rgba(251,191,36,.4); }
    .rank-2 { background:rgba(209,213,219,.15); color:#d1d5db; border:1px solid rgba(209,213,219,.3); }
    .rank-3 { background:rgba(205,127,50,.2); color:#cd7f32; border:1px solid rgba(205,127,50,.4); }
    .rank-n { background:#1e2535; color:#64748b; border:1px solid #2a3348; }
    ::-webkit-scrollbar { width:6px; } ::-webkit-scrollbar-track { background:#0f1117; }
    ::-webkit-scrollbar-thumb { background:#2a3348; border-radius:3px; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="index.php"><i class="bi bi-calculator-fill me-2 text-primary"></i>COCOMO Estimator</a>
    <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <i class="bi bi-list text-light"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto gap-1">
        <li class="nav-item"><a class="nav-link px-3" href="index.php"><i class="bi bi-pencil-square me-1"></i>Form Estimasi</a></li>
        <li class="nav-item"><a class="nav-link active px-3" href="result.php"><i class="bi bi-table me-1"></i>Hasil &amp; Riwayat</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="dataset.php"><i class="bi bi-database me-1"></i>Dataset &amp; Validasi</a></li>
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
    <h1><i class="bi bi-bar-chart-line me-2 text-primary"></i>Hasil &amp; Riwayat Estimasi</h1>
    <p>Riwayat seluruh estimasi effort proyek perangkat lunak menggunakan Intermediate COCOMO + CFPR.</p>
  </div>
</div>

<div class="container-fluid px-4 py-4">

<?php if ($saved && $effort !== null): ?>
<!-- ALERT HASIL TERBARU -->
<div class="alert border-0 mb-4 p-0" style="background:transparent;">
  <div class="card-custom p-4" style="border-color:rgba(79,142,247,.5);background:rgba(79,142,247,.06);">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-check-circle-fill text-success fs-5"></i>
      <span class="fw-semibold" style="color:#e2e8f0;">Estimasi berhasil disimpan!</span>
    </div>
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="stat-card" style="border-color:rgba(79,142,247,.4);">
          <div class="sc-label">EAF</div>
          <div class="sc-value text-primary"><?= $eaf ?></div>
          <div class="sc-unit">Effort Adjustment Factor</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card" style="border-color:rgba(52,211,153,.4);">
          <div class="sc-label">Effort</div>
          <div class="sc-value" style="color:#34d399;"><?= $effort ?></div>
          <div class="sc-unit">Person-Month (PM)</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card" style="border-color:rgba(251,191,36,.4);">
          <div class="sc-label">Waktu Pengembangan</div>
          <div class="sc-value" style="color:#fbbf24;"><?= $time ?></div>
          <div class="sc-unit">Bulan</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card" style="border-color:rgba(167,139,250,.4);">
          <div class="sc-label">Ukuran Tim</div>
          <div class="sc-value" style="color:#a78bfa;"><?= $team ?></div>
          <div class="sc-unit">Orang</div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<!-- RIWAYAT ESTIMASI -->
<div class="card-custom p-4 mb-4">
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-semibold mb-0" style="color:#e2e8f0;">
      <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Estimasi
    </h5>
    <div class="d-flex gap-2">
      <a href="download.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-download me-1"></i>Export CSV
      </a>
      <a href="index.php" class="btn btn-sm" style="background:#4f8ef7;color:#fff;">
        <i class="bi bi-plus-lg me-1"></i>Estimasi Baru
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-dark-custom table-striped table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>#</th><th>Nama Proyek</th><th>LOC</th><th>Tipe</th>
          <th>EAF</th><th>Effort (PM)</th><th>Waktu (bln)</th><th>Tim</th><th>Dibuat</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows): foreach ($rows as $i => $r): ?>
        <tr>
          <td><span class="text-muted"><?= $i+1 ?></span></td>
          <td class="fw-medium" style="color:#e2e8f0;"><?= htmlspecialchars($r['project_name']) ?></td>
          <td><?= number_format((int)$r['loc']) ?></td>
          <td><?= type_badge($r['project_type']) ?></td>
          <td><code style="color:#7cb3ff;"><?= round((float)($r['eaf'] ?? 1), 4) ?></code></td>
          <td><strong style="color:#34d399;"><?= round((float)$r['effort_pm'], 4) ?></strong></td>
          <td><?= round((float)$r['tdev_months'], 4) ?></td>
          <td><?= round((float)$r['team_size'], 4) ?></td>
          <td style="font-size:.78rem;color:#64748b;"><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="9" class="text-center py-5" style="color:#475569;">
          <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
          Belum ada data estimasi.
          <a href="index.php" class="d-block mt-2" style="color:#4f8ef7;">Buat estimasi pertama &rarr;</a>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<!-- CFPR WEIGHT TABLE -->
<div class="card-custom p-4 mb-4">
  <div class="mb-3">
    <h5 class="fw-semibold mb-1" style="color:#e2e8f0;">
      <i class="bi bi-diagram-3 me-2 text-primary"></i>Bobot CFPR – 15 Cost Driver
    </h5>
    <p class="text-muted mb-0" style="font-size:.82rem;">
      Dihitung menggunakan <strong class="text-light">Consistent Fuzzy Preference Relation (CFPR)</strong>
      – Tabel 16, Lestari et al., JUTIF 2025. Bobot lebih tinggi = pengaruh lebih besar terhadap EAF.
    </p>
  </div>
  <div class="table-responsive">
    <table class="table table-dark-custom table-hover align-middle mb-0">
      <thead>
        <tr><th>Rank</th><th>Kode</th><th>Cost Driver</th><th>Atribut</th><th>Bobot</th><th style="min-width:120px;">Proporsi</th></tr>
      </thead>
      <tbody>
        <?php
        $max_w = max(array_column($cfpr_data, 'weight'));
        $group_colors = ['Product'=>'primary','Computer'=>'success','Personnel'=>'purple','Project'=>'warning'];
        $bs_colors    = ['Product'=>'primary','Computer'=>'success','Personnel'=>'info','Project'=>'warning'];
        foreach ($cfpr_data as $idx => $row):
          $rank = $idx + 1;
          $rank_cls = $rank===1?'rank-1':($rank===2?'rank-2':($rank===3?'rank-3':'rank-n'));
          $pct = round(($row['weight'] / $max_w) * 100);
          $bcol = $bs_colors[$row['group']] ?? 'secondary';
        ?>
        <tr>
          <td><span class="rank-badge <?= $rank_cls ?>"><?= $rank ?></span></td>
          <td><code style="color:#7cb3ff;font-size:.82rem;"><?= htmlspecialchars($row['code']) ?></code></td>
          <td style="color:#c4cfde;"><?= htmlspecialchars($row['label']) ?></td>
          <td><span class="badge bg-<?= $bcol ?> bg-opacity-20 text-<?= $bcol ?> border border-<?= $bcol ?> border-opacity-25">
            <?= $row['group'] ?>
          </span></td>
          <td><strong style="color:#e2e8f0;"><?= number_format($row['weight'],4) ?></strong></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress-bar-custom flex-grow-1">
                <div class="progress-fill" style="width:<?= $pct ?>%;"></div>
              </div>
              <span style="font-size:.72rem;color:#64748b;width:35px;"><?= number_format($row['weight']*100,1) ?>%</span>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr style="background:#161b27;">
          <td colspan="4" class="text-end fw-semibold" style="color:#94a3b8;">Total Bobot:</td>
          <td colspan="2"><strong style="color:#34d399;"><?= number_format(array_sum(array_column($cfpr_data,'weight')),4) ?></strong></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- FORMULA REFERENCE -->
<div class="card-custom p-4 mb-5">
  <h5 class="fw-semibold mb-3" style="color:#e2e8f0;">
    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Referensi Formula COCOMO
  </h5>
  <div class="row g-3">
    <?php
    $formulas = [
      ['label'=>'Effort Nominal','formula'=>'E_nominal = a × KLOC^b','desc'=>'KLOC = LOC / 1000'],
      ['label'=>'Effort (PM)','formula'=>'E = E_nominal × EAF','desc'=>'EAF = ∏ multiplier 15 cost driver'],
      ['label'=>'Waktu Pengembangan','formula'=>'T = c × E^d','desc'=>'Dalam satuan bulan'],
      ['label'=>'Ukuran Tim','formula'=>'N = E / T','desc'=>'Dalam satuan orang'],
    ];
    foreach ($formulas as $f): ?>
    <div class="col-md-6 col-lg-3">
      <div class="stat-card">
        <div class="sc-label"><?= $f['label'] ?></div>
        <div style="font-family:monospace;color:#7cb3ff;font-size:.9rem;margin:.4rem 0;"><?= $f['formula'] ?></div>
        <div class="sc-unit"><?= $f['desc'] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="mt-3 p-3 rounded" style="background:#131929;border:1px solid #232d42;">
    <div class="row text-center g-2">
      <div class="col-12 col-sm-4 mb-2 mb-sm-0">
        <div style="font-size:.72rem;color:#64748b;margin-bottom:.2rem;">Organic (Sederhana)</div>
        <code style="color:#7cb3ff;font-size:.78rem;">a=2.4 b=1.05 c=2.5 d=0.38</code>
      </div>
      <div class="col-12 col-sm-4 mb-2 mb-sm-0">
        <div style="font-size:.72rem;color:#64748b;margin-bottom:.2rem;">Semi-detached (Menengah)</div>
        <code style="color:#fbbf24;font-size:.78rem;">a=3.0 b=1.12 c=2.5 d=0.35</code>
      </div>
      <div class="col-12 col-sm-4">
        <div style="font-size:.72rem;color:#64748b;margin-bottom:.2rem;">Embedded (Sulit)</div>
        <code style="color:#f87171;font-size:.78rem;">a=3.6 b=1.20 c=2.5 d=0.32</code>
      </div>
    </div>
  </div>
</div>

</div><!-- end container -->

<footer class="text-center py-3 border-top" style="border-color:#2a3348!important;color:#475569;font-size:.78rem;">
  COCOMO Effort Estimator &nbsp;·&nbsp; Intermediate COCOMO + CFPR &nbsp;·&nbsp;
  Ref: <em>Lestari et al., JUTIF Vol.6 No.6, 2025</em>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
