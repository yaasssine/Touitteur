$(document).ready(function() {
	
	$('form#connexion').hide();
	$('a#afficher-inscription').parent().hide();
	
	function afficherConnexion() {
		$('button#afficher-connexion').hide();
		$('form#connexion').slideDown(300);
		$('form#inscription').slideUp(300);
		$('a#afficher-inscription').parent().show();
	}
	
	function afficherInscription() {
		$('a#afficher-inscription').parent().hide();
		$('form#inscription').slideDown(300);
		$('form#connexion').slideUp(300);
		$('button#afficher-connexion').show();
	}
	
	if ($('form#connexion p#erreur-connexion').hasClass('erreur'))
		afficherConnexion();
	
	$('section.home button#afficher-connexion').click(function() {
		afficherConnexion();
	});
	
	$('section.home a#afficher-inscription').click(function() {
		afficherInscription();
	});

});