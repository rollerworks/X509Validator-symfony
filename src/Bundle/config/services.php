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

use Pdp\PublicSuffixList;
use Rollerworks\Component\X509Validator\CAResolverImpl;
use Rollerworks\Component\X509Validator\CertificateValidator;
use Rollerworks\Component\X509Validator\KeyValidator;
use Rollerworks\Component\X509Validator\OCSPValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509CertificateValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509HostnamePatternValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509KeyPairValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509OCSPValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509PurposeValidator;
use Rollerworks\Component\X509Validator\Symfony\Constraint\X509SupportValidator;
use Rollerworks\Component\X509Validator\X509DataExtractor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('rollerworks_x509.ca_resolver.default', CAResolverImpl::class)
            ->alias('rollerworks_x509.ca_resolver', 'rollerworks_x509.ca_resolver.default')
        ->set('rollerworks_x509.x509_data_extractor', X509DataExtractor::class)

        ->set('rollerworks_x509.certificate_validator', CertificateValidator::class)
            ->args([
                inline_service(PublicSuffixList::class)->factory([service('rollerworks_pdb.pdb_manager'), 'getPublicSuffixList']),
                service('rollerworks_x509.x509_data_extractor'),
                service('rollerworks_x509.ca_resolver'),
                service('clock')->nullOnInvalid(),
            ])
        ->set('rollerworks_x509.key_validator', KeyValidator::class)

        ->set('rollerworks_x509.ocsp_validator', OCSPValidator::class)
            ->args([
                service('http_client')->nullOnInvalid(), // Fails when HttpClient is not installed.
                service('logger'),
                service('rollerworks_x509.ca_resolver'),
                service('rollerworks_x509.x509_data_extractor'),
            ])

        // Constraints

        ->set(X509CertificateValidator::class)
            ->args([
                service('rollerworks_x509.certificate_validator'),
            ])
            ->tag('validator.constraint_validator')

        ->set(X509HostnamePatternValidator::class)
            ->args([
                service('rollerworks_x509.certificate_validator'),
            ])
            ->tag('validator.constraint_validator')

        ->set(X509KeyPairValidator::class)
            ->args([
                service('rollerworks_x509.key_validator'),
            ])
            ->tag('validator.constraint_validator')

        ->set(X509OCSPValidator::class)
            ->args([
                service('rollerworks_x509.ocsp_validator'),
            ])
            ->tag('validator.constraint_validator')

        ->set(X509PurposeValidator::class)
            ->args([
                service('rollerworks_x509.certificate_validator'),
            ])
            ->tag('validator.constraint_validator')

        ->set(X509SupportValidator::class)
            ->args([
                service('rollerworks_x509.certificate_validator'),
                tagged_locator('rollerworks_x509.support_callable_service'),
            ])
            ->tag('validator.constraint_validator')
    ;
};
