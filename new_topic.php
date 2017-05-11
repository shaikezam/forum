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
        Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
        //return;
    } else {
        echo 'Hello ' . $_SESSION["logged"] . ',<br>';
        if (isset($_SESSION["user_level"])) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php">Control panel </a><a href="logout.php">log out</a>' .
        '<h3>New Topic</h3>' .
        '<form method="post" action="" align = "center">' .
        '<input type="text" name="topic_subject" class="form-control" placeholder="Enter topic subject"/><br>' .
        '<button type=button><b>Bold</b></button> <button type=button><i>Italic</i></button>  <button type=button><u>Under-line</u></button><br><br>' .
        '<textarea class="form-control" rows="8"  name="topic_content" id="comment" placeholder="Enter topic content"></textarea><br>' .
        '<input type="submit" value="Create new topic" id="submitLogin" class="btn btn-default" />
            </form><br>';
    }
} else {
    $cat_id = $_GET['id'];
    $user_name = ($_SESSION["logged"]);
    $topic_subject = $_POST['topic_subject'];
    $topic_content = $_POST['topic_content'];
    $is_valid = Utils::ValidateNewTopic($topic_subject, $topic_content, $user_name, $cat_id);
    if ($is_valid) {
        $topic_id = Utils::CreateNewTopic($topic_subject, $topic_content, $user_name, $cat_id);
        header('Location: topic_display.php?id=' . $topic_id . '&forum_id=' . $cat_id);
        
    } else {
        Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
    }
}
?>