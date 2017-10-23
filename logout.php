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
//$_SESSION["logged"] = true;
    if (isset($_COOKIE['myforum'])) {
        unset($_COOKIE['myforum']);
        setcookie('myforum', null, -1, '/');
    }
    if (isset($_COOKIE['user_level'])) {
        unset($_COOKIE['user_level']);
        setcookie('user_level', null, -1, '/');
    }
    header('Location: index.php');
}
?>