<?php

if(!defined('NGCMS'))
    exit('HAL');

plugins_load_config();
LoadPluginLang('eshop', 'config', '', '', '#');

include_once(dirname(__FILE__).'/cache.php');
include_once(dirname(__FILE__).'/functions.php');

switch ($_REQUEST['action']) {
    
    case 'list_product':    list_product();                        break;
    case 'add_product':     add_product();                         break;
    case 'edit_product':    edit_product();                        break;
    case 'modify_product':  modify_product(); list_product();      break;
    
    case 'list_feature':    list_feature();                        break;
    case 'add_feature':     add_feature();                         break;
    case 'edit_feature':    edit_feature();                        break;
    case 'modify_feature':  modify_feature(); list_feature();      break;
        
    case 'list_cat':        list_cat();                            break;
    case 'add_cat':         add_cat();                             break;
    case 'edit_cat':        edit_cat();                            break;
    case 'del_cat':         del_cat(); list_cat();                 break;
    
    case 'list_order':      list_order();                          break;
    case 'edit_order':      edit_order();                          break;
    case 'modify_order':    modify_order(); list_order();          break;

    case 'list_comment':    list_comment();                        break;
    case 'modify_comment':  modify_comment(); list_comment();      break;

    case 'list_currencies': list_currencies();                     break;
    case 'add_currency':    add_currency();                        break;
    case 'edit_currency':   edit_currency();                       break;
    case 'del_currency':    del_currency(); list_currencies();     break;
    
    case 'options':         options();                             break;

    case 'urls':            urls();                                break;
    case 'automation':      automation();                          break;
    
    case 'list_payment':    list_payment();                        break;
    case 'edit_payment':    edit_payment();                        break;
    
    default:                list_product();
}

function list_product()
{
global $tpl, $mysql, $lang, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_product'), 'eshop', 1);
    
    $tVars = array();
    
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id");
    
    $cats = getCats($res);
    
    // Load admin page based cookies
    $admCookie = admcookie_get();

    $fName          = $_REQUEST['fname'];
    $fStatus        = $_REQUEST['fstatus'];
    $fCategory      = $_REQUEST['fcategory'];
    $fId            = $_REQUEST['fid'];
    $fCode          = $_REQUEST['fcode'];

    $news_per_page  = isset($_REQUEST['rpp'])?intval($_REQUEST['rpp']):intval($admCookie['eshop']['pp']);
    // - Set default value for `Records Per Page` parameter
    if (($news_per_page < 2)||($news_per_page > 2000))
        $news_per_page = 10;
    
    // - Save into cookies current value
    $admCookie['eshop']['pp'] = $news_per_page;
    admcookie_set($admCookie);

    $conditions = array();
    if ($fName) {
        array_push($conditions, "p.name LIKE ".db_squote("%".$fName."%"));
    }

    if ($fStatus == '0' || $fStatus == '1') {
        array_push($conditions, "p.active = ".db_squote($fStatus));
    }
    
    if ($fCategory) {
        $catz_filter = array();
        $catz_filter = getChildIdsArray($cats, $fCategory, 0);
        $catz_filter[] = $fCategory;
        $catz_filter_comma_separated = implode(",", $catz_filter);
        array_push($conditions, "pc.category_id IN (".$catz_filter_comma_separated.") ");
    }
    
    if ($fId) {
        array_push($conditions, "p.id = ".db_squote($fId));
    }
    
    if ($fCode) {
        array_push($conditions, "p.code = ".db_squote($fCode));
    }

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.url AS url, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";
    
    //$sqlQCount = "SELECT COUNT(p.id) FROM ng_eshop_products p ORDER BY p.id DESC";
    
    $pageNo     = intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1)    $pageNo = 1;
    if (!$start_from)   $start_from = ($pageNo - 1)* $news_per_page;
    
    $count = $mysql->result($sqlQCount);
    $countPages = ceil($count / $news_per_page);
    
    //var_dump($sqlQ);
    //var_dump($sqlQ.' LIMIT '.$start_from.', '.$news_per_page);

    foreach ($mysql->select($sqlQ.' LIMIT '.$start_from.', '.$news_per_page) as $row)
    {
        $view_link = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('alt' => $row['url'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        
        $tEntry[$row['id']] = array (
            'id'                   => $row['id'],
            'code'                 => $row['code'],
            'name'                 => $row['name'],
            
            'category'             => $row['category'],
            'image_filepath'       => $row['image_filepath'],

            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'active'               => $row['active'],
            'featured'             => $row['featured'],
            'stocked'              => $row['stocked'],
            
            'position'             => $row['position'],
            
            'date'                 => $row['date'],
            'editdate'             => $row['editdate'],
            
            'edit_link'            => "?mod=extra-config&plugin=eshop&action=edit_product&id=".$row['id']."",
            'view_link'            => $view_link,
        );
    }
    
    $entries_array_ids = array_keys($tEntry);
    
    if(isset($entries_array_ids) && !empty($entries_array_ids)) {
        
        $entries_string_ids = implode(',', $entries_array_ids);
    
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images i WHERE i.product_id IN ('.$entries_string_ids.') ORDER BY i.position, i.id') as $irow)
        {
            $tEntry[$irow['product_id']]['images'][] = $irow;
        }
        
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_variants v WHERE v.product_id IN ('.$entries_string_ids.') ORDER BY v.position, v.id') as $vrow)
        {
            $tEntry[$vrow['product_id']]['variants'][] = $vrow;
        }
        
    }

    $xt = $twig->loadTemplate($tpath['config/list_product'].'config/'.'list_product.tpl');

    $tVars = array(
        'php_self'      =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop', 
        'filter_cats' => getTree($cats, $fCategory, 0),
        'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=eshop'.($news_per_page?'&rpp='.$news_per_page:'').($fName?'&fname='.$fName:'').($fStatus?'&fstatus='.$fStatus:'').($fCategory?'&fcategory='.$fCategory:'').'&page=%page%')),
        'rpp'           =>  $news_per_page,
        'fname'         =>  secure_html($fName),
        'fstatus'           =>  secure_html($fStatus),
        'fcategory'         =>  secure_html($fCategory),
        'fid'         =>  secure_html($fId),
        'fcode'         =>  secure_html($fCode),
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������',
    );
    
    print $xg->render($tVars);
}

function add_product()
{
global $tpl, $template, $config, $mysql, $lang, $twig, $parse;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_product'), 'eshop', 1);

    if (isset($_REQUEST['handler']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� �������� �� ������';
        }
        
        $SQL['code'] = input_filter_com(convert($_REQUEST['code']));

        $SQL['url'] = input_filter_com(convert($_REQUEST['url']));
        if(empty($SQL['url']))
        {
            $SQL['url'] = strtolower($parse->translit($SQL['name'],1, 1));
        }
        
        if ($SQL["url"]) {
            if ( is_array($mysql->record("select id from ".prefix."_eshop_products where url = ".db_squote($SQL["url"])." limit 1")) ) {
                $error_text[] = '����� altname ��� ����������.';
            }
        }

        $SQL['meta_title'] = input_filter_com(convert($_REQUEST['meta_title']));
        if(empty($SQL['meta_title']))
        {
            $SQL['meta_title'] = $SQL['name'];
        }
        $SQL['meta_keywords'] = input_filter_com(convert($_REQUEST['meta_keywords']));
        $SQL['meta_description'] = input_filter_com(convert($_REQUEST['meta_description']));
        
        $SQL['annotation'] = $_REQUEST['annotation'];
        $SQL['body'] = $_REQUEST['body'];
        
        $SQL['active'] = intval($_REQUEST['active']);
        $SQL['featured'] = intval($_REQUEST['featured']);
        $SQL['stocked'] = intval($_REQUEST['stocked']);
        
        $SQL['date'] = time() + ($config['date_adjust'] * 60);
        $SQL['editdate'] = $SQL['date'];
        
        if(!empty($_REQUEST['data']['features'])) {
            $features = $_REQUEST['data']['features'];
        }
        else {
            $features = NULL;
        }
        
        $images = $_REQUEST['data']['images'];
        
        if($_REQUEST['linked-products'] != "") {
            $linked_products = explode(",", $_REQUEST['linked-products']);
        }
        else {
            $linked_products = NULL;
        }
        /*
        $price = $_REQUEST['price'];
        $compare_price = $_REQUEST['compare_price'];
        $stock = $_REQUEST['stock'];
        */

        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('INSERT INTO '.prefix.'_eshop_products SET '.implode(', ',$vnames).' ');
            
            $qid = $mysql->lastid('eshop_products');
            
            if($images != NULL) {
                
                @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/', 0777);
                @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb', 0777);
                
                foreach ($images as $inx_img => $img) {
                    $timestamp = time();
                    $iname = $timestamp."-".$img;
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/thumb/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $mysql->query("INSERT INTO ".prefix."_eshop_images (`filepath`, `product_id`, `position`) VALUES ('$iname','$qid','$inx_img')");
                }
            }
            
            
            if($features != NULL) {
                foreach ($features as $f_key => $f_value) {
                    if($f_value != '') {
                        $mysql->query("INSERT INTO ".prefix."_eshop_options (`product_id`, `feature_id`, `value`) VALUES ('$qid','$f_key','$f_value')");
                    }
                }
            }
            
            $category_id = intval($_REQUEST['parent']);
            
            if($category_id != 0) {
                $mysql->query("INSERT INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
            }
            
            $bidirect_linked_products = pluginGetVariable('eshop', 'bidirect_linked_products');
            
            if($linked_products != NULL) {
                $mysql->query("DELETE FROM ".prefix."_eshop_related_products WHERE product_id='$qid'");
                foreach ($linked_products as $p_key => $p_value) {
                    $mysql->query("INSERT INTO ".prefix."_eshop_related_products (`product_id`, `related_id`, `position`) VALUES ('$qid','$p_value','0')");
                    if($bidirect_linked_products == "1") {
                        $lprd = $mysql->record("SELECT * FROM ".prefix."_eshop_related_products WHERE product_id = ".db_squote($p_value)." AND related_id = ".db_squote($qid)." ");
                        if(empty($lprd)) {
                            $mysql->query("INSERT INTO ".prefix."_eshop_related_products (`product_id`, `related_id`, `position`) VALUES ('$p_value','$qid','0')");
                        }
                    }
                }
            }
            
            /*
            if(isset($stock)) {
                $mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
                $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `price`, `compare_price`, `stock`) VALUES ('$qid', '$price', '$compare_price', '$stock')");
            }
            */
            $optlist = array();
            if (isset($_REQUEST['so_data']) && is_array($_REQUEST['so_data'])) {
                $mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
                foreach ($_REQUEST['so_data'] as $k => $v) {
                    if (is_array($v)) {
                        if($v[4] == '') {
                            $v[4] = 'NULL';
                        }
                        $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `sku`, `name`, `price`, `compare_price`, `amount`, `stock`) VALUES ('$qid', '$v[0]', '$v[1]', '$v[2]', '$v[3]', $v[4], '$v[5]')");
                    }
                }
            }            
            
            generate_catz_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_product');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    foreach ($SQL as $k => $v) { 
        $tEntry[$k] = $v;
    }
        
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id");
    $cats = getCats($res);
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
    {
        $frow['value'] = '';
        $frow['foptions'] = json_decode($frow['foptions'], true);
        $features_array[] = $frow;
    }
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories_features") as $cfrow)
    {
        $cat_features_array[$cfrow['category_id']][] = $cfrow['feature_id'];
    }

    $tEntry['catz'] = getTree($cats);
    $tEntry['features'] = $features_array;
    $tEntry['cat_features'] = json_encode($cat_features_array);
    $tEntry['error'] = $error_input;
    $tEntry['mode'] = "add";

    $xt = $twig->loadTemplate($tpath['config/add_product'].'config/'.'add_product.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������: ���������� ��������',
    );
    
    print $xg->render($tVars);
}

