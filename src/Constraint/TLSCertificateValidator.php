<?php

declare(strict_types=1);

/*
 * This file is part of the Rollerworks X509Validator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\X509Validator\Symfony\Constraint;

use Rollerworks\Component\X509Validator\Violation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @template T of Constraint
 */
abstract class TLSCertificateValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->checkConstraintType($constraint);

        if ($value === null) {
            return;
        }

        if (! $value instanceof X509CertificateBundle) {
            throw new UnexpectedValueException($value, X509CertificateBundle::class);
        }

        try {
            $this->validateTLS($value, $constraint);
        } catch (Violation $violation) {
            $this->context->buildViolation($violation->getTranslatorMsg())
                ->setParameters($this->getTranslationArguments($violation))
                ->setInvalidValue($value->certificate)
                ->setCause($violation)
                ->addViolation();
        }
    }

    abstract protected function checkConstraintType(Constraint $constraint): void;

    /** @param T $constraint */
    abstract protected function validateTLS(X509CertificateBundle $value, Constraint $constraint): void;

    /** @return array<string, mixed> */
    private function getTranslationArguments(Violation $violation): array
    {
        $arguments = $violation->getParameters();

        foreach ($arguments as $key => $v) {
            unset($arguments[$key]);
            $arguments[\sprintf('%s', $key)] = $v;
        }

        return $arguments;
    }
}
