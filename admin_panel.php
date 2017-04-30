<?php

//signup.php
include 'dbmanager.php';
include 'header.php';
include 'footer.php';
include 'utils.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    if (isset($_SESSION["logged"]) && isset($_SESSION["user_level"])) {
        if ($_SESSION["user_level"] === Utils::ADMIN) {
            echo '<h3>Users management</h3>
            <h3>Categories management</h3>
            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo">New Category</button>
            <div id="demo" class="collapse"><br>
                <form method="post" action="">
                    Category name: <input type="text" name="category_name" class="form-control" /><br>
                    Category description: <input type="text" name="category_description" class="form-control" ><br>
                    <input type="submit" value="Submit" id="submitRegister" class="btn btn-default" />
               </form>
             </div>
            <h3>Topics management</h3>
            <h3>Posts management</h3>';
        }
    } else {
        Utils::ThrowErrorLog(Utils::NOT_AUTHORIZED);
    }
} else {
    if (isset($_POST['category_name']) && isset($_POST['category_description'])) {
        $category_name = $_POST['category_name'];
        $category_description = $_POST['category_description'];
        $res = Utils::createNewCategory($category_name, $category_description);
        if ($res !== true) {
            Utils::ThrowErrorLog($res);
        } else {
            header('Location: admin_panel.php');
        }
    }
}
?>