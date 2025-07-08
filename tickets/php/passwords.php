<?php
    function encriptar($pass){
                
        $pass = password_hash($pass, PASSWORD_BCRYPT);

        return $pass;
    }

    function desencriptar($psw, $passHash){

        $verify = password_verify($psw, $passHash);

        if($verify){

            return true;

        }else{

            return false;
        }

    }
?>