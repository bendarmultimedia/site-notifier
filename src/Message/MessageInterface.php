<?php

namespace App\Message;

interface MessageInterface
{
    public function __construct(string $body, string $title = '', ?string $target = null);
    public function setTitle(string $title): self;
    public function getTitle(): string;

    public function setBody(string $body): self;
    public function getBody(): string;

    public function setTarget(string $target): self;
    public function getTarget(): string;
}