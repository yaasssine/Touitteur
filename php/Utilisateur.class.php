<?php
include_once "Touite.class.php";
include_once "TouiteReponse.class.php";
include_once "ConversationPrivee.class.php";
	
class Utilisateur {
	
	private $id;
	private $pseudo;
	private $email;
	private $nom;
	private $mdp;
	private $photo;
	private $statut;
	
	public function __construct($id) {
		
		if ($id === null) { //crée un "utilisateur NULL" pour l'inscription d'un utilisateur
			$this->id = 0;
			$this->pseudo = '';
			$this->email = '';
			$this->nom = '';
			$this->mdp = '';
			$this->photo = false;
			$this->statut = '';
		}
		
		else { //reconstitue un utilisateur présent dans la BDD, à partir de son ID ou de son PSEUDO
			try {
				global $bdd;
				
				if (is_string($id)) { //si c'est un pseudo
					$req = $bdd->prepare('SELECT * FROM Touitos WHERE pseudonyme=:pseudo');
					$req->bindValue(':pseudo', $id);
				}
				else if (is_int($id)) { //si c'est un ID
					$req = $bdd->prepare('SELECT * FROM Touitos WHERE id=:id');
					$req->bindValue(':id', $id);
				}
				$req->execute();
				$rep = $req->fetch(PDO::FETCH_ASSOC);
				
				$this->id = $rep['id'];
				$this->pseudo = $rep['pseudonyme'];
				$this->email = $rep['email'];
				$this->nom = $rep['nom'];
				$this->mdp = $rep['motPasse'];
				if ($rep['photo'] === "O")
					$this->photo = true;
				else
					$this->photo = false;
				$this->statut = $rep['statut'];
			}
			catch (Exception $e)
			{
				die('<p>' . $e->getMessage() . '</p>');
			}
		}
	}
	
	public function getId() {
		return intval($this->id);
	}
	
	public function getPseudo() {
		return htmlentities($this->pseudo);
	}
	
