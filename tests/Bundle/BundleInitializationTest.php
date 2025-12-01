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

namespace Rollerworks\Component\X509Validator\Tests\Symfony\Bundle;

use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\Attributes\Test;
use Rollerworks\Component\PdbSfBridge\Bundle\RollerworksPdbBundle;
use Rollerworks\Component\X509Validator\Symfony\Bundle\RollerworksX509ValidatorBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509Certificate;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509OCSP;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
final class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(FrameworkBundle::class);
        $kernel->addTestBundle(RollerworksX509ValidatorBundle::class);
        $kernel->addTestBundle(RollerworksPdbBundle::class);

        $kernel->addTestCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->findDefinition('translator')->setPublic(true);
                $container->findDefinition('validator')->setPublic(true);
            }
        });

        $kernel->addTestConfig(__DIR__ . '/config.yml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    #[Test]
    public function init_bundle(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('validator'));
    }

    #[Test]
    public function it_works_with_certificate_validator(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get('validator');

        $violations = $validator->validate(
            new X509CertificateBundle(
                <<<'X509'
                    -----BEGIN CERTIFICATE-----
                    MIIDKzCCAhMCCQDZHE66hI+pmjANBgkqhkiG9w0BAQUFADBUMRowGAYDVQQDDBFS
                    b2xsZXJzY2FwZXMgQ0F2MzEVMBMGA1UECgwMUm9sbGVyc2NhcGVzMRIwEAYDVQQH
                    DAlSb3R0ZXJkYW0xCzAJBgNVBAYTAk5MMB4XDTE0MDcyNzEzMDIzM1oXDTE4MDcy
                    NjEzMDIzM1owWzEhMB8GA1UEAwwYYm9wLmRldi5yb2xsZXJzY2FwZXMubmV0MRUw
                    EwYDVQQKDAxSb2xsZXJzY2FwZXMxEjAQBgNVBAcMCVJvdHRlcmRhbTELMAkGA1UE
                    BhMCTkwwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDFN7758InBPIIE
                    Q/VoYrj/poR1bGEcupAB+Q68R2C5ac5EGQMwaODCphP1RetLGHJE+4hss9GzJb56
                    LfLSKy500Zk6R50zUXNCJwvkMvODHTMDy0xORg7tMbe3kLnHH/lbhmeWmXt5qDxa
                    S2jx5A2pKGmoLS8smYFlPRZ0yiK8Ugy5kDWCEFA31TIsGKcofOWcr+vfJ7HltXav
                    h1VFZ2nzJC8xKaoFQO4uake225CZQ+W4yhIxu5beY/FXlh2PIZqd1rQhQLuV5gK4
                    zGkjNkN6DVJ+7xwnYJ7yeXKlovwMOEJQG1LHnr16gFRRcFeVUHPZkW47QGOYh60n
                    rG8/8/kLAgMBAAEwDQYJKoZIhvcNAQEFBQADggEBAKLWz2F2bJyhTlHxAORbdugm
                    esBbPxlhkCitdXp7uAkQh+0HeJ+jFb+CA0blmGyY3j15t54WV9ySMV8tQRSk5sXl
                    VVaJ4AF0uIvT5gbOvL8Vr2ZNiWp2S0Nqx28JVP/KNCAI3PBIWnDcQOON3gHQQi9O
                    qmL+vAuODEQ2UvgCd2GgFPqsu79Y1PRbqRIwqNiFasHt9pQNlpzRM6AjtUMldShG
                    rpz1WIZIIZuH+TC/iqD7UlSoLxJbe79a6dbBNw7bnWlo+HDl8YfmY6Ks3O6MCbYn
                    qVBRc3K9ywcUYPJNVuUazdXuY6FSiGB1iOLxRHppQapmWK5GdtQFXW3GlkXFYf4=
                    -----END CERTIFICATE-----
                    X509
            ),
            new X509Certificate()
        );

        self::assertCount(1, $violations);
    }

    #[Test]
    public function it_works_with_ocsp_validator(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get('validator');

        $violations = $validator->validate(
            new X509CertificateBundle(
                <<<'X509'
                    -----BEGIN CERTIFICATE-----
                    MIIG4DCCBmagAwIBAgIQBZy2esMzb+7oVrJyhjxvUzAKBggqhkjOPQQDAzBUMQsw
                    CQYDVQQGEwJVUzEXMBUGA1UEChMORGlnaUNlcnQsIEluYy4xLDAqBgNVBAMTI0Rp
                    Z2lDZXJ0IEc1IFRMUyBFQ0MgU0hBMzg0IDIwMjEgQ0ExMB4XDTIzMDMxNjAwMDAw
                    MFoXDTI0MDQxNTIzNTk1OVowge8xEzARBgsrBgEEAYI3PAIBAxMCVVMxFTATBgsr
                    BgEEAYI3PAIBAhMEVXRhaDEdMBsGA1UEDwwUUHJpdmF0ZSBPcmdhbml6YXRpb24x
                    FTATBgNVBAUTDDUyOTk1MzctMDE0MjELMAkGA1UEBhMCVVMxDTALBgNVBAgTBFV0
                    YWgxDTALBgNVBAcTBExlaGkxFzAVBgNVBAoTDkRpZ2lDZXJ0LCBJbmMuMUcwRQYD
                    VQQDEz5kaWdpY2VydC10bHMtZWNjLXAzODQtcm9vdC1nNS1yZXZva2VkLmNoYWlu
                    LWRlbW9zLmRpZ2ljZXJ0LmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoC
                    ggEBAK3SEGd7aOOyi9rknL/GpNSovKvxOeJzrpO7Spq1Ag7KeAtx6kYDm7xgvOXM
                    EPPylKDCvtXl1ic+PYBBVpNZEhHTVefdb9CzsTEcOYLaPFIAOnmie1HHczY57H2f
                    JqvaYqE4VJWAHWuGMf90ZYSkqtoGJJsnLs/Ajd3lawIeUwPCDdWKQiUVG53Ruk5G
                    KRct/Jnxo2qX1GMPt63Q4nvjb0p+4UvWYfBSCAD6UehkdGb1RkbEgKxwBUbFzh7p
                    dQ1WDIVzV0C6OPdt4LUqvVYVw9DpmMSF3YUOvvDEhx1w5bR8JIzmlFYP/IMBx1Bl
                    a9WWbVjCbASq6Z4XrWMpgNoYBEkCAwEAAaOCA7EwggOtMB8GA1UdIwQYMBaAFJtY
                    3I2mZZjnvAb+GqQVoG/L5qmQMB0GA1UdDgQWBBSsdJukmxK5PpB8IpqyeWO1KJt1
                    EzBJBgNVHREEQjBAgj5kaWdpY2VydC10bHMtZWNjLXAzODQtcm9vdC1nNS1yZXZv
                    a2VkLmNoYWluLWRlbW9zLmRpZ2ljZXJ0LmNvbTAOBgNVHQ8BAf8EBAMCBaAwHQYD
                    VR0lBBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMIGTBgNVHR8EgYswgYgwQqBAoD6G
                    PGh0dHA6Ly9jcmwzLmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydEc1VExTRUNDU0hBMzg0
                    MjAyMUNBMS0xLmNybDBCoECgPoY8aHR0cDovL2NybDQuZGlnaWNlcnQuY29tL0Rp
                    Z2lDZXJ0RzVUTFNFQ0NTSEEzODQyMDIxQ0ExLTEuY3JsMEoGA1UdIARDMEEwCwYJ
                    YIZIAYb9bAIBMDIGBWeBDAEBMCkwJwYIKwYBBQUHAgEWG2h0dHA6Ly93d3cuZGln
                    aWNlcnQuY29tL0NQUzCBgQYIKwYBBQUHAQEEdTBzMCQGCCsGAQUFBzABhhhodHRw
                    Oi8vb2NzcC5kaWdpY2VydC5jb20wSwYIKwYBBQUHMAKGP2h0dHA6Ly9jYWNlcnRz
                    LmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydEc1VExTRUNDU0hBMzg0MjAyMUNBMS0xLmNy
                    dDAJBgNVHRMEAjAAMIIBfgYKKwYBBAHWeQIEAgSCAW4EggFqAWgAdgDuzdBk1dsa
                    zsVct520zROiModGfLzs3sNRSFlGcR+1mwAAAYbrrv/aAAAEAwBHMEUCIBj/mZ6B
                    QbGrMpNBZoWihQ7+ckmYk1ZbEi/sxPFluT++AiEAzdLuAClGxj5Qw/9yv9XcuVgo
                    LJxuiPNFmI3cifmpY/UAdwA7U3d1Pi25gE6LMFsG/kA7Z9hPw/THvQANLXJv4frU
                    FwAAAYbrrwAfAAAEAwBIMEYCIQCZUf8y3yPDyIA/hfhvZ21ukC3zcdunoqVq+TQW
                    YksMpgIhALfmINPZJmrjy5T/zeHxCxHCHaBpRMIKGAej1JkgeknSAHUAdv+IPwq2
                    +5VRwmHM9Ye6NLSkzbsp3GhCCp/mZ0xaOnQAAAGG668ATwAABAMARjBEAiBWGuOl
                    X3fLXWfP3ihTE9q/c5sco24KNW0Ij6NaK40hcgIgFKryWdWqqZRoI9LgeBqkzs2p
                    8ivZEu2wLXX+RoyoCL4wCgYIKoZIzj0EAwMDaAAwZQIxAKcEoh9LUSQ2h/XcESEG
                    LxpGGAcssmrUXBDE0jJPSGgg1ypiE0ay+nYv3TIxVenpIQIweLQmI/ljlQtRBEEh
                    JdnlcMbdN5VOUqtwqd3jEVBgU6vyUmRltnZybBAMUiBpWX+t
                    -----END CERTIFICATE-----
                    X509,

                caList: [
                    'DigiCert G5 TLS ECC SHA384 2021 CA1' => <<<'CERT'
                        -----BEGIN CERTIFICATE-----
                        MIIDajCCAu+gAwIBAgIQBBxdKC6zcQ5rcsLavSZxbzAKBggqhkjOPQQDAzBOMQsw
                        CQYDVQQGEwJVUzEXMBUGA1UEChMORGlnaUNlcnQsIEluYy4xJjAkBgNVBAMTHURp
                        Z2lDZXJ0IFRMUyBFQ0MgUDM4NCBSb290IEc1MB4XDTIxMDQxNDAwMDAwMFoXDTMx
                        MDQxMzIzNTk1OVowVDELMAkGA1UEBhMCVVMxFzAVBgNVBAoTDkRpZ2lDZXJ0LCBJ
                        bmMuMSwwKgYDVQQDEyNEaWdpQ2VydCBHNSBUTFMgRUNDIFNIQTM4NCAyMDIxIENB
                        MTB2MBAGByqGSM49AgEGBSuBBAAiA2IABLzRK0f/iJs+T+i9//yOWT++GUuwRjML
                        dbsA+9YQsrHFyayUZeqYRATordp1DcodGY/1DxRAwDDEi0cmkfHX08nLQ0ujF1Vh
                        dlBxYUPol2HLYjN3EzVAcDC8V5uiQc5zCqOCAYowggGGMBIGA1UdEwEB/wQIMAYB
                        Af8CAQAwHQYDVR0OBBYEFJtY3I2mZZjnvAb+GqQVoG/L5qmQMB8GA1UdIwQYMBaA
                        FMFRRVBZqz7nLFr6ICISB4CIfBFqMA4GA1UdDwEB/wQEAwIBhjAdBgNVHSUEFjAU
                        BggrBgEFBQcDAQYIKwYBBQUHAwIwegYIKwYBBQUHAQEEbjBsMCQGCCsGAQUFBzAB
                        hhhodHRwOi8vb2NzcC5kaWdpY2VydC5jb20wRAYIKwYBBQUHMAKGOGh0dHA6Ly9j
                        YWNlcnRzLmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydFRMU0VDQ1AzODRSb290RzUuY3J0
                        MEYGA1UdHwQ/MD0wO6A5oDeGNWh0dHA6Ly9jcmwzLmRpZ2ljZXJ0LmNvbS9EaWdp
                        Q2VydFRMU0VDQ1AzODRSb290RzUuY3JsMD0GA1UdIAQ2MDQwCwYJYIZIAYb9bAIB
                        MAcGBWeBDAEBMAgGBmeBDAECATAIBgZngQwBAgIwCAYGZ4EMAQIDMAoGCCqGSM49
                        BAMDA2kAMGYCMQDt1p2aebRUXBPN1FXg+V6oG+mRLdRC49k8dSxwRG77lsj1YqTO
                        IvZuhDckSAkMNGICMQD4lvGyMGMQirgiqAaMdybUTpcDTLtRQPKiGVZOoSaRtq8o
                        gRocsHZfwG69pfWn10Y=
                        -----END CERTIFICATE-----
                        CERT,

                    'DigiCert TLS ECC P384 Root G5' => <<<'CERT'
                        -----BEGIN CERTIFICATE-----
                        MIIDIDCCAqagAwIBAgIQDdiPPdDfPycTMSudQiae5zAKBggqhkjOPQQDAzBhMQsw
                        CQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3d3cu
                        ZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBHMzAe
                        Fw0yMjA5MjEwMDAwMDBaFw0zNzA5MjAyMzU5NTlaME4xCzAJBgNVBAYTAlVTMRcw
                        FQYDVQQKEw5EaWdpQ2VydCwgSW5jLjEmMCQGA1UEAxMdRGlnaUNlcnQgVExTIEVD
                        QyBQMzg0IFJvb3QgRzUwdjAQBgcqhkjOPQIBBgUrgQQAIgNiAATBRKHPEZdQmt4j
                        gjUHzdDLGJ3S8X93NU873ZRyUu3CO/js+ntrWCDsma7J/GizdbnbCezIE/VOxgod
                        ZjBMux9HCjxhEEIpfKUIDuAi6dM1aM6bY5+EtZlNWKCO9VTnlcmjggE0MIIBMDAP
                        BgNVHRMBAf8EBTADAQH/MB0GA1UdDgQWBBTBUUVQWas+5yxa+iAiEgeAiHwRajAf
                        BgNVHSMEGDAWgBSz20ik+aHF2K42QcwRY2liKbxLxjAOBgNVHQ8BAf8EBAMCAYYw
                        dgYIKwYBBQUHAQEEajBoMCQGCCsGAQUFBzABhhhodHRwOi8vb2NzcC5kaWdpY2Vy
                        dC5jb20wQAYIKwYBBQUHMAKGNGh0dHA6Ly9jYWNlcnRzLmRpZ2ljZXJ0LmNvbS9E
                        aWdpQ2VydEdsb2JhbFJvb3RHMy5jcnQwQgYDVR0fBDswOTA3oDWgM4YxaHR0cDov
                        L2NybDMuZGlnaWNlcnQuY29tL0RpZ2lDZXJ0R2xvYmFsUm9vdEczLmNybDARBgNV
                        HSAECjAIMAYGBFUdIAAwCgYIKoZIzj0EAwMDaAAwZQIwWaC7MYlkl+PK+/jDHt/6
                        +exBh3Dt+pwj3KFpgZBWvKWLBGZUyX9WNBtj1eNgfCtXAjEApDGlZlvWDpn5/Dqe
                        NzZ6X97ngPkW2Eygum0xfANTThtvuxA8KQr3/ME81/h/Tj9O
                        -----END CERTIFICATE-----
                        CERT,

                    'DigiCert Global Root G3' => <<<'CERT'
                        -----BEGIN CERTIFICATE-----
                        MIICPzCCAcWgAwIBAgIQBVVWvPJepDU1w6QP1atFcjAKBggqhkjOPQQDAzBhMQsw
                        CQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3d3cu
                        ZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBHMzAe
                        Fw0xMzA4MDExMjAwMDBaFw0zODAxMTUxMjAwMDBaMGExCzAJBgNVBAYTAlVTMRUw
                        EwYDVQQKEwxEaWdpQ2VydCBJbmMxGTAXBgNVBAsTEHd3dy5kaWdpY2VydC5jb20x
                        IDAeBgNVBAMTF0RpZ2lDZXJ0IEdsb2JhbCBSb290IEczMHYwEAYHKoZIzj0CAQYF
                        K4EEACIDYgAE3afZu4q4C/sLfyHS8L6+c/MzXRq8NOrexpu80JX28MzQC7phW1FG
                        fp4tn+6OYwwX7Adw9c+ELkCDnOg/QW07rdOkFFk2eJ0DQ+4QE2xy3q6Ip6FrtUPO
                        Z9wj/wMco+I+o0IwQDAPBgNVHRMBAf8EBTADAQH/MA4GA1UdDwEB/wQEAwIBhjAd
                        BgNVHQ4EFgQUs9tIpPmhxdiuNkHMEWNpYim8S8YwCgYIKoZIzj0EAwMDaAAwZQIx
                        AK288mw/EkrRLTnDCgmXc/SINoyIJ7vmiI1Qhadj+Z4y3maTD/HMsQmP3Wyr+mt/
                        oAIwOWZbwmSNuJ5Q3KjVSaLtx9zRSX8XAbjIho9OjIgrqJqpisXRAL34VOKa5Vt8
                        sycX
                        -----END CERTIFICATE-----
                        CERT,
                ]
            ),
            new X509OCSP()
        );

        self::assertCount(1, $violations);

        $violation = $violations->get(0);

        self::assertEquals(
            'The certificate with serial-number "7459839413651464540545224973334900563" was marked as revoked on 3/16/23 with reason: (unspecified) no specific reason was given.',
            $kernel->getContainer()->get('translator')->trans(
                $violation->getMessageTemplate(),
                $violation->getParameters(),
                'validators',
                'en'
            )
        );
    }
}
