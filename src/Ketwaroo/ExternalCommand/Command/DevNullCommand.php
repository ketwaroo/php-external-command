<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand\Command;
use Ketwaroo\ExternalCommand\Command;
/**
 * Used for pipig to /dev/mill
 *
 * @author Yaasir Ketwaroo
 */
class DevNullCommand extends Command
{
    protected function getCommand()
    {
        return '/dev/null';
    }

    protected function setupDefaults()
    {
        
    }

//put your code here
}
