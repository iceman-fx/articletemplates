<?php
/*
	Redaxo-Addon Articletemplates
	API-Anbindung
	v1.0
	by Falko Müller @ 2024
*/

class rex_api_arttmpl_loadTemplates extends rex_api_function
{
    public function execute()
    {
        $aid 	= rex_request::get('aid', 'int', null);
        $clid 	= rex_request::get('clid', 'int', null);
        $ctype 	= rex_request::get('ctype', 'int', null);
        $sbeg   = trim(urldecode(rex_request('sbeg')));
    
        if ($aid && $clid && $ctype):
            $sql_where = '';
                if (!empty($sbeg)):
                    $sql_where .= "WHERE ( 
                                   BINARY LOWER(template_name) like LOWER('%".arttmpl_helper::maskSql($sbeg)."%') 
                                   OR BINARY LOWER(description) like LOWER('%".arttmpl_helper::maskSql($sbeg)."%')
                                   )";
                endif;
            
            $db = rex_sql::factory();
            $db->setQuery("SELECT * FROM ( SELECT DISTINCT(t1.id), t1.id_cat, t1.title, t1.description, t1.media, t1.article_id, t1.clang_id, IF(TRIM(t1.title)='', t2.name, t1.title) AS 'template_name', t3.title AS 'catname' FROM ".rex::getTable('1826_articletemplates')." AS t1 INNER JOIN ".rex::getTable('article')." AS t2 ON t1.article_id = t2.id LEFT JOIN rex_1826_articletemplates_cat AS t3 ON t1.id_cat = t3.id ) AS tmplitems ".$sql_where." ORDER BY catname ASC, template_name ASC, id ASC");
            
            
            $css = (arttmpl::getConfig('imageorientation') == 'pt') ? 'arttmpl-images-pt ' : '';
            $css .= (arttmpl::getConfig('displaymode') == 'list') ? 'arttmpl-mode-list ' : '';
            

            if ($db->getRows() > 0):
                $lastCat = -1;
                
                echo '<ul class="arttmpl-list '.$css.'">';

                for ($i=0; $i < $db->getRows(); $i++):
                    $eid = intval($db->getValue('id'));
                    $article_id = intval($db->getValue('article_id'));
                    $addPath = 'index.php?page=content/edit&amp;article_id='.$aid.'&amp;clang='.$clid.'&amp;ctype='.$ctype.'&amp;mode=edit&amp;arttmpl_action=copy&amp;arttmpl_sid='.$article_id;
                    
                    
                    //prüfen, ob Vorlagenartikel existiert
                    $art = rex_article::get($article_id);                    
                    if (empty($article_id) || !$art) { $db->next(); continue; }
                    
                    
                    //Daten aufbereiten
                    $cat  = intval($db->getValue('id_cat'));
                    
                    $title  = arttmpl_helper::textOnly($db->getValue('title'));
                        $title  = (empty($title) && arttmpl::getConfig('use_artname') == 'checked') ? arttmpl_helper::textOnly($db->getValue('template_name')) : $title;
                        $title  = (empty($title)) ? '[ID: '.$eid.']' : $title;
                    
                    $desc   = arttmpl_helper::textOnly($db->getValue('description'));
                        $desc = (!empty($desc)) ? '<div class="arttmpl-item-desc">'.$desc.'</div>' : '';
                    
                    $media  = $db->getValue('media');
                        $media = (!empty($media)) ? rex_media_manager::getUrl('rex_media_medium', $media) : rex_url::addonAssets('articletemplates', 'noimage.png');
                    $media  = (!empty($media) && arttmpl::getConfig('displaymode') != 'list') ? '<div class="arttmpl-item-image"><img src="'.$media.'" alt="" /></div>' : '';
                    
                    
                    //Kategorie auswerten
                    $cat_html = '';
                    if ($cat != $lastCat):
                        $dbc = rex_sql::factory();
                        $dbc->setQuery("SELECT * FROM ".rex::getTable('1826_articletemplates_cat')." WHERE id = '".$cat."' LIMIT 0,1");
            
                        if ($dbc->getRows() > 0):
                            $cat_title = arttmpl_helper::textOnly($dbc->getValue('title'));
                            $cat_desc = arttmpl_helper::textOnly($dbc->getValue('description'), false);
                                $cat_desc = (!empty($cat_desc)) ? '<div class="arttmpl-cat-desc">'.nl2br($cat_desc).'</div>' : '';
                                
                            $cat_html = '
                            <li class="arttmpl-item arttmpl-cat">
                                <div class="arttmpl-cat-header">'.$cat_title.'</div>
                                '.$cat_desc.'
                            </li>
                            ';
                        endif;
                    endif;
                    
                    $lastCat = $cat;
                    
                    
                    //sameTemplate prüfen
                    if (arttmpl::getConfig('use_sametemplates') == 'checked'):
                        $tmp = rex_article::get($aid);
                        $tid = ($tmp) ? $tmp->getTemplateId() : 0;
                    
                        $article_id = intval($db->getValue('article_id'));
                        $article = rex_article::get($article_id);
                        if ($article && $tid != $article->getTemplateId()) { $db->next(); continue; }
                    endif;
                    
                    
                    //Item ausgeben
                    ?>

                    <?php echo $cat_html; ?>
                    <li class="arttmpl-item">
                        <a href="<?php echo $addPath; ?>">
                            <div class="arttmpl-item-header"><?php echo $title; ?></div>
                            <?php echo $media; ?>
                            <?php echo $desc; ?>
                        </a>
                    </li>

                    <?php
                    $db->next();
                endfor;



                echo '</ul>';

            else:
                //nicht gefunden
                echo '<p class="arttmpl-loading">'.rex_i18n::msg('a1826_mod_notfound').'</p>';

            endif;

            exit();
        endif;
		
        throw new rex_functional_exception('Article-ID, clang-ID and ctype-ID parameters are required!');
    }
}

?>