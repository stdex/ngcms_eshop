<?php

if (!defined('NGCMS'))
    exit('HAL');

LoadPluginLang('eshop', 'main', '', '', '#');

add_act('index', 'eshop_header_show');

register_plugin_page('eshop','','eshop');
register_plugin_page('eshop','show','show_eshop');
register_plugin_page('eshop','search','search_eshop');
register_plugin_page('eshop','stocks','stocks_eshop');
register_plugin_page('eshop','compare','compare_eshop');
register_plugin_page('eshop','currency','currency_eshop');
register_plugin_page('eshop','xml_export','xml_export_eshop');

register_plugin_page('eshop','ebasket_list','plugin_ebasket_list');
register_plugin_page('eshop','ebasket_update','plugin_ebasket_update');

register_plugin_page('eshop','order','order_eshop');

include_once(dirname(__FILE__).'/cache.php');

function eshop_header_show()
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang;

    /* print '<pre>';
    print_r ($CurrentHandler);
    print '</pre>'; */

    /* print '<pre>';
    print_r ($SYSTEM_FLAGS);
    print '</pre>';  */

    if(empty($_REQUEST['page']))
    {
        $page = $CurrentHandler['params']['page'];
    } else {
        $page = $_REQUEST['page'];
    }
    
    $pageNo = isset($page)?str_replace('%count%',intval($page), '/ �������� %count%'):'';

    switch ($CurrentHandler['handlerName'])
    {
        case '':
            $titles = str_replace(
                array ('%name_site%', '%separator%', '%group%', '%others%', '%num%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['separator'], $SYSTEM_FLAGS['info']['title']['group'],  $SYSTEM_FLAGS['info']['title']['others'], $pageNo),
                $lang['eshop']['titles']);
            break;
        case 'show':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'],  $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['eshop']['titles_common']);
            break;
        case 'search':
        case 'stocks':
        case 'compare':
            $SYSTEM_FLAGS['info']['breadcrumbs'][0]['text'] = $SYSTEM_FLAGS['info']['title']['others'];
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['eshop']['titles_common']);
            break;
        case 'ebasket_list':
        case 'order':
            $SYSTEM_FLAGS['info']['breadcrumbs'][0]['text'] = $SYSTEM_FLAGS['info']['title']['group'];
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['eshop']['titles_common']);
            break;
        
    }

    $template['vars']['titles'] = trim($titles);
}

function eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler, $tt;

    $sort = array();
    $cat = preg_match('#^[A-Za-z0-9\.\_\-]+$#s', $params['alt'])?input_filter_com(convert($params['alt'])):'';
    //$cat = isset($params['alt'])?$params['alt']:$_REQUEST['alt'];
    
    $cat = str_replace("/", "", $cat);

    $conditions = array();
    
    array_push($conditions, "p.active = 1");

    if(isset($cat) && !empty($cat))
    {
        $fCategory = input_filter_com($cat);
        
        $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY id");
        $cats = getCats($res);
        $catz_filter = array();
        recursiveCategory($cats, $fCategory);
        $catz_filter = $tt;
        if(empty($catz_filter)) {
            return redirect_eshop(home);
        }
        $catz_filter_comma_separated = implode(",", $catz_filter);
        array_push($conditions, "pc.category_id IN (".$catz_filter_comma_separated.") ");
        $sort['alt'] = $fCategory;
    }

    $order = filter_var( $_REQUEST['order'], FILTER_SANITIZE_STRING );
    if(isset($order) && !empty($order))
    {
        switch($order)
        {
            case 'date_desc':
                //$fOrder = " ORDER BY p.editdate DESC";
                $fOrder = " ORDER BY p.id DESC";
                break;
            case 'name_asc':
                $fOrder = " ORDER BY p.name ASC";
                break;
            case 'price_asc':
                $fOrder = " ORDER BY v.price ASC";
                break;
            case 'price_desc':
                $fOrder = " ORDER BY v.price DESC";
                break;
            case 'stock_desc':
                $fOrder = " ORDER BY v.stock DESC";
                break;
            case 'likes_desc':
                $fOrder = " ORDER BY p.likes DESC";
                break;
            default:
                $fOrder = " ORDER BY p.id DESC";
                break;
        }
        $x_sort['order'] = $order;
    }
    else {
        $fOrder = " ORDER BY p.id DESC";
        //$x_sort['order'] = "";
    }

    $url = pluginGetVariable('eshop', 'url');

    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
    case 'core':
            if(isset($url) && !empty($url) && empty($params['page']) && empty($_REQUEST['page']) && empty($sort))
            {
                return redirect_eshop(generateLink('eshop', ''));
            }else if(isset($url) && !empty($url) or (!empty($params['page']) or !empty($_REQUEST['page']) or !empty($sort)))
            {
                //return redirect_eshop(generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => array('cat' => $sort['cat']), 'xparams' => array(), 'paginator' => array('page', 0, false)), intval($_REQUEST['page'])));
            }
            break;
    }

    if(isset($_SESSION['eshop']['info']) && !empty($_SESSION['eshop']['info']))
    {
        $info = $_SESSION['eshop']['info'];
        unset($_SESSION['eshop']['info']);
    } else {
        $info = '';
    }


    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];

    $tpath = locatePluginTemplates(array('eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['eshop'].'eshop.tpl');

    $cat_array = array();

    foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_categories ORDER BY position ASC') as $cat_row)
    {
            if($fCategory == $cat_row['url'])
            {

                $cat_cnt = $mysql->record("SELECT COUNT(*) as CNT FROM ".prefix."_eshop_products_categories pc LEFT JOIN ".prefix."_eshop_products p ON p.id = pc.product_id WHERE pc.category_id IN (".$catz_filter_comma_separated.") AND p.active = 1 ");

                $cat_ids = $cat_row['id'];
                $i = 0;
                $location_tmp = array();
                $location = array();
                do {
                    $bcat_row = $mysql->record("SELECT * FROM ".prefix."_eshop_categories c WHERE c.id IN (".$cat_ids.")");
                    $cat_ids = $bcat_row['parent_id'];
                    $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $bcat_row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $bcat_row['url']));
                    
                    $location_tmp[] = array('text' => $bcat_row['name'],
                                            'link' => $catlink,
                    );
                    $i += 1;
                }
                while($cat_ids != 0);
                
                $location = array_merge($location, array_reverse($location_tmp));
                foreach ($location as $loc_k => $loc)
                {
                    $SYSTEM_FLAGS['info']['breadcrumbs'][$loc_k]['text'] = $loc['text'];
                    $SYSTEM_FLAGS['info']['breadcrumbs'][$loc_k]['link'] = $loc['link'];
                }

                //$SYSTEM_FLAGS['info']['breadcrumbs'][0]['text'] = $SYSTEM_FLAGS['info']['title']['others'];
                
                //$sql_cat_cnt = "SELECT COUNT(*) as CNT FROM ".prefix."_eshop_products_categories where category_id = ".db_squote($cat_row['id'])." ";

                //$cat_cnt = $mysql->result($sql_cat_cnt);
                
                $catlink = checkLinkAvailable('eshop', '')?
                    generateLink('eshop', '', array('alt' => $cat_row['url'])):
                    generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $cat_row['url']));
                
                $cat_array = array (
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
                    
                    'cnt' => $cat_cnt['CNT'],
                    
                 );

            }
    }
   
    $SYSTEM_FLAGS['meta']['description']    = ($cat_array['meta_description'])?$cat_array['meta_description']:'';
    $SYSTEM_FLAGS['meta']['keywords']       = ($cat_array['meta_keywords'])?$cat_array['meta_keywords']:'';
    $SYSTEM_FLAGS['info']['title']['others'] = $cat_array['meta_title'];
    $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['eshop']['separator']; 

    $filter = array();
    if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }


    $cmp_array = array();
    foreach ($mysql->select("select * from ".prefix."_eshop_compare where ".join(" or ", $filter)." ") as $cmp_row)
    {
        $cmp_array[] = $cmp_row['linked_fld'];
    }
    
    $limitCount = pluginGetVariable('eshop', 'count');

    $pageNo     = intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
    if ($pageNo < 1)    $pageNo = 1;
    if (!$limitStart)   $limitStart = ($pageNo - 1)* $limitCount;

    $fSort = " GROUP BY p.id ".$fOrder;
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.url AS url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url AS curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";

    $count = $mysql->result($sqlQCount);

    if($count == 0)
        return msg(array("type" => "error", "text" => "� ������ ��������� ���� ��� ���� ���������"));


    $countPages = ceil($count / $limitCount);

    if($countPages < $pageNo)
        return msg(array("type" => "error", "text" => "����������� �� ����������"));


    if ($countPages > 1 && $countPages >= $pageNo)
    {
        $paginationParams = checkLinkAvailable('eshop', '')?
            array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => $sort, 'xparams' => $x_sort, 'paginator' => array('page', 0, false)):
            array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => $x_sort, 'paginator' => array('page', 1, false));

        $navigations = LoadVariables();
        $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
    }
    
    foreach ($mysql->select($sqlQ.' LIMIT '.$limitStart.', '.$limitCount) as $row)
    {
        
        $fulllink = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('alt' => $row['url'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        $catlink = checkLinkAvailable('eshop', '')?
            generateLink('eshop', '', array('alt' => $row['calt'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

        
        $cmp_flag = in_array($row['id'], $cmp_array);

        $entries[] = array (
            'id' => $row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            
            'annotation' => $row['annotation'],
            'body' => $row['body'],
            
            'active' => $row['active'],
            'featured' => $row['featured'],
            
            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'meta_title' => $row['meta_title'],
            'meta_keywords' => $row['meta_keywords'],
            'meta_description' => $row['meta_description'],
            
            'fulllink' => $fulllink,
            
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['editdate']))?'':$row['editdate'],
            
            'views'     =>  $row['views'],
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'compare' => $cmp_flag,
            
            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

    }
        
        $fltr['order'] = $order;

        $tVars = array(
            'filter' => $fltr,
            'cat_info' => $cat_array,
            'info' => isset($info)?$info:'',
            'entries' => isset($entries)?$entries:'',
            'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
                            ),
            'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => $sort, 'xparams' => $x_sort, 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => $x_sort, 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
                                ),
            'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => $sort, 'xparams' => $x_sort, 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => $x_sort, 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
                                ),
            'tpl_url' => home.'/templates/'.$config['theme'],
            'tpl_home' => admin_url,
        );

            $template['vars']['mainblock'] .= $xt->render($tVars);
            
            
}

