<form action="{{php_self}}?mod=extra-config&plugin=eshop&action=list_currencies" method="post" name="catz_bar">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="20%">Название валюты</td>
<td width="30%">Знак</td>
<td width="15%">Код ISO</td>
<td width="15%">Конверсия</td>
<td width="15%">Действие</td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="20%" class="contentEntry1"><a href="{{ entry.edit_link }}">{{ entry.name }}</a></td>
<td width="30%" class="contentEntry1">{{ entry.sign }}</td>
<td width="15%" class="contentEntry1">{{ entry.code }}</td>
<td width="15%" class="contentEntry1">{{ entry.rate_from }} {{ entry.sign }} = {{ entry.rate_to }} $</td>
<td width="15%" class="contentEntry1"><a href="{{ entry.del_link }}"  /><img src="/engine/skins/default/images/delete.gif"></a></td>
</tr>
{% else %}
<tr align="left">
<td colspan="8" class="contentEntry1">По вашему запросу ничего не найдено.</td>
</tr>
{% endfor %}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>
<tr align="center">
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">

<input class="button" style="float:right; width: 105px;" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop&action=add_currency'" value="Добавить валюту" />
</div>
</td>
</tr>

</table>
</form>
