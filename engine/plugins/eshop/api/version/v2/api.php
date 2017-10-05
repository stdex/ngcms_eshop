<?php

class ApiEshopController extends ApiEshop
{

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
                    $output[$id] = [
                        'id' => null,
                        'status' => self::STATUS_ERROR,
                        'message' => 'Please fill out all required fields',
                    ];
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
                $vnames = $this->generateInsertArray($v);

                $r2 = $this->updateVariant($item['product_id'], $vnames);

                if ($r1 && $r2) {
                    $output[$id] = ['id' => $item['id'], 'status' => self::STATUS_OK];
                }

            } else {
                $checked = $this->checkRequiredParams($item, $required['insert']);
                if (!$checked) {
                    $output[$id] = [
                        'id' => null,
                        'status' => self::STATUS_ERROR,
                        'message' => 'Please fill out all required fields',
                    ];
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

                $r2 = $this->updateVariant($item['product_id'], $vnames);

                if ($r1 && $r2) {
                    $output[$id] = ['id' => $item['id'], 'status' => self::STATUS_OK];
                }
            }
        }

        $this->encodeUtf8Array($output);
        $results = ['data' => $output, 'status' => self::STATUS_OK];
        $this->renderResults($results);
    }

    public function prepareItemArray($item, $itemKeys)
    {
        $newItem = [];
        foreach ($item as $key => $value) {
            if (in_array($key, $itemKeys)) {
                $newItem[$key] = $value;
            }
        }

        return $newItem;
    }

    public function updateVariant($product_id, $vnames)
    {
        $this->removeVariantsByProductID($product_id);

        return $this->addVariant($vnames);
    }

    public function setStock(&$item)
    {
        if (empty($item['stock'])) {
            if ($item['count'] > 0) {
                $item['stock'] = 5;
            } else {
                $item['stock'] = 0;
            }
        }
    }

}