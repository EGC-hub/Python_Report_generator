<?php
// Database connection settings
$host = 'localhost';
$dbname = 'python_database';
$username = 'root';
$password = '';

// Path to the uploads and archive directories
$uploadsDir = 'reports';
$archiveDir = 'archive';

// Ensure the archive directory exists
if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0777, true);
}

// Establish a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportName = $_POST['reportName'] ?? '';
    $deleteReason = $_POST['deleteReason'] ?? '';

    if ($reportName && $deleteReason) {
        $reportPath = $uploadsDir . '/' . $reportName;
        $archivePath = $archiveDir . '/' . $reportName;

        // Check if the file exists
        if (file_exists($reportPath)) {
            // Move the file to the archive folder
            if (rename($reportPath, $archivePath)) {
                // Prepare the SQL query to insert deletion metadata
                $sql = "INSERT INTO deletions (report_name, deleted_at, reason) VALUES (:report_name, :deleted_at, :reason)";
                $stmt = $pdo->prepare($sql);

                // Bind parameters and execute the query
                $stmt->execute([
                    ':report_name' => $reportName,
                    ':deleted_at' => date('Y-m-d H:i:s'),
                    ':reason' => $deleteReason
                ]);

                echo "<script>alert('Report deleted and archived successfully.'); window.location.href = 'index.php';</script>";
            } else {
                echo "<script>alert('Failed to archive the report.'); window.location.href = 'index.php';</script>";
            }
        } else {
            echo "<script>alert('Report not found.'); window.location.href = 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid input.'); window.location.href = 'index.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location.href = 'index.php';</script>";
}
?>