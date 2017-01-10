<?php

namespace Ketwaroo\ExternalCommand;

/**
 * Description of Option
 *
 * @author Yaasir Ketwaroo
 */
class Option
{

    use TraitUtils;

    protected $name, $value, $prefix, $joiner;
    protected $formatter = '{prefix}{name}{joiner}{value}';
    protected $isRepeatable;
    protected $isRawValue;

    public function __construct($name, $value = null, $prefix = '--', $joiner = ' ')
    {
        $this->setName($name)
            ->setValue($value)
            ->setPrefix($prefix)
            ->setJoiner($joiner)
            ->setIsRepeatable(false);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    protected function getRenderedValue()
    {
        $value = $this->getValue();

        if ($this->getIsRawValue())
        {
            return $value;
        }

        return is_null($value) ? '' : $this->escapeShellArg($value);
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
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

    public function getIsRepeatable()
    {
        return $this->isRepeatable;
    }

    public function setIsRepeatable($isRepeatable = false)
    {
        $this->isRepeatable = !empty($isRepeatable);
        return $this;
    }

    public function getIsRawValue()
    {
        return $this->isRawValue;
    }

    public function setIsRawValue($isRawValue = false)
    {
        $this->isRawValue = !empty($isRawValue);
        return $this;
    }

    public function __toString()
    {
        return strtr($this->formatter, array(
            '{name}'   => $this->getName(),
            '{value}'  => $this->getRenderedValue(),
            '{prefix}' => $this->getPrefix(),
            '{joiner}' => $this->getJoiner(),
        ));
    }

    public function __clone()
    {
        $this->__deepClone();
    }

}
