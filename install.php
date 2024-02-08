<?php
/*
	Redaxo-Addon Articletemplates
	Installation
	v1.0
	by Falko Müller @ 2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
if (!$this->hasConfig()):
	$this->setConfig('config', [
		'use_search'				=> 'checked',
        'use_artname'				=> 'checked',
        'use_sametemplates'			=> '',
		'displaymode'				=> 'boxed',
        'imageorientation'			=> 'ls',
	]);
endif;



//Datenbank-Einträge vornehmen
rex_sql_table::get(rex::getTable('1826_articletemplates'))
	->ensureColumn(new rex_sql_column('id', 'int(100)', false, null, 'auto_increment'))
	->ensureColumn(new rex_sql_column('id_cat', 'int(100)'))
	->ensureColumn(new rex_sql_column('title', 'varchar(255)'))
	->ensureColumn(new rex_sql_column('description', 'text'))
	->ensureColumn(new rex_sql_column('media', 'varchar(255)'))
	->ensureColumn(new rex_sql_column('article_id', 'int(100)'))
	->ensureColumn(new rex_sql_column('clang_id', 'int(100)'))
	->ensureGlobalColumns()
	->setPrimaryKey('id')
	->ensure();

rex_sql_table::get(rex::getTable('1826_articletemplates_cat'))
	->ensureColumn(new rex_sql_column('id', 'int(100)', false, null, 'auto_increment'))
	->ensureColumn(new rex_sql_column('title', 'varchar(255)'))
	->ensureColumn(new rex_sql_column('description', 'text'))
    ->ensureColumn(new rex_sql_column('clang_id', 'int(100)'))
	->ensureGlobalColumns()
	->setPrimaryKey('id')
	->ensure();


//Module anlegen
?>