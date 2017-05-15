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
    if (!isset($_SESSION["logged"])) {
        echo 'Please <a href="login.php">log in</a> or <a href="register.php">register</a><br><br>';
        //return;
    } else {
        echo 'Hello ' . $_SESSION["logged"] . ',<br>';
        $user_id = $_GET['id'];
        if (isset($_SESSION["user_level"])) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php?id=' . $user_id . '">Control panel </a><a href="logout.php">log out</a><br><br>';
        $data = Utils::getUserData($user_id);
        if (!$data) {
            Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
        } else {
            echo $data;
        }
    }
} else {
    
}
?>