<?php
// Database configuration
$servername = "sql208.infinityfree.com"; // MySQL Host from InfinityFree
$username = "if0_40809278";             // Your MySQL username from InfinityFree
$password = "Sandhya2004";              // Your MySQL password from InfinityFree
$database = "if0_40809278_evspot";      // Your MySQL database name


// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    if (empty($name) || empty($email) || empty($message)) {
        header("Location: contactus.html?error=missing_fields");
        exit();
    }

    // Prepare SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO contactus(name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);
    
    if ($stmt->execute()) {
        // Success - redirect to confirmation page
        header("Location: contactsuccess.html");
    } else {
        // Error - log or display
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: contactus.html");
    exit();
}
?>

