<script type="text/javascript" src="{{home}}/engine/includes/js/ajax.js"></script>
<script type="text/javascript" src="{{home}}/engine/includes/js/admin.js"></script>
<script type="text/javascript" src="{{home}}/engine/includes/js/libsuggest.js"></script>
<div style="text-align : left;">
<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{{skins_url}}/images/nav.gif" hspace="8" alt="" /><a href="{{admin_url}}/admin.php?mod=extras" title="���������� ���������">���������� ���������</a> &#8594; <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=eshop">�������� �������</a> &#8594; {{current_title}}</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr align="center">
<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}'" value="���������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_cat'" value="���������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_feature'" value="��������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_order'" value="������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=options'" value="���������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_currencies'" value="������" class="navbutton" />
<!--
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_delivery'" value="��������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_payment'" value="������" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_promocode'" value="������" class="navbutton" />
-->
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_comment'" value="�����������" class="navbutton" />
<!--
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=automation'" value="�������������" class="navbutton" />
-->
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=urls'" value="���" class="navbutton" />
</td>
</tr>
</table>
{{entries}}
</div>
