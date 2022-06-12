<?php

include 'inc/init.inc.php';
include 'inc/functions.inc.php';

if (isset($_GET['id_salle'])) {

  $recup_salle = $pdo->prepare("SELECT * FROM salle INNER JOIN produit USING (id_salle)");
  $recup_salle->bindParam('id_salle', $_GET['id_salle'], PDO::PARAM_STR);
  $recup_salle->execute();

  // On vérifie si on a une ligne
  if ($recup_salle->rowCount() < 1) {
    // on redirige vers index
    // header('location:index.php');
  }
} else {
  header('location:fiche_produit.php');
}


$info_salle = $recup_salle->fetch(PDO::FETCH_ASSOC);


// Requete tableau dispo
$tableau_salle = $pdo->prepare("SELECT * FROM produit INNER JOIN salle USING (id_salle) INNER JOIN commande USING (id_produit) WHERE salle.id_salle = produit.id_salle AND produit.id_produit = commande.id_produit AND date_arrivee > CURDATE() ORDER BY YEAR(date_arrivee)");
$tableau_salle->bindParam('id_salle', $_GET['id_salle'], PDO::PARAM_STR);
$tableau_salle->bindParam('id_produit',  $infos_salle['id_produit'], PDO::PARAM_STR);
$tableau_salle->execute();



//requete avis

// -  Enregistrement des avis
if (isset($_POST['commentaire']) && isset($_POST['note'])) {
  $commentaire = trim($_POST['commentaire']);
  $note = trim($_POST['note']);

  // Contrôle : la note & le commentaire ne doivent pas être vides.
  if (empty($note)) {
    $msg .= '<div class="alert alert-danger mt-3">Attention, la note est obligatoire</div>';
  }
  if (empty($commentaire)) {
    $msg .= '<div class="alert alert-danger mt-3">Attention, le commentaire est obligatoire</div>';
  }
  if (empty($msg)) {
    $enregistrement = $pdo->prepare("INSERT INTO avis (id_membre,id_salle,commentaire,note,date_enregistrement_avis) VALUES (:id_membre,:id_salle,:commentaire, :note, NOW()) ");

    $enregistrement->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
    $enregistrement->bindParam(':id_salle', $info_salle['id_salle'], PDO::PARAM_STR);
    $enregistrement->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $enregistrement->bindParam(':note', $note, PDO::PARAM_STR);
    $enregistrement->execute();
    // header('location:fiche_produit.php');

  }

  $avis = $pdo->prepare("SELECT * FROM avis INNER JOIN membre USING (id_membre) INNER JOIN salle USING (id_salle) WHERE id_salle = :id_salle ");
  $avis->bindParam('id_salle', $_GET['id_salle'], PDO::PARAM_STR);
  $avis->execute();
}
//variable confirmation commande
$confirmation = '';

//Enregistrement commande

if (isset($_POST['id_membre']) && isset($_POST['id_produit'])) {

  $enregistrement = $pdo->prepare("INSERT INTO commande (id_membre,id_produit, date_enregistrement) VALUES (:id_membre,:id_produit, NOW()) ");

  $enregistrement->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
  $enregistrement->bindParam(':id_produit',  $infos_salle['id_produit'], PDO::PARAM_STR);

  $enregistrement->execute();

  $confirmation .= '<div class="alert alert-success mt-3">Confirmation de la réservation</div>';
  // header('location:produits.php?id_salle='$_GET["id_salle"]');
}

// Les affichages dans la page commencent depuis la ligne suivante
//-----------------------------------------------------------------
include 'inc/header.inc.php';
// include 'inc/nav.inc.php';


?>


