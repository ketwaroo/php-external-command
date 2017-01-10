<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand;

/**
 * Description of Output
 *
 * @author Yaasir Ketwaroo
 */
class Output
{

    use TraitUtils;

    protected $command;
    protected $outputString;
    protected $exitCode;

    public function __construct($command, $outputString = '', $exitCode = NULL)
    {
        $this->setCommand($command)
            ->setOutputString($outputString)
            ->setExitCode($exitCode);
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getOutputString()
    {
        return $this->outputString;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    public function setOutputString($outputString)
    {
        $this->outputString = $outputString;
        return $this;
    }

    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
        return $this;
    }

    public function __clone()
    {
        $this->__deepClone();
    }

}
