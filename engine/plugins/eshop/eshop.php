<?php

if (!defined('NGCMS'))
    exit('HAL');

LoadPluginLang('eshop', 'main', '', '', '#');
add_act('index', 'eshop_header_show');
register_plugin_page('eshop','','eshop');
register_plugin_page('eshop','show','show_eshop');
register_plugin_page('eshop','search','search_eshop');

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

    $pageNo = isset($page)?str_replace('%count%',intval($page), '/ —траница %count%'):'';

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
                $lang['eshop']['titles_show']);
            break;
        case 'search':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['eshop']['titles_search']);
            break;
    }

    $template['vars']['titles'] = trim($titles);
}

function eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;

    $sort = array();
    $cat = isset($params['cat'])?$params['cat']:$_REQUEST['cat'];

    $conditions = array();

    if(isset($cat) && !empty($cat))
    {
        $fCategory = input_filter_com($cat);
        
        $res = mysql_query("SELECT * FROM ".prefix."_eshop_categories ORDER BY id");
        $cats = getCats($res);
        $catz_filter = array();
        $catz_filter = getChildIdsArray($cats, $fCategory, 0);
        $catz_filter[] = $fCategory;
        $catz_filter_comma_separated = implode(",", $catz_filter);

        array_push($conditions, "pc.category_id IN (".$catz_filter_comma_separated.") ");
        $sort['cat'] = $fCategory;
    }


    $sorting = $cat_id;

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
            if($_REQUEST['cat'] == $cat_row['id'] or $params['cat'] == $cat_row['id'])
            {
                
                $catlink = checkLinkAvailable('eshop', '')?
                    generateLink('eshop', '', array('cat' => $cat_row['id'])):
                    generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $cat_row['id']));
                
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
                    
                 );
                
                $SYSTEM_FLAGS['meta']['description']	= ($cat_row['meta_description'])?$cat_row['meta_description']:'';
                $SYSTEM_FLAGS['meta']['keywords']		= ($cat_row['meta_keywords'])?$cat_row['meta_keywords']:'';
                $SYSTEM_FLAGS['info']['title']['others'] = str_replace( array( '{name}' ), array($cat_row['meta_title']), $lang['eshop']['sorting']);
                $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['eshop']['separator'];
            }
            else {
                $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['eshop']['separator'];
                $SYSTEM_FLAGS['meta']['description']	= '';
                $SYSTEM_FLAGS['meta']['keywords']		= '';
            }
    }

    $limitCount = pluginGetVariable('eshop', 'count');

    $pageNo		= intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
    if ($pageNo < 1)	$pageNo = 1;
    if (!$limitStart)	$limitStart = ($pageNo - 1)* $limitCount;

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
    
    $sqlQCount = "SELECT COUNT(*) as CNT FROM (".$sqlQ. ") AS T ";

    $count = $mysql->result($sqlQCount);

    if($count == 0)
        return msg(array("type" => "error", "text" => "¬ данной категории пока что нету продукции"));


    $countPages = ceil($count / $limitCount);

    if($countPages < $pageNo)
        return msg(array("type" => "error", "text" => "ѕодстраницы не существует"));


    if ($countPages > 1 && $countPages >= $pageNo)
    {
        $paginationParams = checkLinkAvailable('eshop', '')?
            array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => $sort, 'xparams' => array(), 'paginator' => array('page', 0, false)):
            array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => $sort, 'paginator' => array('page', 1, false));

        $navigations = LoadVariables();
        $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
    }
    
    foreach ($mysql->select($sqlQ.' LIMIT '.$limitStart.', '.$limitCount) as $row)
    {
        $fulllink = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('id' => $row['id'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $row['id']));
        $catlink = checkLinkAvailable('eshop', '')?
            generateLink('eshop', '', array('cat' => $row['cid'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $row['cid']));


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
            
            'views'		=>	$row['views'],
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

    }

        if ($limitStart)
        {
            $prev = floor($limitStart / $limitCount);
            $PageLink = checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev);

            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['prevlink']));
        } else {
            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = "";
            $prev = 0;
        }

        if (($prev + 2 <= $countPages))
        {
            $PageLink = checkLinkAvailable('eshop', '')?
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2);
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['nextlink']));
        } else {
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = "";
        }

        $tVars = array(
            'cat_info' => $cat_array,
            'info' =>	isset($info)?$info:'',
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
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
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
                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
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
    $SYSTEM_FLAGS['meta']['description']	= (pluginGetVariable('eshop', 'description'))?pluginGetVariable('eshop', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']		= (pluginGetVariable('eshop', 'keywords'))?pluginGetVariable('eshop', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $tpath = locatePluginTemplates(array('search_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['search_eshop'].'search_eshop.tpl');

    if(isset($_REQUEST['submit']) && $_REQUEST['submit']){
        $keywords = secure_search_eshop($_REQUEST['keywords']);
        $cat_id = intval($_REQUEST['cat_id']);
        if(empty($cat_id))
            $cat_id = 0;

        $search_in = secure_search_eshop($_REQUEST['search_in']);
        if(empty($search_in))
            $search_in = 'all';

        $search = substr($keywords, 0, 64);
         if( strlen($search) < 3 )
            $output = msg(array("type" => "error", "text" => "—лишком короткое слово"), 1, 2);

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

        if(isset($params['page']))
            $pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
        else
            $pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;

        $limitCount = intval(pluginGetVariable('eshop', 'count_search'));

        if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;

        if($cat_id)
            $cats_id = " AND a.`cat_id` = '{$cat_id}'";
        else
            $cats_id = NULL;

        switch($search_in){
            case 'all':$sql_count = "SELECT COUNT(*) FROM ".prefix."_eshop AS a
                                    WHERE MATCH (a.announce_name, a.announce_description) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
            case 'text':$sql_count = "SELECT COUNT(*) FROM ".prefix."_eshop AS a
                                    WHERE MATCH (a.announce_description) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
            case 'title':$sql_count = "SELECT COUNT(*) FROM ".prefix."_eshop AS a
                                    WHERE MATCH (a.announce_name) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
        }

        $count = $mysql->result($sql_count);

        $countPages = ceil($count / $limitCount);
        if($countPages < $pageNo)
            $output = msg(array("type" => "error", "text" => "ѕодстраницы не существует"), 1, 2);

        if ($pageNo < 1) $pageNo = 1;
        if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;

        if ($countPages > 1 && $countPages >= $pageNo){

            $paginationParams = checkLinkAvailable('eshop', 'search')?
                array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false)):
                array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false));

            $navigations = LoadVariables();
            $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
        }

        switch($search_in){
            case 'all': $sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_eshop a LEFT JOIN '.prefix.'_eshop_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_eshop_images c ON a.id = c.zid WHERE MATCH (a.announce_name, a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_name, a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
            case 'text':$sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_eshop a LEFT JOIN '.prefix.'_eshop_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_eshop_images c ON a.id = c.zid WHERE MATCH (a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
            case 'title':$sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_eshop a LEFT JOIN '.prefix.'_eshop_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_eshop_images c ON a.id = c.zid WHERE MATCH (a.announce_name) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_name) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
        }

        foreach ($mysql->select($sql_two) as $row_two){
            /* print '<pre>';
            print_r ($row_two);
            print '</pre>'; */

            $fulllink = checkLinkAvailable('eshop', 'show')?
                generateLink('eshop', 'show', array('id' => $row_two['aid'])):
                generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $row_two['aid']));
            $catlink = checkLinkAvailable('eshop', '')?
                generateLink('eshop', '', array('cat' => $row_two['bid'])):
                generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $row_two['bid']));


            $tEntry[] = array (
                'aid'					=>	$row_two['aid'],
                'bid'					=>	$row_two['bid'],
                'date'					=>	$row_two['date'],
                'editdate'				=>	$row_two['editdate'],
                'views'					=>	$row_two['views'],
                'announce_name'			=>	$row_two['announce_name'],
                'author'				=>	$row_two['author'],
                'author_id'				=>	$row_two['author_id'],
                'author_email'			=>	$row_two['author_email'],
                'announce_period'		=>	$row_two['announce_period'],
                'announce_description'	=>	preg_replace("/\b(".$text.")(.*?)\b/i", "<span style='color:red; font-weight:bold'>\\0</span>", eshop_bbcode_p($row_two['announce_description'])),
                'announce_contacts'		=>	$row_two['announce_contacts'],
                'vip_added'				=>	$row_two['vip_added'],
                'vip_expired'			=>	$row_two['vip_expired'],
                'fulllink'				=>	$fulllink,
                'catlink'				=>	$catlink,
                'cat_name'				=>	$row_two['cat_name'],
                'pid'					=>	$row_two['pid'],
                'filepath'				=>	$row_two['filepath'],
            );

        }

        if( empty($row_two) )
            $output = msg(array("type" => "error", "text" => "ѕо вашему запросу <b>".$get_url."</b> ничего не найдено"), 1, 2);
    }else{
            $res = mysql_query("SELECT * FROM ".prefix."_eshop_cat ORDER BY id");
            $cats = getCats($res);
            $options = getTree($cats, $row['cat_id'], 0);

        //	$tVars['options'] = $options;

        /*foreach ($mysql->select('SELECT `id`, `title` FROM `'.prefix.'_forum_forums` ORDER BY `position`') as $row){
            $tEntry[] = array (
                'forum_id' => $row['id'],
                'forum_name' => $row['title'],
            );
        }*/

    }

    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'',
        'options' => isset($options)?$options:'',
        'output'	  =>  $output,
        'get_url'	  =>  $get_url,
        'submit' => (isset($_REQUEST['submit']) && $_REQUEST['submit'])?0:1,
        'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
        ),
        'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', 'search')?
                                                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'xparams' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)):
                                                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop', 'handler' => 'search'), 'xparams' => array('keywords' => isset($get_url)?$get_url:'', 'cat_id' => isset($cat_id)?$cat_id:'', 'search_in' => isset($search_in)?$search_in:'', 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false)),
                                                    $prev = floor((isset($limitStart) && $limitStart)?$limitStart:10 / (isset($limitCount) && $limitCount)?$limitCount:'5')),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
        ),
        'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('eshop', 'search')?
                                                generatePageLink(array('pluginName' => 'eshop', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'xparams' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false)), $prev+2):
                                                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'eshop', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'ќтправить'), 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
        ),
    );

    //$output = $xt->render($tVars);
    $template['vars']['mainblock'] .= $xt->render($tVars);

}

