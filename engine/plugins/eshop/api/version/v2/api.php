<?php

class ApiEshopController extends ApiEshop
{

    public $allowMethods = [
        'get_orders',

        'update_products',
        'update_options',
        'update_params',
    ];

    public function update_products()
    {

        $output = [];
        $data = $this->getParams();

        $this->setActiveProducts(0);

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
            'active' => 'active',
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

                $this->setActive($item);
                $product = $this->prepareItemArray(
                    $item,
                    ['vendor_code', 'name', 'short_description', 'description', 'active']
                );
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

                $this->setActive($item);
                $product = $this->prepareItemArray(
                    $item,
                    ['url', 'vendor_code', 'name', 'short_description', 'description', 'id', 'active']
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
            'stock' => 'stock',
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
                    if (!empty($sVariants)) {
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

    public function update_params()
    {

        $output = [];
        $data = $this->getParams();

        $params = $data['options'];

        $mapParams = [
            'id' => 'external_id',
            'product_id' => 'product_id',
            'name' => 'name',
            'value' => 'value',
        ];

        $required = ['update' => ['id'], 'insert' => ['id']];

        foreach ($params as $id => $item) {
            $update = $this->checkProductExternalID($item['product_id']);
            if ($update) {
                $product = $this->getProductByExternalID($item['product_id']);
                $update2 = $this->checkCategoryExternalID($item['id']);
                if ($update2) {
                    $this->prepareUpdateParams($id, $item, $mapParams, $product, $output);
                } else {
                    $this->prepareAddCategory($item, $mapParams);
                    $this->prepareUpdateParams($id, $item, $mapParams, $product, $output);
                }
            } else {

                $output[$id] = $this->setError(
                    $item['id'],
                    self::STATUS_ERROR,
                    'Update error'
                );
            }
        }

        generate_catz_cache(true);
        generate_features_cache(true);
        generate_categories_features_cache(true);

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

    public function prepareAddCategory($item, $mapParams)
    {
        $pA = ['name', 'url', 'external_id'];
        $item['name'] = $item['id'];
        $mapParams['url'] = 'url';
        $item['url'] = $item['id'];
        $mapParams['external_id'] = 'external_id';
        $item['external_id'] = $item['id'];
        $cat = $this->prepareItemArray($item, $pA);
        $c = $this->getParamsArray($cat, $mapParams);
        $vnames = $this->generateInsertArray($c);
        $c_id = $this->addCategory($vnames);

        return $c_id;
    }

    public function prepareAddParamFeature($item, $mapParams)
    {
        $pA = ['ftype', 'name', 'categories_external_id'];
        $mapParams['ftype'] = 'ftype';
        $item['ftype'] = 0;
        $mapParams['categories_external_id'] = 'categories_external_id';
        $item['categories_external_id'] = $item['id'];
        $param = $this->prepareItemArray($item, $pA);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateInsertArray($p);
        $f_id = $this->addParamFeature($vnames);

        return $f_id;
    }

    public function prepareAddParamOption($item, $mapParams, $f_id, $product)
    {
        $pB = ['product_id', 'feature_id', 'value'];
        $mapParams['feature_id'] = 'feature_id';
        $item['feature_id'] = $f_id;
        $item['product_id'] = $product['id'];
        $param = $this->prepareItemArray($item, $pB);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateInsertArray($p);
        $o_id = $this->addParamOption($vnames);

        return $o_id;
    }


    public function prepareEditParamFeature($item, $mapParams, $pm)
    {
        $pA = ['name'];
        $param = $this->prepareItemArray($item, $pA);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateUpdateArray($p);
        $f_id = $this->updateParamFeature($pm['id'], $vnames);

        return $pm['id'];
    }

    public function prepareEditParamOption($item, $mapParams, $f_id, $product)
    {
        $pB = ['value'];
        $param = $this->prepareItemArray($item, $pB);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateUpdateArray($p);
        $o_id = $this->updateParamOption($product['id'], $f_id, $vnames);

        return $o_id;
    }

    public function prepareReplaceCategoryFeature($item, $mapParams, $category_id, $feature_id)
    {
        $pB = ['category_id', 'feature_id'];

        $mapParams['category_id'] = 'category_id';
        $item['category_id'] = $category_id;

        $mapParams['feature_id'] = 'feature_id';
        $item['feature_id'] = $feature_id;

        $param = $this->prepareItemArray($item, $pB);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateUpdateArray($p);
        $cf_id = $this->replaceCategoryFeature($vnames);

        return $cf_id;
    }

    public function prepareReplaceCategoryProduct($item, $mapParams, $category_id, $product_id)
    {
        $pB = ['category_id', 'product_id'];

        $mapParams['category_id'] = 'category_id';
        $item['category_id'] = $category_id;

        $mapParams['product_id'] = 'product_id';
        $item['product_id'] = $product_id;

        $param = $this->prepareItemArray($item, $pB);
        $p = $this->getParamsArray($param, $mapParams);
        $vnames = $this->generateUpdateArray($p);
        $cf_id = $this->replaceCategoryProduct($vnames);

        return $cf_id;
    }

    public function prepareUpdateParams($id, $item, $mapParams, $product, &$output)
    {
        $category = $this->getCategoryByExternalID($item['id']);
        $update3 = $this->checkParamsName($item['id'], $item['name']);
        if ($update3) {
            $param = $this->getParamsByName($item['id'], $item['name']);
            $f_id = $this->prepareEditParamFeature($item, $mapParams, $param);
            $update4 = $this->checkOptionID($f_id, $product['id']);
            if ($update4) {
                $o_id = $this->prepareEditParamOption($item, $mapParams, $f_id, $product);
            } else {
                $o_id = $this->prepareAddParamOption($item, $mapParams, $f_id, $product);
            }
        } else {
            $f_id = $this->prepareAddParamFeature($item, $mapParams);
            $o_id = $this->prepareAddParamOption($item, $mapParams, $f_id, $product);
        }

        $this->prepareReplaceCategoryFeature($item, $mapParams, $category['id'], $f_id);
        $this->prepareReplaceCategoryProduct($item, $mapParams, $category['id'], $product['id']);

        $output[$id] = ['id' => $item['id'], 'status' => self::STATUS_OK];
    }

    public function prepareOrdersItemArray($orders)
    {
        foreach ($orders as $order) {
            $order_id = $order['order_id'];

            $paymentType = $this->getEntityRow(prefix."_eshop_payment_type", $order_id);
            $deliveryType = $this->getEntityRow(prefix."_eshop_delivery_type", $order_id);

            $order['paymentType'] = $paymentType['name'];
            $order['deliveryType'] = $deliveryType['name'];

            $order['dt'] = $this->formatDate('Y-m-d H:i:s', $order['dt']);

            $ordersInfo[$order_id] = $this->prepareItemArray(
                $order,
                [
                    'order_id',
                    'dt',
                    'paid',
                    'name',
                    'address',
                    'phone',
                    'email',
                    'comment',
                    'total_price',
                    'paymentType',
                    'deliveryType',
                ]
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

    public function formatDate($format, $dt)
    {
        return date($format, $dt);
    }

}