function edit_product()
{
global $tpl, $template, $config, $mysql, $lang, $twig, $parse;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_product'), 'eshop', 1);

    $qid = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_products LEFT JOIN '.prefix.'_eshop_products_categories ON '.prefix.'_eshop_products.id='.prefix.'_eshop_products_categories.product_id WHERE id = '.db_squote($qid).' LIMIT 1');

    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id");
    $cats = getCats($res);

    $options_array = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_options LEFT JOIN ".prefix."_eshop_features ON ".prefix."_eshop_features.id=".prefix."_eshop_options.feature_id WHERE ".prefix."_eshop_options.product_id = '$qid' ORDER BY position, id") as $orow)
    {
        $options_array[$orow['id']] = $orow['value'];
    }
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
    {
        $frow['value'] = $options_array[$frow['id']];
        $frow['foptions'] = json_decode($frow['foptions'], true);
        $features_array[] = $frow;
    }
    
    $positions_img = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_images WHERE product_id = '$qid' ORDER BY position, id") as $irow)
    {
        $images_array[] = 
            array(
                'id' => $irow['id'],
                'filepath' => $irow['filepath'],
                'product_id' => $irow['product_id'],
                'position' => $irow['position'],
                'del_link' => home.'/engine/admin.php?mod=extra-config&plugin=eshop&action=edit_product&id='.$qid.'&delimg='.$irow['id'].'&filepath='.$irow['filepath'].'',
                );
        $positions_img[] = $irow['position'];
    }
    
    if(!empty($positions_img)) {
        $max_img_pos = max($positions_img) + 1;
    }
    else {
        $max_img_pos = 0;
    }
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_related_products rp LEFT JOIN ".prefix."_eshop_products p ON p.id=rp.related_id WHERE rp.product_id = '$qid' ORDER BY rp.position") as $rrow)
    {
        $related_array[] = 
            array(
                'name' => $rrow['name'],
                'product_id' => $rrow['product_id'],
                'related_id' => $rrow['related_id'],
                'position' => $rrow['position']
                );
    }

    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_variants WHERE product_id = '$qid' ORDER BY position, id") as $vrow)
    {
        $price_array[] = $vrow;
    }

    $sOpts = array();
    $fNum = 1;
    if (isset($price_array) && is_array($price_array)) {
            foreach ($price_array as $k => $v) {
                $check_stock_array = array();
                $check_stock = $v['stock'];
                switch($check_stock) {
                    case '5':
                        $check_stock_array[5] = 'selected="selected"';
                        $check_stock_array[0] = '';
                        $check_stock_array[1] = '';
                        break;
                    case '0':
                        $check_stock_array[5] = '';
                        $check_stock_array[0] = 'selected="selected"';
                        $check_stock_array[1] = '';
                        break;
                    case '1':
                        $check_stock_array[5] = '';
                        $check_stock_array[0] = '';
                        $check_stock_array[1] = 'selected="selected"';
                        break;
                    default:
                        $check_stock_array[5] = 'selected="selected"';
                        $check_stock_array[0] = '';
                        $check_stock_array[1] = '';
                        break;
                }
                array_push($sOpts, '<tr>
                <td><input type="text" size="12" name="so_data['.($fNum).'][0]" value="'.htmlspecialchars($v['sku'], ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td>
                <td><input type="text" size="45" name="so_data['.($fNum).'][1]" value="'.htmlspecialchars($v['name'], ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td>
                <td><input type="text" size="12" name="so_data['.($fNum).'][2]" value="'.htmlspecialchars($v['price'], ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td>
                <td><input type="text" size="12" name="so_data['.($fNum).'][3]" value="'.htmlspecialchars($v['compare_price'], ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td>
                <td><input type="text" size="12" name="so_data['.($fNum).'][4]" value="'.htmlspecialchars($v['amount'], ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td>
                <td>
                    <select name="so_data['.($fNum).'][5]" style="width: 100px;">
                        <option '.$check_stock_array[5].' value="5">����</option>
                        <option '.$check_stock_array[0].' value="0">���</option>
                        <option '.$check_stock_array[1].' value="1">�� �����</option>
                    </select>
                </td>
                <td><a href="#" onclick="return false;"><img src="'.skins_url.'/images/delete.gif" alt="DEL" width="12" height="12" /></a></td>
                </tr>');
                $fNum++;
            }
    }
    if (!count($sOpts)) {
        array_push($sOpts, '<tr>
            <td><input size="12" name="so_data[1][0]" type="text" value=""/></td>
            <td><input size="45" name="so_data[1][1]" type="text" value=""/></td>
            <td><input size="12" name="so_data[1][2]" type="text" value=""/></td>
            <td><input size="12" name="so_data[1][3]" type="text" value=""/></td>
            <td><input size="12" name="so_data[1][4]" type="text" value=""/></td>
            <td>
                <select name="so_data[1][5]" style="width: 100px;">
                    <option selected="selected" value="5">����</option>
                    <option value="0">���</option>
                    <option value="1">�� �����</option>
                </select>
            </td>
            <td><a href="#" onclick="return false;"><img src="'.skins_url.'/images/delete.gif" alt="DEL" width="12" height="12" /></a></td>
        </tr>');
    }
    
    $sOpts = implode("\n", $sOpts);
 
    if (isset($_REQUEST['handler']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� �������� �� ������';
        }
        
        $SQL['code'] = input_filter_com(convert($_REQUEST['code']));

        $SQL['url'] = input_filter_com(convert($_REQUEST['url']));
        if(empty($SQL['url']))
        {
            $SQL['url'] = strtolower($parse->translit($SQL['name'],1, 1));
        }

        if ($SQL["url"]) {
            if ( is_array($mysql->record("select id from ".prefix."_eshop_products where url = ".db_squote($SQL["url"])." and id <> ".$row['id']." limit 1")) ) {
                $error_text[] = '����� altname ��� ����������.';
            }
        }

        $SQL['meta_title'] = input_filter_com(convert($_REQUEST['meta_title']));
        $SQL['meta_keywords'] = input_filter_com(convert($_REQUEST['meta_keywords']));
        $SQL['meta_description'] = input_filter_com(convert($_REQUEST['meta_description']));
        
        $SQL['annotation'] = $_REQUEST['annotation'];
        $SQL['body'] = $_REQUEST['body'];
        
        $SQL['active'] = intval($_REQUEST['active']);
        $SQL['featured'] = intval($_REQUEST['featured']);
        $SQL['stocked'] = intval($_REQUEST['stocked']);

        $SQL['editdate'] = time() + ($config['date_adjust'] * 60);
        
        $features = $_REQUEST['data']['features'];
        $images = $_REQUEST['data']['images'];
        if($_REQUEST['linked-products'] != "") {
            $linked_products = explode(",", $_REQUEST['linked-products']);
        }
        else {
            $linked_products = NULL;
        }

        /*
        $price = $_REQUEST['price'];
        $compare_price = $_REQUEST['compare_price'];
        $stock = $_REQUEST['stock'];
        */

        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('UPDATE '.prefix.'_eshop_products SET '.implode(', ',$vnames).' WHERE id = \''.intval($qid).'\'  ');

            if($images != NULL) {
                
                @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/', 0777);
                @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb', 0777);
                
                foreach ($images as $inx_img => $img) {
                    $timestamp = time();
                    $iname = $timestamp."-".$img;
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/thumb/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $pos = $max_img_pos + $inx_img;
                    
                    $mysql->query("INSERT INTO ".prefix."_eshop_images (`filepath`, `product_id`, `position`) VALUES ('$iname','$qid','$pos')");
                }
            }

            if($features != NULL) {
                $mysql->query("DELETE FROM ".prefix."_eshop_options WHERE product_id='$qid'");
                foreach ($features as $f_key => $f_value) {
                    
                    if($f_value != '')
                        $mysql->query("REPLACE INTO ".prefix."_eshop_options (`product_id`, `feature_id`, `value`) VALUES ('$qid','$f_key','$f_value') ");
                }
            }

            $bidirect_linked_products = pluginGetVariable('eshop', 'bidirect_linked_products');
            
            if($linked_products != NULL) {
                $mysql->query("DELETE FROM ".prefix."_eshop_related_products WHERE product_id='$qid'");
                foreach ($linked_products as $p_key => $p_value) {
                    $mysql->query("INSERT INTO ".prefix."_eshop_related_products (`product_id`, `related_id`, `position`) VALUES ('$qid','$p_value','0')");
                    if($bidirect_linked_products == "1") {
                        $lprd = $mysql->record("SELECT * FROM ".prefix."_eshop_related_products WHERE product_id = ".db_squote($p_value)." AND related_id = ".db_squote($qid)." ");
                        if(empty($lprd)) {
                            $mysql->query("INSERT INTO ".prefix."_eshop_related_products (`product_id`, `related_id`, `position`) VALUES ('$p_value','$qid','0')");
                        }
                    }
                }
            }
            else {
                $mysql->query("DELETE FROM ".prefix."_eshop_related_products WHERE product_id='$qid'");
            }
            
            $category_id = intval($_REQUEST['parent']);
            
            if($category_id != 0) {
                $mysql->query("DELETE FROM ".prefix."_eshop_products_categories WHERE product_id='$qid'");
                $mysql->query("INSERT INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
            }
            else {
                $mysql->query("DELETE FROM ".prefix."_eshop_products_categories WHERE product_id='$qid'");
            }
            
            if (isset($_REQUEST['so_data']) && is_array($_REQUEST['so_data'])) {
                $mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
                foreach ($_REQUEST['so_data'] as $k => $v) {
                    if (is_array($v)) {
                        if($v[4] == '') {
                            $v[4] = 'NULL';
                        }
                        $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `sku`, `name`, `price`, `compare_price`, `amount`, `stock`) VALUES ('$qid', '$v[0]', '$v[1]', '$v[2]', '$v[3]', $v[4], '$v[5]')");
                    }
                }
            }
            
            /*
            if(isset($stock)) {
                $mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
                $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `price`, `compare_price`, `stock`) VALUES ('$qid', '$price', '$compare_price', '$stock')");
            }
            */
            
            generate_catz_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_product');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    foreach ($row as $k => $v) { 
        $tEntry[$k] = $v;
    }
    
    if (isset($_REQUEST['delimg']) && isset($_REQUEST['filepath']))
    {
        $imgID = intval($_REQUEST['delimg']);
        $imgPath = input_filter_com(convert($_REQUEST['filepath']));
        $mysql->query("delete from ".prefix."_eshop_images where id = ".$imgID."");
        delete_product_image($imgPath, $qid);

        $r_pos = 0;
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images WHERE product_id = '.$row['id'].' ORDER BY position, id ') as $img_row)
        {
            $mysql->query("update ".prefix."_eshop_images set position = ".db_squote($r_pos)." where (id = ".db_squote($img_row['id']).") ");
            $r_pos += 1;
        }

        
        //echo root . '/uploads/zboard/' . $imgPath;
        //unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/products/' . $imgPath);
        //unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/products/thumb/' . $imgPath);
        redirect_eshop('?mod=extra-config&plugin=eshop&action=edit_product&id='.$qid.'');
    }
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories_features") as $cfrow)
    {
        $cat_features_array[$cfrow['category_id']][] = $cfrow['feature_id'];
    }

    $tEntry['cat_features'] = json_encode($cat_features_array);
    $tEntry['catz'] = getTree($cats, $row['category_id'], 0);
    $tEntry['features'] = $features_array;
    $tEntry['entriesImg'] = $images_array;
    $tEntry['related'] = $related_array;
    $tEntry['sOpts'] = $sOpts;
    
    $tEntry['error'] = $error_input;
    $tEntry['mode'] = "edit";
    
    $view_link = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('alt' => $row['url'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
    $prd_link = home.$view_link;
            

    $xt = $twig->loadTemplate($tpath['config/add_product'].'config/'.'add_product.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������: �������������� �������� (����������� &#8594; <small><a href="'.$prd_link.'" target="_blank">'.$prd_link.'</a></small>)',
    );
    
    print $xg->render($tVars);
}

function modify_product()
{
global $mysql;
    
    $selected_product = $_REQUEST['selected_product'];
    $subaction  =   $_REQUEST['subaction'];
    
    $id = implode( ',', $selected_product );
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "�� ������ ID!"));
    }
    
    switch($subaction) {
        case 'mass_delete'           : $del = true; break;
        case 'mass_active_add'       : $active_add = true; break;
        case 'mass_active_remove'    : $active_remove = true; break;
        case 'mass_featured_add'     : $featured_add = true; break;
        case 'mass_featured_remove'  : $featured_remove = true; break;
        case 'mass_stocked_add'      : $stocked_add = true; break;
        case 'mass_stocked_remove'   : $stocked_remove = true; break;
    }

    if(isset($del))
    {
        $mysql->query("delete from ".prefix."_eshop_products where id in ({$id})");
        $mysql->query("delete from ".prefix."_eshop_options where product_id in ({$id})");
        
        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_images WHERE product_id in ({$id})") as $irow)
        {
            delete_product_image($irow['filepath'], $irow['product_id']);
        }
        $mysql->query("delete from ".prefix."_eshop_images where product_id in ({$id})");
        
        $mysql->query("DELETE FROM ".prefix."_eshop_products_categories WHERE product_id in ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} �������!"));
    }
    
    if(isset($active_add))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET active = 1 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }

    if(isset($active_remove))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET active = 0 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }       
    
    if(isset($featured_add))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET featured = 1 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }
    
    if(isset($featured_remove))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET featured = 0 WHERE id IN ({$id})");
        
        //edirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }   

    if(isset($stocked_add))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET stocked = 1 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }
    
    if(isset($stocked_remove))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products SET stocked = 0 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }
    
    generate_catz_cache(true);     

}

