<?php

namespace DatabaseJson\Classes\Helpers;

use DatabaseJson\Classes\DatabaseJsonException;
use DatabaseJson\Classes\Relation;

class Validate
{

    /**
     * Name of table
     * @var string
     */
    private $name;

    /**
     * Table name
     * @param string $name
     * @return Validate
     */
    public static function table($name)
    {
        $validate       = new Validate();
        $validate->name = $name;
        return $validate;
    }

    /**
     * Checking that field type is numeric
     * @param string $type
     * @return boolean
     */
    public static function isNumeric($type)
    {
        $defined = array('integer', 'double');

        if (in_array($type, $defined)) {
            return true;
        }

        return false;
    }

    /**
     * Checking that types from array matching with [boolean, integer, string, double, array]
     * @param array $types Indexed array
     * @return bool
     * @throws DatabaseJsonException
     */
    public static function types(array $types)
    {
        $defined = array('boolean', 'integer', 'string', 'double', 'array');
        $diff    = array_diff($types, $defined);

        if (empty($diff)) {
            return true;
        }
        throw new DatabaseJsonException('Wrong types: "' . implode(', ', $diff) . '". Available "boolean, integer, string, double, array"');
    }

    /**
     * Delete ID field from arrays
     * @param array $fields
     * @return array Fields without ID
     */
    public static function filter(array $fields)
    {
        if (array_values($fields) === $fields) {
            if (($key = array_search('id', $fields)) !== false) {
                unset($fields[$key]);
            }
        } else {
            unset($fields['id']);
        }
        return $fields;
    }

    /**
     * Change keys and values case to lower
     * @param array $array
     * @return array
     */
    public static function arrToLower(array $array)
    {
        $array = array_change_key_case($array);
        $array = array_map('strtolower', $array);

        return $array;
    }

    /**
     * Checking that typed fields really exist in table
     * @param array $fields Indexed array
     * @return boolean
     * @throws DatabaseJsonException If field(s) does not exist
     */
    public function fields(array $fields)
    {
        $fields = self::filter($fields);
        $diff   = array_diff($fields, Config::table($this->name)->fields());

        if (empty($diff)) {
            return true;
        }
        throw new DatabaseJsonException('Field(s) "' . implode(', ', $diff) . '" does not exists in table "' . $this->name . '"');
    }

    /**
     * Checking that typed field really exist in table
     * @param string $name
     * @return boolean
     * @throws DatabaseJsonException If field does not exist
     */
    public function field($name)
    {
        if (in_array($name, Config::table($this->name)->fields())) {
            return true;
        }
        throw new DatabaseJsonException('Field ' . $name . ' does not exists in table "' . $this->name . '"');
    }

    /**
     * Checking that Table and Config exists and throw exceptions if not
     * @return boolean
     * @throws DatabaseJsonException
     */
    public function exists()
    {
        if (!Data::table($this->name)->exists()) {
            throw new DatabaseJsonException('Table "' . $this->name . '" does not exists');
        }

        if (!Config::table($this->name)->exists()) {
            throw new DatabaseJsonException('Config "' . $this->name . '" does not exists');
        }

        return true;
    }

    /**
     * Checking that typed field have correct type of value
     * @param string $name
     * @param mixed $value
     * @return boolean
     * @throws DatabaseJsonException If type is wrong
     */
    public function type($name, $value)
    {
        $schema = Config::table($this->name)->schema();
        if (array_key_exists($name, $schema) && $schema[$name] == gettype($value)) {
            return true;
        }

        throw new DatabaseJsonException('Wrong data type');
    }

    /**
     * Checking that relation between tables exists
     * @param string $local local table
     * @param string $foreign related table
     * @return bool relation exists
     * @throws DatabaseJsonException
     */
    public static function relation($local, $foreign)
    {
        $relations = Config::table($local)->relations();
        if (isset($relations->{$foreign})) {
            return true;
        }

        throw new DatabaseJsonException('Relation "' . $local . '" to "' . $foreign . '" doesn\'t exist');
    }

    /**
     * Checking that relation type is correct
     * @param string $type
     * @return bool relation type
     * @throws DatabaseJsonException Wrong relation type
     */
    public static function relationType($type)
    {
        if (in_array($type, Relation::relations())) {
            return true;
        }

        throw new DatabaseJsonException('Wrong relation type');
    }

}
