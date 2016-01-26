<?php

if (!defined('NGCMS'))
    exit('HAL');

function payment_config($id)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;

    $row = $mysql->record('SELECT * FROM '.prefix.'_eshop_payment WHERE name = '.db_squote($id).' LIMIT 1');
    
    if (isset($_REQUEST['submit']))
    {
        
        $PARAMS['name'] = $id;
        $PARAMS['merchant_id'] = $_REQUEST['merchant_id'];
        $PARAMS['secret_key'] = $_REQUEST['secret_key'];
        $PARAMS['hidden_key'] = $_REQUEST['hidden_key'];
        $PARAMS['test_mode'] = $_REQUEST['test_mode'];
        
        $SQL['name'] = $id;
        $SQL['options'] = json_encode($PARAMS);

        if(empty($error_text))
        {
            $vnames = array();
            foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
            $mysql->query("REPLACE INTO ".prefix."_eshop_payment SET ".implode(', ',$vnames)." ");

            redirect_eshop('?mod=extra-config&plugin=eshop&action=list_payment');
        }

    }
    
    /*
    foreach ($row as $k => $v) { 
        $tEntry[$k] = $v;
    }
    */
    
    $tEntry = array();
    $tEntry['name'] = $row['name'];
    $tEntry['options'] = json_decode($row['options'], true);

    $payment_config_tpl = dirname(__FILE__).'/tpl/config.tpl';
    $xt = $twig->loadTemplate($payment_config_tpl);
    
    $tVars = array( 
        'entries' => isset($tEntry)?$tEntry:'' 
    );
    
    $tpath = locatePluginTemplates(array('config/main'), 'eshop', 1);
    $xg = $twig->loadTemplate($tpath['config/main'].'config/'.'main.tpl');

    $tVars = array(
        'entries'       =>  $xt->render($tVars),
        'php_self'      =>  $PHP_SELF,
        'plugin_url'    =>  admin_url.'/admin.php?mod=extra-config&plugin=eshop',
        'skins_url'     =>  skins_url,
        'admin_url'     =>  admin_url,
        'home'          =>  home,
        'current_title' => '������� ������ ['.$id.']',
    );
    
    print $xg->render($tVars);
}