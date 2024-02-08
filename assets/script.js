// articletemplates
// v1.0

$(function(){    
    let $at_btn, $at_dst, $at_dst_inner, $at_dst_hdr, $at_dst_cnt, $at_dst_olay, $at_url = '';

    $(document).on('rex:ready', function(){ initArtTmpl(); });
    initArtTmpl();


    function initArtTmpl()
    {   //console.log("initArtTmpl");

        $at_btn = $('#arttmpl-button');
        $at_dst = $('#arttmpl-selector');
            $at_dst_inner = $at_dst.find('.arttmpl-inner');
            $at_dst_hdr = $at_dst.find('.arttmpl-header');
            $at_dst_cnt = $at_dst.find('.arttmpl-content');
            $at_dst_olay = $at_dst.find('.arttmpl-overlay');
        $at_url = '';

        hideArtTmplSelector();        
        $at_btn.on('click', function(e){
            e.preventDefault();

            //console.log("btn clicked");

            btn = $(this).find('button'); btn.blur();      
                aid = parseInt(btn.data('artid'));
                clid = parseInt(btn.data('clid'));
                ctype = parseInt(btn.data('ctype'));
            $at_url = 'index.php?page=structure&rex-api-call=arttmpl_loadTemplates&aid='+aid+'&clid='+clid+'&ctype='+ctype;

            showArtTmplSelector(aid, clid, ctype);
        });
        $at_dst.find('.arttmpl-overlay, .arttmpl-close').click(function() { hideArtTmplSelector(); });


        //max Höhe des Contentbereich setzen
        resizeArtTmpl();
        $(window).on('load resize', function(e){ resizeArtTmpl(); });


        //Suche anbinden    
        $at_dst_hdr.find('input').keyup(function(){
            sbeg = encodeURIComponent($(this).val());
            $.post($at_url+'&sbeg='+sbeg, function(data){ $at_dst_cnt.html(data); });
        });
    }    
 
    function resizeArtTmpl()
    {   //Auswahlfenster anpassen
        tmp = $at_dst_hdr.outerHeight() + 'px';
            //workaround für Höhe von unsichtbaren Elementen
            if (!$('body').hasClass('arttmpl-active')) {
                $at_dst.css({ 'display': 'block', 'visibility': 'hidden', 'position': 'absolute' });
                $at_dst_inner.css({ 'display': 'block' });
                    tmp = $at_dst_hdr.outerHeight() + 'px';
                $at_dst_inner.css({ 'display': 'none' });
                $at_dst.css({ 'display': 'none', 'visibility': 'visible', 'position': 'fixed' });
            }
        
        pd = parseInt($at_dst.css('padding-top')) + parseInt($at_dst.css('padding-bottom'));
        $at_dst_cnt.css({ 'max-height': 'calc(100vh - '+pd+'px - '+tmp+')' });
    }
    
    function showArtTmplSelector(aid=0, clid=0, ctype=0)
    {   //Vorlagen zur Auswahl einladen
        if ($at_dst_cnt) {
            $.ajax({
                //url: 'index.php?page=structure&rex-api-call=arttmpl_loadTemplates&aid='+aid+'&clid='+clid+'&ctype='+ctype
                url: $at_url
            }).done(function(data) {
                //Rückhabe in DOM einbetten
                $at_dst_cnt.html(data);
                
                //Auswahlpopup einblenden
                $('body').addClass('arttmpl-active');
                $at_dst.show();
                $at_dst_olay.fadeIn();
                setTimeout(function(){ $at_dst_inner.fadeIn(function(){ $at_dst_hdr.find('input').focus(); } ); }, 100);
                
            }).always(function() {
                $('#rex-js-ajax-loader').removeClass('rex-visible');
            }).fail(function() {})
        }
    }
    
    function hideArtTmplSelector()
    {   //Auswahlpopup ausblenden und leeren    
        $at_dst_inner.fadeOut();
        setTimeout(function(){ $at_dst_olay.fadeOut(function(){
            $at_dst.hide();
            $('body').removeClass('arttmpl-active');
            $at_dst.find('.inner').html('');
        }); }, 100);
    }
    
});