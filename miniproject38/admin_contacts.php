<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

// ===== DATABASE CONNECTION =====
$conn = new mysqli(
    "sql208.infinityfree.com",
    "if0_40809278",
    "Sandhya2004",
    "if0_40809278_evspot"
);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// ✅ FIX: id remove chesam
$sql = "SELECT name, email, phone, message FROM contactus ORDER BY name DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Contact Messages</title>

<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f0f4f8;
  margin: 0;
  padding: 20px;
}

h2 {
  text-align: center;
  color: #0077b6;
  font-size: 28px;
  margin-bottom: 25px;
}

.container {
  max-width: 1200px;
  margin: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

thead {
  background: #0077b6;
  color: white;
}

th, td {
  padding: 12px;
  text-align: center;
}

tbody tr {
  border-bottom: 1px solid #ddd;
}

tbody tr:nth-child(even) {
  background: #f9f9f9;
}

tbody tr:hover {
  background: #e6f2ff;
}

button {
  margin-top: 20px;
  padding: 10px 25px;
  border: none;
  border-radius: 8px;
  background: #0077b6;
  color: white;
  cursor: pointer;
}

button:hover {
  background: #005b99;
}
</style>
</head>

<body>

<div class="container">
<h2>📩 User Contact Messages</h2>

<div style="text-align:center;">
<button onclick="window.location.href='admin-dashboard.php'">⬅ Go Back</button>
</div><br>

<?php if($result && $result->num_rows > 0): ?>
<table>
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Message</th>
</tr>
</thead>

<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['phone']); ?></td>

<!-- long message handle -->
<td style="max-width:250px; word-wrap:break-word;">
<?php echo htmlspecialchars($row['message']); ?>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php else: ?>
<p style="text-align:center;">No contact messages found.</p>
<?php endif; ?>

</div>

</body>
</html>

<?php $conn->close(); ?>