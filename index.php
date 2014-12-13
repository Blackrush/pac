<?php
include 'config.inc.php';
include 'fctaux.inc.php';
include 'db.inc.php';

// démarre la session
session_start();
user_bootstrap_fake();

// affiche les différentes parties du website
entete();
corps();
footer();

// les fonctions affichant et gérant le website
function corps() {
	$user = user_from_session_or_null();
	if ($user != null) {
		$name = $user->username;

		header("Location: accueil_$name.php");
		echo "Vous allez etre redirige $name!";
	} else {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			list($name, $password) = scan_formulaire();

			$errors = array();
			if (empty($name)) {
				$errors['name'] = 'ne doit pas etre vide';
			}
			if (empty($password)) {
				$errors['password'] = 'ne doit pas etre vide';
			}
			if (count($errors) <= 0 && !user_authenticate($name, $password)) {
				$errors['form'] = 'Votre nom d\'utilisateur ou mot de passe est erroné.';
			}

			if (count($errors) > 0) {
				affiche_formulaire($name, $errors);
			} else {
				header("Location: accueil_$name.php");
				echo "Vous allez etre redirige $name!";
      }
		} else {
			affiche_formulaire('', array());
		}
  }
}

// affiche le formulaires avec des erreurs s'il y en a
function affiche_formulaire($user, $errors) {
?>
<form action="index.php" method="post">
	<?php if (isset($errors['form'])): ?>
		<p class="error"><?= $errors['form'] ?></p>
	<?php endif; ?>

	<div class="champ">
		<label for="name">Nom d'utilisateur</label>
		<input type="text" name="name" id="name" value="<?= $user ?>" />
		<?php if (isset($errors['name'])): ?>
			<p class="error"><?= $errors['name'] ?></p>
		<?php endif; ?>
	</div>

	<div class="champ">
		<label for="password">Mot de passe</label>
		<input type="password" name="password" id="password" />
		<?php if (isset($errors['password'])): ?>
			<p class="error"><?= $errors['password'] ?></p>
		<?php endif; ?>
	</div>

	<div class="action">
		<input type="submit" />
		<input type="reset" />
	</div>
</form>
<?php
}

// detecte les valeurs du formulaire dans la variable $_POST
function scan_formulaire() {
	$result = array();

	if (isset($_POST['name'])) {
		array_push($result, $_POST['name']);
	} else {
		array_push($result, '');
	}

	if (isset($_POST['password'])) {
		array_push($result, $_POST['password']);
	} else {
		array_push($result, '');
  }

	return $result;
}
