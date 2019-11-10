<?php
$products = [];
for ($i = 1;$i < 200;$i++){
    $product = [
        'id' => $i,
        'name' => 'product'.$i,
        'vendor_code' => 1001002,
        'category_id' => rand(1,3),
        'id_char' => null,
        'agent_id' => rand(1,3),
        'amount' => rand(0,100),
        'unit' => 'шт.',
        'start_price' => floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
        'cost_price' => $price = floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
        'trade_price' => getTradePrice($price),
        'price1' => floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
        'price2' => floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
        'is_variant' => 1,
        'status' => 1,
        'created_at' => 1558941652,
        'updated_at' => 1558941652,
        'change_price' => 0,
        'min_amount' => 10,
        'margin' => 10,
        'agents' => null,
        'view_manager' => rand(0,1),
        'date_adjustment' => null,
        'publish_status' => 1,
    ];

    $products[$i] = $product;
}

return $products;