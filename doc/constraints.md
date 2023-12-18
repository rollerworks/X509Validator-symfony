Constraints
===========

**Note:** The validators are expected to be registered with the Validator ConstraintsFactory,
using the integration bundle this is already done for you, if not see 
[Registering validators](doc/integration.md).

The validators expect a `Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle`
instance is provided as value to validate against, containing the Certificate, PrivateKey (optional, 
required for the `KeyPair` constraint) and a (optionally) a list of CA certificates.

**Note:** CAs are expected to be provided when the certificate is not self-signed.

All contents are expected to be provided as X509 PEM encoded files, any other format is not supported.
_Any invalid PEM encoding is handled by the validator._

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;

$x509Bundle = new X509CertificateBundle(
    certificate: <<<'CERT'
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
        CERT,
            
    privateKey: <<<'PRIVATE_KEY'
        -----BEGIN RSA PRIVATE KEY-----
        MIIEogIBAAKCAQEAxTe++fCJwTyCBEP1aGK4/6aEdWxhHLqQAfkOvEdguWnORBkD
        MGjgwqYT9UXrSxhyRPuIbLPRsyW+ei3y0isudNGZOkedM1FzQicL5DLzgx0zA8tM
        TkYO7TG3t5C5xx/5W4Znlpl7eag8Wkto8eQNqShpqC0vLJmBZT0WdMoivFIMuZA1
        ghBQN9UyLBinKHzlnK/r3yex5bV2r4dVRWdp8yQvMSmqBUDuLmpHttuQmUPluMoS
        MbuW3mPxV5YdjyGanda0IUC7leYCuMxpIzZDeg1Sfu8cJ2Ce8nlypaL8DDhCUBtS
        x569eoBUUXBXlVBz2ZFuO0BjmIetJ6xvP/P5CwIDAQABAoIBAEZcy0A1N5C/28tV
        y7rAbiyX5m5WipdLYJGzoDRAaxv7yeG14tNkt7v6sOgzV+1k/W/rJhNSXKDD+J9y
        wU2Gpn57QWXvowBqMOsLL0zteL/wrQDPiZvrluu9b0SI2B9ZIwgqfc7XV5xiD5ZP
        jVOv/8e4aWndJRWOdwH9t4NXkukI5Joc/l0JvLVlteBwJO22JvWp3skBiNBCwP/e
        +tx9570QJederODEkf0wPpD4PSMM86GpP5x0+NGfO+fn0AD2adSmOSRnzO769AzH
        l3R5Oh2tMFgnyxmLYpa/DL1XAgR6vIPkgJOVkcbg19yps+f35Mi1n9e63QDEB8lI
        fkRFtAECgYEA6Wxvd9miW5ts02K34oxm/UWp6trZKthhWQ0J2JDn7dvO6KnyIzpw
        cfEv6wRHxtSot/VkV1Qf6YwPKvl8KkYVDXbs9AZ4nzEXp6GSkf2SEGx2h2Gofiwq
        DkWRnaI/1kM4ukzW16PiumTd8KQis6V7/2y9Kw1t9u2DyYUv6KfIUAsCgYEA2Era
        4jQ4VQMJBBY8pQN+gMpH+avytvGGHXl/tm6My7LevEZOq00LAhlsa/fwUxI1dXhH
        yFXtQIILZw79a1bRWsbfFrkWiC9g0JgNDt/pzds2EsTltVS5OWRMaVcrL3glP8+U
        ObW4qzTJiI6m6LKV7hnmaL1fR/NUjWk+fvc/mwECgYAMs3fFP7RT47siLWbwDs+z
        zEyYmNvkNu3lGI6GmCvmh2VUx5qDTDS+Hm+LDCqTqRKdH98b2Vn7LUHOBtE4w6N1
        nhj6ljeOAe/VkTcWdoOyHRS9/RRb+S84o5RuzVtH31SA3pl6FlLJ7Z8d7vBscf6z
        QUlxxENNglL/bh3TPP3rTQKBgC8LwSZ4s1QSb/CaoaBG7Uo4NYWiGA4g5MoedmAJ
        Fcjs5DPRmyT5gg531zR43qZDDKu7eOmjfxKL9sz43rhtTuZO4ZGAutzuaUGWASke
        HS3wo4dbmpdhkVRhc5lqI3OUz41cqmIPG9bpiXiRhs6QoboDmjFoF4R/8gE8RiK5
        xvUBAoGACrghAg+GlJZ/Aogx7wK6b1k8rfcpgIoHxOPiqIgyMgevTT6D6w8D0CqI
        cEVTZ/fm+EaNuMZvxqSG5f19/obLus+VNXvnMYi3qwFAZ5NhKBen12YhIcaZpOh1
        ZSjeYozDCyRmv76q3sqcLrwxnULIcaK0l255ZczzwiUl39Bqe1o=
        -----END RSA PRIVATE KEY-----
        PRIVATE_KEY,
        
    
    // Order of CAs doesn't matter, chain must be complete.
    // Keys are used for identifying in case of a violation (string only)
    caList: [
        'DigiCert SHA2 Secure Server CA' => <<<'CERT'
            -----BEGIN CERTIFICATE-----
            MIIElDCCA3ygAwIBAgIQAf2j627KdciIQ4tyS8+8kTANBgkqhkiG9w0BAQsFADBh
            MQswCQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3
            d3cuZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBD
            QTAeFw0xMzAzMDgxMjAwMDBaFw0yMzAzMDgxMjAwMDBaME0xCzAJBgNVBAYTAlVT
            MRUwEwYDVQQKEwxEaWdpQ2VydCBJbmMxJzAlBgNVBAMTHkRpZ2lDZXJ0IFNIQTIg
            U2VjdXJlIFNlcnZlciBDQTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEB
            ANyuWJBNwcQwFZA1W248ghX1LFy949v/cUP6ZCWA1O4Yok3wZtAKc24RmDYXZK83
            nf36QYSvx6+M/hpzTc8zl5CilodTgyu5pnVILR1WN3vaMTIa16yrBvSqXUu3R0bd
            KpPDkC55gIDvEwRqFDu1m5K+wgdlTvza/P96rtxcflUxDOg5B6TXvi/TC2rSsd9f
            /ld0Uzs1gN2ujkSYs58O09rg1/RrKatEp0tYhG2SS4HD2nOLEpdIkARFdRrdNzGX
            kujNVA075ME/OV4uuPNcfhCOhkEAjUVmR7ChZc6gqikJTvOX6+guqw9ypzAO+sf0
            /RR3w6RbKFfCs/mC/bdFWJsCAwEAAaOCAVowggFWMBIGA1UdEwEB/wQIMAYBAf8C
            AQAwDgYDVR0PAQH/BAQDAgGGMDQGCCsGAQUFBwEBBCgwJjAkBggrBgEFBQcwAYYY
            aHR0cDovL29jc3AuZGlnaWNlcnQuY29tMHsGA1UdHwR0MHIwN6A1oDOGMWh0dHA6
            Ly9jcmwzLmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydEdsb2JhbFJvb3RDQS5jcmwwN6A1
            oDOGMWh0dHA6Ly9jcmw0LmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydEdsb2JhbFJvb3RD
            QS5jcmwwPQYDVR0gBDYwNDAyBgRVHSAAMCowKAYIKwYBBQUHAgEWHGh0dHBzOi8v
            d3d3LmRpZ2ljZXJ0LmNvbS9DUFMwHQYDVR0OBBYEFA+AYRyCMWHVLyjnjUY4tCzh
            xtniMB8GA1UdIwQYMBaAFAPeUDVW0Uy7ZvCj4hsbw5eyPdFVMA0GCSqGSIb3DQEB
            CwUAA4IBAQAjPt9L0jFCpbZ+QlwaRMxp0Wi0XUvgBCFsS+JtzLHgl4+mUwnNqipl
            5TlPHoOlblyYoiQm5vuh7ZPHLgLGTUq/sELfeNqzqPlt/yGFUzZgTHbO7Djc1lGA
            8MXW5dRNJ2Srm8c+cftIl7gzbckTB+6WohsYFfZcTEDts8Ls/3HB40f/1LkAtDdC
            2iDJ6m6K7hQGrn2iWZiIqBtvLfTyyRRfJs8sjX7tN8Cp1Tm5gr8ZDOo0rwAhaPit
            c+LJMto4JQtV05od8GiG7S5BNO98pVAdvzr508EIDObtHopYJeS4d60tbvVS3bR0
            j6tJLp07kzQoH3jOlOrHvdPJbRzeXDLz
            -----END CERTIFICATE-----
            CERT,

        'DigiCert Global Root CA' => <<<'CERT'
            -----BEGIN CERTIFICATE-----
            MIIDrzCCApegAwIBAgIQCDvgVpBCRrGhdWrJWZHHSjANBgkqhkiG9w0BAQUFADBh
            MQswCQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3
            d3cuZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBD
            QTAeFw0wNjExMTAwMDAwMDBaFw0zMTExMTAwMDAwMDBaMGExCzAJBgNVBAYTAlVT
            MRUwEwYDVQQKEwxEaWdpQ2VydCBJbmMxGTAXBgNVBAsTEHd3dy5kaWdpY2VydC5j
            b20xIDAeBgNVBAMTF0RpZ2lDZXJ0IEdsb2JhbCBSb290IENBMIIBIjANBgkqhkiG
            9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4jvhEXLeqKTTo1eqUKKPC3eQyaKl7hLOllsB
            CSDMAZOnTjC3U/dDxGkAV53ijSLdhwZAAIEJzs4bg7/fzTtxRuLWZscFs3YnFo97
            nh6Vfe63SKMI2tavegw5BmV/Sl0fvBf4q77uKNd0f3p4mVmFaG5cIzJLv07A6Fpt
            43C/dxC//AH2hdmoRBBYMql1GNXRor5H4idq9Joz+EkIYIvUX7Q6hL+hqkpMfT7P
            T19sdl6gSzeRntwi5m3OFBqOasv+zbMUZBfHWymeMr/y7vrTC0LUq7dBMtoM1O/4
            gdW7jVg/tRvoSSiicNoxBN33shbyTApOB6jtSj1etX+jkMOvJwIDAQABo2MwYTAO
            BgNVHQ8BAf8EBAMCAYYwDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQUA95QNVbR
            TLtm8KPiGxvDl7I90VUwHwYDVR0jBBgwFoAUA95QNVbRTLtm8KPiGxvDl7I90VUw
            DQYJKoZIhvcNAQEFBQADggEBAMucN6pIExIK+t1EnE9SsPTfrgT1eXkIoyQY/Esr
            hMAtudXH/vTBH1jLuG2cenTnmCmrEbXjcKChzUyImZOMkXDiqw8cvpOp/2PV5Adg
            06O/nVsJ8dWO41P0jmP6P6fbtGbfYmbW0W5BjfIttep3Sp+dWOIrWcBAI+0tKIJF
            PnlUkiaY4IBIqDfv8NZ5YBberOgOzW6sRBc4L0na4UU+Krk2U886UAb3LujEV0ls
            YSEY1QSteDwsOoBrp+uvFRTp2InBuThs4pFsiv9kuXclVzDAGySj4dzp30d8tbQk
            CAUw7C29C79Fv1C5qfPrmAESrciIxpg0X40KPMbp1ZWVbd4=
            -----END CERTIFICATE-----
            CERT,
    ],
);
```

### Private-Key memory safety

The private-key must be kept secret as much as technically possible,
storage of this key outside the scope of this manual. But in general
it's recommended to protect against leaking the private-key in core 
dumbs and log files.

Alternative the private-key can be provided using a
`ParagonIE\HiddenString\HiddenString` instance.

First make sure the `paragonie/hidden-string` is installed

```bash
php composer.phar require paragonie/hidden-string
```

And provide the private as follows:

```php
use ParagonIE\HiddenString\HiddenString;

