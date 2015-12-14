<script src="{{ admin_url }}/plugins/eshop/upload/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/upload/uploadifive/uploadifive.css">
<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/upload/capty/jquery.capty.css" type="text/css" />
<script src="{{ admin_url }}/plugins/eshop/upload/uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/upload/capty/jquery.capty.min.js"></script>

<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/tpl/config/select2/css/select2.min.css">
<script src="{{ admin_url }}/plugins/eshop/tpl/config/select2/js/select2.full.min.js" type="text/javascript"></script>


{{entries.error}}
<form method="post" action="" id="product_form" enctype="multipart/form-data">
<input type="hidden" name="handler" value="1" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">Название<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{entries.name}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Код<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="code" value="{{entries.code}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">URL<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="url" value="{{entries.url}}" /></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Meta title<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_title" value="{{entries.meta_title}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Meta keywords<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_keywords" value="{{entries.meta_keywords}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Meta description<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="meta_description" value="{{entries.meta_description}}" /></td>
    </tr>


    <tr>
        <td width="50%" class="contentEntry1">Категория<br /><small></small></td>
        <td width="50%" class="contentEntry2">
            <select name="parent">
                <option value="0">Выберите категорию</option>
                {{entries.catz}}
            </select>
        </td>
    </tr>
    
    <tr>
        <td width="50%" class="contentEntry1">Анотация<br /><small></small></td>
        <td width="50%" class="contentEntry2"><textarea rows="10" cols="45" name="annotation">{{entries.annotation}}</textarea></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Полное описание<br /><small></small></td>
        <td width="50%" class="contentEntry2"><textarea rows="10" cols="45" name="body">{{entries.body}}</textarea></td>
    </tr>    
    
    
    <tr>
    <td width="50%" class="contentEntry1">Прикрепить изображения<br /><small></small></td>
    <td width="50%" class="contentEntry2">

    <script type="text/javascript">
    $(document).ready(function() {

        var i = 0;
        $('#file_upload').uploadifive({
            'auto'             : false,
            'formData'         : {
                                   'id' : $("#txtdes").val()
                                 },
            'queueID'          : 'queue',
            'uploadScript'     : '/engine/plugins/eshop/upload/libs/upload_product_images.php?id={{entries.id}}',
            'onUpload' : function(filesToUpload) {
                     i = 0;
                },
            'onUploadComplete' : function(file, data) {
                    var form = document.forms['product_form'];
                    var el = document.createElement("input");
                    el.type = "hidden";
                    el.name = "data[images]["+i+"]";
                    el.value = file.name;
                    form.appendChild(el);
                    i++;
                },
            'onQueueComplete' : function(uploads) {
                document.getElementById('product_form').submit();
                //$("#txtdes").val();
                //location.reload();
            }
        });


    $('.fix').capty({
       cWrapper:  'capty-tile',
       height:   36,
       opacity:  .6
     });


    });

    </script>

    <div id="queue">
    </div>
    <input id="file_upload" name="file_upload" type="file" multiple="true">

    </td>
    </tr>

    <tr>
    <td width="50%" class="contentEntry1">Прикрепленные изображения<br /><small></small></td>
    <td width="50%" class="contentEntry2">
    <table>
    <tr>
    {% for img in entries.entriesImg %}
    <td>
        <a href='{{home}}/uploads/eshop/products/{{img.filepath}}' target='_blank'><img class="fix" name="#content-target-{{img.id}}" src='{{home}}/uploads/eshop/products/thumb/{{img.filepath}}' width='150' height='120'></a>
        <div id="content-target-{{img.id}}">
           <a href="{{img.del_link}}">[x]</a>&nbsp;&nbsp;&nbsp;
        </div>
    </td>
    {% endfor %}
    </tr>
    </table>
    </td>
    </tr>

    <tr>
    <td width="50%" class="contentEntry1">Активировать объявление?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="active" {% if entries.mode == 'add' %}checked{% else %}{% if entries.active == '1' %}checked{% endif %}{% endif %} value="1" > </td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Рекомендованный?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="featured" {% if entries.featured == '1' %}checked{% endif %} value="1" ></td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Акционный?<br /><small></small></td>
    <td width="50%" class="contentEntry2"><input type="checkbox" name="stocked" {% if entries.stocked == '1' %}checked{% endif %} value="1" ></td>
    </tr>
    
    <tr>
    <td width="50%" class="contentEntry1">Связанные товары?<br /><small></small></td>
    <td width="50%" class="contentEntry2">
        <select name="linked-products[]" class="js-data-linked-products-ajax" id="js-data-linked-products-ajax" multiple="multiple" style="width: 200px;">
        {% if (entries.related) %}
            {% for rel in entries.related %}
            <option selected="selected" value="{{rel.related_id}}">{{rel.name}}</option>
            {% endfor %};
        {% endif %}
        </select>
</td>
    </tr>
    
    <tr>
        <td width="50%" class="contentEntry1">Цены<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="10" name="price" value="{{entries.prices[0].price}}" > <input type="text" size="10" name="compare_price" value="{{entries.prices[0].compare_price}}" > 
        <select name="stock" style="width: 200px;">
            <option {% if entries.mode == 'add' %}selected="selected"{% else %}{% if entries.prices[0].stock == '1' %}selected="selected"{% endif %}{% endif %} value="1">Есть</option>
            <option {% if entries.prices[0].stock == '0' %}selected="selected"{% endif %}value="0">Нет</option>
            <option {% if entries.prices[0].stock == '2' %}selected="selected"{% endif %}value="2">На заказ</option>
        </select></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Дополнительные поля<br /><small></small></td>
        <td width="50%" class="contentEntry2"></td>
    </tr>
    {% for feature in entries.features %}
        <tr>
            <td width="50%" class="contentEntry1">{{feature.name}}<br /><small></small></td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="data[features][{{feature.id}}]" value="{{feature.value}}" ></td>
        </tr>
    {% endfor %}

</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input class="button" style="float:center; width: 55px;" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" value="Сохранить" />
<!--
<input type="submit" name="submit" value="Сохранить" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" />
-->
</td>
</tr>
</table>
</form>

<script>
$(document).ready(function() {

    $(".js-data-linked-products-ajax").select2({
      ajax: {
        url: "{{home}}/engine/rpc.php?methodName=eshop_linked_products",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term,
            mode: "{{entries.mode}}",
            id: "{{entries.id }}"
          };
        },
        processResults: function (data, params) {
          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          params.page = params.page || 1;
     
          return {
            results: data.data
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      minimumInputLength: 1,
      templateResult: formatRepo,
      templateSelection: formatRepoSelection
    });

});

function formatRepo (repo) {

 var img_url = "";
 
    if (repo.image_filepath == null) {
        img_url = "{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg";
    }
    else {
        img_url = "{{home}}/uploads/eshop/products/thumb/" + repo.image_filepath;
    }

  var markup = "<div class='select2-result-repository clearfix'>" +
    "<div class='select2-result-repository__avatar'><img src='" + img_url + "' /></div>" +
    "<div class='select2-result-repository__meta'>" +
      "<div class='select2-result-repository__title'>" + repo.name + "</div>";

  markup += "<div class='select2-result-repository__statistics'>" +
    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> ID: " + repo.id + " </div>" +
    "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> Код: " + repo.code + " </div>" +
    "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> Категория: " + repo.category + " </div>" +
  "</div>" +
  "</div></div>";
      
/*
  if (repo.description) {
    markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
  }

  markup += "<div class='select2-result-repository__statistics'>" +
    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
    "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
    "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
  "</div>" +
  "</div></div>";
*/


  return markup;
}

function formatRepoSelection (repo) {
  return repo.name;
}
</script>

<style>
.select2-result-repository { padding-top: 4px; padding-bottom: 3px; }
.select2-result-repository__avatar { float: left; width: 60px; margin-right: 10px; }
.select2-result-repository__avatar img { width: 100%; height: auto; border-radius: 2px; }
.select2-result-repository__meta { margin-left: 70px; }
.select2-result-repository__title { color: black; font-weight: bold; word-wrap: break-word; line-height: 1.1; margin-bottom: 4px; }
.select2-result-repository__forks, .select2-result-repository__stargazers { margin-right: 1em; }
.select2-result-repository__forks, .select2-result-repository__stargazers, .select2-result-repository__watchers { display: inline-block; color: #aaa; font-size: 11px; }
.select2-result-repository__description { font-size: 13px; color: #777; margin-top: 4px; }
.select2-results__option--highlighted .select2-result-repository__title { color: white; }
.select2-results__option--highlighted .select2-result-repository__forks, .select2-results__option--highlighted .select2-result-repository__stargazers, .select2-results__option--highlighted .select2-result-repository__description, .select2-results__option--highlighted .select2-result-repository__watchers { color: #c6dcef; }
#tags {
    width:100%
}
</style>
