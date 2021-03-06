<?php

/* @var $this yii\web\View */
/* @var $users array */
/* @var $user \app\models\User */
/* @var $codeGenerate null|string */

use yii\helpers\Html;
use yii\bootstrap4\Modal;

\app\assets\DrawAsset::register($this);

$this->title = 'Рисование';
?>
<div class="row">
    <div class="col-sm-1 text-center">
        <span id="blackTd" class="drawActiveBlock"></span>
        <span id="whiteTd" class="drawActiveBlock"></span>

        <br><br><br>
        <span class="btn btn-success" id="saveMainField" title="Сохранить картинку">Сох.</span><br><br>
        <span class="btn btn-info" id="loadImageTemplate" title="Загрузить картинку">Заг.</span>
    </div>
    <div class="col-sm-11" id="screenDrawLeft">

    </div>
    <hr class="col-sm-12">
    <div class="col-sm-3">
        <label>Размер пикселя при просмотре</label>
        <?=Html::textInput('', 2, ['class' => 'form-control', 'id' => 'prevSize'])?>
        <hr>
        <span class="btn btn-warning" id="prevSmallImage">Предпросмотр</span>
    </div>
    <div class="col-sm-3">
        <div id="screenDrawLeftResult"></div>
    </div>
    <div class="col-sm-2">
        <label>Устройствo</label><br>
        <?=Html::dropDownList('', '', [
            1 => 'Синий',
            2 => 'Левый',
            3 => 'Правый',
        ], ['id'=>"deviceId", 'class' => 'form-control'])?>

        <label>Контраст</label><br>
        <?=Html::input('range', '', '', ['min'=>"100", 'max'=>"170", 'step'=>"1", 'id'=>"contrast"])?>

        <label>Яркость подсветки</label><br>
        <?=Html::input('range', '', '', ['min'=>"0", 'max'=>"1023", 'id'=>"bluePwm"])?>
        <label>
            <?=Html::checkbox('', true, ['id' => 'blue'])?>
            Включить подсветку
        </label><br>
        <?=Html::input('hidden', '', Yii::$app->request->get('id'), ['min'=>"0", 'max'=>"1023", 'id'=>"priceId"])?>
        <span class="btn btn-success" id="savePrice">Отправить</span>

        <span class="btn btn-danger btn-sm" id="syncBase">Синхронизация баз</span>
    </div>
    <div class="col-sm-4 text-small" id="logField">

    </div>
</div>


<script>
    let urlSaveMainTemplate = "<?=\yii\helpers\Url::to(['save-main-template'])?>";
    let urlLoadMainTemplate = "<?=\yii\helpers\Url::to(['load-main-template'])?>";
    let urlDeleteMainTemplate = "<?=\yii\helpers\Url::to(['delete-main-template'])?>";
    let urlSavePrice = "<?=\yii\helpers\Url::to(['save-price'])?>";
    let urlShowPrice = "<?=\yii\helpers\Url::to(['show-price'])?>";
    let urlSyncBase = "<?=\yii\helpers\Url::to(['sync-base'])?>";
    let codeGenerate = "";
    <?php if($codeGenerate):?>
    codeGenerate = '<?=$codeGenerate?>';
    <?php endif;?>

</script>

<?php
Modal::begin([
        'id' => 'modalSave'
]);
?>
    <label>Имя</label>
    <?=Html::textInput('', '', ['class' => 'form-control', 'id' => 'templateName'])?>
    <hr>
    <span id="saveTemplate" class="btn btn-info">Сохранить</span>
<?php
Modal::end()
?>

<?php
Modal::begin([
    'id' => 'modalLoadTemplate',
    'size' => Modal::SIZE_LARGE
]);
?>
<label>
    <?=Html::checkbox('', true, ['id' => 'onlyMy', 'data-user' => Yii::$app->user->getId()])?> Только мои
</label>
<div id="templatesAllUsers" style="display: none">
    <hr>
    <?php foreach ($users as $user): ?>
        <label>
            <?=Html::checkbox('', false, ['class' => 'templateUsers', 'data-user' => $user->id])?>
            <?=$user->name?> (<?=$user->username?>)
        </label>
    <?php endforeach; ?>
</div>

<div id="templatesList"></div>
<?php
Modal::end()
?>
