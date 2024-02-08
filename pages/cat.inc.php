<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: Kategorien
	v1.0
	by Falko Müller @ 2024
*/

//Vorgaben
$mainTable = '1826_articletemplates_cat';				//primäre SQL-Tabelle dieses Bereiches


//Variablen deklarieren
$mode = rex_request('mode');
$id = intval(rex_request('id'));
$form_error = $formvalue_error = 0;


$clang_id = rex_be_controller::getCurrentPagePart(3);													//2. Unterebene = dritter Teil des page-Parameters
	$clang_id = (!empty($clang_id)) ? intval(preg_replace("/.*-([0-9])$/i", "$1", $clang_id)) : 0;		//Auslesen der ClangID
	$clang_id = ($clang_id <= 0) ? 1 : $clang_id;


$_SESSION['as_sbeg_aknews_cat'] = (!isset($_SESSION['as_sbeg_aknews_cat'])) ? "" : $_SESSION['as_sbeg_aknews_cat'];


//Formular dieser Seite verarbeiten
if ($func == "save" && (isset($_POST['submit']) || isset($_POST['submit-apply'])) ):
	//Pflichtfelder prüfen
	$fields = array("f_title");
		foreach ($fields as $field):
			$tmp = rex_post($field);
			$form_error = (empty($tmp)) ? 1 : $form_error;
		endforeach;
		
	//Eingaben prüfen
	//$formvalue_error = (!preg_match("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/i", rex_post('f_date'))) ? 1 : $formvalue_error;
		
		
	if ($form_error):
		//Pflichtfelder fehlen
		echo rex_view::warning($this->i18n('a1826_entry_emptyfields'));
	elseif ($formvalue_error):
		//Eingaben fehlerhaft
		echo rex_view::warning($this->i18n('a1826_entry_invaliddata'));
        $form_error = 1;
    else:
		//Eintrag speichern
		$db = rex_sql::factory();
		$db->setTable(rex::getTable($mainTable));

		$db->setValue("title", 			    rex_post('f_title'));
        $db->setValue("description", 		rex_post('f_description'));

        $db->setValue("clang_id",           $clang_id); 


		if ($id > 0):
			$db->addGlobalUpdateFields();						//Standard Datumsfelder hinzufügen
			$db->setWhere("id = '".$id."'");
			$dbreturn = $db->update();
			$lastID = $id;

			$form_error = (isset($_POST['submit-apply'])) ? 1 : $form_error;
		else:
			$db->addGlobalCreateFields();						//Standard Datumsfelder hinzufügen
			$dbreturn = $db->insert();
			$lastID = $db->getLastId();
		endif;
        
        echo ($dbreturn) ? rex_view::info($this->i18n('a1826_entry_saved')) : rex_view::warning($this->i18n('a1826_error'));
	endif;
	
elseif ($func == "delete" && $id > 0):
	//Eintrag löschen - mit möglicher Prüfung auf Zuweisung
	$db = rex_sql::factory();
	$db->setQuery("SELECT id FROM ".rex::getTable('1826_articletemplates')." WHERE id_cat = '".$id."'"); 

	if ($db->getRows() <= 0):
		//löschen
		$db = rex_sql::factory();
		$db->setTable(rex::getTable($mainTable));		
		$db->setWhere("id = '".$id."'");
			
		echo ($db->delete()) ? rex_view::info($this->i18n('a1826_entry_deleted')) : rex_view::warning($this->i18n('a1826_error_deleted'));
	else:
		//nicht löschen aufgrund gültiger Zuweisung
		echo rex_view::warning($this->i18n('a1826_entry_used'));
	endif;	

elseif ($func == "duplicate" && $id > 0):
	//Eintrag duplizieren
	$db = rex_sql::factory();
	$db->setQuery("SELECT * FROM ".rex::getTable($mainTable)." WHERE id = '".$id."'");

	if ($db->getRows() > 0):
		$dbp = rex_sql::factory();
		$dbp->setQuery("SELECT id FROM ".rex::getTable($mainTable));
		$maxPrio = $dbp->getRows();

		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
		$db = rex_sql::factory();
		$db->setTable(rex::getTable($mainTable));

		foreach ($dbe[0] as $key=>$val):
			if ($key == 'id') { continue; }
			if ($key == 'title') { $val = a1826_duplicateNameCat($val); }

			$db->setValue($key, $val);
		endforeach;

		$db->addGlobalCreateFields();
		$dbreturn = $db->insert();

		$lastID = $db->getLastId();
	endif;
	
