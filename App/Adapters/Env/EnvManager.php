<?php

namespace Descolar\Adapters\Env;

use Closure;
use Override;
use Descolar\Managers\Env\Exceptions\EnvAlreadyExistsException;
use Descolar\Managers\Env\Exceptions\EnvDuplicateValueException;
use Descolar\Managers\Env\Exceptions\EnvNotFoundException;
use Descolar\Managers\Env\Interfaces\IEnv;

class EnvManager implements IEnv
{

    /**
     * EnvManager constructor.
     * @param string $fileName The name of the file to read
     * @param array $envContent The content of the file
     */
    public function __construct(
        private readonly string $fileName = "",
        private readonly string $fileExtension = ".env",
        private array           $envContent = []
    )
    {
        $this->loadEnv();
    }

    private function getPath(): string
    {
        return DIR_ROOT . DIRECTORY_SEPARATOR . $this->fileName . $this->fileExtension;
    }

    private function forEach(Closure $function): void
    {

        $lines = file($this->getPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!count($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            [$name, $value] = explode("=", $line);
            $function($name, $value);
        }
    }

    private function loadEnv(): void
    {
        $this->forEach(function ($name, $value) {
            if ($this->has($name)) {
                throw new EnvDuplicateValueException("The environment variable $name is duplicated for the file {$this->getPath()}");
            }
            $this->envContent[$name] = $value;
        });
    }

    #[Override] public function get(string $key): ?string
    {
        return $this->envContent[$key] ?? null;
    }

    #[Override] public function set(string $key, string $value, bool $secure = true): void
    {
        $key = strtoupper(trim($key));

        if ($this->has($key)) {
            if ($secure) {
                throw new EnvAlreadyExistsException("The environment variable $key is already exists {$this->getPath()}");
            } else {
                return;
            }
        }

        $fileContent = fopen($this->getPath(), "ab");
        $textToSet = $key . "=" . trim($value) . PHP_EOL;

        fwrite($fileContent, $textToSet);
        fclose($fileContent);

        $this->envContent[$key] = $value;
    }

    #[Override] public function has(string $key): bool
    {
        return isset($this->envContent[$key]);
    }

    #[Override] public function remove(string $key, bool $secure = true): void
    {
        $key = mb_strtoupper(trim($key));

        if (!$this->has($key)) {
            if ($secure) {
                throw new EnvNotFoundException("The environment variable $key does not exist in the file {$this->getPath()}");
            } else {
                return;
            }
        }

        $line = $key . "=" . trim($this->envContent[$key]);

        $fileContent = file_get_contents($this->getPath());
        $fileContent = str_replace($line, "", $fileContent);
        file_put_contents($this->getPath(), $fileContent);

        unset($this->envContent[$key]);
    }

    #[Override] public function addMultiple(array $env, bool $secure = true): void
    {
        foreach ($env as [$key, $value]) {
            $this->set($key, $value, $secure);
        }
    }

    #[Override] public function removeMultiple(string ...$key): void
    {
        foreach ($key as $keyName) {
            $this->remove($keyName);
        }
    }

    #[Override] public function getAll(): array
    {
        return $this->envContent;
    }
}