function search_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;

    $url = pluginGetVariable('eshop', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_eshop(generateLink('eshop', 'search'));
            }
            break;
        case 'eshop':
            if(empty($url))
            {
                return redirect_eshop(generateLink('core', 'plugin', array('plugin' => 'eshop')));
            }
            break;
    }

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = $lang['eshop']['name_search'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('eshop', 'main_template')?pluginGetVariable('eshop', 'main_template'):'main';
    $SYSTEM_FLAGS['meta']['description']    = (pluginGetVariable('eshop', 'description'))?pluginGetVariable('eshop', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']       = (pluginGetVariable('eshop', 'keywords'))?pluginGetVariable('eshop', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $tpath = locatePluginTemplates(array('search_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['search_eshop'].'search_eshop.tpl');


    if(isset($_REQUEST['keywords']) && $_REQUEST['keywords']){
        $keywords = filter_var( $_REQUEST['keywords'], FILTER_SANITIZE_STRING );
        /*
        $cat_id = intval($_REQUEST['cat_id']);
        if(empty($cat_id))
            $cat_id = 0;

        $search_in = secure_search_eshop($_REQUEST['search_in']);
        if(empty($search_in))
            $search_in = 'all';
        */
        $search = substr($keywords, 0, 64);
         if( strlen($search) < 3 )
            $output = msg(array("type" => "error", "text" => "������� �������� �����"), 1, 2);
        
        
        $keywords = array();

        $get_url = $search;

        $search = str_replace(" +", " ", $search);
        $stemmer = new Lingua_Stem_Ru();

        $tmp = explode( " ", $search );

        foreach ( $tmp as $wrd )
            $keywords[] = $stemmer->stem_word($wrd);

        $string = implode( "* ", $keywords );
        $string = $string.'*';

        $text = implode('|', $keywords);
        
        $conditions = array();

        if(isset($text) && !empty($text))
        {
            array_push($conditions, "MATCH (p.name, p.annotation, p.body) AGAINST ('{$string}' IN BOOLEAN MODE) ");
            array_push($conditions, "p.active = 1 ");
        }
        
        $limitCount = pluginGetVariable('eshop', 'count_search');

        $pageNo     = intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
        if ($pageNo < 1)    $pageNo = 1;
        if (!$limitStart)   $limitStart = ($pageNo - 1)* $limitCount;

        $fSort = " GROUP BY p.id ORDER BY p.id DESC";
        $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
        $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
        
        $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";

        $count = $mysql->result($sqlQCount);

        //if($count == 0)
        //    return msg(array("type" => "error", "text" => "� ������ ��������� ���� ��� ���� ���������"));


        $countPages = ceil($count / $limitCount);

        //if($countPages < $pageNo)
        //    return msg(array("type" => "error", "text" => "����������� �� ����������"));


        if ($countPages > 1 && $countPages >= $pageNo)
        {
            $paginationParams = checkLinkAvailable('eshop', '')?
                array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:''), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 0, false)):
                array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 1, false));

            $navigations = LoadVariables();
            $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
        }
        
        $cmp_array = array();
        foreach ($mysql->select("select * from ".prefix."_eshop_compare where cookie = ".db_squote($_COOKIE['ngTrackID'])." ") as $cmp_row)
        {
            $cmp_array[] = $cmp_row['linked_fld'];
        }
        
        foreach ($mysql->select($sqlQ.' LIMIT '.$limitStart.', '.$limitCount) as $row)
        {
            $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
            $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

            $cmp_flag = in_array($row['id'], $cmp_array);

            $entries[] = array (
                'id' => $row['id'],
                'code' => $row['code'],
                'name' => $row['name'],
                
                'annotation' => $row['annotation'],
                'body' => $row['body'],
                
                'active' => $row['active'],
                'featured' => $row['featured'],
                
                'price'                => $row['price'],
                'compare_price'        => $row['compare_price'],
                'stock'                => $row['stock'],
                
                'meta_title' => $row['meta_title'],
                'meta_keywords' => $row['meta_keywords'],
                'meta_description' => $row['meta_description'],
                
                'fulllink' => $fulllink,
                
                'date' => (empty($row['date']))?'':$row['date'],
                'editdate' => (empty($row['editdate']))?'':$row['editdate'],
                
                'views'     =>  $row['views'],
                
                'cat_name' => $row['category'],
                'cid' => $row['cid'],
                'catlink' => $catlink,
                
                'compare' => $cmp_flag,
                
                'home' => home,
                'image_filepath'    =>  $row['image_filepath'],
                'tpl_url' => home.'/templates/'.$config['theme'],
            );

        }       
        
    }


        $tVars = array(
            'search_request' => $get_url,
            'info' =>   isset($info)?$info:'',
            'entries' => isset($entries)?$entries:'',
            'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
                            ),
            'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:''), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
                                ),
            'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:''), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array('keywords' => $get_url?$get_url:''), 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
                                ),
            'tpl_url' => home.'/templates/'.$config['theme'],
            'tpl_home' => admin_url,
        );

        $template['vars']['mainblock'] .= $xt->render($tVars);
}

