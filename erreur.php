<?php
require_once "php/connexion.php";
require_once "php/fonction.php";
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Touitteur</title>
		<meta charset="UTF-8" />
		<link href="css/main.css" rel="stylesheet" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
		<link href="http://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css" />
		<link href="http://fonts.googleapis.com/css?family=Satisfy:400" rel="stylesheet" type="text/css" />
		<script>document.location.href="index.php";</script>
	</head>
	
	<body>
		<section class="home">
			<h1 id="logo">Touitteur<i class="fa fa-comments"></i></h1>
			<h2>JavaScript est nécessaire!</h2>
			<p>Vous devez activer JavaScript pour aller sur le site.</p>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>