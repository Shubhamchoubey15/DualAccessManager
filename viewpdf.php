<?php
include 'db_connect.php';
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Default filters and sorting
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_date = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'all';
$filter_username = isset($_GET['username']) ? $_GET['username'] : '';
$start_date = '';
$end_date = '';

// Handling date filters
if ($filter_date === 'custom') {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
} else {
    switch ($filter_date) {
        case '1month':
            $start_date = date('Y-m-d', strtotime('-1 month'));
            break;
        case '3months':
            $start_date = date('Y-m-d', strtotime('-3 months'));
            break;
        case '6months':
            $start_date = date('Y-m-d', strtotime('-6 months'));
            break;
        case '1year':
            $start_date = date('Y-m-d', strtotime('-1 year'));
            break;
        default:
            $start_date = '';
            break;
    }
    $end_date = date('Y-m-d'); // Current date
}

// SQL query construction
$sql = "SELECT ps.*, u.username FROM pdf_submissions ps JOIN users u ON ps.user_id = u.id WHERE 1=1";

$params = [];
$types = '';

if ($filter_status !== 'all') {
    $sql .= " AND ps.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_status === 'pending' && $start_date) {
    $sql .= " AND ps.submission_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

if ($filter_username) {
    $sql .= " AND u.username LIKE ?";
    $params[] = '%' . $filter_username . '%';
    $types .= 's';
}

// Prepare and execute the statement
$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View PDFs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form select,
        .filter-form input {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-form input[type="date"] {
            width: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        .action-btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .accept {
            background-color: #28a745;
        }
        .reject {
            background-color: #dc3545;
        }
        .pending {
            background-color: #ffc107;
        }
        .pdf-link {
            color: #007bff;
            text-decoration: none;
        }
        .pdf-link:hover {
            text-decoration: underline;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 900px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>View PDF Submissions</h1>
    </header>

    <div class="container">
        <h2>Filters</h2>
        <form method="GET" class="filter-form">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="all" <?php if ($filter_status === 'all') echo 'selected'; ?>>All</option>
                <option value="pending" <?php if ($filter_status === 'pending') echo 'selected'; ?>>Pending</option>
                <option value="accepted" <?php if ($filter_status === 'accepted') echo 'selected'; ?>>Accepted</option>
                <option value="rejected" <?php if ($filter_status === 'rejected') echo 'selected'; ?>>Rejected</option>
            </select>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($filter_username); ?>">

            <label for="date_filter">Date Filter:</label>
            <select name="date_filter" id="date_filter" onchange="toggleDateInputs(this.value)">
                <option value="all" <?php if ($filter_date === 'all') echo 'selected'; ?>>All</option>
                <option value="1month" <?php if ($filter_date === '1month') echo 'selected'; ?>>Last 1 Month</option>
                <option value="3months" <?php if ($filter_date === '3months') echo 'selected'; ?>>Last 3 Months</option>
                <option value="6months" <?php if ($filter_date === '6months') echo 'selected'; ?>>Last 6 Months</option>
                <option value="1year" <?php if ($filter_date === '1year') echo 'selected'; ?>>Last 1 Year</option>
                <option value="custom" <?php if ($filter_date === 'custom') echo 'selected'; ?>>Custom</option>
            </select>

            <div id="custom-dates" style="<?php echo $filter_date === 'custom' ? '' : 'display:none;'; ?>">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>

            <input type="submit" value="Apply Filters">
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Username</th>
                    <th>PDF</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                    <td>
                        <?php
                        if ($row['status'] === 'accepted') {
                            echo '<span class="status accepted">Accepted</span>';
                        } elseif ($row['status'] === 'rejected') {
                            echo '<span class="status rejected">Rejected</span>';
                        } else {
                            echo '<span class="status pending">Pending</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><a href="#" class="pdf-link" onclick="openPDF('<?php echo htmlspecialchars($row['pdf_filename']); ?>')">View PDF</a></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <a href="accept_pdf.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="action-btn accept">Accept</a>
                            <a href="reject_pdf.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="action-btn reject">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <iframe id="pdfViewer" style="width: 100%; height: 600px;" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        function toggleDateInputs(value) {
            document.getElementById('custom-dates').style.display = value === 'custom' ? '' : 'none';
        }

        function openPDF(pdfFilename) {
            const modal = document.getElementById('pdfModal');
            const pdfViewer = document.getElementById('pdfViewer');
            pdfViewer.src = 'uploads/' + pdfFilename;
            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('pdfModal');
            modal.style.display = 'none';
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('pdfModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

