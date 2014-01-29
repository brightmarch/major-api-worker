<?php

namespace MajorApi\Worker;

abstract class AbstractWorker
{

    /** @var array */
    public $args = [];

    public function __construct()
    {
    }

    /**
     * Returns an argument injected by Resque. Returns
     * null if the argument is not found.
     *
     * @param string $argument
     * @return string
     */
    public function getArgument($argument)
    {
        if (array_key_exists($argument, $this->args)) {
            return $this->args[$argument];
        }

        return null;
    }

    abstract public function perform();

}
