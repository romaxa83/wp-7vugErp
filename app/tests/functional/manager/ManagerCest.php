<?php


class ManagerCest 
{
    private function formattedUrl()
    {
        $controllerlist = [];
        if ($handle = opendir(\Yii::$app->getBasePath() . '/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php' && $file !== 'BaseController.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);

        $fulllist = [];
        foreach ($controllerlist as $controller) {
            $handle = fopen(\Yii::$app->getBasePath() . '/controllers/' . $controller, "r");
            if ($handle && $controller != 'BaseController.php') {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)) {
                        if (strlen($display[1]) > 2) {
                            $fulllist[substr($controller, 0, -4)][] = strtolower(preg_replace("/(?!^)([A-Z])/", '-$1', $display[1]));
                        }
                    }

                    if(str_replace([' ',PHP_EOL],'',$line) == 'if(Yii::$app->request->isAjax){'){
                        array_pop($fulllist[substr($controller, 0, -4)]);
                    }
                }
            }
            fclose($handle);
        }

        $listUrl = [];
        foreach($fulllist as $nameController => $arrayAction){
            preg_match_all('((?:^|[A-Z])[^A-Z]*)', $nameController, $matches);

            if($matches[0][2] != 'Controller'){
                $nameController = $matches[0][1] . '-' . $matches[0][2];
            }else{
                $nameController = $matches[0][1];
            }

            if(isset($matches[0][3]) && $matches[0][3] == 'Consumption'){
                $nameController = $matches[0][1] . '-' . $matches[0][2] . '-' . $matches[0][3];
            }

            foreach($arrayAction as $oneAction){
                $listUrl[] = strtolower(str_replace('Controller','',$nameController . '/' . $oneAction));
            }   
        }

        return $listUrl;
    }

    public function checkAccess(\FunctionalTester $I)
    {
        $routeList = $this->formattedUrl();
      
        $manager = \app\models\User::findOne(['role' => 'manager']);
        $I->amLoggedInAs($manager);

        foreach($routeList as $one){
            if($one == 'site/index' || $one == 'site/logout'){
                continue;
            }

            $I->amOnRoute($one);
            $I->see('You are not allowed to perform this action');
        }
    }    

}
