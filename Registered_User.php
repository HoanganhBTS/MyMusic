<?php
include("C:\MAMP\htdocs\MyMusic\User.php"); 

class Registered_User extends User {
    private $user_password;
    private  $list_Session;

    public function __construct($username, $user_password) {
        parent::__construct($username);
        $this->user_password = $user_password;
        $this->list_Session = [];
    }

    public function __toString() {
        return parent::__toString() . " " . $this->user_password;
    }


    public function inSession() {
        if(isset($this->active_Session)){
            echo "L'utilisateur est dans une session";
            return true;
        } else {
            echo "L'utilisateur n'est pas dans une session";
            return false;
        }
    }

    public function createSession($id_session):void{
        $this->list_Session[] = $id_session;
    }

    public function joinSession($id_session) {
        $this->active_Session = $id_session;
    }

    public function quitSession() {
        $this->active_Session = null; // Quitter la session en mettant la propriété à null
    }



    public function upvote() {
        // Logique pour l'upvote
        connexion();

    }

    public function downvote() {
        // Logique pour le downvote
    }

    public function invite($id_user) {
        // Logique pour inviter un utilisateur
        $this->list_Session[] = $id_user;
    }

    function insert_son(){
    if (isset($_POST['titre'])) {
        $Son = new Son($_POST['nom'], $_POST['album'], $_POST['titre'], $_POST['lien']);
        $connexion = connexion();
        $sql = "INSERT INTO son (Nom, Album, Titre, Lien ) VALUES(?,?,?,?)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$Son->getNom(),$Son->getAlbum(),$Son->getTitre(),$Son->getLien()]);
        
        $Session = new Session($this->active_Session);
        $Session->setListSon($Son);

    }

}

    

 
    
    
}

?>