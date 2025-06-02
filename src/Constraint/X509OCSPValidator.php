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

use Rollerworks\Component\X509Validator\OCSPValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @template-extends TLSCertificateValidator<X509OCSP>
 */
final class X509OCSPValidator extends TLSCertificateValidator
{
    public function __construct(private readonly OCSPValidator $OCSPValidator)
    {
    }

    protected function checkConstraintType(Constraint $constraint): void
    {
        if (! $constraint instanceof X509OCSP) {
            throw new UnexpectedTypeException($constraint, X509OCSP::class);
        }
    }

    protected function validateTLS(X509CertificateBundle $value, Constraint $constraint): void
    {
        $this->OCSPValidator->validateStatus($value->certificate, $value->caList);
    }
}
