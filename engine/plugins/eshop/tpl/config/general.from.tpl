<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=options">
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">���������</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>���������� ��������� �� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="count" type="text" title="���������� ��������� �� ��������" size="4" value="{{entries.count}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������� �� �������� ������<br /></td>
<td class="contentEntry2" valign=top><input name="count_search" type="text" title="���������� ��������� �� �������� ������" size="4" value="{{entries.count_search}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������� �� �������� c �������<br /></td>
<td class="contentEntry2" valign=top><input name="count_stocks" type="text" title="���������� ��������� �� �������� c �������" size="4" value="{{entries.count_stocks}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���� ��������� ����������?<br /></td>
<td class="contentEntry2" valign=top><select name="views_count" >{{entries.views_count}}</select></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������������� ��������� ������?<br /></td>
<td class="contentEntry2" valign=top><select name="bidirect_linked_products" >{{entries.bidirect_linked_products}}</select></td>
</tr>
</table>
</fieldset>
</td>
</tr>

<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">����������� �������</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>������ ����������� �����<br /></td>
<td class="contentEntry2" valign=top><input name="width_thumb" type="text" title="������ ����������� �����" size="20" value="{{entries.width_thumb}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>����������� ���������� ��� �����������<br /><small>������ ������ <b>*.jpg;*.jpeg;*.gif;*.png</b></small></td>
<td class="contentEntry2" valign=top><input name="ext_image" type="text" title="����������� ���������� ��� �����������" size="50" value="{{entries.ext_image}}" /></td>
</tr>
<!--
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>������ � ����������</small></td>
<td class="contentEntry2" valign=top><input name="max_image_size" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.max_image_size}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>����������� � ��������</small></td>
<td class="contentEntry2" valign=top><input name="width" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.width}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>����������� � ��������</small></td>
<td class="contentEntry2" valign=top><input name="height" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.height}}" /></td>
</tr>
-->
</table>
</fieldset>
</td>
</tr>

<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">����������� ���������</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>������ ����������� �����<br /></td>
<td class="contentEntry2" valign=top><input name="catz_width_thumb" type="text" title="������ ����������� �����" size="20" value="{{entries.catz_width_thumb}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>����������� ���������� ��� �����������<br /><small>������ ������ <b>*.jpg;*.jpeg;*.gif;*.png</b></small></td>
<td class="contentEntry2" valign=top><input name="catz_ext_image" type="text" title="����������� ���������� ��� �����������" size="50" value="{{entries.catz_ext_image}}" /></td>
</tr>
<!--
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>������ � ����������</small></td>
<td class="contentEntry2" valign=top><input name="catz_max_image_size" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.catz_max_image_size}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>����������� � ��������</small></td>
<td class="contentEntry2" valign=top><input name="catz_width" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.catz_width}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������������ ������ ������������ �����������<br /><small>����������� � ��������</small></td>
<td class="contentEntry2" valign=top><input name="catz_height" type="text" title="������������ ������ ������������ �����������" size="20" value="{{entries.catz_height}}" /></td>
</tr>
-->
</table>
</fieldset>
</td>
</tr>

<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">����������</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>���������� � �������<br /></td>
<td class="contentEntry2" valign=top><input name="email_notify_orders" type="text" title="���������� � �������" size="100" value="{{entries.email_notify_orders}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� � ������������<br /></td>
<td class="contentEntry2" valign=top><input name="email_notify_comments" type="text" title="���������� � ������������" size="100" value="{{entries.email_notify_comments}}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>�������� ����� ����������<br /></td>
<td class="contentEntry2" valign=top><input name="email_notify_back" type="text" title="�������� ����� ����������" size="100" value="{{entries.email_notify_back}}" /></td>
</tr>
</table>
</fieldset>
</td>
</tr>

<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">��������</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>�������� ��������<br /></td>
<td class="contentEntry2" valign=top><textarea rows="10" cols="45" name="description_delivery">{{entries.description_delivery}}</textarea></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>�������� �������<br /></td>
<td class="contentEntry2" valign=top><textarea rows="10" cols="45" name="description_order">{{entries.description_order}}</textarea></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>�������� ��������<br /></td>
<td class="contentEntry2" valign=top><textarea rows="10" cols="45" name="description_phones">{{entries.description_phones}}</textarea></td>
</tr>
</table>
</fieldset>
</td>
</tr>
    
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input name="submit" type="submit"  value="���������" class="button" />
</td>
</tr>
</table>
</form>
