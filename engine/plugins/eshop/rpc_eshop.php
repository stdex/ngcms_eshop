<?php
if(!defined('NGCMS'))
{
    exit('HAL');
}

rpcRegisterFunction('eshop_linked_products', 'linked_prd');
rpcRegisterFunction('eshop_compare', 'compare_prd');
rpcRegisterFunction('eshop_viewed', 'viewed_prd');

rpcRegisterFunction('eshop_likes_result', 'likes_result');

rpcRegisterFunction('eshop_comments_add', 'comments_add');
rpcRegisterFunction('eshop_comments_show', 'comments_show');

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

function likes_result($params){
    global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $twigLoader;

    $results = array();
    $params = arrayCharsetConvert(1, $params);
    $id = filter_var( $params['id'], FILTER_SANITIZE_STRING );

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO compare ****
        case 'do_like':

            if(!empty($id)) {
                
                // Check if now we're logged in and earlier we started filling ebasket before logging in
                if (is_array($userROW)) {
                    $mysql->query("update ".prefix."_eshop_products_likes set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
                }
                
                $filter = array();
                if (is_array($userROW)) {												$filter []= '(user_id = '.db_squote($userROW['id']).')';		}
                if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {	$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';	}

                $tCount = 0;
                
                if (count($filter) && is_array($check_like = $mysql->record("select count(*) as count from ".prefix."_eshop_products_likes where (".join(" or ", $filter).") AND product_id = ".db_squote($id)." ", 1))) {
                    $tCount = $check_like['count'];
                }
                
                if($tCount == 0) {
            
                    $mysql->query("insert into ".prefix."_eshop_products_likes (user_id, cookie, product_id) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($id).")");
                    
                    $mysql->query("update ".prefix."_eshop_products set likes = likes + 1 where id = ".db_squote($id)." ");

                }
                else {
                    
                    $mysql->query("delete from ".prefix."_eshop_products_likes where (".join(" or ", $filter).") AND product_id = ".db_squote($id)." ");
                    
                    $mysql->query("update ".prefix."_eshop_products set likes = likes - 1 where id = ".db_squote($id)." ");
                }

                $likes = $mysql->record("SELECT COUNT(*) as count FROM ".prefix."_eshop_products_likes l WHERE l.product_id='".$id."'");
                
                $likes_tVars = array (
                    'count' => $likes['count'],
                    'id' => $id,
                );
                        
                //var_dump($likes_tVars);

                $tpath = locatePluginTemplates(array('likes_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
                $xt = $twig->loadTemplate($tpath['likes_eshop'].'likes_eshop.tpl');

                return array('status' => 1, 'errorCode' => 0, 'data' => 'Likes updated', 'update' => arrayCharsetConvert(0, $xt->render($likes_tVars)));
                
                
            }



            break;
        }
    
    return array('status' => 1, 'errorCode' => 0, 'data'	 => 'OK, '.var_export($params, true));
}

function comments_add($params) {
    global $tpl, $template, $twig, $ip, $SYSTEM_FLAGS, $config, $userROW, $mysql, $TemplateCache;

    // Prepare basic reply array
    $results = array();

    if (isset($userROW)) {
        $SQL['name']			= $userROW['name'];
        $SQL['author']			= $userROW['name'];
        $SQL['author_id']		= $userROW['id'];
        $SQL['mail']			= $userROW['mail'];
        $is_member				= 1;
        $memberRec				= $userROW;
    }
    else {
        $SQL['name']			= secure_html(convert(trim($params['comment_author'])));
        $SQL['author']			= secure_html(convert(trim($params['comment_author'])));
        $SQL['author_id']		= 0;
        $SQL['mail']			= secure_html(convert(trim($params['comment_email'])));
        $is_member				= 0;
        $memberRec				= "";
    }
    $SQL['text']	=	secure_html(convert(trim($params['comment_text'])));
    $SQL['product_id'] =  $params['product_id'];
    $SQL['postdate'] = time() + ($config['date_adjust'] * 60);

    $SQL['text']	= str_replace("\r\n", "<br />", $SQL['text']);
    $SQL['ip']		= $ip;
    $SQL['reg']		= ($is_member) ? '1' : '0';
    $SQL['status']		= '1';

    if(empty($SQL['name']))
    {
        $error_text[] = 'Вы не ввели имя!';
    }

    if(empty($SQL['mail']))
    {
        $error_text[] = 'Вы не ввели email!';
    }

    if(empty($SQL['text']))
    {
        $error_text[] = 'Вы не написали комментарий!';
    }

    if( empty($error_text) )
    {

        // Create comment
        $vnames = array(); $vparams = array();
        foreach ($SQL as $k => $v) { $vnames[]  = $k; $vparams[] = db_squote($v); }

        $mysql->query("insert into ".prefix."_eshop_products_comments (".implode(",",$vnames).") values (".implode(",",$vparams).")");

        // Update comment counter
        $mysql->query("update ".prefix."_eshop_products set comments = comments + 1 where id = ".db_squote($SQL['product_id'])." ");


        $results = array(
            'eshop_comments'	=> 100,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', 'Комментарий успешно добавлен!'),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
        );
        
    }
    else {

        $results = array(
            'eshop_comments' => 2,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', implode('<br />', $error_text)),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
        );

    }

    return array('status' => 1, 'errorCode' => 0, 'data' => $results);
}

function comments_show($params){
    global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS, $TemplateCache, $twig, $mysql;

    // Prepare basic reply array
    $results = array();

    $results = array(
            'eshop_comments'	=> 100,
            'eshop_comments_text' => iconv('Windows-1251', 'UTF-8', 'Комментарии'),
            'eshop_comments_show' => iconv('Windows-1251', 'UTF-8', comments_show_handler($params))
    );
    
    return array('status' => 1, 'errorCode' => 0, 'data' => $results);

}

function comments_show_handler($params){
    global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS, $TemplateCache, $twig, $mysql;

    // Preload template configuration variables
    templateLoadVariables();

    // Use default <noavatar> file
    // - Check if noavatar is defined on template level
    $tplVars = $TemplateCache['site']['#variables'];
    $noAvatarURL = (isset($tplVars['configuration']) && is_array($tplVars['configuration']) && isset($tplVars['configuration']['noAvatarImage']) && $tplVars['configuration']['noAvatarImage'])?(tpl_url."/".$tplVars['configuration']['noAvatarImage']):(avatars_url."/noavatar.gif");

    $tpath = locatePluginTemplates(array('comments.show_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath['comments.show_eshop'].'comments.show_eshop.tpl');

    $conditions = array();
    if ($params['product_id']) {
        array_push($conditions, "c.product_id = ".db_squote($params['product_id']));
    }

    $fSort = "ORDER BY c.postdate ASC";

    $sqlQPart = "from ".prefix."_eshop_products_comments c LEFT JOIN ".prefix."_users u ON c.author_id = u.id ".(count($conditions)?"where ".implode(" AND ", $conditions):'').' '.$fSort;
    $sqlQ = "select *, c.id as cid, u.id as uid, u.name as uname, c.name as name ".$sqlQPart;

    $output = '';
    foreach ($mysql->select($sqlQ) as $row) {
        // Add [hide] tag processing
        $text	= $row['text'];

        if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
        if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
        if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }

            if ($config['use_avatars']) {
            if ($row['avatar']) {
                $avatar = avatars_url."/".$row['avatar'];
            } else {
                    $avatar = $noAvatarURL;
            }
        } else {
            $avatar = '';
        }
        
        
        $Entries[] = array (
                'id' => $row['cid'],
                'mail' =>	$row['mail'],
                'author' => $row['name'],
                'date' => $row['postdate'],
                'profile_link' => checkLinkAvailable('uprofile', 'show')?
                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id'])),
                'avatar' => $avatar,
                'name' => $row['uname'],
                'commenttext' => $text
                );
                
        //$output .= $xt->render($tVars);
    }

    $tVars = array(
        'entries' => isset($Entries)?$Entries:'',
        'tpl_url' => home.'/templates/'.$config['theme'],
        'tpl_home' => admin_url
    );
        
    $output .=  $xt->render($tVars);

    return $output;
}
