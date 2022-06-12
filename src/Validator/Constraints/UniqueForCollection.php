<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueForCollection extends Constraint
{
    public const IS_NOT_UNIQUE = 'IS_NOT_UNIQUE';

    public array|string $fields = [];

    protected const ERROR_NAMES = [
        self::IS_NOT_UNIQUE => 'IS_NOT_UNIQUE',
    ];

    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid. This collection should contain only unique elements.';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
