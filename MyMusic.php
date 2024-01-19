<?php
include("functions_globales.php");
session_start();
createSession();
?>
<?php if (isset($_SESSION['User'])) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Music Sharing</title>
        <link rel="stylesheet" href="styles.css">
    </head>

    <body>
        <div class="container">
        <h1>Bienvenue sur MyMusic <?php echo unserialize($_SESSION['User'])->getUsername();?></h1>


            <!-- Formulaire pour rejoindre une session -->
            <h2>Rejoindre une session</h2>
            <form action="session/session_template.php" method="post">
                <label for="session_name">Nom de la session :</label>
                <input type="text" id="sessionId" name="session_name" required>
                <input type="submit" value="Rejoindre">
            </form>
        </div>

        <div class="container">
            <h2> Création d'une session </h2>
            <form action="MyMusic.php" method="post">
                <label for="createSession"> Nom de la session :</label>
                <input type="text" id="sessionId" name="createSession" required>
                <input type="submit" value="Créer"> 
                
            </form>
        </div>


        <a href="./logout.php">Déconnexion</a>
    </body>


    </html>

    <?php
}else{
    header('Location:login.php');
}
?>