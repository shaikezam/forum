<?php

include_once 'dbmanager.php';

class Utils {

    const USER_NAME_MIN_LENGTH = 4;
    const USER_NOT_FOUND = 'Wrong log in details';
    const ADMIN = 'Admin';
    const DEFAULT_ERROR_MSG = 'An error occurred, please try again';

    public static function Logger($str) {
        echo $str;
    }

    public static function ValidateRegisterDetails($username, $user_pass, $user_pass_check, $user_email) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass === $user_pass_check && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            DBConnection::_executeQuery('INSERT INTO users VALUES (DEFAULT,"' . $username . '", "' . $user_pass . '", "' . $user_email . '", DEFAULT, "lo")');
            self::Logger('good user name');
            return true;
        } else {
            self::Logger('bad user name');
            return false;
        }
    }

    public static function ValidateLoginDetails($username, $user_pass) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass) {
            $res = DBConnection::_executeSelectQuery("SELECT * FROM users where user_name = '$username' and user_pass = '$user_pass'");
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

}

?>