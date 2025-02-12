<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'funkcjonariusz'){
    echo json_encode(['updated'=>false, 'html'=>'']);
    exit;
}
include 'common.php';
$call_sign = $_SESSION['call_sign'];
$db = getDB();
$stmt = $db->prepare("SELECT * FROM interventions WHERE call_sign = ? ORDER BY timestamp DESC");
$stmt->execute([$call_sign]);
$interventions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$html = '';
if(count($interventions) > 0){
    foreach($interventions as $intv){
         $urgency = $intv['urgency'] ? ' (Pilne)' : '';
         $html .= '<div class="card-panel">';
         $html .= '<strong>Typ:</strong> '.htmlspecialchars($intv['type']).'<br>';
         $html .= '<strong>Lokalizacja:</strong> '.htmlspecialchars($intv['location']).'<br>';
         $html .= '<strong>Zgłaszający:</strong> '.htmlspecialchars($intv['reporter']).$urgency;
         $html .= '</div>';
    }
} else {
    $html = 'Brak interwencji.';
}
$last_intv_count = $_SESSION['last_intervention_count'] ?? 0;
$current_count = count($interventions);
$updated = ($current_count != $last_intv_count);
$_SESSION['last_intervention_count'] = $current_count;
echo json_encode(['updated'=>$updated, 'html'=>$html]);
?>
