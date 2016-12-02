<?php
ob_start();
session_start();
require_once 'scripts/dbconnect.php';

/*
if (isset($_SESSION['activation_message'])) {

    $msg = $_SESSION['activation_message'];

    // Hide both forms

} else {
*/
    if (isset($_GET['t'])) {
        $token = $_GET['t'];

        $query = "SELECT * FROM verification_table WHERE token='$token'";
        $res = mysqli_query($conn, $query);

        if ($res) {
            $count = mysqli_num_rows($res);

            if ($count == 1) { // What's a good way of saying this?  != 0?  None at all? Token is unique.

                if(($_GET['action']) == "resend") {

                    $subject = "Reminder: Confirm Your ".$appName." Account";
                    $msg =  '<html>Hello '.$firstName.',<br><br>
                        Welcome to '.$appName.'!<br><br>To confirm your account, please click <a href="'.'http://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$token.'"/>here,</a> or copy the following URL into your browser:<br><br>
                        http://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$token.'<br><br>
                        If you\'re not '.$firstName.', please disregard this message.<br><br>Thanks,<br><br>- The '.$appName.' Team</html>';

                    $res = mail($email, $subject, $msg, $headers .= 'From: '.$appName." <".$fromEmail."@".$_SERVER['SERVER_NAME'].">\r\n".'Content-type: text/html; charset=iso-8859-1');

                    if ($res) {
                        $msg = 'Email has been sent to validate your account.';
                    } else {
                        $msg = 'Email couldn\'t be sent!';
                    }

                } else {

                    $userRow = mysqli_fetch_array($res);

                    $email = $userRow['email'];
                    $tokenTime = $userRow['tstamp'];

                    if (time() - $tokenTime < 86400) { // 24 hours
                        $query = "DELETE FROM verification_table WHERE email='$email'"; // Deletes ALL tokens for the current email.  todo should this also delete expired ones, to keep the db clean?
                        $res = mysqli_query($conn, $query);
                        $msg = 'Account <strong>' . $email . '</strong> validated successfully.  You may now <a href="login.php"/>Log In.</a>';


                        //$_SESSION['activation_message'] = $msg;

                    } else {
                        $msg = 'Token has expired.  <a href="http://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$token.'&action=resend>resend verification email</a>';
                    }
                }

            } else {
                $msg = 'Invalid Token - Token does not exist or has expired.';
            }
        }

    } else {
        header("Location: index.php");
    }
// }// todo this depends on if the if statement at the top is uncommented - do we care if they reload during this session.

?>
<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title><?php echo $appName ?> Login</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>

        <?php echo $msg;

        include 'scripts/gtm.php' ?>
    </body>
    </html>
<?php ob_end_flush(); ?>