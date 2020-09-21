<?php


namespace App;


class Auth {

    private $user = null;

    private static $instance = null;

    /**
     * @return Auth
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    /**
     * @param $username
     * @param $password
     * @return boolean
     */
    public function login($username, $password) {
        $req = Database::getInstance()->prepare('SELECT * FROM users WHERE username = ?');
        $req->execute([
            $username
        ]);
        $user = $req->fetch();
        if($user) {
            if(password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                return true;
            }
        }
        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
        $this->user = null;
    }

    /**
     * @return boolean
     */
    public function isLoggedIn() {
        return $this->getUser() !== false;
    }

    /**
     * @return User|false
     */
    public function getUser() {
        if(!$this->user) {
            if(isset($_SESSION['user'])) {
                $this->user = new User($_SESSION['user']['id'], $_SESSION['user']['username'], $_SESSION['user']['role'], $_SESSION['user']['status']);
            } else {
                return false;
            }
        }
        return $this->user;
    }

    /**
     * @param string $role ROLE_COLLABORATOR ou ROLE_ADMIN
     */
    public static function restricted($role = ROLE_COLLABORATOR) {
        if(!self::getInstance()->isLoggedIn()) {
            redirect('login.php');
        }
    }

}