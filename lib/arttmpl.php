<?php
/*
	Redaxo-Addon Articletemplates
	Builder-Basisklasse
	v1.0
	by Falko Müller @ 2024
*/

class arttmpl {

	//Config des Addons auslesen
    public static function getConfig($sKey=null)
	{	
        global $a1826_mypage;
	
        $aConfig = rex_addon::get($a1826_mypage)->getConfig('config');
	        if ($sKey != ""):
				return (isset($aConfig[$sKey])) ? $aConfig[$sKey] : null;
			endif;
            
        return $aConfig;
    }

    
    //TemplatePopup hinzufügen (BE)
    public static function addTemplateSelector($ep)
    {
        $op = $ep->getSubject();
        
            $html = '<div id="arttmpl-selector" data-pjax-container="#rex-js-page-main-content">';      //data-pjax-container="#rex-js-page-main-content"
                $html .= '<div class="arttmpl-inner">';
                                    
                    $html .= '<div class="arttmpl-header">';
                        $html .= '<div class="arttmpl-header">'.rex_i18n::msg('a1826_mod_header').'</div>';
                        if (arttmpl::getConfig('use_search') == 'checked'):
                            $html .= '<div class="arttmpl-search"><form><input type="text" class="form-control" placeholder="'.rex_i18n::msg('a1826_mod_searching').'" /></form></div>';
                        endif;
                        $html .= '<div class="arttmpl-close"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                    $html .= '</div>';
                    
                    $html .= '<div class="arttmpl-content"><p class="arttmpl-loading">'.rex_i18n::msg('a1826_mod_loading').'</p></div>';
                    
                $html .= '</div><div class="arttmpl-overlay"></div>';
            $html .= '</div>';

        $op = preg_replace('#</body>#i', $html.'</body>', $op);
        
        return $op;
    }

    
    //TemplateOpener (Button) hinzufügen (BE)
    public static function addTemplateButton($ep)
    {
        $artid  = rex_article::getCurrentId();
        $clid   = rex_clang::getCurrentId();
        
        //$canEmbed = (!rex_plugin::get('structure', 'version')->isAvailable() || rex_plugin::get('structure', 'version')->isAvailable() && rex_article_revision::getSessionArticleRevision($artid) == 1) ? true : false;
        $canEmbed = true;           //immer einbetten, da beim Übernehmen sowohl Arbeitsversion als auch Liveversion kopiert werden
        
        
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
        //dump($db);
        
        
        if ($canEmbed && $db->getRows() <= 0):
            $op     = $ep->getSubject();
            $ctype  = $ep->getParam('ctype');
            
                //$html = "ArtID: $artid / ClangID: $clid / CType: $ctype";
                
                $html = '<div class="btn-block" id="arttmpl-button">';
                    $html .= '<button class="btn btn-default btn-block" type="button" data-artid="'.$artid.'" data-clid="'.$clid.'" data-ctype="'.$ctype.'">';
                        $html .= '<strong><i class="rex-icon fa-files-o" aria-hidden="true"></i> '.rex_i18n::msg('a1826_mod_addtemplate').'</strong>';
                    $html .= '</button>';
                $html .= '</div>';
        
            $ep->setSubject($html.$op);
        endif;
    }
    

    //Querystring holen
    public static function getQS()
    {
        $qs = hpbuilderone_helper::textOnly($_SERVER['QUERY_STRING'], true, true);
        $qs = (!empty($qs)) ? '?'.$qs : '';
        
        return $qs;
    }
	
}
?>