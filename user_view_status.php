<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        padding: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #007bff;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    a {
        color: #007bff;
        text-decoration: none;
        cursor: pointer;
    }
    a:hover {
        text-decoration: underline;
    }
    form {
        display: inline;
    }
    input[type="submit"] {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #0056b3;
    }
    .back-button {
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 20px;
    }
    .back-button:hover {
        background-color: #5a6268;
    }
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
        height: 80%;
        position: relative;
    }
    .modal-content iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 20px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<?php
include 'db_connect.php';
session_start();

$user_id = $_SESSION['user_id'];
?>

<button class="back-button" onclick="history.back()">Go Back</button>

<?php
$result = $conn->query("SELECT * FROM pdf_submissions WHERE user_id = $user_id ORDER BY submission_date DESC");

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Title</th><th>Category</th><th>PDF</th><th>Status</th><th>Admin Comment</th><th>Status Changed At</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $pdf_filename = htmlspecialchars($row['pdf_filename']);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td><a href='#' onclick=\"openModal('$pdf_filename')\">View PDF</a></td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['admin_comment']) . "</td>";
        echo "<td>" . ($row['status_changed_at'] ? htmlspecialchars($row['status_changed_at']) : 'N/A') . "</td>";
        if ($row['status'] == 'unaccepted') {
            echo "<td>
                <form method='POST' action='reupload_pdf.php' enctype='multipart/form-data'>
                    <input type='hidden' name='pdf_id' value='" . htmlspecialchars($row['id']) . "'>
                    <input type='file' name='pdf' required>
                    <input type='submit' value='Re-upload PDF'>
                </form>
            </td>";
        } else {
            echo "<td>Cannot Edit</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No PDF submissions found.";
}

$conn->close();
?>

<!-- Modal Structure -->
<div id="pdfModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <iframe id="pdfViewer" src=""></iframe>
    </div>
</div>

<script>
    function openModal(pdfFile) {
        document.getElementById('pdfViewer').src = 'uploads/' + pdfFile;
        document.getElementById('pdfModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('pdfViewer').src = '';
        document.getElementById('pdfModal').style.display = 'none';
    }
</script>
