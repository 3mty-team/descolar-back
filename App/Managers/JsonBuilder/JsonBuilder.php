<?php

namespace Descolar\Managers\JsonBuilder;

use Descolar\App;
use Descolar\Managers\JsonBuilder\Interfaces\IJsonBuilder;

class JsonBuilder
{

    /**
     * The main method to build the json response
     * @return IJsonBuilder
     *
     * @see IJsonBuilder
     */
    public static function build(): IJsonBuilder
    {
        return App::getJsonBuilder();
    }

}