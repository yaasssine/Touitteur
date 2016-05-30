<?php
require_once "php/connexion.php";
require_once "php/fonction.php";

session_start();
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Page introuvable | Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<section class="home">
			<h1 id="logo">Touitteur<i class="fa fa-comments"></i></h1>
			<h2>Erreur 404</h2>
			<p>La page demandée est introuvable...</p>
			<p><a href="index.php">Retourner à l'accueil</a></p>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>