<?php

namespace app\modules\xml\type;

class ShopInfo
{
    private $name;

    private $company;


    public function __construct($name, $company)
    {
        $this->name = $name;
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }
}