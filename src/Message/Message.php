<?php

namespace App\Message;

class Message implements MessageInterface
{
    private string $body;
    private string $title;
    private ?string $target;

    public function __construct(string $body, string $title = '', ?string $target = null)
    {
        $this->body = $body;
        $this->title = $title;
        $this->target = $target;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(?string $target = null): self
    {
        $this->target = $target;
        return $this;
    }


}