<?php

class StmtMock extends \PDOStatement {

    private $value;

    public function bindParam($paramno, &$param, $type = NULL, $maxlen = NULL, $driverdata = NULL) {
        $this->value = $param;
    }

    public function fetch($how = NULL, $orientation = NULL, $offset = NULL) {
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