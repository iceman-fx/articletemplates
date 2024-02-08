<?php
/*
	Redaxo-Addon Articletemplates
	DeInstallation
	v1.0
	by Falko Müller @ 2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = ""; $notice = "";


//Datenbank-Einträge löschen
$db = rex_sql::factory();
$db->setQuery("DROP TABLE IF EXISTS ".rex::getTable('1826_articletemplates'));

$db = rex_sql::factory();
$db->setQuery("DROP TABLE IF EXISTS ".rex::getTable('1826_articletemplates_cat'));
?>