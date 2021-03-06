<?php
include_once 'utils.php';
class DBConnection {

    protected static $server = 'localhost';
    protected static $user = 'root';
    protected static $password = '';
    protected static $db = 'myforum';
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
            if (!$db_selected) { /* If data base not found we create it */
                $sql = 'CREATE DATABASE ' . self::getDB();
                if (self::$connectionString->query($sql) === TRUE) {
                    echo "Database " . self::getDB() . " created successfully\n";
                    self::$connectionString = mysqli_connect(self::getServer(), self::getUser(), self::getPassword(), self::getDB());
                    self::_initDataStructure();
                } else {
                    echo 'Error creating database: ' . mysqli_error() . "\n";
                }
            } else { /* If data base found we create data */
                self::$connectionString = mysqli_connect(self::getServer(), self::getUser(), self::getPassword(), self::getDB());
                mysqli_query(self::$connectionString, "use" . self::getDB());
                $result = mysqli_query(self::$connectionString, "show tables");
                $numResults = mysqli_num_rows($result);
                if ($numResults === 0) {
                    self::_initDataStructure();
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
            return true; //table created
        } else {
            return self::$connectionString->error; //error -> table wont created
        }
    }

    public static function _executeSelectQuery($sql) {

        if (self::$connectionString == null) {
            self::getDBConnection();
        }
        $result = mysqli_query(self::$connectionString, $sql);
        if (!$result) { // error in query
            echo 'Invalid query: ' . mysqli_error(); //sending error message
        }
        $numResults = mysqli_num_rows($result);
        if ($numResults == 0) {
            return false;
        } else {
            return $result;
        }
    }

    public static function getLastInsertID() {
        if (self::$connectionString == null) {
            self::getDBConnection();
        }
        return mysqli_insert_id(self::$connectionString);
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
    user_signature TEXT DEFAULT "",
    user_avatar BLOB,
    user_location VARCHAR(255) DEFAULT "",
    UNIQUE INDEX user_name_unique (user_name),
    UNIQUE INDEX user_email_unique (user_email),
    PRIMARY KEY (user_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE categories (
    cat_id          INT(8) NOT NULL AUTO_INCREMENT,
    cat_name        VARCHAR(255) NOT NULL,
    cat_description     VARCHAR(255) NOT NULL,
    cat_visible BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE INDEX cat_name_unique (cat_name),
    PRIMARY KEY (cat_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE topics (
    topic_id        INT(8) NOT NULL AUTO_INCREMENT,
    topic_subject       VARCHAR(255) NOT NULL,
    topic_date      DATETIME NOT NULL DEFAULT NOW(),
    topic_cat       INT(8) NOT NULL,
    topic_by        INT(8) NOT NULL,
    PRIMARY KEY (topic_id)
) Engine=InnoDB');

        self::_executeQuery('CREATE TABLE posts (
    post_id         INT(8) NOT NULL AUTO_INCREMENT,
    post_content        TEXT NOT NULL,
    post_date       DATETIME NOT NULL DEFAULT NOW(),
    post_topic      INT(8) NOT NULL,
    post_by     INT(8) NOT NULL,
    PRIMARY KEY (post_id)
) Engine=InnoDB');
        /* Create admin user and regular user for testing */
        self::_executeQuery('INSERT INTO users VALUES (DEFAULT, "Admin", "Admin1234", "shaike.zam@gmail.com", DEFAULT, "Admin", "", null, "IL")');
        self::_executeQuery('INSERT INTO users VALUES (DEFAULT, "Shaike", "Shaike1234", "babababa360@gmail.com", DEFAULT, "Regular", "", null, "Tel-Aviv")');

        self::_executeQuery('ALTER TABLE ' . self::getDB() . ' topics ADD FOREIGN KEY(topic_cat) REFERENCES  ' . self::getDB() . ' categories(cat_id) ON DELETE CASCADE ON UPDATE CASCADE;');
        self::_executeQuery('ALTER TABLE ' . self::getDB() . ' topics ADD FOREIGN KEY(topic_by) REFERENCES  ' . self::getDB() . ' users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE;');
        self::_executeQuery('ALTER TABLE ' . self::getDB() . ' posts ADD FOREIGN KEY(post_topic) REFERENCES  ' . self::getDB() . ' topics(topic_id) ON DELETE CASCADE ON UPDATE CASCADE;');
        self::_executeQuery('ALTER TABLE ' . self::getDB() . ' posts ADD FOREIGN KEY(post_by) REFERENCES  ' . self::getDB() . ' users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE;');
        self::_executeQuery("INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_description`, `cat_visible`) VALUES
        (1, 'General Discussion', 'The place for general discussions that has no categories', 1),
        (2, 'Forum-related technical issues', 'Found a bug or a technical issue in the forums? let us know', 1),
        (3, 'Games', 'The place to write review on a game or search for friends online', 1),
        (4, 'Movies & Series', 'Have you seen a nice movie or a series addict? Share us', 1),
        (5, 'Photography & Graphics', 'Share with us us your photos or artwork', 1),
        (6, 'Gadgets and technology', 'You bought a new TV? Want to consult before buying a computer? New device you found on eBay? Share with us', 1);");
        
        Utils::logOut();
        
    }

    /* close DB connection */

    public static function closeDBConnection() {
        if (self::$connectionString) {
            mysqli_close(self::$connectionString);
        }
    }

}

?>