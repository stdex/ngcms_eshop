<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

function create_urls()
{global $tpl, $mysql, $twig;

    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    
    $ULIB->registerCommand('eshop', '',
        array ('vars' =>
                array(  'alt' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Altname ���������')),
                        'cat' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID ���������')),
                        'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => '������������ ���������'))
                ),
                'descr' => array ('russian' => '������� ��������'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'show',
        array ('vars' =>
                array('alt' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Altname ��������')),
                      'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID ��������')),
                ),
                'descr' => array ('russian' => '������ �� �������'),
        )
    );
               
    $ULIB->registerCommand('eshop', 'search',
        array ('vars' =>
                array('page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => '������������ ���������'))
                ),
                'descr' => array ('russian' => '����� �� ���������'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'stocks',
        array ('vars' =>
                array('page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => '������������ ���������'))
                ),
                'descr' => array ('russian' => '�����'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'compare',
        array (
                'descr' => array ('russian' => '��������� ���������'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'currency',
        array (
                'descr' => array ('russian' => '������'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'yml_export',
        array (
                'descr' => array ('russian' => '������� XML'),
        )
    );
    
    $ULIB->registerCommand('eshop', 'ebasket_list',
        array (
                'descr' => array ('russian' => '�������'),
        )
    );

    $ULIB->registerCommand('eshop', 'order',
        array (
                'descr' => array ('russian' => '������'),
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
          'rcmd' => '/[{alt}/][page/{page}/]',
          'regex' => '#^/(.+?){0,1}(?:page/(\\d{1,4})/){0,1}$#',
          'regexMap' => 
          array (
            1 => 'alt',
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
              1 => '/',
              2 => 0,
            ),
            1 => 
            array (
              0 => 1,
              1 => 'alt',
              2 => 1,
            ),
            2 => 
            array (
              0 => 0,
              1 => '/',
              2 => 1,
            ),
            3 => 
            array (
              0 => 0,
              1 => 'page/',
              2 => 3,
            ),
            4 => 
            array (
              0 => 1,
              1 => 'page',
              2 => 3,
            ),
            5 => 
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
          'rcmd' => '/{alt}.html',
          'regex' => '#^/(.+?){0,1}.html$#',
          'regexMap' => 
          array (
            1 => 'alt',
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
              1 => '/',
              2 => 0,
            ),
            1 => 
            array (
              0 => 1,
              1 => 'alt',
              2 => 0,
            ),
            2 => 
            array (
              0 => 0,
              1 => '.html',
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
          'rcmd' => '/eshop/search/[page/{page}/]',
          'regex' => '#^/eshop/search/(?:page/(\\d{1,4})/){0,1}$#',
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
              1 => '/eshop/search/',
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
        'handlerName' => 'stocks',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/stocks/[page/{page}/]',
          'regex' => '#^/eshop/stocks/(?:page/(\\d{1,4})/){0,1}$#',
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
              1 => '/eshop/stocks/',
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
        'handlerName' => 'compare',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/compare/',
          'regex' => '#^/eshop/compare/$#',
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
              1 => '/eshop/compare/',
              2 => 0,
            ),
          ),
        ),
      )
    );
    
   $UHANDLER->registerHandler(0,
        array (
        'pluginName' => 'eshop',
        'handlerName' => 'currency',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/currency/',
          'regex' => '#^/eshop/currency/$#',
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
              1 => '/eshop/currency/',
              2 => 0,
            ),
          ),
        ),
      )
    );            

   $UHANDLER->registerHandler(0,
        array (
        'pluginName' => 'eshop',
        'handlerName' => 'yml_export',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/yml_export/',
          'regex' => '#^/eshop/yml_export/$#',
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
              1 => '/eshop/yml_export/',
              2 => 0,
            ),
          ),
        ),
      )
    );

   $UHANDLER->registerHandler(0,
        array (
        'pluginName' => 'eshop',
        'handlerName' => 'ebasket_list',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/ebasket_list/',
          'regex' => '#^/eshop/ebasket_list/$#',
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
              1 => '/eshop/ebasket_list/',
              2 => 0,
            ),
          ),
        ),
      )
    );

   $UHANDLER->registerHandler(0,
        array (
        'pluginName' => 'eshop',
        'handlerName' => 'order',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => 
        array (
          'rcmd' => '/eshop/order/',
          'regex' => '#^/eshop/order/$#',
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
              1 => '/eshop/order/',
              2 => 0,
            ),
          ),
        ),
      )
    );

    $UHANDLER->saveConfig();
}

