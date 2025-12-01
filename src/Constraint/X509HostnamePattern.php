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
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509HostnamePattern extends Constraint
{
    public string $pattern;

    public function __construct(mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        if (\is_string($options)) {
            $this->pattern = $options;

            return;
        }

        if (! \is_array($options) || ! isset($options['pattern'])) {
            throw new MissingOptionsException(\sprintf('The options "pattern" must be set for constraint "%s".', self::class), ['pattern']);
        }

        $this->pattern = $options['pattern'];
    }

    public function getDefaultOption(): string
    {
        return 'pattern';
    }

    public function getRequiredOptions(): array
    {
        return ['pattern'];
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
