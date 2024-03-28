<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: AJAX Loader - Artikelliste
	v1.0.1
	by Falko Müller @ 2024
*/

//Vorgaben
$mainTable = '1826_articletemplates';				//primäre SQL-Tabelle dieses Bereiches


//Paramater
$page = rex_request('page', 'string');
$subpage = "";							//ggf. manuell setzen

$clang_id = rex_be_controller::getCurrentPagePart(3);													//2. Unterebene = dritter Teil des page-Parameters
	$clang_id = (!empty($clang_id)) ? intval(preg_replace("/.*-([0-9])$/i", "$1", $clang_id)) : 0;		//Auslesen der ClangID
	$clang_id = ($clang_id <= 0) ? 1 : $clang_id;

$sbeg = trim(urldecode(rex_request('sbeg')));
//$id = rex_request('id', 'int');
$s_cat = rex_request('s_cat', 'int');

$order = (strtolower(rex_request('order')) == 'desc') ? 'DESC' : 'ASC';

$limStart = rex_request('limStart', 'int');


//Sessionwerte zurücksetzen
$_SESSION['as_sbeg_arttmpl'] = $_SESSION['as_cat_arttmpl'] = "";


//AJAX begin
echo '<!-- ###AJAX### -->';


//SQL erstellen und Filterung berücksichtigen
$sql = "SELECT * FROM ".rex::getTable($mainTable);
$sql_where = " WHERE 1";


//Eingrenzung: Suchbegriff
if (!empty($sbeg)):
	$_SESSION['as_sbeg_arttmpl'] = $sbeg;
	$sql_where .= " AND ( 
					BINARY LOWER(title) like LOWER('%".arttmpl_helper::maskSql($sbeg)."%') 
					OR BINARY LOWER(description) like LOWER('%".arttmpl_helper::maskSql($sbeg)."%')
					)";
					//BINARY sorgt für einen Binärvergleich, wodurch Umlaute auch als Umlaute gewertet werden (ohne BINARY ist ein Ä = A)
					//LOWER sorgt für einen Vergleich auf Basis von Kleinbuchstaben (ohne LOWER würde das BINARY nach Groß/Klein unterscheiden)
					//DATE_FORMAT wandelt den Wert in eine andere Schreibweise um (damit kann der gespeicherte Wert vom gesuchten Wert abweichen) --> DATE_FORMAT(`date`, '%e.%m.%Y')
					//FROM_UNIXTIME arbeit wie DATE-FORMAT, aber benötigt als Quelle einen timestamp
					//		OR ( FROM_UNIXTIME(`date`, '%e.%m.%Y') like '".aFM_maskSql($sbeg)."%' OR FROM_UNIXTIME(`date`, '%d.%m.%Y') like '".aFM_maskSql($sbeg)."%' )
endif;


//Eingrenzung: Sprache (clangID)
//$sql_where .= ($clang_id > 0) ? " AND clang_id = '".$clang_id."'" : '';


//Eingrenzung: Kategorie
if ($s_cat > 0):
	$_SESSION['as_cat_arttmpl'] = $s_cat;
	$sql_where .= " AND id_cat = '".$s_cat."'";
endif;


//Sortierung
$sql_where .= " ORDER BY title ".$order.", id ASC";


//Limit
$limStart = ($limStart > 0) ? $limStart : 0;
$limCount = 25;
$sql_limit = " LIMIT ".($limStart * $limCount).",".$limCount;


//SQL zwischenspeichern
//$_SESSION['as_sql_arttmpl'] = $sql.$sql_where;


