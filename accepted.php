<?php
include 'db_connect.php';
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch accepted PDFs
$stmt = $conn->prepare("SELECT pdf_submissions.*, users.username FROM pdf_submissions JOIN users ON pdf_submissions.user_id = users.id WHERE status = 'accepted' ORDER BY submission_date DESC");
$stmt->execute();
$result = $stmt->get_result();
$pdf_list = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted PDF Submissions</title>
    <style>
        /* Include your CSS here */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        header { background-color: #007bff; color: #fff; padding: 20px; text-align: center; }
        nav a { color: #fff; text-decoration: none; font-weight: bold; margin: 0 10px; }
        nav a:hover { text-decoration: underline; }
        main { max-width: 900px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
        iframe { width: 100%; height: 600px; }
    </style>
</head>
<body>
    <header>
        <h1>Accepted PDF Submissions</h1>
        <nav>
            <a href="review_pdfs.php">Pending</a>
            <a href="rejected.php">Rejected</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Accepted PDFs</h2>
        <?php if (!empty($pdf_list)): ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>PDF</th>
                    <th>Submission Date</th>
                    <th>Status Changed At</th>
                </tr>
                <?php foreach ($pdf_list as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><a href="#" class="view-pdf" data-pdf="uploads/<?php echo htmlspecialchars($row['pdf_filename']); ?>">View PDF</a></td>
                        <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                        <td><?php echo $row['status_changed_at'] ? htmlspecialchars($row['status_changed_at']) : 'Not Updated'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No accepted PDFs found.</p>
        <?php endif; ?>

        <!-- Modal for viewing PDFs -->
        <div id="pdfModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <iframe id="pdfFrame" src=""></iframe>
            </div>
        </div>
    </main>

    <script>
        var modal = document.getElementById("pdfModal");
        var pdfFrame = document.getElementById("pdfFrame");
        var viewPdfLinks = document.querySelectorAll(".view-pdf");
        var span = document.getElementsByClassName("close")[0];

        viewPdfLinks.forEach(function(link) {
            link.onclick = function(e) {
                e.preventDefault();
                var pdfSrc = this.getAttribute("data-pdf");
                document.getElementById("pdfFrame").src = pdfSrc;
                modal.style.display = "block";
            };
        });

        span.onclick = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
