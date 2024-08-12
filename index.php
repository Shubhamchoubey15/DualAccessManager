<?php
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        /* Header styling */
        header {
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            display: inline-block;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Main content styling */
        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #007bff;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 10px 0;
        }

        ul li a {
            color: #007bff;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
            display: block;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        ul li a:hover {
            background-color: #e9ecef;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if ($role == 'admin'): ?>
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="review_pdfs.php">Review PDF Submissions</a></li>
                <!-- Add other admin-specific links here -->
            </ul>
        <?php else: ?>
            <h2>User Dashboard</h2>
            <ul>
                <li><a href="submit_pdf.php">Submit a New PDF</a></li>
                <li><a href="user_view_status.php">View Submission Status</a></li>
                <!-- Add other user-specific links here -->
            </ul>
        <?php endif; ?>
    </main>
</body>
© 2024 Shubham. All rights reserved.
This code is proprietary and confidential. Unauthorized copying of this file, via any medium, is strictly prohibited. The software is the proprietary information of Shubham. Use, distribution, or modification is not permitted without explicit permission from the owner.

</html>