//Ergebnisse nachladen
$db = rex_sql::factory();
$db->setQuery($sql.$sql_where.$sql_limit);
$addPath = "index.php?page=".$page;

	/*
	echo "<tr><td colspan='10'>$sql$sql_where$sql_limit</td></tr>";
	echo "<tr><td colspan='10'>Anzahl Datensätze: ".$db->getRows()."</td></tr>";
	*/
	

            if ($db->getRows() > 0):
                $css = (arttmpl::getConfig('imageorientation') == 'pt') ? 'arttmpl-images-pt' : 'arttmpl-images-ls';
                
                
                //Liste ausgeben
                for ($i=0; $i < $db->getRows(); $i++):
					$eid = intval($db->getValue('id'));
					$editPath = $addPath.'&amp;func=update&amp;id='.$eid;
					
					$article_id = $db->getValue('article_id');
                        $article = rex_article::get($article_id);
                            if ($article):
                                $artID = $article->getId();
                                $article_id = '<a href="index.php?page=content/edit&article_id='.$artID.'&clang='.rex_clang::getCurrentId().'&mode=edit" target="_blank">'.$article->getName().' [ID: '.$artID.']</a>';
                            else:
                                $article_id = '<span class="rex-offline"><i class="rex-icon fa-exclamation-triangle"></i> '.$this->i18n('a1826_bas_list_artid_notfound').' [ID: '.$article_id.']</span>';
                            endif;

					$title = $db->getValue('title');
                        $title = (arttmpl::getConfig('use_artname') == 'checked' && empty($title) && $article) ? $article->getName() : $title;
                        
                    $media = $db->getValue('media');
                        $media = (!empty($media)) ? rex_media_manager::getUrl('rex_media_small', $media) : rex_url::addonAssets($mypage, 'noimage.png');
                    $media = (!empty($media)) ? '<div class="arttmpl_image"><img src="'.$media.'" /></div>' : '';
                    
                    
                    //Ausgabe
                    ?>
                        
                    <tr id="entry<?php echo $eid; ?>" class="<?php echo $css; ?>">
                        <td class="rex-table-icon"><a href="<?php echo $editPath; ?>" title="<?php echo $this->i18n('a1826_edit'); ?>"><i class="rex-icon rex-icon-article"></i></a></td>
                        <td class="rex-table-id"><?php echo $eid; ?></td>
                        <td data-title="<?php echo $this->i18n('a1826_bas_list_name'); ?>"><a href="<?php echo $editPath; ?>"><?php echo $title; ?></a></td>
                        <td data-title="<?php echo $this->i18n('a1826_bas_list_artid'); ?>"><?php echo $article_id; ?></td>
                        <td data-title="<?php echo $this->i18n('a1826_bas_list_media'); ?>"><?php echo $media; ?></td>
                        
                        <td class="rex-table-action"><a href="<?php echo $editPath; ?>"><i class="rex-icon rex-icon-edit"></i> <?php echo $this->i18n('a1826_edit'); ?></a></td>
                        <td class="rex-table-action"><a href="<?php echo $addPath; ?>&func=duplicate&id=<?php echo $eid; ?>"><i class="rex-icon rex-icon-duplicate"></i> <?php echo $this->i18n('a1826_duplicate'); ?></a></td>
                        <td class="rex-table-action"><a href="<?php echo $addPath; ?>&func=delete&id=<?php echo $eid; ?>" data-confirm="<?php echo $this->i18n('a1826_delete'); ?> ?"><span class="rex-offline"><i class="rex-icon rex-icon-delete"></i> <?php echo $this->i18n('a1826_delete'); ?></span></a></td>
                    </tr>
                    
                    <?php
					$db->next();
                endfor;
				
				
				//Seitenschaltung generieren
				$dbl = rex_sql::factory();
				$dbl->setQuery($sql.$sql_where);
					$maxEntry = $dbl->getRows();
					$maxSite = ceil($maxEntry / $limCount);

				if ($dbl->getRows() > $limCount):
					echo '<tr><td colspan="10" align="center"><ul class="addon_list-pagination pagination">';
					
					for ($i=0; $i<$maxSite; $i++):
						$sel = ($i == $limStart) ? 'ajaxNavSel' : '';
						$selLI = ($i == $limStart) ? 'active' : '';
						echo '<li class="rex-page '.$selLI.'"><span class="ajaxNav '.$sel.'" data-navsite="'.$i.'">'.($i+1).'</span></li>';
					endfor;
					
					echo '</ul></td></tr>';
				endif;
				
            else:
                ?>
                
                    <tr>
                        <td colspan="10" align="center"> - <?php echo $this->i18n('a1826_search_notfound'); ?> -</td>
                    </tr>
                
                <?php
            endif;

//AJAX end
echo '<!-- ###/AJAX### -->';
?>