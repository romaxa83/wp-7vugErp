<div class="col-xs-12">
    <div class="row">
        <div class="col-ms-3">
            Количесвтво : <div class="balance">0</div>
        </div>
    </div>
    <form method="post" action="/variant-product/save-variant-product">
        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->csrfToken?>">
<div class="row">
    <table id="variant-product" class="table-fix custom-table v3">
        <thead>
            <tr>
                <th>#</th>
                <th>Назв</th>
                <th>P1</th>
                <th>P2</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0;$i<$groupBack['countProd'];$i++){ ?>
                    <tr>
                        <td>
                            <?= $i+1 ?>
                        </td>
                        <td>
                        <?= $groupBack['product_name']; 
                    for($z=0;$z<$groupBack['countChars']/$groupBack['countProd'];$z++){
                        $key = key($groupBack['char_name']);
                        $nextKey = next($groupBack['char_name']);
                        echo '| - '.$groupBack['char_name'][$key]['name'];
                    }?>
                        </td>
                        <td>
                            <?= \yii\helpers\Html::input('float','vproduct[price1][]',$groupBack['price1'],['min'=>0]); ?>
                        </td>
                        <td>
                            <?= \yii\helpers\Html::input('float','vproduct[price2][]',$groupBack['price2'],['min'=>0]); ?>
                            <?= \yii\helpers\Html::input('text','char_value[]',$groupBack['char_value'][$i],['class'=>'hidden']); ?>
                        </td>
                        <td>
                            <?= \yii\helpers\Html::input('number','vproduct[amount][]',$groupBack['variant_amount'][$i],['min'=>0,'class'=>'variant_amount','data-previous'=>$groupBack['variant_amount'][$i]]); ?>
                        </td>
                        <td>
                            <a href="#" title="Удалить" data-id="<?= $groupBack['product_id'] ?>">
                                <i class="fa fa-trash-o delete-variant-in-create"></i>
                            </a>
                        </td>
                    </tr>
    <?php       }   ?>
                    <input name="product_id" value=<?= $groupBack['product_id'] ?> class="hidden">
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-ms-6">
        <button type="submit" class="btn btn-success col-ms-2">Создать</button>
        <a href="#" class="snap _gray back-to-create col-ms-3">Вернуться к форме</a>
    </div>
</div>
    </form>
</div>