	public function setPseudo($pseudo) {
		try {
			if (verifierPseudo($pseudo)) {
				global $bdd;
				
				$req = $bdd->prepare('UPDATE Touitos SET pseudonyme=:pseudo WHERE id=:id');
				$req->bindValue(':pseudo', htmlentities(strtolower($pseudo)));
				$req->bindValue(':id', $this->getId());
				$req->execute();
				$this->pseudo = htmlentities(strtolower($pseudo));
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	public function getEmail() {
		return htmlentities($this->email);
	}
	
	public function setEmail($email) {
		try {
			if (verifierEmail($email)) {
				global $bdd;
				
				$req = $bdd->prepare('UPDATE Touitos SET email=:email WHERE id=:id');
				$req->bindValue(':email', htmlentities($email));
				$req->bindValue(':id', $this->getId());
				$req->execute();
				$this->email = htmlentities($email);
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	public function getNom() {
		return htmlentities($this->nom);
	}
	
	public function setNom($nom) {
		try {
			if (verifierNom($nom)) {
				global $bdd;
				
				$req = $bdd->prepare('UPDATE Touitos SET nom=:nom WHERE id=:id');
				$req->bindValue(':nom', htmlentities($nom));
				$req->bindValue(':id', $this->getId());
				$req->execute();
				$this->nom = htmlentities($nom);
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	public function getMdp() {
		return htmlentities($this->mdp);
	}
	
	public function setMdp($mdp, $nouveauMdp, $confirmationMdp) {
		try {
			if (verifierMdp($nouveauMdp) AND verifierConfirmationMdp($nouveauMdp, $confirmationMdp)) {
				if (sha1(htmlentities($mdp)) !== $this->getMdp())
					throw new Exception("L'ancien mot de passe est incorrect.");
				
				global $bdd;
				
				$req = $bdd->prepare('UPDATE Touitos SET motPasse=:mdp WHERE id=:id');
				$req->bindValue(':mdp', sha1(htmlentities($nouveauMdp)));
				$req->bindValue(':id', $this->getId());
				$req->execute();
				$this->mdp = sha1(htmlentities($nouveauMdp));
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	public function getPhoto() {
		return $this->photo; //TRUE ou FALSE uniquement
	}
	
	public function setPhoto($photo) { //TRUE ou FALSE uniquement
		try {
			if ($photo === true OR $photo === false) {
				$this->photo = $photo;
				
				if ($photo === true)
					$photo = 'O';
				else
					$photo = 'N';
				
				global $bdd;
					
				$req = $bdd->prepare('UPDATE Touitos SET photo=:photo WHERE id=:id');
				$req->bindValue(':photo', $photo);
				$req->bindValue(':id', $this->getId());
				$req->execute();
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	public function getStatut() {
		return htmlentities($this->statut);
	}
	
	public function setStatut($statut) {
		try {
			if (verifierStatut($statut)) {
				global $bdd;
				
				$req = $bdd->prepare('UPDATE Touitos SET statut=:statut WHERE id=:id');
				$req->bindValue(':statut', htmlentities($statut));
				$req->bindValue(':id', $this->getId());
				$req->execute();
				$this->statut = htmlentities($statut);
			}
		} catch (Exception $e) {
			echo '<p class="erreur">' . $e->getMessage() . '</p>';
		}
	}
	
	
	/*
		Compare deux utilisateurs et renvoie TRUE si ils sont identiques, FALSE sinon.	
	*/
	public function estUtilisateur($idUtilisateur) {
		if ($this->getId() === $idUtilisateur)
			return true;
		else
			return false;
	}
	
	
	/*
		Renvoie la liste des touites (normaux) de l'utilisateur.	
	*/
	public function obtenirListeTouite() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT idMsg FROM TouitesPublics NATURAL JOIN TouitesNormaux WHERE idAuteur=:id ORDER BY idMsg DESC');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeTouite[] = new Touite(intval($rep['idMsg']));
			}
			
			if (empty($listeTouite))
				return false;
			
			return $listeTouite;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie la liste des abonnements de l'utilisateur.	
	*/
	public function obtenirListeAbonnement() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT idReceveur FROM Suivre WHERE idDemandeur=:id');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeAbonnement[] = new Utilisateur(intval($rep['idReceveur']));
			}
			
			if (empty($listeAbonnement))
				return false;
			
			return $listeAbonnement;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie la liste des abonnés de l'utilisateur.	
	*/
	public function obtenirListeAbonne() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT idDemandeur FROM Suivre WHERE idReceveur=:id');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeAbonne[] = new Utilisateur(intval($rep['idDemandeur']));
			}
			
			if (empty($listeAbonne))
				return false;
			
			return $listeAbonne;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie le nombre de touites (normaux + réponses) de l'utilisateur.	
	*/
	public function obtenirNbTouite() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idMsg) AS nbMsg FROM Touites NATURAL JOIN TouitesPublics WHERE idAuteur=:id GROUP BY idAuteur');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			if (empty($rep))
				return 0;
			else
				return intval($rep['nbMsg']);
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie le nombre d'abonnements de l'utilisateur.	
	*/
	public function obtenirNbAbonnement() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idReceveur) AS id FROM Suivre WHERE idDemandeur=:id GROUP BY idDemandeur');
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
		Renvoie le nombre d'abonnés de l'utilisateur.	
	*/
	public function obtenirNbAbonne() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idDemandeur) AS id FROM Suivre WHERE idReceveur=:id GROUP BY idReceveur');
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
		Renvoie TRUE si l'utilisateur suit un autre, FALSE sinon.	
	*/
	public function suit($idUtilisateur) {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT * FROM Suivre WHERE idReceveur=:idR AND idDemandeur=:idD');
			$req->bindValue(':idR', $idUtilisateur);
			$req->bindValue(':idD', $this->getId());
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			if (empty($rep))
				return false;
			else
				return true;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie TRUE si l'utilisateur est suivi par un autre, FALSE sinon.
	*/
	public function estSuiviPar($idUtilisateur) {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT * FROM Suivre WHERE idReceveur=:idR AND idDemandeur=:idD');
			$req->bindValue(':idR', $this->getId());
			$req->bindValue(':idD', $idUtilisateur);
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			if (empty($rep))
				return false;
			else
				return true;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie les touites les plus récents des utilisateurs suivis.
	*/
	public function obtenirTimeline($curseurMin) {
		try {
			global $bdd;
			
			$req = null;
			if ($curseurMin == 0) {
				$req = $bdd->prepare('SELECT * FROM (SELECT idMsg FROM Suivre a JOIN TouitesPublics b ON a.idReceveur=b.idAuteur WHERE a.idDemandeur=:idD UNION SELECT idMsg FROM (SELECT * FROM TouitesPublics NATURAL JOIN TouitesNormaux) x WHERE idAuteur=:idA) AS a ORDER BY idMsg DESC');
				$req->bindValue(':idD', $this->getId());
				$req->bindValue(':idA', $this->getId());
			}
			else {
				$req = $bdd->prepare('SELECT * FROM (SELECT idMsg FROM Suivre a JOIN TouitesPublics b ON a.idReceveur=b.idAuteur WHERE a.idDemandeur=:idD UNION SELECT idMsg FROM (SELECT * FROM TouitesPublics NATURAL JOIN TouitesNormaux) x WHERE idAuteur=:idA) AS a WHERE idMsg<:curseur ORDER BY idMsg DESC');
				$req->bindValue(':idD', $this->getId());
				$req->bindValue(':idA', $this->getId());
				$req->bindValue(':curseur', $curseurMin);
			}
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeTimeline[] = new Touite(intval($rep['idMsg']));
			}
			
			if (empty($listeTimeline))
				return false;
			else if (count($listeTimeline) <= 10)
				return $listeTimeline;
			else {
				$listeTimeline = array_slice($listeTimeline, 0, 10);
				$listeIdTouite = null;
				foreach ($listeTimeline as $touite)
					$listeIdTouite[] = $touite->getId();
				$listeTimeline[] = min($listeIdTouite);
				return $listeTimeline;
			}
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie la liste des utilisateurs ayant envoyé un ou plusieurs messages privés à cet utilisateur.	
	*/
	public function obtenirListeMsgPrive() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT idReceveur FROM Suivre WHERE idDemandeur=:id');
			$req->bindValue(':id', $this->getId());
			$req->execute();
			
			while ($rep = $req->fetch(PDO::FETCH_ASSOC)) {
				$listeConversation[] = new ConversationPrivee(intval($rep['idReceveur']));
			}
			
			if (empty($listeConversation))
				return false;
			
			return $listeConversation;
		}
		catch (Exception $e)
		{
			die('<p>[ERREUR ' . $e->getCode() . '] : ' . $e->getMessage() . '</p>');
		}
	}
	
	
	/*
		Renvoie le nombre de messages privés non lus qu'a reçu cet utilisateur.
	*/
	public function obtenirNbMsgPriveNonLu() {
		try {
			global $bdd;
			
			$req = $bdd->prepare('SELECT count(idMsg) AS nbMsgNonLu FROM Touites NATURAL JOIN TouitesPrives WHERE idReceveur=:id AND vu=\'N\' GROUP BY idReceveur');
			$req->bindValue(':id', $this->getId());
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
		Renvoie la "carte d'identité" de l'utilisateur.
	*/
	public function __toString() {
		global $bdd;
		global $utilisateurConnecte;
		
		$toString = '<div id="' . $this->getId() . '" class="profil">';
		if ($this->estUtilisateur($utilisateurConnecte->getId()))
			$toString .= '';
		else if (!$this->estSuiviPar($utilisateurConnecte->getId()))
			$toString .= '<button data-id="' . $this->getId() . '" class="suivre">Suivre</button>';
		else
			$toString .= '<button data-id="' . $this->getId() . '" class="suivre suivi">Suivi</button>';
		$toString .= '<a href="profil.php?p=' . $this->getPseudo() . '">';
		if ($this->getPhoto())
			$toString .= '<img class="photo" src="img/' . $this->getPseudo() . '.jpg" alt="' . $this->getPseudo() . '" />';
		else
			$toString .= '<img class="photo" src="img/defaut.jpg" alt="' . $this->getPseudo() . '" />';
		$toString .= $this->getPseudo() . '</a><span class="nom">' . $this->getNom();
		if ($this->suit($utilisateurConnecte->getId()))
			$toString .= ' <em>vous suit</em>';
		$toString .= '</span></div>';
		
		return $toString;
	}
	
}