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

use PHPUnit\Framework\Attributes\Test;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509Purpose;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509PurposeValidator;
use Rollerworks\Component\X509Validator\TranslatableArgument;
use Symfony\Component\Validator\ConstraintValidatorInterface;

/**
 * @internal
 */
final class X509PurposeValidatorTest extends X509ValidatorTestCase
{
    #[Test]
    public function it_ignores_null(): void
    {
        $this->validator->validate(null, new X509Purpose([X509Purpose::PURPOSE_SMIME]));

        $this->assertNoViolation();
    }

    #[Test]
    public function fails_with_unsupported_purpose(): void
    {
        $constraint = new X509Purpose([X509Purpose::PURPOSE_SMIME]);

        $this->validator->validate(
            new X509CertificateBundle(
                $cert = <<<'X509'
                    -----BEGIN CERTIFICATE-----
                    MIIHGTCCBgGgAwIBAgIQBh3eOmYhdHQ4TTZVG+hHijANBgkqhkiG9w0BAQsFADBN
                    MQswCQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMScwJQYDVQQDEx5E
                    aWdpQ2VydCBTSEEyIFNlY3VyZSBTZXJ2ZXIgQ0EwHhcNMTgwMjA4MDAwMDAwWhcN
                    MjEwMjEyMTIwMDAwWjBpMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNV
                    BAcTDVNhbiBGcmFuY2lzY28xITAfBgNVBAoTGFNsYWNrIFRlY2hub2xvZ2llcywg
                    SW5jLjESMBAGA1UEAxMJc2xhY2suY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A
                    MIIBCgKCAQEAqb0QCgBUkwHwC1AUT1N1W6wfbKSUZGSQ9Pf7EovdVIt1f8hrq5KZ
                    OvVUaU/5qsS9UMm1GGqhjVrFqRKv//rZ/VaIThNaLVGQ3yTWCmnPxTZBvEOH1oLP
                    i2V+XgDcX2drRUUfFELQy8EZVABwQu5Y3FluB1S7Nv1EH2tOsug5koMIdtMetUo/
                    nKPzpuVC/4C/8oPN3+37cSriAImfxrifrrSCLkMscnwh6VcSuajnlCgw/iVcQzEE
                    0OGht+KmFgIvjTWmKLx44MvkKqPUnvBudKk4k+9V527g9uNM0rxCVXWb1hf5w08I
                    VvEC5/N78HrBl/q/e2oaygp95z/CQ5aJqQIDAQABo4ID1zCCA9MwHwYDVR0jBBgw
                    FoAUD4BhHIIxYdUvKOeNRji0LOHG2eIwHQYDVR0OBBYEFPla7+E8XELNsM7Mg46q
                    uGwJyd0tMCEGA1UdEQQaMBiCCXNsYWNrLmNvbYILKi5zbGFjay5jb20wDgYDVR0P
                    AQH/BAQDAgWgMB0GA1UdJQQWMBQGCCsGAQUFBwMBBggrBgEFBQcDAjBrBgNVHR8E
                    ZDBiMC+gLaArhilodHRwOi8vY3JsMy5kaWdpY2VydC5jb20vc3NjYS1zaGEyLWc2
                    LmNybDAvoC2gK4YpaHR0cDovL2NybDQuZGlnaWNlcnQuY29tL3NzY2Etc2hhMi1n
                    Ni5jcmwwTAYDVR0gBEUwQzA3BglghkgBhv1sAQEwKjAoBggrBgEFBQcCARYcaHR0
                    cHM6Ly93d3cuZGlnaWNlcnQuY29tL0NQUzAIBgZngQwBAgIwfAYIKwYBBQUHAQEE
                    cDBuMCQGCCsGAQUFBzABhhhodHRwOi8vb2NzcC5kaWdpY2VydC5jb20wRgYIKwYB
                    BQUHMAKGOmh0dHA6Ly9jYWNlcnRzLmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydFNIQTJT
                    ZWN1cmVTZXJ2ZXJDQS5jcnQwDAYDVR0TAQH/BAIwADCCAfYGCisGAQQB1nkCBAIE
                    ggHmBIIB4gHgAHYApLkJkLQYWBSHuxOizGdwCjw1mAT5G9+443fNDsgN3BAAAAFh
                    d2Q95wAABAMARzBFAiEA42uacv79w94og76vu/L9nzZJAsU0398rJZuBAY8EY30C
                    IFCuAzawnV4AOtOEEp7ybdy/0SLBgZ7bBO3gs0EhkOYCAHYAh3W/51l8+IxDmV+9
                    827/Vo1HVjb/SrVgwbTq/16ggw8AAAFhd2Q9zQAABAMARzBFAiBIhbiWxOmsFEmC
                    2I6ZBg8Qb+xSIv0AgqZTnIHSzaR0BwIhALoijpGV0JB2xBgW88noxeHdCeqWXQ/a
                    HPDAd/Q37M+WAHYAu9nfvB+KcbWTlCOXqpJ7RzhXlQqrUugakJZkNo4e0YUAAAFh
                    d2Q+IAAABAMARzBFAiEA0p6Cq67EzeVhxYSpNJYU8Ys7Pj9c4EQPmPaAvnLDL0wC
                    IBnOHO2DWoBi+LH6Z/uicH+4nbb4S15zV96NqFn9mXH0AHYAb1N2rDHwMRnYmQCk
                    URX/dxUcEdkCwQApBo2yCJo32RMAAAFhd2Q/4AAABAMARzBFAiEA2C3VUu67nO5T
                    e2Q8okaIkPftUdE+GHyKkZbqmJMg550CIBFZW53z4BUmtP4GDBEA85D/EnDBPOx2
                    OC6cgoRW7sz/MA0GCSqGSIb3DQEBCwUAA4IBAQBUh0yybzRV4ednO+RM4uifnBkf
                    S/9r4IHqvFyYgyofd1hygwD3i/pT10V+yF2teqL/FuwsInbjrvGpwFH/uiuhGgzc
                    hJ5TOA0/+A/RYNo7sN7An9NBYvedJOlV0iDUhVuQpGefEY3VHqtg0qNu9YoAAl67
                    pDCmmQQoNKHDdq2IFq8taF8ros+stqC+cPBipVLxXe9wAFnTkjq0VjB1VqKzLDQ+
                    VGN9QV+gw0KI7opJ4K/UKOTnG7ON0zlKIqAK2pXUVsQa9Q5kMbakOk3930bGrkXW
                    dqEt/Oc2qDvj/OFnFvaAiKhWUmwhu3IJT4B+W15sPYYBAC4N4FhjP+aGv6IK
                    -----END CERTIFICATE-----
                    X509
            ),
            $constraint
        );

        $this->buildViolation('The certificate does not support the purpose: {required_purpose}.')
            ->setInvalidValue($cert)
            ->setParameters(['required_purpose' => new TranslatableArgument('S/MIME signing', domain: 'messages')])
            ->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new X509PurposeValidator($this->getCertificateValidator());
    }
}
