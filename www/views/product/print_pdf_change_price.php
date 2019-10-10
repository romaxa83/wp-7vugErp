<?php 
    echo yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => FALSE,
        'columns' => [
            'id',
            'vendor_code',
            'name',
            'category_id',
            'agent_id',
            'amount',
            'unit',
            'start_price',
            'cost_price',
            'trade_price',
            'price1',
            'is_variant',
            'status',
            'change_price',
            'created_at',
            'updated_at',
            'min_amount',
            'margin'
        ]
    ]);