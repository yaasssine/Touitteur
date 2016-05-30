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
		<title>Modifier le profil | Touitteur</title>
		<?php
		require_once "element/html_head.php";
		?>
	</head>
	
	<body>
		<?php
		include_once "element/menu.php";
		include_once "element/message.php";
		?>
		
		<section id="supprimer-compte" class="fenetre">
			<!--<h1>Supprimer le compte</h1>
			<p>Votre compte ainsi que tous vos touites et messages privés seront effacés de manière définitive. Confirmez-vous?</p>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<p><input id="mdp" type="password" name="mdp" placeholder="Mot de passe" maxlength="200" /></p>
				<p><input type="submit" name="supprimer-compte" value="Supprimer le compte définitivement" /></p>
			</form>-->
			<p>Fonctionnalité à venir.</p>
		</section>
		
		<section>
			<h1>Modifier le profil</h1>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
				<table>
					<tr>
						<td><label for="email">Adresse email</label></td>
						<td><input id="email" type="text" name="email" value="<?php echo $utilisateurConnecte->getEmail(); ?>" maxlength="100" /></td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
							<?php if (isset($_POST['modifier_compte'])) $utilisateurConnecte->setEmail($_POST['email']); ?>
						</td>
					</tr>
					<tr>
						<td><label for="pseudo">Pseudo</label></td>
						<td><input id="pseudo" type="text" name="pseudo" value="<?php echo $utilisateurConnecte->getPseudo(); ?>" disabled="disabled" maxlength="20" /></td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
						</td>
					</tr>
					<tr>
						<td><label for="nom">Nom complet</label></td>
						<td><input id="nom" type="text" name="nom" value="<?php echo $utilisateurConnecte->getNom(); ?>" maxlength="40" /></td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
							<?php if (isset($_POST['modifier_compte'])) $utilisateurConnecte->setNom($_POST['nom']); ?>
						</td>
					</tr>
					<tr>
						<td><label for="photo">Photo du profil</label></td>
						<td>
							<input id="photo" type="file" name="photo" accept="image/jpeg,image/jpg" />
							<p>Seul le format JPG est autorisé. Taille maxi : 1Mo.</p>
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
							<?php 
							if (isset($_POST['modifier_compte']) AND !empty($_FILES['photo']['tmp_name'])) {
								try {
									if ($_FILES['photo']['error'] > 0)
										throw new Exception("Une erreur s'est produite.");
									else if ($_FILES['photo']['size'] > 1048576)
										throw new Exception("L'image est trop lourde.");
									else {
										$extension = array('jpg', 'jpeg');
										$extensionUtilisateur = strtolower(substr(strrchr($_FILES['photo']['name'], '.'), 1));
										if (in_array($extensionUtilisateur, $extension)) {
											$repertoireImg = 'img/' . $utilisateurConnecte->getPseudo() . '.jpg';
											if (move_uploaded_file($_FILES['photo']['tmp_name'], $repertoireImg)) 
												$utilisateurConnecte->setPhoto(true);
											else
												echo '<p class="erreur">Impossible d\'uploader l\'image.</p>';
										}
									}
								} catch (Exception $e) {
									echo '<p class="erreur">' . $e->getMessage() . '</p>';
								}
							} 
							?>
						</td>
					</tr>
					<tr>
						<td><label for="statut">Statut</label></td>
						<td>
							<textarea id="statut" type="text" name="statut" maxlength="160"><?php echo $utilisateurConnecte->getStatut(); ?></textarea>
							<p>160 caractères maximum.</p>
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
							<?php if (isset($_POST['modifier_compte'])) $utilisateurConnecte->setStatut($_POST['statut']); ?>
						</td>
					</tr>
					<tr>
						<td><span id="afficher-supprimer-compte" class="lien"><i class="fa fa-trash"></i> Supprimer le compte</span></td>
						<td><input type="submit" name="modifier_compte" value="Sauvegarder les changements" /></td>
					</tr>
				</table>
			</form>
		</section>
		
		<section>
			<h1>Changer de mot de passe</h1>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<table>
					<tr>
						<td><label for="ancien_mdp">Ancien mot de passe</label></td>
						<td>
							<input id="ancien_mdp" type="password" name="ancien_mdp" placeholder="Ancien mot de passe" maxlength="200" />
						</td>
					</tr>
					<tr>
						<td><label for="nouveau_mdp">Nouveau mot de passe</label></td>
						<td>
							<input id="nouveau_mdp" type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" maxlength="200" style="margin-top: 10px;" />
						</td>
					</tr>
					<tr>
						<td><label for="confirmation_mdp">Confirmer le mot de passe</label></td>
						<td>
							<input id="confirmation_mdp" type="password" name="confirmation_mdp" placeholder="Confirmation mot de passe" maxlength="200" style="margin-top: 10px;" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="info">
							<?php if (isset($_POST['modifier_mdp'])) $utilisateurConnecte->setMdp($_POST['ancien_mdp'], $_POST['nouveau_mdp'], $_POST['confirmation_mdp']); ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="modifier_mdp" value="Modifier le mot de passe" /></td>
					</tr>
				</table>
			</form>
		</section>
		
		<?php
		include_once "element/footer.php";
		?>
	</body>
</html>