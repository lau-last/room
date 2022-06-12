<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';
// Restriction d'accès, si l'utilisateur n'est pas admin, on le redirige vers connexion.php
if (!user_is_admin()) {
    header('location:../connexion.php');
    exit(); // bloque l'exécution du code à la suite de cette ligne.
}
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// SUPPRESSION ARTICLE
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_membre'])) {
    $suppression = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
    $suppression->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
    $suppression->execute();
}
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// ENREGISTREMENT & MODIFICATION ARTICLE 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$id_membre = '';
$pseudo = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';
$statut = '';
$date_enregistrement = date("Y-m-d H:i:s");
// si le formulaire a été validé : isset de tous les champs sauf pour la Photo !
// Les pièces jointes d'un formulaire (input type="file") seront dans la superglobale $_FILES
// Ne pas oublier cet attribut sur la balisse form : enctype="multipart/form-data" sinon on ne récupère pas les pièces jointes.
if (isset($_POST['pseudo']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']) && isset($_POST['statut']) && isset($_POST['date_enregistrement'])) {

    $pseudo = trim($_POST['pseudo']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $civilite = trim($_POST['civilite']);
    $statut = trim($_POST['statut']);
    $date_enregistrement = trim($_POST['date_enregistrement']);

    // Pour la modif, récupération de l'id
    if (!empty($_POST['id_membre'])) {
        $id_membre = trim($_POST['id_membre']);
    }
    // Déclaration d'une variable nous permettant de savoir s'il y a eu des erreurs dans nos contrôles
    $erreur = false;
    if (empty($pseudo)) {
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Pseudo obligatoire.</div>';
        $erreur = true;
    }
    $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $verif_pseudo->execute();

    if ($verif_pseudo->rowCount() > 0 && empty($id_membre)) {
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Pseudo indisponible.</div>';
        $erreur = true;
    }
    if ($erreur == false) {
        if (!empty($id_membre)) {
            $enregistrement = $pdo->prepare("UPDATE membre SET pseudo = :pseudo, nom  = :nom, prenom = :prenom, email = :email, civilite = :civilite, statut = :statut, date_enregistrement = :date_enregistrement WHERE id_membre = :id_membre");
            $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
        } else {
            $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :nom, :prenom, :email, :civilite, :statut, :date_enregistrement)");
        }
        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
        $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
        $enregistrement->bindParam(':statut', $statut, PDO::PARAM_STR);
        $enregistrement->bindParam(':date_enregistrement', $date_enregistrement, PDO::PARAM_STR);
        $enregistrement->execute();
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_membre'])) {
    $recup_infos = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
    $recup_infos->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
    $recup_infos->execute();
    $infos_membre = $recup_infos->fetch(PDO::FETCH_ASSOC);
    $id_membre = $infos_membre['id_membre'];
    $pseudo = $infos_membre['pseudo'];
    $nom = $infos_membre['nom'];
    $prenom = $infos_membre['prenom'];
    $email = $infos_membre['email'];
    $civilite = $infos_membre['civilite'];
    $statut = $infos_membre['statut'];
    $date_enregistrement = $infos_membre['date_enregistrement'];
}
$liste_membre = $pdo->query("SELECT * FROM membre ORDER BY pseudo");
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
?>
<main class="container">
    <div class="row">
        <div class="col-12 mt-5">
            <table class="table table-bordered">
                <tr class="bg-indigo text-white">
                    <th>Id_membre</th>
                    <th>Pseudo</th>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>email</th>
                    <th>Civilite</th>
                    <th>Statut</th>
                    <th>date_enregistrement</th>
                    <th>Modif</th>
                    <th>Suppr</th>
                </tr>
                <?php
                while ($ligne = $liste_membre->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr class="text-center align-middle">';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['pseudo'] . '</td>';
                    echo '<td>' . $ligne['nom'] . '</td>';
                    echo '<td>' . $ligne['prenom'] . '</td>';
                    echo '<td>' . $ligne['email'] . '</td>';
                    echo '<td>' . $ligne['civilite'] . '</td>';
                    echo '<td>' . $ligne['statut'] . '</td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';
                    echo '
                            <td class="text-center"><a href="?action=modifier&id_membre=' . $ligne['id_membre'] . '" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a></td>';
                    echo '<td class="text-center"><a href="?action=supprimer&id_membre=' . $ligne['id_membre'] . '" class="btn btn-danger confirm_delete" ><i class="far fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-5">
            <form method="post" action="" class="border p-3" enctype="multipart/form-data">

                <input type="hidden" name="id_membre" value="<?php echo $id_membre ?>">
                <input type="hidden" name="date_enregistrement" value="<?php echo $date_enregistrement ?>">

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
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prenom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="civilite" class="form-label">Civilite</label>
                            <select class="form-control" id="civilite" name="civilite">
                                <option value="m">Homme</option>
                                <option value="f" <?php if ($civilite == 'f') {
                                                        echo 'selected';
                                                    } ?>>Femme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-control" id="statut" name="statut">
                                <option value="1">membre</option>
                                <option value="2" <?php if ($statut == 'membre') {
                                                        echo 'selected';
                                                    } ?>>admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="submit" class="btn btn-outline-primary w-100" id="enregistrement" name="enregistrement" value="Enregistrement">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
include '../inc/footer.inc.php';
