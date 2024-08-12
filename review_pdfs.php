<?php
include 'db_connect.php';
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submissions to update PDF status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdf_id = $_POST['pdf_id'];
    $status = $_POST['status'];
    $admin_comment = $_POST['admin_comment'];

    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE pdf_submissions SET status = ?, admin_comment = ?, status_changed_at = ?, admin_comment_updated_at = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $status, $admin_comment, $now, $now, $pdf_id);

    if ($stmt->execute()) {
        $message = "PDF status updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch pending PDFs
$stmt = $conn->prepare("SELECT pdf_submissions.*, users.username FROM pdf_submissions JOIN users ON pdf_submissions.user_id = users.id WHERE status = 'pending' ORDER BY submission_date DESC");
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
    <title>Pending PDF Submissions</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        header { background-color: #007bff; color: #fff; padding: 20px; text-align: center; }
        nav a { color: #fff; text-decoration: none; font-weight: bold; margin: 0 10px; }
        nav a:hover { text-decoration: underline; }
        main { max-width: 900px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        .message { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
        iframe { width: 100%; height: 600px; }
    </style>
</head>
<body>
    <header>
        <h1>Pending PDF Submissions</h1>
        <nav>
            <a href="accepted.php">Accepted</a>
            <a href="rejected.php">Rejected</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h2>Pending PDFs</h2>
        <?php if (!empty($pdf_list)): ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Submission Date</th>
                    <th>Status Change Date</th>
                    <th>PDF</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($pdf_list as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status_changed_at']); ?></td>
                        <td><a href="#" class="view-pdf" data-pdf="uploads/<?php echo htmlspecialchars($row['pdf_filename']); ?>">View PDF</a></td>
                        <td>
                            <form method="POST" action="pending.php">
                                <input type="hidden" name="pdf_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <select name="status">
                                    <option value="accepted">Accept</option>
                                    <option value="unaccepted">Reject</option>
                                </select>
                                <textarea name="admin_comment" placeholder="Admin Comment"></textarea>
                                <input type="submit" value="Update Status">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No pending PDFs found.</p>
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
