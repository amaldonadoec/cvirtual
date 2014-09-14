<?php
/** @var EntidadController $this */
/** @var Entidad $model */
$this->menu = array(
    array('label' => Yii::t('AweCrud.app', 'Create'), 'icon' => 'plus', 'url' => array('create'),
    //'visible' => (Util::checkAccess(array('action_incidenciaPrioridad_create')))
    ),
);
?>
<div id="flashMsg"  class="flash-messages">

</div> 
<div class="widget blue">
    <div class="widget-title">
        <h4> <i class="icon-fire-extinguisher"></i> <?php echo Yii::t('AweCrud.app', 'Manage') ?> <?php echo Entidad::label(2) ?> </h4>
        <span class="tools">
            <a href="javascript:;" class="icon-chevron-down"></a>
            <!--a href="javascript:;" class="icon-remove"></a-->
        </span>
    </div>
    <div class="widget-body">

        <?php
        $this->widget('bootstrap.widgets.TbGridView', array(
            'id' => 'entidad-grid',
            'type' => 'striped bordered hover advance',
            'dataProvider' => $model->search(),
            'columns' => array(
                array(
                    'name' => 'nombre',
                    'value' => 'CHtml::link($data->nombre, Yii::app()->createUrl("crm/entidad/view",array("id"=>$data->id)))',
                    'type' => 'raw',
                ),
                'razon_social',
                'documento',
                'website',
                'raking',
                'telefono',
                /*
                  'celular',
                  'email',
                  'max_entidad',
                  array(
                  'name' => 'estado',
                  'filter' => array('ACTIVO'=>'ACTIVO','INACTIVO'=>'INACTIVO',),
                  ),
                  'matriz',
                  array(
                  'name' => 'categoria_id',
                  'value' => 'isset($data->categoria) ? $data->categoria : null',
                  'filter' => CHtml::listData(Categoria::model()->findAll(), 'id', Categoria::representingColumn()),
                  ),
                  array(
                  'name' => 'industria_id',
                  'value' => 'isset($data->industria) ? $data->industria : null',
                  'filter' => CHtml::listData(Industria::model()->findAll(), 'id', Industria::representingColumn()),
                  ),
                  array(
                  'name' => 'entidad_id',
                  'value' => 'isset($data->entidadFotos) ? $data->entidadFotos : null',
                  'filter' => CHtml::listData(EntidadFoto::model()->findAll(), 'id', EntidadFoto::representingColumn()),
                  ),
                  'max_foto',
                 */
                array(
                    'class' => 'CButtonColumn',
                    'template' => '{update} {delete}',
                    'afterDelete' => 'function(link,success,data){ 
                    if(success) {
                         $("#flashMsg").empty();
                         $("#flashMsg").css("display","");
                         $("#flashMsg").html(data).animate({opacity: 1.0}, 5500).fadeOut("slow");
                    }
                    }',
                    'buttons' => array(
                        'update' => array(
                            'label' => '<button class="btn btn-primary"><i class="icon-pencil"></i></button>',
                            'options' => array('title' => 'Actualizar'),
                            'imageUrl' => false,
                        //'visible' => 'Util::checkAccess(array("action_incidenciaPrioridad_update"))'
                        ),
                        'delete' => array(
                            'label' => '<button class="btn btn-danger"><i class="icon-trash"></i></button>',
                            'options' => array('title' => 'Eliminar'),
                            'imageUrl' => false,
                        //'visible' => 'Util::checkAccess(array("action_incidenciaPrioridad_delete"))'
                        ),
                    ),
                    'htmlOptions' => array(
                        'width' => '80px'
                    )
                ),
            ),
        ));
        ?>
    </div>
</div>