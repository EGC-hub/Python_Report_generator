<?php
// Database connection settings
$host = 'localhost';
$dbname = 'python_database';
$username = 'root';
$password = '';

// Path to the archive and uploads directories
$uploadsDir = 'reports';
$archiveDir = 'archive';

// Establish a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Automatically delete files older than 30 days
$thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
$sql = "SELECT * FROM deletions WHERE deleted_at < :thirtyDaysAgo";
$stmt = $pdo->prepare($sql);
$stmt->execute([':thirtyDaysAgo' => $thirtyDaysAgo]);
$oldReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($oldReports as $report) {
    $filePath = $archiveDir . '/' . $report['report_name'];
    if (file_exists($filePath)) {
        unlink($filePath); // Delete the file
    }
    // Remove the record from the database
    $deleteSql = "DELETE FROM deletions WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([':id' => $report['id']]);
}

// Handle restore or delete permanently actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $reportId = $_POST['reportId'] ?? '';

    if ($action && $reportId) {
        $sql = "SELECT * FROM deletions WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $reportId]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($report) {
            $filePath = $archiveDir . '/' . $report['report_name'];

            if ($action === 'restore' && file_exists($filePath)) {
                rename($filePath, $uploadsDir . '/' . $report['report_name']); // Restore the file

                // Remove the record from the database
                $deleteSql = "DELETE FROM deletions WHERE id = :id";
                $deleteStmt = $pdo->prepare($deleteSql);
                $deleteStmt->execute([':id' => $reportId]);

                echo "<script>alert('Report restored successfully.'); window.location.href = 'archive.php';</script>";
            } elseif ($action === 'delete' && file_exists($filePath)) {
                unlink($filePath); // Permanently delete the file

                // Remove the record from the database
                $deleteSql = "DELETE FROM deletions WHERE id = :id";
                $deleteStmt = $pdo->prepare($deleteSql);
                $deleteStmt->execute([':id' => $reportId]);

                echo "<script>alert('Report deleted permanently.'); window.location.href = 'archive.php';</script>";
            } else {
                echo "<script>alert('File not found.'); window.location.href = 'archive.php';</script>";
            }
        }
    }
}

// Fetch all archived reports
$sql = "SELECT * FROM deletions ORDER BY deleted_at DESC";
$stmt = $pdo->query($sql);
$archivedReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h1 class="text-center m-4">Archived Reports</h1>
    <div class="container my-5 border border-primary rounded-4 p-5">
        <p class="text-center text-muted">Reports older than 30 days are automatically deleted.</p>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Report Name</th>
                    <th>Deletion Reason</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($archivedReports) > 0) {
                    $counter = 1;
                    foreach ($archivedReports as $report) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>";
                        echo "<td>" . htmlspecialchars($report['report_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($report['reason']) . "</td>";
                        echo "<td>" . $report['deleted_at'] . "</td>";
                        echo "<td>";
                        echo "<form method='POST' class='d-inline'>";
                        echo "<input type='hidden' name='reportId' value='" . $report['id'] . "'>";
                        echo "<button type='submit' name='action' value='restore' class='btn btn-success btn-sm'>Restore</button> ";
                        echo "<button type='submit' name='action' value='delete' class='btn btn-danger btn-sm'>Delete Permanently</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No archived reports found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="text-start mt-3">
            <a href="index.php" class="btn btn-primary">Back to Reports</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>