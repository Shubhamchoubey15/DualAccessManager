<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"],
        select,
        textarea {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .back-button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .success {
            color: #28a745;
            text-align: center;
        }
        .error {
            color: #dc3545;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="history.back()">Go Back</button>
        <h1>Submit PDF</h1>
        <form method="POST" action="submit_pdf.php" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="RFIA">RFIA</option>
                <option value="Category2">Category2</option>
                <option value="Category3">Category3</option>
                <option value="Category4">Category4</option>
            </select>
            
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment"></textarea>
            
            <label for="pdf">Upload PDF:</label>
            <input type="file" id="pdf" name="pdf" required>
            
            <input type="submit" value="Submit PDF">
        </form>
    </div>
</body>
</html>
