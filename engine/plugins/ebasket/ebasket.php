<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_htmlvar('js', admin_url.'/plugins/ebasket/js/ebasket.js');

//
// Отображение общей информации/остатков в корзине
function plugin_ebasket_total() {
	global $mysql, $twig, $userROW, $template;

	// Определяем условия выборки
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// Считаем итоги
	$tCount = 0;
	$tPrice = 0;

	if (count($filter) && is_array($res = $mysql->record("select count(*) as count, sum(price*count) as price from ".prefix."_eshop_ebasket where ".join(" or ", $filter), 1))) {
		$tCount = $res['count'];
		$tPrice = $res['price'];
	}

	// Готовим переменные
	$tVars = array(
		'count' => $tCount,
		'price' => $tPrice,
	);

	// Выводим шаблон
	$tpath = locatePluginTemplates(array('total'), 'ebasket', pluginGetVariable('ebasket', 'localsource'));
    
    //var_dump($tpath['total'].'total.tpl');

	$xt = $twig->loadTemplate($tpath['total'].'total.tpl');
	$template['vars']['plugin_ebasket'] = $xt->render($tVars);
}

//
// Отображение блока нотификации при добавлении продукта в корзину
function plugin_ebasket_notify() {
	global $mysql, $twig, $userROW, $template;

	// Выводим шаблон
	$tpath = locatePluginTemplates(array('notify'), 'ebasket', pluginGetVariable('ebasket', 'localsource'));
    
    //var_dump($tpath['total'].'total.tpl');
    
    $tVars = array();

	$xt = $twig->loadTemplate($tpath['notify'].'notify.tpl');
	$template['vars']['plugin_ebasket_notify'] = $xt->render($tVars);
}

//
// Показать содержимое корзины
function plugin_ebasket_list(){
	global $mysql, $twig, $userROW, $template, $ip;

	// Определяем условия выборки
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// Выполняем выборку
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
            $error_text[] = 'Имя не задано';
        }

        $SQL['email'] = filter_var( $_REQUEST['userInfo']['email'], FILTER_SANITIZE_STRING );
        if(empty($SQL['email']))
        {
            $error_text[] = 'Email не задан';
        }

        $SQL['phone'] = filter_var( $_REQUEST['userInfo']['phone'], FILTER_SANITIZE_STRING );
        if(empty($SQL['phone']))
        {
            $error_text[] = 'Телефон не задан';
        }
        
        $SQL['address'] = filter_var( $_REQUEST['userInfo']['deliverTo'], FILTER_SANITIZE_STRING );
        if(empty($SQL['address']))
        {
            $error_text[] = 'Адрес доставки не задан';
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
                
                // mail notify
                
                // Определяем условия выборки
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
                
                $notify_text[] = 'Заказ добавлен.';

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

	$tVars = array(
        'formEntry'	=> $tFormEntry,
		'recs'		=> count($recs),
		'entries'	=> $recs,
		'total'		=> sprintf('%9.2f', $total),
		'form_url'  => generatePluginLink('feedback', null, array(), array('id' => intval(pluginGetVariable('ebasket', 'feedback_form')))),
	);

	$tpath = locatePluginTemplates(array('list'), 'ebasket', pluginGetVariable('ebasket', 'localsource'));

	$xt = $twig->loadTemplate($tpath['list'].'list.tpl');

	$template['vars']['mainblock'] = $xt->render($tVars);
}


// Update ebasket content/counters
function plugin_ebasket_update() {
	global $mysql, $twig, $userROW, $template, $SUPRESS_TEMPLATE_SHOW;

	// Определяем условия выборки
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// Scan POST params
	if (count($filter)) {
		foreach ($_POST as $k => $v) {
			if (preg_match('#^count_(\d+)$#', $k, $m)) {
				if (intval($v) < 1) {
					$mysql->query("delete from ".prefix."_eshop_ebasket where (id = ".db_squote($m[1]).") and (".join(" or ", $filter).")");
				} else {
					$mysql->query("update ".prefix."_eshop_ebasket set count = ".db_squote(intval($v))."where (id = ".db_squote($m[1]).") and (".join(" or ", $filter).")");
				}
			}
		}
	}

	// Redirect to ebasket page
	$SUPRESS_TEMPLATE_SHOW = true;
	@header("Location: ".generatePluginLink('ebasket', null, array(), array(), false, true));
}

//
// Вызов обработчика
register_plugin_page('ebasket','','plugin_ebasket_list',0);
register_plugin_page('ebasket','update','plugin_ebasket_update',0);
add_act('index', 'plugin_ebasket_total');
add_act('index', 'plugin_ebasket_notify');