function getCats($res){

    $levels = array();
    $tree = array();
    $cur = array();

    while($rows = mysql_fetch_assoc($res)){

        $cur = &$levels[$rows['id']];
        $cur['parent_id'] = $rows['parent_id'];
        $cur['name'] = $rows['name'];

        if($rows['parent_id'] == 0){
            $tree[$rows['id']] = &$cur;
        }

        else{
            $levels[$rows['parent_id']]['children'][$rows['id']] = &$cur;
        }
    }
    return $tree;
}

function getTree($arr, $flg, $l){
    $flg;
    $out = '';
    $ft = '&#8212; ';
    foreach($arr as $k=>$v){

    if($k==$flg) { $out .= '<option value="'.$k.'" selected>'.str_repeat($ft, $l).$v['name'].'</option>'; }
    else { $out .= '<option value="'.$k.'">'.str_repeat($ft, $l).$v['name'].'</option>'; }
        if(!empty($v['children'])){     
            //$l = $l + 1;
            $out .= getTree($v['children'], $flg, $l + 1);
            //$l = $l - 1;
        }
    }
    return $out;
}

function getChildIdsArray($arr, $flg){
    $out = array();
    $flg;

    foreach($arr as $k=>$v){

        if($k==$flg) {
            $out = array_merge($out, array_keys($v['children']));
            /*
            foreach($v['children'] as $k1=>$v1){
                if(array_key_exists("children",$v1)) {
                    getChildIdsArray($v, $k1);
                }
            }
            */
        }
        
        
    }
    
    return $out;
}

function upload_cat_image()
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    
    if (!empty($_FILES["image"])) {
        
        $myFile = $_FILES["image"];

        if ($myFile["error"] !== UPLOAD_ERR_OK) {
            return "";
        }
        
        // ensure a safe filename
        $img_name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);
        
        // don't overwrite an existing file
        $i = 0;
        $parts = pathinfo($img_name);
        $upload_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/';
        $upload_thumbnail_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb/';
        
        while (file_exists($upload_dir . $img_name)) {
            $i++;
            $img_name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }
        
        $extensions = array_map('trim', explode(',', pluginGetVariable('eshop', 'catz_ext_image')));
        $ext = pathinfo($myFile['name'], PATHINFO_EXTENSION);
        
        if(!in_array($ext, $extensions)) {
            return "";
        }

        // preserve file from temporary directory
        $success = move_uploaded_file($myFile["tmp_name"], $upload_dir . $img_name);
        
        if (!$success) {
             return "";
        }
        
        $tempFile = $upload_dir . $img_name;
        $extension = $parts["extension"];

        $extensions = array_map('trim', explode(',', pluginGetVariable('eshop', 'catz_ext_image')));

        if(!in_array($extension, $extensions)) {
            return "";
        }
        
        // CREATE THUMBNAIL
        if ($extension == "jpg" || $extension == "jpeg") {
            $src = imagecreatefromjpeg ( $tempFile );
        } else if ($extension == "png") {
            $src = imagecreatefrompng ( $tempFile );
        } else {
            $src = imagecreatefromgif ( $tempFile );
        }

        list ( $width, $height ) = getimagesize ( $tempFile );

        $newwidth = pluginGetVariable('eshop', 'catz_width_thumb');
        $newheight = ($height / $width) * $newwidth;
        $tmp = imagecreatetruecolor ( $newwidth, $newheight );

        imagecopyresampled ( $tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

        $thumbname = $upload_thumbnail_dir . $img_name;

        if (file_exists ( $thumbname )) {
            unlink ( $thumbname );
        }

        imagejpeg ( $tmp, $thumbname, 100 );

        imagedestroy ( $src );
        imagedestroy ( $tmp );
        
    }
    
    return $img_name;

}

function delete_cat_image($img_name)
{
global $tpl, $template, $config, $mysql, $lang, $twig;

    $upload_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/';
    $upload_thumbnail_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb/';
    
    $imgname = $upload_dir . $img_name;
    $thumbname = $upload_thumbnail_dir . $img_name;

    if (file_exists ( $imgname )) {
        unlink ( $imgname );
    }
    
    if (file_exists ( $thumbname )) {
        unlink ( $thumbname );
    }
}

function delete_product_image($img_name, $qid)
{
global $tpl, $template, $config, $mysql, $lang, $twig;

    $upload_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/'.$qid.'/';
    $upload_thumbnail_dir = dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/'.$qid.'/thumb/';
    
    $imgname = $upload_dir . $img_name;
    $thumbname = $upload_thumbnail_dir . $img_name;

    if (file_exists ( $imgname )) {
        unlink ( $imgname );
    }
    
    if (file_exists ( $thumbname )) {
        unlink ( $thumbname );
    }
}

