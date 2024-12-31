<?php
// Directory containing generated reports
$reportsDir = 'reports'; // Change to the path where your reports are stored

if (!is_dir($reportsDir)) {
    echo json_encode(["status" => "error", "message" => "Reports directory does not exist."]);
    exit;
}

$reports = [];
$files = array_diff(scandir($reportsDir), array('.', '..'));

foreach ($files as $file) {
    $filePath = $reportsDir . DIRECTORY_SEPARATOR . $file;
    if (is_file($filePath)) {
        $reports[] = [
            "name" => $file,
            "date" => date("Y-m-d H:i:s", filemtime($filePath))
        ];
    }
}

echo json_encode(["status" => "success", "data" => $reports]);
?>