<?php

namespace AffiliconApiClient\Exceptions;


class ConfigurationInvalid extends ClientExceptions
{
    /**
     * CartCreationFailed constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct("Configuration invalid: " . $message, 500);
    }
}