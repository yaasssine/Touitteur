$(document).ready(function() {
	
	$('button.suivre').click(function() {
		var thisObjet = $(this);
		if ($(this).hasClass('suivi')) {
			$.post('php/post_desabonner.php',
				{ id: $(this).attr('data-id') },
				function(reponse) {
					if (reponse.etat == true) {
						thisObjet.removeClass('suivi');
						thisObjet.text('Suivre');
					}
					else
						alert("Une erreur s'est produite.");
				});
		}
		else {
			$.post('php/post_abonner.php',
				{ id: $(this).attr('data-id') },
				function(reponse) {
					if (reponse.etat == true) {
						thisObjet.addClass('suivi');
						thisObjet.text('Suivi');
					}
					else
						alert("Une erreur s'est produite.");
				});
		}
	});

});