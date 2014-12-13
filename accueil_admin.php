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

function corps() {
	global $USER;
	echo "Bienvenue {$USER->username}!";
}
