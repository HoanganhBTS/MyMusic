<?php
include_once("C:\MAMP\htdocs\MyMusic\Registered_User.php");
include_once("C:\MAMP\htdocs\MyMusic\User.php");
include_once("C:\MAMP\htdocs\MyMusic\Son.php");


/*------------------------------------------ DATA BASE CONNEXION -----------------------------------------------------------------------------*/
function connexion()
{
    try {
        $connexion = new PDO("mysql:host=localhost;dbname=mymusic;port=3306", "root", "root");
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connexion;
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
}

/*--------------------------------------------------------------------------------------------------------------------------------------------*/

/* --------------------------------------------------------- LOGIN / REGISTER FORM ------------------------------------------------------------------------ */
function register()
{
    $connexion = connexion();
    if(registerVerif()==true){
        try {
            $sql = "INSERT INTO login_user(login_user,password_user) VALUES (?,?)";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([$_POST['register_user'], md5($_POST['register_password'])]);
            header('Location:login.php');
            exit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }else{
        echo "Le nom d'utilisateur existe déjà , essayez en un autre";
    }

}

function registerVerif()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register_user"])) {
        $connexion = connexion();
        //Vérification si le login existe 
        $sql = "SELECT * FROM login_user WHERE login_user=:login_user";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(":login_user", $_POST["register_user"], PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();

        if ($res == 0) {
            return true;
        } else {
            return false;
        }
    }

}


function login_verif()
{
    try {
        if (isset($_POST["login"]) && isset($_POST["password"])) {

            $connexion = connexion();

            // Utilisation de md5 pour le mot de passe (à des fins de démonstration uniquement, pas recommandé en production)
            $hashed_password = md5($_POST["password"]);

            // Utilisation de requête SQL avec une clause WHERE pour vérifier le login et le mot de passe
            $sql = "SELECT * FROM login_user WHERE login_user = ? AND password_user = ?";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([$_POST['login'], $hashed_password]);

            // Fetch le résultat
            $row = $stmt->fetch();

            if ($row) {
                session_start();
                $_SESSION["login"] = $_POST["login"];
                $_SESSION["password"] = $_POST["password"];
                $User = new Registered_User($_SESSION["login"], $_SESSION["password"]);
                $_SESSION['User'] = serialize($User);
                header('location: ../MyMusic.php');
            } else {
                echo "Login ou mot de passe incorrect";
            }
        }

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}



/* ------------------------------------------------------------------------------------------------------------------------------------------------------------ */





/*--------------------------------------------------- SESSION RETRIVIAL ----------------------------------------------------------------------*/

//Récupère l'ID de la session actuelle 
function currentIdSession()
{
    $connexion = connexion();

    $requeteSelect = $connexion->prepare("SELECT id_session FROM session WHERE nom_session = :nom_session");
    $requeteSelect->bindParam(':nom_session', $_SESSION['session_name'], PDO::PARAM_STR);
    $requeteSelect->execute();
    $res = $requeteSelect->fetch();
    $res = $res['id_session'];

    return $res;
}

//Récupère l'ID de l'utilisateur actuelle

function currentIdUser()
{
    $connexion = connexion();

    $User = unserialize($_SESSION['User'])->getUsername();


    // Je cherche si l'utilisateur est une personne enregistré

    $sql = "SELECT id_user FROM login_user WHERE login_user = :login_user";
    $stmt = $connexion->prepare($sql);

    $stmt->bindParam(":login_user", $User, PDO::PARAM_STR);

    $stmt->execute();
    $res = $stmt->fetch();
    $res = $res['id_user'];

    return $res;
}

//Retourne True ou False selon si l'utilisateur peut voter

function isEnregistredUser()
{
    $connexion = connexion();

    // Récupération de l'Id de l'user actuelle
    $res = currentIdUser();


    // Vérification s'il est dans la liste des personnes pouvant voter
    $sql = "SELECT user_session_admin FROM user_session
        JOIN session ON user_session.id_session = session.id_session
        JOIN login_user ON user_session.id_user = login_user.id_user
        WHERE login_user.id_user = " . $res . ";";

    $stmt = $connexion->query($sql);
    $res2 = $stmt->fetch(PDO::FETCH_ASSOC);
    $res2 = intval($res2['user_session_admin']);

    if ($res2 == 1) {
        return true;
    } else {
        return false;
    }

}



/*---------------------------------------------------------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------- SHOW VIDEOS------------------------------------------------------ */

// Montre les vidéos dans l'ordre DESC 
function show_DESC()
{

    $connexion = connexion();
    $res = currentIdSession();
    $requeteSelect2 = $connexion->query(
        "SELECT * FROM son 
        JOIN son_session ON son.id_son = son_session.id_son
        JOIN session ON son_session.id_session = session.id_session
        WHERE session.id_session ='" . $res . "'
        ORDER BY son_session.son_session_vote DESC;"
    );

    // Open central container div
    echo '<div class="central-container">';
    echo '<form action="session_template.php" method="post">';
    echo '<button class="leave-button" name="leave" value="0" type="submit">Quitter</button>';
    echo '</form>';
    // Session Display div
    echo '<div class="session-container">';
    echo '<h1>' . $_SESSION['session_name'] . '</h1>';

    while ($row = $requeteSelect2->fetch()) {
        $videoID = extractVideoId($row["Lien"]);
        echo '<div class="song">';
        echo '<h2>' . $row['Titre'] . '</h2>';
        echo '<p>Artiste: ' . $row['Nom'] . '</p>';
        echo '<p>Album: ' . $row['Album'] . '</p>';
        echo "<iframe width='200px' height='200px' src='https://www.youtube.com/embed/{$videoID}' frameborder='0' allowfullscreen></iframe>";
        echo "<form action='session_template.php' method='post'>";
        echo '<button class="like-button" name="like" value="' . $row['id_son'] . '" type="submit">Like</button>';
        if (isEnregistredUser() == true) {
            echo '<button class="like-button" name="removeSong" value="' . $row['id_son'] . '"type="submit">Remove</button>';
        }
        echo "</form>";
        echo '</div>';
    }

    // Close Session Display div
    echo '</div>';
    // Close central container div
    echo '</div>';
}


//Découpage du lien youtube afin d'avoir que la dernière partie 
function extractVideoId($videoLink)
{
    $lastSlashPosition = strrpos($videoLink, '/');
    if ($lastSlashPosition !== false) {
        // Utilisez substr pour obtenir la partie après le dernier "/"
        $videoId = substr($videoLink, $lastSlashPosition + 1);
        return $videoId;
    } else {
        // Si le "/" n'est pas trouvé, retournez la chaîne entière
        return $videoLink;
    }
}



/*  ----------------------------------------------------------------------------------------------------------------------------------------------------  */


/* -------------------------------------------------------------- FUNCTIONS LINK TO SESSION -------------------------------------------------------------  */
//Ajout de l'utilisateur dans la liste des personnes dans la session
function addUserSession()
{
    $connexion = connexion();

    //Récupération de l'ID de l'utilisateur
    $User = currentIdUser();

    //Récupération de l'ID de la session
    $currentSession = currentIdSession();

    //Vérification si l'utilisateur n'existe pas déjà 
    $sql = "SELECT user_session_id FROM user_session WHERE id_session = :id_session and id_user= :id_user";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(":id_session", $currentSession, PDO::PARAM_INT);
    $stmt->bindParam(":id_user", $User, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res == 0) {
        $sql2 = "INSERT INTO user_session(id_session,id_user) VALUES(?,?)";
        $stmt2 = $connexion->prepare($sql2);
        $stmt2->execute([$currentSession, $User]);
    }
}

// Vote utilisateur 
function vote()
{
    if (isset($_POST['like'])) {

        $connexion = connexion();

        // Récupérez l'id de la session à partir du résultat de la requête
        $res = currentIdSession();

        // Récupération de la soumission du like
        $id_son = $_POST['like'];

        // Requête pour mettre à jour le vote
        try {
            $requeteUpdate = $connexion->prepare("UPDATE son_session SET son_session_vote = son_session_vote + 1 WHERE id_session = :id_session AND id_son = :id_son;");
            $requeteUpdate->bindParam(':id_session', $res, PDO::PARAM_INT);
            $requeteUpdate->bindParam(':id_son', $id_son, PDO::PARAM_INT);
            $requeteUpdate->execute();
            header('location: session_template.php');
            exit();
        } catch (Exception $e) {
            echo '' . $e->getMessage() . '';
        }
    }

}

// Affichage du formulaire d'ajout son pour les administrateurs
function showFormSong()
{
    $res = isEnregistredUser();

    //Affichage si données existent
    if ($res == true) {

        echo '<div class="add-song-form">';
        echo '<h2>Ajouter une chanson</h2>';
        echo '<form action="session_template.php" method="post">';
        echo '<label for="songTitleSession"> Titre </label>';
        echo '<input type="text" name="songTitleSession" required>';

        echo '<label for="songArtistSession"> Artiste </label> ';
        echo '<input type="text" name="songArtistSession" required>';

        echo '<label for="songLinkSession"> Lien </label>';
        echo '<input type="text" name="songLinkSession" required>';
        echo '<label for="songAlbumSession"> Album </label>';
        echo '<input type="text" name="songAlbumSession">';
        echo '<input type="submit" value="Ajouter"> ';
        echo '</form>';
        echo '</div>';

    } else {
        echo "<h2> Vous n'avez pas les droits pour ajouter un son";
    }
}

//Enlever un son de la sesdsion
function removeSongSession()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeSong'])) {
        $connexion = connexion();

        //Recupération de l'ID de la session
        $id_session = currentIdSession();

        //Requête pour supprimer le son de la session
        $sql = 'DELETE FROM son_session 
                WHERE id_son = :id_son
                AND id_session = :id_session ';
        $res = $connexion->prepare($sql);
        $res->bindParam('id_son', $_POST['removeSong'], PDO::PARAM_INT);
        $res->bindParam('id_session', $id_session, PDO::PARAM_INT);
        $res->execute();

        header('Location:session_template.php');
        exit();
    }
}


