<?php



namespace Descolar\Managers\Router\Interfaces;

interface ILink
{

    public function getMethod(): string;

    public function getPath(): string;

    public function getVariables(): array;

    public function getName(): ?string;
}