function stocks_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;

    $url = pluginGetVariable('eshop', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_eshop(generateLink('eshop', 'stocks'));
            }
            break;
        case 'eshop':
            if(empty($url))
            {
                return redirect_eshop(generateLink('core', 'plugin', array('plugin' => 'eshop')));
            }
            break;
    }

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = $lang['eshop']['name_stocks'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('eshop', 'main_template')?pluginGetVariable('eshop', 'main_template'):'main';
    $SYSTEM_FLAGS['meta']['description']    = (pluginGetVariable('eshop', 'description'))?pluginGetVariable('eshop', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']       = (pluginGetVariable('eshop', 'keywords'))?pluginGetVariable('eshop', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $tpath = locatePluginTemplates(array('stocks_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['stocks_eshop'].'stocks_eshop.tpl');


    if(isset($_REQUEST['mode']) && $_REQUEST['mode']){
        $mode = filter_var( $_REQUEST['mode'], FILTER_SANITIZE_STRING );
    }
    else {
        $mode = "stocks";
    }
    
    $conditions = array();
    array_push($conditions, "p.active = 1 ");
    
    switch ($mode)
    {
        case "stocks":
            array_push($conditions, "p.stocked = 1");
            $orderby = " ORDER BY p.editdate DESC ";
            break;
        default:
            array_push($conditions, "p.stocked = 1");
            $orderby = " ORDER BY p.editdate DESC ";
            break;
    }
    
    $limitCount = pluginGetVariable('eshop', 'count_stocks');

    $pageNo     = intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
    if ($pageNo < 1)    $pageNo = 1;
    if (!$limitStart)   $limitStart = ($pageNo - 1)* $limitCount;

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";

    $count = $mysql->result($sqlQCount);

    $countPages = ceil($count / $limitCount);

    if($countPages < $pageNo)
        return msg(array("type" => "error", "text" => "����������� �� ����������"));


    if ($countPages > 1 && $countPages >= $pageNo)
    {
        $paginationParams = checkLinkAvailable('eshop', '')?
            array('pluginName' => 'eshop', 'pluginHandler' => 'stocks', 'params' => array('keywords' => $get_url), 'xparams' => array(), 'paginator' => array('page', 0, false)):
            array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array('keywords' => $get_url), 'paginator' => array('page', 1, false));

        $navigations = LoadVariables();
        $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
    }
    
    $cmp_array = array();
    foreach ($mysql->select("select * from ".prefix."_eshop_compare where cookie = ".db_squote($_COOKIE['ngTrackID'])." ") as $cmp_row)
    {
        $cmp_array[] = $cmp_row['linked_fld'];
    }
    
    foreach ($mysql->select($sqlQ.' LIMIT '.$limitStart.', '.$limitCount) as $row)
    {
        $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

        $cmp_flag = in_array($row['id'], $cmp_array);

        $entries[] = array (
            'id' => $row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            
            'annotation' => $row['annotation'],
            'body' => $row['body'],
            
            'active' => $row['active'],
            'featured' => $row['featured'],
            
            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'meta_title' => $row['meta_title'],
            'meta_keywords' => $row['meta_keywords'],
            'meta_description' => $row['meta_description'],
            
            'fulllink' => $fulllink,
            
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['editdate']))?'':$row['editdate'],
            
            'views'     =>  $row['views'],
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'compare' => $cmp_flag,
            
            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

    }

    $tVars = array(
        'info' =>   isset($info)?$info:'',
        'entries' => isset($entries)?$entries:'',
        'pages' => array(
        'true' => (isset($pages) && $pages)?1:0,
        'print' => isset($pages)?$pages:''
                        ),
        'prevlink' => array(
                'true' => !empty($limitStart)?1:0,
                'link' => str_replace('%page%',
                                        "$1",
                                        str_replace('%link%',
                                            checkLinkAvailable('eshop', '')?
            generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'stocks', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
            generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
                                            isset($navigations['prevlink'])?$navigations['prevlink']:''
                                        )
                ),
                            ),
        'nextlink' => array(
                'true' => ($prev + 2 <= $countPages)?1:0,
                'link' => str_replace('%page%',
                                        "$1",
                                        str_replace('%link%',
                                            checkLinkAvailable('eshop', '')?
            generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'stocks', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
            generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2),
                                            isset($navigations['nextlink'])?$navigations['nextlink']:''
                                        )
                ),
                            ),
        'tpl_url' => home.'/templates/'.$config['theme'],
        'tpl_home' => admin_url,
    );

    $template['vars']['mainblock'] .= $xt->render($tVars);

}

