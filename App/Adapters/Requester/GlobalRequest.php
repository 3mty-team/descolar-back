<?php

namespace Descolar\Adapters\Requester;

use Descolar\Adapters\Requester\Types\DeleteRequest;
use Descolar\Adapters\Requester\Types\GetRequest;
use Descolar\Adapters\Requester\Types\PostRequest;
use Descolar\Adapters\Requester\Types\PutRequest;
use Descolar\Managers\Requester\Interfaces\IRequest;
use Descolar\Managers\Requester\Requests\Request;
use Descolar\Managers\Requester\Requests\RequestType;
use Override;

class GlobalRequest implements IRequest
{

    #[Override] public function trackOne(array|string $request): mixed
    {
        if (is_array($request)){
            [$name, $defaultValue] = $request;
        }else{
            $name = $request;
        }

        return $this->trackRequest(new Request($name, $defaultValue ?? null));
    }

    #[Override] public function trackRequest(Request $request): mixed
    {

        $requestType = $this->getRequestType();

        $v = $requestType->getItem($request->getName());

        if(!empty($v) && $v !== "[]"){
            return $v;
        }

        if($request->getToThrowIfNotExists() !== null){
            $className = $request->getToThrowIfNotExists();
            throw new $className("The request {$request->getName()} does not exists");
        }

        if($request->getDefaultValue() !== null){
            return $request->getDefaultValue();
        }

        return null;
    }

    #[Override] public function trackMany(string|array ...$name): array
    {
        $results = [];
        foreach ($name as $n){
            $results[] = $this->trackOne($n);
        }

        return $results;
    }

    #[Override] public function getRequestType(): RequestType
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        return match ($requestMethod) {
            'POST' => PostRequest::getInstance(),
            'PUT' => PutRequest::getInstance(),
            'DELETE' => DeleteRequest::getInstance(),
            default => GetRequest::getInstance()
        };

    }
}