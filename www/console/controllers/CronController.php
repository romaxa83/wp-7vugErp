<?php

namespace app\console\controllers;

use Yii;
use yii\console\Controller;
use yii2tech\crontab\CronTab;

class CronController extends Controller {

    private function getCronLink() {
        $root = dirname(dirname(__DIR__)) . '/yii';
        return [
            [
                'hour' => '*/1',
                'command' => "php ${root} xml/update;",
            ],
            [
                'min' => '*/1',
                'command' => "php ${root} syncronization/check-data;",
            ],
        ];
    }

    /**
     * запускает cron.
     * @package app\commands
     */
    public function actionStart() {
        $cronTab = new CronTab();
        $cronTab->headLines = [
            'SHELL=/bin/sh',
            'PATH=/usr/bin:/usr/sbin',
        ];
        $cronTab->setJobs($this->getCronLink());
        $cronTab->apply();
    }

    /**
     * останавливает cron.
     * @package app\commands
     */
    public function actionStop() {
        $cronTab = new CronTab();
        $cronTab->setJobs($this->getCronLink());
        $cronTab->remove();
    }

}