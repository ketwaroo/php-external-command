<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\ExternalCommand;

use Ketwaroo\ExternalCommand\Option;

/**
 * Description of OptionGroup
 *
 * @author Yaasir Ketwaroo
 */
class OptionGroup
{

    use TraitUtils;

    protected $name;

    /**
     *
     * @var Option[] 
     */
    protected $options             = [];
    protected $requiredOptions     = [];
    protected $defaultOptionPrefix = '--';
    protected $defaultOptionJoiner = ' ';
    protected $joiner              = ' ';
    protected $groupJoin           = ' ';
    protected $argumentCounter     = 0;

    public function __construct($name)
    {
        $this->setName($name);
    }

    public function buildOptionGroupString()
    {

        return $this->getGroupJoin() . implode($this->getJoiner(), $this->getOptions());
    }

    public function __toString()
    {

        return $this->buildOptionGroupString();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @param type $name
     * @return static
     */
    protected function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param Option $option
     * @return static
     */
    protected function pushOption(Option $option)
    {
        if ($option->getIsRepeatable())
        {
            $this->options[] = $option;
        }
        else
        {
            $this->options[$option->getName()] = $option;
        }

        return $this;
    }

    /**
     * 
     * @return static
     * @throws \InvalidArgumentException
     */
    public function verifyRequiredOptions()
    {
        $missing = [];
        foreach ($this->getRequiredOptions() as $name)
        {
            if (!$this->hasOption($name))
            {
                $missing[] = $name;
            }
        }
        if (!empty($missing))
        {
            throw new \InvalidArgumentException('Missing the following in [' . $this->getName() . '] options: ' . implode(', ', $missing));
        }

        return $this;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function hasOption($name)
    {
        if (isset($this->options[$name]))
        {
            return true;
        }
        else
        {
            foreach ($this->options as $k => $o)
            {
                if (strcmp($name, $o->getName()) === 0)
                {
                    return true;
                }
            }
        }


        return false;
    }

    /**
     * 
     * @param type $name
     * @return static
     */
    public function removeOption($name)
    {

        if (isset($this->options[$name]))
        {
            unset($this->options[$name]);
        }
        else
        {
            foreach ($this->options as $k => $o)
            {
                if (strcmp($name, $o->getName()) === 0)
                {
                    $this->options[$k] = null;
                }
            }
            $this->options = array_filter($this->options);
        }


        return $this;
    }

    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $prefix
     * @param type $joiner
     * @param type $repeatable
     * @return Option
     */
    protected function newOption($name, $value = null, $prefix = null, $joiner = null, $repeatable = false)
    {

        $opt = new Option(
                $name
                , $value
                , $this->getOptionPrefix($prefix)
                , $this->getOptionJoiner($joiner)
        );
        if ($repeatable)
        {
            $opt->setIsRepeatable(true);
        }
        return $opt;
    }

    /**
     * 
     * @param type $string
     * @return static
     */
    public function addRawOption($string)
    {
        return $this->pushOption($this->newOption($string, null, null, null));
    }

    /**
     * 
     * @param mixed $value
     * @return static
     */
    public function addArgument($value)
    {

        $arg = new Argument($value);

        $arg->setName('arg' . ( ++$this->argumentCounter));

        return $this->pushOption($arg);
    }

    /**
     * 
     * @param type $name
     * @param type $prefix
     * @return static
     */
    public function addOptionWithNoValue($name, $prefix = null)
    {
        return $this->pushOption($this->newOption($name, null, $this->getOptionPrefix($prefix), ''));
    }

    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $prefix
     * @param type $joiner
     * @return static
     */
    public function addOption($name, $value = null, $prefix = null, $joiner = null)
    {
        return $this->pushOption($this->newOption($name, $value, $prefix, $joiner));
    }

    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $prefix
     * @param type $joiner
     * @return static
     */
    public function addOptionWithRawValue($name, $value = null, $prefix = null, $joiner = null)
    {
        return $this->pushOption(
                        $this->newOption($name, $value, $prefix, $joiner)
                                ->setIsRawValue(true)
        );
    }

    /**
     * 
     * @param type $name
     * @param array $rawIfValueIn
     * @param type $value
     * @param type $prefix
     * @param type $joiner
     * @return static
     */
    public function addOptionWithRawValueIf($name, array $rawIfValueIn = [], $value = null, $prefix = null, $joiner = null)
    {
        if (in_array($value, $rawIfValueIn, true))
        {
            return $this->addOptionWithRawValue($name, $value, $prefix, $joiner);
        }
        else
        {
            return $this->addOption($name, $value, $prefix, $joiner);
        }
    }

    /**
     * 
     * @param type $name
     * @return Option
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    public function addRepeatableOption($name, $value = null, $prefix = null, $joiner = null)
    {
        return $this->pushOption($this->newOption($name, $value, $prefix, $joiner, true));
    }

    public function getOptionPrefix($customPrefix = null)
    {
        return null === $customPrefix ? $this->getDefaultOptionPrefix() : $customPrefix;
    }

    public function getOptionJoiner($customJoiner = null)
    {
        return null === $customJoiner ? $this->getDefaultOptionJoiner() : $customJoiner;
    }

    /**
     * 
     * @param type $defaultOptionPrefix
     * @return static
     */
    public function setDefaultOptionPrefix($defaultOptionPrefix = '--')
    {
        $this->defaultOptionPrefix = $defaultOptionPrefix;
        return $this;
    }

    public function getDefaultOptionPrefix()
    {
        return $this->defaultOptionPrefix;
    }

    /**
     * 
     * @param type $defaultOptionJoiner
     * @return static
     */
    public function setDefaultOptionJoiner($defaultOptionJoiner = ' ')
    {
        $this->defaultOptionJoiner = $defaultOptionJoiner;
        return $this;
    }

    public function getDefaultOptionJoiner()
    {
        return $this->defaultOptionJoiner;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getRequiredOptions()
    {
        return $this->requiredOptions;
    }

    public function getJoiner()
    {
        return $this->joiner;
    }

    /**
     * 
     * @param type $requiredOptions
     * @return static
     */
    public function setRequiredOptions($requiredOptions = [])
    {
        $this->requiredOptions = $requiredOptions;
        return $this;
    }

    /**
     * 
     * @param type $joiner
     * @return static
     */
    public function setJoiner($joiner = ' ')
    {
        $this->joiner = $joiner;
        return $this;
    }

    public function getGroupJoin()
    {
        return $this->groupJoin;
    }

    /**
     * 
     * @param type $groupJoin
     * @return static
     */
    public function setGroupJoin($groupJoin = ' ')
    {
        $this->groupJoin = $groupJoin;
        return $this;
    }

    public function __clone()
    {
        $this->__deepClone();
    }

}
