<?php

namespace Descolar\Managers\Swagger\Interfaces;

interface ISwagger
{

    /**
     * Get the content of the swagger page.
     * @return string page content
     */
    public function getContent(): string;

}