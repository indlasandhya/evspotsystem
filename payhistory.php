<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===== Database connection =====
$servername = "sql208.infinityfree.com";  // InfinityFree MySQL host
$username = "if0_40809278";               // Your MySQL username
$password = "Sandhya2004";                // Your MySQL password
$database = "if0_40809278_evspot";        // Your database name 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// ===== Fetch all payments =====
$sql = "SELECT * FROM payments ORDER BY payment_date DESC";
$result = $conn->query($sql);

$payments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments History</title>
<style>
body {
    font-family: 'Arial', sans-serif;
    background: #f0f4f8;
    margin: 0;
    padding: 20px;
}
h2 {
    text-align: center;
    color: #007acc;
    margin-bottom: 20px;
}
button.go-back {
    display: block;
    margin: 0 auto 20px auto;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    background-color: #007acc;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}
button.go-back:hover {
    background-color: #005b99;
    transform: scale(1.05);
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
thead {
    background: #007acc;
    color: white;
}
thead th {
    padding: 12px;
    text-align: left;
}
tbody td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    color: #333;
}
tbody tr:nth-child(even) {
    background: #f9f9f9;
}
tbody tr:hover {
    background: #e6f2ff;
}
.no-payments {
    text-align: center;
    color: #007acc;
    font-size: 18px;
    margin-top: 20px;
}
</style>
</head>
<body>
<h2>💰 Booking Payments History </h2>

<button class="go-back" onclick="window.location.href='admin-dashboard.php'">⬅ Go Back</button>

<?php if (empty($payments)) : ?>
    <p class="no-payments">No payments found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Station</th>
                <th>City</th>
                <th>Charger Type</th>
                <th>Hours</th>
                <th>Amount (₹)</th>
                <th>Method</th>
                <th>Card Last4</th>
                <th>UPI ID</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['full_name']); ?></td>
                <td><?= htmlspecialchars($p['email']); ?></td>
                <td><?= htmlspecialchars($p['phone']); ?></td>
                <td><?= htmlspecialchars($p['station_name']); ?></td>
                <td><?= htmlspecialchars($p['city']); ?></td>
                <td><?= htmlspecialchars($p['charger_type']); ?></td>
                <td><?= htmlspecialchars($p['hours']); ?></td>
                <td><?= htmlspecialchars($p['amount']); ?></td>
                <td><?= htmlspecialchars($p['payment_method']); ?></td>
                <td><?= $p['card_last4'] ? "**** ".$p['card_last4'] : "-"; ?></td>
                <td><?= $p['upi_id'] ?: "-"; ?></td>
                <td class="status-paid">✅Paid</td>
                <td><?= htmlspecialchars($p['payment_date']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html> 