<?php
ob_start();
session_start();
if( isset($_SESSION['user'])!="" ){ // todo consider - logged in user cannot request password reset link
    header("Location: ".$appHome);
}
require_once 'scripts/appsettings.php';

$error = false;

// TODO if (t is set in get) { hide the email form, if password or confirm set, check pass and error or set else hide the pass form and continue to get email.  This method of dual-page use feels confused, might be hard to make beautiful later, or I might have planned something incorrectly.  Might be buggy

if(isset($_POST['password']) && isset($_POST['confirm'])) {

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // password validation
    if (empty($password)){
        $error = true;
        $passError = "Please enter password.";
    } else if(strlen($password) < 6) {
        $error = true;
        $passError = "Password must have at least 6 characters.";
    }

    // password match validation
    if ($password != $confirm) {
        $error = true;
        $passConfError = "Passwords do not match!";
    }

    // todo hide email form

    if(!$error) {

        echo "Password will be changed of user: ".$email; //TODO get this defined.  Go through all todos, esp. different activation link types.  Then work on profile editing page and local email validation.  Then on book listings.
        $password = hash('sha256', $password);

        //$query = "UPDATE users SET userPass='$password' WHERE userEmail='$email'";
        //$res = mysqli_query($conn, $query);

        unset($password);
        unset($confirm);

    }


} else if(isset($_GET['t'])) {
    $query = "SELECT * FROM verification_table WHERE token='$token'";
    $res = mysqli_query($conn, $query);
    if ($res) {
        $count = mysqli_num_rows($res);

        //    if timestamp

        // verify, show pass input field, hide email input

        // else

        // else

        // invalid token - link to this page with reset request

    } else {

        if (isset($_POST['reset-email']) || isset($_POST['email'])) { // TODO run through this when form has been posted too (_post email set) but get email from there instead

            $email = isset($_POST['reset-email']) ? $_POST['reset-email'] : $_POST['email']; //TODO determine which one yields reset-email and which yields email.  Then hide other field based on that.

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = true;
                $emailError = "Please enter your valid email address.";
            } else {
                $firstName = htmlspecialchars(strip_tags(trim($_POST['reset-email'])));

                // Make sure email exists
                $query = "SELECT userEmail FROM users WHERE userEmail='$email'";
                $result = mysqli_query($conn, $query);
                $count = mysqli_num_rows($result);
                if ($count != 1) {
                    $error = true;
                    $emailError = 'There\'s no account associated with that email.  Why not <a href="register.php">register</a>?';
                }
            }

            // if there's no error, continue to reset
            if (!$error) {

                $token = bin2hex(random_bytes(64));
                $query = "INSERT INTO verification_table(token, email) VALUES('$token', '$email')";
                $res = mysqli_query($conn, $query);

                if ($res) {

                    $subject = $appName . " Account Recovery Information";
                    $msg = '<html>Hello ' . $firstName . ',<br><br>
                A password reset request has been made for your ' . $appName . ' account.<br><br>To reset your password, please click <a href="' . 'http://' . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/reset.php?t=' . $token . '"/>here,</a> or copy the following URL into your browser:<br><br>
                https://' . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/reset.php?t=' . $token . '<br><br>
                This link will expire in 24 hours.<br><br>
                If you did not initiate this request, please disregard this message.<br><br>Thanks,<br><br>- The ' . $appName . ' Team</html>';

                    $res = mail($email, $subject, $msg, $headers .= 'From: ' . $appName . " <no-reply@" . $_SERVER['SERVER_NAME'] . ">\r\n" . 'Content-type: text/html; charset=iso-8859-1');

                    if ($res) {
                        $errTyp = "success";
                        $errMSG = "A password reset link has been sent to you at .";
                    } else {
                        $errTyp = "danger";
                        $errMSG = "Error sending email.  Please try again later.\n If the problem persists, contact the system administrator.";
                    }

                    unset($email);

                } else {
                    $errTyp = "danger";
                    $errMSG = "Something went wrong, in a bad way.  You should still be able to log in, but you're not verified, and that's bad...";
                }

            } else {
                $errTyp = "danger";
                $errMSG = "Something went wrong, try again later...";
            }
        }


    }
}
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $appName ?> - Reset Password</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" href="style.css" type="text/css"/>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
        <?php echo $style ?>
    </head>
    <body>

    <div class="container">

    <div id="login-form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">

            <div class="col-md-12">

                <div class="form-group">
                    <h2 class="">Reset Password</h2>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <?php
                if (isset($errMSG)) {

                    ?>
                    <div class="form-group">
                        <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                            <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter Your Email"
                               maxlength="40" value="<?php echo $email ?>"/>
                    </div>
                    <span class="text-danger"><?php echo $emailError; ?></span>
                    <button type="submit" class="btn btn-block btn-primary" name="btn-signup">Reset Password</button>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <div class="form-group">
                    <a href="login.php">Sign in Here...</a>
                </div>

            </div>

        </form>
    </div>

    <div id="reset-form">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off"> <!-- todo set name for detecting and choosing which form to display? -->

    <div class="col-md-12">

    <div class="form-group">
        <h2 class="">Reset Password</h2>
    </div>

    <div class="form-group">
        <hr/>
    </div>

    <?php
    if (isset($errMSG)) {

        ?>
        <div class="form-group">
            <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
            </div>
        </div>
        <?php
    }
                    ?>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" name="password" class="form-control" placeholder="Enter a New Password" maxlength="40" value="<?php echo $password ?>" />
                        </div>
                        <span class="text-danger"><?php echo $passError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" maxlength="40" value="<?php echo $confirm ?>" />
                        </div>
                        <span class="text-danger"><?php echo $passConfError; ?></span>

                    </div>

                    <div>
                        <hr>
                        <button type="submit" class="btn btn-block btn-primary" name="btn-reset">Reset Password</button>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <div class="form-group">
                        <a href="login.php">Sign in Here...</a>
                    </div>

                </div>

            </form>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
    <?php include 'scripts/gtm.php' ?>

    </body>
    </html>
<?php ob_end_flush();