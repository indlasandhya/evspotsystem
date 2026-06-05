<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// ===== DATABASE CONNECTION =====
$conn = new mysqli(
    "sql208.infinityfree.com",
    "if0_40809278",
    "Sandhya2004",
    "if0_40809278_evspot"
);

// Check connection
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Fetch all waitlist records
$sql = "SELECT id, station_name, charger_type, rate, full_name, email, phone, status, payment_method, card_last4, upi_id, payment_date 
        FROM joinlist 
        ORDER BY payment_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Waitlist Records</title>
<style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
  padding: 12px 15px;
  text-align: center;
}
tbody tr {
  border-bottom: 1px solid #e0e0e0;
}
tbody tr:nth-child(even) {
  background: #f9f9f9;
}
tbody tr:hover {
  background: #e6f2ff;
  transition: 0.3s;
}
.status-paid { color: green; font-weight: bold; }
.status-waiting { color: orange; font-weight: bold; }
button {
  margin-top: 20px;
  padding: 10px 25px;
  border: none;
  border-radius: 8px;
  background: #0077b6;
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
}
button:hover {
  background: #005b99;
}
</style>
</head>
<body>

<div class="container">
<h2>⚡Waitlist Payment History</h2>

<div style="text-align:center;">
<button onclick="window.location.href='admin-dashboard.php'">⬅ Go Back</button>
</div><br>

<?php if($result && $result->num_rows > 0): ?>
<table>
<thead>
<tr>
<th>Station</th>
<th>Charger Type</th>
<th>Rate (₹)</th>
<th>Full Name</th>
<th>Email</th>
<th>Phone</th>
<th>Status</th>
<th>Payment Method</th>
<th>Card Last 4</th>
<th>UPI ID</th>
<th>Payment Date</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row['station_name']); ?></td>
<td><?php echo htmlspecialchars($row['charger_type']); ?></td>
<td><?php echo htmlspecialchars($row['rate']); ?></td>
<td><?php echo htmlspecialchars($row['full_name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['phone']); ?></td>
<td class="<?php echo strtolower($row['status'])=='paid' ? 'status-paid' : 'status-waiting'; ?>">
<?php echo htmlspecialchars($row['status']); ?>
</td>
<td><?php echo htmlspecialchars($row['payment_method']); ?></td>
<td><?php echo $row['card_last4'] ? '****'.$row['card_last4'] : '-'; ?></td>
<td><?php echo $row['upi_id'] ? htmlspecialchars($row['upi_id']) : '-'; ?></td>
<td><?php echo htmlspecialchars($row['payment_date']); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No waitlist records found.</p>
<?php endif; ?>

</div>
</body>
</html>

<?php $conn->close(); ?>
