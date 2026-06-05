<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// Corrected DB connection variable
$conn = new mysqli("sql208.infinityfree.com","if0_40809278","Sandhya2004","if0_40809278_evspot");

if($conn->connect_error){
    die("DB Connection failed: " . $conn->connect_error);
}

$payments = [];
$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM joinlist WHERE email = ? ORDER BY payment_date DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🔍 View Join Waitlist Payments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
form {
    text-align: center;
    margin-bottom: 30px;
}
input[type="email"] {
    padding: 10px;
    width: 250px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 16px;
}
button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    background-color: #007acc;
    color: white;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}
button:hover {
    background-color: #005b99;
}
.error {
    color: red;
    text-align: center;
    margin-bottom: 20px;
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

<h2>Join Waitlist Payments</h2>

<form method="post">
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <input type="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($email) ?>">
    <button type="submit">View My Payments</button>
    <button onclick="window.location.href='user.html'; return false;">Go back</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?>
    <?php if (empty($payments)): ?>
        <p class="no-payments">No payments found for <strong><?= htmlspecialchars($email) ?></strong>.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Station</th>
                    <th>Charger Type</th>
                    <th>Amount (₹)</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Payment Method</th>
                    <th>Card Last 4</th>
                    <th>UPI ID</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['station_name']) ?></td>
                        <td><?= htmlspecialchars($p['charger_type']) ?></td>
                        <td><?= htmlspecialchars($p['rate']) ?></td>
                        <td><?= htmlspecialchars($p['full_name']) ?></td>
                        <td><?= htmlspecialchars($p['phone']) ?></td>
                        <td><?= htmlspecialchars($p['payment_method']) ?></td>
                        <td><?= $p['card_last4'] ? '**** ' . htmlspecialchars($p['card_last4']) : '-' ?></td>
                        <td><?= $p['upi_id'] ?: '-' ?></td>
                        <td><?= htmlspecialchars($p['status']) ?></td>
                        <td><?= htmlspecialchars($p['payment_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
