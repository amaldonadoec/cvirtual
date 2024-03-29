<?php

Yii::import('gestion.models._base.BaseUsuariosAsignados');

class UsuariosAsignados extends BaseUsuariosAsignados {

    /**
     * @return UsuariosAsignados
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function label($n = 1) {
        return Yii::t('app', 'UsuariosAsignados|UsuariosAsignadoses', $n);
    }

    public function searchAsignados() {
        $criteria = new CDbCriteria;

        $criteria->compare('iduser', $this->iduser);
        $criteria->compare('iduser_asignado', $this->iduser_asignado);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
