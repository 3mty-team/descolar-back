<?php

namespace Descolar\Managers\JsonBuilder\Interfaces;

interface IJsonBuilder
{

    /**
     * Add a data to the JSON
     * @param string $key The key of the data
     * @param mixed $value The value of the data
     * @return IJsonBuilder The JSON
     */
    public function addData(string $key, mixed $value): self;

    /**
     * Add a message to the JSON
     * @param int $code The code of the message
     * @return IJsonBuilder The JSON
     */
    public function setCode(int $code): self;


    /**
     * Show the JSON (echo)
     */
    public function getResult(): void;

    /**
     * Get the JSON as a data
     * @return array The JSON
     */
    public function getJson(): array;

    /**
     * Get the JSON as a string
     * @return string The JSON
     */
    public function getString(): string;

}