<?php

namespace App\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueForCollectionValidator extends ConstraintValidator
{
    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function validate(mixed $collection, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueForCollection) {
            throw new UnexpectedTypeException($constraint, UniqueForCollection::class);
        }

        if ($collection === null) {
            return;
        }

        $fields = (array) $constraint->fields;

        $propertyValues = [];
        foreach ($collection as $key => $element) {
            $propertyValue = [];
            foreach ($fields as $field) {
                $propertyValue[] = $this->propertyAccessor->getValue($element, $field);
            }

            if (\in_array($propertyValue, $propertyValues, true)) {
                $this->context->buildViolation($constraint->message)
                    //->atPath($this->propertyAccessor->getValue($element, 'product.id'))  // ->atPath(sprintf('[%s]', $key))
                    ->atPath(sprintf('[%s]', $key))
                    ->setParameter('{{ value }}', $this->propertyAccessor->getValue($element, 'product.name'))
                    ->setCode(UniqueForCollection::IS_NOT_UNIQUE)
                    ->addViolation();
            }
            $propertyValues[] = $propertyValue;
        }
    }
}
