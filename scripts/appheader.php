<?php
ob_start();
session_start();
require_once 'appsettings.php';
require_once 'require_login.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $pageTitle." - ".$appName;?></title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet"> <!-- // Cannot resolve file, but that's good - It will be resolvable when included-->
    <!-- There's also an "Optional Theme."  Whatever that means... Load these with integrity in production-->

    <link href="assets/css/navbar-fixed-top.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>

<body>
<!-- Static navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span><!-- What do these do! -->
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $appHome; ?>"><?php echo $appName; ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <!--li class="active"><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li-->

                <?php

                $links = [
                    [
                        ["Home", "booksearch.php"],
                        ["Account", "profile.php"],
                        ["Contact", "contact.php"]
                    ],
                    [
                        ["Logout", "logout.php?logout"]
                    ]
                ];

                echoNavLinks($links, 0);
                /**
                 * @param $links    An array of arrays of links
                 * @param $i        index within said array for link output
                 */
                function echoNavLinks($links, $i) {

                    foreach ($links[$i] as $link) {
                        echo "<li";
                        if (basename($_SERVER['SCRIPT_NAME']) == $link[1]) {
                            echo " class=\"active\"";
                        }
                        echo "><a href=\"" . $link[1] . "\">" . $link[0] . "</a></li>\n";
                    }
                }


                ?>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                echoNavLinks($links, 1);
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
