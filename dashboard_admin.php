<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit;
}
echo "<h1>Welcome Admin: " . $_SESSION['username'] . "</h1>";
echo "<a href='logout.php'>Logout</a>";
?>
