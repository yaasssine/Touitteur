<?php
include_once "Touite.class.php";

class TouiteReponse extends Touite {
	
	private $idSource;
	
	public function __construct($id) {
		
		try {
			parent::__construct($id);
			
			global $bdd;
			
			$req = $bdd->prepare('SELECT idMsgSource FROM TouitesReponses WHERE idMsgSource=:id');
			$req->bindValue(':id', $id);
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			$this->idSource = $rep['idMsgSource'];
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	public function getIdSource() {
		return $this->idSource;
	}
	
	public function setIdSource($idSource) {
		$this->idSource = $idSource;
	}
	
	/*
		Supprime cette réponse de touite.	
	*/
	public function supprimerTouite() {
		global $utilisateurConnecte;
		
		if ($utilisateurConnecte->estUtilisateur($this->getAuteur()->getId())) {
			try {
				global $bdd;
				
				$req = $bdd->prepare('DELETE FROM TouitesReponses WHERE idMsgRep=:id');
				$req->bindValue(':id', $this->getId());
				$req->execute();
				
				$req = $bdd->prepare('DELETE FROM TouitesPublics WHERE idMsg=:id');
				$req->bindValue(':id', $this->getId());
				$req->execute();
				
				$req = $bdd->prepare('DELETE FROM Touites WHERE idMsg=:id');
				$req->bindValue(':id', $this->getId());
				$req->execute();
				
				return true;
			}
			catch (Exception $e) {
				return false;
			}
		}
		else
			return false;
	}
	
	/*
		Renvoie cette réponse de touite.	
	*/
	public function __toString() {
		global $bdd;
		global $utilisateurConnecte;
		
		$touite = '<div data-id-reponse="' . $this->getId() . '" class="touite reponse">';
		
		$touite .= '<a href="profil.php?p=' . $this->getAuteur()->getPseudo() . '" title="Voir le profil de ' . $this->getAuteur()->getPseudo() . '">';
		if ($this->getAuteur()->getPhoto())
			$touite .= '<img class="photo" src="img/' . $this->getAuteur()->getPseudo() . '.jpg" alt="' . $this->getAuteur()->getPseudo() . '" />';
		else
			$touite .= '<img class="photo" src="img/defaut.jpg" alt="' . $this->getAuteur()->getPseudo() . '" />';
		$touite .= $this->getAuteur()->getPseudo();
		$touite .= '</a>';
		
		$touite .= '<aside>';
		
		$jour = intval($this->getDate()->format('d'));
		$mois = intval($this->getDate()->format('m'));
		$annee = intval($this->getDate()->format('Y'));
		$touite .= '<span class="date" title="' . $jour . ' ' . convertirMois($mois) . ' ' . $annee . ' à ' . $this->getDate()->format('H:i') . '">';
		$dateActuelle = new DateTime();
		if ($jour == intval($dateActuelle->format('d'))) 
			$touite .= $this->getDate()->format('H:i');
		else {
			if ($annee == intval($dateActuelle->format('Y')))
				$annee = '';
			$touite .= $jour . ' ' . convertirMoisCourt($mois) . ' ' . $annee;
		}
		$touite .= '</span>';
		
		$touite .= '<ul class="action">';
		$touite .= '<li class="repondre" title="Répondre à ' . $this->getAuteur()->getPseudo() . '"><i class="fa fa-reply"></i></li>';
		if ($this->getAuteur()->estUtilisateur($utilisateurConnecte->getId()))
			$touite .= '<li class="supprimer" title="Supprimer ce touite"><i class="fa fa-trash"></i></li>';
		$touite .= '</ul>';
		
		$touite .= '</aside>';
		
		$touite .= '<p>' . $this->getTexte() . '</p>';
		
		$touite .= '</div>';
		
		return $touite;
	}
	
}