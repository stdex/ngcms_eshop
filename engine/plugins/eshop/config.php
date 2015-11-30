<?php

if(!defined('NGCMS'))
    exit('HAL');

plugins_load_config();
LoadPluginLang('eshop', 'config', '', '', '#');

include_once(dirname(__FILE__).'/cache.php');
#include_once(dirname(__FILE__).'/catz.php');

switch ($_REQUEST['action']) {
    
    case 'list_product':    list_product();                        break;
    case 'add_product':     add_product();                         break;
    case 'edit_product':    edit_product();                        break;
    case 'modify_product':  modify(); list_product();              break;
        
    case 'list_cat':        list_cat();                            break;
    case 'add_cat':         add_cat();                             break;
    case 'edit_cat':        edit_cat();                            break;
    case 'del_cat':         del_cat(); list_cat();                 break;
    
    case 'options':         options();                             break;

    case 'url':             url();                                 break;
    
    default:                list_product();
}

function list_product()
{
global $tpl, $mysql, $lang, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/list_product'), 'eshop', 1);
    
    $tVars = array();
    
    $news_per_page = 10;

    $fSort = "ORDER BY id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products ".$fSort;
    $sqlQCount = "SELECT COUNT(id) ".$sqlQPart;
    $sqlQ = "SELECT * ".$sqlQPart;
    
    $pageNo     = intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1)    $pageNo = 1;
    if (!$start_from)   $start_from = ($pageNo - 1)* $news_per_page;
    
    $count = $mysql->result($sqlQCount);
    $countPages = ceil($count / $news_per_page);

    foreach ($mysql->select($sqlQ.' LIMIT '.$start_from.', '.$news_per_page) as $row)
    {
        $tEntry[] = array (
            'id'                   => $row['id'],
            'code'                 => $row['code'],
            'name'                 => $row['name'],
            
            'category'             => '',
            'image'                => '',

            'current_price'        => '',
            'old_price'            => '',
            
            'active'               => $row['active'],
            'featured'             => $row['featured'],
            'position'             => $row['position'],
            
            'date'                 => $row['date'],
            'editdate'             => $row['editdate'],
            
            'edit_link'            => "?mod=extra-config&plugin=eshop&action=edit_product&id=".$row['id']."",
            'view_link'            => '',
        );
    }

    $xt = $twig->loadTemplate($tpath['config/list_product'].'config/'.'list_product.tpl');
    
    $tVars = array( 
        'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=eshop'.($news_per_page?'&rpp='.$news_per_page:'').'&page=%page%')),
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
        'current_title' => 'Продукция',
    );
    
    print $xg->render($tVars);
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

