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

use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BeforeClass;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Exporter\Exporter;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @internal
 */
trait ConstraintViolationComparatorTrait
{
    private static ?ConstraintViolationComparator $violationComparator = null;

    #[BeforeClass]
    public static function setUpValidatorComparator(): void
    {
        self::$violationComparator = new ConstraintViolationComparator();

        $comparatorFactory = ComparatorFactory::getInstance();
        $comparatorFactory->register(self::$violationComparator);
    }

    #[AfterClass]
    public static function tearDownValidatorComparator(): void
    {
        if (self::$violationComparator === null) {
            return;
        }

        $comparatorFactory = ComparatorFactory::getInstance();
        $comparatorFactory->unregister(self::$violationComparator);
        self::$violationComparator = null;
    }
}

final class ConstraintViolationComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof ConstraintViolation && $actual instanceof ConstraintViolation;
    }

    /**
     * @param ConstraintViolation $expected
     * @param ConstraintViolation $actual
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        // Should we also check the Root??
        if ($this->equalsViolation($expected, $actual)
            && $expected->getMessage() === $actual->getMessage()
            && $expected->getMessageTemplate() === $actual->getMessageTemplate()
            && $expected->getCode() === $actual->getCode()
            && $expected->getPropertyPath() === $actual->getPropertyPath()
        ) {
            return;
        }

        $exporter = new Exporter();

        throw new ComparisonFailure(
            $expected,
            $actual,
            $exportedExpected = $exporter->export($expected),
            $exportedActual = $exporter->export($actual),
            \sprintf(
                'Failed asserting that %s matches expected %s.',
                $exportedActual,
                $exportedExpected
            )
        );
    }

    private function equalsViolation(ConstraintViolation $expected, ConstraintViolation $actual): bool
    {
        $factory = $this->factory();

        try {
            $factory->getComparatorFor($expected->getParameters(), $actual->getParameters())
                ->assertEquals($expected->getParameters(), $actual->getParameters());

            $factory->getComparatorFor($expected->getInvalidValue(), $actual->getInvalidValue())
                ->assertEquals($expected->getInvalidValue(), $actual->getInvalidValue());

            if ($expected->getCause() !== null) {
                $factory->getComparatorFor($expected->getCause(), $actual->getCause())
                    ->assertEquals($expected->getCause(), $actual->getCause());
            }

            return true;
        } catch (ComparisonFailure) {
            return false;
        }
    }
}
