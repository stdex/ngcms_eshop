<form action="{{php_self}}" method="post" name="options_bar">
<table width="1000" border="0" cellspacing="0" cellpadding="0" class="editfilter">
  <tr>
<!--Block 1--><td rowspan="2">
<table border="0" cellspacing="0" cellpadding="0" class="filterblock">
  <tr>
    <td valign="top">
    <label>���</label>
    <input name="fname" id="fname" class="bfname" type="text" value="{{fname}}" size="53"/></span>
    </td>
  </tr>
  <tr>
    <td>
    <label>������������</label>
    <input name="an" id="an" class="bfauthor" type="text"  value="{{an}}" autocomplete="off" /> <span id="suggestLoader" style="width: 20px; visibility: hidden;"><img src="{{skins_url}}/images/loading.gif"/></span>
    </td>
  </tr>
</table>

</td><!--/Block 1-->

<!--Block 2-->
<td valign="top" >
<table border="0" cellspacing="0" cellpadding="0" class="filterblock2">
  <tr>
    <td colspan="2">
    <label class="left">�������</label>&nbsp;&nbsp;
    <input name="fphone" id="fphone" class="bfname" type="text" value="{{fphone}}" size="53"/>
    </td>
  </tr>
  <tr>
    <td colspan="2">
    <label class="left">�����</label>&nbsp;&nbsp;
    <input name="fadress" id="fadress" class="bfname" type="text" value="{{fadress}}" size="53"/>
    </td>
  </tr>

</table>

</td>
<!--/Block 2-->

<!--Block 3-->
<td rowspan="2">
<table border="0" cellspacing="0" cellpadding="0" class="filterblock">
  <tr>
    <td>
    <label>����</label>
    �:&nbsp; <input type="text" id="dr1" name="dr1" value="{{fDateStart}}" class="bfdate"/>&nbsp;&nbsp; ��&nbsp;&nbsp; <input type="text" id="dr2" name="dr2" value="{{fDateEnd}}" class="bfdate"/>
    </td>
  </tr>

  <tr>
    <td>
    <label>�� ��������</label>
    <input name="rpp" value="{{rpp}}" type="text" size="3" />
    </td>
  </tr>


</table>
</td>

  </tr>
  <tr>
    <td><input type="submit" value="��������" class="filterbutton"  /></td>
  </tr>
</table>
</form>
<!-- ����� ����� ���������� -->

<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=modify_order" method="post" name="check_order">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="10%">����</td>
<td width="30%">���</td>
<td width="20%">�������</td>
<td width="20%">�����</td>
<td width="10%">���������</td>
<td width="5%">�������?</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" onclick="javascript:check_uncheck_all(check_order)" /></td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="10%" class="contentEntry1">{{ entry.dt|date('d.m.Y H:i') }}</td>
<td width="30%" class="contentEntry1"><a href="{{ entry.edit_link }}" >{{ entry.name }}</a></td>
<td width="20%" class="contentEntry1">{{ entry.phone }}</td>
<td width="20%" class="contentEntry1">{{ entry.address }}</td>
<td width="10%" class="contentEntry1">{{ entry.total_price }}</td>
<td width="5%" class="contentEntry1"><img src="{{home}}/engine/skins/default/images/{% if (entry.paid == 1) %}yes.png{% else %}no.png{% endif %}" alt=""></td>
<td width="5%" class="contentEntry1"><input name="selected_order[]" value="{{ entry.id }}" class="check" type="checkbox" /></td>
</tr>
{% else %}
<tr align="left">
<td colspan="8" class="contentEntry1">�� ������ ������� ������ �� �������.</td>
</tr>
{% endfor %}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="9" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
��������: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- �������� --</option>
<option value="mass_delete">�������</option>
</select>
<input type="submit" value="���������.." class="button" />
<br/>
</div>
</td>
</tr>


<tr>
<td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
</tr>

</table>
</form>

<script language="javascript" type="text/javascript">

function addEvent(elem, type, handler){
  if (elem.addEventListener){
    elem.addEventListener(type, handler, false)
  } else {
    elem.attachEvent("on"+type, handler)
  }
}

// DateEdit filter
function filter_attach_DateEdit(id) {
    var field = document.getElementById(id);
    if (!field)
        return false;

    if (field.value == '')
        field.value = 'DD.MM.YYYY';

    field.onfocus = function(event) {
        var ev = event ? event : window.event;
        var elem = ev.target ? ev.target : ev.srcElement;

        if (elem.value == 'DD.MM.YYYY')
            elem.value = '';

        return true;
    }


    field.onkeypress = function(event) {
        var ev = event ? event : window.event;
        var keyCode = ev.keyCode ? ev.keyCode : ev.charCode;
        var elem = ev.target ? ev.target : ev.srcElement;
        var elv = elem.value;

        isMozilla = false;
        isIE = false;
        isOpera = false;
        if (navigator.appName == 'Netscape') { isMozilla = true; }
        else if (navigator.appName == 'Microsoft Internet Explorer') { isIE = true; }
        else if (navigator.appName == 'Opera') { isOpera = true; }
        else { /* alert('Unknown navigator: `'+navigator.appName+'`'); */ }

        //document.getElementById('debugWin').innerHTML = 'keyPress('+ev.keyCode+':'+ev.charCode+')['+(ev.shiftKey?'S':'.')+(ev.ctrlKey?'C':'.')+(ev.altKey?'A':'.')+']<br/>' + document.getElementById('debugWin').innerHTML;

        // FF - onKeyPress captures functional keys. Skip anything with charCode = 0
        if (isMozilla && !ev.charCode)
            return true;

        // Opera - dumb browser, don't let us to determine some keys
        if (isOpera) {
            var ek = '';
            //for (i in event) { ek = ek + '['+i+']: '+event[i]+'<br/>\n'; }
            //alert(ek);
            if (ev.keyCode < 32) return true;
            if (!ev.shiftKey && ((ev.keyCode >= 33) && (ev.keyCode <= 47))) return true;
            if (!ev.keyCode) return true;
            if (!ev.which) return true;
        }


        // Don't block CTRL / ALT keys
        if (ev.altKey || ev.ctrlKey || !keyCode)
            return true;

        // Allow to input only digits [0..9] and dot [.]
        if (((keyCode >= 48) && (keyCode <= 57)) || (keyCode == 46))
            return true;

        return false;
    }

    return true;
}

</script>


<script language="javascript" type="text/javascript">

function systemInit() {
var aSuggest = new ngSuggest('an',
                                {
                                    'localPrefix'   : '',
                                    'reqMethodName' : 'core.users.search',
                                    'lId'       : 'suggestLoader',
                                    'hlr'       : 'true',
                                    'iMinLen'   : 1,
                                    'stCols'    : 2,
                                    'stColsClass': [ 'cleft', 'cright' ],
                                    'stColsHLR' : [ true, false ],
                                }
                            );

}

// Init system [ IE / Other browsers should be inited in different ways ]
if (document.body.attachEvent) {
    // IE
    document.body.onload = systemInit;
} else {
    // Others
    systemInit();
}

// Init jQueryUI datepicker
$("#dr1").datepicker( { currentText: "", dateFormat: "dd.mm.yy" });
$("#dr2").datepicker( { currentText: "", dateFormat: "dd.mm.yy" });

filter_attach_DateEdit('dr1');
filter_attach_DateEdit('dr2');

</script>
