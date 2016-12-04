<?php
// TODO: learn this method of error handling - EDIT: it's lame, actually.
// TODO: Require email from specific domain as an option, and confirm emails before allowing login
// TODO: Add OAuth signins
// TODO: Add password confirmation input
// TODO: grey-out sign up button until input is validated and display issues in real time

ob_start();
session_start();
if( isset($_SESSION['user'])!="" ){
    header("Location: ".$appHome);
}
include_once 'scripts/appsettings.php';

$error = false;

if ( isset($_POST['btn-signup']) ) {

    // clean user inputs to prevent sql injections
    $firstName = htmlspecialchars(strip_tags(trim($_POST['firstName'])));
    $lastName = htmlspecialchars(strip_tags(trim($_POST['lastName'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $pass = htmlspecialchars(strip_tags(trim($_POST['pass'])));

    // basic name validation
    if (empty($firstName)) {
        $error = true;
        $firstNameError = "Please enter your first name.";
    } else if (strlen($firstName) < 3) {
        $error = true;
        $firstNameError = "Most first names are a bit longer than that.";
    } else if (!preg_match("/^[a-zA-Z]+$/",$firstName)) {
        $error = true;
        $firstNameError = "Invalid first name.  Alphabet characters only.";
    }

    // basic last name validation
    if (empty($lastName)) {
        $error = true;
        $lastNameError = "Please enter your last name.";
    } else if (strlen($lastName) < 3) {
        $error = true;
        $lastNameError = "Most last names are a bit longer than that.";
    } else if (!preg_match("/^[a-zA-Z]+$/",$lastName)) {
        $error = true;
        $lastNameError = "Invalid first name.  Alphabet characters only.";
    }

    //basic email validation
    if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
        $error = true;
        $emailError = "Please enter your valid email address.";
    } else {
        // check email exist or not
        $query = "SELECT userEmail FROM users WHERE userEmail='$email'";
        $result = mysqli_query($conn, $query);
        $count = mysqli_num_rows($result);
        if($count!=0){
            $error = true;
            $emailError = "There's already an account with that email.  (<a href=\"login.php\">log in</a> or <a href=\"reset.php\">reset password</a>)"; //@TODO pass email, if it's set, to the reset page.
        }
    }
    // password validation
    if (empty($pass)){
        $error = true;
        $passError = "Please enter password.";
    } else if(strlen($pass) < 6) {
        $error = true;
        $passError = "Password must have at least 6 characters.";
    }

    // encrypt password using SHA256();
    $password = hash('sha256', $pass);

    // If there's no error, add the new user to the database, store a hash, and send them a confirmation email.
    if( !$error ) {

        $query = "INSERT INTO users(firstName, lastName, userEmail, userPass) VALUES('$firstName','$lastName','$email','$password')";
        $res = mysqli_query($conn, $query);

        if ($res) {
            $token = bin2hex(random_bytes(64));
            $verifyType = 0; // 0 = new user, 1 = forgot password.  TODO remove all of that particular type when resetting.
            $timestamp = time();
            $query = "INSERT INTO verification_table(token, email, type, tstamp) VALUES('$token', '$email', $type, '$timestamp')";
            $res = mysqli_query($conn, $query);

            $fromEmail = "no-reply";

            if ($res) {

                $subject = "Confirm Your ".$appName." Account";
                $msg =  '<html>Hello '.$firstName.',<br><br>
                Welcome to '.$appName.'!<br><br>To confirm your account, please click <a href="'.'http://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$token.'"/>here,</a> or copy the following URL into your browser:<br><br>
                https://'.$_SERVER['HTTP_HOST'].'/'.basename(__DIR__).'/verify.php?t='.$token.'<br><br>
                If you\'re not '.$firstName.', please disregard this message.<br><br>Thanks,<br><br>- The '.$appName.' Team</html>';

                $res = mail($email, $subject, $msg, $headers .= 'From: '.$appName." <".$fromEmail."@".$_SERVER['SERVER_NAME'].">\r\n".'Content-type: text/html; charset=iso-8859-1');

                if ($res) {
                    $errTyp = "success";
                    $errMSG = "Account created successfully.  A confirmation email has been sent to you at your newly registered address.<br><i>(If you don't receive it, check your spam folder, or add ".$fromEmail."@".$_SERVER['SERVER_NAME']." to your address book and try again.)</i>";
                } else {
                    $errTyp = "danger";
                    $errMSG = "Account created successfully.  Confirmation email could not be sent, you will not be able to log in.\nPlease contact the system administrator.";
                }

                unset($firstName);
                unset($lastName);
                unset($email);
                unset($pass);
            } else {
                $errTyp = "danger";
                $errMSG = "Something went wrong, in a bad way.  You should still be able to log in, but you're not verified, and that's bad...";
            }

        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, try again later... (SQL Failure)";
        }

    }


}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $appName ?> - Register</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>

    <div class="container">

        <div id="login-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">

                <div class="col-md-12">

                    <div class="form-group">
                        <h2 class="">Sign Up.</h2>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <?php
                    if ( isset($errMSG) ) {

                        ?>
                        <div class="form-group">
                            <div class="alert alert-<?php echo ($errTyp=="success") ? "success" : $errTyp; ?>">
                                <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="text" name="firstName" class="form-control" placeholder="Enter First Name" maxlength="50" value="<?php echo $firstName ?>" />
                        </div>
                        <span class="text-danger"><?php echo $firstNameError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="text" name="lastName" class="form-control" placeholder="Enter Last Name" maxlength="50" value="<?php echo $lastName ?>" />
                        </div>
                        <span class="text-danger"><?php echo $lastNameError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                            <input type="email" name="email" class="form-control" placeholder="Enter Your Email" maxlength="40" value="<?php echo $email ?>" />
                        </div>
                        <span class="text-danger"><?php echo $emailError; ?></span>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" name="pass" class="form-control" placeholder="Enter Password" maxlength="15" />
                        </div>
                        <span class="text-danger"><?php echo $passError; ?></span>
                    </div>

                    <div class="form-group">
                        <hr />
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary" name="btn-signup">Sign Up</button>
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

<?php include 'scripts/appfooter.php';