<?php
/*
	Redaxo-Addon Articletemplates
	Verwaltung: Hilfe
	v1.0
	by Falko Müller @ 2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */
/** @var array $config */
/** @var string $func */
/** @var string $page */
/** @var string $subpage */


//Vorgaben
?>

<style>
.faq { margin: 0px !important; cursor: pointer; }
.faq + div { margin: 0px 0px 15px; }
</style>

<section class="rex-page-section">
<div class="panel panel-default">

<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1826_head_help'); ?></div></header>

<div class="panel-body">
    <div class="rex-docs">
        <div class="rex-docs-sidebar">
            <nav class="rex-nav-toc">
                <ul>
                    <li><a href="#start">Allgemein</a>					
                    <li><a href="#faq">FAQ</a>
                    	</ul>
            </nav>
        </div>

                
<div class="rex-docs-content">
<h1>Addon: <?php echo $this->i18n('a1826_title'); ?></h1>


<!-- Alkgemein -->
<a name="start"></a>

<p>Mit dieser Erweiterung können vordefinierter Artikelvorlagen (CMS-Artikel mit angelegten Modulblöcken) in leere Artikel eingefügt werden.</p>
<p>Die Vorlagenartikel werden dabei als normale CMS-Artikel mit den gewünschten Modulblöcken und -inhalten angelegt und anschließend als Inhaltsvorlagen bei leeren Artikeln zur Auswahl bereitgestellt.</p>
<p>Über die optionalen Kategorien kann die Auswahl der Vorlagen entsprechend gruppiert werden.</p>



<p>&nbsp;</p>

<!-- Modul -->
<a name="faq"></a>
<h2>FAQ:</h2>

<p class="faq text-danger" data-toggle="collapse" data-target="#f001"><span class="caret"></span> Verwendung von CTypes</p>
<div id="f001" class="collapse">CTypes können ganz normal genutzt werden. Die in der Vorlage definierten Inhalte des jeweiligen CTypes werden 1:1 in den neuen Artikel kopiert, auch wenn  diese nur angezeigt werden, sofern die gleiche Anzahl an CTypes im entsprechenden Template des neuen Artikels vorhanden ist.</div>

<p class="faq text-danger" data-toggle="collapse" data-target="#f002"><span class="caret"></span> Arbeitsversion &amp; Liveversion</p>
<div id="f002" class="collapse">Das  Kopieren der Inhalte der Vorlage in den neuen Artikel erfolgt über eine Redaxo-Systemfunktion. Dabei werden die Inhalte der jeweiligen Arbeits- und Liveversion des Vorlagenartikels entsprechend berücksichtig und 1:1 mitkopiert.</div>





<p>&nbsp;</p>
<!-- Fragen / Probleme -->
<h3>Fragen, Wünsche, Probleme?</h3>
Du hast einen Fehler gefunden oder ein nettes Feature parat?<br>
Lege ein Issue unter <a href="<?php echo $this->getProperty('supportpage'); ?>" target="_blank"><?php echo $this->getProperty('supportpage'); ?></a> an. 


</div>
</div>

</div>
</div>
</section>