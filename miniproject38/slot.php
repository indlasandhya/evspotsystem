<?php
// ===== PHP: Handle Booking Submission =====

// Create database connection
$conn = new mysqli("sql208.infinityfree.com","if0_40809278","Sandhya2004","if0_40809278_evspot");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitization helper
function sanitize($conn, $data) {
    return $conn->real_escape_string(trim($data));
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize incoming fields
    $stationName = sanitize($conn, $_POST['stationName'] ?? '');
    $city        = sanitize($conn, $_POST['city'] ?? '');
    $chargerType = sanitize($conn, $_POST['chargerType'] ?? '');
    $fullName    = sanitize($conn, $_POST['fullName'] ?? '');
    $email       = sanitize($conn, $_POST['email'] ?? '');
    $phone       = sanitize($conn, $_POST['phone'] ?? '');
    $hours       = intval($_POST['hours'] ?? 1);
    $status      = sanitize($conn, $_POST['status'] ?? 'Confirmed');

    // Determine rate if missing
    $rate = floatval($_POST['rate'] ?? 0);
    if ($rate <= 0) {
        if (strtolower($chargerType) === "fast charger") $rate = 30;
        elseif (strtolower($chargerType) === "standard charger") $rate = 20;
        elseif (strtolower($chargerType) === "ultra-fast charger") $rate = 40;
        else $rate = 25; // default
    }

    // Calculate total
    $total = $rate * $hours;

    // Validate required fields
    if (!$stationName || !$city || !$chargerType || $rate <= 0 || $hours <= 0 || $total <= 0 || !$fullName || !$email || !$phone) {
        echo "<h2 style='color:red'>Invalid booking data. Please go back and try again.</h2>";
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO bookings (stationName, city, chargerType, rate, hours, total, fullName, email, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("sssdidssss", $stationName, $city, $chargerType, $rate, $hours, $total, $fullName, $email, $phone, $status);

    if ($stmt->execute()) {
        echo "<h2 style='color:green'>✅ Booking confirmed successfully for $fullName! Total charge: ₹$total</h2>";
    } else {
        echo "<h2 style='color:red'>Error saving booking: " . htmlspecialchars($stmt->error) . "</h2>";
    }

    $stmt->close();
    $conn->close();
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Book Charging Spot | EVSpot System</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg,#e0f7fa,#e1f5fe);
    margin: 0; padding: 20px;
    display: flex; justify-content: center; align-items: flex-start;
    min-height: 100vh;
}
.booking-container {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    max-width: 400px;
    width: 100%;
}
h2 { color: #005f73; margin-bottom: 20px; }
label { display: block; margin: 15px 0 6px 0; font-weight: 600; }
input, select { width: 100%; padding: 10px; border-radius: 10px; border: 1.8px solid #0a9396; font-size: 1rem; outline: none; box-sizing: border-box; transition: border-color 0.3s; }
input:focus, select:focus { border-color: #007f83; }
button {
    margin-top: 15px;
    padding: 12px 20px;
    width: 100%;
    background-color: #0a9396;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s, transform 0.2s;
}
button:hover { background-color: #007f83; transform: scale(1.05); }
.summary { margin-top: 20px; background: #e0f7fa; padding: 15px; border-radius: 12px; font-weight: 600; color: #004d40; text-align: center; }
</style>
</head>
<body>
<div class="booking-container">
<h2>⚡ Book Charging Spot</h2>

<form id="bookingForm">
    <label for="stationName">Station Name*</label>
    <input type="text" id="stationName" required />

    <label for="city">City*</label>
    <input type="text" id="city" required />

    <label for="chargerType">Charger Type*</label>
    <input type="text" id="chargerType" required />

    <label for="rate">Rate per hour*</label>
    <input type="number" id="rate" value="30" required />

    <label for="hours">Charging Hours*</label>
    <input type="number" id="hours" min="1" value="1" required />

    <label for="fullName">Full Name*</label>
    <input type="text" id="fullName" required />

    <label for="email">Email*</label>
    <input type="email" id="email" required />

    <label for="phone">Phone Number*</label>
    <input type="tel" id="phone" required placeholder="10-digit number" />

    <div class="summary" id="chargeSummary">Estimated Charge: ₹0</div>

    <button type="submit">Confirm Booking & Pay</button>
</form>
</div>

<script>
// Update charge summary
const rateInput = document.getElementById("rate");
const hoursInput = document.getElementById("hours");
const chargeSummary = document.getElementById("chargeSummary");

function updateCharge() {
    const rate = parseFloat(rateInput.value) || 0;
    const hours = parseInt(hoursInput.value) || 1;
    chargeSummary.textContent = `Estimated Charge: ₹${rate*hours}`;
}
rateInput.addEventListener("input", updateCharge);
hoursInput.addEventListener("input", updateCharge);
updateCharge();

// Submit form via POST
document.getElementById("bookingForm").addEventListener("submit", function(e){
    e.preventDefault();

    // Collect form data
    const data = {
        stationName: document.getElementById("stationName").value.trim(),
        city: document.getElementById("city").value.trim(),
        chargerType: document.getElementById("chargerType").value.trim(),
        rate: parseFloat(document.getElementById("rate").value),
        hours: parseInt(document.getElementById("hours").value),
        fullName: document.getElementById("fullName").value.trim(),
        email: document.getElementById("email").value.trim(),
        phone: document.getElementById("phone").value.trim(),
        status: "Confirmed"
    };

    // Basic validation
    if (!data.stationName || !data.city || !data.chargerType || data.rate <= 0 || data.hours <= 0 || !data.fullName || !data.email || !data.phone) {
        alert("Please fill all fields correctly.");
        return;
    }

    // Create a form to POST to PHP
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "slot.php";
    for (const key in data) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
});
</script>
</body>
</html>