function compare_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;

    $url = pluginGetVariable('eshop', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_eshop(generateLink('eshop', 'stocks'));
            }
            break;
        case 'eshop':
            if(empty($url))
            {
                return redirect_eshop(generateLink('core', 'plugin', array('plugin' => 'eshop')));
            }
            break;
    }

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = $lang['eshop']['name_compare'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('eshop', 'main_template')?pluginGetVariable('eshop', 'main_template'):'main';
    $SYSTEM_FLAGS['meta']['description']    = (pluginGetVariable('eshop', 'description'))?pluginGetVariable('eshop', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']       = (pluginGetVariable('eshop', 'keywords'))?pluginGetVariable('eshop', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $tpath = locatePluginTemplates(array('compare_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['compare_eshop'].'compare_eshop.tpl');

    $filter = array();
    if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }

    $cmp_array = array();
    foreach ($mysql->select("select * from ".prefix."_eshop_compare where ".join(" or ", $filter)." ") as $cmp_row)
    {
        $cmp_array[] = $cmp_row['linked_fld'];
    }
    
    $conditions = array();
    
    if(isset($cmp_array) && !empty($cmp_array)) {
        $compare = implode(",", $cmp_array);
        array_push($conditions, "p.id IN (".$compare.") ");
        array_push($conditions, "p.active = 1 ");
    
        $features_list = array();
        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
        {
            $features_list[] = 
                array(
                    'id' => $frow['id'],
                    'name' => $frow['name'],
                    'position' => $frow['position'],
                    'in_filter' => $frow['in_filter']
                    );
        }

        $fSort = " GROUP BY p.id ORDER BY p.id DESC";
        $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
        $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
        
        foreach ($mysql->select($sqlQ) as $row)
        {
            $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
            $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

            $cmp_flag = in_array($row['id'], $cmp_array);
            
            $qid = $row['id'];
            
            $options_array = array();
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_options LEFT JOIN ".prefix."_eshop_features ON ".prefix."_eshop_features.id=".prefix."_eshop_options.feature_id WHERE ".prefix."_eshop_options.product_id = '$qid' ORDER BY position, id") as $orow)
            {
                $options_array[$orow['id']] = $orow['value'];
            }
            
            $features_array = array();
            foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow)
            {
                $features_array[] = 
                    array(
                        'id' => $frow['id'],
                        'name' => $frow['name'],
                        'position' => $frow['position'],
                        'in_filter' => $frow['in_filter'],
                        'value' => $options_array[$frow['id']]
                        );
            }

            $entries[] = array (
                'id' => $row['id'],
                'code' => $row['code'],
                'name' => $row['name'],
                
                'annotation' => $row['annotation'],
                'body' => $row['body'],
                
                'active' => $row['active'],
                'featured' => $row['featured'],
                
                'price'                => $row['price'],
                'compare_price'        => $row['compare_price'],
                'stock'                => $row['stock'],
                
                'meta_title' => $row['meta_title'],
                'meta_keywords' => $row['meta_keywords'],
                'meta_description' => $row['meta_description'],
                
                'fulllink' => $fulllink,

                'date' => (empty($row['date']))?'':$row['date'],
                'editdate' => (empty($row['editdate']))?'':$row['editdate'],
                
                'views'     =>  $row['views'],
                
                'cat_name' => $row['category'],
                'cid' => $row['cid'],
                'catlink' => $catlink,
                
                'compare' => $cmp_flag,
                'features' => $features_array,
                
                'home' => home,
                'image_filepath'    =>  $row['image_filepath'],
                'tpl_url' => home.'/templates/'.$config['theme'],
            );

        }
        
    }

    $tVars = array(
        'info' =>   isset($info)?$info:'',
        'entries' => isset($entries)?$entries:'',
        'features_list' => isset($features_list)?$features_list:'',
        'tpl_url' => home.'/templates/'.$config['theme'],
        'tpl_home' => admin_url,
    );

    $template['vars']['mainblock'] .= $xt->render($tVars);
}

function currency_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang, $ngCookieDomain;

    $ngCurrencyID = intval(filter_var( $_REQUEST['id'], FILTER_SANITIZE_STRING ));

    if (($ngCurrencyID) != 0) {
        @setcookie('ngCurrencyID', $ngCurrencyID, time()+86400*365, '/', $ngCookieDomain, 0, 1);
    } else {
        $ngCurrencyID = $_COOKIE['ngCurrencyID'];
    }
    
    redirect_eshop($_SERVER['HTTP_REFERER']);
}



