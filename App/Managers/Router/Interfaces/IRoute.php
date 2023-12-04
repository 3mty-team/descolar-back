<?php


namespace Descolar\Managers\Router\Interfaces;

interface IRoute
{

    public function getPath(): string;

    public function getName(): ?string;

    public function getParams(): array;

    public function getUrl(array $params = array()): string;

    public function with($param, $regex): self;

    public function match(string $url): bool;

    public function call(): mixed;

}