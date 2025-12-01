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
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class X509Support extends Constraint
{
    /**
     * @var \Closure|array{0: string|object, 1: string}|string
     */
    public \Closure | array | string $callback;

    /**
     * Constructor.
     *
     * The $callback parameter can be either one of the following:
     *
     * - A Closure
     * - A method name (on the current object)
     * - A self invoking service-id as '@app.x509.custom_validator'
     * - A callback array with indexes as `['ClassName', 'methodName']`
     * - A callback array with indexes as `[{object}, 'methodName']`
     * - A callback array with indexes as `['@serviceId', 'methodName']` for accessible services
     *   (service id must be a string)
     *
     * The callback receives the information as ({X509Info object}, "$certificate", {CertificateValidator}, $root).
     * The '$root' callback-argument refers to the ValidatorContext (the object/value that is validated).
     *
     * **Note:** Service callbacks depend a ServiceContainer, which is implementation specific,
     * check the documentation of the implementation you are using.
     *
     * @param \Closure|array{0: string|object, 1: string}|string $callback
     */
    public function __construct(\Closure | array | string $callback, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->callback = $callback;

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

    public function getTargets(): array | string
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
