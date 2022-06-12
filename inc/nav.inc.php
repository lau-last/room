<nav class="navbar navbar-expand-lg navbar-dark bg-indigo">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URL; ?>index.php">Room</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?php echo URL; ?>qui_sommes_nous.php">Qui Sommes Nous</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL; ?>contact.php">Contact</a>
        </li>

        <?php if (user_is_connected()) { ?>

          <li class="nav-item">
            <a class="nav-link <?php echo class_active('/profil.php'); ?>" href="<?php echo URL; ?>profil.php">Profil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo class_active('/connexion.php'); ?>" href="<?php echo URL; ?>connexion.php?action=deconnexion">Déconnexion</a>
          </li>

        <?php } else { ?>

          <li class="nav-item">
            <a class="nav-link <?php echo class_active('/connexion.php'); ?>" href="<?php echo URL; ?>connexion.php">Connexion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo class_active('/inscription.php'); ?>" href="<?php echo URL; ?>inscription.php">Inscription</a>
          </li>

        <?php } ?>

        <?php if (user_is_admin()) { ?>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-expanded="false">Administration</a>
            <ul class="dropdown-menu " aria-labelledby="dropdown01">
              <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_salles.php">Gestion salles</a></li>
              <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_membres.php">Gestion membres</a></li>
              <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_produits.php">Gestion produits</a></li>
              <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_commandes.php">Gestion commandes</a></li>
              <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_des_avis.php">Gestion avis</a></li>
            </ul>
          </li>

        <?php } ?>


      </ul>
    </div>
  </div>
</nav>