<?php
error_reporting(E_ALL);

$appName = "LCM Books";  // TODO use this to support multiple campuses TODO what is this doing here?
$appIndex = 0;

define('db_host', $_SERVER["DBHOST"]);
define('db_user', $_SERVER["DBUSER"]);
define('db_pass', $_SERVER["DBPASS"]);
define('db_name', $_SERVER["DBNAME"]);

$conn = mysqli_connect(db_host,db_user,db_pass);
$dbcon = mysqli_select_db($conn, db_name);

if ( !$conn ) {
    die("Connection failed : " . mysqli_error($conn));
}

if ( !$dbcon ) {
    die("Database Connection failed : " . mysqli_error($conn));
}
