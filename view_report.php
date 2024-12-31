<?php
// Directory containing generated reports
$reportsDir = 'reports'; // Path to the reports folder

if (!isset($_GET['file'])) {
    echo "No report specified.";
    exit;
}

$fileName = $_GET['file'];
$filePath = $reportsDir . DIRECTORY_SEPARATOR . $fileName;

// Check if the file exists
if (!file_exists($filePath)) {
    echo "The requested report does not exist.";
    exit;
}

// Serve the file for viewing or download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit;
?>