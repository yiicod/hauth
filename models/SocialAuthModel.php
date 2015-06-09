<?php

namespace yiicod\hauth\models;

/**
 * This is the model class for table "SocialAuth".
 *
 * The followings are the available columns in table 'SocialAuth':
 * @property integer $id
 * @property integer $userId
 * @property string $provider
 * @property string $identifier
 * @property string $createDate
 */
use CActiveRecord;
use Yii;

class SocialAuthModel extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'SocialAuth';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId, provider, identifier', 'required'),
            array('userId', 'numerical', 'integerOnly' => true),
            array('provider, identifier', 'length', 'max' => 100),
            array('createDate', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, userId, provider, identifier, createDate', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'userId' => 'User',
            'provider' => 'Provider',
            'identifier' => 'Identifier',
            'createDate' => 'Create Date',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SocialAuthModel the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'AttributesMapBehavior' => array(
                'class' => 'yiicod\hauth\models\behaviors\AttributesMapBehavior',
                'attributesMap' => Yii::app()->getComponent('hauth')->modelMap['SocialAuth']
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => in_array(Yii::app()->getComponent('hauth')->modelMap['SocialAuth']['fieldCreateDate'], $this->attributeNames()) ?
                    Yii::app()->getComponent('hauth')->modelMap['SocialAuth']['fieldCreateDate'] : null,
                'updateAttribute' => null,
                'timestampExpression' => 'date("Y-m-d H:i:s")',
            )
        );
    }

}
