<?php
session_start();
if ($_SESSION['role'] !== 'staff') {
    header("Location: index.html");
    exit;
}
echo "<h1>Welcome Staff: " . $_SESSION['username'] . "</h1>";
echo "<a href='logout.php'>Logout</a>";
?>