$privateKey = '....'; // User-input

$privateKeySecure = new HiddenString($privateKey);
sodium_memzero($privateKey);

// The $privateKey variable only contains null-data (of the same length) at this point
unset($privateKey);

$x509Bundle = new X509CertificateBundle(
    certificate: '...',
    privateKey: $privateKeySecure,
);
```

**Note:** The `paragonie/hidden-string` is released under the Mozilla Public Licence v2.0
which _is_ compatible with the GNU GPL license.

X509Certificate
---------------

This constraint ensures the provided `X509CertificateBundle`
contains a valid certificate with the following conditions:

* Certificate is not expired;
* Domain wildcard (if present) respects the PublicSuffix rules;
* Strong signature-type is used (less than SHA256)
  (can be disabled with 'allowWeakAlgorithm' option);
* Provided CAs chain is complete;

**This constraint should be used in all validation cases, as all the other constraints
only check the PEM encoded validity.**

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509Certificate;

class CommandDto
{
    #[X509Certificate()]
    public X509CertificateBundle $x509;
}
```

X509HostnamePattern
-------------------

Validates that the provided `X509CertificateBundle` certificate contains one or more
hostnames that matches a specific hostname or wildcard pattern (`*.example.com`).

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509HostnamePattern;

class CommandDto
{
    #[X509CertificateBundle('example.com')]
    public X509CertificateBundle $x509;
}
```

**Tip:** This constraint is mostly usable in a Form or input handler
as hard coding a hostname-pattern is not something you would rather do.

The [X509Certificate](#X509Certificate) constraint ensures none of the
hostnames in the certificate violate the PublicSuffix rules.

X509KeyPair
-----------

Validates that the provided `X509CertificateBundle` certificate and private-key
matches. If the private-key is not provided this will give a violation.

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509KeyPair;

class CommandDto
{
    #[X509KeyPair()]
    public X509CertificateBundle $x509;
}
```

