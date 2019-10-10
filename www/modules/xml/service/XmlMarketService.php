<?php

namespace app\modules\xml\service;

use yii\helpers\Html;
use app\modules\xml\type\ShopInfo;
use app\repositories\CategoryRepository;
use app\repositories\ProductRepository;

class XmlMarketService
{
    /**
     * @var integer
     */
    private $periodUpdate = 3600;

    /**
     * @var ShopInfo
     */
    private $shop;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        ShopInfo $shop,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    )
    {
        $this->shop = $shop;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * генерирует xml
     * @param bool $update добавляет только обновленные за час товары
     * @return string|array
     */
    public function generate($update=false)
    {
        //включаем буферизацию вывода
        ob_start();

        $writer = new \XMLWriter();
        $writer->openURI('php://output');

        $writer->startDocument('1.0','utf-8');
        $writer->startDTD('yml_catalog SYSTEM "shops.dtd"');
        $writer->endDTD();

        $writer->startElement('yml_catalog');
        $writer->writeAttribute('date',date('Y-m-d H:i'));

        $writer->startElement('shop');
        $writer->writeElement('name',Html::encode($this->shop->getName()));
        $writer->writeElement('company',Html::encode($this->shop->getCompany()));

        //валюта

        $writer->startElement('currencies');
        $writer->startElement('currency');
        $writer->writeAttribute('id','UAH');
        $writer->writeAttribute('rate',1);
        $writer->endElement();
        $writer->endElement();

        //категории

        $writer->startElement('categories');
        foreach ($this->categoryRepository->getAll() as $category){
            $writer->startElement('category');
            $writer->writeAttribute('id',$category->id);
            if($category->parent_id !== 0){
                $writer->writeAttribute('parentId',$category->parent_id);
            }
            $writer->writeRaw(Html::encode($category->name));
            $writer->endElement();
        }
        $writer->endElement();

        //продукты

        $products = $this->productRepository->getAllIterator();
        if($update){
            $products = $this->productRepository->getAllIteratorUpdate($this->periodUpdate);
        }

        $writer->startElement('offers');
        foreach ($products as $product){
            $writer->startElement('offer');

            $writer->writeAttribute('id',$product->id);
            $writer->writeAttribute('available',($product->amount > 0) ? 'true' : 'false');

            $writer->writeElement('currencyId','UAH');
            $writer->writeElement('categoryId',$product->category_id);
            $writer->writeElement('quantity',$product->amount);
            $writer->writeElement('price',number_format($product->price1,2,'.',' '));

            $writer->writeElement('name',$product->name);

            $writer->startElement('param');
            $writer->writeAttribute('name','Артикул');
            $writer->text($product->vendor_code);
            $writer->endElement();

            $writer->startElement('param');
            $writer->writeAttribute('name','Цена');
            $writer->writeAttribute('id',$product->id);
            $writer->text($product->cost_price);
            $writer->endElement();

            $writer->startElement('param');
            $writer->writeAttribute('name','ВЕС');
            $writer->text('33 test');
            $writer->endElement();


            $writer->endElement();
        }
        $writer->endElement();

        //товары на удаления
        if($update){
            $productsDelete = $this->productRepository->getAllIteratorDelete($this->periodUpdate);
            if($productsDelete){
                $writer->startElement('offers');
                foreach ($productsDelete as $product){
                    $writer->startElement('offer');

                    $writer->writeAttribute('id',$product->id);
                    $writer->writeAttribute('available',($product->amount > 0) ? 'true' : 'false');

                    $writer->writeElement('delete','true');
                    $writer->writeElement('currencyId','UAH');
                    $writer->writeElement('categoryId',$product->category_id);
                    $writer->writeElement('quantity',$product->amount);
                    $writer->writeElement('price',number_format($product->price1,2,'.',' '));

                    $writer->writeElement('name',$product->name);

                    $writer->startElement('param');
                    $writer->writeAttribute('name','Артикул');
                    $writer->text($product->vendor_code);
                    $writer->endElement();

                    $writer->endElement();
                }
                $writer->endElement();
            }
        }

        $writer->fullEndElement();
        $writer->fullEndElement();

        $writer->endDocument();

        return ob_get_clean();
    }

    /**
     * @param $xml
     * @return bool
     */
    public function isXmlError($xml)
    {
        return is_array($xml) && array_key_exists('error',$xml);
    }
}