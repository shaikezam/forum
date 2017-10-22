<?php
ob_start();
session_start();
include_once 'dbmanager.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'utils.php';


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    if (isset($_SESSION["logged"]) && isset($_SESSION["user_level"])) {
        if ($_SESSION["user_level"] === Utils::ADMIN) {
            echo '<h3>Users management</h3>
            <h3>Categories management</h3>
            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#newCategory">New Category</button>
            <div id="newCategory" class="collapse"><br>
                <form method="post" action="">
                    Category name: <input type="text" name="category_name" class="form-control" /><br>
                    Category description: <input type="text" name="category_description" class="form-control" ><br>
                    <input type="submit" value="Submit" id="submitRegister" class="btn btn-default" />
               </form>
             </div><br><br>
             <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#hideCategory">Hide Category</button>
            <div id="hideCategory" class="collapse"><br>
                <form method="post" action="">
                    <select class="form-control" name="categoryId">
                      <option selected>Choose category</option>';
                        $categories = Utils::getCategoriesForAdminPanel();
                        foreach($categories as $category_id => $category_name):
                            echo '<option value="'.$category_id.'">'.$category_name.'</option>'; //close your tags!!
                        endforeach;
                    echo '</select><br>
                    <select class="form-control" name="categoryAction">
                      <option selected>Choose visibility</option>
                      <option>Visible</option>
                      <option>Hidden</option>
                    </select><br>
                    <input type="submit" value="Submit" id="hideCategories" class="btn btn-default" />
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
            header('Location: index.php');
        }
    } else if(isset($_POST['categoryId']) && isset($_POST['categoryAction'])) {
        $category_id =  $_POST['categoryId'];
        $category_action = $_POST['categoryAction'];
        $res = Utils::hiddenCategory($category_id, $category_action);
        if ($res !== true) {
            Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
        } else {
            header('Location: index.php');
        }
    }
}
?>