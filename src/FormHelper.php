<?php
namespace UPhp\ActionView;

use src\Inflection;

class FormHelper{

    public static $object;
    public static $fieldsFor;
    /**
     * @param $object
     * @param array $htmlOptions
     */
    public static function formFor($object, string $action="", string $method="", Array $htmlOptions=[])
    {
        $className = self::getClassName(get_class($object));

        $htmlProperties = array_keys($htmlOptions);
        if (! in_array("id", $htmlProperties)) $htmlOptions["id"] = "form_" . Inflection::tableize($className);
        if (! in_array("action", $htmlProperties)) $htmlOptions["action"] = $action;
        if (! in_array("method", $htmlProperties)) $htmlOptions["method"] = $method;

        $htmlPropertiesSerialized = self::serializeHtmlOptions($htmlOptions);
        $formHtml = "<form " . $htmlPropertiesSerialized . ">";
        self::$object = $object;
        return $formHtml;
    }

    /**
     * @param string $action
     * @param string $method
     * @param array $htmlOptions
     * @return string
     */
    public static function formTag(string $action="", string $method="", Array $htmlOptions=[])
    {
        $htmlProperties = array_keys($htmlOptions);
        if (! in_array("action", $htmlProperties)) $htmlOptions["action"] = $action;
        if (! in_array("method", $htmlProperties)) $htmlOptions["method"] = $method;

        $htmlPropertiesSerialized = self::serializeHtmlOptions($htmlOptions);
        $formHtml = "<form " . $htmlPropertiesSerialized . ">";
        return $formHtml;
    }

    /**
     * @return string
     */
    public static function endForm()
    {
        self::$object = null;
        return "</form>";
    }

    /**
     * @param string $property
     * @param array $htmlOptions
     * @return string
     */
    public static function textField(string $property, Array $htmlOptions=[])
    {
        return self::inputField("text", $property, $htmlOptions);
    }

    /**
     * @param string $property
     * @param array $htmlOptions
     * @return string
     */
    public static function hiddenField(string $property, Array $htmlOptions=[])
    {
        return self::inputField("hidden", $property, $htmlOptions);
    }

    public static function select(string $property, Array $htmlOptions=[])
    {

    }

    /**
     * @param string $property
     * @param array $htmlOptions
     * @return string
     */
    public static function textFieldTag(string $property, Array $htmlOptions=[])
    {
        $htmlProperties = array_keys($htmlOptions);
        if (! in_array("id", $htmlProperties)) $htmlOptions["id"] = "txt" . "_" . $property;
        if (empty(self::$fieldsFor)) {
            $htmlOptions["name"] = $property;
        } else {
            $htmlOptions["name"] = self::$fieldsFor . "[" . $property . "]";
        }

        $htmlPropertiesSerialized = self::serializeHtmlOptions($htmlOptions);
        $inputHtml = "<input type=\"text\" " . $htmlPropertiesSerialized . " />";
        return $inputHtml;
    }

    /**
     * @param string $arrayFieldName
     */
    public static function fieldsFor(string $arrayFieldName)
    {
        self::$fieldsFor = $arrayFieldName;
    }

    /**
     *
     */
    public static function endFieldsFor()
    {
        self::$fieldsFor = null;
    }

    /**
     * @param string $label
     * @param array $htmlOptions
     */
    public static function submit(string $label, Array $htmlOptions=[])
    {
        //TODO
    }

    /**
     * @param string $type
     * @param string $label
     * @param array $htmlOptions
     * @return string
     */
    public static function button(string $type, string $label, Array $htmlOptions=[])
    {
        $htmlProperties = array_keys($htmlOptions);
        if (! in_array("type", $htmlProperties)) $htmlOptions["type"] = $type;

        $htmlPropertiesSerialized = self::serializeHtmlOptions($htmlOptions);
        $htmlButton = "<button type=\"" . $htmlOptions["type"] . "\" " . $htmlPropertiesSerialized . ">" . $label . "</button>";
        return $htmlButton;
    }

    /**
     * @param string $type
     */
    private static function inputField(string $type, string $property, Array $htmlOptions=[])
    {
        $className = self::getClassName(get_class(self::$object));

        $htmlProperties = array_keys($htmlOptions);
        if (! in_array("id", $htmlProperties)) $htmlOptions["id"] = Inflection::tableize($className) . "_" . $property;
        if (! in_array("name", $htmlProperties)) $htmlOptions["name"] = Inflection::tableize($className) . "[" . $property . "]";
        if (! in_array("value", $htmlProperties)) $htmlOptions["value"] = self::$object->$property()->value();

        $htmlPropertiesSerialized = self::serializeHtmlOptions($htmlOptions);
        $inputHtml = "<input type=\"" . $type . "\" " . $htmlPropertiesSerialized . " />";
        return $inputHtml;
    }

    /**
     * @param string $class
     * @return array|mixed
     */
    private static function getClassName(string $class)
    {
        $className = explode("\\", $class);
        $className = end($className);
        return $className;
    }

    /**
     * @param string $className
     * @param array $htmlOptions
     * @return string
     */
    private static function serializeHtmlOptions(Array $htmlOptions)
    {
        $serialized = "";
        foreach ($htmlOptions as $property => $value) {
            if ($value == false) continue;
            $serialized .= $property . "=" . "\"" . $value . "\" ";
        }
        return $serialized;
    }

}