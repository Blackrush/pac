<?php

include 'config.inc.php';
include 'fctaux.inc.php';
include 'db.inc.php';

session_start();
user_assert_authenticated();
user_assert_admin();

entete();
corps();
footer();

// gère les différentes actions de la page
function corps() {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_GET['edit'])) {
			do_edit();
		} else {
			do_add();
    }
	} else if (isset($_GET['del'])) {
		do_del();
	} else {
		do_show();
  }
}

// affiche la table
function do_show($errors = array()) {
	$especes = DB::getInstance()->select(build_query());
	affiche_corps($especes, $errors);
}

// ajoute une ligne dans la table
function do_add() {
	$idesp = htmlentities(pg_escape_string($_POST['idesp']));
	$nom   = htmlentities(pg_escape_string($_POST['nom']));
	$type  = htmlentities(pg_escape_string($_POST['type']));

	$result = DB::getInstance()
		->maj("insert into espece(idesp, nom, type) values('$idesp', '$nom', '$type')");

	if ($result) {
		header("Location: modifEspece.php");
	} else {
		do_show(array('L\'ajout a échoué!'));
  }
}

// modifie une ligne de la table
function do_edit() {
	$curidesp = pg_escape_string($_GET['edit']);

	$idesp = htmlentities(pg_escape_string($_POST['idesp']));
	$nom   = htmlentities(pg_escape_string($_POST['nom']));
	$type  = htmlentities(pg_escape_string($_POST['type']));

	$result = DB::getInstance()
		->maj("update espece set idesp='$idesp', nom='$nom', type='$type' where idesp='$curidesp'");

	if ($result) {
		header("Location: modifEspece.php?id=$idesp");
	} else {
		do_show(array('La modification a échoué!'));
  }
}

// supprime une ligne de la table
function do_del() {
	$idesp = pg_escape_string($_GET['del']);

	$result = DB::getInstance()->maj("delete from espece where idesp='$idesp'");

	if ($result) {
		header("Location: modifEspece.php");
	} else {
		do_show(array('La suppression a echoué!'));
  }
}

// génère la requête pour récupérer la table
function build_query() {
	$result = 'select idesp, nom, type from espece';

	if (isset($_GET['tri'])) {
		switch ($_GET['tri']) {
		case "1":
			$result .= ' order by idesp';
			break;
		case "2":
			$result .= ' order by nom';
			break;
		case "3":
			$result .= ' order by type';
			break;
		}
		if (isset($_GET['desc'])) {
			$result .= ' desc';
    }
	}

	return $result;
}

// génère un lien pour trier la colonne
function sorted_column_path($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "modifEspece.php?tri=$id";
	} else {
		return "modifEspece.php?tri=$id&desc=true";
  }
}

// affiche la table
function affiche_corps($especes, $errors) {
	$curid = isset($_GET['id']) ? $_GET['id'] : '';
	$cureditid = isset($_GET['edit']) ? $_GET['edit'] : '';
	echo '<p>Consultation de la table <strong>espece</strong></p>';

	foreach ($errors as $error) {
		echo "<p class=\"error\">$error</p>";
	}
	if (count($errors) > 0) {
		affiche_conseils_erreurs();
	}

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th><a href="'. sorted_column_path(1) .'">idesp</a></th>';
	echo '			<th><a href="'. sorted_column_path(2) .'">nom</a></th>';
	echo '			<th><a href="'. sorted_column_path(3) .'">type</a></th>';
	echo '			<th></th>';
	echo '			<th></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';
	foreach ($especes as $espece) {
		if ($espece->idesp == $cureditid) {
			affiche_formulaire_modification($espece);
		} else {
			if ($espece->idesp == $curid) {
				echo '<tr class="current">';
			} else {
				echo '<tr>';
	    }
			echo "<td><a href=\"modifEspece.php?id={$espece->idesp}\">{$espece->idesp}</a></td>";
			echo "<td>{$espece->nom}</td>";
			echo "<td>{$espece->type}</td>";
			echo "<td><a href=\"modifEspece.php?edit={$espece->idesp}\">modifier</a></td>";
			echo "<td><a href=\"modifEspece.php?del={$espece->idesp}\">supprimer</a></td>";
			echo '</tr>';
		}
	}
	if (!isset($_GET['edit'])) {
		affiche_formulaire_creation();
  }

	echo '  </tbody>';
	echo '</table>';
}

function affiche_formulaire_modification($espece) {
	echo '<tr>';
	echo '<form action="modifEspece.php?edit='.$espece->idesp.'" method="post">';
	echo '<td><input type="text" name="idesp" value="'.$espece->idesp.'"</td>';
	echo '<td><input type="text" name="nom" value="'.$espece->nom.'"</td>';
	echo '<td><input type="text" name="type" value="'.$espece->type.'"</td>';
	echo '<td><input type="submit" value="modifier" /></td>';
	echo "<td><a href=\"modifEspece.php?del={$espece->idesp}\">supprimer</a></td>";
	echo '</form>';
	echo '</tr>';
}

function affiche_formulaire_creation() {
	echo '<tr>';
	echo '<form action="modifEspece.php" method="post">';
	echo '<td><input type="text" name="idesp" placeholder="idesp" /></td>';
	echo '<td><input type="text" name="nom" placeholder="nom" /></td>';
	echo '<td><input type="text" name="type" placeholder="type" /></td>';
	echo '<td><input type="submit" value="ajouter" /></td>';
	echo '<td></td>';
	echo '</form>';
	echo '</tr>';
}

function affiche_conseils_erreurs() {
?>
<div class="error">
	<p>Vérifiez que :</p>
	<ul>
		
	</ul>
</div>
<?
}
