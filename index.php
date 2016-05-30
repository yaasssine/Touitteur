<?php
require_once "php/connexion.php";
require_once "php/fonction.php";

session_start();

require_once "php/Utilisateur.class.php";

global $utilisateurConnecte;

if (!empty($_SESSION['id']))
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));

require_once "php/Touite.class.php";
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<?php
		if (!empty($_SESSION['id'])) {
			include_once "element/menu.php";
			include_once "element/message.php";
		?>
		<section>
			<h2>Publier un touite</h2>
				<aside class="publier-touite-timeline">
					<button id="publier-touite">Publier</button>
					<span id="compteur-publier-touite">140</span>
				</aside>
				<p>
					<textarea id="texte-publier-touite" name="touite" placeholder="Que souhaitez-vous touitter?" maxlength="1000"></textarea>
				</p>
		</section>
		
		<section>
			<h1>Touites récents</h1>
			<div class="timeline">
			<?php
			$listeTimeline = $utilisateurConnecte->obtenirTimeline(0);
			
			if (!$listeTimeline)
				echo '<p>Aucun touite n\'a été publié récemment.</p>';
			else {
				if (count($listeTimeline) <= 10) {
					foreach ($listeTimeline as $touite)
						echo $touite;
				}
				else {
					for ($i = 0; $i < 10; $i++)
						echo $listeTimeline[$i];
					echo '<p data-curseur="' . $listeTimeline[10] . '" class="charger-touite"><a>Charger la suite...</a></p>';
				}
			}
			?>
			</div>
		</section>
		<?php
		}
		else
			include_once "element/connexion_inscription.php";
		
		include_once "element/footer.php";
		?>
	</body>
</html>