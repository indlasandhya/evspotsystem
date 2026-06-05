<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// InfinityFree Database Config
$servername = "sql208.infinityfree.com"; // MySQL Host from InfinityFree
$username = "if0_40809278";             // Your MySQL username from InfinityFree
$password = "Sandhya2004";              // Your MySQL password from InfinityFree
$database = "if0_40809278_evspot";      // Your MySQL database name


$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Save booking when form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stationName = $conn->real_escape_string($_POST['stationName'] ?? '');
    $city        = $conn->real_escape_string($_POST['city'] ?? '');
    $chargerType = $conn->real_escape_string($_POST['chargerType'] ?? '');
    $rate        = floatval($_POST['rate'] ?? 0);
    $hours       = intval($_POST['hours'] ?? 1);
    $total       = $rate * $hours;
    $fullName    = $conn->real_escape_string($_POST['fullName'] ?? '');
    $email       = $conn->real_escape_string($_POST['email'] ?? '');
    $phone       = $conn->real_escape_string($_POST['phone'] ?? '');
    $status      = "Confirmed";
    $booking_date = date("Y-m-d H:i:s");

    if ($stationName && $chargerType && $fullName && $email && $phone) {
        $stmt = $conn->prepare("INSERT INTO bookings (station_name, city, charger_type, rate, hours, total, full_name, email, phone, status, booking_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if($stmt === false){
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sssdidsssss", $stationName, $city, $chargerType, $rate, $hours, $total, $fullName, $email, $phone, $status, $booking_date);
        $stmt->execute();
        if($stmt->error){
            die("Execute failed: " . htmlspecialchars($stmt->error));
        }
        $stmt->close();
        echo "Booking saved successfully.";
    } else {
        echo "Please fill all required fields.";
    }
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
    display: flex; justify-content: center; align-items: flex-start; min-height: 100vh;
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
  .confirmation-buttons { display: flex; gap: 10px; margin-top: 20px; }
  .confirmation-buttons button { flex: 1; }
  .cancel-btn { background: #d9534f; }
  .cancel-btn:hover { background: #c9302c; }
</style>
</head>
<body>
<div class="booking-container">
  <h2>⚡ Book Charging Spot</h2>
  <div id="stationDetails"></div>

  <form id="bookingForm" action="slot.php" method="POST">
    <label for="fullName">Full Name*</label>
    <input type="text" id="fullName" name="fullName" required />

    <label for="email">Email*</label>
    <input type="email" id="email" name="email" required />

    <label for="phone">Phone Number*</label>
    <input type="tel" id="phone" name="phone" required placeholder="10-digit number" />

    <label for="hours">Charging Hours*</label>
    <input type="number" id="hours" name="hours" min="1" max="24" value="1" required />

    <div class="summary" id="chargeSummary">Estimated Charge: ₹0</div>

    <button type="submit">Confirm Booking</button>
  </form>

  <div id="confirmationMessage" style="display:none; margin-top:20px; text-align:center; font-weight:bold; color:#007f83;">
    <div id="messageText"></div>
    <div class="confirmation-buttons" style="display:none;" id="confirmationButtons">
      <button id="payNowBtn">Pay Now</button>
      <button class="cancel-btn" id="cancelBtn">Cancel</button>
    </div>
  </div>
</div>

<script>
// Dummy spots data
const spots = [
{ id:1, name:"Warangal City Center", city:"Warangal", lat:17.9785, lng:79.5890, status:"Available", type:"Fast Charger", rate:30 },
  { id:2, name:"Warangal Bus Station", city:"Warangal", lat:17.9740, lng:79.5935, status:"Available", type:"Standard Charger", rate:20 },
  { id:3, name:"Hanumakonda Market Hub", city:"Hanumakonda", lat:17.9960, lng:79.5850, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:4, name:"Hanumakonda Temple Road", city:"Hanumakonda", lat:17.9930, lng:79.5910, status:"Available", type:"Fast Charger", rate:30 },
  { id:5, name:"Hanumakonda Railway Station", city:"Hanumakonda", lat:17.9905, lng:79.5875, status:"Available", type:"Standard Charger", rate:20 },
  { id:6, name:"Ameerpet Crossroads", city:"Hyderabad", lat:17.4391, lng:78.4483, status:"Available", type:"Fast Charger", rate:30 },
  { id:7, name:"Miyapur Metro Station", city:"Hyderabad", lat:17.4935, lng:78.3768, status:"Available", type:"Standard Charger", rate:20 },
  { id:8, name:"Nagole Terminal", city:"Hyderabad", lat:17.3984, lng:78.5679, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:9, name:"Paradise Junction", city:"Hyderabad", lat:17.3986, lng:78.4828, status:"Available", type:"Standard Charger", rate:20 },
  { id:10, name:"Jubilee Hills Checkpost", city:"Hyderabad", lat:17.4199, lng:78.4024, status:"Available", type:"Fast Charger", rate:30 },
  { id:11, name:"Habsiguda Circle", city:"Hyderabad", lat:17.4092, lng:78.5407, status:"Available", type:"Standard Charger", rate:20 },
  { id:12, name:"Nalgonda Main Square", city:"Nalgonda", lat:17.0565, lng:79.2670, status:"Available", type:"Fast Charger", rate:30 },
  { id:13, name:"Suryapet Bus Stop", city:"Suryapet", lat:17.1315, lng:79.6225, status:"Available", type:"Standard Charger", rate:20 },
  { id:14, name:"Khammam City Hub", city:"Khammam", lat:17.2473, lng:80.1514, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:15, name:"Mahabubnagar Circle", city:"Mahabubnagar", lat:16.7425, lng:78.0014, status:"Available", type:"Fast Charger", rate:30 },
  { id:16, name:"Nizamabad Junction", city:"Nizamabad", lat:18.6731, lng:78.0943, status:"Available", type:"Standard Charger", rate:20 },
  { id:17, name:"Hitech City Station", city:"Hyderabad", lat:17.443464, lng:78.377229, status:"Available", type:"Fast Charger", rate:30 },
  { id:18, name:"Secunderabad Junction", city:"Hyderabad", lat:17.439929, lng:78.498274, status:"Available", type:"Standard Charger", rate:20 },
  { id:19, name:"Banjara Hills Plaza", city:"Hyderabad", lat:17.412348, lng:78.441207, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:20, name:"Charminar Spot", city:"Hyderabad", lat:17.361563, lng:78.474665, status:"Available", type:"Standard Charger", rate:20 },
  { id:21, name:"Gachibowli Hub", city:"Hyderabad", lat:17.443243, lng:78.348930, status:"Available", type:"Fast Charger", rate:30 },
  { id:22, name:"Kukatpally Center", city:"Hyderabad", lat:17.493271, lng:78.391547, status:"Available", type:"Standard Charger", rate:20 },
  { id:23, name:"LB Nagar Station", city:"Hyderabad", lat:17.352446, lng:78.557849, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:24, name:"Madhapur Square", city:"Hyderabad", lat:17.448294, lng:78.391487, status:"Available", type:"Standard Charger", rate:20 },
  { id:25, name:"Begumpet Road", city:"Hyderabad", lat:17.437462, lng:78.448288, status:"Available", type:"Fast Charger", rate:30 },
  { id:26, name:"Mehdipatnam Circle", city:"Hyderabad", lat:17.396602, lng:78.442217, status:"Available", type:"Standard Charger", rate:20 },
  { id:27, name:"Suryapet Plaza", city:"Suryapet", lat:17.1320, lng:79.6225, status:"Available", type:"Standard Charger", rate:20 },
  { id:28, name:"Ramagundam Terminal", city:"Ramagundam", lat:18.7463, lng:79.5766, status:"Available", type:"Ultra-Fast Charger", rate:40 },
  { id:29, name:"Medak Center", city:"Medak", lat:17.9259, lng:78.2721, status:"Available", type:"Fast Charger", rate:30 },
  { id:30, name:"Adilabad Junction", city:"Adilabad", lat:19.6673, lng:78.5314, status:"Available", type:"Standard Charger", rate:20 }


];

// Get stationId from URL
const urlParams = new URLSearchParams(window.location.search);
const stationId = parseInt(urlParams.get("stationId"));

// Elements
const stationDetailsDiv = document.getElementById("stationDetails");
const bookingForm = document.getElementById("bookingForm");
const chargeSummary = document.getElementById("chargeSummary");
const confirmationMessage = document.getElementById("confirmationMessage");
const messageText = document.getElementById("messageText");
const confirmationButtons = document.getElementById("confirmationButtons");

// Find selected station
let selectedStation = spots.find(s => s.id === stationId);

if(!selectedStation){
  stationDetailsDiv.innerHTML = "<p style='color:#d9534f; font-weight:bold;'>Invalid or missing station selection.</p>";
  bookingForm.style.display = "none";
} else if(selectedStation.status !== "Available") {
  stationDetailsDiv.innerHTML = `<p style='color:#d9534f; font-weight:bold;'>Sorry, this station is currently not available.</p>`;
  bookingForm.style.display = "none";
} else {
  stationDetailsDiv.innerHTML = `
    <p><strong>Station:</strong> ${selectedStation.name}</p>
    <p><strong>City:</strong> ${selectedStation.city}</p>
    <p><strong>Charger Type:</strong> ${selectedStation.type}</p>
    <p><strong>Rate:</strong> ₹${selectedStation.rate} / hour</p>
  `;

  function updateCharge() {
    let hours = parseInt(document.getElementById("hours").value) || 1;
    chargeSummary.textContent = `Estimated Charge: ₹${hours * selectedStation.rate}`;
  }

  document.getElementById("hours").addEventListener("input", updateCharge);
  updateCharge();

  bookingForm.addEventListener("submit", e => {
    e.preventDefault();

    const fullName = document.getElementById("fullName").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    let hours = parseInt(document.getElementById("hours").value) || 1;

    if(!fullName || !email || !phone || phone.length !== 10 || isNaN(hours) || hours < 1){
      alert("Please fill all fields correctly.");
      return;
    }

    // Save booking
    const bookingData = {
      stationName: selectedStation.name,
      city: selectedStation.city,
      chargerType: selectedStation.type,
      rate: selectedStation.rate,
      hours: hours,
      total: hours * selectedStation.rate,
      fullName: fullName,
      email: email,
      phone: phone,
      status: "Confirmed",
      date: new Date().toLocaleString()
    };

    let bookings = JSON.parse(localStorage.getItem("bookings")) || [];
    bookings.push(bookingData);
    localStorage.setItem("bookings", JSON.stringify(bookings));

    // Save logged-in email
    localStorage.setItem("loggedInEmail", email);

    messageText.textContent = `✅ Thank you, ${fullName}! Your booking for ${selectedStation.name} is confirmed. Total charge: ₹${hours * selectedStation.rate}`;
    bookingForm.style.display = "none";
    confirmationMessage.style.display = "block";
    confirmationButtons.style.display = "flex";
  });

  // Pay -> payment.php
  document.getElementById("payNowBtn").addEventListener("click", ()=>{
    const bookings = JSON.parse(localStorage.getItem("bookings")) || [];
    const lastBooking = bookings[bookings.length-1];
    if(!lastBooking) return;

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "payment.php";
    for(let key in lastBooking){
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = lastBooking[key];
      form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
  });

  // Cancel -> cancel.php
  document.getElementById("cancelBtn").addEventListener("click", ()=>{
    const bookings = JSON.parse(localStorage.getItem("bookings")) || [];
    const lastBooking = bookings[bookings.length-1];
    if(!lastBooking) return;

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "cancel.html";
    for(let key in lastBooking){
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = lastBooking[key];
      form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
  });
}
</script>
</body>
</html>