endif;


//Formular oder Liste ausgeben
if ($func == "update" || $func == "insert" || $form_error == 1):
	//gespeicherte Daten aus DB holen
	if (($mode == "update" || $func == "update") && $id > 0):
		$db = rex_sql::factory();
		$db->setQuery("SELECT * FROM ".rex::getTable($mainTable)." WHERE id = '".$id."' LIMIT 0,1"); 
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
		
		//Values aufbereiten
	endif;


	//Std.vorgaben der Felder setzen
	if (!isset($dbe) || (is_array($dbe) && count($dbe) <= 0)):
		$db = rex_sql::factory();
		$db->setQuery("SELECT * FROM ".rex::getTable($mainTable)." LIMIT 0,1");
			foreach ($db->getFieldnames() as $fn) { $dbe[0][$fn] = ''; }
	endif;
	//$dbe[0] = array_map('htmlspecialchars', $dbe[0]);
	
	//Insert-Vorgaben
	if ($mode == "insert" || $id <= 0):
	endif;
	
	if ($form_error):
		//Formular bei Fehleingaben wieder befüllen
		$dbe[0]['id'] = $id;
		
		$dbe[0]["title"] = 			rex_post('f_title');
        $dbe[0]["description"] = 	rex_post('f_description');

		$func = $mode;
	endif;
	
    	
	//Ausgabe: Formular (Update / Insert)
	?>

	<script type="text/javascript">jQuery(function() { jQuery('#f_title').focus(); });</script>
    
        
    <form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
        <!-- <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" /> -->
        <input type="hidden" name="func" value="save" />
        <input type="hidden" name="id" value="<?php echo $dbe[0]['id']; ?>" />
        <input type="hidden" name="mode" value="<?php echo $func; ?>" />
    
        <section class="rex-page-section">
            <div class="panel panel-edit">

                <header class="panel-heading">
                    <div class="panel-title"><?php echo $this->i18n('a1826_head_cat'); ?></div>
                </header>

                <div class="panel-body">

                    <dl class="rex-form-group form-group">
                        <dt><label for=""><?php echo $this->i18n('a1826_std_title'); ?> *</label></dt>
                        <dd>
                            <input type="text" size="25" name="f_title" id="f_title" value="<?php echo arttmpl_helper::maskChar($dbe[0]['title']); ?>" maxlength="100" class="form-control" required />
                        </dd>
                    </dl>
                    
                    
                    <dl class="rex-form-group form-group">
                        <dt><label for=""><?php echo $this->i18n('a1826_std_description'); ?></label></dt>
                        <dd>
                          <textarea name="f_description" rows="4" id="f_description" class="form-control"><?php echo arttmpl_helper::maskChar($dbe[0]['description']); ?></textarea>
                        </dd>
                    </dl>                    

                </div>


				<footer class="panel-footer">
					<div class="rex-form-panel-footer">
						<div class="btn-toolbar">
							<input class="btn btn-save rex-form-aligned" type="submit" name="submit" title="<?php echo $this->i18n('a1826_save'); ?>" value="<?php echo $this->i18n('a1826_save'); ?>" />
							<?php if ($func == "update"): ?>
								<input class="btn btn-save" type="submit" name="submit-apply" title="<?php echo $this->i18n('a1826_apply'); ?>" value="<?php echo $this->i18n('a1826_apply'); ?>" />
							<?php endif; ?>
							<input class="btn btn-abort" type="submit" name="submit-abort" title="<?php echo $this->i18n('a1826_abort'); ?>" value="<?php echo $this->i18n('a1826_abort'); ?>" />
						</div>
					</div>
				</footer>

            </div>
        </section>
    
    </form>


