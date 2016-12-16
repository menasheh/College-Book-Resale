<?php
$pageTitle = "New Listing";
include 'scripts/appheader.php';
include 'scripts/isbn.php';
$tools = new isbn;

if(isset($_SESSION['sISBN'])){
    $isbn = $_SESSION['sISBN'];
}elseif(isset($_GET['isbn']) && $tools->isValidISBN($_GET['isbn'])){
    $isbn = $tools->get_isbn10($_GET['isbn']); //TODO is this sanitized?
}else{
    header("Location: $appHome");
}
unset($_SESSION['sISBN']);

echo "We should be listing that you're selling the book with ISBN ".$isbn;

echo "<br>collect sale amount and go..."

?>
<br>

    If it's not, it loads info on the ISBN. (from our db and google)  If it's null, it redirects to $appHome.  If it's listed as inappropriate, <br> it tells you that and suggest you ask for whitelisting if it's a mistake.

    <br>

    Form:
    Title, [greyed out if google gives us data]
    Author, [greyed out if google gives us data]
    Description,



<?php include 'scripts/appfooter.php';