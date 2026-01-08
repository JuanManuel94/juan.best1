<?php

class BaseService
{
  protected $eventManager;

  public function __construct()
  {
    $this->eventManager = new EventManager();
  }
}