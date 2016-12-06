<?php

// if session is not set this will redirect to home page
if( !isset($_SESSION['user']) ) {
    // TODO set session variable of destination URI when redirecting to login page
    header("Location: login.php");
    exit;
}
// load logged in user's details
$res=mysqli_query($conn, "SELECT * FROM users WHERE userId=".$_SESSION['user']);
$userRow=mysqli_fetch_array($res);