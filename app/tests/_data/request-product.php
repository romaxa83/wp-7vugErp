<?php 

$product = app\models\Product::find()->where(['view_manager' => 1])->asArray()->all();

for($i = 1;$i < 50;$i++){
    $requestId = rand(1,3);
    $request = app\modules\manager\models\Request::find(['id' => $requestId])->asArray()->with('store')->one();

    $productRequest = [
        'id' => $i,
        'request_id' => $requestId,
        'product_id' => $product[$i]['id'],
        'vproduct_id' => '',
        'amount' => rand(0,$product[$i]['amount']),
        'price' => $product[$i]['price'.$request['store']['price_type']],
        'cost_price' => $product[$i]['cost_price'],
        'trade_price' => $product[$i]['trade_price']
    ];

    $allProduct[] = $productRequest;
}

return $allProduct;