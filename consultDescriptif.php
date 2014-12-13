<?php

include 'config.inc.php';
include 'fctaux.inc.php';
include 'db.inc.php';

session_start();
user_assert_authenticated();

entete();
corps();
footer();

function corps() {
	$descriptifs = DB::getInstance()->select(build_query());
	
	affiche_corps($descriptifs);
}

function build_query() {
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

function sublink($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "consultDescriptif.php?tri=$id";
	} else {
		return "consultDescriptif.php?tri=$id&desc=true";
  }
}

function affiche_corps($descriptifs) {
	echo '<p>Consultation de la table <strong>descriptif</strong></p>';

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th><a href="'. sublink(1) .'">idtroup</a></th>';
	echo '			<th><a href="'. sublink(2) .'">idesp</a></th>';
	echo '			<th><a href="'. sublink(3) .'">sexe</a></th>';
	echo '			<th><a href="'. sublink(4) .'">nombre</a></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';
	foreach ($descriptifs as $descriptif) {
		echo '<tr>';
		echo "<td><a href=\"consultTroupeau.php?id={$descriptif->idtroup}\">{$descriptif->idtroup}</a></td>";
		echo "<td><a href=\"consultEspece.php?id={$descriptif->idesp}\">{$descriptif->idesp}</a></td>";
		echo "<td>{$descriptif->sexe}</td>";
		echo "<td>{$descriptif->nombre}</td>";
		echo '</tr>';
  }
	echo '  </tbody>';
	echo '</table>';
}