X509OCSP
--------

Validates that the provided `X509CertificateBundle` certificate is not revoked
by the Certificate Authority.

**Note:** This validator requires internet access to contact the Certificate Authority server.

Make sure the Symfony HttpClient component is installed and enabled (when using the FrameworkBundle).

```bash
php composer.phar require symfony/http-client
```

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509OCSP;

class CommandDto
{
    #[X509OCSP()]
    public X509CertificateBundle $x509;
}
```

X509Purpose
-----------

Validates that the provided `X509CertificateBundle` certificate supports the required
purpose.

Supported purposes: 

* `PURPOSE_SMIME` (as `[X509Purpose::PURPOSE_SMIME_ENCRYPTION, X509Purpose::PURPOSE_SMIME_SIGNING]` )
* `PURPOSE_SMIME_ENCRYPTION` (Secure MIME encryption)
* `PURPOSE_SMIME_SIGNING` (Secure MIME signing)
* `PURPOSE_SSL_CLIENT` (used for client-side certificate-based PKI authentication)
* `PURPOSE_SSL_SERVER` (used for https server connection)

```php
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509Purpose;

class CommandDto
{
    #[X509Purpose([X509Purpose::PURPOSE_SSL_SERVER])]
    // #[X509Purpose([X509Purpose::PURPOSE_SSL_SERVER, X509Purpose::PURPOSE_SMIME])]
    public X509CertificateBundle $x509;
}
```

X509Support
-----------

This validator allows to use a custom callable that receives the extracted x509
information and internal validator for more advanced validation.

The callable can be either valid callable (`Closure` or `[ClassName::class, 'methodName']`),
a method-name on the currently validated object (like the `CommandDto` in this example) 
or a [service-reference](#x509support-service).

```php
use Rollerworks\Component\X509Validator\CertificateValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateBundle;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509Support;
use Rollerworks\Component\X509Validator\Violation;
use Rollerworks\Component\X509Validator\X509Info;

