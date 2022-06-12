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
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_salle'])) {
    $suppression = $pdo->prepare("DELETE FROM salle WHERE id_salle = :id_salle");
    $suppression->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $suppression->execute();
}

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// ENREGISTREMENT & MODIFICATION ARTICLE 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$id_salle = '';
$ancienne_photo = '';
$titre = '';
$description = '';
$pays = '';
$ville = '';
$adresse = '';
$cp = '';
$capacite = '';
$categorie = '';
$photo = '';

// si le formulaire a été validé : isset de tous les champs sauf pour la Photo !
if (isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['pays']) && isset($_POST['ville']) && isset($_POST['adresse']) && isset($_POST['cp']) && isset($_POST['capacite']) && isset($_POST['categorie'])) {

    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $pays = trim($_POST['pays']);
    $ville = trim($_POST['ville']);
    $adresse = trim($_POST['adresse']);
    $cp = trim($_POST['cp']);
    $capacite = trim($_POST['capacite']);
    $categorie = trim($_POST['categorie']);

    // Pour la modif, récupération de l'id et de la photo
    if (!empty($_POST['id_salle'])) {
        $id_salle = trim($_POST['id_salle']);
    }
    if (!empty($_POST['ancienne_photo'])) {
        $photo = trim($_POST['ancienne_photo']);
    }

    // Déclaration d'une variable nous permettant de savoir s'il y a eu des erreurs dans nos contrôles
    $erreur = false;

    // Contrôle sur la disponibilité de la référence : car unique en BDD
    $verif_titre = $pdo->prepare("SELECT * FROM salle WHERE titre = :titre");
    $verif_titre->bindParam(':titre', $titre, PDO::PARAM_STR);
    $verif_titre->execute();

    // Pour la modif on a rajouté le "&& empty($id_salle)" car il ne faut pas tester la référence dans le cadre d'une modif car elle existe en bdd obligatoirement.
    if ($verif_titre->rowCount() > 0 && empty($id_salle)) {
        $msg .= '<div class="alert alert-danger mt-3">Attention,<br>Titre indisponible.</div>';

        // cas d'erreur 
        $erreur = true;
    }
    // Contrôle sur l'image
    // Les pièces jointes sont dans $_FILES
    // L'indice (le name du champ) qui sera dans $_FILES ne sera jamais vide car c'est un sous tableau array
    // Pour être sûr qu'un fichier a été chargé, on vérifie si l'indice name dans ce sous tableau n'est pas vide.
    if (!empty($_FILES['photo']['name'])) {

        // pour éviter qu'une nouvelle image ayant le même nom qu'une image déjà enregistrée, on renomme le nom de l'image en rajoutant la référence qui est unique.
        $photo = $titre . '-' . $_FILES['photo']['name'];

        // Nous devons vérifier l'extension de l'image afin d'être sûr que c'est bien une image et que le format est compatible pour le web
        // tableau array contenant les extensions acceptées : 
        $tab_extension = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        // on récupère l'extension du fichier, les extensions peuvent avoir une nb de caractère différent (jpg / jpeg / js ...)
        // Pour être sûr de récupérer l'extension complète, on va découper la chaine en partant de la fin et on remonte jusqu'à un caractère fourni en argument : le point . (même approche que dans la fonction class_active() voir le fichier functions.php)
        // exemple : strrchr('image.png', '.') => on récupère .png
        // au passage on enlève le . de l'extension avec substr()

        $extension = strrchr($photo, '.'); // exemple : strrchr('image.png', '.') => on récupère .png
        $extension = substr($extension, 1); // exemple : pour .png => on récupère png
        $extension = strtolower($extension); // on passe la chaine en minuscule pour pouvoir la tester // exemple : PNG => on récupère png

        if (in_array($extension, $tab_extension)) {

            // format ok
            // on retravaille le nom de l'image pour enlever les caractères spéciaux et les espaces
            $photo = preg_replace('/[^A-Za-z0-9.\-]/', '', $photo);

            // echo $photo;

            // s'il n'y a pas eu d'erreur dans nos contrôles, on copie l'image depuis le form vers un dossier
            if ($erreur == false) {


                // copy(emplacement_de_base, emplacement_cible);
                // l'image est conservée à la validation du formulaire dans l'indice de $_FILES['photo']['img']
                copy($_FILES['photo']['tmp_name'], ROOT_PATH . PROJECT_PATH . 'assets/img/' . $photo);
            }
        }
    } // fin d'une photo chargé

    // on enregistre en BDD 
    if ($erreur == false) {

        //si l'id_salle n'est pas vide, on est dans une modif
        if (!empty($id_salle)) {
            $enregistrement = $pdo->prepare("UPDATE salle SET titre = :titre, description  = :description, pays = :pays, ville = :ville, adresse = :adresse, cp = :cp, capacite = :capacite, categorie = :categorie, photo = :photo WHERE id_salle = :id_salle");
            $enregistrement->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
        } else {
            $enregistrement = $pdo->prepare("INSERT INTO salle (titre, description, pays, ville, adresse, cp, capacite, categorie, photo) VALUES (:titre, :description, :pays, :ville, :adresse, :cp, :capacite, :categorie, :photo)");
        }

        $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
        $enregistrement->bindParam(':description', $description, PDO::PARAM_STR);
        $enregistrement->bindParam(':pays', $pays, PDO::PARAM_STR);
        $enregistrement->bindParam(':ville', $ville, PDO::PARAM_STR);
        $enregistrement->bindParam(':adresse', $adresse, PDO::PARAM_STR);
        $enregistrement->bindParam(':cp', $cp, PDO::PARAM_STR);
        $enregistrement->bindParam(':capacite', $capacite, PDO::PARAM_STR);
        $enregistrement->bindParam(':categorie', $categorie, PDO::PARAM_STR);
        $enregistrement->bindParam(':photo', $photo, PDO::PARAM_STR);
        $enregistrement->execute();
    }
} // fin du isset

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// RECUPERATION DES INFOS DE L'ARTICLE A MODIFIER
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_salle'])) {

    // pour la modification, on lance une requete en bdd et on affecte les infos dans les variables présentent dans les value de nos champs du form
    $recup_infos = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
    $recup_infos->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $recup_infos->execute();

    $infos_salle = $recup_infos->fetch(PDO::FETCH_ASSOC);

    $id_salle = $infos_salle['id_salle'];
    $titre = $infos_salle['titre'];
    $description = $infos_salle['description'];
    $pays = $infos_salle['pays'];
    $ville = $infos_salle['ville'];
    $adresse = $infos_salle['adresse'];
    $cp = $infos_salle['cp'];
    $capacite = $infos_salle['capacite'];
    $categorie = $infos_salle['categorie'];
    $ancienne_photo = $infos_salle['photo'];
}

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// RECUPERATION DES ARTICLES EN BDD
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$liste_salle = $pdo->query("SELECT * FROM salle ORDER BY categorie, titre");

