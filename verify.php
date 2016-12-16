<?php
// Verification page for various types of verifications as needed by the LCM Books web app.

// Loads the database connection and sets $appName, $appIndex, and $appHome[page].
require_once 'scripts/appsettings.php';

$pageTitle = "Verify";

// Displays the site header including basic HTML page structure
require_once 'scripts/siteheader.php';

/**
 * Draft of MODE settings:
 *
 * 0 = verify user email
 * 1 = forgot password.
 * 2 = verify contact email
 * 3 = contact SMS verification
 * 4 = Telegram account
 * 5 = campus identity email verification (~~~)
 */

// To discourage playing with the link URL, we manually move the $_GET variables to $_SESSION variables and then reload
// the page without the query string.
if ($_SESSION['vAction'] == "Verify" && !isset($_GET['t'])) { //Session variables set correctly. TODO take this GET check &&-> out but account for legitimate new verifications.  Get rid of query string always

    // Pull mode and token variables from session for easier use in queries
    $vMode = $_SESSION['vMode'];
    $token = $_SESSION['vToken'];

    // Query Holds Database
    $query = "SELECT * FROM verification_table WHERE token='$token' AND vType='$vMode'";
    $res = mysqli_query($conn, $query);
    $email; //Define a blank variable email so that we won't lose it to the scope of the if statement which makes
            // sure we get a response

    if ($res) { // Successful Query, but can still have no results...

        $queryResult = mysqli_fetch_array($res);
        if (!count($queryResult)) {
            header("Location: ".$appHome);
        }
        if ((time() - $queryResult['tstamp']) > 86400) { // Check saved timestamp for 24 hour expiry
            $_SESSION['vEmail'] = $queryResult['email'];
            // $_SESSION['vCell'] = $queryResult['cell']; // TODO Requiring these extra rows seems like bad database design.  Possibly rename email to contactmethod or something like that...
            // $_SESSION['vTelegram'] = $queryResult['telegram'];
            $_SESSION['vAction'] = "Resend";
            die("ERROR: Expired link. <a href=\"https://" . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/verify.php>resend verification email</a>');
        }
        $email = $queryResult['email'];

    } else { // Failed Query

        die("ERROR: Invalid Link (not found in database)");

    }

    // If the code gets up to here, we know there is a single, valid result in the query.  Now we can confirm by
    // deleting the token and then executing any relevant action. (eg; reset password.  Usually nothing.)


    // Delete all verification_table entries for this type of verification for this user.  This ensures old, expired
    // tokens are removed as well.
    $query = "DELETE FROM verification_table WHERE email='$email' AND vType='$vMode'";
    $res = mysqli_query($conn, $query);

    if (!$res) { // If this query fails, we still can't do anything.  This will rarely happen, if ever.
        die("ERROR:  Could not confirm.  Please <a href=\"" . $_SERVER['REQUEST_URI'] . "\">try again</a> or <a href=\"mailto:" . $_SERVER['SERVER_ADMIN'] . "\">contact</a> the system administrator.");
    }

    // Success Message
    if (vMode == 0) { // User Account Email Verification
        $alertType = "success";
        $alertMsg = "Account Successfully Verified.  You may now <a href=\"login.php\">log in</a>.";
    } elseif ($vMode == 1) { // Forgot Password
        //TODO go to reset password.php with rUserEmail session variable
        echo "This is where you may reset your password if you forgot it.";
    } elseif ($vMode == 2) { // Contact Email Verification
        echo "Contact email verified successfully."; // TODO add link to profile page, which // TODO should list all integrations and requirements.
    } elseif ($vMode == 3) {
        echo "Contact cellphone verified successfully."; // TODO add link to profile page
    } elseif ($vMode == 4) {
        echo "Telegram account verified successfully."; // TODO add link to profile page
    } elseif ($vMode == 5) {
        echo "School email account verified successfully.";
    } else {
        die("You just verified something, but we haven't the slightest clue what.");
    }
    unset($_SESSION['vAction']); // Force redirect to homepage if you reload.

} else {
    if ($_SESSION['vAction'] == "Resend") {

        // Pull variables from session variables for use in queries
        $vMode = $_SESSION['vMode'];
        $token = $_SESSION['vToken'];

        $email = $_SESSION['vEmail'];


        // Resend verification
        if (vMode == 0) { // User Account Email Verification

            $token = bin2hex(random_bytes(64)); // There is a certain level of redundancy between this and the original
            // send in register.php
            $timestamp = time();
            $query = "INSERT INTO verification_table(token, email, vtype, tstamp) VALUES('$token', '$email', '$vMode', '$timestamp')";
            $res = mysqli_query($conn, $query);

            if ($res) {

                $fromEmail = "no-reply";
                $confirmURI = 'https://' . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/verify.php?m=' . $vMode . '&t=' . $token;

                $subject = "Confirm Your " . $appName . " Account";
                $msg = '<html>Hello ' . $firstName . ',<br><br>
                Welcome to ' . $appName . '!<br>To confirm your account, please click <a href="' . $confirmURI . '"/>here,</a> or copy the following URL into your browser:<br><br>
                ' . $confirmURI . '<br><br>
                If you\'re not ' . $firstName . ', please disregard this message.<br><br>Thanks,<br><br>- The ' . $appName . ' Team</html>';

                $res = mail($email, $subject, $msg, $headers .= 'From: ' . $appName . " <" . $fromEmail . "@" . $_SERVER['SERVER_NAME'] . ">\r\nContent-type: text/html; charset=iso-8859-1");

                if ($res) {
                    $alertType = "success";
                    $alertMsg = "A new confirmation email has been sent.<br><i>(If you don't receive it, check your spam folder, or add " . $fromEmail . "@" . $_SERVER['SERVER_NAME'] . " to your address book and try again.)</i>";
                } else {
                    $alertType = "danger";
                    $alertMsg = "Confirmation email could not be sent.  Please <a href=\"mailto:" . $_SERVER['SERVER_ADMIN'] . "\">contact</a> the system administrator.";
                }

            } else {
                $alertType = "danger";
                $alertMsg = "Confirmation email could not be sent due to a database issue.  Please <a href=\"mailto:" . $_SERVER['SERVER_ADMIN'] . "\">contact</a> the system administrator.";
            }


        } elseif ($vMode == 1) { // Forgot Password
            echo "This is where we should resend your password reset email.";
        } elseif ($vMode == 2) { // Contact Email Verification
            echo "This is where we confirm your contact email.";
        } elseif ($vMode == 3) {
            echo "This is where we confirm your contact cellphone.";
        } elseif ($vMode == 4) {
            echo "This is where we confirm your Telegram account.";
        } elseif ($vMode == 5) {
            echo "This is where we confirm your school email account.";
        } else {
            die("You just verified something, but we haven't the slightest clue what.");
        }

        unset($_SESSION['vAction']);

    } else {  // Put the arguments in session variables to discourage the user from playing with them.

        if (isset($_GET['m']) && strlen($_GET['t']) == 128) { // Will throw a warning if t is not set. Meh.

            $_SESSION['vAction'] = "Verify";
            $_SESSION['vMode'] = $_GET['m'];
            $_SESSION['vToken'] = $_GET['t'];

            header("Location: verify.php");

        } else {

            header("Location: " . $appHome); // Was index.php, but I wanted it to go to booksearch if you're logged in.
            //  If you're not logged in though, perhaps it should go to index.php and not the login page?

        }

    }
}

if (isset($alertMsg)) {

    ?>
    <div class="form-group">
        <div class="alert alert-<?php echo $alertType; ?>">
            <span class="glyphicon glyphicon-info-sign"></span> <?php echo $alertMsg; ?>
        </div>
    </div>
    <?php
}

// Displays the site footer and loads relevant scripts such as Google Analytics and jQuery.  Also closes HTML document.
require_once 'scripts/sitefooter.php';