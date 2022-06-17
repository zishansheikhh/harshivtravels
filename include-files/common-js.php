<script src="/js/jquery.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script src="/js/common.js?v=<?=_Version?>"></script>
<script src="/js/custom.js?v=<?=_Version?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/js/owl.carousel.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    $('.sidenav').sidenav();

    $(".dropdown-trigger").dropdown({
        coverTrigger: false,
        hover: true,
    });
        /*-----------------------------
    Sidebar menu item click event
    -----------------------------*/
    if($(".sidenav").length > 0)
    {
        $('.sidenav').sidenav();

        $(".sidenav a").on('click', function(e)
        {
            if($(this).parent().hasClass("has_sub"))
            {
                e.preventDefault();
            }
            if(!$(this).hasClass("subdrop"))
            {
                // hide any open menus and remove all other classes
                //$("ul",$(this).parents("ul:first")).slideUp(350);
                //$("a",$(this).parents("ul:first")).removeClass("subdrop");

                // open our new menu and add the open class
                $(this).next("ul").slideUp(300);
                $(this).addClass("subdrop");
            }
            else if($(this).hasClass("subdrop"))
            {
            $(this).removeClass("subdrop");

                if($(this).next("ul").css('display') !== 'none')
                {
                    $(this).next("ul").slideUp(300);
                }
                else
                {
                    $(this).next("ul").slideDown(300);
                }
            }
        });
    }

    if($('.scroll_top').length > 0)
    {
        $('.scroll_top').on('click', function(){
            $('html,body').animate({ scrollTop: 0 }, 500);
        });

        $(window).scroll(function()
        {
            var scroll = $(window).scrollTop();

            if (scroll > 300)
            {
                $('.scroll_top').removeClass('hide');
            }
            else
            {
               $('.scroll_top').addClass('hide');
            }
        });
    }
});
</script>
