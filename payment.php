<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===== Database connection =====
$servername = "sql208.infinityfree.com"; // MySQL Host from InfinityFree
$username = "if0_40809278";             // Your MySQL username from InfinityFree
$password = "Sandhya2004";              // Your MySQL password from InfinityFree
$database = "if0_40809278_evspot";      // Your MySQL database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$bookingData = null;
$error = '';

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmPayment'])) {
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $amount = floatval($_POST['total']);
    $paymentMethod = $conn->real_escape_string($_POST['paymentMethod'] ?? 'Online');
    $cardLast4 = isset($_POST['cardNumber']) ? substr(preg_replace("/\D/", "", $_POST['cardNumber']), -4) : null;
    $upiId = $_POST['upiId'] ?? null;
    $stationName = $conn->real_escape_string($_POST['stationName']);
    $city = $conn->real_escape_string($_POST['city']);
    $chargerType = $conn->real_escape_string($_POST['chargerType']);
    $hours = intval($_POST['hours']);
    $date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO payments (full_name,email,phone,amount,payment_method,card_last4,upi_id,station_name,city,charger_type,hours,payment_date)
            VALUES ('$fullName','$email','$phone','$amount','$paymentMethod','$cardLast4','$upiId','$stationName','$city','$chargerType','$hours','$date')";

    if ($conn->query($sql) === TRUE) {
        header("Location: paymentsuccessful.html");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingData = $_POST;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EVSpot Payment</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body {margin:0;font-family:'Roboto',sans-serif;background:linear-gradient(135deg,#00b4d8,#90e0ef);display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:20px;}
.payment-container{background:#fff;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.15);max-width:500px;width:100%;padding:30px;}
h2{text-align:center;color:#0077b6;margin-bottom:25px;}
.order-summary{background:#caf0f8;padding:20px;border-radius:15px;margin-bottom:25px;color:#023e8a;font-weight:500;}
.order-summary h3{margin-top:0;}
input, select{width:100%;padding:12px;margin-top:8px;margin-bottom:15px;border-radius:10px;border:1.8px solid #0077b6;font-size:1rem;outline:none;box-sizing:border-box;}
input:focus, select:focus{border-color:#00b4d8;}
button{width:100%;padding:14px;border:none;border-radius:12px;background-color:#0077b6;color:white;font-size:1.1rem;font-weight:600;cursor:pointer;transition: all 0.3s ease;}
button:hover{background-color:#023e8a;transform:scale(1.03);}
.error{color:red;text-align:center;margin-bottom:15px;}
.card-fields, .upi-fields{display:none;}
</style>
</head>
<body>
<div class="payment-container">
<h2>💳 Complete Your Payment</h2>

<?php if($error) echo "<p class='error'>$error</p>"; ?>

<?php if($bookingData): ?>
<div class="order-summary">
<h3>Order Summary</h3>
<p><strong>Station:</strong> <?php echo htmlspecialchars($bookingData['stationName']); ?></p>
<p><strong>City:</strong> <?php echo htmlspecialchars($bookingData['city']); ?></p>
<p><strong>Charger Type:</strong> <?php echo htmlspecialchars($bookingData['chargerType']); ?></p>
<p><strong>Rate per Hour:</strong> ₹<?php echo htmlspecialchars($bookingData['rate']); ?></p>
<p><strong>Hours:</strong> <?php echo htmlspecialchars($bookingData['hours']); ?></p>
<p><strong>Total Amount:</strong> ₹<?php echo htmlspecialchars($bookingData['total']); ?></p>
<p><strong>Name:</strong> <?php echo htmlspecialchars($bookingData['fullName']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($bookingData['email']); ?></p>
<p><strong>Phone:</strong> <?php echo htmlspecialchars($bookingData['phone']); ?></p>
</div>

<form method="POST">
<?php foreach($bookingData as $key => $value){
    echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
} ?>
<label for="paymentMethod">Payment Method</label>
<select name="paymentMethod" id="paymentMethod" required>
<option value="">Select</option>
<option value="Credit Card">Credit / Debit Card</option>
<option value="UPI">UPI</option>
</select>

<div class="card-fields">
<label>Card Number</label>
<input type="text" name="cardNumber" placeholder="XXXX XXXX XXXX XXXX">
<label>Expiry</label>
<input type="text" name="expiry" placeholder="MM/YY">
<label>CVV</label>
<input type="text" name="cvv" placeholder="XXX">
</div>

<div class="upi-fields">
<label>UPI ID</label>
<input type="text" name="upiId" placeholder="example@upi">
</div>

<input type="hidden" name="confirmPayment" value="1">
<button type="submit">Pay Now</button>
</form>
<?php else: ?>
<p style="text-align:center; color:#0077b6;">No booking data available.</p>
<?php endif; ?>
</div>

<script>
const paymentMethod = document.getElementById("paymentMethod");
const cardFields = document.querySelector(".card-fields");
const upiFields = document.querySelector(".upi-fields");

paymentMethod.addEventListener("change", ()=>{
if(paymentMethod.value==="Credit Card"){ cardFields.style.display="block"; upiFields.style.display="none"; }
else if(paymentMethod.value==="UPI"){ upiFields.style.display="block"; cardFields.style.display="none"; }
else { cardFields.style.display="none"; upiFields.style.display="none"; }
});
</script>

<!-- ADD THIS SCRIPT TO SAVE BOOKING TO LOCALSTORAGE FOR mybooking.html -->
<script>
<?php if($bookingData): ?>
let bookings = JSON.parse(localStorage.getItem("bookings")) || [];

bookings.push({
    stationName: "<?php echo addslashes($bookingData['stationName']); ?>",
    city: "<?php echo addslashes($bookingData['city']); ?>",
    chargerType: "<?php echo addslashes($bookingData['chargerType']); ?>",
    rate: "<?php echo addslashes($bookingData['rate']); ?>",
    hours: "<?php echo addslashes($bookingData['hours']); ?>",
    total: "<?php echo addslashes($bookingData['total']); ?>",
    fullName: "<?php echo addslashes($bookingData['fullName']); ?>",
    email: "<?php echo addslashes($bookingData['email']); ?>",
    phone: "<?php echo addslashes($bookingData['phone']); ?>",
    date: "<?php echo date('Y-m-d H:i:s'); ?>",
    status: "Paid"
});

localStorage.setItem("bookings", JSON.stringify(bookings));
<?php endif; ?>
</script>

</body>
</html> 