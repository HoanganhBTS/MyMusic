<?php
class Session  {
    private array $id_admin_user;
    private string $id_session;
    public int $nombre_Personne;


    public function __construct($id_session){
        $this->id_admin_user = array();
        $this->id_session = $id_session;
        $this->nombre_Personne = 0;
    }

    
    //Donne la liste des utilisateurs administrateur 
    public function getIDAdminUser(){
        return $this->id_admin_user;
    }
    
    //Ajoute un utilisateur dans la liste
    public function setIdUser($id_user){
        $this->id_user = $id_user;
    }

    //Donne l'ID de la session
    public function getIdSession($id_session){
        return $this->id_session;
    }

    //Donne la liste des utilisateurs
    public function getListUser(){
        foreach( $this->listUser as $id_user ){
            echo $id_user;
        }
    }

    //Donne la liste des utilisateurs (Non enregistré et enregistré)
    public function setListUser($id_user){
        $this->listUser[] = $id_user;
    }

    //Donne la liste des sons
    public function getListSon($id_session){
        foreach( $this->list_Son as $son ){
            echo $son;
        }
    }

    //Ajoute un son dans la liste 
    public function setListSon($son){
        $this->list_Son[] = $son ;
    }

    //Lorsque l'utilisateur rejoint la session on vérifie si il est administrateur afin qu'il puisse faire des requêtes d'ajout de son 
    //Sinon il affiche juste les sons . Dans les deux cas les sons sont affichés
    function verificationAdmin(){
        $Current_User= unserialize($_SESSION['User']);
        foreach($this->listUser as $id_user ){
            if($id_user==$Current_User){
                echo '<html>';
                echo '<head>';
                echo '<title>Page de notre section membre</title>';
                echo '</head>';
                echo '<body>';
                echo '<h2> Ajout son </h2>';
                echo '<form action="MyMusic.php" method="post">';
                echo '<label for="nom">Artiste :</label>';
                echo '<input type="text" id="nom" name="nom" required><br>';
                echo '<label for="titre">Titre :</label>';
                echo '<input type="text" id="titre" name="titre" required><br>';
                echo '<label for="lien">Lien :</label>';
                echo '<input type="text" id="lien" name="lien" required><br>';
                echo '<label for="album">Album (Optionnel) :</label>';
                echo '<input type="text" id="album" name="album"><br>';
                echo '<input type="submit" value="Ajouter">';
                echo '</form>';
            }   
        }
        show_DESC();
    }

}



        

?>