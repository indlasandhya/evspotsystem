<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
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

$payments = [];
$email = "";
$error = "";

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $sql = "SELECT * FROM payments 
                WHERE email = ? 
                ORDER BY payment_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }

        $stmt->close();

    } else {
        $error = "Please enter a valid email address.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Payment History</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f4f8;
    padding: 20px;
}
h2 {
    text-align: center;
    color: #007acc;
}
form {
    text-align: center;
    margin-bottom: 20px;
}
input[type="email"] {
    padding: 10px;
    width: 260px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
button {
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    background: #007acc;
    color: white;
    cursor: pointer;
}
button:hover {
    background: #005b99;
}
.error {
    color: red;
    text-align: center;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    margin-top: 20px;
}
th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}
th {
    background: #007acc;
    color: white;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
.no-payments {
    text-align: center;
    color: #007acc;
    font-size: 18px;
}
</style>
</head>

<body>

<h2>Booking Payment History</h2>

<form method="post">
    <input type="email" name="email"
           placeholder="Enter your email"
           required
           value="<?= htmlspecialchars($email); ?>">
    <button type="submit">View Payments</button>
    <button type="button" onclick="window.location.href='user.html'">Go Back</button>
</form>

<?php if ($error): ?>
    <p class="error"><?= $error; ?></p>
<?php endif; ?>

<?php if ($_SERVER["REQUEST_METHOD"] === "POST" && !$error): ?>

    <?php if (empty($payments)): ?>
        <p class="no-payments">
            No payments found for <b><?= htmlspecialchars($email); ?></b>
        </p>
    <?php else: ?>

<table>
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Station</th>
    <th>City</th>
    <th>Charger</th>
    <th>Hours</th>
    <th>Amount (₹)</th>
    <th>Method</th>
    <th>Card</th>
    <th>UPI</th>
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
    <td><?= $p['card_last4'] ? "****".$p['card_last4'] : "-"; ?></td>
    <td><?= $p['upi_id'] ?: "-"; ?></td>
    <td>✅ Paid</td>
    <td><?= htmlspecialchars($p['payment_date']); ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>
<?php endif; ?>

</body>
</html>
