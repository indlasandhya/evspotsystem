<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - EV Spot System</title>
<style>
body { margin:0; font-family:'Segoe UI', sans-serif; background:#f0f4f8;}
header { background:#203859; color:#fff; padding:15px 20px; display:flex; justify-content:space-between; align-items:center;}
header h1 { margin:0; font-size:1.8rem; }
header a { color:#49e86f; text-decoration:none; font-weight:bold;}
.dashboard-links { display:flex; gap:20px; margin:30px 20px;}
.dashboard-links a { padding:15px 25px; background:#203859; color:#fff; text-decoration:none; border-radius:10px; font-weight:bold; transition:0.3s;}
.dashboard-links a:hover { background:#49e86f; color:#203859;}
</style>
</head>
<body>
<header>
<h1>Admin Dashboard</h1>
<a href="logout.html">Logout</a>
</header>

<div class="dashboard-links">
    <a href="history.php">Booking Status</a>
    <a href="payhistory.php">Booking Payment Status</a>
    <a href="waitlistpayment.php">Waitlist Status</a>
    <a href="adminwaitlist.php">Waitlist Payment Status</a>
     <a href="admin_contacts.php">User Contact</a>
</div>

</body>
</html>
