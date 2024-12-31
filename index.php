<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Additional CSS (if needed) -->
  <style>
    .table-container {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="container mt-4">
    <h1 class="text-center">Report Management</h1>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Report Name</th>
            <th scope="col">Last Modified</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="report-table">
          <!-- Dynamic content populated via fetch_reports.php -->
        </tbody>
      </table>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Delete Report</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="delete-form">
              <div class="mb-3">
                <label for="delete-reason" class="form-label">Reason for Deletion</label>
                <textarea class="form-control" id="delete-reason" rows="3" required></textarea>
              </div>
              <input type="hidden" id="delete-report-name">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      // Fetch reports
      // Fetch reports dynamically
      $.ajax({
        url: "fetch_reports.php",
        method: "GET",
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            const reports = response.data;
            const tableBody = $("#reportTable");
            tableBody.empty(); // Clear any placeholder content

            reports.forEach((report, index) => {
              tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${report.name}</td>
                                <td>${report.date}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-btn" data-name="${report.name}">View</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-name="${report.name}">Delete</button>
                                </td>
                            </tr>`);
            });
          } else {
            alert("Failed to fetch reports: " + response.message);
          }
        },
        error: function () {
          alert("An error occurred while fetching reports.");
        }
      });
    });

    // Open delete modal
    $(document).on('click', '.delete-btn', function () {
      const reportName = $(this).data('name');
      $('#delete-report-name').val(reportName);
      $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirm-delete').on('click', function () {
      const reportName = $('#delete-report-name').val();
      const reason = $('#delete-reason').val();

      if (reason.trim() === '') {
        alert('Please provide a reason for deletion.');
        return;
      }

      $.post('delete_report.php', { report: reportName, reason: reason }, function (response) {
        alert(response);
        $('#deleteModal').modal('hide');
        fetchReports();
      });
    });
    });
  </script>
</body>

</html>