<main class="container">

  <section class="team_spad">

    <div class="mt-5 p-5 rounded">
      <h1 class="mt-5 text-center"> <?php echo $info_salle['titre']; ?></h1>

      <br>
    </div>




    <div class="row">

      <div class="col-sm-6">
        <img src="<?php echo URL . 'assets/img/' .  $info_salle['photo']; ?>" alt="Image de  l'salle : <?php echo $info_salle['titre']; ?>" class="img-thumbnail w-15 text-center">
        <div class="section-title mt-4">
          <h5>Description :</h5>
        </div>
        <p><?php echo $info_salle['description']; ?></p>
        <?php
        echo $confirmation
        ?>
      </div>

      <div class="col-sm-6 ">
        <div class="section-title">
          <input type="hidden" name="id_salle" value="<?php echo $info_salle['id_salle']; ?>">

          <?php
          if (user_is_connected()) {
            echo "<a href='#tableau' class='btn btn-outline-success w-100 shadow mb-2'>Réserver</a>";
          } else {
            echo "<div class='mt-5 mb-5'> Veuillez vous <a href='connexion.php'>connecter</a> ou vous <a href='inscription.php'>inscrire</a> afin de réserver</div>";
          }
          ?>
          <h5 class="mt-3">Localisation :</h5>
        </div>
        <?php
        $ville = $info_salle['ville'];
        $adresse = $info_salle['adresse'];
        $cp = $info_salle['cp'];
        $ville_url = str_replace(' ', '+', $ville);
        $adresse_url = str_replace(' ', '+', $adresse);
        $MapCoordsUrl = urlencode($cp . '+' . $ville_url . '+' . $adresse_url);
        ?>
        <iframe width="100%" height="450" src="http://maps.google.fr/maps?q=<?php echo $MapCoordsUrl; ?>&amp;t=h&amp;output=embed"></iframe>
      </div>
    </div>

    <div class="col-12">
      <div class="section-title mt-4">
        <h5>Informations complémentaires :</h5>
      </div>
      <div>
        <div class="row">
          <div class="col-sm-6">
            <div class="mb-3">
              <i class="fas fa-users "></i>
              <p>Capacité : <?php echo $info_salle['capacite']; ?></p>
              <i class="fas fa-folder"></i>
              <p>Catégorie : <?php echo $info_salle['categorie']; ?></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="mb-3">
              <i class="fas fa-map-marked-alt"></i>
              <p>Adresse : <?php echo $info_salle['adresse'] . ' ' .  $info_salle['cp'] . ' ' . $info_salle['ville']; ?></p>
              <i class="fas fa-euro-sign"></i>
              <p>Prix : <?php echo $info_salle['prix']; ?></p>
            </div>
          </div>
        </div>

      </div>


      <a href="produits.php" class="btn btn-outline-success w-100 shadow mb-5 mt-5" id="tableau">Retourner au catalogue</a>


    </div>
  </section>




  <!-- Affichage Tableau dispo -->

  <div class="row mx-auto mt-5">
    <div class="col-sm-12 col-md-10 col-lg-12  mx-auto">
      <h3 class="text-center mb-3"> Tableau des disponibilités</h3>
      <table class="table table-striped table-hover shadow pt-3">
        <tr class="bg-indigo text-dark text-center">
          <th>Date Arrivée</th>
          <th>Date Départ</th>
          <th>État</th>
          <th>Prix</th>
          <th>Réserver</th>
        </tr>

        <form action="" method="POST" class="pb-5">
          <?php


          while ($info_salle = $tableau_salle->fetch(PDO::FETCH_ASSOC)) {
            //  var_dump($info_salle);

            if ($info_salle['etat'] == 'reserver') {
              $info_salle['etat'] = 'Réservé';
            } else {
              $info_salle['etat'] = 'Libre';
            }

            if (empty($info_salle)) {

              echo '<div class="section-content">';
              echo '<span class="text-center">Pas de dispos</span>';
              echo '</div>';
            }

            $date_arrivee = date('d/m/Y ', strtotime($info_salle['date_arrivee']));
            $date_depart = date('d/m/Y ', strtotime($info_salle['date_depart']));
            // $date_jour = date("d/m/Y");;
            //  var_dump($info_salle);


            if ($date_arrivee !== null && $date_depart !== null) {




              echo '<tr>';
              echo '<td class="text-center ">' . $date_arrivee . '</td>';
              echo '<td class="text-center ">' . $date_depart . '</td>';


              echo '<td class="text-center">' . $info_salle['etat'] . '</td>';
              echo '<td class="text-center ">' . $info_salle['prix'] . '€</td>';

              if (user_is_connected()) {
                if ($info_salle['etat'] == 'Réservé') {
                  echo "<td class='text-center'><button type='submit' class='btn btn-outline-success w-100' disabled>Occupé</button> </td>";
                } else {
                  echo "<td class='text-center'><button type='submit' class='btn btn-outline-success w-100'  name='reserver'>Réserver</button> </td>";
                }
              } else {
                echo "<td class='text-center '><div class='mt-5 mb-5'> Veuillez vous <a href='connexion.php'>connecter</a> ou vous <a href='inscription.php'>inscrire</a> afin de réserver</div> </td>";
              }
            } else {
              echo '<div class="section-content">';
              echo '<span class="text-center">Pas de dispos</span>';
              echo '</div>';
            }
          }
          //  var_dump($info_salle);

          ?>
        </form>
      </table>
    </div>

    <!-- Affichage des avis-->
    <div id="avis" class="col-sm-12 col-md-10 col-lg-12 mx-auto mt-5">
      <h3 class="text-center"> Votre avis compte !!</h3>

      <div class="col-12 bg-indigo mx-auto shadow rounded pt-2 mb-5">
        <div class="my-3">
          <!-- Affichage de tous les commentaires -->
          <?php
          $avis = $pdo->prepare("SELECT * FROM avis INNER JOIN membre USING (id_membre) INNER JOIN salle USING (id_salle) WHERE id_salle = :id_salle ");
          $avis->bindParam('id_salle', $_GET['id_salle'], PDO::PARAM_STR);
          $avis->execute();

          while ($ligne_avis = $avis->fetch(PDO::FETCH_ASSOC)) {
            //var_dump($ligne_avis);
            $date_avis = date('d/m/Y à H:m', strtotime($ligne_avis['date_enregistrement_avis']));
            echo '<strong>Pseudo : </strong>' . ' ' . $ligne_avis['pseudo'] . '<br> Le ' . $date_avis . '<br><strong>Note : </strong>' . $ligne_avis['note'] . '/10 <br><strong>Commentaire : </strong> ' . $ligne_avis['commentaire'] . '<hr>';
          }

          ?>
        </div>
        <form action="" method="POST" class="pb-5">
          <?php
          // on  affiche la variable qui peut contenir des commentaires pour l'utilisateur.
          echo $msg;
          ?>
          <div class="my-3">
            <h5 class="text-center"><?php echo ucfirst($_SESSION['membre']['prenom']); ?>, vous aussi laissez votre avis aussi !!</h5>

          </div>
          <div class="mb-3">
            Note :
            <select class="form-select" id="note" name="note">
              <option selected>-- Choisir une note --</option>
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire :</label>
            <textarea class="form-control mt-2" name="commentaire" placeholder="Votre commentaire" rows="5" id="commentaire"></textarea>
          </div>
          <?php
          if (user_is_connected()) {
            echo ' <button type="submit" class="btn btn-outline-success w-100" id="enregistrer" name="enregistrer">Enregistrer</button>';
          } else {
            echo "<div class='mt-5 mb-5'> Veuillez vous <a href='connexion.php'>connecter</a> ou vous <a href='inscription.php'>inscrire</a> afin de laisser un commentaire</div>";
          }
          //var_dump($_SESSION)      ;

          ?>
        </form>

      </div>
    </div>
  </div>

</main>
<?php
include 'inc/footer.inc.php';
?>