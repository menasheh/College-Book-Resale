<?php
ob_start();
session_start();
require_once 'scripts/dbconnect.php';

// If you're already logged in, redirect to the main app
if ( isset($_SESSION['user'])!="" ) {
    header("Location: booksearch.php");
    exit;
}

$error = false;

if( isset($_POST['btn-login']) ) {

    // prevent sql injections
    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

    // prevent clearly invalid inputs
    if(empty($email)){
        $error = true;
        $emailError = "Please enter your email address.";
    } else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
        $error = true;
        $emailError = "Please enter valid email address.";
    }

    if(empty($pass)){
        $error = true;
        $passError = "Please enter your password.";
    }

    // log in if validation passes:
    if (!$error) {

        $res=mysqli_query($conn, "SELECT * FROM verification_table WHERE email='$email'");
        $row=mysqli_fetch_array($res);
        $verify = mysqli_num_rows($res);

        if ($verify == 0) {

            $password = hash('sha256', $pass); // password hashing using SHA256

            $res = mysqli_query($conn, "SELECT userId, firstName, userPass FROM users WHERE userEmail='$email'");
            $row = mysqli_fetch_array($res);
            $count = mysqli_num_rows($res); // if username and password are correct only 1 row will be returned. (Think about this - template's claim.)

            if ($count == 1 && $row['userPass'] == $password) {

                $_SESSION['user'] = $row['userId'];
                header("Location: booksearch.php");
            } else {
                $errMSG = "Incorrect Credentials, Try again...";
            }
        } else {
            $resendLink = 'http://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$row['token'].'&action=resend';
            if(time() - $row['tstamp'] < 86400) {
                $errMSG = 'Your account is currently disabled pending verification.  Check your email, or <a href="'.$resendLink.'">click here</a> to resend.';
            } else {
                $errMSG = 'Your account is currently disabled pending verification, but all verification links have expired.  <a href="'.$resendLink.'">Click here</a> to resend.';
                //todo - different links based on password reset and original account confirmation - possibly can wait till after bootstrapping
                // @TODO: Maybe if password matches, assume this is first verification, otherwise send password reset verification
            }
        }


    }

}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>BookSearch Login</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>

    <div class="container">

        <div id="login-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">

                <div class="col-md-12">

                    <div class="form-group">
                        <h2 class="">Sign In.</h2>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <?php
                    if ( isset($errMSG) ) {

                        ?>
                        <div class="form-group">
                            <div class="alert alert-danger">
                                <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                            <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo $email; ?>" maxlength="40" />
                        </div>
                        <span class="text-danger"><?php echo $emailError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" name="pass" class="form-control" placeholder="Your Password" maxlength="15" />
                        </div>
                        <span class="text-danger"><?php echo $passError; ?></span>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary" name="btn-login">Sign In</button>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <div class="form-group">
                        Not registered? Sign up <a href="register.php">here.</a>
                    </div>

                </div>

            </form>
        </div>

    </div>

    <?php include 'scripts/gtm.php' ?>

    </body>
    </html>
<?php ob_end_flush(); ?>