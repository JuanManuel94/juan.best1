<?php

class EventManager
{
  private array $observers = [];

  public function subscribe(Observer $observer)
  {
    $this->observers[] = $observer;
  }

  public function triggerEvent($eventData)
  {
    foreach ($this->observers as $observer) {
      try {
        $observer->update($eventData);
      } catch (\Throwable $th) {
      }
    }
  }
}