<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand;

use Ketwaroo\ExternalCommand\CommandPreset;
use Ketwaroo\ExternalCommand\OptionGroup;

/**
 * Description of AbstractExternalCommand
 *
 * @author Yaasir Ketwaroo
 */
abstract class Command
{

    use TraitUtils;

    const DEFAULT_INPUT_GROUP_NAME = 'global';
    const piped_inout              = '-';
    const pipe_vertical            = '|';
    const pipe_file                = '>';
    const pipe_file_concat         = '>>';
    const pipe_stdout_stderr       = '2>&1';

    protected $joiner = ' ';
    protected $pipes  = [];

    /**
     *
     * @var Output 
     */
    protected $output;

    /**
     *
     * @var OptionGroup[] 
     */
    protected $optionGroups = [];

    public function __construct($name = NULL)
    {
        $this->addOptionGroup(static::DEFAULT_INPUT_GROUP_NAME);
        $this->setupDefaults();
    }

    abstract protected function setupDefaults();

    abstract protected function getCommand();

    /**
     * 
     * @return \Ketwaroo\ExternalCommand\Command
     */
    public function execute()
    {
        $optGroups = $this->getOptionGroups();

        foreach ($optGroups as $grp)
        {
            $grp->verifyRequiredOptions();
        }

        $return  = null;
        $command = $this->buildCommandString();
        ob_start();
        passthru($command . ' ' . static::pipe_stdout_stderr, $return);
        $output  = ob_get_clean();

        $this->setOutput(new Output($command, $output, $return));

        return $this;
    }

    /**
     * 
     * @return string default '{command}{joiner}{options}'
     */
    protected function getCommandStringFormatter()
    {
        return '{command}{options}{piped_commands}';
    }

    /**
     * 
     * @return array
     */
    protected function getCommandStringTranslationMap()
    {
        return [
            '{command}'        => $this->getCommand(),
            '{joiner}'         => $this->getJoiner(),
            '{options}'        => $this->getDefaultOptionGroup(),
            '{piped_commands}' => $this->renderPipedCommands(),
        ];
    }

    /**
     * 
     * @return string
     */
    public function buildCommandString()
    {
        return $this->formatString(
                $this->getCommandStringFormatter()
                , $this->getCommandStringTranslationMap()
        );
    }

    public function __toString()
    {
        return $this->buildCommandString();
    }

    /**
     * 
     * @param type $name
     * @param type $requiredOptions
     * @param type $defaultOptionPrefix
     * @param type $defaultOptionJoiner
     * @param type $joiner
     * @return \Ketwaroo\ExternalCommand\OptionGroup
     */
    protected function addOptionGroup($name, $requiredOptions = [], $defaultOptionPrefix = '--', $defaultOptionJoiner = ' ', $joiner = ' ')
    {

        if ($name instanceof OptionGroup)
        {
            $this->optionGroups[$name->getName()] = $name;
            return $this->optionGroups[$name->getName()];
        }
        else
        {

            $this->optionGroups[$name] = $this->newOptionGroup($name, $requiredOptions, $defaultOptionPrefix, $defaultOptionJoiner, $joiner);
            return $this->optionGroups[$name];
        }
    }

    /**
     * 
     * @param type $param
     */
    public function replaceOptionGroup(OptionGroup $optionGroup)
    {
        $cloneGrp                                 = clone $optionGroup;
        $this->optionGroups[$cloneGrp->getName()] = $cloneGrp;
    }

    /**
     * 
     * @param type $name
     * @param type $requiredOptions
     * @param type $defaultOptionPrefix
     * @param type $defaultOptionJoiner
     * @param type $joiner
     * @return \Ketwaroo\ExternalCommand\OptionGroup
     */
    protected function newOptionGroup($name, $requiredOptions = [], $defaultOptionPrefix = '--', $defaultOptionJoiner = ' ', $joiner = ' ')
    {
        $optGrp = $this->setupOptionGroup(new OptionGroup($name), $requiredOptions, $defaultOptionPrefix, $defaultOptionJoiner, $joiner);

        return $optGrp;
    }

    protected function setupOptionGroup(OptionGroup $optionGroup, $requiredOptions = [], $defaultOptionPrefix = '--', $defaultOptionJoiner = ' ', $joiner = ' ')
    {
        $optionGroup->setDefaultOptionJoiner($defaultOptionJoiner)
            ->setDefaultOptionPrefix($defaultOptionPrefix)
            ->setJoiner($joiner)
            ->setRequiredOptions($requiredOptions)
            ->setGroupJoin($this->getJoiner());
        return $optionGroup;
    }

    /**
     * 
     * @param string $name
     * @return OptionGroup
     */
    protected function getOptionGroup($name)
    {
        if (!isset($this->optionGroups[$name]))
        {
            $this->addOptionGroup($name);
        }
        return $this->optionGroups[$name];
    }

    public function getDefaultOptionGroup()
    {
        return $this->getOptionGroup(self::DEFAULT_INPUT_GROUP_NAME);
    }

    public function removeOptionGroup($name)
    {
        unset($this->optionGroups[$name]);
        return $this;
    }

    /**
     * 
     * @return OptionGroup[]
     */
    protected function getOptionGroups()
    {
        return $this->optionGroups;
    }

    /**
     * 
     * @param CommandPreset $preset
     * @return static
     */
    public function applyPreset(CommandPreset $preset)
    {
        return $preset->setCommand($this)
                ->apply();
    }

    /**
     * 
     * @param \Ketwaroo\ExternalCommand\Command $command
     * @param type $pipe
     * @return static
     */
    public function addPipedCommand(Command $command, $pipe = '|')
    {
        $this->pipes[] = [$pipe, $command];

        return $this;
    }

    /**
     * 
     * @return static
     */
    public function clearPipedCommands()
    {
        $this->pipes = [];
        return $this;
    }

    /**
     * 
     * @return string
     */
    protected function renderPipedCommands()
    {

        if (empty($this->pipes))
        {
            return '';
        }

        $piped = [];

        foreach ($this->pipes as $p)
        {
            $piped[] = strtr('{joiner}{pipe}{joiner}{command}', [
                '{joiner}'  => $this->getJoiner(),
                '{pipe}'    => $p[0],
                '{command}' => $p[1],
            ]);
        }

        return implode('', $piped);
    }

    /**
     * 
     * @return Output
     */
    public function getOutput()
    {
        return $this->output;
    }

    protected function setOutput(Output $output)
    {
        $this->output = $output;
        return $this;
    }

    public function getJoiner()
    {
        return $this->joiner;
    }

    public function setJoiner($joiner)
    {
        $this->joiner = $joiner;
        return $this;
    }

    public function __clone()
    {
        $this->__deepClone();
    }

}
