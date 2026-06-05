<?php
$fname=$_POST['fname'];
$email=$_POST['email'];
$phno=$_POST['phno'];
$vehicle=$_POST['vehicle'];
$addr=$_POST['addr'];
$pwd=$_POST['pwd'];
$con=new mysqli("sql208.infinityfree.com","if0_40809278","Sandhya2004","if0_40809278_evspot");
$sql="insert into reg(fname,email,phno,vehicle,addr,pwd)values('$fname','$email','$phno','$vehicle','$addr','$pwd')";
$res=$con->query($sql);
if($res)
header("location:login.html");
else
echo("not reg")
?> 