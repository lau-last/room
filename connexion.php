<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// Déconnexion utilisateur
if( isset($_GET['action']) && $_GET['action' ] == 'deconnexion' ){
    session_destroy(); // on détruit la session : l'utilisateur n'est plus connecté
}
// Restriction d'accès, si l'utilisateur n'est pas connecté, on redirige vers connexion.php
if( user_is_connected() ) {
    header('location:profil.php');
}

// Si le formulaire a été validé
if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);

    // on déclenche une requete de récupération basée sur le pseudo
    $connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $connexion->execute();

    // on vérifie s'il y a une ligne récupérée.
    if ($connexion->rowCount() > 0) {
        // pseudo ok
        // on doit vérifier le mdp

        $infos = $connexion->fetch(PDO::FETCH_ASSOC);
        // var_dump($infos);
        // password_verify(mdp_du_form, mdp_de_bdd); => true / false
        if (password_verify($mdp, $infos['mdp'])) {
            // mdp ok
            // On place dans la $_SESSION les informations utilisateur (sauf le mdp) dans un sous tableau "membre"
            // L'ouverture de la session provient de init.inc.php
            $_SESSION['membre'] = array();
            $_SESSION['membre']['id_membre'] = $infos['id_membre'];
            $_SESSION['membre']['pseudo'] = $infos['pseudo'];
            $_SESSION['membre']['nom'] = $infos['nom'];
            $_SESSION['membre']['prenom'] = $infos['prenom'];
            $_SESSION['membre']['email'] = $infos['email'];
            $_SESSION['membre']['civilite'] = $infos['civilite'];
            $_SESSION['membre']['statut'] = $infos['statut'];
            header('location:profil.php');
        } else {
            // mdp nok
            $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe</div>';
        }
    } else {
        // pseudo nok
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe</div>';
    }
}

// Les affichages dans la page commencent depuis la ligne suivante :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>
<main class="container">
    <?php echo $msg . '<hr>'; ?>
    <div class="row">
        <div class="col-sm-4 mt-5 mx-auto">
            <form method="post" action="" class="border p-3">
                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudo</label>
                    <input type="text" class="form-control" id="pseudo" name="pseudo" value="">
                </div>
                <div class="mb-3">
                    <label for="mdp" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mdp" name="mdp">
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-outline-dark w-100" id="connexion" name="connexion" value="Connexion">
                </div>
            </form>
        </div>
    </div>
</main>
<?php
include 'inc/footer.inc.php';
