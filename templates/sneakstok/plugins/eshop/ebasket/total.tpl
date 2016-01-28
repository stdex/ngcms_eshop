<a href="{{home}}{{basket_link}}">
    <div class="btn-bask pointer">
        <button class="btnBask" type="button">
            <span class="frame-icon">
                <span class="helper"></span>
                <span class="icon_cleaner"></span>
            </span>
            <span class="text-cleaner">
                <span class="helper"></span>
                <span class="">
                    <span class="title text-el d_l">��� �������</span>
                    {% if (count > 0) %}
                    <span class="d_b" style="padding-top:3px;">
                        <span class="text-el small" id="basket_count">{{ count }}</span>
                        <span class="text-el">&nbsp;</span>
                        <span class="text-el">�����</span>
                        <span class="divider text-el">�</span>
                        <span class="d_i-b"><span class="text-el" id="basket_price">{{ (price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</span><span class="text-el" id="basket_price"> {{ system_flags.eshop.current_currency.sign }}</span></span>
                    </span>
                    {% else %}
                    <span class="d_b" style="padding-top:3px;">
                        <span class="text-el">�����</span>
                    </span>
                    {% endif %}
                </span>
            </span>
        </button>
    </div>
</a>
