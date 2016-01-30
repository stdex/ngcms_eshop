{{ prev_link }}
{{pages}}
{{ next_link }}

<script>
$(document).ready(function(){
    $("#mainPagesPreview a").on('click', function(e) {
        
        var page = $(this).data('page');
        
        if(page == 'next') {
            $("#mainPagesPreview a").each(function() {
                   if($(this).hasClass("active")) {
                    page = $(this).data('page')+1;
                   }
            });
        }
        else if (page == 'prev') {
            $("#mainPagesPreview a").each(function() {
                   if($(this).hasClass("active")) {
                    page = $(this).data('page')-1;
                   }
            });
        }
        else if (page == '') {
            return false;
        }
        
        rpcEshopRequest('eshop_amain', {'action': 'show', 'number':8, 'mode':'last', 'page':page }, function (resTX) {
            if ((resTX['data']['prd_main']>0)&&(resTX['data']['prd_main'] < 100)) {
                $("div#mainProductsPreview").html(""+resTX['data']['prd_main_text']+"");
                $("div#mainPagesPreview").html(""+resTX['data']['prd_main_pages_text']+"");
            } else {
                $("div#mainProductsPreview").html(""+resTX['data']['prd_main_text']+"");
                $("div#mainPagesPreview").html(""+resTX['data']['prd_main_pages_text']+"");
            }
        });

    });
    
});
</script>