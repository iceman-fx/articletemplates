<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: AJAX Loader - Kategorien
	v1.0.1
	by Falko Müller @ 2024
*/

//Vorgaben
$mainTable = '1826_articletemplates_cat';				//primäre SQL-Tabelle dieses Bereiches


//Paramater
$page = rex_request('page', 'string');
$subpage = "";							//ggf. manuell setzen
$subpage2 = rex_be_controller::getCurrentPagePart(3);												//2. Unterebene = dritter Teil des page-Parameters
	$subpage2 = (!empty($subpage2)) ? preg_replace("/.*-([0-9])$/i", "$1", $subpage2) : '';			//Auslesen der ClangID

$sbeg = trim(urldecode(rex_request('sbeg')));

$order = (strtolower(rex_request('order')) == 'desc') ? 'DESC' : 'ASC';

$limStart = rex_request('limStart', 'int');


//Sessionwerte zurücksetzen
$_SESSION['as_sbeg_arttmpl_cat'] = "";


//AJAX begin
echo '<!-- ###AJAX### -->';


//SQL erstellen und Filterung berücksichtigen
$sql = "SELECT * FROM ".rex::getTable($mainTable);
$sql_where = " WHERE 1";


//Eingrenzung: Suchbegriff
if (!empty($sbeg)):
	$_SESSION['as_sbeg_arttmpl_cat'] = $sbeg;
	$sql_where .= " AND ( 
					BINARY LOWER(title) like LOWER('%".arttmpl_helper::maskSql($sbeg)."%') 
					)";
endif;


//Eingrenzung: Sprache (clangID)
//$sql_where .= ($subpage2 > 0) ? " AND clang_id = '".$subpage2."'" : '';


//Sortierung
$sql_where .= " ORDER BY title ".$order.", id ASC";


//Limit
$limStart = ($limStart > 0) ? $limStart : 0;
$limCount = 25;
$sql_limit = " LIMIT ".($limStart * $limCount).",".$limCount;


//SQL zwischenspeichern
//$_SESSION['as_sql_arttmpl_cat'] = $sql.$sql_where;


//Ergebnisse nachladen
$db = rex_sql::factory();
$db->setQuery($sql.$sql_where.$sql_limit);
$addPath = "index.php?page=".$page;

	/*
	echo "<tr><td colspan='10'>$sql$sql_where$sql_limit</td></tr>";
	echo "<tr><td colspan='10'>Anzahl Datensätze: ".$db->getRows()."</td></tr>";
	*/
	

            if ($db->getRows() > 0):
                //Liste ausgeben
                for ($i=0; $i < $db->getRows(); $i++):
					$eid = intval($db->getValue('id'));
					$editPath = $addPath.'&amp;func=update&amp;id='.$eid;

                    $title 	= arttmpl_helper::maskChar(arttmpl_helper::textOnly($db->getValue('title'), true));
                    
                    
                    //Ausgabe
                    ?>
                        
                    <tr id="entry<?php echo $eid; ?>">
                        <td class="rex-table-icon"><a href="<?php echo $editPath; ?>" title="<?php echo $this->i18n('a1826_edit'); ?>"><i class="rex-icon rex-icon-article"></i></a></td>
                        <td class="rex-table-id"><?php echo $eid; ?></td>
                        <td data-title="<?php echo $this->i18n('a1826_bas_list_name'); ?>"><a href="<?php echo $editPath; ?>"><?php echo $title; ?></a></td>
                        
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