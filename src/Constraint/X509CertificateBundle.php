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

use ParagonIE\HiddenString\HiddenString;

/**
 * DTO to provide a X.509 Certificate bundle with (optional) private-key
 * and CA-list to validators.
 */
final class X509CertificateBundle
{
    /**
     * @param HiddenString|string|null $privateKey private key provided as memory-protected string
     * @param array<string, string>    $caList     [user-provided CA-name => X509 contents]
     */
    public function __construct(
        public string $certificate,
        public HiddenString | string | null $privateKey = null,
        public array $caList = []
    ) {}
}
