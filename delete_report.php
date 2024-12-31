<?php
session_start(); // Start session to manage admin login (placeholder)

// Check if admin is logged in
if (!isset($_SESSION['admin_name'])) {
    echo json_encode(["status" => "error", "message" => "You are not logged in as admin."]);
    exit;
}

$reportsDir = 'reports';
$archiveDir = 'archive';
if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0777, true); // Create archive directory if not exists
}

// Validate input
if (!isset($_POST['file']) || !isset($_POST['reason'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
    exit;
}

$fileName = $_POST['file'];
$reason = $_POST['reason'];
$timestamp = date("Y-m-d H:i:s");
$adminName = $_SESSION['admin_name']; // Placeholder for admin name

$filePath = $reportsDir . DIRECTORY_SEPARATOR . $fileName;
$archivePath = $archiveDir . DIRECTORY_SEPARATOR . $fileName;

// Check if file exists
if (!file_exists($filePath)) {
    echo json_encode(["status" => "error", "message" => "File not found."]);
    exit;
}

// Archive the file and log metadata
if (rename($filePath, $archivePath)) {
    // Prepare log entry
    $logEntry = [
        "file" => $fileName,
        "admin" => $adminName,
        "timestamp" => $timestamp,
        "reason" => $reason
    ];

    // Log file path
    $logFilePath = $archiveDir . DIRECTORY_SEPARATOR . "log.json";

    // Read existing log data
    $existingLogs = [];
    if (file_exists($logFilePath)) {
        $existingLogs = json_decode(file_get_contents($logFilePath), true);
    }

    // Append new log entry
    $existingLogs[] = $logEntry;

    // Write updated log data back to the file
    file_put_contents($logFilePath, json_encode($existingLogs, JSON_PRETTY_PRINT));

    echo json_encode(["status" => "success", "message" => "File archived successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to archive file."]);
}
?>