function add_cat($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig, $parse;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_cat'), 'eshop', 1);
    
    if (isset($_REQUEST['submit']))
    {

        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        if(empty($cat_name))
        {
            $error_text[] = '�������� ��������� �� ������';
        }
        
        $description = input_filter_com(convert($_REQUEST['description']));

        $url = input_filter_com(convert($_REQUEST['url']));
        if(empty($url))
        {
            $url = strtolower($parse->translit($cat_name,1, 1));
        }
        
        if ($url) {
            if ( is_array($mysql->record("select id from ".prefix."_eshop_categories where url = ".db_squote($url)." limit 1")) ) {
                $error_text[] = '����� altname ��� ����������.';
            }
        } 

        $meta_title = input_filter_com(convert($_REQUEST['meta_title']));
        if(empty($meta_title))
        {
            $meta_title = $cat_name;
        }
        
        $meta_keywords = input_filter_com(convert($_REQUEST['meta_keywords']));
        $meta_description = input_filter_com(convert($_REQUEST['meta_description']));
        
        $parent_id = intval($_REQUEST['parent']);
                
        $position = intval($_REQUEST['position']);
        if(empty($position))
        {
            $position = 0;
        }

        $active = "1";
        
        $img_name = upload_cat_image();

        if(empty($error_text))
        {
            $mysql->query('INSERT INTO '.prefix.'_eshop_categories (name, description, url, meta_title, meta_keywords,  meta_description, parent_id, position, image, active) 
                VALUES 
                ('.db_squote($cat_name).',
                    '.db_squote($description).',
                    '.db_squote($url).',
                    '.db_squote($meta_title).',
                    '.db_squote($meta_keywords).',
                    '.db_squote($meta_description).',
                    '.db_squote($parent_id).',
                    '.db_squote($position).',
                    '.db_squote($img_name).',
                    '.intval($active).'
                )
            ');
            
            generate_catz_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_cat');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }
        
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id");
    $cats = getCats($res);

    $tEntry = array (
        'cat_name' => $cat_name,
        'description' => $description,
        
        'url' => $url,
        'meta_title' => $meta_title,
        'meta_keywords' => $meta_keywords,
        'meta_description' => $meta_description,
        
        'parent_id' => $parent_id,
        'position' => $position,
        
        'image' => $image,
        'active' => $active,
        
        'error' => $error_input,
        'catz' => getTree($cats),
    );

    $xt = $twig->loadTemplate($tpath['config/add_cat'].'config/'.'add_cat.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������: ���������� ���������',
    );
    
    print $xg->render($tVars);
}

function edit_cat($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig, $parse;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_cat'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_categories WHERE id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {

        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        if(empty($cat_name))
        {
            $error_text[] = '�������� ��������� �� ������';
        }

        $url = input_filter_com(convert($_REQUEST['url']));
        if(empty($url))
        {
            $url = strtolower($parse->translit($cat_name,1, 1));
        }

        if ($url) {
            if ( is_array($mysql->record("select id from ".prefix."_eshop_categories where url = ".db_squote($url)." and id <> ".$id." limit 1")) ) {
                $error_text[] = '����� altname ��� ����������.';
            }
        }

        $meta_title = input_filter_com(convert($_REQUEST['meta_title']));
        if(empty($meta_title))
        {
            $meta_title = $cat_name;
        }

        $meta_title = input_filter_com(convert($_REQUEST['meta_title']));
        $meta_keywords = input_filter_com(convert($_REQUEST['meta_keywords']));
        $meta_description = input_filter_com(convert($_REQUEST['meta_description']));
        
        $parent_id = intval($_REQUEST['parent']);
                
        $position = intval($_REQUEST['position']);
        if(empty($position))
        {
            $position = 0;
        }

        $active = "1";

        $img_name = upload_cat_image();
        if($img_name != "") {
            $image_sql = "image = ".db_squote($img_name).",";
        }
        else {
            $image_sql = "";
        }
        
        $image_del = intval($_REQUEST['image_del']);
        if($image_del == 1) {
            delete_cat_image($row['image']);
            $image_sql = "image = '',";
        }

        if(empty($error_text))
        {
            
            $mysql->query('UPDATE '.prefix.'_eshop_categories SET  
                name = '.db_squote($cat_name).',
                description = '.db_squote($description).', 
                url = '.db_squote($url).',
                meta_title = '.db_squote($meta_title).',
                meta_keywords = '.db_squote($meta_keywords).',
                meta_description = '.db_squote($meta_description).',
                parent_id = '.db_squote($parent_id).',
                position = '.db_squote($position).',
                '.$image_sql.'
                active = '.db_squote($active).'
                WHERE id = '.$id.'
            ');

            generate_catz_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_cat');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

        
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY id");
    $cats = getCats($res);


    $tEntry = array (
        'cat_name' => $row['name'],
        'description' => $row['description'],
        
        'url' => $row['url'],
        'meta_title' => $row['meta_title'],
        'meta_keywords' => $row['meta_keywords'],
        'meta_description' => $row['meta_description'],
        
        'parent_id' => $row['parent_id'],
        'position' => $row['position'],
        
        'image' => $row['image'],
        'active' => $row['active'],
        
        'error' => $error_input,
        'catz' => getTree($cats, $row['parent_id'], 0),
    );

    $xt = $twig->loadTemplate($tpath['config/add_cat'].'config/'.'add_cat.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������: �������������� ���������',
    );
    
    print $xg->render($tVars);
}

function del_cat($params)
{global $mysql;
    
    $id = intval($_REQUEST['id']);
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "������, �� �� ������� ��� ������ �������"));
    }
    
    $cnt_products_in_cat = $mysql->record('SELECT COUNT(*) AS cnt FROM '.prefix.'_eshop_products_categories WHERE category_id = '.db_squote($id).'');

    if($cnt_products_in_cat['cnt'] == 0) {
        $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_categories WHERE id = '.db_squote($id).' LIMIT 1');
        delete_cat_image($row['image']);
        $mysql->query("DELETE FROM ".prefix."_eshop_categories WHERE id = {$id}");
        msg(array("type" => "info", "info" => "��������� �������"));
        generate_catz_cache(true);
    }
    else {
        msg(array("type" => "info", "info" => "��������� �� ����� ���� �������, �.�. � ��� ���� ���������"));
    }

    
}

function list_cat($params)
{
global $tpl, $mysql, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_cat'), 'eshop', 1);
    
    $tVars = array();

    //get all categories
    $catz_array = array();
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id") as $row)
    {

        $catz_array[$row['id']] = 
            array('name' => $row['name'],
                'alt' => $row['url'],
                'parent' => $row['parent_id'],
                'CategoryID' => $row['id'],
                'CategoryName' => $row['name'],
                'Description' => $row['description'],                                               
                'SortOrder' => $row['position'],
                'IconFile' => $row['image']
                );
    }
    //generate menu starting with parent categories (that have a 0 parent)
    $tEntry = generate_menu(0, $catz_array);
    //var_dump($tEntry);

    $xt = $twig->loadTemplate($tpath['config/list_cat'].'config/'.'list_cat.tpl');
    
    $tVars = array( 
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������',
    );
    
    print $xg->render($tVars);

}

//function that lists categories
function generate_menu($parent, $catz_array)
{
    global $gvars;
    $has_childs = false;

    $addspaces = '';

    foreach($catz_array as $key => $value)
    {

        if ($value['parent'] == $parent) 
        {
            $view_link = checkLinkAvailable('eshop', '')?
            generateLink('eshop', '', array('alt' => $value['alt'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $value['alt']));

            
            $gvars[] = array (
                'id' => $value['CategoryID'],
                'alt' => $value['alt'],
                'cat_name' => $value['CategoryName'],
                'edit_link' => "?mod=extra-config&plugin=eshop&action=edit_cat&id=".$value['CategoryID'],
                'del_link' => "?mod=extra-config&plugin=eshop&action=del_cat&id=".$value['CategoryID'],
                'view_link' => $view_link,
                'prefix' => get_prefix($value['CategoryID']),
                'parent' => $value['parent'],
                'position' => $value['SortOrder'],
                'image' => $value['IconFile']
            );
            
            if ($key != 0) $addspaces .= '&nbsp;';

            //call function again to generate list for subcategories belonging to current category
            generate_menu($key, $catz_array);
        }
    }
    #var_dump($gvars);
    #var_dump("=========================================");
    
    #var_dump($gvars);
    
    return $gvars;
}

//get spaces to list subcategories
function get_prefix($CategoryID)
{
    global $tpl, $template, $config, $mysql, $lang, $twig, $prefixed;
    $ParentID = $mysql->result('SELECT parent_id FROM '.prefix.'_eshop_categories WHERE id = '.$CategoryID.' ');
    
    $prefixed[$CategoryID]['f'] .= '&nbsp;&nbsp;&nbsp;';
    #$add_prefix .= '&nbsp;&nbsp;&nbsp;'; 
    {
        if ($ParentID == 0) 
        { 
            $add_prefix .= ''; 
        }
        else
        {
            $prefixed[$CategoryID]['s'] .= '<img src="/engine/plugins/eshop/tpl/img/tree.gif">&nbsp;&nbsp;&nbsp;';
            $add_prefix .= '<img src="/engine/plugins/eshop/tpl/img/tree.gif">&nbsp;&nbsp;&nbsp;';
            
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories WHERE id=".$ParentID." ") as $row2)
            {
                $CategoryID2 = $row2['id'];         
                $ParentID2 = $row2['parent_id'];
            }

            get_prefix($CategoryID2);
        }
    }
    return $add_prefix;
}


function list_feature($params)
{
global $tpl, $mysql, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_feature'), 'eshop', 1);
    
    $tVars = array();

    //get all categories
    $catz_array = array();
    
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $row)
    {
        $row['edit_link'] = "?mod=extra-config&plugin=eshop&action=edit_feature&id=".$row['id'];
        $row['del_link'] = "?mod=extra-config&plugin=eshop&action=del_feature&id=".$row['id'];
        $row['foptions'] = json_decode($row['foptions'],true);
        $tEntry[] = $row;
    }
 
    $xt = $twig->loadTemplate($tpath['config/list_feature'].'config/'.'list_feature.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '��������',
    );
    
    print $xg->render($tVars);

}

function add_feature($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_feature'), 'eshop', 1);
    
    if (isset($_REQUEST['submit']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� �������� �� ������';
        }
                  
        $SQL['position'] = intval($_REQUEST['position']);
        if(empty($SQL['position']))
        {
            $SQL['position'] = 0;
        }

        $SQL['in_filter'] = intval($_REQUEST['in_filter']);
        if(empty($SQL['in_filter']))
        {
            $SQL['in_filter'] = 0;
        }
        
        $ftype = input_filter_com(convert($_REQUEST['ftype']));
        switch ($ftype) {
            case 'text':
                $SQL['ftype'] = '0';
                $SQL['fdefault'] = input_filter_com(convert($_REQUEST['text_default']));
                break;
            case 'checkbox':
                $SQL['ftype'] = '1';
                $SQL['fdefault'] = intval($_REQUEST['checkbox_default']);
                break;
            case 'select':
                $SQL['ftype'] = '2';
                $SQL['fdefault'] = input_filter_com(convert($_REQUEST['select_default']));
                $optlist = array();
                if (isset($_REQUEST['so_data']) && is_array($_REQUEST['so_data'])) {
                    foreach ($_REQUEST['so_data'] as $k => $v) {
                        if (is_array($v) && isset($v[0]) && isset($v[1]) && (($v[0] != '') || ($v[1] != ''))) {
                            if ($v[0] != '') {
                                $optlist[$v[0]] = $v[1];
                            } else {
                                $optlist[] = $v[1];
                            }
                        }
                    }
                }
                $SQL['foptions'] = json_encode($optlist);
                break;
        }

        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('INSERT INTO '.prefix.'_eshop_features SET '.implode(', ',$vnames).' ');

            $rowID = $mysql->lastid('eshop_features');
            //$rowID = $mysql->record("select LAST_INSERT_ID() as id");

            $ids = $_REQUEST['feature_categories'];

            foreach($ids as $id)
            {
              $mysql->query('INSERT INTO '.prefix.'_eshop_categories_features (category_id, feature_id) 
                VALUES 
                ('.db_squote($id).',
                 '.db_squote($rowID).'
                )
               ');
            }
            
            generate_features_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_feature');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }
        
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY id");
    $cats = getCats($res);

    foreach ($SQL as $k => $v) { 
        $tEntry[$k] = $v;
    }
    
    $tEntry['error'] = $error_input;
    $tEntry['catz'] = getTree($cats);

    $xt = $twig->loadTemplate($tpath['config/add_feature'].'config/'.'add_feature.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'',
        'mode'          => "add", 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '��������: ���������� ��������',
    );
    
    print $xg->render($tVars);
}

function edit_feature($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_feature'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_features WHERE id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� �������� �� ������';
        }
                  
        $SQL['position'] = intval($_REQUEST['position']);
        if(empty($SQL['position']))
        {
            $SQL['position'] = 0;
        }

        $SQL['in_filter'] = intval($_REQUEST['in_filter']);
        if(empty($SQL['in_filter']))
        {
            $SQL['in_filter'] = 0;
        }
        
        $ftype = input_filter_com(convert($_REQUEST['ftype']));
        switch ($ftype) {
            case 'text':
                $SQL['ftype'] = '0';
                $SQL['fdefault'] = input_filter_com(convert($_REQUEST['text_default']));
                break;
            case 'checkbox':
                $SQL['ftype'] = '1';
                $SQL['fdefault'] = intval($_REQUEST['checkbox_default']);
                break;
            case 'select':
                $SQL['ftype'] = '2';
                $SQL['fdefault'] = input_filter_com(convert($_REQUEST['select_default']));
                $optlist = array();
                if (isset($_REQUEST['so_data']) && is_array($_REQUEST['so_data'])) {
                    foreach ($_REQUEST['so_data'] as $k => $v) {
                        if (is_array($v) && isset($v[0]) && isset($v[1]) && (($v[0] != '') || ($v[1] != ''))) {
                            if ($v[0] != '') {
                                $optlist[$v[0]] = $v[1];
                            } else {
                                $optlist[] = $v[1];
                            }
                        }
                    }
                }
                $SQL['foptions'] = json_encode($optlist);
                break;
        }

        if(empty($error_text))
        {

            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('UPDATE '.prefix.'_eshop_features SET '.implode(', ',$vnames).' WHERE id = \''.intval($id).'\' ');

            $mysql->query("delete from ".prefix."_eshop_categories_features where feature_id in ({$id})");
            $ids = $_REQUEST['feature_categories'];

            foreach($ids as $id_x)
            {
              $mysql->query('INSERT INTO '.prefix.'_eshop_categories_features (category_id, feature_id) 
                VALUES 
                ('.db_squote($id_x).',
                 '.db_squote($id).'
                )
               ');
            }
            
            generate_features_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_feature');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

        
    $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY id");
    $cats = getCats($res);

    $cat_ids = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories_features WHERE feature_id = ".db_squote($id)."") as $frow)
    {
        $cat_ids[] = $frow['category_id'];
    }

    foreach ($row as $k => $v) { 
        $tEntry[$k] = $v;
    }
    
    $tEntry['error'] = $error_input;
    $tEntry['catz'] = getMultiTree($cats, $cat_ids, 0);
    $tEntry['foptions'] = json_decode($tEntry['foptions'], true);

    $sOpts = array();
    $fNum = 1;
    if ($tEntry['ftype'] == '2') {
        if (is_array($tEntry['foptions']))
            foreach ($tEntry['foptions'] as $k => $v) {
                array_push($sOpts, '<tr><td><input size="12" name="so_data['.($fNum).'][0]" type="text" value="'.($tEntry['foptions']?htmlspecialchars($k, ENT_COMPAT | ENT_HTML401, 'cp1251'):'').'"/></td><td><input type="text" size="55" name="so_data['.($fNum).'][1]" value="'.htmlspecialchars($v, ENT_COMPAT | ENT_HTML401, 'cp1251').'"/></td><td><a href="#" onclick="return false;"><img src="'.skins_url.'/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
                $fNum++;
            }
    }
    if (!count($sOpts)) {
        array_push($sOpts, '<tr><td><input size="12" name="so_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="so_data[1][1]" value=""/></td><td><a href="#" onclick="return false;"><img src="'.skins_url.'/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
    }
    
    $tEntry['sOpts'] = implode("\n", $sOpts);


    $xt = $twig->loadTemplate($tpath['config/add_feature'].'config/'.'add_feature.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'',
        'mode'    => "edit",  
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '��������: �������������� ��������',
    );
    
    print $xg->render($tVars);
}

function getMultiTree($arr, $flg, $l){
    $flg;
    $out = '';
    $ft = '&#8212; ';

    foreach($arr as $k=>$v){
        if(in_array($k, $flg)) { $out .= '<option value="'.$k.'" selected>'.str_repeat($ft, $l).$v['name'].'</option>'; }
        else { $out .= '<option value="'.$k.'">'.str_repeat($ft, $l).$v['name'].'</option>'; }
            if(!empty($v['children'])){     
                //$l = $l + 1;
                $out .= getMultiTree($v['children'], $flg, $l + 1);
                //$l = $l - 1;
            }
    }
    return $out;
}

function modify_feature()
{
global $mysql;
    
    $selected_feature = $_REQUEST['selected_feature'];
    $subaction  =   $_REQUEST['subaction'];
    
    $id = implode( ',', $selected_feature );
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "�� ������ ID!"));
    }
    
    switch($subaction) {
        case 'mass_delete'       : $del = true; break;
    }

    if(isset($del))
    {
        $mysql->query("delete from ".prefix."_eshop_features where id in ({$id})");
        $mysql->query("delete from ".prefix."_eshop_categories_features where feature_id in ({$id})");
        $mysql->query("delete from ".prefix."_eshop_options where feature_id in ({$id})");
        msg(array("type" => "info", "info" => "������ � ID${id} �������!"));
    }
    
    generate_features_cache(true);
}


function list_order($params)
{
global $tpl, $mysql, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_order'), 'eshop', 1);
    
    $tVars = array();

    // Load admin page based cookies
    $admCookie = admcookie_get();

    $fName          = $_REQUEST['fname'];
    $fPhone         = $_REQUEST['fphone'];
    $fAdress        = $_REQUEST['fadress'];
    $fCustomerName  = $_REQUEST['an'];

    // Selected date
    $fDateStart     = $_REQUEST['dr1'];
    $fDateEnd       = $_REQUEST['dr2'];
    
    if($fDateStart == 'DD.MM.YYYY') $fDateStart = '';
    if($fDateEnd == 'DD.MM.YYYY') $fDateEnd = '';

    $news_per_page  = isset($_REQUEST['rpp'])?intval($_REQUEST['rpp']):intval($admCookie['eshop']['pp_order']);
    // - Set default value for `Records Per Page` parameter
    if (($news_per_page < 2)||($news_per_page > 2000))
        $news_per_page = 10;
    
    // - Save into cookies current value
    $admCookie['eshop']['pp_order'] = $news_per_page;
    admcookie_set($admCookie);

    $conditions = array();
    if ($fName) {
        array_push($conditions, "name LIKE ".db_squote("%".$fName."%"));
    }
    
    if ($fPhone) {
        array_push($conditions, "phone LIKE ".db_squote("%".$fPhone."%"));
    }

    if ($fAdress) {
        array_push($conditions, "address LIKE ".db_squote("%".$fAdress."%"));
    }
    
    if ($fCustomerName) {
        $sqlQCustomer = "SELECT id FROM ".prefix."_users where name = ".db_squote($fCustomerName).";";
        $customer_id = $mysql->result($sqlQCustomer);
        array_push($conditions, "author_id = ".db_squote($customer_id));
    }
    
    if ($fDateStart && $fDateEnd) {
        $fDateEndWithTime = $fDateEnd." 23:59:59";
        array_push( $conditions, "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateStart).",'%d.%m.%Y')) AND UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateEndWithTime).",'%d.%m.%Y %H:%i:%s'))" );
    }
    elseif ($fDateStart) {
        array_push($conditions, "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateStart).",'%d.%m.%Y')) AND NOW()" );
    }
    elseif ($fDateEnd) {
        $fDateEndWithTime = $fDateEnd." 23:59:59";
        array_push($conditions, "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('01.01.1970','%d.%m.%Y')) AND UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateEndWithTime).",'%d.%m.%Y %H:%i:%s'))" );
    }

    $fSort = " ORDER BY id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_orders ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT * ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";
    
    //$sqlQCount = "SELECT COUNT(p.id) FROM ng_eshop_products p ORDER BY p.id DESC";
    //var_dump($sqlQ);
    
    $pageNo     = intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1)    $pageNo = 1;
    if (!$start_from)   $start_from = ($pageNo - 1)* $news_per_page;
    
    $count = $mysql->result($sqlQCount);
    $countPages = ceil($count / $news_per_page);

    foreach ($mysql->select($sqlQ.' LIMIT '.$start_from.', '.$news_per_page) as $row)
    {
        
        $row['edit_link'] = "?mod=extra-config&plugin=eshop&action=edit_order&id=".$row['id'];
        $tEntry[] = $row;
        
    }
 
    $xt = $twig->loadTemplate($tpath['config/list_order'].'config/'.'list_order.tpl');
    
    $tVars = array(
        'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=eshop&action=list_order'.($news_per_page?'&rpp='.$news_per_page:'').($fName?'&fname='.$fName:'').($fPhone?'&fphone='.$fPhone:'').($fAdress?'&fadress='.$fAdress:'').'&page=%page%')),
        'rpp'           =>  $news_per_page,
        'fname'         =>  secure_html($fName),
        'fphone'            =>  secure_html($fPhone),
        'fadress'           =>  secure_html($fAdress),
        'an'           =>  secure_html($fCustomerName),
        'fDateStart'            =>  $fDateStart?$fDateStart:'',
        'fDateEnd'          =>  $fDateEnd?$fDateEnd:'',
        'entries' => isset($tEntry)?$tEntry:'',
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������',
    );
    
    print $xg->render($tVars);

}

