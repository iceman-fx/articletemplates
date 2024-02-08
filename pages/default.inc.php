<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: Hauptseite (Default)
	v1.0
	by Falko Müller @ 2024
*/

//Vorgaben
$mainTable = '1826_articletemplates';				//primäre SQL-Tabelle dieses Bereiches


//Paramater
$mode = rex_request('mode');
$id = intval(rex_request('id'));
$form_error = $formvalue_error = 0;


$clang_id = rex_be_controller::getCurrentPagePart(3);													//2. Unterebene = dritter Teil des page-Parameters
	$clang_id = (!empty($clang_id)) ? intval(preg_replace("/.*-([0-9])$/i", "$1", $clang_id)) : 0;		//Auslesen der ClangID
	$clang_id = ($clang_id <= 0) ? 1 : $clang_id;


$_SESSION['as_sbeg_arttmpl'] = 	(!isset($_SESSION['as_sbeg_arttmpl'])) 	? "" : $_SESSION['as_sbeg_arttmpl'];
$_SESSION['as_cat_arttmpl'] = 	(!isset($_SESSION['as_cat_arttmpl'])) 	? "" : $_SESSION['as_cat_arttmpl'];


//Formular dieser Seite verarbeiten
if ($func == "save" && (isset($_POST['submit']) || isset($_POST['submit-apply'])) ):
	//Pflichtfelder prüfen
	$fields = array("f_article_id");
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

        $db->setValue("id_cat",             rex_post('f_id_cat'));

		$db->setValue("title", 			    rex_post('f_title'));
		$db->setValue("description", 		rex_post('f_description'));
		$db->setValue("media", 	            rex_post('f_media'));
		
		$db->setValue("article_id", 	    rex_post('f_article_id'));
		$db->setValue("clang_id", 	        $clang_id); 


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
    /*
	$db = rex_sql::factory();
	$db->setQuery("SELECT id FROM ".rex::getTable($mainTable)." WHERE id_andereTabelle = '".$id."'"); 

	if ($db->getRows() <= 0):
    */
		//löschen
		$db = rex_sql::factory();
		$db->setTable(rex::getTable($mainTable));		
		$db->setWhere("id = '".$id."'");
			
		echo ($db->delete()) ? rex_view::info($this->i18n('a1826_entry_deleted')) : rex_view::warning($this->i18n('a1826_error_deleted'));
	/*
    else:
		//nicht löschen aufgrund gültiger Zuweisung
		echo rex_view::warning($this->i18n('a1826_entry_used'));
	endif;
    */
	
