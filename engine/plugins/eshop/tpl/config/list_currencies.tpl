<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=list_currencies" method="post" name="catz_bar">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="20%">�������� ������</td>
<td width="20%">����</td>
<td width="15%">��� ISO</td>
<td width="25%">���������</td>
<td width="2%">�������</td>
<td width="25%">������</td>
<td width="6%">��������</td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="20%" class="contentEntry1"><a href="{{ entry.edit_link }}">{{ entry.name }}</a></td>
<td width="20%" class="contentEntry1">{{ entry.sign }}</td>
<td width="15%" class="contentEntry1">{{ entry.code }}</td>
<td width="25%" class="contentEntry1">{{ entry.rate_from }} $ = {{ entry.rate_to|number_format(2, '.', '') }} {{ entry.sign }}</td>
<td width="2%" class="contentEntry1" style="text-align:center;vertical-align:middle">{{ entry.position }}</td>
<td width="25%" class="contentEntry1" style="text-align:center;vertical-align:middle">{% if (loop.index == 1) %}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{% endif %}<img src="{{home}}/engine/skins/default/images/{% if (entry.enabled == 1) %}yes.png{% else %}no.png{% endif %}" alt="">{% if (loop.index == 1) %}&nbsp;<img src="{{home}}/engine/skins/default/images/important.gif" alt="">{% endif %}</td>
<td width="6%" class="contentEntry1" style="text-align:center;vertical-align:middle"><a href="{{ entry.del_link }}"  /><img src="/engine/skins/default/images/delete.gif"></a></td>
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
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">

<input class="button" style="float:right; width: 105px;" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop&action=add_currency'" value="�������� ������" />
</div>
</td>
</tr>

</table>
</form>
