<?php

namespace App\Classes;

use Carbon\Carbon;

class ToastMsgContainer
{
    protected Carbon $time;
    protected $id;

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'time' => $this->getTime(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'is_success' => $this->isSuccess(),
        ];
    }

    public function getId()
    {
        return md5($this->time->toIso8601String() . $this->title . $this->message . ($this->isSuccess ? ' true' : ' false'));
    }
    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function __construct(
        protected string $title,
        protected string $message,
        protected bool $isSuccess = true
    ){}

}
