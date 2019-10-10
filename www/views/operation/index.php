<div class="content">
<?php  
    if($type === 'consumption'){
        echo $this->render('consumption',compact('dataProvider'));
    }
    if($type === 'coming'){
        echo $this->render('coming',compact('dataProvider'));
    }
    if($type === 'all'){
        echo $this->render('all-transaction',compact('dataProvider','model'));
    }
?>
    <div class="row">
        <div class="transaction-info col-sm-12 hidden"></div>
    </div>
</div>