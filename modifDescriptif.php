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

// retourne la clé primaire (pk=PrimaryKey) d'un descriptif
function pk($descriptif) {
	return $descriptif->idtroup .';'. $descriptif->idesp .';'. $descriptif->sexe;
}

// extrait une clé primaire jointe par des ";" sous forme de tableau
function pk_extract($pk) {
	return explode(";", $pk, 3);
}

// retourne vrai si le descriptif a pour clé primaire celle passée en paramètre
function is_pk($d, $pk) {
	return pk($d) == $pk;
}

// génère la requête pour récupérer la table
function build_select_query() {
	$result = 'select idtroup, idesp, sexe, nombre from descriptif';

	if (isset($_GET['tri'])) {
		switch ($_GET['tri']) {
		case "1":
			$result .= ' order by idtroup';
			break;
		case "2":
			$result .= ' order by idesp';
			break;
		case "3":
			$result .= ' order by sexe';
			break;
		case "4":
			$result .= ' order by nombre';
			break;
		}
		if (isset($_GET['desc'])) {
			$result .= ' desc';
    }
	}

	return $result;
}

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
	$descriptifs = DB::getInstance()->select(build_select_query());
	affiche_corps($descriptifs, $errors);
}

// ajoute une ligne dans la table
function do_add() {
	$idtroup = htmlentities(pg_escape_string($_POST['idtroup']));
	$idesp   = htmlentities(pg_escape_string($_POST['idesp']));
	$sexe    = htmlentities(pg_escape_string($_POST['sexe']));
	$nombre  = htmlentities(pg_escape_string($_POST['nombre']));

	$result =
		DB::getInstance()->maj("insert into descriptif(idtroup, idesp, sexe, nombre) "
			. "values('$idtroup', '$idesp', '$sexe', '$nombre')");

	if ($result) {
		header("Location: modifDescriptif.php");
	} else {
		do_show(array('L\'ajout a échoué!'));
  }
}

// modifie une ligne de la table
function do_edit() {
	list($curidtroup, $curidesp, $cursexe) = pk_extract(pg_escape_string($_GET['edit']));

	$idtroup = htmlentities(pg_escape_string($_POST['idtroup']));
	$idesp   = htmlentities(pg_escape_string($_POST['idesp']));
	$sexe    = htmlentities(pg_escape_string($_POST['sexe']));
	$nombre  = htmlentities(pg_escape_string($_POST['nombre']));

	$result = DB::getInstance()->maj("update descriptif set "
		. "idtroup='$idtroup', "
		. "idesp='$idesp', "
		. "sexe='$sexe', "
		. "nombre='$nombre' "
		. "where idtroup='$curidtroup' and idesp='$curidesp' and sexe='$cursexe'");

	if ($result) {
		header("Location: modifDescriptif.php?id=".implode(';', array($idtroup, $idesp, $sexe)));
	} else {
		do_show(array('La modification a échoué!'));
  }
}

// supprime une ligne de la table
function do_del() {
	list($curidtroup, $curidesp, $cursexe) = pk_extract(pg_escape_string($_GET['del']));

	$result = DB::getInstance()
		->maj("delete from descriptif where idtroup='$curidtroup' and idesp='$curidesp' and sexe='$cursexe'");

	if ($result) {
		header("Location: modifDescriptif.php");
	} else {
		do_show(array('La suppression a échoué!'));
  }
}

// génère le lien de tri d'une colonne selon le contexte
function sorted_column_path($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "modifDescriptif.php?tri=$id";
	} else {
		return "modifDescriptif.php?tri=$id&desc=true";
  }
}

