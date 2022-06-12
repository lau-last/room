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
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_produit'])) {
    $suppression = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $suppression->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $suppression->execute();
}
// echo '<pre>';
// var_dump($_SESSION);
// die();
// echo '</pre>';
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// ENREGISTREMENT & MODIFICATION ARTICLE 
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$id_produit = '';
$date_arrivee = '';
$date_depart = '';
$id_salle = '';
$prix = '';
$etat = 'libre';
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
// si le formulaire a été validé : isset de tous les champs !
if (isset($_POST['date_arrivee']) && isset($_POST['date_depart']) && isset($_POST['prix']) && isset($_POST['id_salle'])) {
    // echo '<pre>';
    // var_dump("ok");
    // echo '</pre>';
    $date_arrivee = trim($_POST['date_arrivee']);
    $date_depart = trim($_POST['date_depart']);
    $prix = trim($_POST['prix']);
    $id_salle = trim($_POST['id_salle']);

    // Pour la modif, récupération de l'id 
    if (!empty($_POST['id_produit'])) {
        $id_produit = intval($_POST['id_produit']);
    }

    // Déclaration d'une variable nous permettant de savoir s'il y a eu des erreurs dans nos contrôles
    $erreur = false;

    // on enregistre la BDD
    if ($erreur == false) {
        // echo '<pre>';
        // var_dump(!empty($id_produit));
        // echo '</pre>';
        // si l'id_article n'est pas vide, on est dans une modif :
        if (!empty($id_produit)) {
            $enregistrement = $pdo->prepare("UPDATE produit SET date_arrivee = :date_arrivee, date_depart = :date_depart, prix = :prix, id_salle = :id_salle, etat = :etat WHERE id_produit = :id_produit");
            $enregistrement->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
        } else {
            $enregistrement = $pdo->prepare("INSERT INTO produit (date_arrivee, date_depart, prix, id_salle, etat) VALUES (:date_arrivee, :date_depart, :prix, :id_salle, :etat)");
        }

        $enregistrement->bindParam(':date_arrivee', $date_arrivee, PDO::PARAM_STR);
        $enregistrement->bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
        $enregistrement->bindParam(':prix', $prix, PDO::PARAM_STR);
        $enregistrement->bindParam(':etat', $etat, PDO::PARAM_STR);
        $enregistrement->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
        // echo '<pre>';
        // var_dump($enregistrement);
        // echo '</pre>';
        $enregistrement->execute();
    }
} // fin des isset

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// RECUPERATION DES INFOS DE L'ARTICLE A MODIFIER
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_produit'])) {

    // pour la modification, on lance une requete en bdd et on affecte les infos dans les variables présentent dans les value de nos champs du form
    $recup_infos = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $recup_infos->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $recup_infos->execute();

    $infos_produit = $recup_infos->fetch(PDO::FETCH_ASSOC);

    $id_produit = $infos_produit['id_produit'];
    $date_arrivee = $infos_produit['date_arrivee'];
    $date_depart = $infos_produit['date_depart'];
    $id_salle = $infos_produit['id_salle'];
    $prix = $infos_produit['prix'];
    $etat = $infos_produit['etat'];
}

//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
// RECUPERATION DES ARTICLES EN BDD
//--------------------------------------------------------------------------
//--------------------------------------------------------------------------
$liste_produit = $pdo->query("SELECT * FROM produit LEFT JOIN salle ON produit.id_salle = salle.id_salle");

// Les affichages dans la page commencent depuis la ligne suivante :
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>';
// var_dump($liste_produit->fetchAll(PDO::FETCH_ASSOC));
// echo '</pre>';
?>
<main class="container">
    <div class="row">
        <div class="col-12 mt-5">
            <table class="table table-bordered">
                <tr class="bg-indigo text-white">
                    <th>Id_produit</th>
                    <th>Date_arrivee</th>
                    <th>Date_depart</th>
                    <th>Id_salle</th>
                    <th>Prix</th>
                    <th>Etat</th>
                    <th>Modif</th>
                    <th>Suppr</th>
                </tr>
                <?php
                while ($ligne = $liste_produit->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr class="text-center align-middle">';
                    echo '<td>' . $ligne['id_produit'] . '</td>';
                    echo '<td>' . $ligne['date_arrivee'] . '</td>';
                    echo '<td>' . $ligne['date_depart'] . '</td>';
                    echo '<td class="text-center">' . $ligne['titre'] . '<br>' . '<img src="' . URL . 'assets/img/' . $ligne['photo'] . '"class="img-thumbnail" width="70">' . '</td>';
                    echo '<td>' . $ligne['prix'] . ' €</td>';
                    echo '<td>' . $ligne['etat'] . '</td>';
                    echo '
                            <td class="text-center"><a href="?action=modifier&id_produit=' . $ligne['id_produit'] . '" class="btn btn-warning text-white"><i class="fas fa-edit"></i></a></td>';
                    echo '<td class="text-center"><a href="?action=supprimer&id_produit=' . $ligne['id_produit'] . '" class="btn btn-danger confirm_delete" ><i class="far fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-5">
            <form method="post" action="" class="border p-3" enctype="multipart/form-data">
                <input type="hidden" name="id_produit" value="<?php echo $id_produit ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="date_arrivee" class="form-label">Start date:</label>
                            <input type="text" class="form-control" id="date_arrivee" name="date_arrivee" value="<?php echo $date_arrivee; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="date_depart" class="form-label">End date:</label>
                            <input type="text" class="form-control" id="date_depart" name="date_depart" value="<?php echo $date_depart; ?>">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="id_salle" class="form-label">ID Salle</label>
                            <select class="form-control" id="id_salle" name="id_salle">
                                <?php
                                $liste_salle = $pdo->query("SELECT * FROM salle");
                                while ($donnees_salle = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <option value="<?php echo $donnees_salle['id_salle']; ?>"><?php echo $donnees_salle['id_salle'] . ' ' . $donnees_salle['titre'] . ' ' . $donnees_salle['adresse'] . ' ' . $donnees_salle['cp'] . ' ' .
                                                                                                    $donnees_salle['ville'] . ' ' . $donnees_salle['capacite'] . ' personne'; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class=" mb-3">
                            <label for="prix" class="form-label">Prix</label>
                            <input type="text" class="form-control" id="prix" name="prix" value="<?php echo $prix; ?>">
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
