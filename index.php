<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <h1 class="text-center m-4">Generated Reports</h1>
  <div class="container my-5 border border-primary rounded-4 p-5">
    <!-- Table for displaying reports -->
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Report Name</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Path to the uploads folder
        $uploadsDir = 'reports';

        // Check if the folder exists
        if (is_dir($uploadsDir)) {
          // Get all files in the folder
          $files = array_diff(scandir($uploadsDir), ['.', '..']);

          // Counter for numbering the rows
          $counter = 1;

          // Loop through files and display them in the table
          foreach ($files as $file) {
            $filePath = $uploadsDir . '/' . $file;
            $fileDate = date("Y-m-d H:i:s", filemtime($filePath));

            echo "<tr>";
            echo "<td>" . $counter++ . "</td>";
            echo "<td>" . htmlspecialchars($file) . "</td>";
            echo "<td>" . $fileDate . "</td>";
            echo "<td>";
            echo "<a href='" . $filePath . "' target='_blank' class='btn btn-primary btn-sm'>View</a> ";
            echo "<button class='btn btn-danger btn-sm' onclick=\"showDeleteModal('" . htmlspecialchars($file) . "')\">Delete</button>";
            echo "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4' class='text-center'>No reports found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <div class="text-start mt-3">
      <a href="archive.php" class="btn btn-primary">View Archived Reports</a>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Delete Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="delete_report.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="reportName" id="reportName">
            <div class="mb-3">
              <label for="deleteReason" class="form-label">Reason for deletion:</label>
              <textarea class="form-control" id="deleteReason" name="deleteReason" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS for Modal Interaction -->
  <script>
    function showDeleteModal(reportName) {
      document.getElementById('reportName').value = reportName;
      var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
      deleteModal.show();
    }
  </script>
</body>

</html>