<?php

class DBConnection {

    protected static $server = 'localhost';
    protected static $user = 'root';
    protected static $password = '';
    protected static $db = 'myforum1';
    protected static $connectionString;

    /* setters & getters */

    public static function setServer($value) {
        self::$server = $value; //Works fine
    }

    public static function getServer() {
        return self::$server; //Works fine
    }

    public static function setUser($value) {
        self::$user = $value; //Works fine
    }

    public static function getUser() {
        return self::$user; //Works fine
    }

    public static function setPassword($value) {
        self::$password = $value; //Works fine
    }

    public static function getPassword() {
        return self::$password; //Works fine
    }

    public static function setDB($value) {
        self::$db = $value; //Works fine
    }

    public static function getDB() {
        return self::$db; //Works fine
    }

    /* get DB connection */

    public static function getDBConnection() {
        if (!self::$connectionString) {
            self::$connectionString = mysqli_connect(self::getServer(), self::getUser(), self::getPassword());

            if (!self::$connectionString) {
                die("Connection failed: ");
            }

            $db_selected = mysqli_select_db(self::$connectionString, self::getDB());
            if (!$db_selected) {
// If we couldn't, then it either doesn't exist, or we can't see it.
                $sql = 'CREATE DATABASE ' . self::getDB();
                if (self::$connectionString->query($sql) === TRUE) {
                    echo "Database " . self::getDB() . " created successfully\n";
                    self::$connectionString = mysqli_connect(self::getServer(), self::getUser(), self::getPassword(), self::getDB());
                    self::_initDataStructure();
                } else {
                    echo 'Error creating database: ' . mysqli_error() . "\n";
                }
            }
        }
        return self::$connectionString;
    }

    /* execute query */

    public static function _executeQuery($sql) {

        if (self::$connectionString == null) {
            self::getDBConnection();
        }
        $res = self::$connectionString->query($sql);
        if ($res === TRUE) {
            echo "query execute successfully\n"; //table created
        } else {
            echo "Error: " . self::$connectionString->error . '\n'; //error -> table wont created
        }
    }

    public static function _executeSelectQuery($sql) {

        if (self::$connectionString == null) {
            self::getDBConnection();
        }
        $result = mysqli_query(self::$connectionString, $sql);
        if (!$result) // error in query
            echo 'Invalid query: ' . mysqli_error(); //sending error message
        $numResults = mysqli_num_rows($result);
        if ($numResults == 0) {
            return false;
        } else {
            return $result;
        }
    }

    /* create table */

    private static function _initDataStructure() {
        self::_executeQuery('CREATE TABLE users (
    user_id     INT(8) NOT NULL AUTO_INCREMENT,
    user_name   VARCHAR(30) NOT NULL,
    user_pass   VARCHAR(255) NOT NULL,
    user_email  VARCHAR(255) NOT NULL,
    user_date   DATETIME NOT NULL DEFAULT NOW(),
    user_level  VARCHAR(255) NOT NULL,
    UNIQUE INDEX user_name_unique (user_name),
    PRIMARY KEY (user_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE categories (
    cat_id          INT(8) NOT NULL AUTO_INCREMENT,
    cat_name        VARCHAR(255) NOT NULL,
    cat_description     VARCHAR(255) NOT NULL,
    UNIQUE INDEX cat_name_unique (cat_name),
    PRIMARY KEY (cat_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE topics (
    topic_id        INT(8) NOT NULL AUTO_INCREMENT,
    topic_subject       VARCHAR(255) NOT NULL,
    topic_date      DATETIME NOT NULL,
    topic_cat       INT(8) NOT NULL,
    topic_by        INT(8) NOT NULL,
    PRIMARY KEY (topic_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE posts (
    post_id         INT(8) NOT NULL AUTO_INCREMENT,
    post_content        TEXT NOT NULL,
    post_date       DATETIME NOT NULL,
    post_topic      INT(8) NOT NULL,
    post_by     INT(8) NOT NULL,
    PRIMARY KEY (post_id)
) Engine=InnoDB');
//self::_executeQuery('ALTER TABLE ' . self::getDB() . ' topics ADD FOREIGN KEY(topic_cat) REFERENCES  ' . self::getDB() . ' categories(cat_id) ON DELETE CASCADE ON UPDATE CASCADE;');
//self::_executeQuery('ALTER TABLE ' . self::getDB() . ' topics ADD FOREIGN KEY(topic_by) REFERENCES  ' . self::getDB() . ' users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE;');
//self::_executeQuery('ALTER TABLE ' . self::getDB() . ' posts ADD FOREIGN KEY(post_topic) REFERENCES  ' . self::getDB() . ' topics(topic_id) ON DELETE CASCADE ON UPDATE CASCADE;');
//self::_executeQuery('ALTER TABLE ' . self::getDB() . ' posts ADD FOREIGN KEY(post_by) REFERENCES  ' . self::getDB() . ' users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE;');
    }

    /* close DB connection */

    public static function closeDBConnection() {
        if (self::$connectionString) {
            mysqli_close(self::$connectionString);
        }
    }

}

?>