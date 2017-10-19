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
    if (!isset($_SESSION["logged"])) {
        echo '<h3>Sign in</h3>
            <form method="post" action="">
                Username: <input type="text" name="user_name" class="form-control" /><br>
                Password: <input type="password" name="user_pass" class="form-control" ><br>
                <input type="submit" value="log in" id="submitLogin" class="btn btn-default" />
            </form>';
        return;
    } else {
        echo '<h3>Already logged in</h3>';
    }
} else {
    if (isset($_POST['user_name']) && isset($_POST['user_pass'])) {
        $username = $_POST['user_name'];
        $user_pass = $_POST['user_pass'];
        $is_valid = Utils::ValidateLoginDetails($username, $user_pass);
        if ($is_valid === true) {
            $_SESSION["logged"] = $username;
            header('Location: index.php');
        } else if ($is_valid === false){
            Utils::ThrowErrorLog(Utils::USER_NOT_FOUND);
        } else {
            Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
        }
    }
}
