<?php

namespace app\modules\xml;

use yii\base\Module;

class Xml extends Module
{
    public $controllerNamespace = 'app\modules\xml\controllers';

    public function init() {
        parent::init();
        $this->setAliases([
            '@xml-assets' => __DIR__ . '/assets'
        ]);
    }

}