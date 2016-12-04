<?php
$pageTitle = "Search";
include 'scripts/appheader.php'; ?>

    Here's how this page is supposed to work: <br>

    If there's no query, big all-encompassing search box in the middle of the page. <br>

    If there is a query, search the ISBN, title, author, (and description?) fields of our database.<br>

    Show cards of books, with cheapest available (nonpending) offer reserved when you press buy.

    <br> Plan the database fully before you start this...

    Add search field and query results...<br>
    <a href="listbook.php">Sell With Us</a>
    etc;
    finalize stuff from datagrip (does that work in standard sql?)


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

            $bookTitle = $bookInfo->title;
            $bookImage = $bookInfo->imageLinks->thumbnail;
            $bookDesc = $bookInfo->description;

            echo "Title: " . $bookTitle . "<br><br>";
            echo '<img src ="' . $bookImage . '"/>';
            echo "Description: " . $bookDesc . "<br><br>";

            //This form is ugly and useless.
            echo "If user presses, list, take to a page where they submit data.  Maybe that's this page, and this page should be main search page.  But main search should have title and such, or not?";
            echo '<div class="form-group">
                <form method="post" action="'.$_SERVER['PHP_SELF'].'">
                <!-- Button -->
                <button id="lookup" class="btn btn-sell">List</button>
                </div>';
//            See the googledoc, what info to take.  Fix up database first.

        } else {
            echo "That book is not appropriate.";
            ga('send', 'event', 'NewBookError' , 'search', 'Inappropriate book not shown.');
        }

    } else {
        echo "That book does not exist.";
        ga('send', 'event', 'NewBookError' , 'search', 'bookNotFound');
    }

    ?>


<?php include 'scripts/appfooter.php';