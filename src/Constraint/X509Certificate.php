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

use Symfony\Component\Validator\Constraint;

/**
 * Validates that the value is a valid certificate.
 *
 * * Certificate is not expired;
 * * Domain wildcard (if present) respects the PublicSuffix rules;
 * * Strong signature-type is used (less than SHA256)
 *   (can be disabled with 'allowWeakAlgorithm' option);
 * * Provided CAs chain is complete;
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509Certificate extends Constraint
{
    public function __construct(public bool $allowWeakAlgorithm = false, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): array | string
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
