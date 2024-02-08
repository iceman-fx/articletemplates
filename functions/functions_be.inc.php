<?php
/*
	Redaxo-Addon Articletemplates
	Backend-Funktionen (Addon + Modul)
	v1.0
	by Falko Müller @ 2024
*/

//aktive Session prüfen


//globale Variablen


//Funktionen
//prüft die Bezeichnung auf Vorhandensein
function a1826_duplicateName($str)
{	if (!empty($str)):
		$db = rex_sql::factory();
		$db->setQuery("SELECT id, title FROM ".rex::getTable('1826_articletemplates')." WHERE title = '".arttmpl_helper::maskSql($str)."'"); 
		
		if ($db->getRows() > 0):
			$str = $str.rex_i18n::msg('a1826_copiedname');
			$str = a1826_duplicateName($str);
		endif;
	endif;

	return $str;
}


function a1826_duplicateNameCat($str)
{	if (!empty($str)):
		$db = rex_sql::factory();
		$db->setQuery("SELECT id, title FROM ".rex::getTable('1826_articletemplates_cat')." WHERE title = '".arttmpl_helper::maskSql($str)."'"); 
		
		if ($db->getRows() > 0):
			$str = $str.rex_i18n::msg('a1826_copiedname');
			$str = a1826_duplicateNameCat($str);
		endif;
	endif;

	return $str;
}

?>