<?php

Yii::import('crm.models._base.BaseUsuariosAsignados');

class UsuariosAsignados extends BaseUsuariosAsignados
{
    /**
     * @return UsuariosAsignados
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function label($n = 1)
    {
        return Yii::t('app', 'UsuariosAsignados|UsuariosAsignadoses', $n);
    }

}