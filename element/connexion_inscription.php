<section class="home">
	<h1 id="logo">Touitteur<i class="fa fa-comments"></i></h1>	
	<h2>Inscrivez-vous pour échanger avec vos amis.</h2>
			
	<form id="connexion" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">		
		<?php
		if (isset($_POST['connexion'])) {
			try {
				if (!empty($_POST['identifiant']) AND !empty($_POST['mdp'])) {
					$req = $bdd->prepare('SELECT id, pseudonyme, email, motPasse FROM Touitos WHERE pseudonyme=:identifiant OR email=:identifiant');
					$req->bindValue(':identifiant', htmlentities($_POST['identifiant']));
					$req->execute();
					$rep = $req->fetch(PDO::FETCH_ASSOC);
					if (verifierIdentifiantConnexion($_POST['identifiant'], $rep['pseudonyme'], $rep['email'], $_POST['mdp'], $rep['motPasse']))
						$_SESSION['id'] = $rep['id'];
						header('Location: ' . $_SERVER['PHP_SELF']);
				}
				else
					throw new Exception("Votre email ou pseudo ainsi que votre mot de passe sont nécessaires pour vous connecter.");
			} catch (Exception $e) {
				echo '<p id="erreur-connexion" class="erreur">' . $e->getMessage() . '</p>';
			}
		}
		?>
		<input type="text" name="identifiant" placeholder="Adresse email ou pseudo" maxlength="200" required="required" />
		<input type="password" name="mdp" placeholder="Mot de passe" maxlength="200" required="required" />
		<input type="submit" name="connexion" value="Se connecter" />
	</form>
			
	<button id="afficher-connexion">Se connecter à Touitteur</button>
	
	<div class="separateur">
		<hr />
		<span>ou</span>
	</div>
	
	<form id="inscription" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<?php
		if (isset($_POST['inscription'])) {
			try {
				if (verifierEmail($_POST['email']) AND verifierPseudo($_POST['pseudo']) AND verifierNom($_POST['nom']) AND verifierMdp($_POST['mdp']) AND verifierConfirmationMdp($_POST['mdp'], $_POST['confirmation_mdp'])) {
					$req = $bdd->prepare('INSERT INTO Touitos VALUES(null, :pseudo, :email, :mdp, :nom, "N", null)');
					$req->bindValue(':pseudo', htmlentities($_POST['pseudo']));
					$req->bindValue(':email', htmlentities($_POST['email']));
					$req->bindValue(':mdp', sha1(htmlentities($_POST['mdp'])));
					$req->bindValue(':nom', htmlentities($_POST['nom']));
					$req->execute();
					
					//Connexion de l'utilisateur qui vient de s'inscrire
					$req = $bdd->prepare('SELECT id FROM Touitos WHERE pseudonyme=:pseudo');
					$req->bindValue(':pseudo', htmlentities($_POST['pseudo']));
					$req->execute();
					$rep = $req->fetch(PDO::FETCH_ASSOC);
					$_SESSION['id'] = $rep['id'];
					header('Location: ' . $_SERVER['PHP_SELF']);
				}
			} catch (Exception $e) {
				echo '<p class="erreur">' . $e->getMessage() . '</p>';
			}
		}
		?>
		<input type="email" name="email" placeholder="Adresse email" maxlength="100" required="required" />
		<input type="text" name="pseudo" placeholder="Pseudo" maxlength="20" required="required" />
		<input type="text" name="nom" placeholder="Nom complet" maxlength="40" required="required" />
		<input type="password" name="mdp" placeholder="Mot de passe" maxlength="200" required="required" />
		<input type="password" name="confirmation_mdp" placeholder="Confirmation mot de passe" maxlength="200" required="required" />
		<input type="submit" name="inscription" value="S'inscrire" />
	</form>
	
	<p>Pas encore inscrit? <a id="afficher-inscription">Inscrivez-vous</a></p>
</section>