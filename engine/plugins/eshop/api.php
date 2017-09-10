<?php

class ApiEshop
{
    private $type;
    private $control;

    public function __construct($type)
    {
        $this->type = $type;
        $this->control = $_GET;
    }

    public function run()
    {

        $allowMethods = [
            'get_orders',
            'get_order_products',
            'get_features',
            'get_variants',

            'update_order_statuses',
            'update_variants',
            'update_products',
            'update_features',
        ];

        $method = str_replace('-', '_', $this->type);
        if (method_exists($this, $method) && in_array($method, $allowMethods)) {
            $this->$method();
        } else {
            $results = ['data' => [], 'status' => 'ERROR'];
            $this->renderResults($results);
        }
    }

    public function get_orders()
    {
        $conditions = [];
        $this->addDateTimeCondition($conditions);
        $orders = $this->getOrders($conditions);

        $results = ['data' => $orders, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function get_order_products()
    {
        $conditions = [];
        $this->addDateTimeCondition($conditions);
        $orders = array_keys($this->getOrders($conditions));
        $positions = $this->getProducts($orders);

        $results = ['data' => $positions, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function get_features()
    {
        global $mysql;

        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow) {
            $options = $frow;
        }

        $results = ['data' => $options, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function get_variants()
    {

        $conditions = [];
        if (!empty($this->control['product_id'])) {
            $conditions[] = 'product_id = ' . (int)$this->control['product_id'];
        }

        $variants = $this->getVariants($conditions);

        $results = ['data' => $variants, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_order_statuses()
    {
        global $mysql;

        $params = $this->getParams();

        foreach ($params as $item) {
            $order_id = $item['order_id'];
            $status = $item['status'];

            if(!empty($order_id)) {
                $mysql->query(
                    "UPDATE ".prefix."_eshop_orders SET paid = ".(int)$status." WHERE id = ".(int)$order_id
                );
            }
        }

        $results = ['data' => [], 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_variants()
    {
        global $mysql;

        $params = $this->getParams();

        $mapParams = [
            'id' => 'id',
            'product_id' => 'product_id',
            'name' => 'name',
            'count' => 'amount',
            'price' => 'price',
            'price_old' => 'compare_price',
            'sku' => 'sku',
        ];

        foreach ($params as $item) {
            if (!empty($item['id'])) {
                $vnames = [];
                foreach ($item as $id => $value) {
                    if (in_array($id, ['id', 'product_id'])) {
                        continue;
                    }
                    $key = $mapParams[$id];
                    $vnames[] = $key.' = '.db_squote($value);
                }
                if (!empty($vnames)) {
                    $mysql->query(
                        'UPDATE '.prefix.'_eshop_variants SET '.implode(
                            ', ',
                            $vnames
                        ).' WHERE id = \''.(int)$item['id'].'\' '
                    );
                }
            }
        }

        $results = ['data' => [], 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_products()
    {

        global $mysql;

        $params = $this->getParams();

        $mapParams = [
            'id' => 'id',
            'name' => 'name',
            'short_description' => 'annotation',
            'description' => 'body',
            'vendor_code' => 'code',
        ];

        foreach ($params as $item) {
            if (!empty($item['id'])) {
                $vnames = [];
                foreach ($item as $id => $value) {
                    if ('id' == $id) {
                        continue;
                    }
                    $key = $mapParams[$id];
                    $vnames[] = $key.' = '.db_squote($value);
                }
                if (!empty($vnames)) {
                    $mysql->query(
                        'UPDATE '.prefix.'_eshop_products SET '.implode(
                            ', ',
                            $vnames
                        ).' WHERE id = \''.(int)$item['id'].'\' '
                    );
                }
            }
        }

        $results = ['data' => [], 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_features() {

        global $mysql;

        $params = $this->getParams();

        $mapParams = [
            'id' => 'feature_id',
            'product_id' => 'product_id',
            'value' => 'value'
        ];

        foreach ($params as $item) {
            if (!empty($item['id'])) {
                $vnames = [];
                foreach ($item as $id => $value) {
                    if ('id' == $id) {
                        continue;
                    }
                    $key = $mapParams[$id];
                    $vnames[] = $key.' = '.db_squote($value);
                }

                if (!empty($vnames)) {
                    $mysql->query(
                        'UPDATE '.prefix.'_eshop_options SET '.implode(
                            ', ',
                            $vnames
                        ).' WHERE '.$mapParams["id"].' = \''.(int)$item['id'].'\' '
                    );
                }
            }
        }

        $results = ['data' => [], 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function getProducts($orders)
    {
        global $mysql;

        $conditions = [];
        if (!empty($orders)) {
            $orders_comma_separated = implode(",", $orders);
            array_push($conditions, "order_id IN (".$orders_comma_separated.") ");
        }

        $basket = [];
        $total = 0;
        foreach ($mysql->select(
            "SELECT * FROM ".prefix."_eshop_order_basket ".(count($conditions) ? "WHERE ".implode(
                    " AND ",
                    $conditions
                ) : ""),
            1
        ) as $rec) {
            $rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
            $rec['xfields'] = unserialize($rec['linked_fld']);
            unset($rec['linked_fld']);
            $basket[] = $rec;
        }

        $orders = [];
        foreach ($basket as $position) {
            $total += round($position['price'] * $position['count'], 2);
            $orders[$position['order_id']]['positions'][] = $position;
        }

        $purchases = [];
        foreach ($mysql->select(
            "SELECT * FROM ".prefix."_eshop_purchases ".(count($conditions) ? "WHERE ".implode(
                    " AND ",
                    $conditions
                ) : ""),
            1
        ) as $prow) {
            $prow['info'] = json_decode($prow['info'], true);
            foreach ($prow['info'] as $k_info => $v_info) {
                $prow['info_string'] .= $k_info." => ".iconv("utf-8", "windows-1251", $v_info)."<br/>";
            }
            $purchases[] = $prow;
        }

        foreach ($orders as $id => $order) {
            $orders[$id]['purchases'] = $purchases;
            $orders[$id]['total'] = $total;
        }

        return $orders;
    }

    public function getOrders($conditions)
    {
        global $mysql;

        $orders = [];
        $fSort = " ORDER BY o.id";
        $sqlQ = "
        SELECT * 
        FROM ".prefix."_eshop_orders o 
        LEFT JOIN ".prefix."_users u ON o.author_id = u.id ".(count($conditions) ? "WHERE ".implode(
                    " AND ",
                    $conditions
                ) : "").$fSort;

        foreach ($mysql->select($sqlQ) as $row) {
            $orders[$row['id']] = $row;
        }

        return $orders;
    }

    public function getVariants($conditions)
    {
        global $mysql;
        $variants = [];
        $fSort = " ORDER BY id DESC";
        $sqlQ = "SELECT * FROM ".prefix."_eshop_variants ".(count($conditions) ? "WHERE ".implode(
                    " AND ",
                    $conditions
                ) : "").$fSort;
        foreach ($mysql->select($sqlQ) as $row) {
            $variants[] = $row;
        }

        return $variants;
    }

    public function addDateTimeCondition(&$conditions)
    {

        $fDateStart = $this->control['from'];
        $fDateEnd = $this->control['to'];

        if ($fDateStart && $fDateEnd) {
            $fDateEndWithTime = $fDateEnd." 23:59:59";
            array_push(
                $conditions,
                "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE(".db_squote(
                    $fDateStart
                ).",'%Y-%m-%d')) AND UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateEndWithTime).",'%Y-%m-%d %H:%i:%s'))"
            );
        } elseif ($fDateStart) {
            array_push(
                $conditions,
                "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE(".db_squote($fDateStart).",'%Y-%m-%d')) AND NOW()"
            );
        } elseif ($fDateEnd) {
            $fDateEndWithTime = $fDateEnd." 23:59:59";
            array_push(
                $conditions,
                "dt BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('01.01.1970','%Y-%m-%d')) AND UNIX_TIMESTAMP(STR_TO_DATE(".db_squote(
                    $fDateEndWithTime
                ).",'%Y-%m-%d %H:%i:%s'))"
            );
        }
    }

    public function getParams()
    {
        $params = json_decode($_POST['data'], 1);

        return $params;
    }

    public function renderResults($data)
    {

        global $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW;

        $SUPRESS_TEMPLATE_SHOW = 1;
        $SUPRESS_MAINBLOCK_SHOW = 1;

        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode($data);
        exit();
    }

}