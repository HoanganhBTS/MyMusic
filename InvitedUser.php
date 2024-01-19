<?php
class InvitedUser extends User{

    public function __construct($id_user){
        parent::__construct($id_user);
    }

    public function createSession(){}
    public function inSession(){}

    public function joinSession($id_session) {
        $this->active_Session = $id_session;
    }
    
    public function quitSession(){
        $this->active_Session = null; // Quitter la session en mettant la propriété à null
    }
    public function giveAdminRight(){}
    public function upvote(){}
    public function downvote(){}
    public function invite(){}
}
?>