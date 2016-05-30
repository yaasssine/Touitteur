$(document).ready(function() {

	function actualiserCompteurMsgNonLu() {
		$.get('php/get_compteur_message_non_lu.php', null,
			function(data) {
				if (data != null) {
					$('a#afficher-message span').text(data);
					console.log(data);
				}
			}
		);
		setTimeout(actualiserCompteurMsgNonLu, 1000);
	}
		
	actualiserCompteurMsgNonLu();


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

	function fonctionsListeMessage() {
		$('.liste-msg div').click(function() {
			$.get('php/get_message_conversation.php', { id: $(this).attr('data-id-destinataire') },
				function(data) {
					if (data != null) {
						$('#message.fenetre').html(data);
						element = document.getElementById('convers');
                        element.scrollTop = element.scrollHeight;
						fonctionsConversation();	
					}
				}
			);
		});
	}
	
	function fonctionsConversation() {
		function actualiserMessageConversation() {
			$.get('php/get_message.php', { id: $('.zone-envoi-message').attr('data-id-destinataire') },
				function(data) {
					if (data != null) {
						$('#message.fenetre ul.conversation').html(data);
						//console.log('ok');
					}
				}
			);
			setTimeout(actualiserMessageConversation, 1000);
		}
		actualiserMessageConversation();
		
		$('#message button.retour').click(function() {
			$.get('php/get_message_liste.php', null,
				function(data) {
					if (data != null) {
						$('#message.fenetre').html(data);
						
						fonctionsListeMessage();
					}
				}
			);
		});
		
		$('#message button#envoyer-message').click(function() { //BOUTON ENVOYER MESSAGE
			$.post('php/post_envoyer_message.php', { id: $(this).parent().parent().attr('data-id-destinataire'), texte: $('#message textarea#texte-envoyer-message').val() },
				function(data) {
					if (data == null || data.erreur == true)
						alert(data.erreurMsg);
					else {
						$('ul.conversation').append(data);
						$('#message textarea#texte-envoyer-message').val('');
                        element = document.getElementById('convers');
                        element.scrollTop = element.scrollHeight;	
					} 
				}
			);
		});
		
		$('textarea#texte-envoyer-message').keyup(function(e) { //TEXTAREA ENVOYER MESSAGE
			compteurCaractereTouite($(this), $('span#compteur-envoyer-message'), 140);
		});
	}
	
	fonctionsListeMessage();

});