function edit_order($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_order'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT *, o.id as id, o.ip as ip, o.name as name, u.name as author, o.paid as paid FROM '.prefix.'_eshop_orders o LEFT JOIN '.prefix.'_users u ON o.author_id = u.id WHERE o.id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {

        $name = input_filter_com(convert($_REQUEST['name']));
        $email = input_filter_com(convert($_REQUEST['email']));
        $phone = input_filter_com(convert($_REQUEST['phone']));
        $address = input_filter_com(convert($_REQUEST['address']));
        
        $comment = input_filter_com(convert($_REQUEST['comment']));
        $paid = input_filter_com(convert($_REQUEST['paid']));
  
        if(empty($error_text))
        {

            $mysql->query('UPDATE '.prefix.'_eshop_orders SET  
                name = '.db_squote($name).',
                email = '.db_squote($email).',
                phone = '.db_squote($phone).',
                address = '.db_squote($address).',
                comment = '.db_squote($comment).',
                paid = '.db_squote($paid).'
                WHERE id = '.$id.'
            ');
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_order');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }


    $filter = array();
    if ($id) {
        $filter []= '(order_id = '.db_squote($id).')';
    }
    
    $basket = array();
    foreach ($mysql->select("select * from ".prefix."_eshop_order_basket where ".join(" or ", $filter), 1) as $rec) {
                $total += round($rec['price'] * $rec['count'], 2);

                $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                $rec['xfields'] = unserialize($rec['linked_fld']);
                unset($rec['linked_fld']);

                $basket []= $rec;
    }
    
    $purchases = array();
    foreach ($mysql->select("select * from ".prefix."_eshop_purchases where ".join(" or ", $filter), 1) as $prow) {
                $prow['info'] = json_decode($prow['info'], true);
                foreach($prow['info'] as $k_info => $v_info) {
                    $prow['info_string'] .= $k_info." => ".$v_info."<br/>";
                }
                $purchases []= $prow;
    }
    
    $row['profile_link'] = checkLinkAvailable('uprofile', 'show')?
                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
    
    $tEntry = $row;
    $tEntry['error'] = $error_input;
    $tEntry['basket'] = $basket;
    $tEntry['purchases'] = $purchases;
    $tEntry['basket_total'] = $total;

    $xt = $twig->loadTemplate($tpath['config/add_order'].'config/'.'add_order.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '�����: �������� ������ (ID: '.$id.')',
    );
    
    print $xg->render($tVars);
}

function modify_order()
{
global $mysql;
    
    $selected_order = $_REQUEST['selected_order'];
    $subaction  =   $_REQUEST['subaction'];
    
    $id = implode( ',', $selected_order );
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "�� ������ ID!"));
    }
    
    switch($subaction) {
        case 'mass_delete'       : $del = true; break;
    }

    if(isset($del))
    {
        $mysql->query("delete from ".prefix."_eshop_orders where id in ({$id})");
        $mysql->query("delete from ".prefix."_eshop_order_basket where order_id in ({$id})");
        msg(array("type" => "info", "info" => "������ � ID${id} �������!"));
    }
}

function list_comment($params)
{
global $tpl, $mysql, $twig, $parse, $config;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_comment'), 'eshop', 1);
    
    $tVars = array();
    
    $news_per_page  = isset($_REQUEST['rpp'])?intval($_REQUEST['rpp']):intval($admCookie['eshop']['pp_comment']);
    // - Set default value for `Records Per Page` parameter
    if (($news_per_page < 2)||($news_per_page > 2000))
        $news_per_page = 10;
    
    // - Save into cookies current value
    $admCookie['eshop']['pp_comment'] = $news_per_page;
    admcookie_set($admCookie);

    $conditions = array();

    $fSort = "ORDER BY c.postdate DESC";
    $sqlQPart = "from ".prefix."_eshop_products_comments c LEFT JOIN ".prefix."_users u ON c.author_id = u.id LEFT JOIN ".prefix."_eshop_products p ON c.product_id = p.id ".(count($conditions)?"where ".implode(" AND ", $conditions):'').' '.$fSort;
    $sqlQ = "select c.id as cid, u.id as uid, u.name as uname, c.name as name, p.id as product_id, p.url as url, p.name as title, c.mail as mail, c.postdate as postdate, c.author as author, c.author_id as author_id, u.avatar as avatar, c.reg as reg, c.text as text, c.status as status ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";
    
    $pageNo     = intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1)    $pageNo = 1;
    if (!$start_from)   $start_from = ($pageNo - 1)* $news_per_page;
    
    $count = $mysql->result($sqlQCount);
    $countPages = ceil($count / $news_per_page);
    
    foreach ($mysql->select($sqlQ.' LIMIT '.$start_from.', '.$news_per_page) as $row)
    {
        // Add [hide] tag processing
        $text   = $row['text'];

        if ($config['use_bbcodes'])         { $text = $parse -> bbcodes($text); }
        if ($config['use_htmlformatter'])   { $text = $parse -> htmlformatter($text); }
        if ($config['use_smilies'])         { $text = $parse -> smilies($text); }

        if ($config['use_avatars']) {
            if ($row['avatar']) {
                $avatar = avatars_url."/".$row['avatar'];
            } else {
                    $avatar = $noAvatarURL;
            }
        } else {
            $avatar = '';
        }
        
        $view_link = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        
        $tEntries[] = array (
                'id' => $row['cid'],
                'mail' =>   $row['mail'],
                'author' => $row['name'],
                'date' => $row['postdate'],
                'profile_link' => checkLinkAvailable('uprofile', 'show')?
                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id'])),
                'avatar' => $avatar,
                'name' => $row['uname'],
                'commenttext' => $text,
                'title' => $row['title'],
                'view_link' => $view_link,
                'product_edit_link' => "?mod=extra-config&plugin=eshop&action=edit_product&id=".$row['product_id']."",
                'reg' => $row['reg'],
                'status' => $row['status'],
                );
                
    }
 
    $xt = $twig->loadTemplate($tpath['config/list_comment'].'config/'.'list_comment.tpl');

    $tVars = array(
        'entries' => isset($tEntries)?$tEntries:'',
        'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=eshop&action=list_comment'.($news_per_page?'&rpp='.$news_per_page:'').'&page=%page%')),
        'rpp'   =>  $news_per_page,
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '�����������',
    );
    
    print $xg->render($tVars);

}

function modify_comment()
{
global $mysql;
    
    $selected_comment = $_REQUEST['selected_comment'];
    $subaction  =   $_REQUEST['subaction'];
    
    $id = implode( ',', $selected_comment );
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "�� ������ ID!"));
    }
    
    switch($subaction) {
        case 'mass_delete'          : $del = true; break;
        case 'mass_active_add'      : $active_add = true; break;
        case 'mass_active_remove'   : $active_remove = true; break;
    }

    if(isset($del))
    {
        foreach ($selected_comment as $com_id) {
            $com_row = $mysql->record("SELECT * FROM ".prefix."_eshop_products_comments WHERE id=".db_squote($com_id)." ");
            $mysql->query("delete from ".prefix."_eshop_products_comments where id = ".db_squote($com_id)." ");
            $mysql->query("update ".prefix."_eshop_products set comments = comments - 1 where id = ".db_squote($com_row['product_id'])." ");
        }
        
        msg(array("type" => "info", "info" => "������ � ID${id} �������!"));
    }
    
    if(isset($active_add))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products_comments SET status = 1 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }

    if(isset($active_remove))
    {
        $mysql->query("UPDATE ".prefix."_eshop_products_comments SET status = 0 WHERE id IN ({$id})");
        
        //redirect_eshop('?mod=extra-config&plugin=eshop');
        msg(array("type" => "info", "info" => "������ � ID ${id} ���������!"));
    }    
    
}


function add_currency($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_currencies'), 'eshop', 1);

    if (isset($_REQUEST['submit']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� ������ �� ������';
        }
        
        $SQL['sign'] = input_filter_com(convert($_REQUEST['sign']));
        if(empty($SQL['sign']))
        {
            $error_text[] = '���� ������ �� �����';
        }
        
        $SQL['code'] = input_filter_com(convert($_REQUEST['code']));
        if(empty($SQL['code']))
        {
            $error_text[] = '��� ������ �� �����';
        }

        $SQL['rate_from'] = input_filter_com(convert($_REQUEST['rate_from']));
        if(empty($SQL['rate_from']))
        {
            $error_text[] = '��������� �� ������';
        }
        
        $SQL['rate_to'] = 1;
        $SQL['cents'] = 1;
        $SQL['position'] = input_filter_com(convert($_REQUEST['position']));
        if($SQL['position'] == "")
        {
            $SQL['position'] = 0;
        }
        $SQL['enabled'] = input_filter_com(convert($_REQUEST['enabled']));
        
        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('INSERT INTO '.prefix.'_eshop_currencies SET '.implode(', ',$vnames).' ');
            generate_currency_cache(true);
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_currencies');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    foreach ($SQL as $k => $v) { 
        $tEntry[$k] = $v;
    }
    
    $tEntry['error'] = $error_input;
    $tEntry['mode'] = "add";

    $xt = $twig->loadTemplate($tpath['config/add_currencies'].'config/'.'add_currencies.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );

    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������: ���������� ������',
    );
    
    print $xg->render($tVars);
}

function edit_currency($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_currencies'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_currencies WHERE id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {

        $SQL['name'] = input_filter_com(convert($_REQUEST['name']));
        if(empty($SQL['name']))
        {
            $error_text[] = '�������� ������ �� ������';
        }
        
        $SQL['sign'] = input_filter_com(convert($_REQUEST['sign']));
        if(empty($SQL['sign']))
        {
            $error_text[] = '���� ������ �� �����';
        }
        
        $SQL['code'] = input_filter_com(convert($_REQUEST['code']));
        if(empty($SQL['code']))
        {
            $error_text[] = '��� ������ �� �����';
        }

        $SQL['rate_from'] = input_filter_com(convert($_REQUEST['rate_from']));
        if(empty($SQL['rate_from']))
        {
            $error_text[] = '��������� �� ������';
        }
        
        $SQL['rate_to'] = 1;
        $SQL['cents'] = 1;
        $SQL['position'] = input_filter_com(convert($_REQUEST['position']));
        if($SQL['position'] == "")
        {
            $SQL['position'] = 0;
        }
        $SQL['enabled'] = input_filter_com(convert($_REQUEST['enabled']));
        
        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('UPDATE '.prefix.'_eshop_currencies SET '.implode(', ',$vnames).' WHERE id = \''.intval($id).'\' ');
            
            generate_currency_cache(true);
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_currencies');
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    foreach ($row as $k => $v) { 
        $tEntry[$k] = $v;
    }
    
    $tEntry['error'] = $error_input;
    $tEntry['mode'] = "edit";

    $xt = $twig->loadTemplate($tpath['config/add_currencies'].'config/'.'add_currencies.tpl');
    
    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������: �������������� ������',
    );
    
    print $xg->render($tVars);
}

function del_currency($params)
{global $mysql;
    
    $id = intval($_REQUEST['id']);
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "������, �� �� ������� ��� ������ �������"));
    }
    
    if( $id == "1" )
    {
        return msg(array("type" => "error", "text" => "������, �� �� ������ ������ �������� ������"));
    }
    
    $mysql->query("DELETE FROM ".prefix."_eshop_currencies WHERE id = {$id}");
    generate_currency_cache(true);
    msg(array("type" => "info", "info" => "������ �������"));
    
}

