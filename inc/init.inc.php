<?php

// Connexion à la BDD : eshop
$host = 'mysql: ;dbname= ';
$login = ' ';
$password = ' ';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
$pdo = new PDO($host, $login, $password, $options);



// on ouvre une session :
session_start();

// Déclaration de constante :
// constante représentant l'url absolue racine de notre projet eshop
define('URL', ' '); // à modifier lors de la mise en ligne

// chemin racine serveur pour l'enregistrement des images depuis gestion_articles.php
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']); // info récupérée dans la superglobal donc il ne sera pas necessaire de la changer

// chemin depuis notre serveur vers le dossier de notre projet
define('PROJECT_PATH', '/'); // depuis notre dossier www ou htdocs, vers la racinne de notre projet. attention de ne pas oublier le premier /

// echo  ROOT_PATH . PROJECT_PATH . '<br>';


// variable destinée à afficher des messages utilisateur. Cette variable est appelée en dessous du titre de nos page. Sur toutes nos pages.
$msg = '';
