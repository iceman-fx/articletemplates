<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: Einstellungen (config)
	v1.0
	by Falko Müller @ 2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */
/** @var array $config */
/** @var string $func */
/** @var string $page */
/** @var string $subpage */


//Variablen deklarieren
$form_error = 0;


//Formular dieser Seite verarbeiten
if ($func == "save" && isset($_POST['submit'])):

	//Modulauswahl aufbereiten
	$mods = rex_post('modules');
	$mods = (is_array($mods)) ? implode("#", rex_post('modules')) : '';

	//Konfig speichern
	$res = $this->setConfig('config', [
		'use_search'				=> rex_post('use_search'),
        'use_artname'				=> rex_post('use_artname'),
        'use_sametemplates'			=> rex_post('use_sametemplates'),
		'displaymode'				=> rex_post('displaymode'),
        'imageorientation'			=> rex_post('imageorientation'),
	]);

	//Rückmeldung
	echo ($res) ? rex_view::info($this->i18n('a1826_settings_saved')) : rex_view::warning($this->i18n('a1826_error'));

	//reload Konfig
	$config = $this->getConfig('config');
endif;


//Formular ausgeben
?>


<script>setTimeout(function() { jQuery('.alert-info').fadeOut(); }, 5000);</script>


<form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
<input type="hidden" name="func" value="save" />

<section class="rex-page-section">
	<div class="panel panel-edit">
	
		<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1826_head_config'); ?></div></header>
		
		<div class="panel-body">
        
			<dl class="rex-form-group form-group">
				<dt><label for=""><?php echo $this->i18n('a1826_config_displaymode'); ?></label></dt>
				<dd>
                    <div class="radio toggle switch">
                        <label for="mode1">
                            <input name="displaymode" type="radio" id="mode1" value="boxed" <?php echo (@$config['displaymode'] != 'list') ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1826_config_displaymode_boxed'); ?>
                        </label>
                        
                        <label for="mode2">
                            <input name="displaymode" type="radio" id="mode2" value="list" <?php echo (@$config['displaymode'] == 'list') ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1826_config_displaymode_list'); ?>
                        </label>
                    </div>
                </dd>
            </dl>        
                    
            
            <dl class="spacerline"></dl>


			<dl class="rex-form-group form-group">
				<dt><label for=""><?php echo $this->i18n('a1826_config_imageorientation'); ?></label></dt>
				<dd>
                    <div class="radio toggle switch">
                        <label for="img1">
                            <input name="imageorientation" type="radio" id="img1" value="ls" <?php echo (@$config['imageorientation'] != 'pt') ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1826_config_imageorientation_ls'); ?>
                        </label>
                        
                        <label for="img2">
                            <input name="imageorientation" type="radio" id="img2" value="pt" <?php echo (@$config['imageorientation'] == 'pt') ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1826_config_imageorientation_pt'); ?>
                        </label>
                    </div>
                </dd>
            </dl>    
            

            <dl class="spacerline"></dl>

            
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1826_config_use_artname'); ?></label></dt>
                <dd>
                    <div class="checkbox toggle">
						<label for="use_artname">
                        	<input type="checkbox" name="use_artname" id="use_artname" value="checked" <?php echo @$config['use_artname']; ?> /> <?php echo $this->i18n('a1826_config_use_artname_info'); ?>
						</label>
                    </div>
                </dd>
            </dl>
             
            
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1826_config_use_search'); ?></label></dt>
                <dd>
                    <div class="checkbox toggle">
						<label for="use_search">
                        	<input type="checkbox" name="use_search" id="use_search" value="checked" <?php echo @$config['use_search']; ?> /> <?php echo $this->i18n('a1826_config_use_search_info'); ?>
						</label>
                    </div>
                </dd>
            </dl>
             
            
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1826_config_use_sametemplates'); ?></label></dt>
                <dd>
                    <div class="checkbox toggle">
						<label for="use_sametemplates">
                        	<input type="checkbox" name="use_sametemplates" id="use_sametemplates" value="checked" <?php echo @$config['use_sametemplates']; ?> /> <?php echo $this->i18n('a1826_config_use_sametemplates_info'); ?>
						</label>
                    </div>
                </dd>
            </dl>

		</div>
                
		
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<input class="btn btn-save rex-form-aligned" type="submit" name="submit" title="<?php echo $this->i18n('a1826_save'); ?>" value="<?php echo $this->i18n('a1826_save'); ?>" />
				</div>
			</div>
		</footer>
		
	</div>
</section>
	
</form>