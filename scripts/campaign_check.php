<?php
require_once __DIR__ . '/../app/models/Campaign.php';

try {
    $vig = Campaign::findVigentes();
    $exp = Campaign::findExpiradas();
    $all = Campaign::all();
    echo "Vigentes:\n" . json_encode($vig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    echo "Expiradas:\n" . json_encode($exp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    echo "Todas (últimas 10):\n" . json_encode(array_slice($all,0,10), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>