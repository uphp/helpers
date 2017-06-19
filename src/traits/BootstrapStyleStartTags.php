<?php
namespace UPhp\ActionView;

trait BootstrapStyleStartTags{

    public function rowStart($class = "row", $options = [])
    {
        self::$rowStarted = true;
        return "<" . self::$tagRow . " class=\"" . $class . "\">\n";
    }

    public function columnStart($class = "1", $option = [])
    {
        if (self::$rowStarted) {
            if (isset($option["class"])) {
                $class .= " " . $option["class"];
            }
            self::$columnStarted += 1;
            return "<" . self::$tagColumn . " class=\"" . self::$prefixClassColumn . $class . "\">\n";
        } else {
            throw new ViewErrorException("You don't use column() without before use rowStart() to init");
        }
    }

    public function panelStart($class = "default")
    {
        self::$panelStarted = true;
        return "<" . self::$tagPanel . " class=\"" . self::$classPanel . $class . "\">\n";
    }

    public function panelBodyStart($class = "panel-body")
    {
        if (self::$panelStarted) {
            self::$panelBodyStarted = true;
            return "<" . self::$tagPanelBody . " class=\"" . $class . "\">\n";
        } else {
            throw new ViewErrorException("You don't use panelBodyStart() without before use panelStart() to init");
        }
    }

}