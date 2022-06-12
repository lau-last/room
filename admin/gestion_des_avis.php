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
// SUPPRESSION 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_avis'])) {
    $suppression = $pdo->prepare("DELETE FROM avis WHERE id_avis = :id_avis");
    $suppression->bindParam(':id_avis', $_GET['id_avis'], PDO::PARAM_STR);
    $suppression->execute();
}
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// ENREGISTREMENT & MODIFICATION 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$id_avis = '';
$commentaire = '';
$date_enregistrement = date("Y-m-d H:i:s");
// si le formulaire a été validé : isset de tous les champs !
// Les pièces jointes d'un formulaire (input type="file") seront dans la superglobale $_FILES
// Ne pas oublier cet attribut sur la balisse form : enctype="multipart/form-data" sinon on ne récupère pas les pièces jointes.
if (isset($_POST['commentaire'])) {

    $commentaire = trim($_POST['commentaire']);

    // Pour la modif, récupération de l'id
    if (!empty($_POST['id_avis'])) {
        $id_avis = trim($_POST['id_avis']);
    }
    // Déclaration d'une variable nous permettant de savoir s'il y a eu des erreurs dans nos contrôles
    $erreur = false;

    if (!empty($id_avis)) {
        $enregistrement = $pdo->prepare("UPDATE avis SET commentaire = :commentaire WHERE id_avis = :id_avis");
        $enregistrement->bindParam(':id_avis', $id_avis, PDO::PARAM_STR);
    }

    $enregistrement->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $enregistrement->execute();
}
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_avis'])) {
    
    $recup_infos = $pdo->prepare("SELECT * FROM avis WHERE id_avis = :id_avis");
    $recup_infos->bindParam(':id_avis', $_GET['id_avis'], PDO::PARAM_STR);
    $recup_infos->execute();

    $infos_avis = $recup_infos->fetch(PDO::FETCH_ASSOC);

    $id_avis = $infos_avis['id_avis'];
    $id_membre = $infos_avis['id_membre'];
    $id_salle = $infos_avis['id_salle'];
    $commentaire = $infos_avis['commentaire'];
    $note = $infos_avis['note'];
    $date_enregistrement = $infos_avis['date_enregistrement'];
}
$liste_articles = $pdo->query("SELECT * FROM avis LEFT JOIN membre ON avis.id_membre = membre.id_membre LEFT JOIN salle ON avis.id_salle = salle.id_salle");
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<main class="container">
    <div class="row">
        <div class="col-12 mt-5">
            <table class="table table-bordered">
                <tr class="bg-indigo text-white">
                    <th>Id avis</th>
                    <th>Id membre</th>
                    <th>Id salle</th>
                    <th>Commentaire</th>
                    <th>Note</th>
                    <th>Date_enregistrement</th>
                    <th>Modif</th>
                    <th>Suppr</th>
                </tr>
                <?php
                while ($ligne = $liste_articles->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr class="text-center align-middle">';
                    echo '<td>' . $ligne['id_avis'] . '</td>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['commentaire'] . '</td>';
                    echo '<td>' . $ligne['note'] . '</td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';
                    echo '<td><a href="?action=modifier&id_avis=' . $ligne['id_avis'] . '" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a></td>';
                    echo '<td><a href="?action=supprimer&id_avis=' . $ligne['id_avis'] . '" class="btn btn-danger confirm_delete" ><i class="far fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-5">
            <form method="post" action="" class="border p-3" enctype="multipart/form-data">
                <input type="hidden" class="form-control" id="id_avis" name="id_avis" value="<?php echo $id_avis; ?>">
                <input type="hidden" name="id_membre" value="<?php echo $id_membre ?>">
                <input type="hidden" class="form-control" id="id_salle" name="id_salle" value="<?php echo $id_salle; ?>">
                <input type="hidden" name="date_enregistrement" value="<?php echo $date_enregistrement ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">commentaire</label>
                            <textarea type="text" class="form-control" id="commentaire" name="commentaire"><?php echo $commentaire; ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
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
