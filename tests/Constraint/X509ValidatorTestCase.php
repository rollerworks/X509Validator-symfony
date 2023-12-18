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

namespace Rollerworks\Component\X509Validator\Tests\Symfony\Constraint;

use Rollerworks\Component\PdbSfBridge\PdpMockProvider;
use Rollerworks\Component\X509Validator\CertificateValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
abstract class X509ValidatorTestCase extends ConstraintValidatorTestCase
{
    use ConstraintViolationComparatorTrait;

    protected function getCertificateValidator(): CertificateValidator
    {
        return new CertificateValidator(PdpMockProvider::getPdpManager()->getPublicSuffixList());
    }
}
