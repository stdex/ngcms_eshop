<?php
if (!defined('NGCMS'))
{
    die ('HAL');
}

function plugin_eshop_install($action) {
    global $lang, $mysql;

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/thumb/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/thumb'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/temp'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/products/temp/thumb/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/products/temp/thumb'), 1);

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/categories'), 1);
            
    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/eshop/categories/thumb/', 0777))
            msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/eshop/categories/thumb'), 1);

    if ($action != 'autoapply')
        loadPluginLang('eshop', 'config', '', '', ':');
    $db_update = array(
    array(
        'table'		=> 'eshop_products',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `url` (`url`), KEY `name` (`name`), KEY `brand_id` (`brand_id`), KEY `position` (`position`), KEY `featured` (`featured`), KEY `active` (`active`), KEY `zboard_view` (`views`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'code', 'type' => 'varchar(255)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'url', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'brand_id', 'type' => 'INT(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'annotation', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'body', 'type' => 'longtext', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'1\''),
            array('action'	=> 'cmodify', 'name' => 'featured', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            
            array('action'	=> 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'date', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'editdate', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
            
            array('action'	=> 'cmodify', 'name' => 'views', 'type' => 'INT(11)', 'params' => 'NOT NULL DEFAULT \'0\''),
        )
    ),

    array(
        'table'  => 'eshop_products_comments',
        'action' => 'cmodify',
        'key'	   => 'primary key(id), KEY `product_id` (`product_id`)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(11)', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'postdate', 'type' => 'INT(11)', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'product_id', 'type' => 'int', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(255)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'author', 'type' => 'char(100)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => "default '0'"),
            array('action' => 'cmodify', 'name' => 'mail', 'type' => 'char(100)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'text', 'type' => 'text'),
            array('action' => 'cmodify', 'name' => 'answer', 'type' => 'text'),
            array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)', 'params' => "default ''"),
            array('action' => 'cmodify', 'name' => 'reg', 'type' => 'tinyint(1)', 'params'=> "default '0'"),
        )
    ),
        
    array(
        'table'		=> 'eshop_products_view',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL'),
            array('action'	=> 'cmodify', 'name' => 'cnt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'		=> 'eshop_features',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `position` (`position`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'in_filter', 'type' => 'int(1)', 'params' => 'NOT NULL default \'1\''),
        )
    ),
        
    array(
        'table'		=> 'eshop_options',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(`product_id`, `feature_id`), KEY `product_id` (`product_id`), KEY `feature_id` (`feature_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'feature_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'value', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
        )
    ),

    array(
        'table'		=> 'eshop_related_products',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(`product_id`, `related_id`), KEY `product_id` (`product_id`), KEY `related_id` (`related_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'related_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
        
    array(
        'table'		=> 'eshop_categories',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `url` (`url`), KEY `name` (`name`), KEY `parent_id` (`parent_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'url', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'image', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'parent_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'1\''),
        )
    ),

    array(
        'table'		=> 'eshop_products_categories',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(`category_id`, `product_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'category_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'		=> 'eshop_categories_features',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(`category_id`, `feature_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'category_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'feature_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    ),
        
    array(
        'table'		=> 'eshop_brands',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `url` (`url`), KEY `name` (`name`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'url', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'image', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),

            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'meta_title', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_keywords', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'meta_description', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),

        )
    ),
        
    array(
        'table'		=> 'eshop_purchases',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `order_id` (`order_id`), KEY `product_id` (`product_id`), KEY `variant_id` (`variant_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'dt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'order_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'variant_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),

            array('action'	=> 'cmodify', 'name' => 'product_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'variant_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),

            array('action'	=> 'cmodify', 'name' => 'amount', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'sku', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            /*
            array('action'	=> 'cmodify', 'name' => 'merchant_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'order_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'amount', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'currency', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'paymode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'trans_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'status', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'error_msg', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'test_mode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            */
        )
    ),

    array(
        'table'		=> 'eshop_orders',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'dt', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'paid', 'type' => 'tinyint(1)', 'params' => 'NOT NULL default \'0\''),

            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'address', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'phone', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'email', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'comment', 'type' => 'varchar(500)', 'params' => 'NOT NULL default \'\''),

            array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)', 'params' => "default ''"),
            array('action'	=> 'cmodify', 'name' => 'total_price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
        )
    ),
        
    array(
        'table'		=> 'eshop_variants',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `product_id` (`product_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),

            array('action'	=> 'cmodify', 'name' => 'sku', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            
            array('action'	=> 'cmodify', 'name' => 'price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
            array('action'	=> 'cmodify', 'name' => 'compare_price', 'type' => 'decimal(14,2)', 'params' => 'NOT NULL default \'0.00\''),
            
            array('action'	=> 'cmodify', 'name' => 'stock', 'type' => 'mediumint(9)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            
            array('action'	=> 'cmodify', 'name' => 'amount', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'attachment', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
        )
    ),
        
    array(
        'table'		=> 'eshop_images',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `product_id` (`product_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'filepath', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'product_id', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(11)', 'params' => 'NOT NULL default \'0\''),
        )
    )
);

    switch ($action)
    {
        case 'confirm':
            generate_install_page('eshop', file_get_contents(''));break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('eshop', $db_update, 'install', ($action=='autoapply')?true:false)) {
                plugin_mark_installed('eshop');
            } else {
                return false;
            }

            $params = array(
                'count' => '10',
                'count_search' => '20',
                'views_count' => '1',
                
                'max_image_size' => '5',
                'width' => '2000',
                'height' => '2000',
                'width_thumb' => '350',
                'ext_image' => '*.jpg;*.jpeg;*.gif;*.png',
                
                'catz_max_image_size' => '5',
                'catz_width' => '2000',
                'catz_height' => '2000',
                'catz_width_thumb' => '350',
                'catz_ext_image' => '*.jpg;*.jpeg;*.gif;*.png',
                
                'email_notify_orders' => '',
                'email_notify_comments' => '',
                'email_notify_back' => '',
                
                'description_delivery' => '',
                'description_order' => '',
                'description_phones' => '',

            );
            foreach ($params as $k => $v) {
                extra_set_param('eshop', $k, $v);
            }
            extra_commit_changes();
            break;
    }
    return true;
}
