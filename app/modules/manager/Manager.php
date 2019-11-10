<?php

namespace app\modules\manager;

use yii\base\Module;

class Manager extends Module {

    public $controllerNamespace = 'app\modules\manager\controllers';

    public function init() {
        parent::init();
        $this->setAliases([
            '@manager-assets' => __DIR__ . '/assets'
        ]);
    }

}