function show_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));
    $alt = preg_match('#^[A-Za-z0-9\.\_\-]+$#s', $params['alt'])?input_filter_com(convert($params['alt'])):'';

    $url = pluginGetVariable('eshop', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_eshop(generateLink('eshop', 'show', array('alt' => $alt)));
            }
            break;
        case 'eshop':
            if(empty($url))
            {
                return redirect_eshop(generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $alt)));
            }
            break;
    }

    $conditions = array();
    if(isset($alt) && !empty($alt))
    {
        array_push($conditions, "p.url = ".db_squote($alt)." ");
    }
    else {
        redirect_eshop(link_eshop());
    }

    array_push($conditions, "p.active = 1 ");

    $tpath = locatePluginTemplates(array('show_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['show_eshop'].'show_eshop.tpl');

    $fSort = " GROUP BY p.id ORDER BY p.id DESC LIMIT 1";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.url as curl, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;

    $row = $mysql->record($sqlQ);

    if(isset($row) && !empty($row))
    {
            
        $qid = $row['id'];

        $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        $cat_ids = $row['cid'];
        $i = 0;
        $location_tmp = array();
        $location = array();
        $location_tmp[] = array('text' => $row['name'],
                                'link' => $fulllink,
        );
        do {
            $bcat_row = $mysql->record("SELECT * FROM ".prefix."_eshop_categories c WHERE c.id IN (".$cat_ids.")");
            $cat_ids = $bcat_row['parent_id'];
            $catlink = checkLinkAvailable('eshop', '')?
                generateLink('eshop', '', array('alt' => $bcat_row['url'])):
                generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $bcat_row['url']));
            
            $location_tmp[] = array('text' => $bcat_row['name'],
                                    'link' => $catlink,
            );
            $i += 1;
        }
        while($cat_ids != 0);

        $location = array_merge($location, array_reverse($location_tmp));
        foreach ($location as $loc_k => $loc)
        {
            $SYSTEM_FLAGS['info']['breadcrumbs'][$loc_k]['text'] = $loc['text'];
            $SYSTEM_FLAGS['info']['breadcrumbs'][$loc_k]['link'] = $loc['link'];
        }

        /*    
            $filter = array();
            if (is_array($userROW)) {
                $filter []= '(user_id = '.db_squote($userROW['id']).')';
            }

            if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
                $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
            }

            foreach ($mysql->select("select * from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1) as $rec) {
                        $total += round($rec['price'] * $rec['count'], 2);

                        $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                        $rec['xfields'] = unserialize($rec['linked_fld']);
                        unset($rec['linked_fld']);

                        $recs []= $rec;
            }

            var_dump($recs);
        */
        
        $entriesImg = array();
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images WHERE product_id = '.$row['id'].' ORDER BY position, id ') as $row2)
        {
            $entriesImg[] = array (
                'id' => $row2['id'],
                'filepath' => $row2['filepath'],
                'product_id' => $row2['product_id'],
                'position' => $row2['position'],
            );
        }

        $features_array = array();
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_options LEFT JOIN '.prefix.'_eshop_features ON '.prefix.'_eshop_features.id='.prefix.'_eshop_options.feature_id WHERE '.prefix.'_eshop_options.product_id = '.$row['id'].' ORDER BY position, id') as $orow)
        {
            
            $features_array[] = 
                array(
                    'id' => $orow['id'],
                    'name' => $orow['name'],
                    'position' => $orow['position'],
                    'in_filter' => $orow['in_filter'],
                    'value' => $orow['value']
                    );
        }
        
        foreach ($mysql->select('SELECT p.id AS id, p.url as url, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock FROM '.prefix.'_eshop_related_products rp LEFT JOIN '.prefix.'_eshop_products p ON p.id=rp.related_id LEFT JOIN (SELECT * FROM '.prefix.'_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN '.prefix.'_eshop_variants v ON p.id = v.product_id WHERE rp.product_id = '.$row['id'].' AND p.active = 1 GROUP BY p.id ORDER BY rp.position') as $rrow)
        {
            
            $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $rrow['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $rrow['url']));
            
            $rrow['fulllink'] = $fulllink;
            
            $related_array[] = $rrow;
            /*
            $related_array[] = 
                array(
                    'name' => $rrow['name'],
                    'product_id' => $rrow['product_id'],
                    'related_id' => $rrow['related_id'],
                    'position' => $rrow['position']
                    );
            */
        }

        $SYSTEM_FLAGS['info']['title']['others'] = $row['meta_title'];
        $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];
        $SYSTEM_FLAGS['meta']['description']    = ($row['meta_description'])?$row['meta_description']:'';
        $SYSTEM_FLAGS['meta']['keywords']       = ($row['meta_keywords'])?$row['meta_keywords']:'';

        $cmode = intval(pluginGetVariable('eshop', 'views_count'));
        if ($cmode > 1) {
            // Delayed update of counters
            $mysql->query("insert into ".prefix."_eshop_products_view (id, cnt) values (".db_squote($row['id']).", 1) on duplicate key update cnt = cnt + 1");
        } else if ($cmode > 0) {
            $mysql->query("update ".prefix."_eshop_products set views=views+1 where id = ".db_squote($row['id']));
        }
        
        $cmp_id = $qid;

        $filter = array();
        if (is_array($userROW)) {                                               $filter []= '(user_id = '.db_squote($userROW['id']).')';        }
        if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {    $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';  }
        if (isset($cmp_id) && ($cmp_id != '')) {    $filter []= '(linked_fld = '.db_squote($cmp_id).')';  }
        
        $cmp_row = $mysql->record("select * from ".prefix."_eshop_compare where ".join(" or ", $filter)." ");
        
        $likes_tpath = locatePluginTemplates(array('likes_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
        $likes_xt = $twig->loadTemplate($likes_tpath['likes_eshop'].'likes_eshop.tpl');

        $likes = $mysql->record("SELECT COUNT(*) as count FROM ".prefix."_eshop_products_likes l WHERE l.product_id='".$qid."'");
        
        $likes_tVars = array (
            'count' => $likes['count'],
            'id' => $qid,
        );
        
        $comments_tpath = locatePluginTemplates(array('comments.form_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
        $comments_xt = $twig->loadTemplate($comments_tpath['comments.form_eshop'].'comments.form_eshop.tpl');

        $comments_tVars = array (
            'id' => $qid,
        );

        $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));

        $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

        $tVars = array (
            'id' => $row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            
            'annotation' => $row['annotation'],
            'body' => $row['body'],
            
            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'active' => $row['active'],
            'featured' => $row['featured'],
            
            'meta_title' => $row['meta_title'],
            'meta_keywords' => $row['meta_keywords'],
            'meta_description' => $row['meta_description'],
            
            'full_link'  => $fulllink,
            'edit_link' => admin_url."/admin.php?mod=extra-config&plugin=eshop&action=edit_product&id=".$row['id']."",
            
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['editdate']))?'':$row['editdate'],
            
            'views'     =>  $row['views']+1,
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
            
            'entriesImg' => isset($entriesImg)?$entriesImg:'',
            'entriesFeatures' => isset($features_array)?$features_array:'',
            'entriesRelated' => isset($related_array)?$related_array:'',
            'compare' => $cmp_row,
            
            'likes_form' => $likes_xt->render($likes_tVars),
            'comments_form' => $comments_xt->render($comments_tVars),
        );


        $template['vars']['mainblock'] .= $xt->render($tVars);

        
    } else {
        error404();
    }
}

