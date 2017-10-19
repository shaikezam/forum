<?php
ob_start();
session_start();
include_once 'dbmanager.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'utils.php';

//session_destroy();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    /* the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    $topic_id = $_GET['id'];
    $cat_id = $_GET['forum_id'];
    if (!isset($_SESSION["logged"])) {
        echo 'Please <a href="login.php">log in</a> or <a href="register.php">register</a><br><br>';
        //return;
    } else {
        echo 'Hello ' . $_SESSION["logged"] . ',<br>';
        if (isset($_SESSION["user_level"])) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php">Control panel </a><a href="logout.php">log out</a><br>' .
        '<form method="get" action="new_post.php">' .
        '<input type="hidden" name="topic_id" value="' . $topic_id . '" />' .
        '<input type="hidden" name="forum_id" value="' . $cat_id . '" />' .
        '<input type="submit" value="New Post" id="submitLogin" class="btn btn-default" />
            </form><br>';
    }
    $posts = Utils::getPosts($cat_id, $topic_id);
    if (!$posts) {
        Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
    } else {
        echo $posts;
    }
} else {
    
}
?>