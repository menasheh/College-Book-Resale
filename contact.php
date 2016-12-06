<?php
include 'scripts/siteheader.php'
?>

    <div class="container">

        Just for the record, be advised that this contact form doesn't actually do anything...

        Contact - Eg; Add your campus - or should this be its own page?

        <form id="contact" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
            <h3>Contact Form<br></h3>
            <h4>
                It's pretty straightforward.  Go ahead.  You can do it!
            </h4>
            <fieldset>
                <input placeholder="Your name" type="text" tabindex="1" name="name" required autofocus>
            </fieldset>
            <fieldset>
                <input placeholder="Your Email Address" type="email" tabindex="2" name="email" required pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Please use a valid email address!">
            </fieldset>
            <fieldset>
                <input placeholder="Your Phone Number" type="tel" tabindex="3" name="phone" required>
            </fieldset>
            <fieldset>
                <textarea placeholder="Type your Message Here...." tabindex="4" name="message" required></textarea>
            </fieldset>
            <fieldset>
                <div class="g-recaptcha" data-sitekey="6LcfuwwUAAAAACHly6QD0kdou7Kyb5z6ZMDYGtBY"></div>
            </fieldset>
            <fieldset>
                <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
            </fieldset>
        </form>
    </div>

<?php include 'scripts/sitefooter.php';