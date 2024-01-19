<?php
interface InterfaceUser{
    public function createSession($id_session) : void;
    public function inSession();
    public function joinSession($id_session);
    public function quitSession();

    public function upvote();
    public function downvote();
    public function invite($id_user);

    function insert_son();
   
}

?>