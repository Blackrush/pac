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
	$especes = DB::getInstance()->select(build_query());
	
	affiche_corps($especes);
}

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

function sublink($id) {
	if (!isset($_GET['tri']) || $_GET['tri'] == $id && isset($_GET['desc'])) {
		return "consultEspece.php?tri=$id";
	} else {
		return "consultEspece.php?tri=$id&desc=true";
  }
}

function affiche_corps($especes) {
	$curid = $_GET['id'];
	echo '<p>Consultation de la table <strong>espece</strong></p>';

	echo '<table>';
	echo '  <thead>';
	echo '		<tr>';
	echo '			<th><a href="'. sublink(1) .'">idesp</a></th>';
	echo '			<th><a href="'. sublink(2) .'">nom</a></th>';
	echo '			<th><a href="'. sublink(3) .'">type</a></th>';
	echo '		</tr>';
	echo '  </thead>';

	echo '  <tbody>';
	foreach ($especes as $espece) {
		if ($espece->idesp == $curid) {
			echo '<tr class="current">';
		} else {
			echo '<tr>';
    }
		echo "<td><a href=\"consultEspece.php?id={$espece->idesp}\">{$espece->idesp}</a></td>";
		echo "<td>{$espece->nom}</td>";
		echo "<td>{$espece->type}</td>";
		echo '</tr>';
  }
	echo '  </tbody>';
	echo '</table>';
}
