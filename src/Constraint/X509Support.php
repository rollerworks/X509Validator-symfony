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
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Execute the callback on the CertificateValidator::validateCertificateSupport().
 *
 * @see CertificateValidator::validateCertificateSupport()
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509Support extends Constraint
{
    /**
     * The callback receives the information as ({X509Info object}, "$certificate", {CertificateValidator}).
     *
     * @var array{0: string|object, 1: string}|string|\Closure callable/method-name (on the current object)
     */
    public array | string | \Closure $callback;

    /**
     * @param array{0: string|object, 1: string}|string|null $callback callable/method-name (on the current object)
     * @param array<string, mixed>                           $options
     */
    public function __construct(\Closure | array | string | null $callback = null, ?array $groups = null, mixed $payload = null, array $options = [])
    {
        // Invocation through attributes with an array parameter only
        if (\is_array($callback) && \count($callback) === 1 && isset($callback['value'])) {
            $callback = $callback['value'];
        }

        if (! \is_array($callback) || (! isset($callback['callback']) && ! isset($callback['groups']) && ! isset($callback['payload']))) {
            $options['callback'] = $callback;
        } else {
            $options = array_merge($callback, $options);
        }

        parent::__construct($options, $groups, $payload);

        if (\is_callable($this->callback) || \is_string($this->callback)) {
            return;
        }

        if (! isset($this->callback[0], $this->callback[1])) {
            throw new ConstraintDefinitionException(
                \sprintf(
                    'Callback %s targeted by X509Support constraint is expected to be a callable, a string or an array with indexes 0 and 1.',
                    json_encode($this->callback)
                )
            );
        }

        // Object, is_callable() should have passed
        if (\is_object($this->callback[0]) || (! \is_string($this->callback[0]) || $this->callback[0][0] !== '@')) {
            throw new ConstraintDefinitionException(
                \sprintf(
                    'Callback "%s" targeted by X509Support constraint is expected to be a callable, a string or an array with index 0 being an object or string.',
                    json_encode([\is_object($this->callback[0]) ? $this->callback[0]::class : $this->callback[0], $this->callback[1]], \JSON_THROW_ON_ERROR),
                )
            );
        }
    }

    public function getDefaultOption(): string
    {
        return 'callback';
    }

    public function getRequiredOptions(): array
    {
        return ['callback'];
    }

    public function getTargets(): array | string
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
