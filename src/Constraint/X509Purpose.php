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

use Rollerworks\Component\X509Validator\CertificateValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509Purpose extends Constraint
{
    public const PURPOSE_SMIME = CertificateValidator::PURPOSE_SMIME;
    public const PURPOSE_SMIME_ENCRYPTION = CertificateValidator::PURPOSE_SMIME_ENCRYPTION;
    public const PURPOSE_SMIME_SIGNING = CertificateValidator::PURPOSE_SMIME_SIGNING;

    public const PURPOSE_SSL_CLIENT = CertificateValidator::PURPOSE_SSL_CLIENT;
    public const PURPOSE_SSL_SERVER = CertificateValidator::PURPOSE_SSL_SERVER;

    /** @param string[] $purposes */
    public function __construct(public array $purposes, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);
    }

    public function getTargets(): array | string
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
