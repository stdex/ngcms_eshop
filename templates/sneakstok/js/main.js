
    $(document).ready(function(){
        $('.dropdown').dropdown();
        
        $('#searchShow').click(function(){
            $(this).hide();
            $("header").find('.search').fadeIn(200)
        });
        $('.search').find('.remove').click(function(){
            $(this).closest('.search').hide();
            $('#searchShow').fadeIn(200);
        });


        $('.dropdownItem').click(function(){

            $('.dropdownMenu').html('').hide();
            
            $('#mainMenu').find('.angle').removeClass('up').addClass('down');

            if($(this).hasClass('active')){
                $(this).removeClass('active');
                 
            }else{
                $('#mainMenu').find('a').removeClass('active');
                
                $(this).addClass('active');
                
                $(this).find('.angle').removeClass('down').addClass('up');

                var submenu = $(this).next('.submenu').html();
                $('.dropdownMenu').html(submenu).fadeIn(300);
            }
            
            return false;
        });
        
    });
    