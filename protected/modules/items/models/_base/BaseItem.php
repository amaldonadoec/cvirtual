<?php

/**
 * This is the model base class for the table "item".
 * DO NOT MODIFY THIS FILE! It is automatically generated by AweCrud.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Item".
 *
 * Columns in table "item" available as properties of the model,
 * followed by relations of table "item" available as properties of the model.
 *
 * @property integer $id
 * @property integer $num_foto
 * @property integer $entidad_id
 * @property string $descripcion
 *
 * @property ItemFoto[] $itemFotos
 */
abstract class BaseItem extends AweActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'item';
    }

    public static function representingColumn() {
        return 'descripcion';
    }

    public function rules() {
        return array(
            array('num_foto, entidad_id, descripcion', 'required'),
            array('num_foto, entidad_id', 'numerical', 'integerOnly'=>true),
            array('descripcion', 'length', 'max'=>45),
            array('id, num_foto, entidad_id, descripcion', 'safe', 'on'=>'search'),
        );
    }

    public function relations() {
        return array(
            'itemFotos' => array(self::HAS_MANY, 'ItemFoto', 'item_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'id' => Yii::t('app', 'ID'),
                'num_foto' => Yii::t('app', 'Num Foto'),
                'entidad_id' => Yii::t('app', 'Entidad'),
                'descripcion' => Yii::t('app', 'Descripcion'),
                'itemFotos' => null,
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('num_foto', $this->num_foto);
        $criteria->compare('entidad_id', $this->entidad_id);
        $criteria->compare('descripcion', $this->descripcion, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors() {
        return array_merge(array(
        ), parent::behaviors());
    }
}