<?php


namespace Descolar\Managers\Router\Annotations;

use Attribute;
use Descolar\Managers\Router\Interfaces\ILink;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
abstract readonly class Link implements ILink
{

    public function __construct(
        private string $path,
        private array $variables = array(),
        private ?string $name = null
    )
    {
    }

    public abstract function getMethod(): string;

    public function getPath(): string
    {
        return $this->path;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}