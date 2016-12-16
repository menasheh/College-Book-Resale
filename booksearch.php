<?php
$pageTitle = "Search";
include 'scripts/appheader.php';
include 'scripts/isbn.php';
$isbncheck = new isbn();

$bookError = "";

if (isset($_GET['ISBN'])) {
    $ISBN = htmlspecialchars($_GET['ISBN']);
    if (!$isbncheck->isValidISBN($ISBN)) {
        //!(preg_match('/^(?=[0-9]*$)(?:.{10}|.{13})$/', str_replace('-', '', $ISBN)))&&(==0)) {
        unset($ISBN);
        $bookError = "That is not a valid ISBN-10 or ISBN-13.";
    }
};

function getBookDetails($isbn)
{
    //$isbn = 9781451648546; //Testing Purposes}
    if (isset($isbn)) {
        $isbn = str_replace('-', '', $isbn);
        $gbooks_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;
        $json = file_get_contents($gbooks_url);
        $data = json_decode($json); //json_decode($json, true); //Some swear this is better...

        // @DEBUGGING
        echo "<pre>";
        print_r($data);
        echo "</pre>";


        return $data;
    } else {
        $GLOBALS['bookError'] = "Please enter an ISBN umber.";
        return null;
    }

}

$bookInfo = getBookDetails($ISBN);

$resultMarkup = '';
if ($bookInfo != null) {
    if ($bookInfo->totalItems == 1) {
        $bookInfo = $bookInfo->items[0]->volumeInfo;

        if ($bookInfo->maturityRating == "NOT_MATURE") {

            $bookTitle = $bookInfo->title;
            $bookImage = $bookInfo->imageLinks->thumbnail;
            $bookDesc = $bookInfo->description;

            $_SESSION['sISBN'] = $bookInfo->industryIdentifiers[1]->identifier;

        } else {
            $bookError = "That book is not appropriate.";
            echo "<script>ga('send','event','NewBookError','search','Inappropriate book not shown.')</script>";
        }

    } else {
        $bookError = "Google doesn't know about that book, at least not the version with that ISBN... <i>sigh</i>";
        echo "<script>ga('send','event','NewBookError','search','bookNotFound')</script>";
    }
}

?>
    <br><br>
    <div class="container">
        <?php if (!isset($_SESSION['hideJumbo'])){ ?>
        <div class="jumbotron">
            <h3>Start by searching for a book:</h3><br><?php } ?>

            <?php if ($bookError != "") { ?>
                <div class="alert alert-danger"><?php echo $bookError; ?></div><?php } ?>

            <form class="form" method="get" action="<?php echo $_SESSION['PHP_SELF']; ?>">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-book"></span></span>
                    <input type="text" class="form-control input-md" id="lookup" name="ISBN" placeholder="ISBN"
                           value="<?php echo $ISBN; ?>">
                    <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                   <span class="glyphicon glyphicon-search">
                   </span>
                </button>
              </span>
            </form>
            <?php if (!isset($_SESSION['hideJumbo'])){ ?></div><?php } ?>
    </div>
    <br>

<?php if(isset($_SESSION['sISBN'])) { ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <strong><?php echo $bookTitle; ?></strong>
            <span style="float:right;">
                <form method="post" action="listbook.php">
                    <button id="lookup" class="btn btn-sell">Sell this book
                    </button>
                </form>
            </span><br><br>

            <div class="row">
                <div class="col-md-2">
                    <img class="center-block" src="<?php echo $bookImage; ?>"/>
                </div>
                <div class="col-md-10">
                    <table>
                        <tr>
                            <td>
                                Table 1, 1
                            </td>
                            <td> Table 1, 2</td>
                        </tr>
                        <tr>
                            <td>table 2, 1</td>
                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-default">
                <div class="panel-body"><?php echo $bookDesc ?><br><br>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

    <br>Here's how this page is supposed to work: <br>

    If there is a query, search the ISBN, title, author, (and description?) fields of our database.<br>

    Show cards of books, with cheapest available (nonpending) offer reserved when you press buy.

    <br> Plan the database fully before you start this...

    Add search field and query results...<br>
    <a href="listbook.php">Sell With Us</a>
    etc;
    finalize stuff from datagrip (does that work in standard sql?)

    <div class="col-md-12">


    </div>

    Add search field and query results...
    newlisting.php
    etc;
    finalize stuff from datagrip (does that work in standard sql?)<br><br>


<?php include 'scripts/appfooter.php';

$_SESSION['hideJumbo'] = true;