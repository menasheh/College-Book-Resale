<?php
ob_start();
session_start();
require_once 'scripts/appsettings.php';

// If you're already logged in, redirect to the main app
if (isset($_SESSION['user']) != "") {
    header("Location: " . $appHome);
    exit;
}

$error = false;

if (isset($_POST['btn-login'])) {

    // prevent sql injections
    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

    // prevent clearly invalid inputs
    if (empty($email)) {
        $error = true;
        $emailError = "Please enter your email address.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    }

    if (empty($pass)) {
        $error = true;
        $passError = "Please enter your password.";
    }

    // Try to log in if validation passes:
    if (!$error) {

        $res = mysqli_query($conn, "SELECT * FROM verification_table WHERE email='$email' AND vType='0'");
        $row = mysqli_fetch_array($res);
        $verify = mysqli_num_rows($res);

        if ($verify == 0) {

            $password = hash('sha256', $pass); // password hashing using SHA256 TODO use something more secure

            $res = mysqli_query($conn, "SELECT userId, firstName, userPass FROM users WHERE userEmail='$email'");
            $row = mysqli_fetch_array($res);
            $count = mysqli_num_rows($res); // if username and password are correct only 1 row will be returned. (Food for thought - template's claim.)

            if ($count == 1 && $row['userPass'] == $password) {

                $_SESSION['user'] = $row['userId'];
                header("Location: " . $appHome);
            } else {
                $errMSG = "Username or password is incorrect."; //TODO - split count==1 and userPass == password and give links to sign up and reset password pages accordingly
            }
        } else {
            $_SESSION['vAction'] = "Resend";
            $_SESSION['vMode'] = 0;
            $_SESSION['vEmail'] = $email;

            $resendLink = 'https://' . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/verify.php';

            $res = mysqli_query($conn, "SELECT * FROM verification_table WHERE email='$email' AND vType='0'");
            $timestamp = time(); // If connection fails, won't be able to send a new link.  It should never come to use
                                 // this value;  In theory there has to be a timestamp in the database to set to, the
                                 // only chance of hitting this is if the database coughs.
            if ($res) {
                $row = mysqli_fetch_array($res);
                $timestamp = $row['tstamp'];
            }
            $resendAllowed = (time() - $timestamp) > (60 * 60 * 30);
            if (time() - $row['tstamp'] < 86400) {
                $errMSG = 'Your account is currently on hold pending email verification.  Check your email';
                if ($resendAllowed) {
                    $errMSG .= ", or <a href=\"" . $resendLink . "\">click here</a> to resend.";
                } else {
                    $errMSG .= ".";
                }

            } else {
                $errMSG = 'Your account is currently on hold pending email verification, but all verification links have expired.';
                if ($resendAllowed) {
                    $errMSG .= '<a href="' . $resendLink . '">Click here</a> to resend.';
                }

            }

        }

    }

}

include 'scripts/siteheader.php';
?>

    <div class="container">

        <div id="login-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">

                <div class="col-md-12">

                    <div class="form-group">
                        <h2 class="">Sign In.</h2>
                    </div>

                    <div class="form-group">
                        <hr/>
                    </div>

                    <?php
                    if (isset($errMSG)) {

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
                            <input type="email" name="email" class="form-control" placeholder="Your Email"
                                   value="<?php echo $email; ?>" maxlength="40"/>
                        </div>
                        <span class="text-danger"><?php echo $emailError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" name="pass" class="form-control" placeholder="Your Password"
                                   maxlength="15"/>
                        </div>
                        <span class="text-danger"><?php echo $passError; ?></span>
                    </div>

                    <div class="form-group">
                        <hr/>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary" name="btn-login">Sign In</button>
                    </div>

                    <div class="form-group">
                        <hr/>
                    </div>

                    <div class="form-group">
                        Not registered? <a href="register.php">Sign up</a>!
                    </div>

                </div>

            </form>
        </div>

    </div>

<?php include 'scripts/sitefooter.php';