function xml_export_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler, $SUPRESS_TEMPLATE_SHOW;

    $SUPRESS_TEMPLATE_SHOW = 1;
    $SUPRESS_MAINBLOCK_SHOW = 1;
    /**/
    @header('Content-type: text/xml; charset=windows-1251');
    $SYSTEM_FLAGS['http.headers'] = array(
        'content-type'      => 'application/xml; charset=charset=windows-1251',
        'cache-control'     => 'private',
    );
    

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

    $tpath = locatePluginTemplates(array('xml_export_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath['xml_export_eshop'].'xml_export_eshop.tpl');

    //$limitCount = "10000";

    $fSort = " GROUP BY p.id ORDER BY p.id DESC ";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN (SELECT * FROM ".prefix."_eshop_images ORDER BY position, id) i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
    
     
    foreach ($mysql->select($sqlQ) as $row)
    {
        
        $entriesImg = array();
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_images WHERE product_id = '.$row['id'].' ') as $row2)
        {
            $entriesImg[] = array (
                'id' => $row2['id'],
                'filepath' => $row2['filepath'],
                'product_id' => $row2['product_id'],
                'position' => $row2['position'],
            );
        }

        $features_array = array();
        foreach ($mysql->select('SELECT * FROM '.prefix.'_eshop_options LEFT JOIN '.prefix.'_eshop_features ON '.prefix.'_eshop_features.id='.prefix.'_eshop_options.feature_id WHERE '.prefix.'_eshop_options.product_id = '.$row['id'].' ORDER BY position, id') as $orow)
        {
            $features_array[] = 
                array(
                    'id' => $orow['id'],
                    'name' => $orow['name'],
                    'position' => $orow['position'],
                    'in_filter' => $orow['in_filter'],
                    'value' => $orow['value']
                    );
        }
        
        $fulllink = checkLinkAvailable('eshop', 'show')?
                        generateLink('eshop', 'show', array('alt' => $row['url'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $row['url']));
        $catlink = checkLinkAvailable('eshop', '')?
                        generateLink('eshop', '', array('alt' => $row['curl'])):
                        generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $row['curl']));

        $entries[] = array (
            'id' => $row['id'],
            'code' => $row['code'],
            'name' => $row['name'],
            
            'annotation' => $row['annotation'],
            'body' => $row['body'],
            
            'active' => $row['active'],
            'featured' => $row['featured'],
            
            'price'                => $row['price'],
            'compare_price'        => $row['compare_price'],
            'stock'                => $row['stock'],
            
            'meta_title' => $row['meta_title'],
            'meta_keywords' => $row['meta_keywords'],
            'meta_description' => $row['meta_description'],
            
            'fulllink' => $fulllink,
            
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['editdate']))?'':$row['editdate'],
            
            'views'     =>  $row['views'],
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'images' => $entriesImg,
            'features' => $features_array,

            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

    }

    $tVars = array(
        'cat_info' => $cat_array,
        'entries' => isset($entries)?$entries:'',
        'tpl_url' => home.'/templates/'.$config['theme'],
        'tpl_home' => admin_url,
    );

    print ( $xt->render($tVars) );
}


