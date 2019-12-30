<?php
/*
69 ATMを作成しよう
コマンドラインから実行すること
要件定義
・残額、入金、引き出しの機能を実装
実際にATMに必要な機能をリストアップして、ご自由に開発してみてください！
*/
const ID = 'id';
const NAME = 'name';
const PASS = 'pass';

class Atm {
    private $id = '';
    private $pass = '';
    public static $error_count = 0;

    public function __construct() {
        echo '青空銀行へようこそ。' . PHP_EOL;
    }
    
    public function setId() {
        echo 'ユーザーIDを入力してください。' . PHP_EOL;
        $this->id = trim(fgets(STDIN));
    }
    
    public function setPass() {
        echo 'パスワードを入力してください。' . PHP_EOL;
        $this->pass = trim(fgets(STDIN));
    }

    public function collation($user) {
        if($this->id === $user[ID] && $this->pass === $user[PASS]) {
            return true;
        }
        return false;
    }
}

?>