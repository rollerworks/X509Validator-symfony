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

use Psr\Container\ContainerInterface;
use Rollerworks\Component\X509Validator\CertificateValidator;
use Rollerworks\Component\X509Validator\X509Info;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @template-extends TLSCertificateValidator<X509Support>
 */
final class X509SupportValidator extends TLSCertificateValidator
{
    public function __construct(
        private readonly CertificateValidator $certificateValidator,
        private readonly ContainerInterface $container,
    ) {}

    protected function checkConstraintType(Constraint $constraint): void
    {
        if (! $constraint instanceof X509Support) {
            throw new UnexpectedTypeException($constraint, X509Support::class);
        }
    }

    protected function validateTLS(X509CertificateBundle $value, Constraint $constraint): void
    {
        $this->certificateValidator->validateCertificateSupport(
            $value->certificate,
            $this->getCallback($constraint->callback)
        );
    }

    /** @param array{0: string|object, 1: string}|string|\Closure $callback */
    private function getCallback(\Closure | array | string $callback): callable
    {
        $object = $this->context->getObject();

        // Whether the reference is callable is already checked in the X509Support constraint
        // So at this point not being a callable means it's either a service or method-name.

        if ($callback instanceof \Closure) {
            return static function (X509Info $info, string $certificate, CertificateValidator $validator) use ($object, $callback): void {
                $callback($info, $certificate, $validator, $object);
            };
        }

        if (\is_string($callback)) {
            $callback = [$object, $callback];
        } elseif (\is_string($callback[0]) && $callback[0][0] === '@') {
            $callback = $this->getService($callback[0], $callback[1]);
        }

        if (! \is_callable($callback)) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Callback %s targeted by X509Support constraint is not callable.',
                    json_encode([\is_object($callback[0]) ? $callback[0]::class : $callback[0], $callback[1]], \JSON_THROW_ON_ERROR),
                )
            );
        }

        return static function (X509Info $info, string $certificate, CertificateValidator $validator) use ($object, $callback): void {
            $callback($info, $certificate, $validator, $object);
        };
    }

    private function getService(string $callback, string $method): mixed
    {
        $id = mb_substr($callback, 1);

        try {
            $service = $this->container->get($id);
        } catch (\Throwable $e) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Service %s targeted by X509Support constraint %s.',
                    json_encode($id, \JSON_THROW_ON_ERROR),
                    ! $this->container->has($id) ? 'does not exist' : 'is invalid'
                ),
                0,
                $e
            );
        }

        if (! \is_callable([$service, $method])) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Callback %s (with class %s) targeted by X509Support constraint is not callable.',
                    json_encode(['@' . $id, $method], \JSON_THROW_ON_ERROR),
                    json_encode($service::class, \JSON_THROW_ON_ERROR),
                )
            );
        }

        return [$service, $method];
    }
}
