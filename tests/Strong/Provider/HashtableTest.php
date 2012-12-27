<?php

use Strong\Strong;

class Strong_Provider_HashtableTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->provider = new Strong(array(
            'name' => 'hashtableTest',
            'provider' => 'Hashtable',
            'users' => array('admin' => 'pass')
        ));
        $_SESSION['auth_user'] = null;
    }

    public function tearDown() {
        $_SESSION['auth_user'] = null;
    }

    public function testCreateInstance() {
        $this->assertInstanceOf('\Strong\Provider', $this->provider->getProvider());
    }

    public function testCreateInstanceInvalid() {
        $this->setExpectedException('\InvalidArgumentException', 'No declare users');
        $strong = new Strong(array(
            'name' => 'hashtableTestInvalid',
            'provider' => 'Hashtable',
        ));
    }

    public function testCreateInstanceUsersNotArray() {
        $this->setExpectedException('\InvalidArgumentException', 'No declare users');
        $strong = new Strong(array(
            'name' => 'hashtableTestInvalid2',
            'provider' => 'Hashtable',
            'users' => 'test',
        ));
    }

    public function testCheckNotLogin() {
        $this->assertFalse($this->provider->loggedIn());
    }

    public function testLoginNonExistsUser() {
        $this->assertFalse($this->provider->login('adminTest', 'pass'));
    }

    public function testLoginInvalid() {
        $this->assertFalse($this->provider->login('admin', 'testInvalidPass'));
    }

    public function testLoginValid() {
        $this->assertTrue($this->provider->login('admin', 'pass'));
    }

    public function testLogout() {
        $this->provider->login('admin', 'pass');
        $this->provider->logout();
        $this->assertEquals(null, $this->provider->getUser());
    }


}