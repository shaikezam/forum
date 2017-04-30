<?php

include_once 'dbmanager.php';

class Utils {

    const USER_NAME_MIN_LENGTH = 4;
    const USER_NOT_FOUND = 'Wrong log in details';
    const ADMIN = 'Admin';
    const DEFAULT_ERROR_MSG = 'An error occurred, please try again';
    const NOT_AUTHORIZED = 'Not authorized operation';
    const USER_EXISTS = 'There is user with those details';

    public static function Logger($str) {
        echo $str;
    }

    public static function ThrowErrorLog($str) {
        echo '<div class="alert alert-danger">' . $str . '</div>';
    }

    public static function ValidateRegisterDetails($username, $user_pass, $user_pass_check, $user_email) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass === $user_pass_check && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            return DBConnection::_executeQuery('INSERT INTO users VALUES (DEFAULT,"' . $username . '", "' . $user_pass . '", "' . $user_email . '", DEFAULT, "Admin")');
        } else {
            return false;
        }
    }

    public static function ValidateLoginDetails($username, $user_pass) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass) {
            $res = DBConnection::_executeSelectQuery("SELECT * FROM users where user_name like binary '$username' and user_pass like binary '$user_pass'");
            if ($res === false) {
                return false;
            } else {
                while ($row = mysqli_fetch_array($res)) { //send back result
                    if ($row['user_level'] === self::ADMIN) {
                        $_SESSION["user_level"] = self::ADMIN;
                    }
                }
                return true;
            }
        }
    }

    public static function createNewCategory($category_name, $category_description) {
        return DBConnection::_executeQuery('INSERT INTO categories VALUES (DEFAULT,"' . $category_name . '", "' . $category_description . '")');
    }

    public static function getCategories() {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM categories");
        if ($res === false) {
            return false;
        } else {
            $response = '<table class="table table-bordered"><thead><tr style="background-color:#a7acaf;"><th style = "width:70%;">Categories</th><th># of topics</th><th># of posts</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_array($res)) { //send back result
                    $response = $response . '<tr><td><img src="assets/Speech_balloon.svg" alt="Smiley face" height="42" width="42"><a href="forum_display.php?id=' . $row['cat_id'] . '"><strong> ' . $row['cat_name'] . '</strong></a><br>' . $row['cat_description'] . '</td>
                    <td>' . 6666 . '</td>
                    <td>' . 3333 . '</td>
                  </tr>';
            }
            $response = $response . '</tbody></table>';
            
            return $response;
        }
    }

}

?>