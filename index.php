<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// Récupération des catégories
$liste_categories = $pdo->query("SELECT DISTINCT categorie FROM salle ORDER BY categorie");
$liste_ville = $pdo->query("SELECT DISTINCT ville FROM salle ORDER BY ville");
$liste_capacite = $pdo->query("SELECT DISTINCT capacite FROM salle ORDER BY capacite");
$liste_prix = $pdo->query("SELECT DISTINCT prix FROM produit ORDER BY prix");

// Récupération des articles
if (isset($_GET['categorie'])) {
    $liste_salle = $pdo->prepare("SELECT * FROM salle LEFT JOIN produit USING (id_salle) WHERE categorie = :categorie ORDER BY categorie, titre");
    $liste_salle->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $liste_salle->execute();
} else if (isset($_GET['ville'])) {
    $liste_salle = $pdo->prepare("SELECT * FROM salle LEFT JOIN produit USING (id_salle) WHERE ville = :ville ORDER BY categorie, titre");
    $liste_salle->bindParam(':ville', $_GET['ville'], PDO::PARAM_STR);
    $liste_salle->execute();
} else if (isset($_GET['capacite'])) {
    $liste_salle = $pdo->prepare("SELECT * FROM salle LEFT JOIN produit USING (id_salle) WHERE capacite = :capacite ORDER BY capacite");
    $liste_salle->bindParam(':capacite', $_GET['capacite'], PDO::PARAM_STR);
    $liste_salle->execute();
} else if (isset($_GET['prix'])) {
    $liste_salle = $pdo->prepare("SELECT * FROM produit LEFT JOIN salle USING (id_salle) WHERE prix = :prix ORDER BY prix");
    $liste_salle->bindParam(':prix', $_GET['prix'], PDO::PARAM_STR);
    $liste_salle->execute();
} else {
    $liste_salle = $pdo->query("SELECT * FROM salle LEFT JOIN produit USING (id_salle) ORDER BY categorie, titre");
}
include 'inc/header.inc.php';
include 'inc/nav.inc.php'
?>
<main class="container">
    <div class="row">
        <div class="col-sm-3 mt-5">
            <!-- categorie -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Catégories : </li>
                <?php
                while ($ligne = $liste_categories->fetch(PDO::FETCH_ASSOC)) {
                    echo '<li class="list-group-item"><a href="?categorie=' . $ligne['categorie'] . '">' . ucfirst($ligne['categorie']) . '</a></li>';
                }
                ?>
            </ul>
            <br>
            <!-- villes -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Villes : </li>
                <?php
                while ($ligne = $liste_ville->fetch(PDO::FETCH_ASSOC)) {
                    echo '<li class="list-group-item"><a href="?ville=' . $ligne['ville'] . '">' . ucfirst($ligne['ville']) . '</a></li>';
                }
                ?>
            </ul>
            <br>
            <!-- capacité -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Capacité : </li>
                <?php
                while ($ligne = $liste_capacite->fetch(PDO::FETCH_ASSOC)) {
                    echo '<li class="list-group-item"><a href="?capacite=' . $ligne['capacite'] . '">' . ucfirst($ligne['capacite']) . '</a></li>';
                }
                ?>
            </ul>

            <br>
            <!-- prix -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Prix</li>
                <?php
                while ($ligne = $liste_prix->fetch(PDO::FETCH_ASSOC)) {
                    echo '<li class="list-group-item"><a href="?prix=' . $ligne['prix'] . '">' . ucfirst($ligne['prix']) . '</a></li>';
                }
                ?>
            </ul>
            <br>
            <!-- date arrivee -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">Start date:</li>
                <li>
                    <input class="form-control" type="date" id="start" name="trip-start">
                </li>
            </ul>
            <br>
            <!-- date depart -->
            <ul class="list-group">
                <li class="list-group-item bg-indigo text-white" aria-current="true">End date:</li>
                <li>
                    <input class="form-control" type="date" id="end" name="trip-end">
                </li>
            </ul>
        </div>
        <div class="col-sm-9 mt-5">
            <div class="row">
                <?php
                while ($salle = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
                    // var_dump($salle);
                    echo '
                    <div class=" col-sm-3 mb-3">
                    <div class="card">
                        <img src="' . URL . 'assets/img/' . $salle['photo'] . '" class="card-img-top" alt="Une image salle : ' . $salle['titre'] . '">
                        <div class="card-body">
                            <h5 class="card-title">' . ucfirst($salle['titre']) . '</h5>
                            <p class="card-text">Catégorie : ' . $salle['categorie'] . '<br>Prix : ' . $salle['prix'] . ' €<br>Capacité : ' . $salle['capacite'] . '<br>Ville de : ' . $salle['ville'] . '</p>
                            <p class="card-text card-text-description">' . $salle['description'] . '</p>
                            <a href="fiche_salle.php?id_salle=' . $salle['id_salle'] . '" class="btn btn-outline-primary w-100">Fiche salle</a>
                        </div>
                    </div>
                </div>';
                }
                ?>
            </div>
        </div>
    </div>
</main>
<?php
include 'inc/footer.inc.php';
