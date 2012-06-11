<?php

/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 *
 * @license     MIT Licence
 * @category    Libraries
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @since       0.1.0
 * @version     0.5.0
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

    public function logged_in()
    {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    public function login($username_or_email, $password, $remember = false)
    {
        if(! is_object($username_or_email)) {
            $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username_or_email);
            $stmt->bindParam(':email', $username_or_email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_OBJ);
        }
        
        if(is_object($user) && ($user->email === $username_or_email || $user->username === $username_or_email) && $user->password === $password) {
            return $this->complete_login($user);
        }

        return false;
    }

    public function hash_password($password)
    {
        return md5($password);
    }

    protected function complete_login($user)
    {
        $user_info = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'logged_in' => true
        );

        return parent::complete_login($user_info);
    }
}