// génère la page HTML
function affiche_corps($descriptifs, $errors) {
	$curid = isset($_GET['id']) ? $_GET['id'] : '';
	$cureditid = isset($_GET['edit']) ? $_GET['edit'] : '';
	echo '<p>Consultation de la table <strong>descriptif</strong></p>';

	foreach ($errors as $error) {
		echo "<p class=\"error\">$error</p>";
	}
	if (count($errors) > 0) {
		affiche_conseils_erreurs();
	}

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th></th>';
	echo '			<th><a href="'. sorted_column_path(1) .'">idtroup</a></th>';
	echo '			<th><a href="'. sorted_column_path(2) .'">idesp</a></th>';
	echo '			<th><a href="'. sorted_column_path(3) .'">sexe</a></th>';
	echo '			<th><a href="'. sorted_column_path(4) .'">nombre</a></th>';
	echo '			<th></th>';
	echo '			<th></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';

	foreach ($descriptifs as $descriptif) {
		if (is_pk($descriptif, $cureditid)) {
			affiche_formulaire_modification($descriptif);
		} else {
			if (is_pk($descriptif, $curid)) {
				echo '<tr class="current">';
			} else {
				echo '<tr>';
      }
			echo "<td><a href=\"modifDescriptif.php?id=".pk($descriptif)."\">#</a></td>";
			echo "<td><a href=\"modifTroupeau.php?id={$descriptif->idtroup}\">{$descriptif->idtroup}</a></td>";
			echo "<td><a href=\"modifEspece.php?id={$descriptif->idesp}\">{$descriptif->idesp}</a></td>";
			echo "<td>{$descriptif->sexe}</td>";
			echo "<td>{$descriptif->nombre}</td>";
			echo "<td><a href=\"modifDescriptif.php?edit=".pk($descriptif)."\">modifier</a></td>";
			echo "<td><a href=\"modifDescriptif.php?del=".pk($descriptif)."\" class=\"row_del\">supprimer</a></td>";
			echo '</tr>';
    }
	}

	if (!isset($_GET['edit'])) {
		affiche_formulaire_creation();
	}

	echo '  </tbody>';
	echo '</table>';
}

function affiche_formulaire_modification($descriptif) {
	echo '<tr>';
	echo '<form action="modifDescriptif.php?edit='.pk($descriptif).'" method="post">';
	echo '<td></td>';
	echo "<td><input type=\"text\" name=\"idtroup\" value=\"{$descriptif->idtroup}\"/></td>";
	echo "<td><input type=\"text\" name=\"idesp\" value=\"{$descriptif->idesp}\"/></td>";
	echo '<td><select name="sexe">
		<option value="male"'.($descriptif->sexe == 'male' ? ' selected="selected"' : '').'>Mâle</option>
		<option value="femelle"'.($descriptif->sexe == 'femelle' ? ' selected="selected"' : '').'>Femelle</option>
	</select></td>';
	echo "<td><input type=\"text\" name=\"nombre\" value=\"{$descriptif->nombre}\"/></td>";
	echo "<td><input type=\"submit\" value=\"modifier\" /></td>";
	echo "<td><a href=\"modifDescriptif.php?del=".pk($descriptif)."\">supprimer</a></td>";
	echo '</form>';
	echo '</tr>';
}

function affiche_formulaire_creation() {
	echo '<tr>';
	echo '<form action="modifDescriptif.php" method="post">';
	echo '<td></td>';
	echo '<td><input type="text" name="idtroup"	placeholder="idtroup" /></td>';
	echo '<td><input type="text" name="idesp"	placeholder="idesp" /></td>';
	echo '<td><select name="sexe">
		<option value="male">Mâle</option>
		<option value="femelle">Femelle</option>
	</select></td>';
	echo '<td><input type="text" name="nombre"	placeholder="nombre" /></td>';
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
		<li><em>idtroup</em>, <em>idesp</em>, et <em>nombre</em> sont des entiers numériques</li>
		<li>la valeur d'<em>idtroup</em> soit présente dans la table <em>troupeau</em></li>
		<li>la valeur d'<em>idesp</em> soit présente dans la table <em>espece</em></li>
	</ul>
</div>
<?php
}
