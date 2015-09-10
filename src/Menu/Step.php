<?php

namespace Hellofresh\DoctrineTutorial\Menu;

class Step
{
    /**
     * @var string
     */
    protected $instructions;

    public function getInstructions()
    {
        return $this->instructions;
    }

    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }
}
