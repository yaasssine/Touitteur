<?php
include_once 'Utilisateur.class.php';
	
class ConversationPrivee {
	
	private $destinataire;
	private $vu;
	
	
	public function __construct($idDestinataire) {
		
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT * FROM Touitos WHERE id=:id');
			$req->bindValue(':id', $idDestinataire);
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			$this->destinataire = new Utilisateur(intval($rep['id']));
			$this->vu = false;
		}
		catch (Exception $e)
		{
			die('<p class="erreur">' . $e->getMessage() . '</p>');
		}
	}
	
	public function getDestinataire() {
		return $this->destinataire;
	}
	
	public function setDestinataire($idDestinataire) {
		$idDestinataire = new Utilisateur($idDestinataire);
	}
	
	public function getVu() {
		return $this->vu;
	}
	
	public function setVu($vu) {
		if ($vu === true OR $vu === false)
			$this->vu = $vu;
	}
	
	
	/*
		Envoi un message dans cette conversation à partir de l'utilisateur connecté.	
	*/
	public function envoyerMessage($texte) {
		global $utilisateurConnecte;

		try {
			if (verifierTouite($texte)) {
			
				global $bdd;
				
				$dateActuelle = new DateTime();
				$req = $bdd->prepare('INSERT INTO Touites VALUES(null, :date, :texte)');
				$req->bindValue(':date', $dateActuelle->format('Y-m-d H:i:s'));
				$req->bindValue(':texte', $texte);
				$req->execute();
				
				$req = $bdd->prepare('SELECT idMsg FROM Touites WHERE dateT=:date AND texte=:texte');
				$req->bindValue(':date', $dateActuelle->format('Y-m-d H:i:s'));
				$req->bindValue(':texte', $texte);
				$req->execute();
				$rep = $req->fetch(PDO::FETCH_ASSOC);
				
				$idMsg = intval($rep['idMsg']);
				
				$req = $bdd->prepare('INSERT INTO TouitesPrives VALUES(:idMsg, :idAuteur, :idReceveur, null, \'N\')');
				$req->bindValue(':idMsg', $idMsg);
				$req->bindValue(':idAuteur', $utilisateurConnecte->getId());
				$req->bindValue(':idReceveur', $this->getDestinataire()->getId());
				$req->execute();
				
				return true;
			}
		}
		catch (Exception $e) {
			return false;
		}
	}
	
	
	/*
		Met la conversation à l'état vu.	
	*/
	public function setVuConversation() {
		global $utilisateurConnecte;

		try {
			global $bdd;
			
			$req = $bdd->prepare('UPDATE TouitesPrives SET vu=\'O\' WHERE idReceveur=:id');
			$req->bindValue(':id', $utilisateurConnecte->getId());
			$req->execute();
			
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}
	
	
	/*
		Renvoie le nombre de messages privés non lus de cette conversation.
	*/
	public function obtenirNbMsgPriveNonLu() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idMsg) AS nbMsgNonLu FROM Touites NATURAL JOIN TouitesPrives WHERE idAuteur=:id AND vu=\'N\' GROUP BY idAuteur');
			$req->bindValue(':id', $this->getDestinataire()->getId());
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			if (empty($rep))
				return 0;
			else
				return intval($rep['nbMsgNonLu']);
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie les messages de la conversation.	
	*/
	public function obtenirMsgPriveConversation() {
		try {
			global $bdd;
			global $utilisateurConnecte;
			
			$req = $bdd->prepare('SELECT * FROM Touites NATURAL JOIN TouitesPrives WHERE (idReceveur=:id AND idAuteur=:idD) OR (idReceveur=:idD AND idAuteur=:id)');
			$req->bindValue(':id', $utilisateurConnecte->getId());
			$req->bindValue(':idD', $this->getDestinataire()->getId());
			$req->bindValue(':idD', $this->getDestinataire()->getId());
			$req->bindValue(':id', $utilisateurConnecte->getId());
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				if (intval($rep['idAuteur']) == $utilisateurConnecte->getId()) //si le message a pour auteur l'utilisateur connecté
					$listeMessage[] = '<li class="utilisateur"><p>' . $rep['texte'] . '</p></li>';
				else
					$listeMessage[] = '<li class="destinataire"><p>' . $rep['texte'] . '</p></li>';
			}
			
			if (empty($listeMessage))
				return false;
			
			return $listeMessage;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie le lien vers la conversation.	
	*/
	public function __toString() {
		global $bdd;
		
		$toString = '<div data-id-destinataire="' . $this->getDestinataire()->getId() . '" class="touite message">';
		
		if ($this->getDestinataire()->getPhoto())
			$toString .= '<img class="photo" src="img/' . $this->getDestinataire()->getPseudo() . '.jpg" alt="' . $this->getDestinataire()->getPseudo() . '" />';
		else
			$toString .= '<img class="photo" src="img/defaut.jpg" alt="' . $this->getDestinataire()->getPseudo() . '" />';
		$toString .= $this->getDestinataire()->getPseudo();
		
		$toString .= '<aside>';
		
		/*$toString .= '<ul class="action">';
		$toString .= '<li class="supprimer" title="Supprimer cette conversation"><i class="fa fa-trash"></i></li>';
		$toString .= '</ul>';*/
		
		$toString .= '</aside>';
		
		$nbMsgNonLu = $this->obtenirNbMsgPriveNonLu();
		if (!$this->obtenirMsgPriveConversation())
			$toString .= '<p>Créer une nouvelle conversation</p>';
		else if ($nbMsgNonLu == 0)
			$toString .= '<p>Aucun nouveau message</p>';
		else if ($nbMsgNonLu == 1)
			$toString .= '<p>1 nouveau message</p>';
		else
			$toString .= '<p>' . $nbMsgNonLu . ' nouveaux messages</p>';
		
		$toString .= '</div>';
		
		return $toString;
	}
	
}