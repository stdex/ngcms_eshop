{{entries.error}}
<form method="post" action="" enctype="multipart/form-data">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">Название валюты<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{entries.name}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Знак<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="sign" value="{{entries.sign}}" /></td>
    </tr>
    <tr>
        <td width="50%" class="contentEntry1">Код ISO<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="code" value="{{entries.code}}" /></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Конверсия<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="20" name="rate_from" value="{{entries.rate_from}}" /> = <input type="text" size="20" name="rate_to" value="1.00"  disabled="disabled" /> $</td>
    </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="Сохранить" class="button" />
</td>
</tr>
</table>
</form>
