<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'hashed_password')->textInput(['maxlength' => true])->label('Password') ?>

    <label>Confirm Password</label> <br>
    <div style='color:red'>
        <?=$errors?>
    </div>
    <input class="confirm" type="text" name="confirm_password" placeholder="Повторите пароль">
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
