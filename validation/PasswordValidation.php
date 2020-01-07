<?php

class PasswordValidation {
    public function check($input) {
        if($input === '') {
            return false;
        }

        return true;
    }
}

?>