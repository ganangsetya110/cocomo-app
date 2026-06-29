<?php
require_once "db.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="cocomo_estimations_'.date('Ymd_His').'.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

fputcsv($output, [
    'id','project_name','loc','project_type',
    'RELY','DATA','CPLX','TIME','STOR','VIRT','TURN',
    'ACAP','AEXP','PCAP','VEXP','LEXP','MODP','TOOL','SCED',
    'eaf','effort_pm','tdev_months','team_size','created_at',
]);

$sql = "SELECT id, project_name, loc, project_type,
    `RELY - Required Realibility`,`DATA - Database Size`,`CPLX - Product Complexity`,
    `TIME - Execution Time Constraint`,`STOR - Main Storage Constraint`,
    `VIRT - Vrtual MAchine Volatility`,`TURN - Computer Turnaround Time`,
    `ACAP - Analyst Capability`,`AEXP - Application Experience`,
    `PCAP - Programmer Capability`,`VEXP - Virtual Machine Experience`,
    `LEXP - Programming Language Experience`,`MODP - Modern Programming Prectice`,
    `TOOL - Use of Software Tools`,`SCED - Required Development Schedule`,
    eaf, effort_pm, tdev_months, team_size, created_at
    FROM estimations ORDER BY created_at DESC";

try {
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        fputcsv($output, $row);
    }
} catch (PDOException $e) {
    fputcsv($output, ['ERROR', $e->getMessage()]);
}

fclose($output);
exit;
