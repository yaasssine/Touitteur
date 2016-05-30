<?php
require_once "php/connexion.php";
require_once "php/fonction.php";

session_start();

if (empty($_SESSION['id']))
	header('Location: index.php');

require_once "php/Utilisateur.class.php";

global $utilisateurConnecte;
$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));

require_once "php/Touite.class.php";
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>Recherche de <?php echo htmlentities($_GET['req']); ?> | Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<?php
		include_once "element/menu.php";
		include_once "element/message.php";
		?>
		
		<section>
			<?php
			if (!empty($_GET['req'])) {
				echo '<h2>Recherche pour ' . htmlentities($_GET['req']) . '</h2>';
				
				try {
					$req = $bdd->prepare('SELECT id FROM Touitos WHERE pseudonyme=:pseudo OR nom=:nom');
					$req->bindValue(':pseudo', htmlentities($_GET['req']));
					$req->bindValue(':nom', htmlentities($_GET['req']));
					$req->execute();
			
					while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
						$listeUtilisateur[] = new Utilisateur(intval($rep['id']));
					}
					
					if (empty($listeUtilisateur))
						echo '<p>Il n\'y a aucun utilisateur du nom de ' . htmlentities($_GET['req']) . '.</p>';
					else {
						foreach ($listeUtilisateur as $utilisateur)
							echo $utilisateur;
						if (count($listeUtilisateur) == 1)
							echo '<p style="color: #999;">1 utilisateur trouvé</p>';
						else
							echo '<p style="color: #999;">' . count($listeUtilisateur) . ' utilisateurs trouvés</p>';
					}
				}
				catch (Exception $e) {
					echo '<p class="erreur">' . $e->getMessage() . '</p>';
				}
			}
			else
				echo '<h2>Quel utilisateur souhaitez-vous trouver?</h2>';
			?>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>