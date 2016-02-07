{% if not (formEntry.error) %}
<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <h3 class="ui header" style="margin-top:10px;">Заказ №:{{ formEntry.id }}</h3>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>


<div class="entry-content">
<div class="ui form container two column stackable grid checkout woocommerce-checkout">

<div class="column">

        <div class="woocommerce-billing-fields">
    
        <h3 class="ui header">Детали оплаты</h3>

        <p class="form-row form-row form-row-first validate-required woocommerce-invalid woocommerce-invalid-required-field" id="billing_first_name_field"><label for="billing_first_name" class="">Имя </label>{{ formEntry.name }}</p>
    
        <p class="form-row form-row form-row-last woocommerce-validated" id="billing_last_name_field"><label for="billing_last_name" class="">Email</label>{{ formEntry.email }}</p><div class="clear"></div>
    
        <p class="form-row form-row form-row-first validate-required woocommerce-invalid woocommerce-invalid-required-field" id="billing_email_field"><label for="billing_email" class="">Телефон </label>{{ formEntry.phone }}</p>
    
        <p class="form-row form-row form-row-last validate-required woocommerce-invalid woocommerce-invalid-required-field" id="billing_phone_field"><label for="billing_phone" class="">Адрес доставки </label>{{ formEntry.address }}</p><div class="clear"></div>
        
<form method="get" action="{{ payment.link }}" target="_blank">
    <input type="hidden" value="{{ formEntry.id }}" name="order_id">
    <input type="hidden" value="{{ formEntry.uniqid }}" name="order_uniqid">
    <input type="hidden" value="{{ payment.systems[0].name }}" name="payment_id">
    <button type="submit" class="ui button black large">Оплатить</button>
</form>

        <div class="clear"></div>
    

    </div>

    
</div>
<div class="column">
        <div id="order_review" class="woocommerce-checkout-review-order">
        <table class="ui celled table shop_table woocommerce-checkout-review-order-table">
    <thead>
        <tr>
            <th class="product-name">Товары</th>
            <th class="product-total">Итого</th>
        </tr>
    </thead>
    <tbody>
        {% for entry in entries %}
            <tr class="cart_item">
                        <td class="product-name">
                            {{ entry.title }} <strong class="product-quantity">x {{ entry.count }} шт.</strong>                          
                        </td>
                        <td class="product-total">
                            <span class="amount">{{ (entry.price * entry.count * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span>                       </td>
                    </tr>
        {% endfor %}
    </tbody>
    <tfoot>

        
        <tr class="order-total">
            <th>Итого</th>
            <th><strong><span class="amount">{{ (total * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span></strong> </th>
        </tr>

        
    </tfoot>
</table>    </div>
</div>


</div>
</div>
{% else %}

<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <span><b>{% for err in formEntry.error %}
                        {{ err }}<br/>
                        {% endfor %}</b></span>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>

{% endif %}