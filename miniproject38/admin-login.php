<?php
session_start();

// ===== DATABASE CONNECTION =====
$conn = new mysqli(
    "sql208.infinityfree.com",
    "if0_40809278",
    "Sandhya2004",
    "if0_40809278_evspot"
);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

$error = "";

// ===== HANDLE LOGIN =====
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM admins WHERE username=? AND password=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $_SESSION['admin'] = $username;
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $error = "Invalid Username or Password!";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - EV Spot</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f8faff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.login-box {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
    width: 320px;
    text-align: center;
}
.login-box h2 {
    margin-bottom: 20px;
    color: #333;
}
.login-box input {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #bbb;
    border-radius: 5px;
}
.login-box button {
    background: #007bff;
    color: white;
    padding: 10px;
    width: 95%;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.login-box button:hover {
    background: #0056b3;
}
.error {
    color: red;
    margin: 10px 0;
}
</style>
</head>
<body>
<div class="login-box">
    <h2>Admin Login</h2>
    <?php if ($error != "") echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Enter Username" required><br>
        <input type="password" name="password" placeholder="Enter Password" required><br>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
