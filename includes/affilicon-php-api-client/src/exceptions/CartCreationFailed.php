<?php

namespace AffiliconApiClient\Exceptions;


class CartCreationFailed extends ClientExceptions
{
  /**
   * CartCreationFailed constructor.
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct("Cart creation failed: " . $message, 1);
  }
}