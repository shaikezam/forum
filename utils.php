<?php

include_once 'dbmanager.php';

class Utils {

    const USER_NAME_MIN_LENGTH = 4;
    const USER_NOT_FOUND = 'Wrong log in details';
    /* useres levels */
    const ADMIN = 'Admin';
    const REGULAR = 'Regular';

    /* messages */
    const DEFAULT_ERROR_MSG = 'An error occurred, please try again';
    const NOT_AUTHORIZED = 'Not authorized operation';
    const USER_EXISTS = 'There is user with those details';
    const DUPLICATE_POST = 'Please prevent from posting duplicate posts';
    const TOPICS_NOT_FOUND = "There isn't topics in this category";
    const CATEGORIES_NOT_FOUND = "There isn't categories";
    const INVALID_FORMAT = "Not supported format";
    const INVALID_SIZE = "Image weighs more than 65 KB";
    const NOT_IMAGE = "File is not an image";
    const TOPIC_EMPTY = "New topic must have subject and content";
    const POST_EMPTY = "New post must have content";
    const EMPTY_FIELDS = 'All fields are empty, try again with data';
    const CHOOSE_CATEGORY = 'Choose Category';
    const VISIBLE = 'Visible';
    const HIDDEN = 'Hidden';

    public static function Logger($str) {
        echo $str;
    }

    public static function ThrowErrorLog($str) {
        echo '<div class="alert alert-danger">' . $str . '</div>';
    }

