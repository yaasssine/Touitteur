<?php
require_once "php/connexion.php";
require_once "php/fonction.php";

session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Déconnexion | Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<section class="home">
			<h1 id="logo">Touitteur<i class="fa fa-comments"></i></h1>
			<h2>Déconnexion</h2>
			<p>Vous avez été déconnecté.</p>
			<p><a href="index.php">Aller à l'accueil</a></p>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>