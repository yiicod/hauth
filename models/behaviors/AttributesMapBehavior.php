<?php

namespace yiicod\hauth\models\behaviors;

use CActiveRecordBehavior;
use yii\base\InvalidParamException;

/**
 * Class get and set for model Comment.
 * 
 * Magic attrs by model map congig
 * If you use extension with model map? you can use like this:
 * 'ModelMap' => array(
 *  'Comment' => array(
 *      'filed<name>' => parentId
 *  )
 * )
 * 
 * $model-><name> It is magic :)
 * 
 * For get field name:
 * 
 * $model->field<name> It is magic :)
 */
class AttributesMapBehavior extends CActiveRecordBehavior
{
    public $attributesMap = [];

    /**
     * @param type $name
     * @param type $value
     *
     * @return type
     */
    public function __set($name, $value)
    {
        if ($this->hasAttr($name)) {
            return $this->setAttr($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param type $name
     *
     * @return type
     */
    public function __get($name)
    {
        if (strpos($name, 'field') === 0 && $this->hasFieldByModelMap($name)) {
            return $this->getFieldByModelMap($name);
        }
        if ($this->hasAttr($name)) {
            return $this->getAttr($name);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @param type $name
     *
     * @return bool
     */
    public function canGetProperty($name)
    {
        if (strpos($name, 'field') === 0 && $this->hasFieldByModelMap($name)) {
            return true;
        }
        if ($this->hasAttr($name)) {
            return true;
        } else {
            return parent::canGetProperty($name);
        }
    }

    /**
     * @param type $name
     *
     * @return bool
     */
    public function canSetProperty($name)
    {
        if ($this->hasAttr($name)) {
            return true;
        } else {
            return parent::canSetProperty($name);
        }
    }

    /**
     * @param type $name
     *
     * @return type
     */
    public function getFieldByModelMap($name)
    {
        if ($this->hasFieldByModelMap($name)) {
            return $this->attributesMap[$name];
        }

        return;
    }

    /**
     * @param type $name
     *
     * @return bool
     */
    public function hasFieldByModelMap($name)
    {
        return isset($this->attributesMap[$name]);
    }

    /**
     * Returns a value indicating whether the model has an attribute with the specified name.
     *
     * @param string $name the name of the attribute
     *
     * @return bool whether the model has an attribute with the specified name.
     */
    public function hasAttr($name)
    {
        $fieldAttr = 'field'.ucfirst($name);

        return isset($this->attributesMap[$fieldAttr]) &&
            $this->getOwner()->hasAttribute($this->attributesMap[$fieldAttr]);
    }

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     *
     * @param string $name the attribute name
     *
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     *
     * @see hasAttribute()
     */
    public function getAttr($name)
    {
        $fieldAttr = 'field'.ucfirst($name);

        return $this->getOwner()->getAttribute($this->attributesMap[$fieldAttr]);
    }

    /**
     * Sets the named attribute value.
     *
     * @param string $name  the attribute name
     * @param mixed  $value the attribute value.
     *
     * @throws InvalidParamException if the named attribute does not exist.
     *
     * @see hasAttribute()
     */
    public function setAttr($name, $value)
    {
        if ($this->hasAttr($name)) {
            $fieldAttr = 'field'.ucfirst($name);
            $this->getOwner()->{$this->attributesMap[$fieldAttr]} = $value;
        } else {
            throw new СException(get_class($this).' has no attribute named "'.$name.'".', 500);
        }
    }
}
