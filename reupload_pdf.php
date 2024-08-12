<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdf_id = $_POST['pdf_id'];
    
    $pdf_name = uniqid() . '_' . $_FILES['pdf']['name'];
    $pdf_path = 'uploads/' . $pdf_name;
    
    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdf_path)) {
        $stmt = $conn->prepare("UPDATE pdf_submissions SET pdf_filename = ?, status = 'pending', admin_comment = NULL WHERE id = ?");
        $stmt->bind_param("si", $pdf_name, $pdf_id);
        
        if ($stmt->execute()) {
            echo "PDF re-uploaded successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to upload PDF.";
    }
    $conn->close();
}
?>
