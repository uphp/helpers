<?php
namespace UPhp\ActionView;

use \UPhp\ActionView\Exception\ViewErrorException;

require_once("traits/BootstrapStyleStartTags.php");
require_once("traits/BootstrapStyleEndTags.php");

class BootstrapStyle{

    use BootstrapStyleStartTags;
    use BootstrapStyleEndTags;

    //controls
    private static $rowStarted = false;
    private static $columnStarted = 0;
    private static $panelStarted = false;
    private static $panelBodyStarted = false;
    //configure
    private static $tagRow = "div";
    private static $tagColumn = "div";
    private static $tagPanel = "div";
    private static $tagPanelBody = "div";
    private static $prefixClassColumn = "col-md-";
    private static $classPanel = "panel panel-";

    public function configure($options=[])
    {
        if (isset($options["tagRow"])) self::$tagRow = $options["tagRow"];
        if (isset($options["tagColumn"])) self::$tagColumn = $options["tagColumn"];
        if (isset($options["tagPanel"])) self::$tagPanel = $options["tagPanel"];
        if (isset($options["tagPanelBody"])) self::$tagColumn = $options["tagPanelBody"];
        if (isset($options["prefixClassColumn"])) self::$prefixClassColumn = $options["prefixClassColumn"];
        if (isset($options["classPanel"])) self::$classPanel = $options["classPanel"];
    }

    private function endDiv()
    {
        return "</div>\n";
    }
}