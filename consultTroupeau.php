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
	$troupeaux = DB::getInstance()->select(build_query());
	
	affiche_corps($troupeaux);
}

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

function sublink($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "consultTroupeau.php?tri=$id";
	} else {
		return "consultTroupeau.php?tri=$id&desc=true";
  }
}

function affiche_corps($troupeaux) {
	$curid = $_GET['id'];
	echo '<p>Consultation de la table <strong>troupeau</strong></p>';

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th><a href="'. sublink(1) .'">idtroup</a></th>';
	echo '			<th><a href="'. sublink(2) .'">nom</a></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';
	foreach ($troupeaux as $troupeau) {
		if ($troupeau->idtroup == $curid) {
			echo '<tr class="current">';
		} else {
			echo '<tr>';
    }

		echo "<td><a href=\"consultTroupeau.php?id={$troupeau->idtroup}\">{$troupeau->idtroup}</a></td>";
		echo "<td>{$troupeau->nom}</td>";
		echo '</tr>';
  }
	echo '  </tbody>';
	echo '</table>';
}
