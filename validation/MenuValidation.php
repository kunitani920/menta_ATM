<?php

class MenuValidation {
    public function check($input) {
        if(Atm::ATM_MENU[$input]) {
            return true;
        }
        return false;
    }
}

?>