<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=edit_payment&id={{entries.name}}">
<fieldset class="admGroup">
<legend class="title">��������� Pay2Pay</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>������������� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="merchant_id" type="text" title="������������� ��������" size="100" value="{{entries.options.merchant_id}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>��������� ����<br /></td>
<td class="contentEntry2" valign=top><input name="secret_key" type="text" title="��������� ����" size="100" value="{{entries.options.secret_key}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������� ����<br /></td>
<td class="contentEntry2" valign=top><input name="hidden_key" type="text" title="������� ����" size="100" value="{{entries.options.hidden_key}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>�������� �����?<br /></td>
<td class="contentEntry2" valign=top><select name="test_mode"><option value="0" {% if entries.options.test_mode == 0 %}selected{% endif %}>���</option><option value="1" {% if entries.options.test_mode == 1 %}selected{% endif %}>��</option></select></td>
</tr>
</table>
</fieldset>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input name="submit" type="submit"  value="���������" class="button" />
</td>
</tr>
</table>
</form>