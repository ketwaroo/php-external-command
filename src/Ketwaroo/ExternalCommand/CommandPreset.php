<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand;

/**
 * Command decorator.
 *
 * @author Yaasir Ketwaroo
 */
abstract class CommandPreset
{
use TraitUtils;
    protected $presetApplied = false;

    /**
     *
     * @var Command 
     */
    protected $command = null;

    public function __construct(Command $command = null)
    {
        $this->command = $command;
    }

    /**
     * 
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * 
     * @param Command $command
     * @return static
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
        $this->presetApplied = false;
        return $this;
    }

    /**
     * Apply preset data to command.
     */
    abstract protected function applyPreset();

    /**
     * 
     * @return Command
     */
    public function apply()
    {
        if (!($this->presetApplied))
        {
            $this->applyPreset();
            $this->presetApplied = true;
        }
        return $this->command;
    }
    public function __clone()
    {
        $this->__deepClone();
    }
}
