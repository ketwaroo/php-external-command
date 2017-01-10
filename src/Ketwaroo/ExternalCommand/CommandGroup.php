<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand;

use Ketwaroo\ExternalCommand\CommandPreset;

/**
 * Description of CommandGroup
 *
 * @author Yaasir Ketwaroo
 */
class CommandGroup
{
use TraitUtils;
    protected $commands = [];

    /**
     * 
     * @param string $name
     * @param array $params
     * @return Output
     */
    protected function executeCommand($name, array $params = [])
    {
        $cmd = $this->getCommand($name);

        foreach ($params as $k => $v)
        {
            $cmd->getOptionGroup()->addOption($k, $v);
        }

        return $cmd->execute()
                ->getOutput();
    }

    protected function getCommand($name)
    {

        if (!isset($this->commands[$name]))
        {
            $this->commands[$name] = $this->newCommand($name);
        }
        return $this->commands[$name];
    }

    /**
     * 
     * @param string $name
     * @param CommandPreset $preset
     * @return Command
     * @throws \RuntimeException
     */
    protected function newCommand($name)
    {
        $class = $this->getCommandClassPrefix($name);

        if (!is_subclass_of($class, __NAMESPACE__ . '\\Command'))
        {
            throw new \RuntimeException("[$class] is not a subclass of Command.");
        }

        $cmd = new $class();

        return $cmd;
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    protected function getCommandClassPrefix($name)
    {
        return get_called_class() . '\\' . $name . 'Command';
    }

    protected function getCommandPresetClassPrefix($name)
    {
        return get_called_class() . '\\Preset\\' . $name . 'Preset';
    }

    /**
     * 
     * @param type $name
     * @param Command $command
     * @return CommandPreset
     */
    public function getPreset($name, Command $command = null)
    {
        $cls = $this->getCommandPresetClassPrefix($name);
        return new $cls($command);
    }

    public function __clone()
    {
        $this->__deepClone();
    }

}