function show_eshop($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));
//	$name = preg_match('/^[a-zA-Z0-9_\xC0-\xD6\xD8-\xF6]+$/', $params['name'])?input_filter_com(convert($params['name'])):'';

    $url = pluginGetVariable('eshop', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_eshop(generateLink('eshop', 'show', array('id' => $id)));
            }
            break;
        case 'eshop':
            if(empty($url))
            {
                return redirect_eshop(generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $id)));
            }
            break;
    }

    $conditions = array();
    if(isset($id) && !empty($id))
    {
        array_push($conditions, "p.id = ".$id." ");
    }
    else {
        redirect_eshop(link_eshop());
    }

    $tpath = locatePluginTemplates(array('show_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'), pluginGetVariable('eshop','localskin'));
    $xt = $twig->loadTemplate($tpath['show_eshop'].'show_eshop.tpl');

    $fSort = " GROUP BY p.id ORDER BY p.id DESC LIMIT 1";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;

    $row = $mysql->record($sqlQ);
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


    $SYSTEM_FLAGS['info']['title']['others'] = $row['meta_title'];
    $SYSTEM_FLAGS['info']['title']['group'] = $lang['eshop']['name_plugin'];
    $SYSTEM_FLAGS['meta']['description']	= ($row['meta_description'])?$row['meta_description']:'';
    $SYSTEM_FLAGS['meta']['keywords']		= ($row['meta_keywords'])?$row['meta_keywords']:'';

    if(isset($row) && !empty($row))
    {

        $cmode = intval(pluginGetVariable('eshop', 'views_count'));
        if ($cmode > 1) {
            // Delayed update of counters
            $mysql->query("insert into ".prefix."_eshop_products_view (id, cnt) values (".db_squote($row['id']).", 1) on duplicate key update cnt = cnt + 1");
        } else if ($cmode > 0) {
            $mysql->query("update ".prefix."_eshop_products set views=views+1 where id = ".db_squote($row['id']));
        }

        $fulllink = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('id' => $row['id'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $row['id']));

        $catlink = checkLinkAvailable('eshop', '')?
            generateLink('eshop', '', array('cat' => $row['cid'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop'), array('cat' => $row['cid']));

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
            
            'views'		=>	$row['views']+1,
            
            'cat_name' => $row['category'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            
            'home' => home,
            'image_filepath'    =>  $row['image_filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
            
            'entriesImg' => isset($entriesImg)?$entriesImg:'',
            'entriesFeatures' => isset($features_array)?$features_array:'',
        );

        $template['vars']['mainblock'] .= $xt->render($tVars);
    } else {
        error404();
    }
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

    if ($handler == 'eshop_expired') {
        eshopUpdateExpiredAnnounces();
    }

    if ($handler == 'eshop_views') {
        eshopUpdateDelayedCounters();
    }

}

// Update expired announces
function eshopUpdateExpiredAnnounces() {
global $tpl, $cron, $mysql, $config, $lang, $parse, $PFILTERS;
        
        foreach ($mysql->select("select * from ".prefix."_eshop where active = 1 AND (datediff(NOW(), FROM_UNIXTIME(vip_expired)) > 0) AND vip_expired != 0") as $irow) {
            $mysql->query("UPDATE ".prefix."_eshop SET vip_expired = '', vip_added = '' WHERE id = '".$irow['id']."' ");
        }
        
        foreach ($mysql->select("select * from ".prefix."_eshop where active = 1 AND datediff(NOW(),FROM_UNIXTIME(editdate)) > announce_period * 30") as $irow) {

        $hashcode = rand_str();

        $mysql->query("UPDATE ".prefix."_eshop SET active = 0, expired = '".$hashcode."' WHERE id = '".$irow['id']."' ");

            //Email informer
            if($irow['uid'] != 0) { $alink = generatePluginLink('uprofile', 'show', array('name' => $irow['name'], 'id' => $irow['uid']), array(), false, true); }
            else { $alink = ''; }

            $body = str_replace(
                    array(	'{username}',
                            '[userlink]',
                            '[/userlink]',
                            '{description}',
                            '{announcename}',
                            '{expired_expend}',
                            ),
                    array(	$irow['name'],
                            ($irow['uid'])?'<a href="'.$alink.'">':'',
                            ($irow['uid'])?'</a>':'',
                            secure_html($irow['announce_description']),
                            $irow['announce_name'],
                            home.generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'expend'), array('id' => $irow['id'],'hashcode' => $hashcode)),
                            ), $lang['eshop']['mail_exp']
                );

                zzMail($irow['author_email'], $lang['eshop']['mail_exp_title'], $body, 'html');
        }

        generate_entries_cnt_cache(true);
        generate_catz_cache(true);

}

// Update delayed news counters
function eshopUpdateDelayedCounters() {
    global $mysql;

    // Lock tables
    $mysql->query("lock tables ".prefix."_eshop_view write, ".prefix."_eshop write");

    // Read data and update counters
    foreach ($mysql->select("select * from ".prefix."_eshop_view") as $vrec) {
        $mysql->query("update ".prefix."_eshop set views = views + ".intval($vrec['cnt'])." where id = ".intval($vrec['id']));
    }

    // Truncate view table
    //$mysql->query("truncate table ".prefix."_eshop_view");
    // DUE TO BUG IN MYSQL - USE DELETE + OPTIMIZE
    $mysql->query("delete from ".prefix."_eshop_view");
    $mysql->query("optimize table ".prefix."_eshop_view");

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
    public $VOWEL = '/аеиоуыэю€/';
    public $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[а€])(в|вши|вшись)))$/';
    public $REFLEXIVE = '/(с[€ь])$/';
    public $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|а€|€€|ою|ею)$/';
    public $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[а€])(ем|нн|вш|ющ|щ)))$/';
    public $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|€т|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[а€])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    public $NOUN = '/(а|ев|ов|ие|ье|е|и€ми|€ми|ами|еи|ии|и|ией|ей|ой|ий|й|и€м|€м|ием|ем|ам|ом|о|у|ах|и€х|€х|ы|ь|ию|ью|ю|и€|ь€|€)$/';
    public $RVRE = '/^(.*?[аеиоуыэю€])(.*)$/';
    public $DERIVATIONAL = '/[^аеиоуыэю€][аеиоуыэю€]+[^аеиоуыэю€]+[аеиоуыэю€].*(?<=о)сть?$/';

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
        $word = strtr($word, 'Є', 'е');

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

            $this->s($RV, '/и$/', '');


            if($this->m($RV, $this->DERIVATIONAL))
            {
                $this->s($RV, '/ость?$/', '');
            }

            if(!$this->s($RV, '/ь$/', ''))
            {
                $this->s($RV, '/ейше?/', '');
                $this->s($RV, '/нн$/', 'н');
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