// Les affichages dans la page commencent depuis la ligne suivante :
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
                    <th>Id_salle</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Pays</th>
                    <th>Ville</th>
                    <th>Adresse</th>
                    <th>Cp</th>
                    <th>Capacité</th>
                    <th>Catégorie</th>
                    <th>Modif</th>
                    <th>Suppr</th>
                </tr>
                <?php
                while ($ligne = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr class="text-center align-middle">';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['titre'] . '</td>';
                    echo '<td>' . $ligne['description'] . '</td>';
                    echo '<td class="text-center"><img src="' . URL . 'assets/img/' . $ligne['photo'] . '" class="img-thumbnail" width="70"></td>';
                    echo '<td>' . $ligne['pays'] . '</td>';
                    echo '<td>' . $ligne['ville'] . '</td>';
                    echo '<td>' . $ligne['adresse'] . '</td>';
                    echo '<td>' . $ligne['cp'] . '</td>';
                    echo '<td>' . $ligne['capacite'] . '</td>';
                    echo '<td>' . $ligne['categorie'] . '</td>';
                    echo '
                            <td class="text-center"><a href="?action=modifier&id_salle=' . $ligne['id_salle'] . '" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a></td>';
                    echo '<td class="text-center"><a href="?action=supprimer&id_salle=' . $ligne['id_salle'] . '" class="btn btn-danger confirm_delete" ><i class="far fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-5">
            <form method="post" action="" class="border p-3" enctype="multipart/form-data">
                <input type="hidden" name="id_salle" value="<?php echo $id_salle ?>">
                <input type="hidden" name="ancienne_photo" value="<?php echo $ancienne_photo ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $titre; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="<?php echo $description; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo">
                        </div>
                        <div class="mb-3">
                            <label for="capacite" class="form-label">Capacite</label>
                            <select class="form-control" id="capacite" name="capacite">
                                <option>1</option>
                                <option <?php if ($capacite == '2') {
                                            echo 'selected';
                                        } ?>>2</option>
                                <option <?php if ($capacite == '3') {
                                            echo 'selected';
                                        } ?>>3</option>
                                <option <?php if ($capacite == '4') {
                                            echo 'selected';
                                        } ?>>4</option>
                                <option <?php if ($capacite == '5') {
                                            echo 'selected';
                                        } ?>>5</option>
                                <option <?php if ($capacite == '10') {
                                            echo 'selected';
                                        } ?>>10</option>
                                <option <?php if ($capacite == '15') {
                                            echo 'selected';
                                        } ?>>15</option>
                                <option <?php if ($capacite == '20') {
                                            echo 'selected';
                                        } ?>>20</option>
                                <option <?php if ($capacite == '25') {
                                            echo 'selected';
                                        } ?>>25</option>
                                <option <?php if ($capacite == '30') {
                                            echo 'selected';
                                        } ?>>30</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="categorie" class="form-label">Categorie</label>
                            <select class="form-control" id="categorie" name="categorie">
                                <option>Réunion</option>
                                <option <?php if ($categorie == 'Bureau') {
                                            echo 'selected';
                                        } ?>>Bureau</option>
                                <option <?php if ($categorie == 'Formation') {
                                            echo 'selected';
                                        } ?>>Formation</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="pays" class="form-label">Pays</label>
                            <select class="form-control" id="pays" name="pays">
                                <option value="France">France</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <select class="form-control" id="ville" name="ville">
                                <option>Paris</option>
                                <option>Marseille</option>
                                <option>Lyon</option>
                                <option>Montpellier</option>
                                <option>Nante</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" name="adresse"><?php echo $adresse; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="cp" class="form-label">Code postal</label>
                            <input type="text" class="form-control" id="cp" name="cp" value="<?php echo $cp; ?>">
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
