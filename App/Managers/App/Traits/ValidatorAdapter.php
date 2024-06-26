<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Validator\Exceptions\ValidatorNotFoundException;
use Descolar\Managers\Validator\Interfaces\IValidator;

trait ValidatorAdapter
{
    use BaseAdapter;

    private static ?IValidator $_validator = null;

    /**
     * Set the validator adapter to be used by the application.
     *
     * @param class-string<IValidator> $validatorClazz the validator class.
     * @throws ValidatorNotFoundException if validator is not found or if he doesn't extend {@see IValidator} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useValidator(string $validatorClazz): void
    {
        self::useAdapter(self::$_validator, ValidatorNotFoundException::class, IValidator::class, $validatorClazz);
    }

    /**
     * Return validator, if it is set, from adapters.
     *
     * @return IValidator|null validator.
     * @throws ValidatorNotFoundException if validator is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getValidator(): ?IValidator
    {
        return self::getAdapter(self::$_validator, ValidatorNotFoundException::class);
    }

}