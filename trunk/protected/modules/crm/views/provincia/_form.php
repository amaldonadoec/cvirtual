<?php
/** @var ProvinciaController $this */
/** @var Provincia $model */
/** @var AweActiveForm $form */
$form = $this->beginWidget('ext.AweCrud.components.AweActiveForm', array(
    'type' => 'horizontal',
    'id' => 'provincia-form',
    'enableAjaxValidation' => true,
    'clientOptions' => array('validateOnSubmit' => true, 'validateOnChange' => false,),
    'enableClientValidation' => false,
        ));
?>
<div class="widget blue">
    <div class="widget-title">
        <h4><i class="icon-plus"></i><?php echo Yii::t('AweCrud.app', $model->isNewRecord ? 'Create' : 'Update') . ' ' . Provincia::label(1); ?></h4>
        <span class="tools">
            <a href="javascript:;" class="icon-chevron-down"></a>
            <!--a href="javascript:;" class="icon-remove"></a-->
        </span>
    </div>
    <div class="widget-body">



        <div class="control-group" >
            <!-- drop de region -->
            <label class="control-label"> <?php echo $form->labelEx($model, 'pais_id') ?></label>
            <div class="controls">
                <?php
                $paises = Pais::model()->getInscritasPaises();
                $lista_paises = !(count($paises) == 0) ? array(0 => '- Pais -') + CHtml::listData($paises, 'id', 'nombre') : array(0 => '- Ninguna -');
                $this->widget(
                        'ext.bootstrap.widgets.TbSelect2', array(
                    'asDropDownList' => TRUE,
                    'model' => $model,
                    'attribute' => 'pais_id',
                    'data' => $lista_paises,
                    'events' => array("event_name" => "Javascript code for handler"),
                    'options' => array(
                        'placeholder' => 'Seleccione Un Pais!',
                        'width' => '25%',
                    )
                        )
                );
                ?>

                <?php echo $form->error($model, 'pais_id'); ?>

            </div>
        </div>
        <?php echo $form->textFieldRow($model, 'nombre', array('maxlength' => 45)) ?>

        <?php // echo $form->dropDownListRow($model, 'pais_id', array('' => ' -- Seleccione -- ') + CHtml::listData(Pais::model()->findAll(), 'id', Pais::representingColumn()))  ?>




        <div class="form-actions">
            <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type' => 'success',
                'label' => $model->isNewRecord ? Yii::t('AweCrud.app', 'Create') : Yii::t('AweCrud.app', 'Save'),
            ));
            ?>
            <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'label' => Yii::t('AweCrud.app', 'Cancel'),
                'htmlOptions' => array('onclick' => 'javascript:history.go(-1)')
            ));
            ?>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>