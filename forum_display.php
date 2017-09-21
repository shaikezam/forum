<?php

session_start();
include_once 'dbmanager.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'utils.php';

//session_destroy();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    /* the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    $cat_id = $_GET['id'];
    if (!isset($_SESSION["logged"])) {
        echo 'Please <a href="login.php">log in</a> or <a href="register.php">register</a><br><br>';
        //return;
    } else {
        echo 'Hello ' . $_SESSION["logged"] . ',<br>';
        if (isset($_SESSION["user_level"])) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php">Control panel </a><a href="logout.php">log out</a><br>' .
        '<form method="get" action="new_topic.php?id=">' .
        '<input type="hidden" name="id" value="' . $cat_id . '" />' .
        '<input type="submit" value="New Topic" id="submitLogin" class="btn btn-default" />
            </form><br>';
    }
    $topics = Utils::getTopics($cat_id);
    if (!$topics) {
        Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
    } else {
        echo $topics;
    }
} else {
    
}
?>