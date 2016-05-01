<a class="mini-cart" href="{{home}}{{basket_link}}">
    <div>
        <i class="icon cart"></i>
    </div>
    <div>
        <span class="cartTotalPrice" style="font-weight: bold;color:#555">{{ (price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} <i>{{ system_flags.eshop.current_currency.sign }}</i></span> 
    </div>
</a>
<div class="ui message-add-to-cart popup bottom center transition hidden" style="top: 130px; right: 355px;">Добавлено в корзину</div>