function list_currencies($params)
{
global $tpl, $mysql, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_currencies'), 'eshop', 1);
    
    $tVars = array();

    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_currencies ORDER BY position, id") as $row)
    {
    
        $row['edit_link'] = "?mod=extra-config&plugin=eshop&action=edit_currency&id=".$row['id']."";
        $row['del_link'] = "?mod=extra-config&plugin=eshop&action=del_currency&id=".$row['id']."";
        $tEntry[] = $row;

    }

    $xt = $twig->loadTemplate($tpath['config/list_currencies'].'config/'.'list_currencies.tpl');
    
    $tVars = array( 
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������',
    );
    
    print $xg->render($tVars);

}

function list_payment($params)
{
global $tpl, $mysql, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_payment'), 'eshop', 1);
    
    $tVars = array();

    if ($handle = opendir(dirname(__FILE__).'/payment')) {
        $blacklist = array('.', '..');
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, $blacklist)) {
                $row['name'] = $file;
                $row['edit_link'] = "?mod=extra-config&plugin=eshop&action=edit_payment&id=".$file."";
                $tEntry[] = $row;
            }
        }
        closedir($handle);
    }

    $xt = $twig->loadTemplate($tpath['config/list_payment'].'config/'.'list_payment.tpl');
    
    $tVars = array( 
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������� ������',
    );
    
    print $xg->render($tVars);

}

function edit_payment($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_currencies'), 'eshop', 1);
    
    $id = $_REQUEST['id'];
    $config_filename = dirname(__FILE__).'/payment/'.$id.'/config.php';

    if (file_exists($config_filename)) {
        include_once($config_filename);
        payment_config($id);
    } else {
        $error =  "���� $config_filename �� ����������";
        msg(array("type" => "error", "text" => $error));
    }
}

function urls()
{global $tpl, $mysql, $twig, $CurrentHandler, $SUPRESS_TEMPLATE_SHOW;
    $tpath = locatePluginTemplates(array('config/main', 'config/urls'), 'eshop', 1);

    $url = pluginGetVariable('eshop', 'url');
    
    if (isset($_REQUEST['submit']))
    {
        if(($url != '1') && ($_REQUEST['url'] == '1'))
        {
            create_urls();
        } elseif(($_REQUEST['url'] == '0')) {
            
            remove_urls();
        }
        
        pluginSetVariable('eshop', 'url', intval($_REQUEST['url']));
        pluginsSaveConfig();
        
        redirect_eshop('?mod=extra-config&plugin=eshop&action=urls');
    }

    $url = '<option value="0" '.(empty($url)?'selected':'').'>���</option><option value="1" '.(!empty($url)?'selected':'').'>��</option>';

    $xt = $twig->loadTemplate($tpath['config/urls'].'config/'.'urls.tpl');
    
    $tVars = array(
        'info' => $url 
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '��������� ���',
    );
    
    print $xg->render($tVars);

}


function automation()
{global $tpl, $mysql, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/automation'), 'eshop', 1);

    if (isset($_REQUEST['yml_url']) && !empty($_REQUEST['yml_url']) && isset($_REQUEST['import']))
    {
        
        import_yml($_REQUEST['yml_url']);
        //$import_str = implode('<br/>',$_SESSION['import_yml']);
        //$info = "������ YML ������� ��������<br/><br/>".$import_str;
        $info = "������ YML ������� ��������";
        
        msg(array("type" => "info", "info" => $info));
    }
    
    if (isset($_REQUEST['currency']))
    {

        $rates_str = update_currency();
        $info = "������ ���������<br/><br/>".$rates_str;
        
        msg(array("type" => "info", "info" => $info));
    }
    
    if (isset($_REQUEST['change_price']))
    {
        $change_price_type = intval($_REQUEST['change_price_type']);
        $change_price_qnt = intval($_REQUEST['change_price_qnt']);
        
        update_prices($change_price_type, $change_price_qnt);
        $info = "���� ���������<br/>";
        msg(array("type" => "info", "info" => $info));
    }

    if (isset($_REQUEST['export_csv']))
    {
        
        $SUPRESS_TEMPLATE_SHOW = 1;
        $SUPRESS_MAINBLOCK_SHOW = 1;

        require_once dirname(__FILE__) . '/csv_lib/CsvImportInterface.php';
        require_once dirname(__FILE__) . '/csv_lib/CsvImport.php';
        require_once dirname(__FILE__) . '/csv_lib/CsvExportInterface.php';
        require_once dirname(__FILE__) . '/csv_lib/CsvExport.php';

        $export = new CsvExport();
        
        $cat_array = array();

        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_categories ORDER BY position ASC') as $cat_row)
        {

                $catlink = checkLinkAvailable('eshop', '')?
                    generateLink('eshop', '', array('cat' => $cat_row['id'])):
                    generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $cat_row['id']));
                
                $cat_array[] = array (
                    'id' => $cat_row['id'],
                    'url' => $cat_row['url'],
                    'image' => $cat_row['image'],
                    
                    'name' => $cat_row['name'],
                    'description' => $cat_row['description'],
                    
                    'parent_id' => $cat_row['parent_id'],
                    'position' => $cat_row['position'],
                    
                    'meta_title' => $cat_row['meta_title'],
                    'meta_keywords' => $cat_row['meta_keywords'],
                    'meta_description' => $cat_row['meta_description'],
                    
                    'link' => $catlink,

                 );
                 
        }
        
        $conditions = array();
        
        array_push($conditions, "p.active = 1");

        //$limitCount = "10000";

        $fSort = " GROUP BY p.id ORDER BY p.id DESC ";
        $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
        $sqlQ = "SELECT p.id AS id, p.url AS url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.stocked AS stocked, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category ".$sqlQPart;
        
        $entries = array();
         
        foreach ($mysql->select($sqlQ) as $row)
        {
            
            $entriesImg = array();
            foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images i WHERE i.product_id = '.$row['id'].' ') as $row2)
            {
                $entriesImg[] = $row2['filepath'];
            }
            
            $entriesVariants = array();
            foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_variants v WHERE v.product_id = '.$row['id'].' ') as $vrow)
            {
                $entriesVariants[] = $vrow;
            }

            $options_array = array();
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_options LEFT JOIN ".prefix."_eshop_features ON ".prefix."_eshop_features.id=".prefix."_eshop_options.feature_id WHERE ".prefix."_eshop_options.product_id = ".$row['id']." ORDER BY position, id") as $orow)
            {
                $options_array[$orow['id']] = $orow['value'];
            }
            
            $xf_name_id = array();
            $features_array = array();
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
            {
                $frow['value'] = $options_array[$frow['id']];
                $frow['foptions'] = json_decode($frow['foptions'], true);
                $features_array["xfields_".$frow['name']] = $frow['value'];
                $xf_name_id[$frow['name']] = $frow['id'];
            }

            $images_comma_separated = implode(",", $entriesImg);

            $entry = array();
            
            $entry = array (
                'id' => $row['id'],
                'code' => $row['code'],
                'url' => $row['url'],
                'name' => $row['name'],
                
                'variants' => $entriesVariants,
                
                'annotation' => $row['annotation'],
                'body' => $row['body'],
                
                'active' => $row['active'],
                'featured' => $row['featured'],
                'stocked' => $row['stocked'],

                'meta_title' => $row['meta_title'],
                'meta_keywords' => $row['meta_keywords'],
                'meta_description' => $row['meta_description'],
                
                'date' => (empty($row['date']))?'':$row['date'],
                'editdate' => (empty($row['editdate']))?'':$row['editdate'],

                'cat_name' => $row['category'],
                'cid' => $row['cid'],
                
                'images' => $images_comma_separated,
            );
            
            foreach ($features_array as $fk => $fv)
            {
                $entry[$fk] = $fv;
            }
            
            $entries[] = $entry;
            
        }
        
        $count = 0;
        foreach ($entries as $entry)
        {
            foreach ($entry['variants'] as $variant) {
                
                $entry_row = array();
                $entry_row = $entry;
                unset($entry_row['variants']);
                
                $entry_row['v_id'] = $variant['id'];
                $entry_row['v_sku'] = $variant['sku'];
                $entry_row['v_name'] = $variant['name'];
                $entry_row['v_price'] = $variant['price'];
                $entry_row['v_compare_price'] = $variant['compare_price'];
                $entry_row['v_stock'] = $variant['stock'];
                $entry_row['v_amount'] = $variant['amount'];
                
                if($count == 0) {
                    $export->setHeader(array_keys($entry_row));
                }
                $count += 1;
                
                $export->append(
                    array(
                        $entry_row
                    )
                );
                
                unset($entry_row);
                
            }
        }

        $export->export('products.csv', ';');
        die();
    }

    if (isset($_REQUEST['import_csv']))
    {
        
        $SUPRESS_TEMPLATE_SHOW = 1;
        $SUPRESS_MAINBLOCK_SHOW = 1;
        
        if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
        {
            
            require_once dirname(__FILE__) . '/csv_lib/CsvImportInterface.php';
            require_once dirname(__FILE__) . '/csv_lib/CsvImport.php';
            require_once dirname(__FILE__) . '/csv_lib/CsvExportInterface.php';
            require_once dirname(__FILE__) . '/csv_lib/CsvExport.php';

            $import = new CsvImport();
            
            $filepath = dirname(__FILE__)."/import/".$_FILES["filename"]["name"];
            move_uploaded_file($_FILES["filename"]["tmp_name"], $filepath);
            $import->setFile($filepath,10000,";");
            $items = $import->getRows();

            $xf_name_id = array();
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
            {
                $xf_name_id[$frow['name']] = $frow['id'];
            }

            foreach ($items as $iv)
            {
                
                $current_time = time();
                
                $id = $iv['id'];
                $code = empty($iv['code'])?"":$iv['code'];
                $url = empty($iv['url'])?"":$iv['url'];
                $name = empty($iv['name'])?"":$iv['name'];
                
                $v_id = empty($iv['v_id'])?"":$iv['v_id'];
                $v_sku = empty($iv['v_sku'])?"":$iv['v_sku'];
                $v_name = empty($iv['v_name'])?"":$iv['v_name'];
                $v_price = empty($iv['v_price'])?"":$iv['v_price'];
                $v_compare_price = empty($iv['v_compare_price'])?"":$iv['v_compare_price'];
                $v_stock = empty($iv['v_stock'])?"":$iv['v_stock'];
                $v_amount = empty($iv['v_amount'])?"":$iv['v_amount'];
                
                if($v_amount == '') {
                        $v_amount = 'NULL';
                }
                
                $annotation = empty($iv['annotation'])?"":$iv['annotation'];
                $body = empty($iv['body'])?"":$iv['body'];
                $active = empty($iv['active'])?"1":$iv['active'];
                $featured = empty($iv['featured'])?"0":$iv['featured'];
                $stocked = empty($iv['stocked'])?"0":$iv['stocked'];
                $meta_title = empty($iv['meta_title'])?"":$iv['meta_title'];
                $meta_keywords = empty($iv['meta_keywords'])?"":$iv['meta_keywords'];
                $meta_description = empty($iv['meta_description'])?"":$iv['meta_description'];
                $date = empty($iv['date'])?$current_time:$iv['date'];
                $editdate = empty($iv['editdate'])?$current_time:$iv['editdate'];
                $cat_name = empty($iv['cat_name'])?"":$iv['cat_name'];
                $cid = empty($iv['cid'])?"0":$iv['cid'];
                //$images = explode(',',$iv['images']);
                
                $xfields_items = array();
                foreach ($iv as $xfk => $xfv)
                {
                    preg_match("/xfields_/",$xfk,$find_xf);
                    if(isset($find_xf[0])) {
                        $xf_name = str_replace('xfields_','',$xfk);
                        $xf_id = $xf_name_id[$xf_name];
                        $xfields_items[$xf_id] = $xfv;
                    }
                    
                }
                
                if($iv['id'] != "") {
                    
                    $product_row = $mysql->record("SELECT * FROM ".prefix."_eshop_products WHERE id=".db_squote($iv['id'])." ");

                    if(empty($product_row)) {
                        if ($url != "") {
                            if ( !is_array($mysql->record("select id from ".prefix."_eshop_products where url = ".db_squote($product_row["url"])." limit 1")) ) {
                                $mysql->query("INSERT INTO ".prefix."_eshop_products (`id`, `code`, `url`, `name`, `annotation`, `body`, `active`, `featured`, `stocked`, `meta_title`, `meta_keywords`, `meta_description`, `date`, `editdate`) VALUES ('$id','$code','$url','$name','$annotation','$body','$active','$featured','$stocked','$meta_title','$meta_keywords','$meta_description','$date','$editdate')");
                                
                                $qid = $mysql->lastid('eshop_products');
                                
                                import_upload_images($qid);
                                
                                if(!empty($xfields_items)) {
                                    foreach ($xfields_items as $f_key => $f_value) {
                                        if($f_value != '') {
                                            $mysql->query("INSERT INTO ".prefix."_eshop_options (`product_id`, `feature_id`, `value`) VALUES ('$qid','$f_key','$f_value')");
                                        }
                                    }
                                }
                                
                                $category_id = intval($cid);
                                
                                if($category_id != 0) {
                                    $mysql->query("INSERT INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
                                }
                                
                                $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `sku`, `name`, `price`, `compare_price`, `stock`, `amount`) VALUES ('$qid', '$v_sku', '$v_name', '$v_price', '$v_compare_price', '$v_stock', '$v_amount')");
                                
                            }
                        }
                    }
                    else {
                        if ($product_row["url"]) {
                            if ( is_array($mysql->record("select id from ".prefix."_eshop_products where url = ".db_squote($url)." limit 1")) ) {
                                $mysql->query("REPLACE INTO ".prefix."_eshop_products (`id`, `code`, `url`, `name`, `annotation`, `body`, `active`, `featured`, `stocked`, `meta_title`, `meta_keywords`, `meta_description`, `date`, `editdate`) VALUES ('$id','$code','$url','$name','$annotation','$body','$active','$featured','$stocked','$meta_title','$meta_keywords','$meta_description','$date','$editdate')");
                                
                                $qid = $product_row["id"];
                                
                                import_upload_images($qid);
                                
                                if(!empty($xfields_items)) {
                                    //$mysql->query("DELETE FROM ".prefix."_eshop_options WHERE product_id='$qid'");
                                    foreach ($xfields_items as $f_key => $f_value) {
                                        if($f_value != '') {
                                            $mysql->query("REPLACE INTO ".prefix."_eshop_options (`product_id`, `feature_id`, `value`) VALUES ('$qid','$f_key','$f_value')");
                                        }
                                    }
                                }
                                
                                $category_id = intval($cid);
                                
                                if($category_id != 0) {
                                    //$mysql->query("DELETE FROM ".prefix."_eshop_products_categories WHERE product_id='$qid'");
                                    $mysql->query("REPLACE INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
                                }
                                else {
                                    $mysql->query("DELETE FROM ".prefix."_eshop_products_categories WHERE product_id='$qid'");
                                }
                                
                                $SQLv = array();
                                $SQLv['product_id'] = $qid;
                                $SQLv['sku'] = $v_sku;
                                $SQLv['name'] = $v_name;
                                $SQLv['price'] = $v_price;
                                $SQLv['compare_price'] = $v_compare_price;
                                $SQLv['stock'] = $v_stock;
                                $SQLv['amount'] = $v_amount;
                                
                                foreach ($SQLv as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
                                
                                if($v_id != "") {
                                    $mysql->query('UPDATE '.prefix.'_eshop_variants SET '.implode(', ',$vnames).' WHERE id = \''.intval($v_id).'\'  ');
                                }
                                else {
                                    $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `sku`, `name`, `price`, `compare_price`, `stock`, `amount`) VALUES ('$qid', '$v_sku', '$v_name', '$v_price', '$v_compare_price', '$v_stock', '$v_amount')");
                                }
                                
                                //$mysql->query("DELETE FROM ".prefix."_eshop_variants WHERE product_id='$qid'");
                                //$mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `price`, `compare_price`, `stock`) VALUES ('$qid', '$price', '$compare_price', '$stock')");
                                
                            }
                        }
                    }
                }
                else {
                    
                    $mysql->query("INSERT INTO ".prefix."_eshop_products (`code`, `url`, `name`, `annotation`, `body`, `active`, `featured`, `stocked`, `meta_title`, `meta_keywords`, `meta_description`, `date`, `editdate`) VALUES ('$code','$url','$name','$annotation','$body','$active','$featured','$stocked','$meta_title','$meta_keywords','$meta_description','$date','$editdate')");
                    
                    $qid = $mysql->lastid('eshop_products');
                    
                    import_upload_images($qid);
                    
                    if(!empty($xfields_items)) {
                        foreach ($xfields_items as $f_key => $f_value) {
                            if($f_value != '') {
                                $mysql->query("INSERT INTO ".prefix."_eshop_options (`product_id`, `feature_id`, `value`) VALUES ('$qid','$f_key','$f_value')");
                            }
                        }
                    }
                    
                    $category_id = intval($cid);
                    
                    if($category_id != 0) {
                        $mysql->query("INSERT INTO ".prefix."_eshop_products_categories (`product_id`, `category_id`) VALUES ('$qid','$category_id')");
                    }
                    
                    $mysql->query("INSERT INTO ".prefix."_eshop_variants (`product_id`, `sku`, `name`, `price`, `compare_price`, `stock`, `amount`) VALUES ('$qid', '$v_sku', '$v_name', '$v_price', '$v_compare_price', '$v_stock', '$v_amount')");
                        
                }
            }
            
            unlink($filepath);
            $info = "������ CSV ��������<br/>";
            msg(array("type" => "info", "info" => $info));
            die();
            
        } else {
            $info = "������ �������� �����<br/>";
            msg(array("type" => "info", "info" => $info));
            die();
        }
        
    }

    if (isset($_REQUEST['multiple_upload_images']))
    {

        $images = $_REQUEST['data']['images'];

        if($images != NULL) {
            
            foreach ($images as $inx_img => $img) {
            
                $img_parts = explode(".", $img);
                $img_s = explode("_", $img_parts[0]);
                $qid = $img_s[0];
                if($qid != "") {
                    
                    $prd_row = $mysql->record("select * from ".prefix."_eshop_products where id = ".db_squote($qid)." limit 1");
                    if ( !is_array($prd_row) ) {
                        break;
                    }
            
                    $positions_img = array();
                    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_images WHERE product_id = '$qid' ORDER BY position, id") as $irow)
                    {
                        $positions_img[] = $irow['position'];
                    }

                    if(!empty($positions_img)) {
                        $max_img_pos = max($positions_img) + 1;
                    }
                    else {
                        $max_img_pos = 0;
                    }

                    $inx_img =  $max_img_pos;
                    
                    @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/', 0777);
                    @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb', 0777);
                
                    $timestamp = time();
                    $iname = $timestamp."-".$img;
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/thumb/'.$img;
                    $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb/'.$iname;
                    rename($temp_name, $current_name);
                    
                    $mysql->query("INSERT INTO ".prefix."_eshop_images (`filepath`, `product_id`, `position`) VALUES ('$iname','$qid','$inx_img')");
                    
                }
            }
        }

        $info = "����������� ���������<br/>";
        msg(array("type" => "info", "info" => $info));
    }

    $xt = $twig->loadTemplate($tpath['config/automation'].'config/'.'automation.tpl');
    
    $yml_export_link = checkLinkAvailable('eshop', 'yml_export')?
                generateLink('eshop', 'yml_export', array()):
                generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'yml_export'), array());
    
    $tVars = array(
        'yml_export_link' => $yml_export_link,
        'info' => '',
    );
    
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '�������������',
    );
    
    print $xg->render($tVars);

}


