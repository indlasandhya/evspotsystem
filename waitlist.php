<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Join Waitlist | EVSpot System</title>
<style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg,#e0f7fa,#e1f5fe);
  margin: 0; padding: 20px;
  display: flex; justify-content: center; align-items: flex-start; min-height: 100vh;
}
.waitlist-container {
  background: white;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  max-width: 400px;
  width: 100%;
}
h2 { color: #005f73; margin-bottom: 20px; }
label { display: block; margin: 15px 0 6px 0; font-weight: 600; }
input { width: 100%; padding: 10px; border-radius: 10px; border: 1.8px solid #0a9396; font-size: 1rem; outline: none; box-sizing: border-box; transition: border-color 0.3s; }
input:focus { border-color: #007f83; }
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
<div class="waitlist-container">
  <h2>⚡ Join Waitlist</h2>
  <div id="stationDetails"></div>

  <form id="waitlistForm">
    <label for="fullName">Full Name*</label>
    <input type="text" id="fullName" name="fullName" required />

    <label for="email">Email*</label>
    <input type="email" id="email" name="email" required />

    <label for="phone">Phone Number*</label>
    <input type="tel" id="phone" name="phone" required placeholder="10-digit number" />

    <div class="summary" id="queueSummary">Current Queue: 0</div>

    <button type="submit">Join Waitlist</button>
  </form>

  <div id="confirmationMessage" style="display:none; text-align:center; font-weight:bold; color:#007f83;">
    <div id="messageText"></div>
    <div class="confirmation-buttons" id="confirmationButtons" style="display:none;">
      <button id="payNowBtn">Pay Now</button>
      <button class="cancel-btn" id="cancelBtn">Cancel Waitlist</button>
      <button id="goBackBtn" style="display:none; background:#0a9396; color:white;">Go Back</button>
    </div>
  </div>
</div>

<script>
// Dummy stations
const spots = [
   { id:1,name:"Warangal City Center",type:"Fast Charger",rate:30,queue:2 },
  { id:2,name:"Warangal Bus Station",type:"Standard Charger",rate:20,queue:5 },
  { id:3,name:"Hanumakonda Market Hub",type:"Ultra-Fast Charger",rate:40,queue:1 },
  { id:4,name:"Hanumakonda Temple Road",type:"Fast Charger",rate:30,queue:3 },
  { id:5,name:"Hanumakonda Railway Station",type:"Standard Charger",rate:20,queue:4 },
  { id:6,name:"Ameerpet Crossroads",type:"Fast Charger",rate:30,queue:6 },
  { id:7,name:"Miyapur Metro Station",type:"Standard Charger",rate:20,queue:2 },
  { id:8,name:"Nagole Terminal",type:"Ultra-Fast Charger",rate:40,queue:3 },
  { id:9,name:"Paradise Junction",type:"Standard Charger",rate:20,queue:7 },
  { id:10,name:"Jubilee Hills Checkpost",type:"Fast Charger",rate:30,queue:5 },
  { id:11,name:"Habsiguda Circle",type:"Standard Charger",rate:20,queue:2 },
  { id:12,name:"Nalgonda Main Square",type:"Fast Charger",rate:30,queue:1 },
  { id:13,name:"Suryapet Bus Stop",type:"Standard Charger",rate:20,queue:6 },
  { id:14,name:"Khammam City Hub",type:"Ultra-Fast Charger",rate:40,queue:3 },
  { id:15,name:"Mahabubnagar Circle",type:"Fast Charger",rate:30,queue:4 },
  { id:16,name:"Nizamabad Junction",type:"Standard Charger",rate:20,queue:2 },
  { id:17,name:"Hitech City Station",type:"Fast Charger",rate:30,queue:5 },
  { id:18,name:"Secunderabad Junction",type:"Standard Charger",rate:20,queue:3 },
  { id:19,name:"Banjara Hills Plaza",type:"Ultra-Fast Charger",rate:40,queue:4 },
  { id:20,name:"Charminar Spot",type:"Standard Charger",rate:20,queue:7 },
  { id:21,name:"Gachibowli Hub",type:"Fast Charger",rate:30,queue:3 },
  { id:22,name:"Kukatpally Center",type:"Standard Charger",rate:20,queue:2 },
  { id:23,name:"LB Nagar Station",type:"Ultra-Fast Charger",rate:40,queue:4 },
  { id:24,name:"Madhapur Square",type:"Standard Charger",rate:20,queue:3 },
  { id:25,name:"Begumpet Road",type:"Fast Charger",rate:30,queue:5 },
  { id:26,name:"Mehdipatnam Circle",type:"Standard Charger",rate:20,queue:6 },
  { id:27,name:"Suryapet Plaza",type:"Standard Charger",rate:20,queue:3 },
  { id:28,name:"Ramagundam Terminal",type:"Ultra-Fast Charger",rate:40,queue:4 },
  { id:29,name:"Medak Center",type:"Fast Charger",rate:30,queue:2 },
  { id:30,name:"Adilabad Junction",type:"Standard Charger",rate:20,queue:5 }
];

// Get stationId from URL
const urlParams = new URLSearchParams(window.location.search);
const stationId = parseInt(urlParams.get('stationId'));
const station = spots.find(s => s.id === stationId);

const stationDetailsDiv = document.getElementById('stationDetails');
const queueSummary = document.getElementById('queueSummary');
const waitlistForm = document.getElementById('waitlistForm');
const confirmationMessage = document.getElementById('confirmationMessage');
const messageText = document.getElementById('messageText');
const confirmationButtons = document.getElementById('confirmationButtons');
const payNowBtn = document.getElementById('payNowBtn');
const cancelBtn = document.getElementById('cancelBtn');
const goBackBtn = document.getElementById('goBackBtn');

function updateQueueSummary(){
  queueSummary.textContent = `Current Queue: ${station.queue}`;
}

if(station){
  stationDetailsDiv.innerHTML = `<p><strong>Station:</strong> ${station.name}</p><p><strong>Charger Type:</strong> ${station.type}</p><p><strong>Rate:</strong> ₹${station.rate}/hr</p>`;
  updateQueueSummary();
}else{
  stationDetailsDiv.innerHTML = "❌ Station not found!";
  waitlistForm.style.display="none";
}

waitlistForm.addEventListener('submit', e=>{
  e.preventDefault();
  const fullName = document.getElementById("fullName").value.trim();
  const email = document.getElementById("email").value.trim();
  const phone = document.getElementById("phone").value.trim();

  if(!fullName || !email || !/^\d{10}$/.test(phone)){
    alert("Please fill all fields correctly!");
    return;
  }

  // Increase queue
  station.queue += 1;
  updateQueueSummary();

  // Store booking data
  const bookingData = {
    stationId: station.id,
    stationName: station.name,
    chargerType: station.type,
    rate: station.rate,
    status: "Waitlisted",
    fullName, email, phone
  };
  localStorage.setItem("myBooking", JSON.stringify(bookingData));

  // Show confirmation
  messageText.textContent = `✅ Thank you, ${fullName}! You joined the waitlist for ${station.name}.`;
  waitlistForm.style.display="none";
  confirmationMessage.style.display="block";
  confirmationButtons.style.display="flex";
  payNowBtn.style.display="inline-block";
  cancelBtn.style.display="inline-block";
  goBackBtn.style.display="none";
});

// Redirect to joinpayment.php on Pay Now
payNowBtn.addEventListener('click', ()=>{
  const booking = JSON.parse(localStorage.getItem("myBooking"));
  if(!booking) return;

  const form = document.createElement("form");
  form.method = "POST";
  form.action = "joinpayment.php"; // Redirect to joinpayment.php

  for(let key in booking){
    const input = document.createElement("input");
    input.type="hidden";
    input.name=key;
    input.value=booking[key];
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
});

// Cancel -> show Go Back
cancelBtn.addEventListener('click', ()=>{
  if(station.queue>0) station.queue -=1;
  updateQueueSummary();
  messageText.textContent = `❌ You left the waitlist for ${station.name}.`;
  payNowBtn.style.display="none";
  cancelBtn.style.display="none";
  goBackBtn.style.display="inline-block"; // show go back button
  localStorage.removeItem("myBooking");
});

// Go back button action
goBackBtn.addEventListener('click', ()=>{
  window.location.href = "main.html"; // Redirect to your main spot page
});
</script>
</body>
</html>
