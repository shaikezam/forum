<?php

//signup.php
include 'dbmanager.php';
include 'header.php';
include 'footer.php';
session_start();
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
    }
} else {
    
}
