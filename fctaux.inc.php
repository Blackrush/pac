<?php

require 'user.class.php';

// affiche l'entete de la page
function entete() {
	global $USER;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title></title>
		<link rel="stylesheet" type="text/css" href="site.css">
	</head>

	<body>
		<div class="haut">
			<div class="hautGauche">
			  <img src="imagesWA3.png" alt="logo webapp">
			</div>

			<div class="hautCentre">
				P-A-C
			</div>
		</div>

		<div class="milieu">
			<div class="menu">
				<?php if ($USER->isGuest()): ?>
				<ul>
					<li><a href="index.php">Se connecter</a></li>
				</ul>
				<?php endif; ?>

				<?php if ($USER->isUser()): ?>
				Votre compte
				<ul>
					<li><a href="logout.php">Se déconnecter</a></li>
				</ul>

				Consultation
				<ul>
					<li><a href="consultDescriptif.php">Descriptif</a>
					<li><a href="consultEspece.php">Espece</a>
					<li><a href="consultTroupeau.php">Troupeau</a>
				</ul>
				<?php endif; ?>

				<?php if ($USER->isAdmin()): ?>
				Modification
				<ul>
					<li><a href="modifDescriptif.php">Descriptif</a></li>
					<li><a href="modifEspece.php">Espece</a></li>
					<li><a href="modifTroupeau.php">Troupeau</a></li>
				</ul>
				<?php endif; ?>
			</div>

			<div class="contenu">
<?php
}

// affiche le pied de page
function footer() {
?>
			</div>
		</div>
		<script src="application.js"></script>	
	</body>
</html>
<?php
}

// authentifie l'utilisateur et retourne "true" si la fonction a réussit
function user_authenticate($username, $password) {
	if ($username == 'user' && $password == 'userpwd') {
		$_SESSION['username'] = $username;
		return true;
	} else if ($username == 'admin' && $password == 'adminpwd') {
		$_SESSION['username'] = $username;
		return true;
	}

	return false;
}

// essaie de récupérer l'utilisateur depuis la session sinon retourne "null"
function user_from_session_or_null() {
	if (isset($_SESSION['username'])) {
		$user = new User($_SESSION['username']);
		return $user;
	}

	return null;
}

// manipule la variable globale $USER
function user_assert_authenticated() {
	global $USER;

	// verifie qu'on a pas déjà auth l'user
	if ($USER != null) {
		return;
	}

	// redirige l'user sinon
	$USER = user_from_session_or_null();
	if ($USER == null) {
		header("Location: index.php");
		exit(1);
  }
}

// force que l'admin se connecte
function user_assert_admin() {
	global $USER;

	if ($USER == null || !$USER->isAdmin()) {
		header("Location: index.php");
		exit(1);
  }
}

// initialise un faux utilisateur representant un utilisateur non identifié
function user_bootstrap_fake() {
	global $USER;
	$USER = new User('');
}
