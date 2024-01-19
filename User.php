<?php
include_once("InterfaceUser.php");

abstract class User implements InterfaceUser {
    private $username;
    protected $active_Session; // Changez la visibilité à protected pour qu'elle soit accessible aux classes filles

    public function __construct($username) {
        $this->username = $username;
        $this->active_Session = null;
    }

    public function __toString(){
        return "Le nom de cette utilisateur est ".$this->getUsername();
    }

    public function getUsername(){
        return $this->username;
    }


    // Autres méthodes de l'interface IUser peuvent être déclarées ici ou dans les classes filles
}

?>