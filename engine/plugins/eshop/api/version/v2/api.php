<?php

class ApiEshopController extends ApiEshop
{

    public $allowMethods = [
        'get_orders',

        'update_products',
        'update_options',
    ];

    public function update_products()
    {

        $output = [];
        $data = $this->getParams();

        $params = $data['products'];

        $mapParams = [
            'id' => 'external_id',
            'vendor_code' => 'code',
            'name' => 'name',
            'short_description' => 'annotation',
            'description' => 'body',
            'price' => 'price',
            'price_old' => 'compare_price',
            'count' => 'amount',
            'stock' => 'stock',
        ];

        $required = ['update' => ['id'], 'insert' => ['id']];

        foreach ($params as $id => $item) {
            $update = $this->checkProductExternalID($item['id']);
            if ($update) {
                $checked = $this->checkRequiredParams($item, $required['update']);
                if (!$checked) {
                    $output[$id] = $this->setError(
                        $item['id'],
                        self::STATUS_ERROR,
                        'Please fill out all required fields'
                    );
                    continue;
                }

                $product = $this->prepareItemArray($item, ['vendor_code', 'name', 'short_description', 'description']);
                $p = $this->getParamsArray($product, $mapParams);
                $pnames = $this->generateUpdateArray($p);

                $r1 = $this->updateProductByExternalID($item['id'], $pnames);
                $nProduct = $this->getProductByExternalID($item['id']);

                $item['product_id'] = $nProduct['id'];
                $mapParams['product_id'] = 'product_id';

                $this->setStock($item);

                $variant = $this->prepareItemArray($item, ['product_id', 'price', 'price_old', 'count', 'stock']);
                $v = $this->getParamsArray($variant, $mapParams);
                $vnames = $this->generateUpdateArray($v);

                $r2 = $this->updateVariant($item['product_id'], $vnames);

                if ($r1 && $r2) {
                    $output[$id] = $this->setResult($item['id'], self::STATUS_OK);
                } else {
                    $output[$id] = $this->setError(
                        $item['id'],
                        self::STATUS_ERROR,
                        'Update error'
                    );
                }

            } else {
                $checked = $this->checkRequiredParams($item, $required['insert']);
                if (!$checked) {
                    $output[$id] = $this->setError(
                        $item['id'],
                        self::STATUS_ERROR,
                        'Please fill out all required fields'
                    );
                    continue;
                }

                $item['url'] = $this->generateURLbyName($item['name']);
                $mapParams['url'] = 'url';

                $product = $this->prepareItemArray(
                    $item,
                    ['url', 'vendor_code', 'name', 'short_description', 'description', 'id']
                );
                $p = $this->getParamsArray($product, $mapParams, true);
                $pnames = $this->generateInsertArray($p);
                $r1 = $this->addProduct($pnames);
                $nProduct = $this->getProductByExternalID($item['id']);

                $item['product_id'] = $nProduct['id'];
                $mapParams['product_id'] = 'product_id';

                $this->setStock($item);

                $variant = $this->prepareItemArray($item, ['product_id', 'price', 'price_old', 'count', 'stock']);
                $v = $this->getParamsArray($variant, $mapParams);
                $vnames = $this->generateInsertArray($v);

                $r2 = $this->addVariant($vnames);

                if ($r1 && $r2) {
                    $output[$id] = $this->setResult($item['id'], self::STATUS_OK);
                } else {
                    $output[$id] = $this->setError(
                        $item['id'],
                        self::STATUS_ERROR,
                        'Insert error'
                    );
                }
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => self::STATUS_OK];
        $this->renderResults($results);
    }


    public function update_options()
    {

        $output = [];
        $data = $this->getParams();

        $params = $data['params'];

        $mapParams = [
            'id' => 'external_id',
            'product_id' => 'product_id',
            'name' => 'name',
            'count' => 'amount',
            'stock' => 'stock'
        ];

        $required = ['update' => ['id'], 'insert' => ['id']];

        foreach ($params as $id => $item) {
            $update = $this->checkProductExternalID($item['product_id']);
            if ($update) {
                $product = $this->getProductByExternalID($item['product_id']);
                $checked = $this->checkRequiredParams($item, $required['update']);
                if (!$checked) {
                    $output[$id] = $this->setError(
                        $item['id'],
                        self::STATUS_ERROR,
                        'Please fill out all required fields'
                    );
                    continue;
                }

                $variants = $this->getVariantsByExternalId($item['id']);

                if (!empty($variants)) {
                    $item['product_id'] = $product['id'];
                    $this->setStock($item);
                    $variant = $this->prepareItemArray($item, ['stock', 'product_id', 'name', 'count']);
                    $v = $this->getParamsArray($variant, $mapParams);
                    $vnames = $this->generateUpdateArray($v);
                    $this->updateVariants($variants, $vnames);
                } else {

                    $pA = ['stock', 'id', 'product_id', 'name', 'count'];
                    $sVariants = $this->getVariantsByProductId($product['id']);
                    if(!empty($sVariants)) {
                        $sVariant = current($sVariants);
                        $item['price'] = $sVariant['price'];
                        $item['compare_price'] = $sVariant['compare_price'];
                        $pA = array_merge(['price', 'compare_price'], $pA);
                        $mapParams['price'] = 'price';
                        $mapParams['compare_price'] = 'compare_price';
                    }

                    $item['product_id'] = $product['id'];
                    $this->setStock($item);
                    $variant = $this->prepareItemArray($item, $pA);
                    $v = $this->getParamsArray($variant, $mapParams, true);
                    $vnames = $this->generateInsertArray($v);
                    $this->addVariant($vnames);
                    $this->removeVariantsWithoutExteranalID($item['product_id']);
                }

                $output[$id] = ['id' => $item['id'], 'status' => self::STATUS_OK];

            } else {

                $output[$id] = $this->setError(
                    $item['id'],
                    self::STATUS_ERROR,
                    'Update error'
                );
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => self::STATUS_OK];
        $this->renderResults($results);
    }


    public function get_orders()
    {
        $conditions = [];
        $this->addDateTimeCondition($conditions);
        if (!empty($this->control['order_id'])) {
            $conditions[] = 'o.id = '.(int)$this->control['order_id'];
        }

        $orders = $this->getOrders($conditions);
        $ordersInfo = [];
        if (!empty($orders)) {
            $ordersInfo = $this->prepareOrdersItemArray($orders);
        }

        $this->encodeUtf8Array($ordersInfo);
        $results = ['data' => $ordersInfo, 'status' => self::STATUS_OK];
        $this->renderResults($results);
    }


    public function prepareOrdersItemArray($orders)
    {
        foreach ($orders as $order) {
            $order_id = $order['order_id'];
            $ordersInfo[$order_id] = $this->prepareItemArray(
                $order,
                ['order_id', 'dt', 'paid', 'name', 'address', 'phone', 'email', 'comment', 'total_price']
            );
        }

        $positions = $this->getPos(array_keys($ordersInfo));

        foreach ($orders as $order) {
            $order_id = $order['order_id'];
            $sPosInfo = [];
            $posInfo = $positions[$order_id]['positions'];
            foreach ($posInfo as $pid => $pos) {
                $sPosInfo[$pid] = $this->prepareItemArray(
                    $pos,
                    ['count', 'price', 'sum', 'linked_id', 'xfields']
                );
                $itemInfo = $this->prepareItemArray(
                    $sPosInfo[$pid]['xfields']['item'],
                    ['code', 'name', 'external_id']
                );
                $itemInfo['product_id'] = $itemInfo['external_id'];
                unset($itemInfo['external_id']);
                unset($sPosInfo[$pid]['xfields']);
                $sPosInfo[$pid] = array_merge($sPosInfo[$pid], $itemInfo);
            }
            $ordersInfo[$order_id]['positions'] = $sPosInfo;
        }

        return $ordersInfo;
    }

}