<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Bank Management System - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/dashboard.js"></script>
</head>
<body>
<header>
    <div class="logo">Blood Bank Management</div>
    <div class="header-controls">
        <input type="text" placeholder="Search by Unit No">
        <input type="text" placeholder="Search by Segment No">
        <button class="tag-btn">Unit Tag</button>
        <button class="tag-btn">BBR Tag</button>
    </div>
</header>

<div class="main-container" style="display:flex;">
    <aside class="sidebar">
        <div class="dropdown">
            <a href="#" class="menu-item">Donors</a>
            <div class="dropdown-content">
                <a href="#" onclick="loadContent('add_donor.php')">Add Donor</a>
                <a href="#" onclick="loadContent('view_donors.php')">View Donors</a>
                <a href="#" onclick="loadContent('donor_history.php')">Donor History</a>
                <a href="#" onclick="loadContent('search_donor.php')">Search Donor</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="menu-item">Blood Bank</a>
            <div class="dropdown-content">
                <a href="#" onclick="loadContent('add_stock.php')">Add Stock</a>
                <a href="#" onclick="loadContent('view_inventory.php')">View Inventory</a>
                <a href="#" onclick="loadContent('expiry_list.php')">Expiry List</a>
                <a href="#" onclick="loadContent('search_blood.php')">Search Blood</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="menu-item">Requests</a>
            <div class="dropdown-content">
                <a href="#" onclick="loadContent('new_request.php')">New Request</a>
                <a href="#" onclick="loadContent('view_requests.php')">View Requests</a>
                <a href="#" onclick="loadContent('pending_requests.php')">Pending Requests</a>
                <a href="#" onclick="loadContent('completed_requests.php')">Completed Requests</a>
            </div>
        </div>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="dropdown">
            <a href="#" class="menu-item">Staff Management</a>
            <div class="dropdown-content">
                <a href="#" onclick="loadContent('add_staff.php')">Add Staff</a>
                <a href="#" onclick="loadContent('view_staff.php')">View Staff</a>
                <a href="#" onclick="loadContent('edit_staff.php')">Edit Staff</a>
                <a href="#" onclick="loadContent('remove_staff.php')">Remove Staff</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="menu-item">Reports</a>
            <div class="dropdown-content">
                <a href="#" onclick="loadContent('daily_report.php')">Daily Report</a>
                <a href="#" onclick="loadContent('monthly_report.php')">Monthly Report</a>
                <a href="#" onclick="loadContent('yearly_report.php')">Yearly Report</a>
                <a href="#" onclick="loadContent('custom_report.php')">Custom Report</a>
            </div>
        </div>
        <?php endif; ?>
        <a href="logout.php" class="menu-item">Logout</a>
    </aside>

    <section class="content" style="padding:20px;">
        <div id="dashboard-content">
            <h2>Welcome to Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <p>You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
            <div style="margin-top: 20px;">
                <h3>Quick Actions</h3>
                <p>This is your dashboard where you can manage all blood bank operations.</p>
            </div>
        </div>
        <div id="loading-spinner" style="display: none; text-align: center; padding: 50px;">
            <div style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 0 auto;"></div>
            <p>Loading...</p>
        </div>
    </section>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Blood Bank Management. All rights reserved by Deploy-X.</p>
</footer>
</body>
</html>
