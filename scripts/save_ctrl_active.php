<?php
// Simulate POST to AdminController::saveCampaign (active)
require_once __DIR__ . '/../app/controllers/AdminController.php';
// disable csrf check used in controller when running via CLI
function csrf_check() { return; }

// emulate POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'title' => 'Controller Test Active',
    'description' => 'Guardada vía AdminController (active)',
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-31',
    'banner_image' => '',
    'is_active' => 1
];

$ctrl = new AdminController();
$ctrl->saveCampaign();

echo "Done\n"; // probably never reached because controller exits after redirect
?>