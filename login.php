<?php
$con = new mysqli("sql208.infinityfree.com","if0_40809278","Sandhya2004","if0_40809278_evspot");

if ($con->connect_error) {
    die("DB Connection Failed");
}

$email = trim($_POST['email']);
$pwd   = trim($_POST['pwd']);

$sql = "SELECT * FROM reg WHERE email='$email' LIMIT 1";
$result = $con->query($sql);

if ($result && $result->num_rows == 1) {

    $row = $result->fetch_assoc();

    if (trim($row['pwd']) === $pwd) {

        echo "<script>
                localStorage.setItem('userEmail', '$email'); // ✅ store email
                window.location='main.html';
              </script>";

    } else {
        echo "<script>alert('Wrong Password'); window.location='login.html';</script>";
    }

} else {
    echo "<script>alert('Email not found'); window.location='login.html';</script>";
}

$con->close();
?>