<?php

namespace Descolar\Adapters\Swagger;

use Descolar\Managers\Swagger\Interfaces\ISwagger;
use Override;

class SwaggerManager implements ISwagger
{

    #[Override]
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