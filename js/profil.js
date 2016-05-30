$(document).ready(function() {
	
	//Bouton Déconnexion du profil de l'utilisateur connecté
	$('button#deconnexion').click(function() {
		$(location).attr('href','deconnexion.php');
	});
	
	//Bouton Paramètres du profil de l'utilisateur connecté
	$('button#parametre').click(function() {
		$(location).attr('href','parametres.php');
	});
	
});