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

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509Purpose extends Constraint
{
    public const PURPOSE_SMIME = CertificateValidator::PURPOSE_SMIME;
    public const PURPOSE_SMIME_ENCRYPTION = CertificateValidator::PURPOSE_SMIME_ENCRYPTION;
    public const PURPOSE_SMIME_SIGNING = CertificateValidator::PURPOSE_SMIME_SIGNING;

    public const PURPOSE_SSL_CLIENT = CertificateValidator::PURPOSE_SSL_CLIENT;
    public const PURPOSE_SSL_SERVER = CertificateValidator::PURPOSE_SSL_SERVER;

    /** @param array<array-key, string> $purposes */
    public function __construct(public array $purposes, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        if (\count($purposes) > 0) {
            $options['purposes'] = $purposes;
        }

        parent::__construct($options, $groups, $payload);
    }

    public function getDefaultOption(): string
    {
        return 'purposes';
    }

    public function getRequiredOptions(): array
    {
        return ['purposes'];
    }

    public function getTargets(): array | string
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
