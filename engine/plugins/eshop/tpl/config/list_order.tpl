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
    <label>�� ��������</label>
    <input name="rpp" value="{{rpp}}" type="text" size="3" />
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
<td width="5%"><input class="check" type="checkbox" name="master_box" onclick="javascript:check_uncheck_all(check_order)" /></td>
</tr>
{% for entry in entries %}

<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="10%" class="contentEntry1">{{ entry.dt|date('d.m.Y H:i') }}</td>
<td width="30%" class="contentEntry1"><a href="{{ entry.edit_link }}" >{{ entry.name }}</a></td>
<td width="20%" class="contentEntry1">{{ entry.phone }}</td>
<td width="20%" class="contentEntry1">{{ entry.address }}</td>
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