    /* validate min length of user name, passowrd = password check, user email in email pattern */
    public static function ValidateRegisterDetails($username, $user_pass, $user_pass_check, $user_email, $user_location) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass === $user_pass_check && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            return DBConnection::_executeQuery('INSERT INTO users VALUES (DEFAULT,"' . $username . '", "' . $user_pass . '", "' . $user_email . '", DEFAULT, "Regular", DEFAULT, DEFAULT,"' . $user_location . '")');
        } else {
            return false;
        }
    }

    /* validate uesr name and pass wrod ar the same row and exists */
    public static function ValidateLoginDetails($username, $user_pass) {
        if (strlen($username) > self::USER_NAME_MIN_LENGTH && $user_pass) {
            $res = DBConnection::_executeSelectQuery("SELECT * FROM users where user_name like binary '$username' and user_pass like binary '$user_pass'");
            if ($res === false) {
                return false;
            } else {
                while ($row = mysqli_fetch_array($res)) { //send back result
                    setcookie('user_level', $row['user_level'], time() + 31556926, '/');
                }
                return true;
            }
        }
    }

    /* create new category */
    public static function createNewCategory($category_name, $category_description) {
        //return DBConnection::_executeQuery('INSERT INTO categories VALUES (DEFAULT,"' . $category_name . '", "' . $category_description . ', DEFAULT")');
        return DBConnection::_executeQuery('INSERT INTO `categories`(`cat_id`, `cat_name`, `cat_description`, `cat_visible`) VALUES (DEFAULT,"' . $category_name . '","' . $category_description . '", DEFAULT)');
    }

    /* get categories in HTML format */
    public static function getCategories() {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM categories WHERE cat_visible = 1");
        if ($res === false) {
            $res = array();
            $res['status'] = 'Error';
            $res['message'] = self::CATEGORIES_NOT_FOUND;
            return $res;
        } else {
            $response = '<table class="table table-bordered"><thead><tr style="background-color:#a7acaf;"><th style = "width:70%;">Categories</th><th># of topics</th><th># of posts</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_array($res)) { //send back result
                $num_of_topics = self::_getNumberOfTopics($row['cat_id']);
                $num_of_posts = self::_getNumberOfPostsByCategory($row['cat_id']);
                $response = $response . '<tr><td><img src="assets/Speech_balloon.svg" alt="Smiley face" height="42" width="42"><a href="forum_display.php?id=' . $row['cat_id'] . '"><strong> ' . $row['cat_name'] . '</strong></a><br>' . $row['cat_description'] . '</td>
                    <td>' . $num_of_topics . '</td>
                    <td>' . $num_of_posts . '</td>
                  </tr>';
            }
            $response = $response . '</tbody></table>';

            return $response;
        }
    }

    public static function getTopics($cat_id) {
        $stat = "SELECT DISTINCT topics.topic_id, topics.topic_subject, topics.topic_date, topics.topic_by, posts.post_date FROM topics, posts
            WHERE topic_cat = '" . $cat_id . "' and topic_id = post_topic and posts.post_date = (SELECT post_date FROM posts WHERE post_topic = topics.topic_id ORDER BY post_id DESC LIMIT 1)
            order by post_date DESC";

        $res = DBConnection::_executeSelectQuery($stat);
        if ($res === false) {
            $res = array();
            $res['status'] = 'Error';
            $res['message'] = self::TOPICS_NOT_FOUND;
            return $res;
        } else {
            $response = '<table class="table table-bordered forum-display"><thead><tr style="background-color:#a7acaf;"><th style = "width:70%;">Topic</th><th># of posts</th><th>Last post</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_array($res)) { //send back result
                foreach ($row as $key => $value) {
                    //echo "$key is at $value <br>";
                }
                $user_name = self::_getUserByID($row['topic_by']);
                $num_of_posts = self::_getNumberOfPostsByTopic($row['topic_id']);
                $topic_last_post_details = self::_getLastPostDetails($row['topic_id']);
                $response = $response . '<tr><td><img src="assets/Speech_balloon.svg" alt="Smiley face" height="42" width="42"><a href="topic_display.php?id=' . $row['topic_id'] . '&forum_id=' . $cat_id . '"><strong> ' . $row['topic_subject'] . '</strong></a><br>Created by: ' . $user_name . '<br> ' . $row['topic_date'] . '</td>
                    <td>' . $num_of_posts . '</td>
                    <td>' . $topic_last_post_details['post_date'] . '<br>' . $topic_last_post_details['user_name'] . '</td>
                  </tr>';
            }
            $response = $response . '</tbody></table>';

            return $response;
        }
    }

    public static function getPosts($cat_id, $topic_id) {

        $res = DBConnection::_executeSelectQuery("SELECT DISTINCT topics.topic_subject, posts.post_by, posts.post_date, posts.post_content FROM posts, topics WHERE post_topic = '" . $topic_id . "' and topics.topic_cat = '" . $cat_id . "' and topics.topic_id = '" . $topic_id . "'");
        if ($res === false) {
            return false;
        } else {
            $b_print_subject_once = true;
            $tmpString = '<table class="table table-bordered"><thead><tr style="background-color:#a7acaf;"><th style = "width:20%;">User</th><th>Post content</th></tr></thead><tbody>';
            $response = "<h3>";
            while ($row = mysqli_fetch_array($res)) { //send back result
                if ($b_print_subject_once) {
                    $b_print_subject_once = false;
                    $response = $response . $row['topic_subject'] . '</h3>' . $tmpString;
                }
                $data = self::_getUserData($row['post_by']);
                $num_of_posts = self::_getNumberOfPostsByUserId($row['post_by']);
                $user_name = $data['user_name'];
                $user_signature = $data['user_signature'];
                $user_location = $data['user_location'];
                $user_avatar = $data['user_avatar'];
                $response = $response . '<tr><td><a href="user_display.php?id=' . $row['post_by'] . '"><strong> ' . $user_name . '</strong></a><br><img style="max-width:100px;max-height:100px" src = "data:image/png;base64,' . base64_encode($user_avatar) . '"/><br>Location: ' . $user_location . '</a><br># of posts: ' . $num_of_posts . '<br>Posted in: ' . $row['post_date'] . '</td>
                    <td>' . $row['post_content'];
                //$response = $response . '<br><hr class="post-hr">' . $user_signature . '</td></tr>';
            }
            $response = $response . '</tbody></table>';

            return $response;
        }
    }

    private static function _getNumberOfTopics($cat_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM topics WHERE topic_cat = '" . $cat_id . "'");
        if ($res === false) {
            return 0;
        } else {
            return mysqli_num_rows($res);
        }
    }

    private static function _getNumberOfPostsByCategory($cat_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM posts, topics WHERE post_topic = topic_id and topic_cat = '" . $cat_id . "'");
        if ($res === false) {
            return 0;
        } else {
            return mysqli_num_rows($res);
        }
    }

    private static function _getNumberOfPostsByTopic($topic_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM posts WHERE post_topic = '" . $topic_id . "'");
        if ($res === false) {
            return 0;
        } else {
            return mysqli_num_rows($res);
        }
    }

    private static function _getLastPostDetails($topic_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM posts WHERE post_topic = '" . $topic_id . "' ORDER BY post_id DESC LIMIT 1");
        if ($res === false) {
            return 0;
        } else {
            while ($row = mysqli_fetch_array($res)) { //send back result
                $data = array();
                $data['post_date'] = $row['post_date'];
                $data['user_name'] = self::_getUserByID($row['post_by']);
                return $data;
            }
        }
    }

    private static function _getUserByID($user_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM users WHERE user_id = '" . $user_id . "'");
        while ($row = mysqli_fetch_array($res)) { //send back result
            return $row['user_name'];
        }
    }

    private static function _getUserSignatureByID($user_id) {
        $res = DBConnection::_executeSelectQuery("SELECT user_signature FROM users WHERE user_id = '" . $user_id . "'");
        while ($row = mysqli_fetch_array($res)) { //send back result
            return $row['user_signature'];
        }
    }

    private static function _getUserAvatarByID($user_id) {
        $res = DBConnection::_executeSelectQuery("SELECT user_avatar FROM users WHERE user_id = '" . $user_id . "'");
        while ($row = mysqli_fetch_array($res)) { //send back result
            return $row['user_avatar'];
        }
    }

    public static function getUserIDByName($user_name) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM users WHERE user_name = '" . $user_name . "'");
        while ($row = mysqli_fetch_array($res)) { //send back result
            return $row['user_id'];
        }
    }

    public static function getUserData($user_id, $bFormatted = false) {
        $num_of_posts = self::_getNumberOfPostsByUserId($user_id);
        $data = self::_getUserData($user_id);
        if (!$data) {
            return false;
        }
        if ($bFormatted) {
            return $data;
        }
        return '<h3>' . $data['user_name'] . '</h3><img style="max-width:100px;max-height:100px" src = "data:image/png;base64,' . base64_encode($data['user_avatar']) . '"/><br><strong>User mail:</strong> ' . $data['user_email'] . '<br><strong>Registration Date:</strong> ' . $data['user_date'] . '<br><strong>Level:</strong> ' . $data['user_level'] . '<br><strong># of posts:</strong> ' . $num_of_posts;
    }

    public static function setImageToUser($user_name, $image) {
        $user_id = self::getUserIDByName($user_name);
        return DBConnection::_executeQuery("UPDATE `users` SET `user_avatar` = '{$image}'  WHERE `user_id` =  '$user_id'");
    }

    private static function _getNumberOfPostsByUserId($user_id) {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM posts WHERE post_by = '" . $user_id . "'");
        if ($res === false) {
            return 0;
        } else {
            return mysqli_num_rows($res);
        }
    }

    private static function _getUserData($user_id) {
        $data = array();
        $res = DBConnection::_executeSelectQuery("SELECT * FROM users WHERE user_id = '" . $user_id . "'");
        if ($res === false) {
            return 0;
        } else {
            while ($row = mysqli_fetch_array($res)) { //send back result
                $data['user_email'] = $row['user_email'];
                $data['user_date'] = $row['user_date'];
                $data['user_level'] = $row['user_level'];
                $data['user_name'] = $row['user_name'];
                $data['user_location'] = $row['user_location'];
                $data['user_signature'] = $row['user_signature'];
                $data['user_avatar'] = $row['user_avatar'];
                return $data;
            }
        }
    }

    public static function ValidateNewTopic($topic_subject, $topic_content, $user_name, $cat_id) {
        $return_obj = array("status" => false, "message" => self::TOPIC_EMPTY);
        if ($topic_subject != "" && $topic_content != "") {
            $return_obj["status"] = true;
        }
        return $return_obj;
    }

    /* Checking last message is not posted by the same user (prevent spam and double posts - except Admins) */

    public static function ValidateNewPost($post_content, $user_name, $topic_id) {
        $return_obj = array("status" => false, "message" => self::DUPLICATE_POST);
        $res = DBConnection::_executeSelectQuery("SELECT * FROM posts where post_topic = '" . $topic_id . "' ORDER BY post_id DESC LIMIT 1");
        if ($res === false) {
            $return_obj["message"] = self::DEFAULT_ERROR_MSG;
        } else if ($post_content == "") {
            $return_obj["message"] = self::POST_EMPTY;
        } else {
            $current_user_id = self::getUserIDByName($user_name);
            $row = mysqli_fetch_array($res);
            $last_post_user_id = $row['post_by'];
            if ($current_user_id == $last_post_user_id) {
                return $return_obj;
            }
            $return_obj["status"] = true;
        }
        return $return_obj;
    }

    public static function CreateNewTopic($topic_subject, $topic_content, $user_name, $cat_id) {
        $topic_content = nl2br($topic_content);
        $res = DBConnection::_executeSelectQuery("SELECT * FROM users where user_name = '$user_name' limit 1");
        if ($res === false) {
            return false;
        } else {
            while ($row = mysqli_fetch_array($res)) {
                $user_id = $row['user_id'];
                DBConnection::_executeQuery('INSERT INTO `topics`(`topic_id`, `topic_subject`, `topic_date`, `topic_cat`, `topic_by`) VALUES (DEFAULT,"' . $topic_subject . '",DEFAULT,"' . $cat_id . '","' . $user_id . '")');
                $topic_id = DBConnection::getLastInsertID();
                self::_CreateNewPost($topic_content, $user_id, $topic_id);
                return $topic_id;
            }
        }
    }

    public static function CreateNewPost($post_content, $user_name, $topic_id) {
        $post_content = nl2br($post_content);
        $res = DBConnection::_executeSelectQuery("SELECT * FROM users where user_name = '$user_name' limit 1");
        if ($res === false) {
            return false;
        } else {
            while ($row = mysqli_fetch_array($res)) {
                $user_id = $row['user_id'];
                self::_CreateNewPost($post_content, $user_id, $topic_id);
                return $topic_id;
            }
        }
    }

    public static function _CreateNewPost($post_content, $user_id, $topic_id) {
        return DBConnection::_executeQuery('INSERT INTO `posts`(`post_id`, `post_content`, `post_date`, `post_topic`, `post_by`) VALUES (DEFAULT,"' . $post_content . '",DEFAULT,"' . $topic_id . '","' . $user_id . '")');
    }

    public static function getCategoriesForAdminPanel() {
        $res = DBConnection::_executeSelectQuery("SELECT * FROM categories");
        if ($res === false) {
            return false;
        } else {
            $response = Array();
            while ($row = mysqli_fetch_array($res)) { //send back result
                $response[$row['cat_id']] = $row['cat_name'];
            }
            return $response;
        }
    }

    public static function hiddenCategory($category_id, $category_action) {
        if ($category_action == self::VISIBLE) {
            $flag = TRUE;
        } else if ($category_action == self::HIDDEN) {
            $flag = FALSE;
        } else {
            return FALSE;
        }
        return DBConnection::_executeQuery("UPDATE `categories` SET `cat_visible` = '$flag'  WHERE `cat_id` =  '$category_id'");
    }

    public static function logOut() {
        if (isset($_COOKIE['myforum'])) {
            unset($_COOKIE['myforum']);
            setcookie('myforum', null, -1, '/');
        }
        if (isset($_COOKIE['user_level'])) {
            unset($_COOKIE['user_level']);
            setcookie('user_level', null, -1, '/');
        }
    }

}

?>