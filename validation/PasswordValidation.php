<?php
//ver2.BaseValidationを継承。
require_once 'BaseValidation.php';

class PasswordValidation extends BaseValidation {
    public function check($input) {
        if($input === '') {
            $msg = 'エラー！入力が確認出来ませんでした。';
            $this->addErrorMessage($msg);
            return false;
        }

        return true;
    }
}

?>