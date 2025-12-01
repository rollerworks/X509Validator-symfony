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

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509HostnamePattern extends Constraint
{
    public function __construct(public string $pattern, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
