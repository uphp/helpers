<?php
namespace UPhp\ActionView\Exception;

use \src\UphpException;

class ViewErrorException extends UphpException
{
    public function __construct($msg){
        parent::__construct($msg, __CLASS__);
    }
}