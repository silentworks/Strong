<?php

class StmtMock extends \PDOStatement {

    private $value;

    public function bindParam($name, $value) {
        $this->value = $value;
    }

    public function fetch() {
        if ($this->value === null)
            return null;
        return (object) array(
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin',
            'password' => '1a1dc91c907325c69271ddf0c944bc72',
        );
    }

}