<?php

namespace app\models;

use yii\base\Model;

class CsvForm extends Model 
{
    public $file;
    public function rules() {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'csv'],
        ];
    }
    public function attributeLabels() {
        return [
            'file' => 'Select File',
        ];
    }
}
