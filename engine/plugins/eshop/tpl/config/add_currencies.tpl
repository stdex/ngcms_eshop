{{ entries.error }}
<form method="post" action="" enctype="multipart/form-data">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%" class="contentEntry1">�������� ������<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ entries.name }}"/>
            </td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">����<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="sign" value="{{ entries.sign }}"/>
            </td>
        </tr>
        <tr>
            <td width="50%" class="contentEntry1">��� ISO<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="80" name="code" value="{{ entries.code }}"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">���������<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="6" name="rate_from"
                                                         value="{{ entries.rate_from }}"/> $ = <input type="text"
                                                                                                      size="6"
                                                                                                      name="rate_to"
                                                                                                      value="1.00"
                                                                                                      disabled="disabled"/>
            </td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">�������<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="text" size="1" name="position"
                                                         value="{{ entries.position }}"/></td>
        </tr>

        <tr>
            <td width="50%" class="contentEntry1">��������?<br/>
                <small></small>
            </td>
            <td width="50%" class="contentEntry2"><input type="checkbox" name="enabled"
                                                         {% if entries.mode == 'add' %}checked{% else %}{% if entries.enabled == '1' %}checked{% endif %}{% endif %}
                                                         value="1"></td>
        </tr>

    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" class="contentEdit" align="center">
                <input type="submit" name="submit" value="���������" class="button"/>
            </td>
        </tr>
    </table>
</form>
