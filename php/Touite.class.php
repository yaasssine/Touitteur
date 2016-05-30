<?php
include_once 'Utilisateur.class.php';
include_once 'TouiteReponse.class.php';
	
class Touite {
	
	protected $id;
	protected $date;
	protected $texte;
	protected $auteur;
	
	public function __construct($id) {
		
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT * FROM Touites NATURAL JOIN TouitesPublics WHERE idMsg=:id');
			$req->bindValue(':id', $id);
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			$this->id = $rep['idMsg'];
			$this->date = new DateTime($rep['dateT']);
			$this->texte = $rep['texte'];
			$this->auteur = new Utilisateur(intval($rep['idAuteur']));
		}
		catch (Exception $e)
		{
			die('<p class="erreur">' . $e->getMessage() . '</p>');
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getDate() {
		return $this->date;
	}
	
	public function setDate($date) {
		if (is_string($date)) {
			$date = new DateTime($date);
			$dateActuelle = new DateTime();
			if ($date <= $dateActuelle)
				$this->date = $date;
		}
	}
	
	public function getTexte() {
		return $this->texte;
	}
	
	public function setTexte($texte) {
		if (!empty($email) AND strlen($email) <= 140)
			$this->texte = $texte;
	}
	
	public function getAuteur() {
		return $this->auteur;
	}
	
	public function setAuteur($idAuteur) {
		$idAuteur = new Utilisateur($idAuteur);
	}
	
	
	/*
		Supprime ce touite.	
	*/
	public function supprimerTouite() {
		global $utilisateurConnecte;
		
		if ($utilisateurConnecte->estUtilisateur($this->getAuteur()->getId())) {
			try {
				global $bdd;
				
				$req = $bdd->prepare('DELETE FROM TouitesNormaux WHERE idMsg=:id');
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
		Renvoie le nombre de réponse à ce touite.	
	*/
	public function obtenirNbReponse() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idMsg) AS id FROM Touites NATURAL JOIN TouitesReponses WHERE idMsgSource=:id GROUP BY idMsg');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			if (empty($rep))
				return 0;
			else
				return intval($rep['id']);
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	/*
		Renvoie la liste des réponses (maximum 5 réponses) à ce touite, à partir d'un certain ID de touite (curseur).
	*/
	public function obtenirListeReponse($curseurMin) {
		try {
			global $bdd;
			
			$req = null;
			if ($curseurMin == 0) {
				$req = $bdd->prepare('SELECT idMsgRep FROM TouitesReponses WHERE idMsgSource=:id ORDER BY idMsgRep DESC');
				$req->bindValue(':id', $this->getId());
			}
			else {
				$req = $bdd->prepare('SELECT idMsgRep FROM TouitesReponses WHERE idMsgSource=:id AND idMsgRep<:curseur ORDER BY idMsgRep DESC');
				$req->bindValue(':id', $this->getId());
				$req->bindValue(':curseur', $curseurMin);
			}
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeReponse[] = new TouiteReponse(intval($rep['idMsgRep']));
			}
			
			if (empty($listeReponse))
				return false;
			else if (count($listeReponse) <= 5)
				return $listeReponse;
			else {
				$listeReponse = array_slice($listeReponse, 0, 5);
				$listeIdTouite = null;
				foreach ($listeReponse as $touite)
					$listeIdTouite[] = $touite->getId();
				$listeReponse[] = array('curseurSuivant' => min($listeIdTouite));
				return $listeReponse;
			}
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	/*
		Renvoie le touite.	
	*/
	public function __toString() {
		global $bdd;
		global $utilisateurConnecte;
		
		$toString = '<div data-id-touite="' . $this->getId() . '" class="touite">';
		
		$toString .= '<a href="profil.php?p=' . $this->getAuteur()->getPseudo() . '">';
		if ($this->getAuteur()->getPhoto())
			$toString .= '<img class="photo" src="img/' . $this->getAuteur()->getPseudo() . '.jpg" alt="' . $this->getAuteur()->getPseudo() . '" />';
		else
			$toString .= '<img class="photo" src="img/defaut.jpg" alt="' . $this->getAuteur()->getPseudo() . '" />';
		$toString .= $this->getAuteur()->getPseudo();
		$toString .= '</a>';
		
		$toString .= '<aside>';
		
		$jour = intval($this->getDate()->format('d'));
		$mois = intval($this->getDate()->format('m'));
		$annee = intval($this->getDate()->format('Y'));
		$toString .= '<span class="date" title="' . $jour . ' ' . convertirMois($mois) . ' ' . $annee . ' à ' . $this->getDate()->format('H:i') . '">';
		$dateActuelle = new DateTime();
		if ($jour == intval($dateActuelle->format('d'))) 
			$toString .= $this->getDate()->format('H:i');
		else {
			if ($annee == intval($dateActuelle->format('Y')))
				$annee = '';
			$toString .= $jour . ' ' . convertirMoisCourt($mois) . ' ' . $annee;
		}
		$toString .= '</span>';
		
		$toString .= '<ul class="action">';
		$toString .= '<li class="repondre" title="Répondre à ' . $this->getAuteur()->getPseudo() . '"><i class="fa fa-reply"></i></li>';
		if ($this->getAuteur()->estUtilisateur($utilisateurConnecte->getId()))
			$toString .= '<li class="supprimer" title="Supprimer ce touite"><i class="fa fa-trash"></i></li>';
		$toString .= '</ul>';
		
		$toString .= '</aside>';
		
		$toString .= '<p>' . $this->getTexte() . '</p>';
						
		$toString .= '<div class="liste-reponse"></div>';
						
		//espace pour répondre (masqué par défaut)
		$toString .= '<div class="touite repondre" style="display: none;">';
		if ($utilisateurConnecte->getPhoto())
			$toString .= '<img class="photo" src="img/' . $utilisateurConnecte->getPseudo() . '.jpg" alt="' . $utilisateurConnecte->getPseudo() . '" />';
		else
			$toString .= '<img class="photo" src="img/defaut.jpg" alt="' . $utilisateurConnecte->getPseudo() . '" />';
		$toString .= '<textarea placeholder="Répondre à quelqu\'un (en le mentionnant avec @)" maxlength="500">@' . $this->getAuteur()->getPseudo() . ' </textarea>';
		$toString .= '<aside><button class="publier-touite">Publier</button>';
		$toString .= '<span class="compteur">140</span></aside>';
		$toString .= '</div>';
		
		$nbReponse = $this->obtenirNbReponse();
		if ($nbReponse == 0)
			$toString .= '<div class="afficher-masquer-reponse afficher"><i class="fa fa-commenting"></i> Aucune réponse</div>';
		else if ($nbReponse == 1)
			$toString .= '<div class="afficher-masquer-reponse afficher"><i class="fa fa-commenting"></i> 1 réponse</div>';
		else
			$toString .= '<div class="afficher-masquer-reponse afficher"><i class="fa fa-commenting"></i> ' . $nbReponse . ' réponses</div>';
		
		$toString .= '</div>';
		
		return $toString;
	}
}