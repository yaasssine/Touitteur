$(document).ready(function() {

	function compteurCaractereTouite(texte, afficheCompteur, nbCaractereMaxi) {
		var nbCaractereRestant = nbCaractereMaxi-texte.val().length;
		afficheCompteur.text(nbCaractereRestant);
		
		if (nbCaractereRestant >= 20) {
			if (afficheCompteur.hasClass('erreur'))
				afficheCompteur.removeClass('erreur');
			if (afficheCompteur.hasClass('avertissement'))
				afficheCompteur.removeClass('avertissement');	
		}
		else if (nbCaractereRestant >= 0 && nbCaractereRestant < 20) {
			if (afficheCompteur.hasClass('erreur'))
				afficheCompteur.removeClass('erreur');
			if (!afficheCompteur.hasClass('avertissement'))
				afficheCompteur.addClass('avertissement');	
		}
		else {
			if (!afficheCompteur.hasClass('erreur'))
				afficheCompteur.addClass('erreur');
			if (afficheCompteur.hasClass('avertissement'))
				afficheCompteur.removeClass('avertissement');	
		}
	}
	
	function supprimerTouite(idTouite, touiteReponse, touiteObjet) {
		$.post('php/post_supprimer_touite.php', { id_touite: idTouite, touite_reponse: touiteReponse },
			function(data) {
				if (data == null || data.erreur == true)
					console.log(data.erreurMsg);
				else {
					touiteObjet.slideUp(300);
					touiteObjet.remove(300);
				}
			}
		);
	}
		
	function posterTouite(texteTouite, idTouiteSource, touiteObjet) {
		$.post('php/post_publier_touite.php', { texte_touite: texteTouite, id_touite_source: idTouiteSource },
			function(data) {
				if (data == null || data.erreur == true)
					alert(data.erreurMsg);
				else {
					if (idTouiteSource == 0)
						touiteObjet.prepend(data.afficherTouite);
					else {
						touiteObjet.append(data.afficherTouite);
					}
				}
			}
		);
	}
	
	
	/*
		Lorsque qu'on a chargé les réponses d'un touite	
	*/
	function fonctionsChargerReponseTouite() {
		
		$('.touite .touite.reponse .action .repondre').click(function() {
			var touite = $(this).parent().parent().parent().parent().parent().parent().parent();
			
			touite.find('.touite.repondre textarea').focus();
			touite.find('.touite.repondre textarea').val('@' + $(this).parent().parent().parent().find('> a').text() + ' ');
		});
		
		/* Supprime une réponse d'un touite */
		$('.touite .touite.reponse .action .supprimer').click(function() {
			var reponseTouite = $(this).parent().parent().parent();
			supprimerTouite(reponseTouite.attr('data-id-reponse'), true, reponseTouite);
		});	
		
	}
	
	
	/*
		Lorsque qu'on a chargé d'autres touites de la timeline	
	*/
	function fonctionsChargerTimeline() {
		
		/* Compte les caractères d'une réponse à un touite */
		$('.touite .repondre textarea').keyup(function(e) {
			compteurCaractereTouite($(this), $(this).parent().find('span.compteur'), 140);
		});
		
		/* Supprime un touite normal */
		$('.touite .action .supprimer').click(function() {
			var touite = $(this).parent().parent().parent();
			supprimerTouite(touite.attr('data-id-touite'), false, touite);
		});
		
		/* Poste une réponse d'un touite */
		$('.touite button.publier-touite').click(function() {
			var touite = $(this).parent().parent().parent();
			posterTouite($(this).parent().parent().find('textarea').val(), touite.attr('data-id-touite'), touite.find('.liste-reponse'));
			touite.find('textarea').val('');
		});
		
		$('.touite .afficher-masquer-reponse').click(function() {
			afficherMasquerReponse($(this));
		});
		
		$('.touite .action .repondre').click(function() {
			var touite = $(this).parent().parent().parent();
			
			if (touite.find('.repondre').is(':hidden'))
				afficherMasquerReponse(touite.find('.afficher-masquer-reponse'));
			
			touite.find('.touite.repondre textarea').val('@' + touite.find('> a').text() + ' ');
			touite.find('.touite.repondre textarea').focus();
		});
			
	}
		
	fonctionsChargerTimeline();
	
	
	$('textarea#texte-publier-touite').keyup(function(e) {
		compteurCaractereTouite($(this), $('span#compteur-publier-touite'), 140);
	});
	
	$('button#publier-touite').click(function() {
		posterTouite($(this).parent().parent().find('textarea').val(), 0, $(this).parent().parent().parent().find('div.timeline'));
	});
	
	
	function afficherMasquerReponse(thisObjet) {
		if (thisObjet.hasClass('afficher')) {
			if (thisObjet.parent().find('.liste-reponse div').size() == 0) { //si n'y a aucune réponse de chargée
				var curseurReponseTouite = 0;
				chargerReponseTouite(thisObjet.parent().attr('data-id-touite'), curseurReponseTouite, thisObjet.parent().find('.liste-reponse'));
			}
			else
				thisObjet.parent().find('.liste-reponse .touite.reponse').slideDown(300);
			thisObjet.parent().find('.touite.repondre').slideDown(300);
			thisObjet.attr('data-etat-prec' , thisObjet.html());
			thisObjet.html('<i class="fa fa-angle-up"></i> Fermer');
			thisObjet.removeClass('afficher');
			thisObjet.addClass('masquer');
		}
		else if (thisObjet.hasClass('masquer')) {
			var nbReponse = 0;
			thisObjet.parent().find('.touite.reponse').each(function() {
				$(this).slideUp(300);
				nbReponse++;
			});
			thisObjet.parent().find('.touite.repondre').slideUp(300);
			if (thisObjet.parent().find('.touite.charger-reponse').is(':visible'))
				thisObjet.parent().find('.touite.charger-reponse').slideUp(300);
			thisObjet.html(thisObjet.attr('data-etat-prec'));
			thisObjet.removeClass('masquer');
			thisObjet.addClass('afficher');
		}
	}
	
	
	/*
		Charge les touites de la timeline 	
	*/
	function chargerTouiteTimeline(idMin) {
		$.get('php/get_touite_timeline.php', { id_min: idMin },
			function(data) {
				if (data != null) {
					if (data.length == 11) {
						for (var i = 0; i < 10; i++)
							$('.timeline').append(data[i]);
						$('.timeline').append('<p data-curseur="' + data[10] + '" class="charger-touite"><a>Charger la suite...</a></p>');
					}
					else {
						for (var i = 0; i < data.length; i++) {
							$('.timeline').append(data[i]);
						}
					}
					
					$('.charger-touite').click(function() {
						chargerTouiteTimeline($(this).attr('data-curseur'));
						$(this).remove();
					});
					
					fonctionsChargerTimeline();
				}
			}
		);
	}
	
	
	$('.timeline p.charger-touite').click(function() {
		chargerTouiteTimeline($(this).attr('data-curseur'));
		$(this).remove();
	});

	
	/*
		Charge les réponses 	
	*/
	function chargerReponseTouite(idTouite, idMin, thisObjet) {
		$.get('php/get_reponse_touite.php', { id: idTouite, id_min: idMin },
			function(data) {
				if (data != null) {
					if (data.length == 6) {
						for (var i = 0; i < 5; i++)
							thisObjet.prepend(data[i]);
						thisObjet.prepend('<div data-curseur="' + data[5].curseurSuivant + '" class="touite charger-reponse"><a>Afficher les réponses précédentes...</a></div>');
					}
					else {
						for (var i = 0; i < data.length; i++) {
							thisObjet.prepend(data[i]);
						}
					}
					thisObjet.find('.touite.reponse').slideDown(300);
					
					$('.touite .charger-reponse').click(function() {
						chargerReponseTouite($(this).parent().parent().attr('data-id-touite'), $(this).attr('data-curseur'), $(this).parent());
						$(this).remove();
					});
					
					fonctionsChargerReponseTouite();
					
				}
			}
		);
	}

});