function import_upload_images($qid) {
global $tpl, $mysql, $twig;

    $positions_img = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_images WHERE product_id = '$qid' ORDER BY position, id") as $irow)
    {
        $positions_img[] = $irow['position'];
    }

    if(!empty($positions_img)) {
        $max_img_pos = max($positions_img) + 1;
    }
    else {
        $max_img_pos = 0;
    }

    $inx_img =  $max_img_pos;
    
    $rootpath = $_SERVER['DOCUMENT_ROOT'];
    $img_dir = dirname(__FILE__)."/import/images/".$qid."/";
    $images = array_map('basename', glob($img_dir."*.{jpg,png,gif}", GLOB_BRACE));
    
    if(!empty($images)) {
        
        foreach($images as $name) {
            
            $file_path = $rootpath."/uploads/eshop/products/temp/".$name;
            rename($img_dir.$name, $file_path);
            $fileParts = pathinfo ( $file_path );
            $extension = $fileParts ['extension'];

            $extensions = array_map('trim', explode(',', pluginGetVariable('eshop', 'ext_image')));
            
            $pre_quality = pluginGetVariable('eshop', 'pre_quality');

            if(!in_array($extension, $extensions)) {
                return "0";
            }

            // CREATE THUMBNAIL
            if ($extension == "jpg" || $extension == "jpeg") {
                $src = imagecreatefromjpeg ( $file_path );
            } else if ($extension == "png") {
                $src = imagecreatefrompng ( $file_path );
            } else {
                $src = imagecreatefromgif ( $file_path );
            }

            list ( $width, $height ) = getimagesize ( $file_path );

            $newwidth = pluginGetVariable('eshop', 'width_thumb');

            if($width > $newwidth) {
                $newheight = ($height / $width) * $newwidth;
                $tmp = imagecreatetruecolor ( $newwidth, $newheight );
                
                imagecopyresampled ( $tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
                
                $thumbname = $rootpath."/uploads/eshop/products/temp/thumb/$name";
                
                if (file_exists ( $thumbname )) {
                    unlink ( $thumbname );
                }
                
                imagejpeg ( $tmp, $thumbname, ($pre_quality>=10 && $pre_quality<=100)?$pre_quality:100 );
                
                imagedestroy ( $src );
                imagedestroy ( $tmp );
            }
            else {
                if ($extension == "jpg" || $extension == "jpeg") {
                    $src = imagecreatefromjpeg ( $file_path );
                } else if ($extension == "png") {
                    $src = imagecreatefrompng ( $file_path );
                } else {
                    $src = imagecreatefromgif ( $file_path );
                }
                imagejpeg ( $src, $file_path, ($pre_quality>=10 && $pre_quality<=100)?$pre_quality:100 );
                $thumbname = $rootpath."/uploads/eshop/products/temp/thumb/$name";
                copy($file_path, $thumbname);
                
                imagedestroy ( $src );
            }
                
            $newwidth = pluginGetVariable('eshop', 'pre_width');
            if(isset($newwidth) && ($newwidth != '0')) {
                
                if ($extension == "jpg" || $extension == "jpeg") {
                    $src = imagecreatefromjpeg ( $file_path );
                } else if ($extension == "png") {
                    $src = imagecreatefrompng ( $file_path );
                } else {
                    $src = imagecreatefromgif ( $file_path );
                }
                
                list ( $width, $height ) = getimagesize ( $file_path );
                $newheight = ($height / $width) * $newwidth;
                $tmp = imagecreatetruecolor ( $newwidth, $newheight );
                imagecopyresampled ( $tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

                $thumbname = $file_path;
                
                imagejpeg ( $tmp, $thumbname, ($pre_quality>=10 && $pre_quality<=100)?$pre_quality:100 );
                
                imagedestroy ( $src );
                imagedestroy ( $tmp );
                    
            }

            $img = $name;

            $timestamp = time();
            $iname = $timestamp."-".$img;
            
            @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/', 0777);
            @mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb', 0777);

            $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/'.$img;
            $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/'.$iname;
            rename($temp_name, $current_name);

            $temp_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/temp/thumb/'.$img;
            $current_name = $_SERVER['DOCUMENT_ROOT'].'/uploads/eshop/products/'.$qid.'/thumb/'.$iname;
            rename($temp_name, $current_name);
                                
            $mysql->query("INSERT INTO ".prefix."_eshop_images (`filepath`, `product_id`, `position`) VALUES ('$iname','$qid','$inx_img')");

            $inx_img += 1;
        }
        
    }
    
}



function options()
{
global $tpl, $mysql, $cron, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/general.from'), 'eshop', 1);
        
    $tVars = array();
    
    if (isset($_REQUEST['submit']))
    {
        pluginSetVariable('eshop', 'count', intval($_REQUEST['count']));
        pluginSetVariable('eshop', 'count_search',  secure_html($_REQUEST['count_search']));
        pluginSetVariable('eshop', 'count_stocks',  secure_html($_REQUEST['count_stocks']));
        
        pluginSetVariable('eshop', 'views_count', $_REQUEST['views_count']);
        pluginSetVariable('eshop', 'bidirect_linked_products', $_REQUEST['bidirect_linked_products']);
        pluginSetVariable('eshop', 'approve_comments', $_REQUEST['approve_comments']);
        pluginSetVariable('eshop', 'sort_comments', $_REQUEST['sort_comments']);
        pluginSetVariable('eshop', 'integrate_gsmg', $_REQUEST['integrate_gsmg']);
        
        pluginSetVariable('eshop', 'max_image_size', intval($_REQUEST['max_image_size']));
        pluginSetVariable('eshop', 'width_thumb', intval($_REQUEST['width_thumb']));
        pluginSetVariable('eshop', 'width', intval($_REQUEST['width']));
        pluginSetVariable('eshop', 'height', intval($_REQUEST['height']));

        pluginSetVariable('eshop', 'ext_image', check_php_str($_REQUEST['ext_image']) );
        
        pluginSetVariable('eshop', 'pre_width', intval($_REQUEST['pre_width']));
        pluginSetVariable('eshop', 'pre_quality', intval($_REQUEST['pre_quality']));
        
        pluginSetVariable('eshop', 'catz_max_image_size', intval($_REQUEST['catz_max_image_size']));
        pluginSetVariable('eshop', 'catz_width_thumb', intval($_REQUEST['catz_width_thumb']));
        pluginSetVariable('eshop', 'catz_width', intval($_REQUEST['catz_width']));
        pluginSetVariable('eshop', 'catz_height', intval($_REQUEST['catz_height']));
        pluginSetVariable('eshop', 'catz_ext_image', check_php_str($_REQUEST['catz_ext_image']) );
       
        pluginSetVariable('eshop', 'email_notify_orders', $_REQUEST['email_notify_orders']);
        pluginSetVariable('eshop', 'email_notify_comments', $_REQUEST['email_notify_comments']);
        pluginSetVariable('eshop', 'email_notify_back', $_REQUEST['email_notify_back']);
        
        pluginSetVariable('eshop', 'description_delivery', $_REQUEST['description_delivery']);
        pluginSetVariable('eshop', 'description_order', $_REQUEST['description_order']);
        pluginSetVariable('eshop', 'description_phones', $_REQUEST['description_phones']);
        
        pluginsSaveConfig();
        
        redirect_eshop('?mod=extra-config&plugin=eshop&action=options');
    }
    
    $views_cnt = intval(pluginGetVariable('eshop', 'views_count'));

    if( $views_cnt == 2 ) {
        
        $cron_row = $cron->getConfig();
        foreach($cron_row as $key=>$value) { 
            if( ($value['plugin']=='eshop') && ($value['handler']=='eshop_views') ) {  
                $cron_min = $value['min']; $cron_hour = $value['hour']; $cron_day = $value['day']; $cron_month = $value['month'];
            }
        }
        if(!isset($cron_min)) { $cron_min = '0,15,30,45'; }
        if(!isset($cron_hour)) { $cron_hour = '*'; } 
        if(!isset($cron_day)) { $cron_day = '*'; } 
        if(!isset($cron_month)) { $cron_month = '*'; } 

        $cron->unregisterTask('eshop', 'eshop_views');
        $cron->registerTask('eshop', 'eshop_views', $cron_min, $cron_hour, $cron_day, $cron_month, '*');
    }
    else{
        $cron->unregisterTask('eshop', 'eshop_views');
    }

    $count = pluginGetVariable('eshop', 'count');
    $count_search = pluginGetVariable('eshop', 'count_search');
    $count_stocks = pluginGetVariable('eshop', 'count_stocks');
    
    $views_count = pluginGetVariable('eshop', 'views_count');
    $views_count = '<option value="0" '.($views_count==0?'selected':'').'>���</option><option value="1" '.($views_count==1?'selected':'').'>��</option><option value="2" '.($views_count==2?'selected':'').'>����������</option>';
    
    $bidirect_linked_products = pluginGetVariable('eshop', 'bidirect_linked_products');
    $bidirect_linked_products = '<option value="0" '.($bidirect_linked_products==0?'selected':'').'>���</option><option value="1" '.($bidirect_linked_products==1?'selected':'').'>��</option>';
    
    $approve_comments = pluginGetVariable('eshop', 'approve_comments');
    $approve_comments = '<option value="0" '.($approve_comments==0?'selected':'').'>���</option><option value="1" '.($approve_comments==1?'selected':'').'>��</option>';
    
    $sort_comments = pluginGetVariable('eshop', 'sort_comments');
    $sort_comments = '<option value="0" '.($sort_comments==0?'selected':'').'>����� �����</option><option value="1" '.($sort_comments==1?'selected':'').'>����� ������</option>';

    $integrate_gsmg = pluginGetVariable('eshop', 'integrate_gsmg');
    $integrate_gsmg = '<option value="0" '.($integrate_gsmg==0?'selected':'').'>���</option><option value="1" '.($integrate_gsmg==1?'selected':'').'>��</option>';
    
    $max_image_size = pluginGetVariable('eshop', 'max_image_size');
    $width_thumb = pluginGetVariable('eshop', 'width_thumb');
    $width = pluginGetVariable('eshop', 'width');
    $height = pluginGetVariable('eshop', 'height');
    $ext_image = pluginGetVariable('eshop', 'ext_image');
    
    $pre_width = pluginGetVariable('eshop', 'pre_width');
    $pre_quality = pluginGetVariable('eshop', 'pre_quality');
    
    $catz_max_image_size = pluginGetVariable('eshop', 'catz_max_image_size');
    $catz_width_thumb = pluginGetVariable('eshop', 'catz_width_thumb');
    $catz_width = pluginGetVariable('eshop', 'catz_width');
    $catz_height = pluginGetVariable('eshop', 'catz_height');
    $catz_ext_image = pluginGetVariable('eshop', 'catz_ext_image');

    $email_notify_orders = pluginGetVariable('eshop', 'email_notify_orders');
    $email_notify_comments = pluginGetVariable('eshop', 'email_notify_comments');
    $email_notify_back = pluginGetVariable('eshop', 'email_notify_back');

    $description_delivery = pluginGetVariable('eshop', 'description_delivery');
    $description_order = pluginGetVariable('eshop', 'description_order');
    $description_phones = pluginGetVariable('eshop', 'description_phones');

    $tEntry = array (
        'count' => $count,
        'count_search' => $count_search,
        'count_stocks' => $count_stocks,
        
        'views_count' => $views_count,
        'bidirect_linked_products' => $bidirect_linked_products,
        
        'approve_comments' => $approve_comments,
        'sort_comments' => $sort_comments,
        'integrate_gsmg' => $integrate_gsmg,
        
        'max_image_size' => $max_image_size,
        'width_thumb' => $width_thumb,
        'width' => $width,
        'height' => $height,
        'ext_image' => $ext_image,
        
        'pre_width' => $pre_width,
        'pre_quality' => $pre_quality,
        
        'catz_max_image_size' => $catz_max_image_size,
        'catz_width_thumb' => $catz_width_thumb,
        'catz_width' => $catz_width,
        'catz_height' => $catz_height,
        'catz_ext_image' => $catz_ext_image,
        
        'email_notify_orders' => $email_notify_orders,
        'email_notify_comments' => $email_notify_comments,
        'email_notify_back' => $email_notify_back,

        'description_delivery' => $description_delivery,
        'description_order' => $description_order,
        'description_phones' => $description_phones,

    );
    
    $xt = $twig->loadTemplate($tpath['config/general.from'].'config/'.'general.from.tpl');

    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'' 
    );

    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '���������',
    );

    print $xg->render($tVars);
}

