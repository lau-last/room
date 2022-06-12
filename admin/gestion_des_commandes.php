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
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_commande'])) {
    $suppression = $pdo->prepare("DELETE FROM commande WHERE id_commande = :id_commande");
    $suppression->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
    $suppression->execute();
}
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// ENREGISTREMENT & MODIFICATION ARTICLE 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$id_commande = '';
$id_membre = '';
$id_produit = '';
$prix = '';
$date_enregistrement = '';
// si le formulaire a été validé : isset de tous les champs sauf pour la Photo !
// Les pièces jointes d'un formulaire (input type="file") seront dans la superglobale $_FILES
// Ne pas oublier cet attribut sur la balisse form : enctype="multipart/form-data" sinon on ne récupère pas les pièces jointes.
if (isset($_POST['id_commande']) && isset($_POST['id_membre']) && isset($_POST['id_produit']) && isset($_POST['prix']) && isset($_POST['date_enregistrement'])) {

    $id_commande = trim($_POST['id_commande']);
    $id_membre = trim($_POST['id_membre']);
    $id_produit = trim($_POST['id_produit']);
    $prix = trim($_POST['prix']);
    $date_enregistrement = trim($_POST['date_enregistrement']);
    // Pour la modif, récupération de l'id

    if (!empty($_POST['id_commande'])) {
        $id_article = trim($_POST['id_commande']);
    }
    // Déclaration d'une variable nous permettant de savoir s'il y a eu des erreurs dans nos contrôles
    $erreur = false;

    if ($erreur == false) {

        if (!empty($id_avis)) {
            $enregistrement = $pdo->prepare("UPDATE commande SET id_membre = :id_membre, id_produit = :id_produit, prix = :prix WHERE id_commande = :id_commande");
            $enregistrement->bindParam(':id_commande', $id_commande, PDO::PARAM_STR);
        } else {
            $enregistrement = $pdo->prepare("INSERT INTO commande (id_membre, id_produit, prix, date_enregistrement) VALUES (:id_membre, :id_produit, :prix, NOW())");
        }

        $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
        $enregistrement->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
        $enregistrement->bindParam(':prix', $prix, PDO::PARAM_STR);
        $enregistrement->execute();
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_commande'])) {

    $recup_infos = $pdo->prepare("SELECT * FROM commande WHERE id_commande = :id_commande");
    $recup_infos->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
    $recup_infos->execute();
    $infos_commande = $recup_infos->fetch(PDO::FETCH_ASSOC);
    $id_commande = $infos_commande['id_commande'];
    $id_membre = $infos_commande['id_membre'];
    $id_produit = $infos_commande['id_produit'];
    $prix = $infos_commande['prix'];
    $date_enregistrement = $infos_commande['date_enregistrement'];
}
$liste_articles = $pdo->query("SELECT * FROM commande LEFT JOIN membre ON commande.id_membre = membre.id_membre LEFT JOIN produit ON commande.id_produit = produit.id_produit");
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<main class="container">
    <div class="row">
        <div class="col-12 mt-5">
            <table class="table table-bordered">
                <tr class="bg-indigo text-white">
                    <th>Id commande</th>
                    <th>Id membre</th>
                    <th>Id produit</th>
                    <th>Prix</th>
                    <th>Date_renregistrement</th>
                    <th>suppr</th>
                </tr>
                <?php
                while ($ligne = $liste_articles->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr class="text-center align-middle">';
                    echo '<td>' . $ligne['id_commande'] . '</td>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['id_produit'] . '</td>';
                    echo '<td>' . $ligne['prix'] . '</td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';
                    echo '<td class="text-center"><a href="?action=supprimer&id_commande=' . $ligne['id_commande'] . '" class="btn btn-danger confirm_delete" ><i class="far fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>
</main>
<?php
include '../inc/footer.inc.php';
