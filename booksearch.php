<?php
ob_start();
session_start();
require_once 'scripts/dbconnect.php';
require_once 'scripts/require_login.php';

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>College Books</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    <h1>Hello, <?php echo $userRow['userName']; ?>&nbsp;</h1>
    <a href="logout.php?logout">(log out)</a>
    <br><br>
    Add search field and query results...<br>
    <a href="listnewbook.php">Sell With Us</a>
    etc;
    finalize stuff from datagrip (does that work in standard sql?)


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <?php include 'scripts/gtm.php' ?>
    </body>
    </html>

<?php ob_end_flush(); ?>