<?php

namespace Strong\Provider;

class Hashtable extends \Strong\Provider
{
    /**
     * @var array
     */
    protected $users = array();

    /**
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($config)
    {
        parent::__construct($config);
        if (!array_key_exists('users', $config) || !is_array($config['users'])) {
            throw new \InvalidArgumentException('No declare users');
        }
        $this->users = $config['users'];
    }

    /**
     * @return bool
     */
    public function loggedIn()
    {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    /**
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return array|bool
     */
    public function login($username, $password, $remember = false)
    {

        if (!isset($this->users[$username])) {
            return false;
        }

        if ($this->users[$username] === $password) {
            return $this->completeLogin(
                array(
                    'username' => $username,
                    'logged_in' => true
                )
            );
        }
    }

}
