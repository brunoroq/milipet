<?php
// Simulate POST to AdminController::saveCampaign (inactive)
require_once __DIR__ . '/../app/controllers/AdminController.php';
function csrf_check() { return; }

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'title' => 'Controller Test Inactive',
    'description' => 'Guardada vía AdminController (inactive)',
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-31',
    'banner_image' => '',
    // Not setting is_active simulates unchecked checkbox
];

$ctrl = new AdminController();
$ctrl->saveCampaign();

echo "Done\n";
?>