/*
function eshop_upload_files($files_del){
    $max_file_size = pluginGetVariable('eshop', 'max_file_size') * 1024 * 1024;
    $extensions = explode(',', pluginGetVariable('eshop', 'ext_file'));
    
    if (isset($_FILES['plugin_files']['name']) && !empty($_FILES['plugin_files']['name'])){
        if (is_uploaded_file($_FILES['plugin_files']['tmp_name'])){
            $ext = pathinfo($_FILES['plugin_files']['name'], PATHINFO_EXTENSION);
            if(in_array($ext, $extensions)){
                if ($_FILES['plugin_files']['size'] < $max_file_size){
                    if(is_writable(files_dir . 'eshop/')){
                        $name_file = basename($_FILES['plugin_files']['name'], $ext);
                        $name_file = preg_replace("/[^\w\x7F-\xFF]/", "", $name_file);
                        $Ffile = $name_file . '.' . $ext;
                        
                        if($files_del == $Ffile)
                            unlink(files_dir . 'eshop/'. $files_del);
                        
                        if(file_exists(files_dir . 'eshop/' . $Ffile))
                            $error_text = '����� ���� ��� ����������';
                        else
                            unlink(files_dir . 'eshop/'. $files_del);
                        
                        if(empty($error_text)){
                            if(move_uploaded_file($_FILES['plugin_files']['tmp_name'], files_dir . 'eshop/' . $Ffile)){
                                chmod(files_dir . 'eshop/' . $Ffile, 0644);
                            } else {
                                $error_text = '�������� �� �������';
                            }
                        }
                    } else {
                        $error_text = '��� ���� �� ������';
                    }
                } else {
                    $error_text = '������ ����� ������ �����������';
                }
            } else {
                $error_text = '���������� ����������';
            }
        } else {
            $error_text = '���� �� ��������';
        }
    }
    return array($Ffile, $error_text);
}

function eshop_upload_images($images_del, $w, $h, $quality = 100){
    $max_image_size = pluginGetVariable('eshop', 'max_image_size') * 1024 * 1024;
    $extensions = explode(',', pluginGetVariable('eshop', 'ext_image'));
    
    if (isset($_FILES['plugin_images']['name']) && !empty($_FILES['plugin_images']['name'])){
        if (is_uploaded_file($_FILES['plugin_images']['tmp_name'])){
            $ext = pathinfo($_FILES['plugin_images']['name'], PATHINFO_EXTENSION);
            if(in_array($ext, $extensions)){
                $new = date("Ymd")."_".rand(1000,9999).'.'.$ext;
                if ($_FILES['plugin_images']['size'] < $max_image_size){
                    if($size_img = getimagesize($_FILES['plugin_images']['tmp_name'])){
                        if(($size_img[0] <= pluginGetVariable('eshop', 'width')) && ($size_img[1] <= pluginGetVariable('eshop', 'height'))){
                            $dir_image = images_dir .'eshop/'.$new;
                            if(move_uploaded_file($_FILES['plugin_images']['tmp_name'], $dir_image)){
                                if(isset($images_del)){
                                    unlink(images_dir . 'eshop/thumb/'.$images_del);
                                    unlink(images_dir . 'eshop/'.$images_del);
                                }
                                
                                switch ($size_img[2])
                                {
                                    case 1: $image_ext = 'gif';     break;
                                    case 2: $image_ext = 'jpeg';    break;
                                    case 3: $image_ext = 'png';     break;
                                    case 6: $image_ext = 'bmp';     break;
                                }
                                
                                $dest_img = imagecreatetruecolor($w, $h);
                                
                                switch ($size_img[2]){
                                    case 1: $src_img = imagecreatefromgif($dir_image);      break;
                                    case 2: $src_img = imagecreatefromjpeg($dir_image);     break;
                                    case 3: $src_img = imagecreatefrompng($dir_image);      break;
                                    case 6: $src_img = imagecreatefrombmp($dir_image);      break;
                                }
                                
                                $oTColor = imagecolortransparent($src_img);
                                if ($oTColor >= 0 && $oTColor < imagecolorstotal($src_img)) {
                                    $TColor = imagecolorsforindex($src_img, $oTColor);
                                    $nTColor = imagecolorallocate($dest_img, $TColor['red'], $TColor['green'], $TColor['blue']);
                                    imagefill($dest_img, 0, 0, $nTColor);
                                    imagecolortransparent($dest_img, $nTColor);
                                } else {
                                    if ($size_img[2] == 3) {
                                        imagealphablending($dest_img, false);
                                        $nTColor = imagecolorallocatealpha($dest_img, 0,0,0, 127);
                                        imagefill($dest_img, 0, 0, $nTColor);
                                        imagesavealpha($dest_img, true);
                                    }
                                }
                                
                                imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $w, $h);
                                
                                switch ($size_img[2]){
                                    case 1: imagegif($dest_img, images_dir .'eshop/thumb/'.$new);               break;
                                    case 2: imagejpeg($dest_img, images_dir .'eshop/thumb/'.$new, $quality);    break;
                                    case 3: imagepng($dest_img, images_dir .'eshop/thumb/'.$new);               break;
                                    case 6: imagebmp($dest_img, images_dir .'eshop/thumb/'.$new);               break;
                                }
                                
                                chmod($dir_image, 0644);
                                chmod(images_dir .'eshop/thumb/'.$new, 0644);
                            } else {
                                $error_text = '������ ��� ����������';
                            }
                        } else {
                            $error_text = '������ ����������� ������ ��� '.pluginGetVariable('eshop', 'width').' ?? '.pluginGetVariable('eshop', 'height');
                        }
                    } else {
                        $error_text = '���������� ���� �� �������� ������������';
                    }
                } else {
                    $error_text = '������ ����� ������ �����������';
                }
            } else {
                $error_text = '������������ ����������';
            }
        } else {
            $error_text = '����������� �� ���������';
        }
    }
    return array($new, $error_text);
}
*/

function redirect_eshop($url)
{
    if (headers_sent()) {
        echo "<script>document.location.href='{$url}';</script>\n";
        exit;
    } else {
        header('HTTP/1.1 302 Moved Permanently');
        header("Location: {$url}");
        exit;
    }
}

function input_filter_com($text)
{
    $text = trim($text);
    $search = array("<", ">");
    $replace = array("&lt;", "&gt;");
    $text = preg_replace("/(&amp;)+(?=\#([0-9]{2,3});)/i", "&", str_replace($search, $replace, $text));
    return $text;
}
