<?php

namespace App\Exception;

final class BadConfigurationException extends \Exception
{
    public function __construct(object $object)
    {
        $className = get_class($object);
        parent::__construct("Bad configuration of class {$className}");
    }

}