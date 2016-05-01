{% if (recs > 0) %}
<form class="ui form" action="{{basket_link}}" method="post">
<h3 class="ui header" style="margin-top:10px;">Ваша корзина</h3>
<table class="ui table shop_table cart">
    <thead>
        <tr>
            <th class="product-remove">&nbsp;</th>
            <th class="product-thumbnail">&nbsp;</th>
            <th class="product-name"></th>
            <th class="product-price"></th>
            <th class="product-quantity">Количество</th>
            <th class="product-subtotal">Цена</th>
        </tr>
    </thead>
    <tbody>
                    {% for entry in entries %}
                    <tr class="cart_item">

                    <td class="product-remove" style="vertical-align:middle">
                        <a class="ui icon remove cart_quantity_delete" title="Удалить эту позицию" data-id="{{ entry.id }}" data-linked_ds="{{ entry.linked_ds }}" data-linked_id="{{ entry.linked_id }}">&times;</a>                 </td>

                    <td class="product-thumbnail">
                        <a href="{{entry.xfields.item.view_link}}"><img width="180" height="180" src="{% if (entry.xfields.item.image_filepath) %}{{home}}/uploads/eshop/products/{{entry.xfields.item.id}}/thumb/{{entry.xfields.item.image_filepath}}{% else %}{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg{% endif %}" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" /></a>                 </td>

                    <td class="product-name">
                        <a href="{{entry.xfields.item.view_link}}">{{ entry.title }} </a>
                    </td>

                    <td class="product-quantity">
                        <div class="input-group quantity-control" unselectable="on" style="-webkit-user-select: none;">
                        <a rel="nofollow" class="input-group-addon cart_quantity_down" title="Уменьшить">-</a>
                        </div>
                        <div class="quantity"><input type="text" name="count_{{ entry.id }}" value="{{ entry.count }}" data-id="{{ entry.id }}" data-linked_ds="{{ entry.linked_ds }}" data-linked_id="{{ entry.linked_id }}" data-price="{{ (entry.price) }}" title="Кол-во" class="input-text qty text cart_quantity_input" size="2" /></div>
                        <div class="input-group quantity-control" unselectable="on" style="-webkit-user-select: none;">
                        <a rel="nofollow" class="input-group-addon cart_quantity_up" title="Добавить">+</a>
                        </div>
                    </td>

                    <td class="product-subtotal">
                        <span class="amount">{{ (entry.price * entry.count * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</span> {{ system_flags.eshop.current_currency.sign }}</td>
                </tr>
                {% endfor %}
                
            </tbody>
</table>
<div class="ui two column grid">
    <div class="column actions">

{% if (formEntry.error) %}
{% for error in formEntry.error %}<p>{{ error }}</p>{% endfor %}
{% endif %}
<p class="form-row form-row form-row-first validate-required" id="billing_first_name_field"><label for="billing_first_name" class="">Имя <abbr class="required" title="обязательно">*</abbr></label><input type="text" class="input-text " name="userInfo[fullName]" placeholder="" value=""></p>
<p class="form-row form-row form-row-first validate-required" id="billing_first_name_field"><label for="billing_first_name" class="">Email <abbr class="required" title="обязательно">*</abbr></label><input type="text" class="input-text " name="userInfo[email]" placeholder="" value=""></p>
<p class="form-row form-row form-row-first validate-required" id="billing_first_name_field"><label for="billing_first_name" class="">Телефон <abbr class="required" title="обязательно">*</abbr></label><input type="text" class="input-text " name="userInfo[phone]" placeholder="" value=""></p>
<p class="form-row form-row form-row-first validate-required" id="billing_first_name_field"><label for="billing_first_name" class="">Адрес доставки <abbr class="required" title="обязательно">*</abbr></label><input type="text" class="input-text " name="userInfo[deliverTo]" placeholder="" value=""></p>

</div>
        <div class="column cart-collaterals">

        <h3 class="totals-header">ИТОГ: <span id="total_cart"><strong><span class="total_amount">{{ (total * system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</span> <span>{{ system_flags.eshop.current_currency.sign }}</span></strong> </span></h3>
<input type="submit" class="ui button black large" value="ОФОРМИТЬ ЗАКАЗ" />

    </div>
</div>
</form>
{% else %}
Ваша корзина пуста!
{% endif %}


<style>

.quantity-control .cart_quantity_up, .quantity-control .cart_quantity_down {
    text-decoration: none;
    background: none repeat scroll 0 0 #f5f5f5;
    border: 0 none;
    border-radius: 0 !important;
    cursor: pointer;
    font-size: 15px;
    height: 30px !important;
    line-height: 43px;
    overflow: hidden;
    padding: 5px 10px;
    text-align: center;
    vertical-align: top;
    width: 35px !important;
    color: #A6A6A6;

}

.quantity-control {
        display: inline-block;
}


.quantity-control .cart_quantity_up:hover {
    background: none repeat scroll 0 0 #52A537;
    color: #FFFFFF;
}

.quantity-control .cart_quantity_down:hover {
    background: none repeat scroll 0 0 #E44545;
    color: #FFFFFF;
}

.input-group .quantity-control {
    display: table-cell;
}

</style>

<script>
$(document).ready(function(){

    function update_updown_total(click_this, count) {
        var price = parseFloat(click_this.attr("data-price").trim());
        
        if(isNaN(parseInt(count)) || parseInt(count) <= 0) {
            count = 1;
        }

        var id = click_this.attr("data-id");
        var linked_ds = click_this.attr("data-linked_ds");
        var linked_id = click_this.attr("data-linked_id");
        
        rpcEshopRequest('eshop_ebasket_manage', {'action': 'update_count',  'id':id, 'linked_ds':linked_ds, 'linked_id':linked_id,'count':count }, function (resTX) {
            click_this.val(count);
            
            var total = parseFloat(count * price).toFixed(2);
            click_this.parent().parent().parent().find("td[class='product-subtotal']").find("span[class='amount']").text(total);

            var sum = 0;
            $("td[class='product-subtotal'").each(function() {
                sum = sum + parseFloat($(this).find("span[class='amount']").text());
            });
            $(".total_amount").text(sum.toFixed(2));
        });
        
        rpcEshopRequest('eshop_ebasket_manage', {'action': 'update' }, function (resTX) {
            document.getElementById('tinyBask').innerHTML = resTX['update'];
        });

    }
    
    $('.cart_quantity_up').on('click', function(e){
        var click_this = $(this).parent().parent().find('input');
        var count = parseInt(click_this.val()) + 1;
        update_updown_total(click_this, count);
    });
    
    $('.cart_quantity_down').on('click', function(e){
        var click_this = $(this).parent().parent().find('input');
        var count = parseInt(click_this.val()) - 1;
        update_updown_total(click_this, count);
    });

    $(document).on('change', '.cart_quantity_input',  function(e) {
        var count = parseInt($(this).val());
        update_updown_total($(this), count);
    });
    
    $('.cart_quantity_delete').on('click', function(e){
        var id = $(this).attr("data-id");
        var linked_ds = $(this).attr("data-linked_ds");
        var linked_id = $(this).attr("data-linked_id");
        
        rpcEshopRequest('eshop_ebasket_manage', {'action': 'delete', 'id':id, 'linked_ds':linked_ds, 'linked_id':linked_id }, function (resTX) {
            location.reload();
        });

    });
    

});
</script>
