<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';
if (!user_is_connected()) {
    header('location:connexion.php');
}
if ($_SESSION['membre']['civilite'] == 'm') {
    $civilite = 'homme';
} else {
    $civilite = 'femme';
}
if ($_SESSION['membre']['statut'] == 2) {
    $statut = 'vous êtes administrateur';
} else {
    $statut = 'vous êtes membre';
}
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>
<main class="container">

    <?php echo $msg; ?>

    <div class="row">
        <div class="col-sm-6 mt-5">
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Vos informations</li>

                <li class="list-group-item li_flex"><span><b>N° : </b><?php echo $_SESSION['membre']['id_membre']; ?></span><i class="fas fa-user couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>Pseudo : </b><?php echo $_SESSION['membre']['pseudo']; ?></span><i class="fas fa-ghost couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>Nom : </b><?php echo $_SESSION['membre']['nom']; ?></span><i class="fas fa-signature couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>Prénom : </b><?php echo $_SESSION['membre']['prenom']; ?></span><i class="fas fa-signature couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>Email : </b><?php echo $_SESSION['membre']['email']; ?></span><i class="far fa-envelope couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>civilité : </b><?php echo $civilite; ?></span><i class="fas fa-venus-mars couleur_icone"></i></li>

                <li class="list-group-item li_flex"><span><b>Statut : </b><?php echo $statut; ?></span><i class="fas fa-user-tag couleur_icone"></i></li>
            </ul>
        </div>

        <div class="col-sm-6 mt-5">
            <img src="assets/img/profil.jpeg" alt="une image de profil" class="w-100 img-thumbnail">
        </div>

    </div>
    
</main>
<?php
include 'inc/footer.inc.php';
