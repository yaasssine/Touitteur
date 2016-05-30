<nav>
	<div>
		<div id="logo">
			<a href="index.php">Touitteur<i class="fa fa-comments"></i></a>
		</div>
		<div class="menu">
			<form method="get" action="recherche.php">
				<input type="text" name="req" placeholder="Rechercher" title="Rechercher un utilisateur" value="<?php if (!empty($_GET['req'])) echo htmlentities($_GET['req']); ?>" />
			</form>
			<ul>
				<li><a href="index.php" title="Afficher la timeline">Timeline</a></li>
				<li><a id="afficher-message" title="Accéder aux messages privés">Messages <span><?php echo $utilisateurConnecte->obtenirNbMsgPriveNonLu(); ?></span></a></li>
				<li><a href="profil.php" title="Voir/modifier le profil ou se déconnecter">Profil</a></li>
			</ul>
		</div>
	</div>
</nav>