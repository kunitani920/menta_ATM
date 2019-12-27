<?php
//ver2.本体（ver3）に合わせて、残高追加
//ver3.本体（ver4）に合わせて、クラス内に全て移動。ユーザー情報は初期値でセット。残高をmoney->balanceに変更
class User {
    public $user_list = array(
        1 => array(
            "id" => "1",
            "password" => "pass",
            "name" => "田中",
            "balance" => "500000"
        ),
        2 => array(
            "id" => "2",
            "password" => "word",
            "name" => "原",
            "balance" => "1000000"
        )
    );

    public function getUser() {
        return $this->user_list;
    }
}

?>