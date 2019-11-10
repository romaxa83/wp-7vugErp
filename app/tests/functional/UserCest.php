<?php


class UserCest 
{
    public function CreateUser(\FunctionalTester $I)
    {
        $admin = \app\models\User::findByUsername('admin');
        $I->amLoggedInAs($admin);

        $I->amOnRoute('user/index');

        $I->canSee('Добавить роль');
        $I->canSee('Добавить пользователя');

        $I->click('//*[@id="focus-wrapper"]/div[1]/section[2]/div/div[1]/div/button[1]');

        $I->submitForm('#role-form form', [
            'AuthItem[name]' =>  'manager',
            'AuthItem[description]' => 'Менеджер'
        ]);

        $I->submitForm('#user-form form', [
            'User[username]' =>  'manager1',
            'User[email]' => 'mail@gmail.com',
            'User[password]' => 'password1',
            'User[role]' => 'manager',
            'User[store_id]' => 2
        ]);

        $I->canSee('manager1','#tab1 > div > div > table > tbody > tr:nth-child(2) > td:nth-child(1)');
        $I->canSee('mail@gmail.com','#tab1 > div > div > table > tbody > tr:nth-child(2) > td:nth-child(2)');
        $I->canSee('manager','#tab1 > div > div > table > tbody > tr:nth-child(2) > td:nth-child(3)');
        $I->canSee('password1','#tab1 > div > div > table > tbody > tr:nth-child(2) > td:nth-child(4)');

        $I->submitForm('#user-form form', [
            'User[username]' =>  'manager2',
            'User[email]' => 'mail2@gmail.com',
            'User[password]' => 'password2',
            'User[role]' => 'manager',
            'User[store_id]' => 3
        ]);

        $I->canSee('manager2','#tab1 > div > div > table > tbody > tr:nth-child(3) > td:nth-child(1)');
        $I->canSee('mail2@gmail.com','#tab1 > div > div > table > tbody > tr:nth-child(3) > td:nth-child(2)');
        $I->canSee('manager','#tab1 > div > div > table > tbody > tr:nth-child(3) > td:nth-child(3)');
        $I->canSee('password2','#tab1 > div > div > table > tbody > tr:nth-child(3) > td:nth-child(4)');
        
    }

}
