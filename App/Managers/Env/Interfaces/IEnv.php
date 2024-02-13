<?php

namespace Descolar\Managers\Env\Interfaces;

use Descolar\Managers\Env\Exceptions\EnvDuplicateValueException;
use Descolar\Managers\Env\Exceptions\EnvNotFoundException;

interface IEnv
{

    /**
     * Get element from the environment variables
     * @param string $key The key of the element
     * @return string|null The value of the element, or null if the element does not exist
     */
    public function get(string $key): ?string;

    /**
     * Set an element in the environment variables
     * @param string $key The key of the element
     * @param string $value The value of the element
     * @param bool $secure If the value is true, the method will throw an exception if the element already exists
     * @throws EnvDuplicateValueException If the element already exists
     */
    public function set(string $key, string $value, bool $secure = true): void;

    /**
     * Check if an element exists in the environment variables
     * @param string $key The key of the element
     * @return bool True if the element exists, false otherwise
     */
    public function has(string $key): bool;

    /**
     * Remove an element from the environment variables
     * @param string $key The key of the element
     * @param bool $secure If the value is true, the method will throw an exception if the element does not exist
     * @throws EnvNotFoundException If the element does not exist
     */
    public function remove(string $key, bool $secure = true): void;

    /**
     * Add multiple elements to the environment variables
     * @param array<int, array{string, string}> $env An associative array of elements to add
     * @param bool $secure If the value is true, the method will throw an exception if any of the elements already exists
     * @throws EnvDuplicateValueException If any of the elements already exists
     */
    public function addMultiple(array $env, bool $secure = true): void;

    /**
     * Remove multiple elements from the environment variables
     * @param string ...$key The keys of the elements to remove
     * @throws EnvNotFoundException If any of the elements does not exist
     */
    public function removeMultiple(string ...$key): void;

    /**
     * Get all the elements from the environment variables
     * @return array<string, string> An associative array of elements
     */
    public function getAll(): array;
}