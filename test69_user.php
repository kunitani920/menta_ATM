<?php
const ID = 'id';
const NAME = 'name';
const PASS = 'pass';

class User {
    private $user = array();
    private $id = '';
    private $name = '';
    private $pass = '';

    public function setId(string $id) {
        $this->user[ID] = $id;
    }

    public function setName(string $name) {
        $this->user[NAME] = $name;
    }

    public function setPass(string $pass) {
        $this->user[PASS] = $pass;
    }

    public function getUser() {
        return $this->user;
    }
}

?>