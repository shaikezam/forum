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
    if (!isset($_SESSION["logged"])) {
        echo '<h3>Sign up</h3>
            <form method="post" action="">
                Username: <input type="text" name="user_name" class="form-control" /><br>
                Password: <input type="password" name="user_pass" class="form-control" ><br>
                Password again: <input type="password" name="user_pass_check" class="form-control" ><br>
                E-mail: <input type="email" name="user_email" class="form-control" ><br>
                <input type="submit" value="register" id="submitRegister" class="btn btn-default" />
            </form>';
        return;
    }
} else {
    if (isset($_POST['user_name']) && isset($_POST['user_pass']) && isset($_POST['user_pass_check']) && isset($_POST['user_email'])) {
        $username = $_POST['user_name'];
        $user_pass = $_POST['user_pass'];
        $user_pass_check = $_POST['user_pass_check'];
        $user_email = $_POST['user_email'];
        $is_valid = Utils::ValidateRegisterDetails($username, $user_pass, $user_pass_check, $user_email);
        Utils::Logger($is_valid);
    }
}