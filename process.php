<?php
/**
 * process.php – COCOMO Intermediate Effort Estimation
 */
require_once "db.php";

// ── Validasi input ──
$project_name = trim($_POST['project_name'] ?? '');
$loc          = isset($_POST['loc']) ? (int)$_POST['loc'] : 0;
$project_type = trim($_POST['project_type'] ?? '');

if ($project_name === '' || $loc < 1000 || !in_array($project_type, ['sederhana','menengah','sulit'], true)) {
    header('Location: index.php?error=invalid&msg='.urlencode('LOC minimal 1.000 baris kode.'));
    exit;
}

$valid_ratings = ['vl','l','n','h','vh','xh'];
$driver_codes  = ['rely','data','cplx','time','stor','virt','turn','acap','aexp','pcap','vexp','lexp','modp','tool','sced'];
$ratings = [];
foreach ($driver_codes as $code) {
    $val = strtolower(trim($_POST[$code] ?? 'n'));
    $ratings[$code] = in_array($val, $valid_ratings, true) ? $val : 'n';
}

// ── Multiplier table ──
$rating_multipliers = [
    'RELY'=>['vl'=>0.75,'l'=>0.88,'n'=>1.00,'h'=>1.15,'vh'=>1.40,'xh'=>null],
    'DATA'=>['vl'=>null,'l'=>0.94,'n'=>1.00,'h'=>1.08,'vh'=>1.16,'xh'=>null],
    'CPLX'=>['vl'=>0.70,'l'=>0.85,'n'=>1.00,'h'=>1.15,'vh'=>1.30,'xh'=>1.65],
    'TIME'=>['vl'=>null,'l'=>null, 'n'=>1.00,'h'=>1.11,'vh'=>1.30,'xh'=>1.66],
    'STOR'=>['vl'=>null,'l'=>null, 'n'=>1.00,'h'=>1.06,'vh'=>1.21,'xh'=>1.56],
    'VIRT'=>['vl'=>null,'l'=>0.87, 'n'=>1.00,'h'=>1.15,'vh'=>1.30,'xh'=>null],
    'TURN'=>['vl'=>null,'l'=>0.87, 'n'=>1.00,'h'=>1.07,'vh'=>1.15,'xh'=>null],
    'ACAP'=>['vl'=>1.46,'l'=>1.19, 'n'=>1.00,'h'=>0.86,'vh'=>0.71,'xh'=>null],
    'AEXP'=>['vl'=>1.29,'l'=>1.13, 'n'=>1.00,'h'=>0.91,'vh'=>0.82,'xh'=>null],
    'PCAP'=>['vl'=>1.42,'l'=>1.17, 'n'=>1.00,'h'=>0.86,'vh'=>0.70,'xh'=>null],
    'VEXP'=>['vl'=>1.21,'l'=>1.10, 'n'=>1.00,'h'=>0.90,'vh'=>null,'xh'=>null],
    'LEXP'=>['vl'=>1.14,'l'=>1.07, 'n'=>1.00,'h'=>0.95,'vh'=>null,'xh'=>null],
    'MODP'=>['vl'=>1.24,'l'=>1.10, 'n'=>1.00,'h'=>0.91,'vh'=>0.82,'xh'=>null],
    'TOOL'=>['vl'=>1.24,'l'=>1.10, 'n'=>1.00,'h'=>0.91,'vh'=>0.83,'xh'=>null],
    'SCED'=>['vl'=>1.23,'l'=>1.08, 'n'=>1.00,'h'=>1.04,'vh'=>1.10,'xh'=>null],
];

// ── Hitung EAF ──
$eaf = 1.0;
foreach ($driver_codes as $code) {
    $UPPER   = strtoupper($code);
    $raw_val = $rating_multipliers[$UPPER][$ratings[$code]];
    $eaf    *= ($raw_val !== null) ? (float)$raw_val : 1.0;
}

// ── COCOMO constants + hitung ──
$constants = [
    'sederhana'=>['a'=>2.4,'b'=>1.05,'c'=>2.5,'d'=>0.38],
    'menengah' =>['a'=>3.0,'b'=>1.12,'c'=>2.5,'d'=>0.35],
    'sulit'    =>['a'=>3.6,'b'=>1.20,'c'=>2.5,'d'=>0.32],
];
$cst         = $constants[$project_type];
$kloc        = $loc / 1000.0;
$effort_pm   = $cst['a'] * pow($kloc, $cst['b']) * $eaf;
$tdev_months = $cst['c'] * pow($effort_pm, $cst['d']);
$team_size   = $effort_pm / $tdev_months;

// ── Mapping kolom DB ──
$db_col_map = [
    'RELY'=>'RELY - Required Realibility','DATA'=>'DATA - Database Size',
    'CPLX'=>'CPLX - Product Complexity','TIME'=>'TIME - Execution Time Constraint',
    'STOR'=>'STOR - Main Storage Constraint','VIRT'=>'VIRT - Vrtual MAchine Volatility',
    'TURN'=>'TURN - Computer Turnaround Time','ACAP'=>'ACAP - Analyst Capability',
    'AEXP'=>'AEXP - Application Experience','PCAP'=>'PCAP - Programmer Capability',
    'VEXP'=>'VEXP - Virtual Machine Experience','LEXP'=>'LEXP - Programming Language Experience',
    'MODP'=>'MODP - Modern Programming Prectice','TOOL'=>'TOOL - Use of Software Tools',
    'SCED'=>'SCED - Required Development Schedule',
];

$col_parts = $param_parts = [];
$params = [
    ':project_name'=>htmlspecialchars($project_name, ENT_QUOTES, 'UTF-8'),
    ':loc'=>$loc, ':project_type'=>$project_type,
    ':eaf'=>round($eaf,4), ':effort_pm'=>round($effort_pm,4),
    ':tdev_months'=>round($tdev_months,4), ':team_size'=>round($team_size,4),
];
foreach ($db_col_map as $UPPER => $col_name) {
    $pk = ':'.strtolower($UPPER);
    $col_parts[]  = '`'.$col_name.'`';
    $param_parts[] = $pk;
    $params[$pk]  = $ratings[strtolower($UPPER)];
}

$sql = "INSERT INTO estimations (project_name,loc,project_type,".implode(',',$col_parts).",eaf,effort_pm,tdev_months,team_size)
        VALUES (:project_name,:loc,:project_type,".implode(',',$param_parts).",:eaf,:effort_pm,:tdev_months,:team_size)";

try {
    $pdo->prepare($sql)->execute($params);
} catch (PDOException $e) {
    die('<div class="container mt-5"><div class="alert alert-danger">DB Error: '.htmlspecialchars($e->getMessage()).'</div></div>');
}

header('Location: result.php?saved=1&effort='.round($effort_pm,4).'&time='.round($tdev_months,4).'&team='.round($team_size,4).'&eaf='.round($eaf,4));
exit;
