<?php

//signup.php
include_once 'dbmanager.php';
include 'header.php';
include 'footer.php';
include 'utils.php';
session_start();
//session_destroy();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    /* the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    //$_SESSION["logged"] = true;
    DBConnection::getDBConnection();
    if (!isset($_SESSION["logged"])) {
        echo 'Please <a href="login.php">log in</a> or <a href="register.php">register</a><br><br>';
        //return;
    } else {
        echo 'Hello ' . $_SESSION["logged"];
        if (isset($_SESSION["user_level"])) {
            echo ' <a href="/forum/admin_panel.php">Admin panel</a>';
        }
        echo ' <a href="/forum/logout.php">log out</a><br><br>';
    }
    $categories = Utils::getCategories();
    if (!$categories) {
        Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
    } else {
        echo $categories;
    }
} else {
    /* so, the form has been posted, we'll process the data in three steps:
      1.  Check the data
      2.  Let the user refill the wrong fields (if necessary)
      3.  Save the data
     */
    $errors = array(); /* declare the array for later use */

    if (isset($_POST['user_name'])) {
        //the user name exists
        if (!ctype_alnum($_POST['user_name'])) {
            $errors[] = 'The username can only contain letters and digits.';
        }
        if (strlen($_POST['user_name']) > 30) {
            $errors[] = 'The username cannot be longer than 30 characters.';
        }
    } else {
        $errors[] = 'The username field must not be empty.';
    }


    if (isset($_POST['user_pass'])) {
        if ($_POST['user_pass'] != $_POST['user_pass_check']) {
            $errors[] = 'The two passwords did not match.';
        }
    } else {
        $errors[] = 'The password field cannot be empty.';
    }

    if (!empty($errors)) /* check for an empty array, if there are errors, they're in this array (note the ! operator) */ {
        echo 'Uh-oh.. a couple of fields are not filled in correctly..';
        echo '<ul>';
        foreach ($errors as $key => $value) /* walk through the array so all the errors get displayed */ {
            echo '<li>' . $value . '</li>'; /* this generates a nice error list */
        }
        echo '</ul>';
    } else {
        
    }
}
DBConnection::closeDBConnection();
?>