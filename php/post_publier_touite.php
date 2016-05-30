<?php
require_once "connexion.php";
require_once "fonction.php";
require_once "Utilisateur.class.php";
require_once "Touite.class.php";
require_once "TouiteReponse.class.php";

session_start();

if (!empty($_SESSION['id']) AND !empty($_POST['texte_touite'])) {
	$utilisateurConnecte = new Utilisateur(intval($_SESSION['id']));
	
	$texteTouite = htmlentities($_POST['texte_touite']);
	$idTouiteSource = intval($_POST['id_touite_source']);
	
	try {
		if (verifierTouite($texteTouite)) {
			$dateActuelle = new DateTime();
			$req = $bdd->prepare('INSERT INTO Touites VALUES(null, :date, :texte)');
			$req->bindValue(':date', $dateActuelle->format('Y-m-d H:i:s'));
			$req->bindValue(':texte', $texteTouite);
			$req->execute();
			
			$req = $bdd->prepare('SELECT idMsg FROM Touites WHERE dateT=:date AND texte=:texte');
			$req->bindValue(':date', $dateActuelle->format('Y-m-d H:i:s'));
			$req->bindValue(':texte', $texteTouite);
			$req->execute();
			$rep = $req->fetch(PDO::FETCH_ASSOC);
			
			$idTouite = intval($rep['idMsg']);
			
			$req = $bdd->prepare('INSERT INTO TouitesPublics VALUES(:id, :idAuteur)');
			$req->bindValue(':id', $idTouite);
			$req->bindValue(':idAuteur', $_SESSION['id']);
			$req->execute();
			
			
			if (empty($idTouiteSource)) {
				$req = $bdd->prepare('INSERT INTO TouitesNormaux VALUES(:id)');
				$req->bindValue(':id', $idTouite);
				$req->execute();
				
				$touite = new Touite($idTouite);
				
				header('Content-Type: application/json');
				echo json_encode(array('afficherTouite' => html_entity_decode($touite->__toString())));
			}
			else {
				$req = $bdd->prepare('INSERT INTO TouitesReponses VALUES(:id, :idSource)');
				$req->bindValue(':id', $idTouite);
				$req->bindValue(':idSource', $idTouiteSource);
				$req->execute();
				
				$touite = new TouiteReponse($idTouite);
				
				header('Content-Type: application/json');
				echo json_encode(array('afficherTouite' => html_entity_decode($touite->__toString())));
			}
		}
	} catch (Exception $e) {
		header('Content-Type: application/json');
		echo json_encode(array('erreur' => true, 'erreurMsg' => $e->getMessage()));
	}
}
else {
	header('Content-Type: application/json');
	echo json_encode(array('erreur' => true, 'erreurMsg' => "Une erreur s'est produite. Le touite n'a pas été publié."));
}

?>