elseif ($func == "duplicate" && $id > 0):
	//Eintrag duplizieren
	$db = rex_sql::factory();
	$db->setQuery("SELECT * FROM ".rex::getTable($mainTable)." WHERE id = '".$id."'"); 
	
	if ($db->getRows() > 0):
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
		$db = rex_sql::factory();
		$db->setTable(rex::getTable($mainTable));
		
		foreach ($dbe[0] as $key=>$val):			
			if ($key == 'id') { continue; }
			if ($key == 'title') { $val = a1826_duplicateName($val); }
			
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
    
	
	//Formular bei Fehleingaben wieder befüllen
	if ($form_error):
		//Formular bei Fehleingaben wieder befüllen
		$dbe[0]['id'] = $id;
		
		$dbe[0]["id_cat"] = 		rex_post('f_id_cat');

		$dbe[0]["title"] = 			rex_post('f_title');
		$dbe[0]["description"] = 	rex_post('f_description');
		$dbe[0]["media"] = 	        rex_post('f_media');
        
        $dbe[0]["article_id"] = 	rex_post('f_article_id');

		$func = $mode;
	endif;
    
	
	//Werte aufbereiten

    
	//Ausgabe: Formular (Update / Insert)
	?>

	<script type="text/javascript">jQuery(function() { jQuery('#f_title').focus(); });</script>
    
    <style type="text/css"></style>
    
    <form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" /> -->
    <input type="hidden" name="func" value="save" />
    <input type="hidden" name="id" value="<?php echo $dbe[0]['id']; ?>" />
	<input type="hidden" name="mode" value="<?php echo $func; ?>" />
    
    <section class="rex-page-section">
        <div class="panel panel-edit">
        
            <header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1826_head_basics'); ?></div></header>
            
            <div class="panel-body">
            
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1826_bas_article_id'); ?> *</label></dt>
                    <dd>
                        <?php
                        $elem = new rex_form_widget_linkmap_element();
                            $elem->setAttribute('class', 'form-control');
                            $elem->setAttribute('name', 'f_article_id');
                            $elem->setValue($dbe[0]['article_id']);
                        echo $elem->formatElement();
                        ?>							
                    </dd>
                </dl>

    
                <dl class="spacerline"></dl>
                
                
                <legend><?php echo $this->i18n('a1826_subheader_bas1'); ?></legend>   


                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1826_std_cat'); ?></label></dt>
                    <dd>
                        <?php
                        $sel = new rex_select();
                            $sel->setName('f_id_cat');
                            $sel->setId('f_id_cat');
                            $sel->setAttribute('class', 'form-control selectpicker');
                            $sel->setAttribute('data-live-search', 'true');
                            $sel->setSize(1);
                            $sel->setSelected($dbe[0]['id_cat']);
                            $sel->addOption($this->i18n('a1826_bas_cat_empty'), "0");
                            $sel->addDBSqlOptions("SELECT title, id FROM ".rex::getTable('1826_articletemplates_cat')." ORDER BY title ASC, id ASC");
                        echo $sel->get();
                        ?>
                    </dd>
                </dl>
                
                
                <dl class="spacerline"></dl>


                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1826_std_title'); ?></label></dt>
                    <dd>
                        <input type="text" size="25" name="f_title" id="f_title" value="<?php echo arttmpl_helper::maskChar($dbe[0]['title']); ?>" maxlength="100" class="form-control" />
                    </dd>
                </dl>
                
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1826_std_description'); ?></label></dt>
                    <dd>
                      <textarea name="f_description" rows="4" id="f_description" class="form-control"><?php echo arttmpl_helper::maskChar($dbe[0]['description']); ?></textarea>
                    </dd>
                </dl>
    
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1826_std_media'); ?></label></dt>
                    <dd>
                        <?php
                        $elem = new rex_form_widget_media_element();
                            $elem->setAttribute('class', 'form-control');
                            $elem->setAttribute('name', 'f_media');
                            $elem->setTypes('gif,jpg,jpeg,png,webp');
                            $elem->setValue($dbe[0]['media']);
                        echo $elem->formatElement();
                        ?>	
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
        
            <header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1826_overview').' '.$this->i18n('a1826_default'); ?></div></header>  
              
			<script type="text/javascript">
            jQuery(function() {
                //Ausblenden - Elemente
                jQuery('.search_options').hide();
                
                //Formfeld fokussieren
                jQuery('#s_sbeg').focus();
            
                //Liste - Filtern
                var params = 'page=<?php echo $page; ?>&subpage=load-defaultlist&sbeg=';
                var dst = '#ajax_jlist';
                
                jQuery('#db-order').click(function() {
                    var btn = jQuery(this);
                    btn.toggleClass('db-order-desc');
                        if (btn.hasClass('db-order-desc')) { btn.attr('data-order', 'desc'); } else { btn.attr('data-order', 'asc'); }
                    loadAJAX(params + getSearchParams(), dst, 0);
                });
                
                jQuery('#s_cat').change(function() {	loadAJAX(params + getSearchParams(), dst, 0); });
                jQuery('#s_sbeg').keyup(function() {	loadAJAX(params + getSearchParams(), dst, 0); });
                
                jQuery('#s_button').click(function() { loadAJAX(params + getSearchParams(), dst, 0);	});
                jQuery('#s_resetsbeg').click(function() { jQuery('#s_gid').prop("checked", false); jQuery('#s_cat').val(0); jQuery('#s_sbeg').val("");
                                                          loadAJAX(params, dst, 0);	});
                                                                
                jQuery(document).on('click', 'span.ajaxNav', function(){
                    var navsite = jQuery(this).attr('data-navsite');
                    loadAJAX(params + getSearchParams(), dst, navsite);
                    jQuery("body, html").delay(150).animate({scrollTop:0}, 750, 'swing');
                });
                
                function getSearchParams()
                {	var searchparams = tmp = '';
                    searchparams += encodeURIComponent(jQuery('#s_sbeg').val());								//Suchbegriff (param-Name wird in "var params" gesetzt)
                    searchparams += '&s_cat=' + encodeURIComponent(jQuery('#s_cat').val());
                    searchparams += '&order=' + encodeURIComponent(jQuery('#db-order').attr('data-order'));		//Sortierrichtung asc|desc
                    return searchparams;
                }
            });
            </script>

			<!-- Suchbox -->
			<table class="table table-striped addon_search" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td class="td1" valign="middle">
							<?php echo $this->i18n('a1826_search_cat'); ?>:
                            
                            <select name="s_cat" size="1" id="s_cat" class="form-control rightmargin">
                                <option value="0" selected="selected">- <?php echo $this->i18n('a1826_search_all'); ?> -</option>
								<option value="">&nbsp;</option>

								<?php
								$db = rex_sql::factory();
								$db->setQuery("SELECT title, id FROM ".rex::getTable('1826_articletemplates_cat')." ORDER BY title ASC, id ASC");
								
								if ($db->getRows() > 0):
									for ($i=0; $i < $db->getRows(); $i++):
										$id = $db->getValue('id', 'int');
										$title = arttmpl_helper::maskChar(arttmpl_helper::textOnly($db->getValue('title'), true));
										
										$sel = ($id == $_SESSION['as_cat_arttmpl']) ? 'selected="selected"' : '';
										echo '<option value="'.$id.'" '.$sel.'>'.$title.'</option>';
									
										$db->next();
                					endfor;
								endif;
								?>
                            </select>
						</td>
						<td class="td2"><img src="/assets/addons/<?php echo $mypage; ?>/indicator.gif" width="16" height="16" border="0" id="ajax_loading" style="display:none;" /></td>
						<td class="td3">

							<div class="input-group sbeg">
								<input class="form-control" type="text" name="s_sbeg" id="s_sbeg" maxlength="50" value="<?php echo arttmpl_helper::maskChar($_SESSION['as_sbeg_arttmpl']); ?>" placeholder="<?php echo $this->i18n('a1826_search_keyword'); ?>">
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
						<th><?php echo $this->i18n('a1826_bas_list_name'); ?> <a class="db-order db-order-desc" id="db-order" data-order="desc"><i class="rex-icon fa-sort"></i></a></th>
						<th><?php echo $this->i18n('a1826_bas_list_artid'); ?></th>
						<th><?php echo $this->i18n('a1826_bas_list_media'); ?></th>
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