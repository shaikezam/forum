<?php
ob_start();
session_start();
include_once 'dbmanager.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'utils.php';

//session_destroy();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo $_SERVER['REMOTE_ADDR'] . '   ' . $_SERVER['HTTP_USER_AGENT'];
    /* the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    if (!isset($_COOKIE['myforum'])) {
        echo 'Please <a href="login.php">log in</a> or <a href="register.php">register</a><br><br>';
        //return;
    } else {
        echo 'Hello ' . $_COOKIE['myforum'] . ',<br>';
        $user_id = Utils::getUserIDByName($_COOKIE['myforum']);
        if (isset($_COOKIE["user_level"]) && $_COOKIE["user_level"] === Utils::ADMIN) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php">Control panel </a><a href="logout.php">log out</a><br><br>';
    }
    $categories = Utils::getCategories();
    if (is_array($categories) && $categories['status'] === 'Error') {
        Utils::ThrowErrorLog($categories['message']);
    } else {
        echo $categories;
    }
} else {
    
}
?>