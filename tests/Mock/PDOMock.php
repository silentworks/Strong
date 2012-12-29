<?php

class PDOMock extends PDO {

    public function __construct () {

    }

    public function prepare() {
        return new StmtMock();
    }
}