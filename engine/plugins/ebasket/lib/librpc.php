<?php

//
// Shipping cart RPC manipulations
//

function ebasket_add_item($linked_ds, $linked_id, $title, $price, $count, $xfld = array()) {
    global $mysql, $userROW, $twig, $template;

    // Check if now we're logged in and earlier we started filling ebasket before logging in
    if (is_array($userROW)) {
        $mysql->query("update ".prefix."_eshop_ebasket set user_id = ".db_squote($userROW['id'])." where (user_id = 0) and (cookie = ".db_squote($_COOKIE['ngTrackID']).")");
    }

    $mysql->query("insert into ".prefix."_eshop_ebasket (user_id, cookie, linked_ds, linked_id, title, linked_fld, price, count) values (".(is_array($userROW)?db_squote($userROW['id']):0).", ".db_squote($_COOKIE['ngTrackID']).", ".db_squote($linked_ds).", ".db_squote($linked_id).", ".db_squote($title).", ".db_squote(serialize($xfld)).", ".db_squote($price).", ".db_squote($count).") on duplicate key update price=".db_squote($price).", count = count+".db_squote($count));

    // ======== Prepare update of totals informer ========
    $filter = array();
    if (is_array($userROW)) {												$filter []= '(user_id = '.db_squote($userROW['id']).')';		}
    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {	$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';	}

    $tCount = 0;
    $tPrice = 0;

    if (count($filter) && is_array($res = $mysql->record("select count(*) as count, sum(price*count) as price from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1))) {
        $tCount = $res['count'];
        $tPrice = $res['price'];
    }

    $tVars = array(
        'count' 		=> $tCount,
        'price' 		=> $tPrice,
        'ajaxUpdate'	=> 1,
    );

    $tpath = locatePluginTemplates(array('total'), 'ebasket', pluginGetVariable('ebasket', 'localsource'));

    $xt = $twig->loadTemplate($tpath['total'].'total.tpl');
    //$xt = $twig->loadTemplate('plugins/ebasket/total.tpl');
    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into ebasket', 'update' => arrayCharsetConvert(0, $xt->render($tVars)));
}


function ebasket_add_fast_order($linked_ds, $linked_id, $title, $price, $count, $type, $order, $xfld = array()) {
    global $mysql, $userROW, $twig, $template, $ip;

    $SQL['name'] = $order['name'];
    $SQL['email'] = $order['email'];
    $SQL['phone'] = $order['phone'];
    $SQL['address'] = $order['address'];
    $SQL['comment'] = "";

    $SQL['dt'] = time() + ($config['date_adjust'] * 60);
    $SQL['ip'] =  $ip;
    
    $SQL['type'] =  $type;
    
    $SQL['paid'] = 0;
    $SQL['total_price'] = round($price * $count, 2);
    
    $vnames = array();
    foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
    $mysql->query('INSERT INTO '.prefix.'_eshop_orders SET '.implode(', ',$vnames).' ');
    $qid = $mysql->lastid('eshop_orders');
    
    $mysql->query("INSERT INTO ".prefix."_eshop_order_basket (`order_id`, `linked_id`, `title`, `count`, `price`, `linked_fld`) VALUES (".db_squote($qid).", ".db_squote($linked_id).", ".db_squote($title).", ".db_squote($count).", ".db_squote($price).", ".db_squote(serialize($xfld))." )");
    
    // mail notify

    $filter = array();
    if ($qid) {
        $filter []= '(order_id = '.db_squote($qid).')';
    }

    foreach ($mysql->select("select * from ".prefix."_eshop_order_basket where ".join(" or ", $filter), 1) as $rec) {
                $total += round($rec['price'] * $rec['count'], 2);

                $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
                $rec['xfields'] = unserialize($rec['linked_fld']);
                unset($rec['linked_fld']);
                $basket []= $rec;
    }
    
    $notify_tpath = locatePluginTemplates(array('lfeedback'), 'ebasket', pluginGetVariable('ebasket', 'localsource'));
    $notify_xt = $twig->loadTemplate($notify_tpath['lfeedback'].'lfeedback.tpl');

    $pVars = array(
        'recs'		=> count($recs),
        'entries'	=> $recs,
        'total'		=> sprintf('%9.2f', $total),
        'vnames'   => $vnames,
    );

    $mailBody = $notify_xt->render($pVars);
    $mailSubject = "Новый заказ с сайта";
    $mailTo = pluginGetVariable('eshop', 'email_notify_orders');

    sendEmailMessage($mailTo, $mailSubject, $mailBody, $filename = false, $mail_from = false, $ctype = 'text/html');

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item added into ebasket', 'update' => '');
}

function basket_update_item_count($id, $linked_ds, $linked_id, $count) {
    global $mysql, $userROW, $twig;

    $mysql->query("update ".prefix."_eshop_ebasket set count = ".db_squote($count)." where (linked_id = ".db_squote($linked_id).") and (linked_ds = ".db_squote($linked_ds).") and (id = ".db_squote($id).")");

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item count updated');
}

function basket_delete_item($id, $linked_ds, $linked_id) {
    global $mysql, $userROW, $twig;

    $mysql->query("delete from ".prefix."_eshop_ebasket where id = ".db_squote($id)." and linked_id = ".db_squote($linked_id)." and linked_ds = ".db_squote($linked_ds)." limit 1");

    return array('status' => 1, 'errorCode' => 0, 'data' => 'Item deleted');
}


function ebasket_rpc_manage($params){
    global $userROW, $DSlist, $mysql, $twig;

    LoadPluginLibrary('xfields', 'common');

    if (!is_array($params) || !isset($params['action']))
        return array('status' => 0, 'errorCode' => 1, 'errorText' => 'Activity mode is not set');

    $params = arrayCharsetConvert(1, $params);

    switch ($params['action']) {
        // **** ADD NEW ITEM INTO ebasket ****
        case 'add':
            $linked_ds = intval($params['ds']);
            $linked_id = intval($params['id']);
            $count     = intval($params['count']);

            // Check available DataSources
            if (!(in_array($linked_ds, array($DSlist['news'])))) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ebasket can be used only for NEWS');
            }

            // Check available DataSources
            if ($count < 1) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'Count should be positive');
            }

            // Check if linked item is available
            switch ($linked_ds) {
                case $DSlist['news']:
                
                    $conditions = array();
                    if ($linked_id) {
                        array_push($conditions, "p.id = ".db_squote($linked_id));
                    }
                
                    $fSort = " GROUP BY p.id ORDER BY p.id DESC";
                    $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
                    $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;
                
                    // Retrieve news record
                    $rec = $mysql->record($sqlQ);
                    if (!is_array($rec)) {
                        return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
                    }

                    $btitle = $rec['name'];
                    $price = $rec['price'];
                    
                    $view_link = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('id' => $rec['id'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $rec['id']));
            
                    $rec['view_link'] = $view_link;

                    // Add data into basked
                    return ebasket_add_item($linked_ds, $linked_id, $btitle, $price, $count, array('item' => $rec));

                    break;
                    
            }
            break;
        case 'update_count':
            $id = intval($params['id']);
            $linked_ds = intval($params['linked_ds']);
            $linked_id = intval($params['linked_id']);
            $count     = intval($params['count']);
            return basket_update_item_count($id, $linked_ds, $linked_id, $count);
            break;
        case 'delete':
            $id = intval($params['id']);
            $linked_ds = intval($params['linked_ds']);
            $linked_id = intval($params['linked_id']);
            return basket_delete_item($id, $linked_ds, $linked_id);
            break;
        case 'add_fast':
            $linked_ds = intval($params['ds']);
            $linked_id = intval($params['id']);
            $count     = intval($params['count']);
            $type     = intval($params['type']);
            
            $order['name'] = filter_var( $params['name'], FILTER_SANITIZE_STRING );
            if(empty($order['name']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }

            $order['email'] = "";

            $order['phone'] = filter_var( $params['phone'], FILTER_SANITIZE_STRING );
            if(empty($order['phone']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }
            
            $order['address'] = filter_var( $params['address'], FILTER_SANITIZE_STRING );
            if(empty($order['address']))
            {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }
            
            // Check available DataSources
            if (!(in_array($linked_ds, array($DSlist['news'])))) {
                return array('status' => 0, 'errorCode' => 2, 'errorText' => 'ebasket can be used only for NEWS');
            }

            // Check available DataSources
            if ($count < 1) {
                $count = 1;
            }
            
            $conditions = array();
            if ($linked_id) {
                array_push($conditions, "p.id = ".db_squote($linked_id));
            }

            $fSort = " GROUP BY p.id ORDER BY p.id DESC";
            $sqlQPart = "FROM ".prefix."_eshop_products p LEFT JOIN ".prefix."_eshop_products_categories pc ON p.id = pc.product_id LEFT JOIN ".prefix."_eshop_categories c ON pc.category_id = c.id LEFT JOIN ".prefix."_eshop_images i ON i.product_id = p.id LEFT JOIN ".prefix."_eshop_variants v ON p.id = v.product_id ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
            $sqlQ = "SELECT p.id AS id, p.code AS code, p.name AS name, p.active AS active, p.featured AS featured, p.position AS position, c.name AS category, i.filepath AS image_filepath, v.price AS price, v.compare_price AS compare_price, v.stock AS stock ".$sqlQPart;

            // Retrieve news record
            $rec = $mysql->record($sqlQ);
            if (!is_array($rec)) {
                return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Item [news] with ID ('.$linked_id.') is not found');
            }

            $btitle = $rec['name'];
            $price = $rec['price'];

            $view_link = checkLinkAvailable('eshop', 'show')?
            generateLink('eshop', 'show', array('id' => $rec['id'])):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('id' => $rec['id']));

            $rec['view_link'] = $view_link;

            // Add data into basked
            return ebasket_add_fast_order($linked_ds, $linked_id, $btitle, $price, $count, $type, $order, array('item' => $rec));

            break;
    }
    return array('status' => 1, 'errorCode' => 0, 'data'	 => 'OK, '.var_export($params, true));

}


function ebasket_rpc_demo($params) {
    return array('status' => 1, 'errorCode' => 0, 'data' => var_export($params, true));
}

//rpcRegisterFunction('plugin.cart.demo', 'cart_rpc_demo');
rpcRegisterFunction('plugin.ebasket.manage', 'ebasket_rpc_manage');

