<?php

class MenuValidation {
    private $error_messages = array();
    public function check($input) {
        if($input === '') {
            $msg = 'エラー！入力が確認出来ませんでした。';
            $this->addErrorMessage($msg);
        }

        if(!is_numeric($input)) {
            $msg = 'エラー！メニューの番号を入力してください。';
            $this->addErrorMessage($msg);
        }elseif(!Atm::ATM_MENU[$input]) {
            $msg = 'エラー！入力された番号のメニューはありません。';
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