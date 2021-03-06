<?php

ob_start();
session_start();
include_once 'dbmanager.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'utils.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    if (isset($_COOKIE['myforum'])) {
        if (isset($_COOKIE["user_level"]) && $_COOKIE["user_level"] === Utils::ADMIN) {
            echo ' <a href="admin_panel.php">Admin panel </a>';
        }
        echo '<a href="control_panel.php">Control panel </a><a href="logout.php">log out</a>';

        /* general suer data */
        $user_id = Utils::getUserIDByName($_COOKIE['myforum']);
        $data = Utils::getUserData($user_id);

        if (!$data) {
            Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
        } else {
            echo $data;
        }
        $data = Utils::getUserData($user_id, true);
        echo '<hr class="post-hr">';

        /* Update Profile */
        echo '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#updateProfile">Update Profile</button>
            <div id="updateProfile" class="collapse control-panel-section"><br>
                <form method="post" action="">
                    Email: (' . $data['user_email'] . '): <input type="email" name="email_input" class="form-control" /><br>
                    Location: (' . $data['user_location'] . '): <input type="text" name="location_input" class="form-control" ><br>
                        <input type="submit" value="Submit" id="submitRegister" class="btn btn-default" name="update_profile"/>
               </form>
             </div><br><br>';

        /* Change Password */
        echo '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#changePassword">Change Password</button>
            <div id="changePassword" class="collapse control-panel-section"><br>
                <form method="post" action="">
                    Category name: <input type="text" name="category_name" class="form-control" /><br>
                    Category description: <input type="text" name="category_description" class="form-control" ><br>
                    <input type="submit" value="Submit" id="submitRegister" class="btn btn-default" />
               </form>
             </div><br><br>';

        /* Change Signature */
        /* echo '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#changeSignature">Change Signature</button>
          <div id="changeSignature" class="collapse control-panel-section"><br>
          <form method="post" action="">
          <button class="btn btn-default signature-bold" type=button><b>Bold</b></button>
          <button class="btn btn-default signature-italic" type=button><i>Italic</i></button>
          <button class="btn btn-default signature-under-line" type=button><u>Under-line</u></button><br><br>
          <textarea class="form-control change-signature-textarea" rows="8"  name="post_content" id="comment" placeholder=""></textarea><br>
          <input type="submit" value="Submit" id="hideCategories" class="btn btn-default" />
          </form>
          </div>'; */
        /* Change Avatar */
        echo '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#changeAvatar">Change Avatar</button>
            <div id="changeAvatar" class="collapse control-panel-section"><br>
                <i><b>Supported formats:</b> JPEG, JPG, GIF, PNG<br><b>Max length and width:</b> 100px on 100px</i><br><b>Max size:</b> 2MB</i><br><br>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload" id="fileToUpload" class="btn btn-default"><br>
                    <input type="submit" value="Upload Image" name="upload_file" class="btn btn-default"><br>
                </form>
             </div>';
    } else {
        Utils::ThrowErrorLog(Utils::NOT_AUTHORIZED);
    }
} else {
    if (!isset($_COOKIE['myforum'])) {
        header('Location: index.php');
    }
    if (isset($_POST['update_profile'])) { /* Update profle */
        $new_email = $_POST['email_input'];
        $new_location = $_POST['location_input'];
        if ($new_email === '' && $new_location === '') {
            Utils::ThrowErrorLog(Utils::EMPTY_FIELDS);
        } if ($new_email !== '') {
            $res = Utils::updateUserEmail();
        } if ($new_location !== '') {
            $res = Utils::updateuserLocation();
        }
        /* $category_name = $_POST['category_name'];
          $category_description = $_POST['category_description'];
          $res = Utils::createNewCategory($category_name, $category_description);
          if ($res !== true) {
          Utils::ThrowErrorLog($res);
          } else {
          header('Location: index.php');
          } */
    } else if (isset($_POST['upload_file'])) { /* Change avatar */
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
            if (!($imageFileType === "jpg" || $imageFileType === "png" || $imageFileType === "jpeg" || $imageFileType === "gif")) {
                Utils::ThrowErrorLog(Utils::INVALID_FORMAT);
                return;
            }
            $file_size_in_bytes = $_FILES['fileToUpload']['size'];
            if ($file_size_in_bytes > 65000) {
                Utils::ThrowErrorLog(Utils::INVALID_SIZE);
                return;
            }
            /* if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
              header('Location: index.php');
              } else {
              echo "Sorry, there was an error uploading your file.";
              } */
            $image = addslashes(file_get_contents($_FILES['fileToUpload']['tmp_name']));
            $res = Utils::setImageToUser($_COOKIE['myforum'], $image);
            if ($res !== true) {
                Utils::ThrowErrorLog(Utils::DEFAULT_ERROR_MSG);
            } else {
                header('Location: index.php');
            }
        } else {
            Utils::ThrowErrorLog(Utils::NOT_IMAGE);
            $uploadOk = 0;
        }
    }
}
?>