<?php
else:
	//Übersichtsliste laden + ausgeben
	// --> wird per AJAX nachgeladen !!!
	
	$addpath = "index.php?page=".$page;
	?>
    

    <section class="rex-page-section">
        <div class="panel panel-default">
        
            <header class="panel-heading">
                <div class="panel-title"><?php echo $this->i18n('a1826_overview').' '.$this->i18n('a1826_cat'); ?></div>
            </header>  
              
			<script type="text/javascript">
            jQuery(function() {
                //Ausblenden - Elemente
                jQuery('.search_options').hide();
                
                //Formfeld fokussieren
                jQuery('#s_sbeg').focus();
            
                //Liste - Filtern
                var params = 'page=<?php echo $page; ?>&subpage=load-catlist&sbeg=';
                var dst = '#ajax_jlist';
                
                jQuery('#db-order').click(function() {
                    var btn = jQuery(this);
                    btn.toggleClass('db-order-desc');
                        if (btn.hasClass('db-order-desc')) { btn.attr('data-order', 'desc'); } else { btn.attr('data-order', 'asc'); }
                    loadAJAX(params + getSearchParams(), dst, 0);
                });
                
                jQuery('#s_sbeg').keyup(function() { loadAJAX(params + getSearchParams(), dst, 0); });
                
                jQuery('#s_button').click(function() { loadAJAX(params + getSearchParams(), dst, 0); });
                jQuery('#s_resetsbeg').click(function() { jQuery('#s_cat').val(0); jQuery('#s_sbeg').val("");
                                                          loadAJAX(params, dst, 0);	});
                                                                
                jQuery(document).on('click', 'span.ajaxNav', function(){
                    var navsite = jQuery(this).attr('data-navsite');
                    loadAJAX(params + getSearchParams(), dst, navsite);
                    jQuery("body, html").delay(150).animate({scrollTop:0}, 750, 'swing');
                });
                
                function getSearchParams()
                {	var searchparams = tmp = '';
                    searchparams += encodeURIComponent(jQuery('#s_sbeg').val());								//Suchbegriff (param-Name wird in "var params" gesetzt)
                    searchparams += '&order=' + encodeURIComponent(jQuery('#db-order').attr('data-order'));		//Sortierrichtung asc|desc
                    return searchparams;
                }
            });
            </script>

			<!-- Suchbox -->
			<table class="table table-striped addon_search" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td class="td1" valign="middle">&nbsp;</td>
						<td class="td2"><img src="/assets/addons/<?php echo $mypage; ?>/indicator.gif" width="16" height="16" border="0" id="ajax_loading" style="display:none;" /></td>
						<td class="td3">

							<div class="input-group sbeg">
								<input class="form-control" type="text" name="s_sbeg" id="s_sbeg" maxlength="50" value="<?php echo aknews_helper::maskChar($_SESSION['as_sbeg_aknews_cat']); ?>" placeholder="<?php echo $this->i18n('a1826_search_keyword'); ?>">
								<span class="input-group-btn">
									<a class="btn btn-popup form-control-btn" title="<?php echo $this->i18n('a1826_search_reset'); ?>" id="s_resetsbeg"><i class="rex-icon fa-close"></i></a>
								</span>
							</div>
							<input name="submit" type="button" value="<?php echo $this->i18n('a1826_search_submit'); ?>" class="button" id="s_button" style="display:none" />

						</td>
					</tr>
				</tbody>
			</table>


			<!-- Liste -->
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th class="rex-table-icon"><a href="<?php echo $addpath; ?>&func=insert" accesskey="a" title="<?php echo $this->i18n('a1826_new'); ?> [a]"><i class="rex-icon rex-icon-add-template"></i></a></th>
						<th class="rex-table-id">ID</th>
						<th><?php echo $this->i18n('a1826_bas_list_name'); ?> <a class="db-order" id="db-order" data-order="asc"><i class="rex-icon fa-sort"></i></a></th>
						<th class="rex-table-action" colspan="3"><?php echo $this->i18n('a1826_statusfunc'); ?></th>
					</tr>
				</thead>

				<tbody id="ajax_jlist">
					<script type="text/javascript">jQuery(function() { jQuery('#s_button').trigger('click'); });</script>
				</tbody>
			</table>
            

		</div>
	</section>

<?php
endif;
?>