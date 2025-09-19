<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = SHA2(?, 256)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect to unified dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Redirect back to login page with error
        header("Location: index.html?error=Invalid username or password");
        exit();
    }
    
    $stmt->close();
} else {
    // If not POST, redirect to login page
    header("Location: index.html");
    exit();
}

$conn->close();
?>
