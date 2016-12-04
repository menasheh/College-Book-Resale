<?php
$pageTitle = "New Listing";
include 'scripts/appheader.php';
?>

    This page takes a GET (or post?) of an ISBN.  If it's null, it redirects to $appHome.

    If it's not, it loads info on the ISBN. (from our db and google)  If it's null, it redirects to $appHome.  If it's listed as inappropriate, <br> it tells you that and suggest you ask for whitelisting if it's a mistake.

    <br>

    Form:
    Title, [greyed out if google gives us data]
    Author, [greyed out if google gives us data]
    Description,



<?php include 'scripts/appfooter.php';