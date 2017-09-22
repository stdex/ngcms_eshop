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

        $this->encodeUtf8Array($orders);
        $results = ['data' => $orders, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function get_order_products()
    {
        $conditions = [];
        $this->addDateTimeCondition($conditions);
        $conditions = [];
        if (!empty($this->control['order_id'])) {
            $conditions[] = 'o.id = ' . (int)$this->control['order_id'];
        }
        $orders = array_keys($this->getOrders($conditions));
        $positions = $this->getProducts($orders);

        $results = ['data' => $positions, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function get_features()
    {
        global $mysql;

        foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_features ORDER BY position, id") as $frow) {
            $options[] = $frow;
        }

        $this->encodeUtf8Array($options);
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
        $this->encodeUtf8Array($variants);

        $results = ['data' => $variants, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_order_statuses()
    {
        global $mysql;

        $output = [];
        $params = $this->getParams();

        foreach ($params as $id => $item) {

            $checked = $this->checkRequiredParams($item, ['order_id', 'status']);
            if (!$checked) {
                $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                continue;
            }

            $order_id = $item['order_id'];
            $status = $item['status'];

            $checked = $this->checkOrderID($order_id);
            if (!$checked) {
                $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                continue;
            }

            if (!in_array($status, ["0", "1"])) {
                $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Incorrect status'];
                continue;
            }

            $mysql->query(
                "UPDATE ".prefix."_eshop_orders SET paid = ".(int)$status." WHERE id = ".(int)$order_id
            );
            $output[$id] = ['id' => $order_id, 'status' => 'OK'];

        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_variants()
    {
        global $mysql;

        $output = [];
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

        $required = ['update' => ['id'], 'insert' => ['product_id']];

        foreach ($params as $id => $item) {
            if (!empty($item['id'])) {
                $checked = $this->checkRequiredParams($item, $required['update']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                    continue;
                }
                $checked = $this->checkVariantID($item['id']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                    continue;
                }
                unset($item['product_id']);
                $params = $this->getParamsArray($item, $mapParams);
                $vnames = $this->generateUpdateArray($params);

                if (!empty($vnames)) {
                    $mysql->query(
                        'UPDATE '.prefix.'_eshop_variants SET '.implode(
                            ', ',
                            $vnames
                        ).' WHERE id = \''.(int)$item['id'].'\' '
                    );
                    $output[$id] = ['id' => $item['id'], 'status' => 'OK'];
                }
            } else {
                $checked = $this->checkRequiredParams($item, $required['insert']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                    continue;
                }
                $checked = $this->checkProductID($item['product_id']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                    continue;
                }

                $params = $this->getParamsArray($item, $mapParams);
                $vnames = $this->generateInsertArray($params);
                if (!empty($vnames)) {
                    $mysql->query(
                        "INSERT INTO ".prefix."_eshop_variants (".implode(",", array_keys($vnames)).") values (".implode(
                            ",",
                            array_values($vnames)
                        ).")"
                    );
                    $qid = $mysql->lastid('eshop_variants');
                    $output[$id] = ['id' => $qid, 'status' => 'OK'];
                }
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_products()
    {

        global $mysql;

        $output = [];
        $params = $this->getParams();

        $mapParams = [
            'id' => 'id',
            'name' => 'name',
            'short_description' => 'annotation',
            'description' => 'body',
            'vendor_code' => 'code',
        ];

        $required = ['update' => ['id'], 'insert' => ['name']];

        foreach ($params as $id => $item) {
            if (!empty($item['id'])) {
                $checked = $this->checkRequiredParams($item, $required['update']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                    continue;
                }
                $checked = $this->checkProductID($item['id']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                    continue;
                }
                $params = $this->getParamsArray($item, $mapParams);
                $vnames = $this->generateUpdateArray($params);
                if (!empty($vnames)) {
                    $mysql->query(
                        'UPDATE '.prefix.'_eshop_products SET '.implode(
                            ', ',
                            $vnames
                        ).' WHERE id = \''.(int)$item['id'].'\' '
                    );
                    $output[$id] = ['id' => $item['id'], 'status' => 'OK'];
                }
            } else {
                $checked = $this->checkRequiredParams($item, $required['insert']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                    continue;
                }
                $checked = $this->checkProductURL(['name' => $item['name']]);
                if ($checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with the same URL already exist'];
                    continue;
                }
                $item['url'] = $this->generateURLbyName($item['name']);
                $mapParams['url'] = 'url';
                $params = $this->getParamsArray($item, $mapParams);
                $vnames = $this->generateInsertArray($params);
                if (!empty($vnames)) {
                    $mysql->query(
                        "INSERT INTO ".prefix."_eshop_products (".implode(",", array_keys($vnames)).") values (".implode(
                            ",",
                            array_values($vnames)
                        ).")"
                    );
                    $qid = $mysql->lastid('eshop_products');
                    $output[$id] = ['id' => $qid, 'status' => 'OK'];
                }
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => 'OK'];
        $this->renderResults($results);
    }

    public function update_features() {

        global $mysql;

        $output = [];
        $params = $this->getParams();

        $mapParams = [
            'id' => 'feature_id',
            'product_id' => 'product_id',
            'value' => 'value'
        ];

        $required = ['update' => ['id', 'product_id', 'value'], 'insert' => ['id', 'product_id', 'value']];

        foreach ($params as $id => $item) {
            if (!empty($item['id'])) {

                $checked = $this->checkRequiredParams($item, $required['update']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Please fill out all required fields'];
                    continue;
                }
                $checked = $this->checkFeatureID($item['id']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                    continue;
                }

                $checked = $this->checkProductID($item['product_id']);
                if (!$checked) {
                    $output[$id] = ['id' => NULL, 'status' => 'error', 'message' => 'Item with this ID does not exist'];
                    continue;
                }

                $checked = $this->checkOptionID($item['id'], $item['product_id']);
                if ($checked) {
                    $feature_id = $item['id'];
                    $product_id = $item['product_id'];
                    unset($item['id'], $item['product_id']);
                    $params = $this->getParamsArray($item, $mapParams);
                    $vnames = $this->generateUpdateArray($params);
                    if (!empty($vnames)) {
                        $mysql->query(
                            'UPDATE '.prefix.'_eshop_options SET '.implode(
                                ', ',
                                $vnames
                            ).' WHERE '.$mapParams["id"].' = \''.(int)$feature_id.'\' AND '.$mapParams["product_id"].' = \''.(int)$product_id.'\''
                        );
                        $output[$id] = ['id' => NULL, 'status' => 'OK'];
                    }
                } else {
                    $params = $this->getParamsArray($item, $mapParams, true);
                    $vnames = $this->generateInsertArray($params);
                    if (!empty($vnames)) {
                        $mysql->query(
                            "INSERT INTO ".prefix."_eshop_options (".implode(",", array_keys($vnames)).") values (".implode(
                                ",",
                                array_values($vnames)
                            ).")"
                        );
                        $output[$id] = ['id' => NULL, 'status' => 'OK'];
                    }
                }
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => 'OK'];
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
        SELECT * , o.id as order_id
        FROM ".prefix."_eshop_orders o 
        LEFT JOIN ".prefix."_users u ON o.author_id = u.id ".(count($conditions) ? "WHERE ".implode(
                    " AND ",
                    $conditions
                ) : "").$fSort;

        foreach ($mysql->select($sqlQ) as $row) {
            $orders[$row['order_id']] = $row;
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

    public function getParamsArray($item, $mapParams, $includeID = false)
    {
        $params = [];
        foreach ($item as $id => $value) {
            if (('id' == $id) && !$includeID) {
                continue;
            }
            $key = $mapParams[$id];
            $params[$key] = iconv("utf-8", "windows-1251", $value);
        }

        return $params;
    }

    public function generateUpdateArray($params)
    {
        $output = [];
        foreach ($params as $key => $value) {
            $output[] = $key.' = '.db_squote($value);
        }

        return $output;
    }

    public function generateInsertArray($params)
    {
        $output = [];
        foreach ($params as $key => $value) {
            $output[$key] = db_squote($value);
        }

        return $output;
    }

    public function checkRequiredParams($item, $required)
    {

        if (empty(array_intersect_key($item, array_flip($required)))) {
            return false;
        }

        foreach ($item as $id => $param) {
            if (in_array($id, $required)) {
                if ($param == "") {
                    return false;
                }
            }
        }

        return true;
    }

    public function checkProductURL($SQL)
    {
        global $mysql, $parse;

        if (empty($SQL['url'])) {
            $SQL['url'] = $this->generateURLbyName($SQL['name']);
        }

        if ($SQL["url"]) {
            if (is_array(
                $mysql->record(
                    "SELECT id FROM ".prefix."_eshop_products WHERE url = ".db_squote($SQL["url"])." LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function checkProductID($id)
    {
        global $mysql;

        if ($id) {
            if (is_array(
                $mysql->record(
                    "SELECT id FROM ".prefix."_eshop_products WHERE id = ".(int)$id." LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function checkVariantID($id)
    {
        global $mysql;

        if ($id) {
            if (is_array(
                $mysql->record(
                    "SELECT id FROM ".prefix."_eshop_variants WHERE id = ".(int)$id." LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function checkFeatureID($id)
    {
        global $mysql;

        if ($id) {
            if (is_array(
                $mysql->record(
                    "SELECT id FROM ".prefix."_eshop_features WHERE id = ".(int)$id." LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function checkOptionID($id, $product_id)
    {
        global $mysql;

        if ($id) {
            if (is_array(
                $mysql->record(
                    "SELECT * FROM ".prefix."_eshop_options WHERE feature_id = ".(int)$id." AND product_id = ".(int)$product_id."  LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function checkOrderID($id)
    {
        global $mysql;

        if ($id) {
            if (is_array(
                $mysql->record(
                    "SELECT id FROM ".prefix."_eshop_orders WHERE id = ".(int)$id." LIMIT 1"
                )
            )) {
                return true;
            }
        }

        return false;
    }

    public function generateURLbyName($name)
    {
        global $parse;

        return strtolower($parse->translit($name, 1, 1));
    }

    public function encodeUtf8Array(&$items)
    {
        foreach ($items as $itemID => $item) {
            foreach ($item as $id => $param) {
                $items[$itemID][$id] = iconv("windows-1251", "utf-8", $param);
            }
        }
    }

    public function getParams()
    {
        $data = file_get_contents("php://input");
        $params = json_decode($data, 1);

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