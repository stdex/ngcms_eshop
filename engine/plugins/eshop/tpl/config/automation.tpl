<table border="0" cellspacing="0" cellpadding="0" class="content">
<tbody>
<tr>
    
    <td width="50%" valign="top" class="contentEntry1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
                <tr>
                    <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">������� YML</td>
                </tr>
                <tr>
                    <td><br/>
                        URL: <a href="{{ home }}{{ yml_export_link }}">{{ home }}{{ yml_export_link }}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>

    <td width="50%" class="contentEntry1" valign="top">
        <form action="" method="post">
        <input type="hidden" name="import" value="1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
            <tr>
                <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">������ YML</td>
            </tr>
            <tr>
                <td>
                <table>
                <tbody>
                    <tr id="row">
                        <br/>
                        <td>URL: </td><td><input type="text" size="60" name="yml_url"></td>
                    </tr>
                </tbody>
                </table>
                
                <div class="list">
                    ��������, ������������ ������ ����� ���� �������! 
                    <!--
                    <input type="checkbox" name="replace" value="replace" id="replace2" class="check">
                    <label for="replace2">������������, ���� ��� ����</label><br>
                    <input type="checkbox" name="rand" value="rand" id="rand2" class="check">
                    <label for="rand2">���������� ��������� �����</label><br>
                    <input type="checkbox" name="thumb" value="thumb" id="thumb2" class="check">
                    <label for="thumb2">������� ����������� �����</label><br>
                    <input type="checkbox" name="shadow" value="shadow" id="shadow2" class="check"><label for="shadow2">�������� ����</label><br>
                    <input type="checkbox" name="stamp" value="stamp" id="stamp2" class="check"><label for="stamp2">�������� �����-��������</label>
                    -->
                </div>
                
                </td>
            </tr>
            <tr align="center">
            <td width="100%" class="contentEdit" align="center" valign="top">
                <input type="submit" value="���������!" class="button">
            </td>
            </tr>
            </tbody>
        </table>
        </form>
    </td>
</tr>
<tr>
    
    <td width="50%" valign="top" class="contentEntry1">
        <form action="" method="post">
        <input type="hidden" name="change_price" value="1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
                <tr>
                    <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">�������� ��������� ���</td>
                </tr>
                <tr>
                    <td>
                    <table>
                        <tbody>
                            <tr id="row">
                                <br/>
                                <td>��������� ����:</td>
                                <td>
                                    <select name="change_price_type">
                                            <option value="1">���������</option>
                                            <option value="0">���������</option>
                                    </select>
                                    <input type="text" size="2" name="change_price_qnt"> %
                                </td>
                            </tr>
                        </tbody>
                    </table><br/>
                    </td>
                </tr>
                <tr align="center">
                <td width="100%" class="contentEdit" align="center" valign="top">
                    <input type="submit" value="��������!" class="button">
                </td>
                </tr>
            </tbody>
        </table>
        </form>
    </td>

    <td width="50%" class="contentEntry1" valign="top">
        <form action="" method="post">
        <input type="hidden" name="currency" value="1">
        <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
            <tbody>
            <tr>
                <td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">���������� �����</td>
            </tr>
            <tr>
                <td>

                <div class="list">
                    �������� ������: CurrencyConverterApi.com 
                </div>
                
                </td>
            </tr>
            <tr align="center">
            <td width="100%" class="contentEdit" align="center" valign="top">
                <input type="submit" value="���������!" class="button">
            </td>
            </tr>
            </tbody>
        </table>
        </form>
    </td>
</tr>

</tbody>
</table>
