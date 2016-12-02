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
    <a href="booksearch.php">Home</a><br><a href="logout.php?logout">Log Out</a>
    <br><br>

    <form class="form">
        <fieldset>
            <!-- Form Name -->
            <legend>Book Lookup</legend>
            <!-- Text input-->
            <div class="form-group">
                <form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>">
                <label class="col-md-4 control-label" for="ISBN">ISBN:</label>
                <div class="col-md-4">
                    <input id="ISBN" name="ISBN" type="text" placeholder = "ISBN" value="<?php echo(htmlspecialchars($_GET["ISBN"])) ?>" class="form-control input-md">
                <!-- Button -->
                <label class="col-md-4 control-label" for="lookup"></label>

                    <button id="lookup" class="btn btn-primary">Submit</button></div>
                    <span class="help-block">ISBN-10 or ISBN-13 </span>


                </div>
            </div>

        </fieldset>
    </form>
    Add search field and query results...
    newlisting.php
    etc;
    finalize stuff from datagrip (does that work in standard sql?)<br><br>

    <?php
    if (isset($_GET['ISBN'])) {
        $ISBN = htmlspecialchars($_GET['ISBN']);
    };

    function getBookDetails($isbn) {
        //$isbn = 9781451648546; //Testing Purposes}
        if (preg_match('/^(?=[0-9]*$)(?:.{10}|.{13})$/', $isbn)) {
            $gbooks_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:".$isbn;
            $json = file_get_contents($gbooks_url);
            $data = json_decode($json); //json_decode($json, true); //Some swear this is better...
            return $data;
        } else {
            echo "@ERROR:  That is not a valid ISBN-10 or ISBN-13.<br><br>";
            return null;
        }

    }

    $bookInfo = getBookDetails($ISBN);
    if($bookInfo->totalItems) {
        $bookInfo = $bookInfo->items[0]->volumeInfo;

        if($bookInfo->maturityRating == "NOT_MATURE") {

            //echo "<pre>"; echo print_r($bookInfo); echo "</pre>" . "<br><br>";

            $bookTitle = $bookInfo->title;
            $bookImage = $bookInfo->imageLinks->thumbnail;
            $bookDesc = $bookInfo->description;

            echo "Title: " . $bookTitle . "<br><br>";
            echo '<img src ="' . $bookImage . '"/>';
            echo "Description: " . $bookDesc . "<br><br>";

            //This form is ugly and useless.
            echo "If user presses, list, take to a page where they submit data.  Maybe that's this page, and this page should be main search page.  But main search should have title and such, or not?";
            echo '<div class="form-group">
                <form method="get" action="<?php echo $_SERVER[\'PHP_SELF\'];?>">
                <!-- Button -->
                <button id="lookup" class="btn btn-sell">List</button>
                </div>';
//            See the googledoc, what info to take.  Fix up database first.

        } else {
            echo "That book is not appropriate.";
        }

    } else {
        echo "That book does not exist.";
    }

    ?>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <?php include '/homepages/6/d146462722/htdocs/menasheh/scripts/gtm.php' ?>
    </body>
    </html>

<?php ob_end_flush(); ?>