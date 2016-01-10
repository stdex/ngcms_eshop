{{entries.error}}
<form method="post" action="">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">��������<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{entries.name}}" /></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">���������<br /><small></small></td>
        <td width="50%" class="contentEntry2">
            <select multiple="" name="feature_categories[]">
                {{entries.catz}}
            </select>
        </td>
    </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">��� ����<br /><small></small></td>
        <td width="50%" class="contentEntry2">
            <select name="type" size="5" id="xfSelectType" name="type" onclick="clx(this.value);" onchange="clx(this.value);">
                <option value="text" {% if (mode == "add") %}selected{% endif %}>���������</option>
                <option value="checkbox">������ (checkbox)</option>
                <option value="select">����� ��������</option>
            </select>
        </td>
    </tr>
</table>

<!-- FIELD TYPE: TEXT -->
<div id="type_text">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">�����</td>
  <td width="45%">�������� �� ���������:</td>
  <td><input type="text" name="text_default" value="{{ entries.text_default }}" size=40></td>
 </tr>
</table>
</div>

<!-- FIELD TYPE: CHECKBOX -->
<div id="type_checkbox">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">����</td>
  <td width="45%">�������� �� ���������:</td>
  <td><input type="checkbox" name="checkbox_default" value="{{ entries.checkbox_default }}" ></td>
 </tr>
</table>
</div>

<!-- FIELD TYPE: SELECT -->
<div id="type_select">
<table border="0" cellspacing="1" cellpadding="1" class="content">
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">�����</td>
  <td valign="top">������ ��������: </td>
  <td>
   <table id="xfSelectTable" width="100%" cellspacing="0" cellpadding="0" border="0" class="content" style="padding: 0px;">
   <thead>
    <tr class="contRow1"><td>���</td><td>��������</td><td>&nbsp;</td></tr>
   </thead>
   <tbody id="xfSelectRows">
    {% if (mode == "add") %}
    <tr>
        <td><input size="12" name="so_data[1][0]" type="text" value=""/></td>
        <td><input type="text" size="55" name="so_data[1][1]" value=""/></td>
        <td><a href="#" onclick="return false;"><img src="{{ admin_url }}/skins/default/images/delete.gif" alt="DEL" width="12" height="12" /></a></td>
    </tr>
    {% else %}
        {{ entries.sOpts }}
    {% endif %}
   </tbody>
   <tfoot>
    <tr><td colspan="3"><input type="button" id="xfBtnAdd" style="width: 300px;" value=" + �������� ������"/></td></tr>
   </tfoot>
   </table>
  </td>
 </tr>
 <tr class="contRow1">
  <td width="5%" style="background-color: #EAF0F7; border-left: 1px solid #D1DFEF;">�����</td>
  <td>�������� �� ���������: <br /><small><i>��� ���������� �����</i>: ���</td><td><input type="text" name="select_default" value="" size=40></td>
 </tr>
</table>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">�������<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="position" value="{{entries.position}}" /></td>
    </tr>
    
    <tr>
        <td width="50%" class="contentEntry1">� �������?<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="checkbox" name="in_filter" {% if entries.in_filter == '1' %}checked{% endif %} value="1" ></td>
    </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="���������" class="button" />
</td>
</tr>
</table>
</form>

<script language="javascript">
function clx(mode) {
    document.getElementById('type_text').style.display      = (mode == 'text')?     'block':'none';
    document.getElementById('type_checkbox').style.display  = (mode == 'checkbox')? 'block':'none';
    document.getElementById('type_select').style.display    = (mode == 'select')?   'block':'none';
}

clx('text');

var soMaxNum = $('#xfSelectTable >tbody >tr').length+1;

$('#xfSelectTable a').click(function(){
    if ($('#xfSelectTable >tbody >tr').length > 1) {
        $(this).parent().parent().remove();
    } else {
        $(this).parent().parent().find("input").val('');
    }
});

// jQuery - INIT `select` configuration
$("#xfBtnAdd").click(function() {
    var xl = $('#xfSelectTable tbody>tr:last').clone();
    xl.find("input").val('');
    xl.find("input").eq(0).attr("name", "so_data["+soMaxNum+"][0]");
    //xl.find("span").eq(0).text(soMaxNum);
    xl.find("input").eq(1).attr("name", "so_data["+soMaxNum+"][1]");
    soMaxNum++;

    xl.insertAfter('#xfSelectTable tbody>tr:last');
    $('#xfSelectTable a').click(function(){
        if ($('#xfSelectTable >tbody >tr').length > 1) {
            $(this).parent().parent().remove();
        } else {
            $(this).parent().parent().find("input").val('');
        }
    });
});

</script>
