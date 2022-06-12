<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// Restriction d'accès, si l'utilisateur n'est pas connecté, on redirige vers connexion.php
// Attention, header('location:...') doit être exécutée avant tout affichage dans la page. 
if (user_is_connected()) {
    header('location:profil.php');
}

// Vérification de l'existance des informations provenants du formulaire

// On déclare des variables vides nous permettant de les appeler dans les values de nos champs du form. Si le form est validé, on  récupère dans le if les valeurs dans ces variables, cela permettra d'afficher la valeur par défaut dans le form.
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';
$satut = '';
$date_enregistrement = date("Y-m-d H:i:s");

// Enregistrement :
if (isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']) && isset($_POST['date_enregistrement'])) {

    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $civilite = trim($_POST['civilite']);
    $statut = trim($_POST['statut']);
    $date_enregistrement = trim($_POST['date_enregistrement']);

    // Contrôles :
    // - taille du pseudo
    // - caractères présent dans le pseudo
    // - unicité du pseudo
    // - format du mail
    // - le mdp ne doit pas être vide
    // - taille du code postal et ne doit contenir que des chiffres

    // variable de contrôle nous permettant dans un deuxième temps plus bas de savoir s'il y a eu une erreur dans nos traitement.
    $erreur = false;

    // - taille du pseudo : entre 4 et 14 caractères inclus
    if (iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14) {
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Le pseudo doit avoir entre 4 et 14 caractères inclus.</div>';
        // cas d'erreur 
        $erreur = true;
    }

    // - caractères présent dans le pseudo
    // Afin de vérifier les caractères présents dans le pseudo, nous allons utiliser une expression réguliere (regex)
    // une regex n'est pas du php, on s'en sert via des fonctions de php prévues
    // preg_match() est une fonction prédéfinie permettant de tester une chaine selon une expression régulière fournie en argument.
    // si tous les caractères de la chaine correspondent à notre regex, on obtient 1 sinon 0, et false s'il y a une erreur
    // https://www.php.net/manual/fr/function.preg-match.php
    $verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
    // var_dump($verif_caractere);

    /*
    Explications de la regex
    ------------------------
    #   les # représentent le début et la fin de l'expression (il est possible d'utiliser les /  :'/^[a-zA-Z0-9._-]+$/' )
    ^   dit à la regex que la chaine ne peut commencer que par les caractères entre []
    $   dit à la regex que la chaine ne peut finir que par les caractères entre []
    Le fait de bloquer le début et la fin fait que l'on ne peut avoir que ces caractères dans la chaine.
    +   permet de préciser que l'on peut avoir plusieurs fois le même caractères dans la chaine.
    Entre [] : les caractères autorisés :
    a-z toutes les minuscules
    A-Z toutes les majuscules
    0-9 tous les chiffres
    ._- le point, l'underscore et le tiret sont aussi autorisés.
    */

    if (!$verif_caractere) { // équivalent à : if($verif_caractere == 0)
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Caractères autorisés pour le pseudo : A-Z 0-9 _ . -</div>';
        // cas d'erreur 
        $erreur = true;
    }

    // - unicité du pseudo
    // pour savoir si le pseudo est disponible, on lance une requete select basée sur le pseudo, si on récupère des infos, le pseudo est indisponible sinon disponible.
    $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $verif_pseudo->execute();

    // var_dump($verif_pseudo);
    // var_dump(get_class_methods($verif_pseudo));

    if ($verif_pseudo->rowCount() > 0) {
        // pseudo indisponible
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Pseudo indisponible.</div>';
        // cas d'erreur 
        $erreur = true;
    }

    // - format du mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Format du mail incorrect.</div>';
        // cas d'erreur 
        $erreur = true;
    }

    // - le mdp ne doit pas être vide (ce contrôle est à multiplier pour tous les champs obligatoires)
    // if($mdp == '') {
    // if(iconv_strlen($mdp) < 1) {
    if (empty($mdp)) {

        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Le mot de passe est obligatoire.</div>';
        // cas d'erreur 
        $erreur = true;
    }
    if(!valid_pass($mdp)){
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Le mot de passe doit avoir 1 maj 1....</div>';
        // cas d'erreur 
        $erreur = true;
    }
    //  s'il n'y a pas eu d'erreur, on lance l'enregistrement en bdd
    // if(!$erreur){}
    if ($erreur == false) {

        // cryptage du mdp
        // password_hash($mdp, PASSWORD DEFAULT) // permet de crypter (hasher) une chaine de caractère
        // Pour la connexion afin de pouvoir comparer le mdp du form avec celui enregistré on utilisera
        // password_verify($mdp, $mdp_bdd)
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);
        $inscription = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, 1, :date_enregistrement)");

        $inscription->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $inscription->bindParam(':mdp', $mdp, PDO::PARAM_STR);
        $inscription->bindParam(':nom', $nom, PDO::PARAM_STR);
        $inscription->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $inscription->bindParam(':email', $email, PDO::PARAM_STR);
        $inscription->bindParam(':civilite', $civilite, PDO::PARAM_STR);
        $inscription->bindParam(':date_enregistrement', $date_enregistrement, PDO::PARAM_STR);
        $inscription_valide = $inscription->execute();
     
    }
    if (isset($inscription_valide) && $inscription_valide){
        $msg .= '<div class="alert alert-success mt-3">validation reussi</div>';
    }
}

// Les affichages dans la page commencent depuis la ligne suivante :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// var_dump($_POST);
?>

<main class="container">

    <?php echo $msg ?>

    <div class="row">
        <div class="col-12 mt-5">
            <form method="post" action="" class="border p-3">
                <!-- <label for="statut" class="form-label"></label> -->
                <input type="hidden" name="date_enregistrement" value="<?php echo $date_enregistrement ?>">
                <input type="hidden" class="form-control" id="statut" name="statut" value="<?php echo $statut; ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Pseudo</label>
                            <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo $pseudo; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="mdp" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mdp" name="mdp">
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="civilite" class="form-label">civilite</label>
                            <select class="form-control" id="civilite" name="civilite">
                                <option value="m">homme</option>
                                <option value="f" <?php if ($civilite == 'f') {
                                                        echo 'selected';
                                                    } ?>>femme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="submit" class="btn btn-outline-dark w-100" id="inscription" name="inscription" value="Inscription">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
include 'inc/footer.inc.php';
