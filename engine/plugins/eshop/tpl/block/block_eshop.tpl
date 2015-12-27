
{% for entry in entries %}

<li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-{{ loop.index }} jcarousel-item-{{ loop.index }}-horizontal" data-pos="top" jcarouselindex="{{ loop.index }}" style="float: left; list-style: none; width: 209px;">
  <!-- Start. Photo & Name product -->
  <a href="{{entry.view_link}}" class="frame-photo-title">
    <span class="photo-block">
      <span class="helper">
      </span>
      {% if (entry.image_filepath) %}<img src='{{home}}/uploads/eshop/products/thumb/{{entry.image_filepath}}' class="vImg">{% else %}<img src='{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg' class="vImg">{% endif %}
      {% if (mode == 'stocked') %}<span class="product-status hit"></span>{% endif %}
      {% if (mode == 'last') %}<span class="product-status nowelty"></span>{% endif %}
      {% if (mode =='featured') %}<span class="product-status action"></span>{% endif %}
      
    </span>
    <span class="title">
      {{ entry.name }} 
    </span>
  </a>
  <!-- End. Photo & Name product -->
  <div class="description">
    <!-- Start. article & variant name & brand name -->
    <!-- End. article & variant name & brand name -->
    <!--
    <div class="frame-star f-s_0">
      <div class="star">
        <div id="star_rating_1104" class="productRate star-small">
          <div style="width: 80%">
          </div>
        </div>
      </div>
      <a href="{{home}}/shop/product/mobilnyi-telefon-sony-xperia-v-lt25i-black#comment" class="count-response">
        14                отзывов            
      </a>
    </div>
    -->
    <!-- Start. Prices-->
    <div class="frame-prices f-s_0">
      <!-- Start. Product price-->
      <span class="current-prices f-s_0">
        <span class="price-new">
          <span>
            <span class="price priceVariant">
              {% if (entry.price) %}{{ (entry.price * system_flags.current_currency.rate_from)|number_format(2, '.', '') }}{% else %}0{% endif %}
            </span>
            {{ system_flags.current_currency.sign }}
          </span>
        </span>
        {% if not (entry.compare_price == '0.00') %}
        <span class="price-add">
          <span>
            <span class="price addCurrPrice">
              <s>{{ (entry.compare_price * system_flags.current_currency.rate_from)|number_format(2, '.', '') }}</s>
            </span>
            <s>{{ system_flags.current_currency.sign }}</s>
          </span>
        </span>
        {% endif %}
      </span>
      <!-- End. Product price-->
    </div>
    <!-- End. Prices-->
    
    <!-- End. Collect information about Variants, for future processing -->
  </div>
  <!-- Start. Remove buttons if compare-->
  <!-- End. Remove buttons if compare-->
  
  <!-- Start. For wishlist page-->
  <!-- End. For wishlist page-->
</li>
{% endfor %}
