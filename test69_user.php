<?php
//ver2.本体（ver3）に合わせて、残高追加
const ID = 'id';
const PASS = 'pass';
const NAME = 'name';
const MONEY = 'money';

class User {
    private $user = array();
    private $id = '';
    private $pass = '';
    private $name = '';
    private $money = '';

    public function setId(string $id) {
        $this->user[ID] = $id;
    }
    
    public function setPass(string $pass) {
        $this->user[PASS] = $pass;
    }
    
    public function setName(string $name) {
        $this->user[NAME] = $name;
    }

    public function setMoney(string $money) {
        $this->user[MONEY] = $money;
    }

    public function getUser() {
        return $this->user;
    }
}

?>