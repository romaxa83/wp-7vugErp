<?php

namespace app\modules\order;

use yii\base\Module;

class Order extends Module {

    public $controllerNamespace = 'app\modules\order\controllers';

    public function init() {
        parent::init();
        $this->setAliases([
            '@order-assets' => __DIR__ . '/assets'
        ]);
    }

}
