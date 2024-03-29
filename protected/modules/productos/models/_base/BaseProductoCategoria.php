<?php

/**
 * This is the model base class for the table "producto_categoria".
 * DO NOT MODIFY THIS FILE! It is automatically generated by AweCrud.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ProductoCategoria".
 *
 * Columns in table "producto_categoria" available as properties of the model,
 * followed by relations of table "producto_categoria" available as properties of the model.
 *
 * @property integer $id
 * @property string $nombre
 * @property string $descripcion
 * @property string $estado
 *
 * @property ProductoSubcategoria[] $productoSubcategorias
 */
abstract class BaseProductoCategoria extends AweActiveRecord {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'producto_categoria';
    }

    public static function representingColumn() {
        return 'nombre';
    }

    public function rules() {
        return array(
            array('nombre', 'required'),
            array('nombre', 'length', 'max'=>45),
            array('estado', 'length', 'max'=>8),
            array('descripcion', 'safe'),
            array('estado', 'in', 'range' => array('ACTIVO','INACTIVO')), // enum,
            array('descripcion, estado', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, nombre, descripcion, estado', 'safe', 'on'=>'search'),
        );
    }

    public function relations() {
        return array(
            'productoSubcategorias' => array(self::HAS_MANY, 'ProductoSubcategoria', 'categoria_producto_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'id' => Yii::t('app', 'ID'),
                'nombre' => Yii::t('app', 'Nombre'),
                'descripcion' => Yii::t('app', 'Descripcion'),
                'estado' => Yii::t('app', 'Estado'),
                'productoSubcategorias' => null,
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('nombre', $this->nombre, true);
        $criteria->compare('descripcion', $this->descripcion, true);
        $criteria->compare('estado', $this->estado, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors() {
        return array_merge(array(
        ), parent::behaviors());
    }
}