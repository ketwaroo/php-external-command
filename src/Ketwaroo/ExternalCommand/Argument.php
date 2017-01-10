<?php

namespace Ketwaroo\ExternalCommand;

/**
 * Plain argument
 *
 * @author Administrator
 */
class Argument extends Option
{

    public function __construct($value)
    {
        parent::__construct('', $value, '', '');
    }
    
    public function getIsRepeatable()
    {
        return true;
    }
}
