$(document).ready(function() {
	
	$('div.fond-fenetre').hide();
	$('section.fenetre').hide();
	
	var fenetreOuverte = false;
	var nomFenetreOuverte = '';
	
	function afficherFenetre(nomFenetre) {
		if ((nomFenetreOuverte == 'message' && nomFenetre == 'mention') || (nomFenetreOuverte == 'mention' && nomFenetre == 'message')) {
			masquerFenetre();
			setTimeout(function() {
				$('body').css('overflow','hidden');
				$('div.fond-fenetre').fadeIn();
				$('section#' + nomFenetre + '.fenetre').show('slide', { direction: 'up' }, 300);
				fenetreOuverte = true;
				nomFenetreOuverte = nomFenetre;
			}, 200);
		}
		else if (!fenetreOuverte) {
			$('body').css('overflow','hidden');
			$('div.fond-fenetre').fadeIn();
			$('section#' + nomFenetre + '.fenetre').show('slide', { direction: 'up' }, 300);
			fenetreOuverte = true;
			nomFenetreOuverte = nomFenetre;
		}
		else {
			$('section#' + nomFenetreOuverte + '.fenetre').effect('shake');
		}
	}
	
	function masquerFenetre() {
		if (fenetreOuverte) {
			$('section.fenetre').hide("slide", { direction: "up" }, 300);
			setTimeout(function() {
				$('div.fond-fenetre').fadeOut();
			}, 200);
			$('body').css('overflow','auto');
			fenetreOuverte = false;
			nomFenetreOuverte = '';
		}
	}
	
	$('span#afficher-supprimer-compte').click(function() {
		afficherFenetre('supprimer-compte');
	});
	
	$('a#afficher-message').click(function() {
		afficherFenetre('message');
	});
	
	$('div.fond-fenetre').click(function() {
		masquerFenetre();
	});

});