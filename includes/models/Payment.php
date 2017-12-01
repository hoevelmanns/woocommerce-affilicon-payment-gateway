<?php

class Payment
{
    /** @var string */
    private $method;

    /** @var string */
    private $state;

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return Payment
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return Payment
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }


}