<?php
include_once("C:\MAMP\htdocs\MyMusic\\functions_globales.php");

if(login_verif()){
    header("../MyMusic.php");
}else{
    header("login.php");
}



?>