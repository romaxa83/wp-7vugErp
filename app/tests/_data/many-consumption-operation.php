<?php

$operations = [];

for ($i = 1;$i < 11;$i++){

    $operation = [
        'id' => $i,
        'transaction' => $i . '00' . rand(100,999),
        'old_value' => null,
        'whence' => 1,
        'where' => rand(1,3),
        'prod_value' => null,
        'status' => 0,
        'type' => 2,
        'date' => '2019-05-30 16:48:17',
        'total_usd' => null,
        'total_ua' => null,
        'course' => 26,
        'trade_price' => null,
        'start_price' => null,
        'cost_price' => null,
        'date_update' => '2019-05-30 16:48:17',
        'recalculated' => null
    ];

    $operations[$i] = $operation;
}

return $operations;