//
// �������� ���������� �������
function plugin_ebasket_list(){
    global $mysql, $twig, $userROW, $template, $ip, $SYSTEM_FLAGS, $lang;

    // ���������� ������� �������
    $filter = array();
    if (is_array($userROW)) {
        $filter []= '(user_id = '.db_squote($userROW['id']).')';
    }

    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
        $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
    }

    // ��������� �������
    $recs = array();
    $total = 0;
    if (count($filter)) {
        foreach ($mysql->select("select * from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1) as $rec) {
            $total += round($rec['price'] * $rec['count'], 2);

            $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
            $rec['xfields'] = unserialize($rec['linked_fld']);
            unset($rec['linked_fld']);

            $recs []= $rec;
        }
    }

    if (!empty($_POST))
    {
        $SQL['name'] = filter_var( $_REQUEST['userInfo']['fullName'], FILTER_SANITIZE_STRING );
        if(empty($SQL['name']))
        {
            $error_text[] = '��� �� ������';
        }

        $SQL['email'] = filter_var( $_REQUEST['userInfo']['email'], FILTER_SANITIZE_STRING );
        if(empty($SQL['email']))
        {
            $error_text[] = 'Email �� �����';
        }

        $SQL['phone'] = filter_var( $_REQUEST['userInfo']['phone'], FILTER_SANITIZE_STRING );
        if(empty($SQL['phone']))
        {
            $error_text[] = '������� �� �����';
        }
        
        $SQL['address'] = filter_var( $_REQUEST['userInfo']['deliverTo'], FILTER_SANITIZE_STRING );
        if(empty($SQL['address']))
        {
            $error_text[] = '����� �������� �� �����';
        }
        
        $SQL['comment'] = filter_var( $_REQUEST['userInfo']['commentText'], FILTER_SANITIZE_STRING );
        
        $SQL['dt'] = time() + ($config['date_adjust'] * 60);
        $SQL['ip'] =  $ip;
        
        $SQL['type'] =  "1";
        
        $SQL['paid'] = 0;
        $SQL['total_price'] = $total;
        
        if(isset($userROW)) {
            $SQL['author_id'] = $userROW['id'];
        }
        
        $SQL['uniqid'] = substr(str_shuffle(MD5(microtime())), 0, 10);

        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query('INSERT INTO '.prefix.'_eshop_orders SET '.implode(', ',$vnames).' ');
            
            $qid = $mysql->lastid('eshop_orders');
            
            if($qid != NULL) {
                
                foreach ($mysql->select("select * from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1) as $rec) {
                    $r_linked_id = $rec['linked_id'];
                    $r_title = $rec['title'];
                    $r_count = $rec['count'];
                    $r_price = $rec['price'];
                    $r_linked_fld = $rec['linked_fld'];
                    $mysql->query("INSERT INTO ".prefix."_eshop_order_basket (`order_id`, `linked_id`, `title`, `count`, `price`, `linked_fld`) VALUES ('$qid','$r_linked_id','$r_title','$r_count','$r_price','$r_linked_fld')");
                }
                
                if (count($filter)) {
                    $mysql->query("delete from ".prefix."_eshop_ebasket where ".join(" or ", $filter));
                }

                // ���������� ������� �������
                $filter = array();
                if ($qid) {
                    $filter []= '(order_id = '.db_squote($qid).')';
                }

                $total = 0;
                foreach ($mysql->select("select * from ".prefix."_eshop_order_basket where ".join(" or ", $filter), 1) as $rec) {
                            $total += round($rec['price'] * $rec['count'], 2);

                            $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                            $rec['xfields'] = unserialize($rec['linked_fld']);
                            unset($rec['linked_fld']);
                            $basket []= $rec;
                }
                
                $notify_tpath = locatePluginTemplates(array('mail/lfeedback'), 'eshop', pluginGetVariable('eshop', 'localsource'));
                $notify_xt = $twig->loadTemplate($notify_tpath['mail/lfeedback'].'mail/'.'lfeedback.tpl');

                $pVars = array(
                    'recs'      => count($recs),
                    'entries'   => $recs,
                    'total'     => sprintf('%9.2f', $total),
                    'vnames'   => $SQL,
                );
            
                $mailBody = $notify_xt->render($pVars);
                $mailSubject = "����� ����� � �����";
                $mailTo = pluginGetVariable('eshop', 'email_notify_orders');
                $mail_from = pluginGetVariable('eshop', 'email_notify_back');

                if($mail_from == "") {
                    $mail_from = false;
                }

                if($mailTo != "") {
                    sendEmailMessage($mailTo, $mailSubject, $mailBody, $filename = false, $mail_from, $ctype = 'text/html');
                }
                
                $notify_text[] = '����� ��������.';
                
                $order_link = checkLinkAvailable('eshop', 'order')?
                generateLink('eshop', 'order', array(), array('id' => $qid, 'uniqid' => $SQL['uniqid'])):
                generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'order'), array(), array('id' => $qid,'uniqid' => $SQL['uniqid']));
                
                return redirect_eshop($order_link);

            }
            
        }

    }

    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            //$error_input .= msg(array("type" => "error", "text" => $error));
            $error_input .= "<p>".$error."</p>";
        }
    } else {
        $error_input ='';
    }

    if(!empty($notify_text))
    {
        foreach($notify_text as $notify)
        {
            $notify_input .= msg(array("type" => "info", "text" => $notify));
        }
    } else {
        $notify_input ='';
    }

    foreach ($SQL as $k => $v) { 
        $tFormEntry[$k] = $v;
    }
        
    $tFormEntry['error'] = $error_text;
    $tFormEntry['notify'] = $notify_text;
    $tFormEntry['id'] = $qid;
    
    $basket_link = checkLinkAvailable('eshop', 'ebasket_list')?
            generateLink('eshop', 'ebasket_list', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'ebasket_list'), array());


    $tVars = array(
        'formEntry' => $tFormEntry,
        'recs'      => count($recs),
        'entries'   => $recs,
        'total'     => sprintf('%9.2f', $total),
        'basket_link' => $basket_link,
    );

    $tpath = locatePluginTemplates(array('ebasket/list'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    $xt = $twig->loadTemplate($tpath['ebasket/list'].'ebasket/'.'list.tpl');

    $template['vars']['mainblock'] = $xt->render($tVars);
    
    $SYSTEM_FLAGS['info']['title']['others'] = "";
    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_basket'];
    $SYSTEM_FLAGS['meta']['description']    = "";
    $SYSTEM_FLAGS['meta']['keywords']       = "";

}


function order_eshop(){
    global $mysql, $twig, $userROW, $template, $ip, $SYSTEM_FLAGS, $lang;

    // ���������� ������� �������
    $filter = array();
    
    $SQL['id'] = filter_var( $_REQUEST['id'], FILTER_SANITIZE_STRING );
    if(empty($SQL['id']))
    {
        $error_text[] = 'ID �� ������';
    }
    else {
        $filter []= '(id = '.db_squote($SQL['id']).')';
    }

    $SQL['uniqid'] = filter_var( $_REQUEST['uniqid'], FILTER_SANITIZE_STRING );
    if(empty($SQL['uniqid']))
    {
        $error_text[] = 'Uniqid �� �����';
    }
    else {
        $filter []= '(uniqid = '.db_squote($SQL['uniqid']).')';
    }


    /*
    if (is_array($userROW)) {
        $filter []= '(user_id = '.db_squote($userROW['id']).')';
    }

    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
        $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
    }
    * */

    if(empty($error_text))
    {
        
        $sqlQ = "SELECT * FROM ".prefix."_eshop_orders ".(count($filter)?"WHERE ".implode(" AND ", $filter):'')." LIMIT 1";
        $row = $mysql->record($sqlQ);
        
        if(!empty($row)) {
            $qid = $row['id'];

            // ���������� ������� �������
            $filter = array();
            if ($qid) {
                $filter []= '(order_id = '.db_squote($qid).')';
            }

            $total = 0;
            foreach ($mysql->select("select * from ".prefix."_eshop_order_basket where ".join(" or ", $filter), 1) as $rec) {
                        $total += round($rec['price'] * $rec['count'], 2);
                        $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                        $rec['xfields'] = unserialize($rec['linked_fld']);
                        unset($rec['linked_fld']);
                        $basket []= $rec;
            }
        }
        else {
            $error_text[] = '������� �������� ������';
        }

    }
    
    if(!empty($error_text))
    {
        foreach($error_text as $error)
        {
            //$error_input .= msg(array("type" => "error", "text" => $error));
            $error_input .= "<p>".$error."</p>";
        }
    } else {
        $error_input ='';
    }

    foreach ($row as $k => $v) { 
        $tFormEntry[$k] = $v;
    }
        
    $tFormEntry['error'] = $error_text;
    $tFormEntry['id'] = $qid;
    
    $basket_link = checkLinkAvailable('eshop', 'ebasket_list')?
            generateLink('eshop', 'ebasket_list', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'ebasket_list'), array());

    $tVars = array(
        'formEntry' => $tFormEntry,
        'recs'      => count($basket),
        'entries'   => $basket,
        'total'     => sprintf('%9.2f', $total),
        'basket_link' => $basket_link,
    );

    $tpath = locatePluginTemplates(array('order_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    $xt = $twig->loadTemplate($tpath['order_eshop'].'order_eshop.tpl');

    $template['vars']['mainblock'] = $xt->render($tVars);
    
    $SYSTEM_FLAGS['info']['title']['others'] = "";
    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_order'];
    $SYSTEM_FLAGS['meta']['description']    = "";
    $SYSTEM_FLAGS['meta']['keywords']       = "";
}


function build_tree($cats,$parent_id,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li><a href="'.$cat['url'].'">'.$cat['cat_name'].'</a> ('.$cat['num'].')';
                $tree .=  build_tree($cats,$cat['id']);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li>'.$cat['cat_name'].' #'.$cat['id'];
            $tree .=  build_tree($cats,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}

function getCats($res){

    $levels = array();
    $tree = array();
    $cur = array();

    while($rows = mysql_fetch_assoc($res)){

        $cur = &$levels[$rows['id']];
        $cur['parent_id'] = $rows['parent_id'];
        $cur['name'] = $rows['name'];
        $cur['url'] = $rows['url'];
        $cur['image'] = $rows['image'];
        $cur['description'] = $rows['description'];
        $cur['active'] = $rows['active'];
            
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
        if($v['url']==$flg) {
            $out = array_merge($out, array_keys($v['children']));
            $current_id = $k;
            /*
            foreach($v['children'] as $k1=>$v1){
                if(array_key_exists("children",$v1)) {
                    getChildIdsArray($v, $k1);
                }
            }
            */
        }

    }
    
    $out[] = $current_id;
    
    return $out;
}

function recursiveCategory($arr, $flg){
    global $tt;
    foreach($arr as $k=>$v){
 
        if($v['url']==$flg) { 
            $tt[] = $k;
            if(!empty($v['children'])){     
                $tt = array_merge($tt, array_keys($v['children']));
            }
        }
    
        if(!empty($v['children'])){     
            recursiveCategory($v['children'], $flg);
        }
    }
    //return $outt;
}


function input_filter_com($text)
{
    $text = trim($text);
    $search = array("<", ">");
    $replace = array("&lt;", "&gt;");
    $text = preg_replace("/(&amp;)+(?=\#([0-9]{2,3});)/i", "&", str_replace($search, $replace, $text));
    return $text;
}


function link_eshop()
{
    $eshopURL = checkLinkAvailable('eshop', '')?
                    generateLink('eshop', ''):
                    generateLink('core', 'plugin', array('plugin' => 'eshop'));

    return $eshopURL;
}

function LoadVariables()
{
    $tpath = locatePluginTemplates(array(':'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    return parse_ini_file($tpath[':'].'/variables.ini', true);
}

function redirect_eshop($url)
{
    if (headers_sent()) {
        echo "<script>document.location.href='{$url}';</script>\n";
        exit;
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$url}");
        exit;
    }
}

function plugin_eshop_cron($isSysCron, $handler)
{
global $tpl, $cron, $mysql, $config, $lang, $parse, $PFILTERS;

    if ($handler == 'eshop_views') {
        eshopUpdateDelayedCounters();
    }

}

// Update delayed news counters
function eshopUpdateDelayedCounters() {
    global $mysql;

    // Lock tables
    $mysql->query("lock tables ".prefix."_eshop_products_view write, ".prefix."_eshop write");

    // Read data and update counters
    foreach ($mysql->select("select * from ".prefix."_eshop_products_view") as $vrec) {
        $mysql->query("update ".prefix."_eshop_products set views = views + ".intval($vrec['cnt'])." where id = ".intval($vrec['id']));
    }

    // Truncate view table
    //$mysql->query("truncate table ".prefix."_eshop_view");
    // DUE TO BUG IN MYSQL - USE DELETE + OPTIMIZE
    $mysql->query("delete from ".prefix."_eshop_products_view");
    $mysql->query("optimize table ".prefix."_eshop_products_view");

    // Unlock tables
    $mysql->query("unlock tables");

    return true;
}

// Generate a random character string
function rand_str($length = 10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
 {
     // Length of character list
     $chars_length = (strlen($chars) - 1);

     // Start our string
     $string = $chars{rand(0, $chars_length)};

     // Generate random string
     for ($i = 1; $i < $length; $i = strlen($string))
     {
         // Grab a random character from our list
         $r = $chars{rand(0, $chars_length)};

         // Make sure the same two characters don't appear next to each other
         if ($r != $string{$i - 1}) $string .=  $r;
     }

     // Return the string
     return $string;
 }

function secure_search_eshop($text)
{
    $text = convert(trim($text));
    $text = preg_replace("/[^\w\x7F-\xFF\s]/", "", $text);
    return secure_html($text);
}

class Lingua_Stem_Ru
{
    public $VERSION = "0.02";
    public $Stem_Caching = 0;
    public $Stem_Cache = array();
    public $VOWEL = '/���������/';
    public $PERFECTIVEGROUND = '/((��|����|������|��|����|������)|((?<=[��])(�|���|�����)))$/';
    public $REFLEXIVE = '/(�[��])$/';
    public $ADJECTIVE = '/(��|��|��|��|���|���|��|��|��|��|��|��|��|��|���|���|���|���|��|��|��|��|��|��|��|��)$/';
    public $PARTICIPLE = '/((���|���|���)|((?<=[��])(��|��|��|��|�)))$/';
    public $VERB = '/((���|���|���|����|����|���|���|���|��|��|��|��|��|��|��|���|���|���|��|���|���|��|��|���|���|���|���|��|�)|((?<=[��])(��|��|���|���|��|�|�|��|�|��|��|��|��|��|��|���|���)))$/';
    public $NOUN = '/(�|��|��|��|��|�|����|���|���|��|��|�|���|��|��|��|�|���|��|���|��|��|��|�|�|��|���|��|�|�|��|��|�|��|��|�)$/';
    public $RVRE = '/^(.*?[���������])(.*)$/';
    public $DERIVATIONAL = '/[^���������][���������]+[^���������]+[���������].*(?<=�)���?$/';

    public function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    public function m($s, $re)
    {
        return preg_match($re, $s);
    }

    public function stem_word($word)
    {
        $word = strtolower($word);
        $word = strtr($word, '�', '�');

        if($this->Stem_Caching && isset($this->Stem_Cache[$word]))
        {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do
        {
            if(!preg_match($this->RVRE, $word, $p)) break;
            $start = $p[1];
            $RV = $p[2];
            if(!$RV) break;

            if(!$this->s($RV, $this->PERFECTIVEGROUND, ''))
            {
                $this->s($RV, $this->REFLEXIVE, '');

                if($this->s($RV, $this->ADJECTIVE, ''))
                {
                    $this->s($RV, $this->PARTICIPLE, '');
                } else {
                    if(!$this->s($RV, $this->VERB, ''))
                    {
                        $this->s($RV, $this->NOUN, '');
                    }
                }
            }

            $this->s($RV, '/�$/', '');


            if($this->m($RV, $this->DERIVATIONAL))
            {
                $this->s($RV, '/����?$/', '');
            }

            if(!$this->s($RV, '/�$/', ''))
            {
                $this->s($RV, '/����?/', '');
                $this->s($RV, '/��$/', '�');
            }

            $stem = $start.$RV;
        } while(false);
            if($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
            return $stem;
    }

    public function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if($caching_level)
        {
            if(!$this->m($caching_level, '/^[012]$/'))
            {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    public function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}
