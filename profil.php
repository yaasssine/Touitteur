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
require_once "php/ConversationPrivee.class.php";

// Si la page des profils est appelée sans pseudo passé en paramètre, le profil de l'utilisateur connecté est automatiquement affiché
if (!isset($_GET['p']) OR trim($_GET['p']) == '')
	$_GET['p'] = $utilisateurConnecte->getPseudo();

$profil = new Utilisateur($_GET['p']);
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<title><?php echo $profil->getPseudo(); ?> | Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<?php
		include_once "element/menu.php";
		include_once "element/message.php";
		?>
		
		<header>
			<table class="description">
				<tr>
					<td class="photo">
						<?php
						if ($profil->getPhoto())
							echo '<img src="img/' . $profil->getPseudo() . '.jpg" alt="' . $profil->getPseudo() . '" />';
						else
							echo '<img src="img/defaut.jpg" alt="' . $profil->getPseudo() . '" />';
						?>
					</td>
					<td class="description">
						<h1>
							<?php 
							echo $profil->getPseudo();
							if ($profil->estUtilisateur($utilisateurConnecte->getId()))
								echo '<button id="deconnexion">Déconnexion</button>';
							else if (!$utilisateurConnecte->suit($profil->getId()))
								echo '<button data-id="' . $profil->getId() . '" class="suivre">Suivre</button>';
							else
								echo '<button data-id="' . $profil->getId() . '" class="suivre suivi">Suivi</button>';
							?>
						</h1>
						<div class="nom"><?php echo $profil->getNom(); ?></div>
						<p><?php echo $profil->getStatut(); ?></p>
					</td>
				</tr>
			</table>
			
			<table class="stat">
				<tr>
					<td>
						<?php
						if ($profil->estUtilisateur($utilisateurConnecte->getId()))
							echo '<button id="parametre" style="vertical-align: middle;">Modifier le profil</button>';
						else if ($profil->suit($utilisateurConnecte->getId()) AND $profil->estSuiviPar($utilisateurConnecte->getId()))
							echo '<em style="color: #999;">' . $profil->getNom() . ' vous suit également.</em>';
						else if ($profil->suit($utilisateurConnecte->getId()))
							echo '<em style="color: #999;">' . $profil->getNom() . ' vous suit.</em>';
						else
							echo '<em style="color: #999;">' . $profil->getNom() . ' ne vous suit pas.</em>';
						?>
					</td>
					<td class="info <?php if (!isset($_GET['page'])) echo 'active'; ?>">
						<?php
							echo '<span><a href="profil.php?p=' . htmlentities($_GET['p']) . '">';
							$nbTouite = $profil->obtenirNbTouite();
							echo '<strong>' . $nbTouite . '</strong>';
							if ($nbTouite <= 1) 
								echo 'touite';
							else
								echo 'touites';
							echo '</a></span>';
						?>
					</td>
					<td class="info <?php if (isset($_GET['page']) AND $_GET['page'] == "abonnement") echo 'active'; ?>">
						<?php
							echo '<span><a href="profil.php?p=' . htmlentities($_GET['p']) . '&page=abonnement">';
							$nbAbonnement = $profil->obtenirNbAbonnement();
							echo '<strong>' . $nbAbonnement . '</strong>';
							if ($nbAbonnement <= 1) 
								echo 'abonnement';
							else
								echo 'abonnements';
							echo '</a></span>';
						?>
					</td>
					<td class="info <?php if (isset($_GET['page']) AND $_GET['page'] == "abonne") echo 'active'; ?>">
						<?php
							echo '<span><a href="profil.php?p=' . htmlentities($_GET['p']) . '&page=abonne">';
							$nbAbonne = $profil->obtenirNbAbonne();
							echo '<strong>' . $nbAbonne . '</strong>';
							if ($nbAbonne <= 1) 
								echo 'abonné';
							else
								echo 'abonnés';
							echo '</a></span>';
						?>
					</td>
				</tr>
			</table>
		</header>
		
		<section>
			<?php
			if (isset($_GET['page']) AND $_GET['page'] == "abonnement") {
				echo '<h2>Abonnements de ' . htmlentities($profil->getNom()) . '</h2>';
				
				$listeAbonnement = $profil->obtenirListeAbonnement();
				
				if (!$listeAbonnement)
					echo '<p>' . $profil->getNom() . ' ne suit personne.</p>';
				else {
					foreach ($listeAbonnement as $utilisateur)
						echo $utilisateur;
				}
			}
			else if (isset($_GET['page']) AND $_GET['page'] == "abonne") {
				echo '<h2>Abonnés à ' . htmlentities($profil->getNom()) . '</h2>';
				
				$listeAbonne = $profil->obtenirListeAbonne();
				
				if (!$listeAbonne)
					echo '<p>Aucun utilisateur ne suit ' . $profil->getNom() . '.</p>';
				else {
					foreach ($listeAbonne as $utilisateur)
						echo $utilisateur;
				}
			}
			else {
				echo '<h2>Touites publiés par ' . htmlentities($profil->getNom()) . '</h2>';
				
				$listeTouite = $profil->obtenirListeTouite();
				
				if (!$listeTouite)
					echo '<p>' . $profil->getNom() . ' n\'a posté aucun touite.</p>';
				else {
					foreach ($listeTouite as $touite)
						echo $touite;
				}	
			}
			?>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>