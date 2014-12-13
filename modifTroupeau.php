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
function do_show() {
	$troupeaux = DB::getInstance()->select(build_query());
	affiche_corps($troupeaux);
}

// ajoute une ligne dans la table
function do_add() {
	$idtroup = htmlentities(pg_escape_string($_POST['idtroup']));
	$nom     = htmlentities(pg_escape_string($_POST['nom']));

	DB::getInstance()->maj("insert into troupeau(idtroup, nom) values('$idtroup', '$nom')");

	header("Location: modifTroupeau.php");
}

// modifie une ligne de la table
function do_edit() {
	$curidtroup = pg_escape_string($_GET['edit']);

	$idtroup = htmlentities(pg_escape_string($_POST['idtroup']));
	$nom     = htmlentities(pg_escape_string($_POST['nom']));

	DB::getInstance()->maj("update troupeau set idtroup='$idtroup', nom='$nom' where idtroup='$curidtroup'");

	header("Location: modifTroupeau.php?id=$idtroup");
}

// supprime une ligne de la table
function do_del() {
	$idtroup = pg_escape_string($_GET['del']);

	DB::getInstance()->maj("delete from troupeau where idtroup='$idtroup'");

	header("Location: modifTroupeau.php");
}

// génère la requête récupèrant la table
function build_query() {
	$result = 'select idtroup, nom from troupeau';

	if (isset($_GET['tri'])) {
		switch ($_GET['tri']) {
		case "1":
			$result .= ' order by idtroup';
			break;
		case "2":
			$result .= ' order by nom';
			break;
		}
		if (isset($_GET['desc'])) {
			$result .= ' desc';
    }
	}

	return $result;
}

// génère un lien pour trier une colonne
function sorted_column_path($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "modifTroupeau.php?tri=$id";
	} else {
		return "modifTroupeau.php?tri=$id&desc=true";
  }
}

// affiche la table
function affiche_corps($troupeaux) {
	$curid = isset($_GET['id']) ? $_GET['id'] : '';
	$cureditid = isset($_GET['edit']) ? $_GET['edit'] : '';
	echo '<p>Consultation de la table <strong>troupeau</strong></p>';

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th><a href="'. sorted_column_path(1) .'">idtroup</a></th>';
	echo '			<th><a href="'. sorted_column_path(2) .'">nom</a></th>';
	echo '			<th></th>';
	echo '			<th></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';
	foreach ($troupeaux as $troupeau) {
		if ($cureditid == $troupeau->idtroup) {
			affiche_formulaire_modification($troupeau);
		} else {
			if ($troupeau->idtroup == $curid) {
				echo '<tr class="current">';
			} else {
				echo '<tr>';
	    }

			echo "<td><a href=\"modifTroupeau.php?id={$troupeau->idtroup}\">{$troupeau->idtroup}</a></td>";
			echo "<td>{$troupeau->nom}</td>";
			echo "<td><a href=\"modifTroupeau.php?edit={$troupeau->idtroup}\">modifier</a></td>";
			echo "<td><a href=\"modifTroupeau.php?del={$troupeau->idtroup}\">supprimer</a></td>";
			echo '</tr>';
    }
	}
	if (!isset($_GET['edit'])) {
		affiche_formulaire_creation();
  }

	echo '  </tbody>';
	echo '</table>';
}

function affiche_formulaire_modification($troupeau) {
	echo "<tr>";
	echo "<form action=\"modifTroupeau.php?edit={$troupeau->idtroup}\" method=\"post\">";
	echo "<td><input type=\"text\" name=\"idtroup\" value=\"{$troupeau->idtroup}\" /></td>";
	echo "<td><input type=\"text\" name=\"nom\" value=\"{$troupeau->nom}\" /></td>";
	echo "<td><input type=\"submit\" value=\"modifier\" /></td>";
	echo "<td><a href=\"modifTroupeau.php?del={$troupeau->idtroup}\">supprimer</a></td>";
	echo '</form>';
	echo '</tr>';
}

function affiche_formulaire_creation() {
	echo '<tr>';
	echo '<form action="modifTroupeau.php" method="post">';
	echo '<td><input type="text" name="idtroup" placeholder="idtroup" /></td>';
	echo '<td><input type="text" name="nom" placeholder="nom" /></td>';
	echo '<td><input type="submit" value="ajouter" /></td>';
	echo '<td></td>';
	echo '</form>';
	echo '</tr>';
}
