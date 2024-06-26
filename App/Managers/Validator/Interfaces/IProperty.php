<?php

namespace Descolar\Managers\Validator\Interfaces;

interface IProperty
{

    /**
     * create the property to be validated
     * @param string|int|null $content the content to be validated
     * @return boolean true if the content is valid, false otherwise
     */
    public function check(mixed $content): bool;

}