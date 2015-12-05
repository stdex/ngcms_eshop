<script src="{{ admin_url }}/plugins/eshop/upload/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{{ admin_url }}/plugins/eshop/upload/uploadifive/uploadifive.css">
<link rel="stylesheet" href="{{ admin_url }}/plugins/eshop/upload/capty/jquery.capty.css" type="text/css" />
<script src="{{ admin_url }}/plugins/eshop/upload/uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{{ admin_url }}/plugins/eshop/upload/capty/jquery.capty.min.js"></script>
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
