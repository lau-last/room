<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

if (isset($_GET['id_salle'])) {
    $recup_salle = $pdo->prepare("SELECT * FROM salle LEFT JOIN produit ON salle.id_salle = produit.id_salle WHERE salle.id_salle = :id_salle");
    $recup_salle->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $recup_salle->execute();

    // on vérifie si on a une ligne (si on a bien récupéré une salle)
    if ($recup_salle->rowCount() < 1) {

        // on redirige vers index
        header('location:index.php');
    }
} else {
    header('location:index.php');
}
// on traite la ligne avec fetch
$infos_salle = $recup_salle->fetch(PDO::FETCH_ASSOC);

// Variable vide destinée à afficher des commentaires utilisateur
$msg = '';

// Variable destinée à afficher les requetes exécutées pour voir les soucis de sécurité
$req = '';

// - 04 - Récupération des saisies du form avec controle 
if (isset($_POST['commentaire']) && isset($_POST['note'])) {
    $commentaire = trim($_POST['commentaire']);
    $note = trim($_POST['note']);

    // Contrôle : le pseudo et le commentaire ne doivent pas être vides.

    if (empty($commentaire)) {
        $msg .= '<div class="alert alert-danger mt-3">Attention, le commentaire est obligatoire</div>';
    }
    if (empty($note)) {
        $msg .= '<div class="alert alert-danger mt-3">Attention, la note est obligatoire</div>';
    }

    if (empty($msg)) {
        $enregistrement = $pdo->prepare("INSERT INTO avis (id_membre, id_salle, commentaire, note, date_enregistrement) VALUES (:id_membre, :id_salle, :commentaire, :note, NOW())");

        $enregistrement->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
        $enregistrement->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
        $enregistrement->bindParam(':note', $note, PDO::PARAM_STR);
        $enregistrement->bindParam(':id_salle', $infos_salle['id_salle'], PDO::PARAM_STR);
        $enregistrement->execute();
    }
} // Fin des isset

$avis = $pdo->prepare("SELECT * FROM avis INNER JOIN membre USING (id_membre) INNER JOIN salle USING (id_salle) WHERE id_salle = :id_salle ");
$avis->bindParam('id_salle', $_GET['id_salle'], PDO::PARAM_STR);
$avis->execute();


$liste_commentaire = $pdo->query("SELECT pseudo, commentaire, note, date_format(avis.date_enregistrement, '%d/%m/%Y à %H:%i:%s') AS date_enregistrement FROM avis INNER JOIN membre USING(id_membre) ORDER BY avis.date_enregistrement DESC");
// var_dump($infos_article);
// Les affichages dans la page commencent depuis la ligne suivante :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';

?>

<main class="container">

    <div class="p-5 rounded">
        <h1 class="pb-4"><?php echo $infos_salle['titre']; ?></h1>
        <button type="submit" class="border-0 btn btn-dark bg-indigo w-0" id="Reserver" name="Reserver">Reserver</button>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <img src="<?php echo URL . 'assets/img/' . $infos_salle['photo']; ?>" alt="Image de l'article : <?php echo $infos_salle['titre']; ?>" class="img-thumbnail w-100">
        </div>
        <div class="col-sm-6">
            <p>Description :<br><?php echo $infos_salle['description']; ?></p>
            <p>Localisation : <br><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d10498.355213523138!2d2.35652175!3d48.866051!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sfr!2sfr!4v1633332509306!5m2!1sfr!2sfr" width="500" height="330" style="border:0;" allowfullscreen="" loading="lazy"></iframe></p>
        </div>
    </div>

    <div class="row">
        <p>Informations complémentaires :</p>
        <div class="col-sm-4 mt-5">
            <p>Arrivée : <?php echo $infos_salle['date_arrivee']; ?></p>
            <p>Départ : <?php echo $infos_salle['date_depart']; ?></p>
        </div>
        <div class="col-sm-4 mt-5">
            <p>Capacité : <?php echo $infos_salle['capacite']; ?></p>
            <p>Catégorie : <?php echo $infos_salle['categorie']; ?></p>
        </div>
        <div class="col-sm-4 mt-5">
            <p>Adresse : <?php echo $infos_salle['adresse'] . ' ' . $infos_salle['ville'] . ' ' . $infos_salle['cp'] . ' ' . $infos_salle['pays']; ?></p>
            <p>Tarif : <?php echo $infos_salle['prix']; ?></p>
        </div>
    </div>

    <form method="post" class="mt-5 mx-auto w-50 border p-3">
        <?php
        // on  affiche la variable qui peut contenir des commentaires pour l'utilisateur.
        // echo $req;
        echo $msg;
        ?>
        <h3 class="text-center">Laissez un commantaire sur la salle</h3>
        <div class="row">
            <div class="col-sm-12 mt-5">
                <label for="commentaire" class="form-label">Commentaire</label>
                <textarea class="form-control" id="commentaire" name="commentaire"></textarea>
            </div>
            <div class="col-sm-12 mt-5">
                <label for="note" class="form-label">Note</label>
                <input type="text" class="form-control" id="note" name="note">
            </div>
            <div class="col-sm-12 mt-5">
                <button type="submit" class="border-0 btn btn-dark bg-indigo w-100" id="enregistrer" name="enregistrer">Enregistrer</button>
            </div>
        </div>
    </form>
    <div class='row mt-5'>
        <div class="col-6 mx-auto">
            <!-- Affichage des commentaire -->
            <?php
            while ($ligne_avis = $avis->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="card mt-3">
                            <div class="card-header bg-dark bg-indigo border-0 text-white">
                                <b>Par : </b>' . $ligne_avis['pseudo'] . ', le ' . $date_avis = date('d/m/Y à H:m', strtotime($ligne_avis['date_enregistrement'])) . '
                            </div>
                            <div class="card-body">
                                <p class="card-text">' . $ligne_avis['commentaire'] . '</p>
                            </div>
                            <div class="card-footer text-muted">
                            <p class="card-text">' . 'Note : ' . $ligne_avis['note'] . '/10' . '</p>
                            </div>
                        </div>';
            }
            ?>
        </div>
    </div>
</main>

<?php
include 'inc/footer.inc.php';