function remove_urls()
{global $tpl, $mysql, $twig;

    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $ULIB->removeCommand('eshop', '');
    $ULIB->removeCommand('eshop', 'show');
    $ULIB->removeCommand('eshop', 'search');
    $ULIB->removeCommand('eshop', 'stocks');
    $ULIB->removeCommand('eshop', 'compare');
    $ULIB->removeCommand('eshop', 'currency');
    $ULIB->removeCommand('eshop', 'yml_export');
    $ULIB->removeCommand('eshop', 'ebasket_list');
    $ULIB->removeCommand('eshop', 'order');
    $ULIB->saveConfig();

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    $UHANDLER->removePluginHandlers('eshop', '');
    $UHANDLER->removePluginHandlers('eshop', 'show');
    $UHANDLER->removePluginHandlers('eshop', 'search');
    $UHANDLER->removePluginHandlers('eshop', 'stocks');
    $UHANDLER->removePluginHandlers('eshop', 'compare');
    $UHANDLER->removePluginHandlers('eshop', 'currency');
    $UHANDLER->removePluginHandlers('eshop', 'yml_export');
    $UHANDLER->removePluginHandlers('eshop', 'ebasket_list');
    $UHANDLER->removePluginHandlers('eshop', 'order');
    $UHANDLER->saveConfig();

}


function check_php_str($ext_image)
{
    $ext_image = secure_html(trim($ext_image));
    $extensions = array_map('trim', explode(',', $ext_image));
    $ext_image = array_filter($extensions, function($value){
    if (strstr($value, 'php') !== false)
    {
        return false;
    }
        return true;
    });
    
    return implode(', ', $ext_image);
    
}


function import_yml($yml_url)
{global $tpl, $mysql, $twig, $parse, $SYSTEM_FLAGS;

    include_once(dirname(__FILE__).'/import.class.php');

    $file = file_get_contents($_REQUEST['yml_url']);
    $xml = new SimpleXMLElement($file);
    unset($file);
    unset(
        $_SESSION['cats'],
        $_SESSION['cats_uf_ids'],
        $_SESSION['update'],
        $_SESSION['offers'],
        $_SESSION['IDS'],
        $_SESSION['j'],
        $_SESSION['page'],
        $_SESSION['work']
    );
    
    foreach($xml->shop->offers->offer as $key => $offer) {
        $_SESSION['work'][] = (int)$offer->attributes()->id;
    }

    $ctg = new YMLCategory();
    $ctg->GetFromSite();
    $ctg->GetFromXML($xml->shop->categories->category);
    
    $ofs = new YMLOffer();

    foreach($xml->shop->offers->offer as $key => $offer) {
        $oif = (int)$offer->attributes()->id;

        $name = iconv('utf-8','windows-1251',(string)$offer->name);
        
        if($name == "")
        {
            $name = iconv('utf-8','windows-1251', trim((string)$offer->model." ".(string)$offer->barcode));
        }
        
        if($name != "")
        {
            $url = strtolower($parse->translit($name,1, 1));
            $url = str_replace("/", "-", $url);
        }
        
        if ($url) {
            $prd_row = $mysql->record("select * from ".prefix."_eshop_products where url = ".db_squote($url)." limit 1");
            if ( !is_array($prd_row) ) {
                $oid = $ofs->Add($offer, $name, $url);
                $ofs->eco('�������� �����: '.$name.'<br>');
            }
            else {
                $oid = $ofs->Update($prd_row['id'], $offer);
                $ofs->eco('�������� �����: '.$name.'<br>');
            }
        } 
    }
    
    generate_catz_cache(true);
    
}