function add_cat($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_cat'), 'eshop', 1);
    
    if (isset($_REQUEST['submit']))
    {

        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        if(empty($cat_name))
        {
            $error_text[] = 'Название категории не задано';
        }
        
        $description = input_filter_com(convert($_REQUEST['description']));
        if(empty($description))
        {
            $error_text[] = 'Описание категории не задано';
        }

        $url = input_filter_com(convert($_REQUEST['url']));
        if(empty($url))
        {
            $error_text[] = 'URL категории не задан';
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
            
            #generate_catz_cache(true);
            
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
        'current_title' => 'Категории: Добавление категории',
    );
    
    print $xg->render($tVars);
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

        // preserve file from temporary directory
        $success = move_uploaded_file($myFile["tmp_name"], $upload_dir . $img_name);
        
        if (!$success) {
             return "";
        }
        
        $tempFile = $upload_dir . $img_name;
        $extension = $parts["extension"];
        
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

function edit_cat($params)
{
global $tpl, $template, $config, $mysql, $lang, $twig;
    $tpath = locatePluginTemplates(array('config/main', 'config/add_cat'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_categories WHERE id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {

        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        if(empty($cat_name))
        {
            $error_text[] = 'Название категории не задано';
        }
        
        $description = input_filter_com(convert($_REQUEST['description']));
        if(empty($description))
        {
            $error_text[] = 'Описание категории не задано';
        }

        $url = input_filter_com(convert($_REQUEST['url']));
        if(empty($url))
        {
            $error_text[] = 'URL категории не задан';
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
                parent_id = '.db_squote($parent_id).',
                position = '.db_squote($position).',
                '.$image_sql.'
                active = '.db_squote($active).'
                WHERE id = '.$id.'
            ');

            #generate_catz_cache(true);
            
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
        'current_title' => 'Категории: Редактирование категории',
    );
    
    print $xg->render($tVars);
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
        'current_title' => 'Категории',
    );
    
    print $xg->render($tVars);
    
    /*
    foreach ($mysql->select('SELECT cat_id, COUNT(id) as num FROM '.prefix.'_eshop GROUP BY cat_id') as $rows)
    {
        $cat[$rows['cat_id']] .= $rows['num'];
    }
    

    foreach ($mysql->select('SELECT * from '.prefix.'_eshop_cat ORDER BY position ASC') as $row)
    {
        $gvars['vars'] = array (
            'num' => $cat[$row['id']],
            'id' => $row['id'],
            'cat_name' => '<a href="?mod=extra-config&plugin=eshop&action=cat_edit&id='.$row['id'].'"  />'.$row['cat_name'].'</a>',
            'cat_name_del' => '<a href="?mod=extra-config&plugin=eshop&action=cat_name_del&id='.$row['id'].'"  /><img title="???????" alt="???????" src="/engine/skins/default/images/delete.gif"></a>',
        );
        
        $tpl->template('list_cat_entries', $tpath['config/list_cat_entries'].'config');
        $tpl->vars('list_cat_entries', $gvars);
        $entries .= $tpl -> show('list_cat_entries');
    }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');

    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_cat', $tpath['config/list_cat'].'config');
    $tpl->vars('list_cat', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_cat'),
        'global' => 'Список категорий'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
    */
}

function del_cat($params)
{global $mysql;
    
    $id = intval($_REQUEST['id']);
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
    }
    
    $cnt_products_in_cat = $mysql->record('SELECT COUNT(*) AS cnt FROM '.prefix.'_eshop_products_categories WHERE category_id = '.db_squote($id).'');

    if($cnt_products_in_cat['cnt'] == 0) {
        $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_categories WHERE id = '.db_squote($id).' LIMIT 1');
        delete_cat_image($row['image']);
        $mysql->query("DELETE FROM ".prefix."_eshop_categories WHERE id = {$id}");
        msg(array("type" => "info", "info" => "Категория удалена"));
    }
    else {
        msg(array("type" => "info", "info" => "Категория не может быть удалена, т.к. в ней есть продукция"));
    }

    
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
            $gvars[] = array (
                'id' => $value['CategoryID'],
                'cat_name' => $value['CategoryName'],
                'edit_link' => "?mod=extra-config&plugin=eshop&action=edit_cat&id=".$value['CategoryID'],
                'del_link' => "?mod=extra-config&plugin=eshop&action=del_cat&id=".$value['CategoryID'],
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
    #var_dump($prefixed[$CategoryID]);
    return $add_prefix;
}

function list_order()
{
global $tpl, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_order', 'config/list_order_entries'), 'eshop', 1);

    foreach ($mysql->select('SELECT *, po.id as id, zb.id as zid from '.prefix.'_eshop_pay_order po LEFT JOIN '.prefix.'_eshop zb  ON po.zid = zb.id ORDER BY po.id ASC') as $row)
    {
        $gvars['vars'] = array (
            'id' => $row['id'],
            'dt' => (empty($row['dt']))?'Дата не указана':date(pluginGetVariable('eshop', 'date'), $row['dt']),
            'price' => $row['amount']." ".$row['currency'],
            'discr' => $row['description'],
            'status' => $row['status'],
            'announce' => '<a href="?mod=extra-config&plugin=eshop&action=edit_announce&id='.$row['zid'].'"  />'.$row['announce_name'].'</a>',
        );
        
        $tpl->template('list_order_entries', $tpath['config/list_order_entries'].'config');
        $tpl->vars('list_order_entries', $gvars);
        $entries .= $tpl -> show('list_order_entries');
    }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_order', $tpath['config/list_order'].'config');
    $tpl->vars('list_order', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_order'),
        'global' => 'Прайс'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function list_price()
{
global $tpl, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_price', 'config/list_price_entries'), 'eshop', 1);

    foreach ($mysql->select('SELECT * from '.prefix.'_eshop_pay_price ORDER BY id ASC') as $row)
    {
        $gvars['vars'] = array (
            'id' => $row['id'],
            'time' => $row['time'],
            'price' => '<a href="?mod=extra-config&plugin=eshop&action=price_edit&id='.$row['id'].'"  />'.$row['price'].'</a>',
            'price_del' => '<a href="?mod=extra-config&plugin=eshop&action=price_del&id='.$row['id'].'"  /><img src="/engine/skins/default/images/delete.gif"></a>',
        );
        
        $tpl->template('list_price_entries', $tpath['config/list_price_entries'].'config');
        $tpl->vars('list_price_entries', $gvars);
        $entries .= $tpl -> show('list_price_entries');
    }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_price', $tpath['config/list_price'].'config');
    $tpl->vars('list_price', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_price'),
        'global' => 'Прайс'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function send_price($params)
{
global $tpl, $template, $config, $mysql, $lang;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_price'), 'eshop', 1);
    
    if (isset($_REQUEST['submit']))
    {
    
        $price = input_filter_com(convert($_REQUEST['price']));
        $time = intval($_REQUEST['time']);
        
        if(empty($price))
        {
            $error_text[] = 'Прайс не задан';
        }

        if(empty($time))
        {
            $error_text[] = 'Время не задано';
        }

        if(empty($error_text))
        {
            $mysql->query('INSERT INTO '.prefix.'_eshop_pay_price (time, price) 
                VALUES 
                ('.db_squote($time).',
                '.db_squote($price).'
                )
            ');
            
            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_price');
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

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $pvars['vars'] = array (
        'time' => $time,
        'price' => $price,
        'error' => $error_input,
    );
    
    
    $tpl->template('send_price', $tpath['config/send_price'].'config');
    $tpl->vars('send_price', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_price'),
        'global' => 'Добавить категорию'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function price_edit()
{
global $tpl, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_price'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    
    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_pay_price WHERE id = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {
        $time = intval($_REQUEST['time']);
        $price = input_filter_com(convert($_REQUEST['price']));
        
        if(empty($price))
        {
            $error_text[] = 'Прайс не задан';
        }

        if(empty($time))
        {
            $error_text[] = 'Время не задано';
        }
        
        if(empty($error_text))
        {
            //  position = '.intval($position).'
            
            $mysql->query('UPDATE '.prefix.'_eshop_pay_price SET  
                time = '.db_squote($time).',
                price = '.db_squote($price).'
                WHERE id = '.$id.'
            ');

            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_price');
        }
    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            $error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
        }
    } else {
        $error_input ='';
    }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $pvars['vars'] = array (
        'time' => $row['time'],
        'price' => $row['price'],
        'error' => $error_input,
    );
    
    $tpl->template('send_price', $tpath['config/send_price'].'config');
    $tpl->vars('send_price', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_price'),
        'global' => 'Редактировать прайс'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function price_del()
{global $mysql;
    
    $id = intval($_REQUEST['id']);
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
    }
    
    $mysql->query("delete from ".prefix."_eshop_pay_price where id = {$id}");

    msg(array("type" => "info", "info" => "Прайс удален"));
    
}


function url()
{global $tpl, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/url'), 'eshop', 1);
    
    if (isset($_REQUEST['submit']))
    {
        if(isset($_REQUEST['url']) && !empty($_REQUEST['url']))
        {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            
            $ULIB->registerCommand('eshop', '',
                array ('vars' =>
                        array(  'cat' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'Категории')),
                                'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация'))
                        ),
                        'descr' => array ('russian' => 'Главная страница'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'show',
                array ('vars' =>
                        array(  'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr' => array ('russian' => 'Ссылка на объявление'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'send',
                array ('vars' =>
                        array(),
                        'descr' => array ('russian' => 'Добавить объявлдение'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'search',
                array ('vars' =>
                        array(),
                        'descr' => array ('russian' => 'Поиск по объявлениям'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'list',
                array ('vars' =>
                        array( 'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация'))
                        ),
                        'descr' => array ('russian' => 'Список объявлений добавленных пользователем'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'edit',
                array ('vars' =>
                        array(  'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr' => array ('russian' => 'Ссылка для редактирования'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'del',
                array ('vars' =>
                        array(  'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr' => array ('russian' => 'Ссылка для удаления'),
                )
            );
            
            $ULIB->registerCommand('eshop', 'expend',
                array ('vars' =>
                        array(  'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                                'hashcode' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Hashcode объявления')),
                        ),
                        'descr' => array ('russian' => 'Ссылка для продления'),
                )
            );
            
            $ULIB->saveConfig();
            
            $UHANDLER = new urlHandler();
            $UHANDLER->loadConfig();
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => '',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/[cat/{cat}/][page/{page}/]',
                  'regex' => '#^/eshop/(?:cat/(\\d+)/){0,1}(?:page/(\\d{1,4})/){0,1}$#',
                  'regexMap' => 
                  array (
                    1 => 'cat',
                    2 => 'page',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 0,
                      1 => 'cat/',
                      2 => 1,
                    ),
                    2 => 
                    array (
                      0 => 1,
                      1 => 'cat',
                      2 => 1,
                    ),
                    3 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                    4 => 
                    array (
                      0 => 0,
                      1 => 'page/',
                      2 => 3,
                    ),
                    5 => 
                    array (
                      0 => 1,
                      1 => 'page',
                      2 => 3,
                    ),
                    6 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 3,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'show',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/{id}/',
                  'regex' => '#^/eshop/(\\d+)/$#',
                  'regexMap' => 
                  array (
                    1 => 'id',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'send',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/send/',
                  'regex' => '#^/eshop/send/$#',
                  'regexMap' => 
                  array (
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/send/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'search',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/search/',
                  'regex' => '#^/eshop/search/$#',
                  'regexMap' => 
                  array (
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/search/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'list',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/list/[page/{page}/]',
                  'regex' => '#^/eshop/list/(?:page/(\\d{1,4})/){0,1}$#',
                  'regexMap' => 
                  array (
                    1 => 'page',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/list/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 0,
                      1 => 'page/',
                      2 => 1,
                    ),
                    2 => 
                    array (
                      0 => 1,
                      1 => 'page',
                      2 => 1,
                    ),
                    3 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'edit',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/edit/{id}/',
                  'regex' => '#^/eshop/edit/(\\d+)/$#',
                  'regexMap' => 
                  array (
                    1 => 'id',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/edit/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'del',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/del/{id}/',
                  'regex' => '#^/eshop/del/(\\d+)/$#',
                  'regexMap' => 
                  array (
                    1 => 'id',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/del/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );
            
            
            $UHANDLER->registerHandler(0,
                array (
                'pluginName' => 'eshop',
                'handlerName' => 'expend',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' => 
                array (
                  'rcmd' => '/eshop/expend/[id/{id}/][hashcode/{hashcode}/]',
                  'regex' => '#^/eshop/expend/(?:id/(\\d+)/){0,1}(?:hashcode/(.+?)/){0,1}$#',
                  'regexMap' => 
                  array (
                    1 => 'id',
                    2 => 'hashcode',
                  ),
                  'reqCheck' => 
                  array (
                  ),
                  'setVars' => 
                  array (
                  ),
                  'genrMAP' => 
                  array (
                    0 => 
                    array (
                      0 => 0,
                      1 => '/eshop/',
                      2 => 0,
                    ),
                    1 => 
                    array (
                      0 => 0,
                      1 => 'id/',
                      2 => 1,
                    ),
                    2 => 
                    array (
                      0 => 1,
                      1 => 'id',
                      2 => 1,
                    ),
                    3 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                    4 => 
                    array (
                      0 => 0,
                      1 => 'hashcode/',
                      2 => 3,
                    ),
                    5 => 
                    array (
                      0 => 1,
                      1 => 'hashcode',
                      2 => 3,
                    ),
                    6 => 
                    array (
                      0 => 0,
                      1 => '/',
                      2 => 3,
                    ),
                  ),
                ),
              )
            );
            
            $UHANDLER->saveConfig();
        } else {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            $ULIB->removeCommand('eshop', '');
            $ULIB->removeCommand('eshop', 'show');
            $ULIB->removeCommand('eshop', 'send');
            $ULIB->removeCommand('eshop', 'search');
            $ULIB->removeCommand('eshop', 'list');
            $ULIB->removeCommand('eshop', 'edit');
            $ULIB->removeCommand('eshop', 'del');
            $ULIB->removeCommand('eshop', 'expend');
            $ULIB->saveConfig();
            $UHANDLER = new urlHandler();
            $UHANDLER->loadConfig();
            $UHANDLER->removePluginHandlers('eshop', '');
            $UHANDLER->removePluginHandlers('eshop', 'show');
            $UHANDLER->removePluginHandlers('eshop', 'send');
            $UHANDLER->removePluginHandlers('eshop', 'search');
            $UHANDLER->removePluginHandlers('eshop', 'list');
            $UHANDLER->removePluginHandlers('eshop', 'edit');
            $UHANDLER->removePluginHandlers('eshop', 'del');
            $UHANDLER->removePluginHandlers('eshop', 'expend');
            $UHANDLER->saveConfig();
        }
        
        pluginSetVariable('eshop', 'url', intval($_REQUEST['url']));
        pluginsSaveConfig();
        
        redirect_eshop('?mod=extra-config&plugin=eshop&action=url');
    }
    $url = pluginGetVariable('eshop', 'url');
    $url = '<option value="0" '.(empty($url)?'selected':'').'>Нет</option><option value="1" '.(!empty($url)?'selected':'').'>Да</option>';
    $pvars['vars']['info'] = $url;
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $tpl->template('url', $tpath['config/url'].'config');
    $tpl->vars('url', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('url'),
        'global' => 'Настройка ЧПУ'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function list_announce()
{
global $tpl, $mysql, $lang;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_announce', 'config/list_entries'), 'eshop', 1);
    
    $news_per_page = pluginGetVariable('eshop', 'admin_count');
    
    if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
    
    $pageNo     = intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1)    $pageNo = 1;
    if (!$start_from)   $start_from = ($pageNo - 1)* $news_per_page;
    
    $count = $mysql->result('SELECT count(id) from '.prefix.'_eshop');
    $countPages = ceil($count / $news_per_page);
    
    foreach ($mysql->select('SELECT * from '.prefix.'_eshop ORDER BY editdate DESC LIMIT '.$start_from.', '.$news_per_page) as $row)
    {
        switch ($row['active'])
        {
            case 1: $active = 'Да'; break;
            case 0: $active = 'Нет'; break;
            default: $active = 'Ошибка';
        }
        
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_eshop_cat where id='.$row['cat_id'].'') as $cat)
        {
            $options = $cat['cat_name'];
        }
        
        $gvars['vars'] = array (
            'id' => $row['id'],
            'announce_name' => '<a href="?mod=extra-config&plugin=eshop&action=edit_announce&id='.$row['id'].'"  />'.$row['announce_name'].'</a>',
            'announce_period' => $row['announce_period'],
            'announce_description' => $row['announce_description'],
            'announce_contacts' => $row['announce_contacts'],
            'vip_added'             =>  $row['vip_added'],
            'vip_expired'           =>  $row['vip_expired'],
            'date' => (empty($row['date']))?'Дата не указана':date(pluginGetVariable('eshop', 'date'), $row['date']),
            'category' => $options,
            'active' => $active,
            'author' => $row['author'],
        );
        
        $tpl->template('list_entries', $tpath['config/list_entries'].'config');
        $tpl->vars('list_entries', $gvars);
        $entries .= $tpl -> show('list_entries');
    }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $pvars['vars']['pagesss'] = generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=eshop&action=list_announce'.($_REQUEST['news_per_page']?'&news_per_page='.$news_per_page:'').($_REQUEST['author']?'&author='.$_REQUEST['author']:'').($_REQUEST['sort']?'&sort='.$_REQUEST['sort']:'').($postdate?'&postdate='.$postdate:'').($author?'&author='.$author:'').($status?'&status='.$status:'').'&page=%page%'));
    $pvars['vars']['entries'] = $entries;
    $tpl->template('list_announce', $tpath['config/list_announce'].'config');
    $tpl->vars('list_announce', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_announce'),
        'global' => 'Список объявлений'
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}

function edit_announce()
{
global $tpl, $lang, $mysql, $config;
    $tpath = locatePluginTemplates(array('config/main', 'config/edit_announce', 'config/list_images'), 'eshop', 1);
    
    $id = intval($_REQUEST['id']);
    if (!empty($id))
    {
        
        $row = $mysql->record('SELECT * FROM '.prefix.'_eshop WHERE id = '.db_squote($id).' LIMIT 1');
        
        foreach (explode("|",pluginGetVariable('eshop', 'list_period')) as $line) {
            $list_period .= str_replace( array('{line}', '{activ}'), array($line, ($line==$row['announce_period']?'selected':'')), $lang['eshop']['list_period']);
        }
        /*
        $options = '<option disabled>---------</option>';
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_eshop_cat') as $cat)
        {
            $options .= '<option value="' . $cat['id'] . '"'.(($row['cat_id']==$cat['id'])?'selected':'').'>' . $cat['cat_name'] . '</option>';
        }
        */
            $res = mysql_query("SELECT * FROM ".prefix."_eshop_cat ORDER BY id");
            $cats = getCats($res);
            $options = getTree($cats, $row['cat_id'], 0);
    
        if (isset($_REQUEST['submit']))
        {
            $SQL['editdate'] = time() + ($config['date_adjust'] * 60);
            
            $SQL['announce_name'] = input_filter_com(convert($_REQUEST['announce_name']));
            if(empty($SQL['announce_name']))
                $error_text[] = 'Название объявления пустое';

            
            $SQL['author'] = input_filter_com(convert($_REQUEST['author']));
            if(empty($SQL['author']))
                $error_text[] = 'Поле автор не заполнено';
            
            $SQL['announce_period'] = input_filter_com(convert($_REQUEST['announce_period']));
            if(!empty($SQL['announce_period']))
            {
                if(!in_array($SQL['announce_period'], explode("|",pluginGetVariable('eshop', 'list_period'))))
                {
                    $error_text[] = 'Поле период задано неверно '.$SQL['announce_period'];
                }
                
            } else {
                $error_text[] = 'Поле период не заполнено';
            }
            
            $SQL['cat_id'] = intval($_REQUEST['cat_id']);
            if(!empty($SQL['cat_id']))
            {
                $cat = $mysql->result('SELECT 1 FROM '.prefix.'_eshop_cat WHERE id = \'' . $SQL['cat_id'] . '\' LIMIT 1');
                
                if(empty($cat))
                {
                    $error_text[] = 'Такой категории не существует';
                }
            } else {
                $error_text[] = 'Вы не выбрали категорию';
            }
            
            
            $SQL['announce_description'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_description'])));
            if(empty($SQL['announce_description']))
            {
                $error_text[] = 'Нет описания к объявлению';
            }
            
            $SQL['announce_contacts'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_contacts'])));
            if(empty($SQL['announce_contacts']))
            {
                $error_text[] = 'Нет контактов к объявлению';
            }
            
            $SQL['active'] = $_REQUEST['announce_activeme'];
            
            if(is_array($SQLi)){
                $vnamess = array();
                foreach ($SQLi as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_eshop set '.implode(', ',$vnamess).' where  id = \''.intval($id).'\'');
            }
            
            if(empty($error_text))
            {
                $vnames = array();
                foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_eshop set '.implode(', ',$vnames).' where  id = \''.intval($id).'\'');
                
                generate_entries_cnt_cache(true);
                generate_catz_cache(true);
                
                sleep(5);
                
                redirect_eshop('?mod=extra-config&plugin=eshop&action=list_announce');
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
        
        if($row['active'] == 1) { $checked = 'checked'; } else  { $checked = ''; }

        $pvars['vars'] = array (
            'images_url' => str_replace( array( '{url_images}', '{url_images_thumb}'), 
                            array(images_url.'/eshop/'.$row['plugin_images'], images_url.'/eshop/thumb/'.$row['plugin_images']), $lang['eshop']['images_url']),
            'options' => $options,
            'announce_activeme' => $checked,
            'announce_name' => $row['announce_name'],
            'list_period' => $list_period,
            'announce_contacts' =>$row['announce_contacts'],
            'author' => $row['author'],
            'announce_description' => $row['announce_description'],
            'vip_added'             =>  $row['vip_added'],
            'vip_expired'           =>  $row['vip_expired'],
            'tpl_url' => home.'/eshop/'.$config['theme'],
            'tpl_home' => admin_url,
            'id' => $id,
            'error' => $error_input,
        );
    } else {
        msg(array("type" => "error", "text" => "Вы выбрали неверное id"));
    }
    
    
        foreach ($mysql->select('select * from '.prefix.'_eshop_images where zid='.$id.'') as $row2)
        {
        $gvars['vars'] = array (
            'home' => home,
            'del' => home.'/engine/admin.php?mod=extra-config&plugin=eshop&action=edit_announce&id='.$id.'&delimg='.$row2['pid'].'&filepath='.$row2['filepath'].'',
            'pid' => $row2['pid'],
            'filepath' => $row2['filepath'],
            'zid' => $row2['zid'],
        );
        
        $tpl->template('list_images', $tpath['config/list_images'].'config');
        $tpl->vars('list_images', $gvars);
        $entriesImg .= $tpl -> show('list_images');
        }
        
        $pvars['vars']['entriesImg'] = $entriesImg;
        
    if (isset($_REQUEST['delimg']) && isset($_REQUEST['filepath']))
        {
        $imgID = intval($_REQUEST['delimg']);
        $imgPath = $_REQUEST['filepath'];
        $mysql->query("delete from ".prefix."_eshop_images where pid = ".$imgID."");
        //echo root . '/uploads/eshop/' . $imgPath;
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/' . $imgPath);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/thumb/' . $imgPath);
        redirect_eshop('?mod=extra-config&plugin=eshop&action=edit_announce&id='.$id.'');
        }
    
    if (isset($_REQUEST['delme']))
        {
        
        foreach ($mysql->select('select * from '.prefix.'_eshop_images where zid='.db_squote($id).'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_eshop_images where zid = ".db_squote($id)."");

        $mysql->query('delete from '.prefix.'_eshop where id = '.db_squote($id));
        
        generate_entries_cnt_cache(true);
        
        redirect_eshop('?mod=extra-config&plugin=eshop&action=list_announce');
        }
    
    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_eshop WHERE active = \'0\' ');
    
    $tpl->template('edit_announce', $tpath['config/edit_announce'].'config');
    $tpl->vars('edit_announce', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('edit_announce'),
        'entriesImg' => $tpl->show('list_images'),
        'global' => 'Редактирование: '.$row['announce_name']
    );
    
    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}


function cat_name_del()
{global $mysql;
    
    $id = intval($_REQUEST['id']);
    
    if( empty($id) )
    {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
    }
    
    $mysql->query("delete from ".prefix."_eshop_cat where id = {$id}");
    
    generate_catz_cache(true);
    
    msg(array("type" => "info", "info" => "Категория удалена"));
    
}

function modify()
{
global $mysql;
    
    $selected_news = $_REQUEST['selected_files'];
    $subaction  =   $_REQUEST['subaction'];
    
    if( empty($selected_news) )
    {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали объявление"));
    }
    
    switch($subaction) {
        case 'mass_approve'      : $active = 'active = 1'; break;
        case 'mass_forbidden'    : $active = 'active = 0'; break;
        case 'mass_delete'       : $del = true; break;
    }
    
    foreach ($selected_news as $id)
    {
        if(isset($active))
        {
            $mysql->query('update '.prefix.'_eshop 
                    set '.$active.'
                    WHERE id = '.db_squote($id).'
                    ');
            $result = 'Объявления Активированы/Деактивированы';
        }
        if(isset($del))
        {
        
        foreach ($mysql->select('select * from '.prefix.'_eshop_images where zid='.db_squote($id).'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/eshop/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_eshop_images where zid = ".db_squote($id)."");

            $mysql->query('delete from '.prefix.'_eshop where id = '.db_squote($id));
            $result = 'Объявления удалены';
        }
    }
    generate_entries_cnt_cache(true);
    generate_catz_cache(true);
    msg(array("type" => "info", "info" => $result));
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
        pluginSetVariable('eshop', 'views_count', $_REQUEST['views_count']);
        
        pluginSetVariable('eshop', 'max_image_size', intval($_REQUEST['max_image_size']));
        pluginSetVariable('eshop', 'width_thumb', intval($_REQUEST['width_thumb']));
        pluginSetVariable('eshop', 'width', intval($_REQUEST['width']));
        pluginSetVariable('eshop', 'height', intval($_REQUEST['height']));
        pluginSetVariable('eshop', 'ext_image', secure_html(trim($_REQUEST['ext_image'])));
        
        pluginSetVariable('eshop', 'catz_max_image_size', intval($_REQUEST['catz_max_image_size']));
        pluginSetVariable('eshop', 'catz_width_thumb', intval($_REQUEST['catz_width_thumb']));
        pluginSetVariable('eshop', 'catz_width', intval($_REQUEST['catz_width']));
        pluginSetVariable('eshop', 'catz_height', intval($_REQUEST['catz_height']));
        pluginSetVariable('eshop', 'catz_ext_image', secure_html(trim($_REQUEST['catz_ext_image'])));
       
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
    $views_count = pluginGetVariable('eshop', 'views_count');
    $views_count = '<option value="0" '.($views_count==0?'selected':'').'>Нет</option><option value="1" '.($views_count==1?'selected':'').'>Да</option><option value="2" '.($views_count==2?'selected':'').'>Отложенное</option>';
    
    $max_image_size = pluginGetVariable('eshop', 'max_image_size');
    $width_thumb = pluginGetVariable('eshop', 'width_thumb');
    $width = pluginGetVariable('eshop', 'width');
    $height = pluginGetVariable('eshop', 'height');
    $ext_image = pluginGetVariable('eshop', 'ext_image');

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
        'views_count' => $views_count,
        
        'max_image_size' => $max_image_size,
        'width_thumb' => $width_thumb,
        'width' => $width,
        'height' => $height,
        'ext_image' => $ext_image,
        
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
        'current_title' => 'Настройки',
    );

    print $xg->render($tVars);
}

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
                            $error_text = 'Такой файл уже существует';
                        else
                            unlink(files_dir . 'eshop/'. $files_del);
                        
                        if(empty($error_text)){
                            if(move_uploaded_file($_FILES['plugin_files']['tmp_name'], files_dir . 'eshop/' . $Ffile)){
                                chmod(files_dir . 'eshop/' . $Ffile, 0644);
                            } else {
                                $error_text = 'Загрузка не удалась';
                            }
                        }
                    } else {
                        $error_text = 'Нет прав на запись';
                    }
                } else {
                    $error_text = 'Размер файла больше допустимого';
                }
            } else {
                $error_text = 'Запрещеное расширение';
            }
        } else {
            $error_text = 'Файл не загружен';
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
                                $error_text = 'Ошибка при сохранении';
                            }
                        } else {
                            $error_text = 'Размер изображения больше чем '.pluginGetVariable('eshop', 'width').' ?? '.pluginGetVariable('eshop', 'height');
                        }
                    } else {
                        $error_text = 'Загруженый файл не является изображением';
                    }
                } else {
                    $error_text = 'Размер файла больше допустимого';
                }
            } else {
                $error_text = 'Недопустимое расширение';
            }
        } else {
            $error_text = 'Изображение не загружено';
        }
    }
    return array($new, $error_text);
}

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
