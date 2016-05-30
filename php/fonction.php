<?php
/*
	Vérifie le format d'un pseudo
*/
function verifierPseudo($pseudo) {
	if (empty($pseudo))
		throw new Exception("Le pseudo ne peut pas être vide.");
	else if (!preg_match('/^[A-Za-z0-9_]+$/', $pseudo))
		throw new Exception("Le pseudo n'est pas valide");
	else if (strlen($pseudo) <= 2)
		throw new Exception("Le pseudo est trop court.");
	else if (strlen($pseudo) > 20)
		throw new Exception("Le pseudo est trop long.");
	else
		return true;
}

/*
	Vérifie le format d'un email
*/
function verifierEmail($email) {
	if (empty($email))
		throw new Exception("L'adresse email ne peut pas être vide.");
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		throw new Exception("L'adresse email n'est pas valide.");
	else if (strlen($email) > 100)
		throw new Exception("L'adresse email est trop longue.");
	else
		return true;
}

/*
	Vérifie le format d'un nom
*/
function verifierNom($nom) {
	if (empty($nom))
		throw new Exception("Le nom ne peut pas être vide.");
	else if (strlen($nom) <= 2)
		throw new Exception("Le nom est trop court.");
	else if (strlen($nom) > 40)
		throw new Exception("Le nom est trop long.");
	else
		return true;
}

/*
	Vérifie le format d'un mot de passe
*/
function verifierMdp($mdp) {
	if (empty($mdp))
		throw new Exception("Le mot de passe ne peut pas être vide.");
	else if (strlen($mdp) <= 6)
		throw new Exception("Le mot de passe est trop court.");
	else if (strlen($mdp) > 100)
		throw new Exception("Le mot de passe est trop long.");
	else
		return true;
}

/*
	Vérifie le format d'un statut
*/
function verifierStatut($statut) {
	if (strlen($statut) > 160)
		throw new Exception("Le statut est trop long.");
	else
		return true;
}

/*
	Vérifie un mot de passe et sa confirmation
*/
function verifierConfirmationMdp($mdp, $confirmationMdp) {
	if ($mdp === $confirmationMdp)
		return true;
	else
		throw new Exception("Le mot de passe et sa confirmation ne sont pas identiques.");
}

/*
	Vérifie si un identifiant et un mot de passe sont identiques à d'autres
*/
function verifierIdentifiantConnexion($identifiant, $pseudoBDD, $emailBDD, $mdp, $mdpBDD) {
	if (empty($identifiant) OR empty($mdp))
		throw new Exception("Votre email ou pseudo ainsi que votre mot de passe sont nécessaires pour vous connecter.");
	else if (($identifiant !== $pseudoBDD AND $identifiant !== $emailBDD) OR sha1($mdp) !== $mdpBDD)
		throw new Exception("L'identifiant et/ou le mot de passe est incorrect.");
	else
		return true;
}

/*
	Vérifie le format du texte d'un touite
*/
function verifierTouite($texte) {
	if (empty($texte))
		throw new Exception("Le touite ne peut pas être vide.");
	else if (strlen($texte) > 140)
		throw new Exception("Le touite est trop long.");
	else
		return true;
}

	
	
/*
	Convertie un mois (ex : 01) en version lettre abrégée (jan.)
*/
function convertirMoisCourt($mois) {
	$mois = intval($mois);
	if ($mois == 1)
		return "jan.";
	else if ($mois == 2)
		return "fév.";
	else if ($mois == 3)
		return "mars";
	else if ($mois == 4)
		return "avr.";
	else if ($mois == 5)
		return "mai";
	else if ($mois == 6)
		return "juin";
	else if ($mois == 7)
		return "jui.";
	else if ($mois == 8)
		return "août";
	else if ($mois == 9)
		return "sep.";
	else if ($mois == 10)
		return "oct.";
	else if ($mois == 11)
		return "nov.";
	else if ($mois == 12)
		return "déc.";
}

/*
	Convertie un mois (ex : 01) en version lettre (janvier)
*/
function convertirMois($mois) {
	$mois = intval($mois);
	if ($mois == 1)
		return "janvier";
	else if ($mois == 2)
		return "février";
	else if ($mois == 3)
		return "mars";
	else if ($mois == 4)
		return "avril";
	else if ($mois == 5)
		return "mai";
	else if ($mois == 6)
		return "juin";
	else if ($mois == 7)
		return "juillet";
	else if ($mois == 8)
		return "août";
	else if ($mois == 9)
		return "septembre";
	else if ($mois == 10)
		return "octobre";
	else if ($mois == 11)
		return "novembre";
	else if ($mois == 12)
		return "décembre";
}

?>