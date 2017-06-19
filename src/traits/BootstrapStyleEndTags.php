<?php
namespace UPhp\ActionView;

use \UPhp\ActionView\Exception\ViewErrorException;

trait BootstrapStyleEndTags{

    public function rowEnd()
    {
        self::$rowStarted = false;
        return $this->endDiv();
    }    

    public function columnEnd()
    {
        if (self::$columnStarted > 0) {
            self::$columnStarted -= 1;
            return $this->endDiv();
        } else {
            throw new ViewErrorException("Bootstrap Column not started");
        }
    }

    public function panelEnd()
    {
        if (self::$panelStarted) {
            self::$panelStarted = false;
            return $this->endDiv();
        } else {
            throw new ViewErrorException("Bootstrap Panel not started");
        }
    }

    public function panelBodyEnd()
    {
        if (self::$panelBodyStarted) {
            self::$panelBodyStarted = false;
            return $this->endDiv();
        } else {
            throw new ViewErrorException("Bootstrap Panel Body not started");
        }
    }

}