<?php
//ver2.BaseValidationを継承。
require_once 'BaseValidation.php';

class MenuValidation extends BaseValidation {
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
}

?>