<div class="fond-fenetre"></div>

<section id="message" class="fenetre">
	<h1>Messages privés</h1>
	<?php
	$listeConversation = $utilisateurConnecte->obtenirListeMsgPrive();
	
	if (!$listeConversation)
		echo '<p>Vous n\'avez reçu aucun message privé.</p>';
	else {
		echo '<ul class="liste-msg">';
		foreach ($listeConversation as $conversation) {
			echo '<li>';
			echo $conversation;
			echo '</li>';
		}
	}
	?>
</section>