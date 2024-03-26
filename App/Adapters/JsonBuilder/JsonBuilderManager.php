<?php

namespace Descolar\Adapters\JsonBuilder;

use Descolar\Managers\JsonBuilder\Exceptions\DataAlreadyExistsException;
use Descolar\Managers\JsonBuilder\Exceptions\JsonIsNotValidException;
use Descolar\Managers\JsonBuilder\Exceptions\StatusCodeIsNotValidException;
use Descolar\Managers\JsonBuilder\Interfaces\IJsonBuilder;
use Override;

class JsonBuilderManager implements IJsonBuilder
{

    public array $jsonData = [];
    public int $code = 200;

    #[Override] public function addData(string $key, mixed $value): IJsonBuilder
    {
        //check if key exists
        if (array_key_exists($key, $this->jsonData)) {
          throw new DataAlreadyExistsException("The key $key already exists in the json data");
        }

        $this->jsonData[$key] = $value;

        return $this;
    }

    #[Override] public function setCode(int $code): IJsonBuilder
    {
        if ($code < 100 || $code > 599) {
            throw new StatusCodeIsNotValidException("The code $code must be between 100 and 599");
        }

        $this->code = $code;

        return $this;
    }

    private function printJson(mixed $value): string {
        switch ($value) {
            case is_string($value):
                return "\"$value\"";
            case is_array($value):
                $json = "[";
                foreach ($value as $key => $arrayValue) {
                    $json .= "{$this->printJson($arrayValue)},";
                }
                $json = substr($json, 0, -1);
                return $json . "]";
            case is_bool($value):
                return $value ? "true" : "false";
            case is_null($value):
                return "null";
            default:
                return $value;
        }
    }

    #[Override] public function getResult(): void
    {
        header('Content-Type: application/json');
        http_response_code($this->code);
        echo json_encode($this->jsonData);
    }

    #[Override] public function getJson(): array
    {
        return $this->jsonData;
    }

    #[Override] public function getString(): string
    {
        $json = "{";
        foreach ($this->jsonData as $key => $value) {
            $json .= "\"$key\": {$this->printJson($value)},";
        }
        $json = substr($json, 0, -1);
        $json .= "}";


        if(!json_validate($json)) {
            throw new JsonIsNotValidException("The json is not valid");
        }

        return $json;
    }
}