<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// ===== Database Connection =====
$servername = "sql208.infinityfree.com"; // MySQL Host from InfinityFree
$username = "if0_40809278";             // Your MySQL username from InfinityFree
$password = "Sandhya2004";              // Your MySQL password from InfinityFree
$database = "if0_40809278_evspot";      // Your MySQL database name


$conn = new mysqli($servername, $username, $password, $database);
if($conn->connect_error){
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
  transition: all 0.3s ease;
}
button:hover {
  background: #005b99;
  transform: scale(1.05);
}
@media screen and (max-width: 768px) {
  table, thead, tbody, th, td, tr { display: block; }
  thead tr { display: none; }
  tbody tr {
    margin-bottom: 15px;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    padding: 10px;
  }
  td {
    text-align: right;
    padding: 10px;
    position: relative;
  }
  td::before {
    content: attr(data-label);
    position: absolute;
    left: 10px;
    width: 50%;
    text-align: left;
    font-weight: 600;
  }
}
</style>
</head>
<body>
<div class="container">
  <h2>⚡Waitlist Records</h2>
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
        
        
        <th>Payment Date</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        
        <td data-label="Station"><?php echo htmlspecialchars($row['station_name']); ?></td>
        <td data-label="Charger Type"><?php echo htmlspecialchars($row['charger_type']); ?></td>
        <td data-label="Rate (₹)"><?php echo htmlspecialchars($row['rate']); ?></td>
        <td data-label="Full Name"><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
        <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
        
          
        </td>
        
        <td data-label="Payment Date"><?php echo htmlspecialchars($row['payment_date']); ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p style="text-align:center; color:#555; margin-top:20px;">No waitlist records found.</p>
  <?php endif; ?>

 
</div>
</body>
</html>
<?php $conn->close(); ?>
