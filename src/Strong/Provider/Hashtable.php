<?php

namespace Strong\Provider;

class Hashtable extends \Strong\Provider
{
    protected $users = array();

    public function __construct($config)
    {
        parent::__construct($config);
        if (!array_key_exists('users', $config) || !is_array($config['users']))
            throw new \InvalidArgumentException('No declare users');
        $this->users = $config['users'];
    }

    public function loggedIn() {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    public function login($username, $password) {

        if(!isset($this->users[$username]))
            return false;

        if($this->users[$username] === $password)
            return $this->completeLogin(array(
                'username' => $username,
                'logged_in' => true
            ));

        return false;
    }

}
