<?php


class ShopCest 
{
    public function CreateShop(\FunctionalTester $I)
    {
        $admin = \app\models\User::findByUsername('admin');
        $I->amLoggedInAs($admin);

        $I->amOnRoute('agent/store');
        $I->canSee('Добавить новый магазин');

        $I->click('//*[@id="focus-wrapper"]/div[1]/section[2]/div/div[1]/div/button[1]');

        $I->submitForm('#agent-form form', [
            'Agent[firm]' =>  'shopPrice1',
            'Agent[address]' => 'test',
            'Agent[telephone]' => '0504948285',
            'Agent[data]' => 'test',
            'Agent[status]' => 'on'
        ]);

        $I->click('//*[@id="focus-wrapper"]/div[1]/section[2]/div/div[1]/div/button[1]');

        $I->submitForm('#agent-form form', [
            'Agent[firm]' =>  'shopPrice2',
            'Agent[address]' => 'test2',
            'Agent[telephone]' => '0504948285',
            'Agent[data]' => 'test2',
            'Agent[status]' => 'on'
        ]);
    }

    public function UpdateTypePrice(\FunctionalTester $I)
    {
        $admin = \app\models\User::findByUsername('admin');
        $I->amLoggedInAs($admin);
        $shop = \app\models\Agent::find()->asArray()->where(['firm' => 'shopPrice2'])->one();

        $I->amOnRoute('agent/update',['id' => $shop['id'],'type' => 2]);
        $I->selectOption('//*[@id="agent-price_type"]',2);
        $I->click('.save-agent');
    }
}
