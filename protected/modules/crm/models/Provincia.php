<?php

Yii::import('crm.models._base.BaseProvincia');

class Provincia extends BaseProvincia {

    /**
     * @return Provincia
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function label($n = 1) {
        return Yii::t('app', 'Provincia|Provincias', $n);
    }

    public function rules() {
        return array_merge(parent::rules(), array(
            array('nombre, pais_id', 'required'),
//            array('ciudad_id, canton_id, provincia_id, region_id ', 'required'),
//            array('region_id', 'numerical',
//                'integerOnly' => true,
//                'min' => 1,
//                'tooSmall' => 'Elija una region  por favor.',
//            ),
            array('pais_id', 'numerical',
                'integerOnly' => true,
                'min' => 1,
                'tooSmall' => 'Elija un Pais  por favor.',
            ),
//            array('canton_id', 'numerical',
//                'integerOnly' => true,
//                'min' => 1,
//                'tooSmall' => 'Elija un canton  por favor.',
//            ),
//            array('ciudad_id', 'numerical',
//                'integerOnly' => true,
//                'min' => 1,
//                'tooSmall' => 'Elija un ciudad por favor.',
//            ),
        ));
    }

    /**
     * @author Miguel Alba <malba@tradesystem.com.ec>
     * @param type $pais_id
     * @return type Obtener todas las provincias de cada pais con su id pais
     * Utilizacion en el script de ubicacino en form crear Ciudad
     */
    public function getProvinciasPais($pais_id) {

//        SELECT pr.id,pr.nombre FROM provincia pr
//where pr.region_id=7
//order by pr.nombre
//;
        $command = Yii::app()->db->createCommand()
                ->select("pro.id, pro.nombre")
                ->from("provincia pro")
                ->where("pro.pais_id = :pais_id");
        $command->bindValues(array(
            ':pais_id' => $pais_id,
        ));
        $command->order("pro.nombre");
        $result = $command->queryAll();
        return ($result);
    }

    public function getListSelect2Provincia($search_value = null, $pais_id) {
        $command = New CDbCommand(Yii::app()->db);
        $command->select(array(
            "t.id as id",
            "t.nombre as text",
        ));
        $command->from("{$this->tableName()} as t");
        $command->join("pais p", "p.id=t.pais_id");
         $command->andWhere("p.id = '$pais_id'");
        if ($search_value) {
            $command->andWhere("t.nombre like '$search_value%'");
        }
        $command->limit(10);
        return $command->queryAll();
    }

}
