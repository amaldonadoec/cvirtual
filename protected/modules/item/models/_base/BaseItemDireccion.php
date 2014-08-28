<?php

/**
 * This is the model base class for the table "item_direccion".
 * DO NOT MODIFY THIS FILE! It is automatically generated by AweCrud.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ItemDireccion".
 *
 * Columns in table "item_direccion" available as properties of the model,
 * followed by relations of table "item_direccion" available as properties of the model.
 *
 * @property integer $id
 * @property double $coord_x
 * @property double $coord_y
 * @property integer $pais_id
 * @property integer $provincia_id
 * @property integer $ciudad_id
 * @property string $calle_principal
 * @property string $calle_secundaria
 * @property string $numero
 * @property string $referencia
 *
 * @property Item[] $items
 */
abstract class BaseItemDireccion extends AweActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'item_direccion';
    }

    public static function representingColumn() {
        return 'calle_principal';
    }

    public function rules() {
        return array(
            array('coord_x, coord_y', 'required'),
            array('pais_id, provincia_id, ciudad_id', 'numerical', 'integerOnly'=>true),
            array('coord_x, coord_y', 'numerical'),
            array('calle_principal, calle_secundaria, numero', 'length', 'max'=>45),
            array('referencia', 'length', 'max'=>64),
            array('pais_id, provincia_id, ciudad_id, calle_principal, calle_secundaria, numero, referencia', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, coord_x, coord_y, pais_id, provincia_id, ciudad_id, calle_principal, calle_secundaria, numero, referencia', 'safe', 'on'=>'search'),
        );
    }

    public function relations() {
        return array(
            'items' => array(self::HAS_MANY, 'Item', 'item_direccion_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'id' => Yii::t('app', 'ID'),
                'coord_x' => Yii::t('app', 'Coord X'),
                'coord_y' => Yii::t('app', 'Coord Y'),
                'pais_id' => Yii::t('app', 'Pais'),
                'provincia_id' => Yii::t('app', 'Provincia'),
                'ciudad_id' => Yii::t('app', 'Ciudad'),
                'calle_principal' => Yii::t('app', 'Calle Principal'),
                'calle_secundaria' => Yii::t('app', 'Calle Secundaria'),
                'numero' => Yii::t('app', 'Numero'),
                'referencia' => Yii::t('app', 'Referencia'),
                'items' => null,
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('coord_x', $this->coord_x);
        $criteria->compare('coord_y', $this->coord_y);
        $criteria->compare('pais_id', $this->pais_id);
        $criteria->compare('provincia_id', $this->provincia_id);
        $criteria->compare('ciudad_id', $this->ciudad_id);
        $criteria->compare('calle_principal', $this->calle_principal, true);
        $criteria->compare('calle_secundaria', $this->calle_secundaria, true);
        $criteria->compare('numero', $this->numero, true);
        $criteria->compare('referencia', $this->referencia, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors() {
        return array_merge(array(
        ), parent::behaviors());
    }
}