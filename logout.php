<?php

//signup.php
include 'dbmanager.php';
include 'header.php';
include 'footer.php';
include 'utils.php';
session_start();
//session_destroy();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    /* the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
//$_SESSION["logged"] = true;
    if (isset($_SESSION["logged"])) {
        unset($_SESSION["logged"]);
        unset($_SESSION["user_level"]);
        header('Location: index.php');
    }
}
?>