//Quitter la session actuelle
function leaveSession()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave'])) {
        unset($_SESSION['session_name']);
        header("Location:/MyMusic/MyMusic.php");
        exit();
    }
}



/* ------------------------------------------------------------------------------------------------------------------------------------------------------- */


function sessionExist()
{
    $connexion = connexion();
    $sql = "SELECT * FROM session WHERE nom_session = :session_name";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':session_name', $_SESSION["session_name"], PDO::PARAM_STR);
    $stmt->execute();

    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        return true;
    } else {
        return false;
    }
}



function addSong()
{


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['songTitleSession'])) {



        $songTitleSession = $_POST['songTitleSession'];
        $songArtistSession = $_POST['songArtistSession'];
        $songLinkSession = $_POST['songLinkSession'];
        $songAlbumSession = $_POST['songAlbumSession'];


        //
        $connexion = connexion();
        $sql1 = 'INSERT INTO son(Titre,Nom,Lien,Album) VALUES(?,?,?,?)';
        $stmt1 = $connexion->prepare($sql1);
        $stmt1->execute([$songTitleSession, $songArtistSession, $songLinkSession, $songAlbumSession]);



        //
        $sql2 = "SELECT id_son FROM son WHERE Lien ='" . $songLinkSession . "';";
        $stmt2 = $connexion->query($sql2);
        $res = $stmt2->fetch(PDO::FETCH_ASSOC);
        $resIdSon = $res['id_son'];



        //
        $sql3 = $connexion->prepare("SELECT id_session FROM session WHERE nom_session = :nom_session");
        $sql3->bindParam(':nom_session', $_SESSION['session_name'], PDO::PARAM_STR);
        $sql3->execute();
        $res2 = $sql3->fetch();
        $resIdSession = $res2['id_session'];


        //
        $sql1 = 'INSERT INTO son_session(id_son,id_session) VALUES(?,?)';
        $stmt1 = $connexion->prepare($sql1);
        $stmt1->execute([$resIdSon, $resIdSession]);

        header('Location:session_template.php');
        exit();


    }


}





function createSession()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["createSession"])) {
        $_SESSION['session_name'] = $_POST['createSession'];
        try {
            $connexion = connexion();
            $connexion = $connexion->prepare("INSERT INTO session(nom_session) VALUES(?);");
            $req = $connexion->execute([$_SESSION['createSession']]);
            header('Location:session/session_template.php');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}






?>