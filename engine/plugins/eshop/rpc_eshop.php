<?php
if(!defined('NGCMS'))
{
    exit('HAL');
}

rpcRegisterFunction('eshop_linked_products', 'linked_prd');
rpcRegisterFunction('eshop_compare', 'compare_prd');
rpcRegisterFunction('eshop_viewed', 'viewed_prd');

function linked_prd($params){
    global $userROW, $mysql;

    //$req = iconv( "windows-1251", "utf-8", urldecode($_REQUEST["q"]) );
    $req = urldecode(filter_var( $_REQUEST["q"], FILTER_SANITIZE_STRING ));
    $mode = filter_var( $_REQUEST["mode"], FILTER_SANITIZE_STRING );
    $id = filter_var( $_REQUEST["id"], FILTER_SANITIZE_STRING );

    $conditions = array();
    if ($req) {
        array_push($conditions, "p.name LIKE '%".$req."%' ");
    }
    
    if ($mode == "edit") {
        array_push($conditions, "p.id != '".$id."' ");
    }

    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category, i.filepath AS image_filepath ".$sqlQPart;

    foreach ($mysql->select(iconv( "utf-8", "windows-1251", $sqlQ )) as $row)
    {

        $tEntry[] = array (
            'id'                   => $row['id'],
            'name'                 => iconv("windows-1251", "utf-8", $row['name'] ),
            'image_filepath'       => $row['image_filepath'],
            'code'                 => iconv("windows-1251", "utf-8", $row['code'] ),
            'category'             => iconv("windows-1251", "utf-8", $row['category'] ),
        );
    }

    return array('status' => 1, 'errorCode' => 0, 'data' => $tEntry);
}

function compare_prd($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    
    $id = intval($params['id']);

    if ($id < 1) {
        return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ID should be positive');
    }

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'add':

            // Check if now we're logged in and earlier we started filling ebasket before logging in
            if (is_array($userROW)) {
                $mysql->query("update ".prefix."_eshop_compare set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
            }

            $mysql->query("insert into ".prefix."_eshop_compare (user_id, cookie, linked_fld) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($id).")");

            // ======== Prepare update of totals informer ========
            $filter = array();
            if (is_array($userROW)) {												$filter []= '(user_id = '.db_squote($userROW['id']).')';		}
            if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {	$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';	}

            $tCount = 0;
            
            if (count($filter) && is_array($res = $mysql->record("select count(*) as count from ".prefix."_eshop_compare where ".join(" or ", $filter), 1))) {
                $tCount = $res['count'];
            }

            $tVars = array(
                'count' => $tCount,
            );

            $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');

            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
        case 'remove':
        
            // Check if now we're logged in and earlier we started filling ebasket before logging in
            if (is_array($userROW)) {
                $mysql->query("update ".prefix."_eshop_compare set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
            }

            $mysql->query("delete from ".prefix."_eshop_compare where cookie = ".db_squote($_COOKIE['ngTrackID'])." and linked_fld = ".db_squote($id)." ");

            // ======== Prepare update of totals informer ========
            $filter = array();
            if (is_array($userROW)) {												$filter []= '(user_id = '.db_squote($userROW['id']).')';		}
            if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {	$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';	}

            $tCount = 0;
            
            if (count($filter) && is_array($res = $mysql->record("select count(*) as count from ".prefix."_eshop_compare where ".join(" or ", $filter), 1))) {
                $tCount = $res['count'];
            }

            $tVars = array(
                'count' => $tCount,
            );

            $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');

            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item removed from compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
    }
    
    return array('status' => 1, 'errorCode' => 0, 'data'	 => 'OK, '.var_export($params, true));
}

function viewed_prd($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    $page_stack = filter_var( $params['page_stack'], FILTER_SANITIZE_STRING );

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'show':

            if(!empty($page_stack)) {

                $conditions = array();
                array_push($conditions, "p.active = 1 ");
                array_push($conditions, "p.id IN (".$page_stack.") ");

                $fSort = " GROUP BY p.id ORDER BY p.id DESC";
                $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
                $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.annotation AS annotation, p.body AS body, p.active AS active, p.featured AS featured, p.position AS position, p.meta_title AS meta_title, p.meta_keywords AS meta_keywords, p.meta_description AS meta_description, p.date AS date, p.editdate AS editdate, p.views AS views, c.id AS cid, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
                
                foreach ($mysql->select($sqlQ) as $row)
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
                        
                        'compare' => $cmp_flag,
                        'features' => $features_array,
                        
                        'home' => home,
                        'image_filepath'    =>  $row['image_filepath'],
                        'tpl_url' => home.'/templates/'.$config['theme'],
                    );

                }
                
            }
            
            $tVars = array(
                'info' =>	isset($info)?$info:'',
                'entries' => isset($entries)?$entries:'',
                'tpl_url' => home.'/templates/'.$config['theme'],
                'tpl_home' => admin_url,
            );
            
            $tpath = locatePluginTemplates(array('viewed_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
            $xt = $twig->loadTemplate($tpath['viewed_block_eshop'].'viewed_block_eshop.tpl');
    
            return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into compare', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));

            break;
    }
    
    return array('status' => 1, 'errorCode' => 0, 'data'	 => 'OK, '.var_export($params, true));
}
