<?php

namespace Descolar\Adapters\Swagger;

use Descolar\Managers\Swagger\Interfaces\ISwagger;

class SwaggerManager implements ISwagger
{

    /**
     * @see ISwagger::getContent()
     */
    public function getContent(): string
    {
        $data = ob_start();
        //todo set subfolder instead "back" !
        $subfolder = '/back';
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $subfolder . '/api/data.json';
        require_once __DIR__ . '\ui\index.php';
        return ob_get_clean();
    }
}