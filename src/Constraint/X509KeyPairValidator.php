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

use Rollerworks\Component\X509Validator\KeyValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @template-extends TLSCertificateValidator<X509KeyPair>
 */
final class X509KeyPairValidator extends TLSCertificateValidator
{
    public function __construct(private readonly KeyValidator $keyValidator) {}

    protected function checkConstraintType(Constraint $constraint): void
    {
        if (! $constraint instanceof X509KeyPair) {
            throw new UnexpectedTypeException($constraint, X509KeyPair::class);
        }
    }

    protected function validateTLS(X509CertificateBundle $value, Constraint $constraint): void
    {
        if (! isset($value->privateKey)) {
            throw new \InvalidArgumentException('No PrivateKey provided with X509CertificateBundle.');
        }

        $this->keyValidator->validate($value->privateKey, $value->certificate);
    }
}
