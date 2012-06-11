<?php

/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 *
 * @license     MIT Licence
 * @category    Driver
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @since       1.0.0
 * @version     1.0.0
 */
class Strong_Driver_PDO extends Strong_Driver
{
    protected $settings = array(
        'dsn' => '',
        'dbuser' => null,
        'dbpass' => null,
    );

    public function __construct($config)
    {
        parent::__construct($config);
        $this->config = array_merge($this->settings, $this->config);

        try {
            $this->pdo = new PDO($this->config['dsn'], $this->config['dbuser'], $this->config['dbpass']);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function loggedIn()
    {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    public function login($usernameOrEmail, $password, $remember = false)
    {
        if(! is_object($usernameOrEmail)) {
            $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $usernameOrEmail);
            $stmt->bindParam(':email', $usernameOrEmail);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_OBJ);
        }
        
        if(is_object($user) && ($user->email === $usernameOrEmail || $user->username === $usernameOrEmail) && $user->password === $password) {
            return $this->completeLogin($user);
        }

        return false;
    }

    public function hashPassword($password)
    {
        return md5($password);
    }

    protected function completeLogin($user)
    {
        $userInfo = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'logged_in' => true
        );

        return parent::completeLogin($userInfo);
    }
}
