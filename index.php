<?php
session_start();
require('user.php');
if(!empty($_POST)){
    $utilisateur = new User($_POST);
    if(isset($_GET['inscrition']))
        {
            $message_register = $utilisateur->register($_POST);
        }
    if(isset($_GET['connect']) && !empty($_POST['login_connect']))
        {
            $user_connect = $utilisateur->connect($_POST);
        }
    }
elseif(isset($_SESSION['utilisateur']))
{
    $utilisateur = new User($_SESSION['utilisateur']);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Exercice Classe</title>
</head>
<body>
    <article>
    <section class="barre_lateral">
        <h3>Méthodes</h3>
        <ul>
            <li><a href="index.php?inscrition=up">register</a></li>
            <li><a href="index.php?connect=up">connect</a></li>
            <li><a href="index.php?disconnect=up">disconnect</a></li>
            <li><a href="index.php?isConnected=up">isConnected</a></li>
            <li><a href="index.getAllInfos=up">getAllInfos</a></li>
            
        </ul>
    </section>
    <section class="présentation">
        <?php
        if(isset($_SESSION['message'])){
            ?>
            <h3><?=$_SESSION['message']?></h3>
            <?php
        }
        //PAGE INSCRIPTION//
        if(isset($_GET['inscrition']) && $_GET['inscrition']=='up')
        {
            if(isset($message_register) && is_array($message_register)==true){
                ?>
                <h3>Vous êtes à présent inscrit</h3>
                <form action="index.php?inscrition=info" methode="get">
                <label for="inscrition">Ces informations ne seront disponnible que pour les 5 prochaines minutes</label>
                <button type="submit" name="inscrition" value="info">Voir mes information</button>
                </form>
                <?php
            }
            elseif (isset($message_register)){
                echo '<h3>'.$message_register.'</h3>';
            }
        ?>
        <form action="index.php?inscription=up" method="post">
            <h3>Inscription</h3>
            <label for="login_inscritpion">Votre login</label>
            <input type="text" name="login_inscritpion">
            <label for="email_inscritpion">Votre email</label>
            <input type="text" name="email_inscription">
            <label for="firstname_inscritpion">Votre Prénom</label>
            <input type="text" name="firstname_inscription">
            <label for="lastname_inscritpion">Votre Nom</label>
            <input type="text" name="lastname_inscription">
            <label for="lastname_inscritpion">Votre mot de passe</label>
            <input type="password" name="password_inscription">
            <label for="lastname_inscritpion">Valider le mot de passe</label>
            <input type="password" name="passwordv_inscription">
            <br>
            <input type="submit" value="S'inscrire"> 
        </form>
        <?php
        }
        elseif(isset($_GET['inscrition']) && $_GET['inscrition']=='info'){
            if(isset($_COOKIE['login_cookie'])&&isset($_COOKIE['password_cookie'])){
            $info_user = $utilisateur->connect($_COOKIE);
            ?>
            <table id="result_user">
                <thead>
                    <tr>
                        <th colspan="2">Vos informations personnels</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td id="tr">id :</td>
                    <td><?=$info_user['id']?></td>
                </tr>
                <tr>
                    <td id="tr">login :</td>
                    <td><?=$info_user['login']?></td>
                </tr>
                <tr>
                    <td id="tr">Mot de passe :</td>
                    <td><?=$_COOKIE['password_cookie']?></td>
                </tr>
                <tr>
                    <td id="tr">Adresse email :</td>
                    <td><?=$info_user['email']?></td>
                </tr>
                <tr>
                    <td id="tr">Prénom :</td>
                    <td><?=$info_user['firstname']?></td>
                </tr>
                <tr>
                    <td id="tr">Nom :</td>
                    <td><?=$info_user['lastname']?></td>
                </tr>
                </tbody>
            </table>
            <div class="nav_interne">
            <a href="index.php?inscrition=up">Retour</a>
            <a href="index.php?connect=up">Etape suivante</a>
            </div>
            <?php
            }
            else{
                echo '<h3>Le temps de visibilité sur ces informations ont expirées</h3>';
            }
        }
        //PAGE CONNEXION//
        if(isset($_GET['connect']))
        { 
            if(isset($_POST) && isset($user_connect)){
                    ?>
                    <h3><?=$user_connect?></h3>
                    <?php
                    $log_user = explode(' ',$user_connect);
                    if($log_user[0]=='Félicitation'){
                        

                        $_GET['connect']='ok';
                    }
            }
            elseif(isset($_POST)){
                ?>
                    <h3>Veuillez renseigné votre login</h3>
                <?php
            }
            if($_GET['connect']=='up'){
            ?>
            <form action="index.php?connect=up" method="post">
                <h3>Connexion</h3>
                <input type="text" name="login_connect">
                <input type="password" name="password_connect">
                <input type="submit" value="Connexion">
            </form>
            <?php
            }
            elseif($_GET['connect']=='ok'){
                ?>
                    <a href="index.php?connect=up">Retour</a>
                    <?php
                    $_SESSION['utilisateur']=$utilisateur;
                    if(isset($_COOKIE['next_tape'])&&($_COOKIE['next_tape']==$_SESSION['utilisateur']['login_session'])){
                    ?>
                        <a href="index.php?isConnected=up">Prochaine étape, après déconexion</a>
                    <?php
                    }
                    else{
                    ?>
                    <a href="index.php?disconnect=up">Prochaine étape</a>
                <?php
                    }
            }
        }
        //PAGE DECONEXION//
        if(isset($_GET['disconnect']) && $_GET['disconnect']=='up'){
            ?>
            <a href="index.php?disconnect=down">Se déconnecter</a>
            <?php
        }
        elseif(isset($_GET['disconnect']) && $_GET['disconnect']='down'){
            setcookie("next_tape", $_SESSION['utilisateur']['login_session'],time()+120);
            $utilisateur->disconnect();
            
            ?>
            <h3>Vous êtes à présent déconecter, veuillez vous reconnecter pour poursuivre l'exercice !</h3>
            <a href="index.php?connect=up">Se reconnecter</a>
            <?php
        }
        //PAGE isConnected//
        if(isset($_GET['isConnected'])){
            if($_GET['isConnected']=='up'){
            ?>
            <h3>Verifier si je suis bien connecter ?</h3>
            <a href="index.php?isConnected=verif">vérifier</a>
            <?php
            }
            elseif($_GET['isConnected']=='verif'){
                var_dump($utilisateur);
                $bool_user = $utilisateur->isConnected();
                var_dump($bool_user);
                if($bool_user==true){
                    ?>
                    <h3>La valeur <span>True</span> définit votre état comme connecté.</h3>
                    <a href="index.php?isConnected=verif">retour</a>
                    <a href="index.getAllInfos=up">étape suivante</a>
                    <?php
                }
                else{
                    ?>
                    <h3>La valeur <span>False</span> définit votre état comme déconnecté.</h3>
                    <a href="index.php?connect=up">Se connecter</a>
                    <?php
                }
            }
        }
        //PAGE getAllInfos//
        if(isset($_GET['getAllInfos'])){

        }
        ?>
        
    </section>
    </article>
</body>
</html>

