<?php

class IdValidation {
    private $error_messages = array();
    public function check($input) {
        if($input === '') {
            $msg = 'エラー！入力が確認出来ませんでした。';
            $this->addErrorMessage($msg);
        }
        
        if (!is_numeric($input)) {
            $msg = 'エラー！IDは数字です。';
            $this->addErrorMessage($msg);
        }

        //ユーザーリストに存在するidかチェック
        if(!User::checkUserList($input)) {
            $msg = 'エラー！そのIDは存在しません。';
            $this->addErrorMessage($msg);
        }

        //エラーがある場合
        if($msg) {
            return false;
        }

        return true;
    }

    public function addErrorMessage($msg) {
        $this->error_messages[] = $msg;
        return;
    }

    public function getErrorMessages() {
        return $this->error_messages;
    }
}

?>