final class EmailFieldRequired extends Violation
{
    public function __construct()
    {
        parent::__construct('The certificate should contains an emails extension.');
    }

    public function getTranslatorMsg(): string
    {
        return 'The certificate should contains an emails extension.';
    }
}

class CommandDto
{
    #[X509Support('validateCert')]                                  // Method-name on "$this" object
    // #[X509Support(['ClassName', 'validateCert'])]                // ClassName with method
    // #[X509Support('ClassName')]                                  // Self invoking by ClassName
    // #[X509Support(['@app.x509.custom_validator', 'methodName'])] // Service by service-id with method
    // #[X509Support('@app.x509.custom_validator')]                 // Service by service-id (self invoking)
    // #[X509Support('@' . ServicedCustomX509Validator::class)]     // Service by service-id (autowire by ClassName)
    public X509CertificateBundle $x509;

    /**
     * @param X509Info $info contains all the raw information extracted from
     *                       the certificate. Field starting with underscore are normalized.
     * @param mixed    $root The 'root' of the ValidatorContext.
     *                       In the case of the example above this would be the CommandDto object.
     */
    public function validateCert(X509Info $info, string $certificate, CertificateValidator $validator, mixed $root): void
    {
        // $validator->validateCertificate($certificate, $root->x509->caList);

        // Unlike the Symfony Validator this validation is expected to throw a
        // (custom) `\Rollerworks\Component\X509Validator\Violation`;
        if (count($info->emails) === 0) {
            throw new EmailFieldRequired();
        }
    }
}
```

See also https://github.com/rollerworks/X509Validator/blob/main/docs/index.md#working-with-validation-violations
on how to work with Violation exceptions.

### Service callable
<a name="x509support-service"></a>

For more advanced use-cases it's possible to use a custom service as callable.
Either `@ServiceId` for a self-invoking class, or `['@ServiceId', 'methodName']`.

Make sure the services are made available to the `X509SupportValidator`
through the second argument, or when using the FrameworkBundle tagging
them with the `X509SupportCallableService` attribute.

**Note:** When not using the attribute tag the service as `rollerworks_x509.support_callable_service`.

```php
use Rollerworks\Component\X509Validator\Symfony\X509SupportCallableService;
use Rollerworks\Component\X509Validator\X509Info;

#[X509SupportCallableService()]
class ServicedCustomX509Validator
{
    /**
     * @param X509Info $info contains all the raw information extracted from
     *                       the certificate. Field starting with underscore are normalized.
     * @param mixed    $root The 'root' of the ValidatorContext.
     *                       In the case of the example above this would be the CommandDto object.
     */
    public function validateCert(X509Info $info, string $certificate, CertificateValidator $validator, mixed $root): void
    {
        // $validator->validateCertificate($certificate, $root->x509->caList);

        // Unlike the Symfony Validator this validation is expected to throw a
        // (custom) `\Rollerworks\Component\X509Validator\Violation`;
        if (count($info->emails) === 0) {
            throw new EmailFieldRequired();
        }
    }
}
```
