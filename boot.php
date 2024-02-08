<?php
/*
	Redaxo-Addon Articletemplates
	Boot (weitere Konfigurationen & Einbindung)
	v1.0
	by Falko Müller @ 2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');


//Userrechte prüfen
$isAdmin = ( is_object(rex::getUser()) AND (rex::getUser()->hasPerm($mypage.'[admin]') OR rex::getUser()->isAdmin()) ) ? true : false;


//Addon Einstellungen
$config = $this->getConfig('config');			                                    //Addon-Konfig einladen


//Funktionen einladen/definieren
//Global für Backend+Frontend
global $a1826_mypage;
$a1826_mypage = $mypage;

global $a1826_darkmode;
$a1826_darkmode = (rex_string::versionCompare(rex::getVersion(), '5.13.0-dev', '>=')) ? true : false;


//Backend
if (rex::isBackend()):
	require_once(rex_path::addon($mypage, "functions/functions_be.inc.php"));
    
	if (rex::getUser()):

        //AJAX anbinden
        $ajaxPages = array('load-defaultlist', 'load-catlist');
            if (rex_be_controller::getCurrentPagePart(1) == $mypage && in_array(rex_request('subpage', 'string'), $ajaxPages)):
                rex_extension::register('OUTPUT_FILTER', 'arttmpl_helper::bindAjax');
            endif;
			

        //BE-Templateauswahl einbinden
        rex_extension::register('OUTPUT_FILTER', 'arttmpl::addTemplateSelector');
        
        rex_extension::register('STRUCTURE_CONTENT_MODULE_SELECT', function(rex_extension_point $ep){
			arttmpl::addTemplateButton($ep);
		}, rex_extension::LATE);
        
        
        //Template in neuen Aertikel kopieren
        $artid 	      = rex_request::get('article_id', 'int', null);
        $clid 	      = rex_request::get('clang', 'int', null);
        $arttmpl_sid  = rex_request::get('arttmpl_sid', 'int', null);
        $page         = arttmpl_helper::textOnly(rex_request::get('page'));
        $action       = arttmpl_helper::textOnly(rex_request::get('arttmpl_action'));
        
        if ($action == 'copy' && $page == 'content/edit' && !empty($arttmpl_sid) && !empty($artid) && !empty($clid)):            
            //prüfen, ob Artikel bereits Inhalte hat
            $cts = 0;
            $art = rex_article::get($artid);
                if ($art):
                    $art_cts = rex_ctype::forTemplate($art->getTemplateId());
                    $cts = count($art_cts);
                endif;
            $cts = ($cts < 1) ? 1 : $cts;
        
            $db = rex_sql::factory();
            $db->setQuery("SELECT id FROM ".rex::getTable('article_slice')." WHERE article_id = '".$artid."' AND clang_id = '".$clid."' AND ctype_id <= '".$cts."'");
            
            //prüfen, ob Vorlagenartikel existiert
            $art = rex_article::get($arttmpl_sid);
            
        
            if ($art && $db->getRows() <= 0):
                rex_content_service::copyContent($arttmpl_sid, $artid, $clid, $clid);
            else:
                rex_extension::register('STRUCTURE_CONTENT_HEADER', function(rex_extension_point $ep){
                    echo rex_view::warning(rex_i18n::msg('a1826_error_noarticle_or_notempty'), 'arttmpl-alert-warning');
                    echo '<script>setTimeout(function() { $("div.arttmpl-alert-warning").fadeOut(); }, 5000)</script>';
                });
            endif;
        endif;
        
	endif;
endif;



// Assets im Backend einbinden (z.B. style.css) - es wird eine Versionsangabe angehängt, damit nach einem neuen Release des Addons die Datei nicht aus dem Browsercache verwendet wird
rex_view::addCssFile($this->getAssetsUrl('style.css'));
if ($a1826_darkmode) { rex_view::addCssFile($this->getAssetsUrl('style-darkmode.css')); }

rex_view::addJsFile($this->getAssetsUrl('script.js'));
?>