<?php

$formA = $this->beginWidget('ext.AweCrud.components.AweActiveForm', array(
    'id' => 'entidad-foto-form',
    'type' => 'vertical',
//    'action' => Yii::app()->createUrl('/crm/entidadFoto/SaveImagen'),
        'action' =>  Yii::app()->createUrl('/crm/entidadFoto/SaveImagen', array('id' => $model->id)),

    'enableAjaxValidation' => true,
    'clientOptions' => array('validateOnSubmit' => false, 'validateOnChange' => false,),
    'enableClientValidation' => false,
        ));
?>
<?php

$formA->widget('xupload.XUpload', array(
    'model' => $archivos,
    'url' => CController::createUrl('/crm/entidadFoto/uploadTmp'),
    'htmlOptions' => array('id' => 'archivo-form'),
    'attribute' => 'file',
//    'pictures' => !$model->isNewRecord ? $model->inmuebleImagens : array(),
    'multiple' => true,
    'autoUpload' => true,
    'options' => array(
        'maxFileSize' => 2000000,
        'acceptFileTypes' => 'js:/(\.|\/)(gif|jpe?g|png)$/i',
    )
));
?>
<?php $this->endWidget(); ?>

<?php

Util::checkAccess(array("action_entidadFoto_saveImagen")) ?
                $this->widget('bootstrap.widgets.TbButton', array(
                    'type' => 'success',
                    'icon' => 'ok',
                    'label' => Yii::t('AweCrud.app', 'Agregar Fotos') ,
                    'htmlOptions' => array(
                        'onclick' => 'js:guardarImagen()',
                    ),
                )) : '';
?>

