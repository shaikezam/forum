<?php

class Utils {

    public static function Logger($str) {
        echo $str;
    }

    public static function ValidateRegisterDetails($username, $user_pass, $user_pass_check, $user_email) {
        if (strlen($username) > 5 && $user_pass === $user_pass_check) {
            self::Logger('good user name');
            return true;
        } else {
            self::Logger('bad user name');
            return false;
        }
    }

}
?>
