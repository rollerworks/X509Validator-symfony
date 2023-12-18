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

namespace Rollerworks\Component\X509Validator\Symfony\Bundle\DependencyInjection;

use Rollerworks\Component\X509Validator\CertificateValidator;
use Rollerworks\Component\X509Validator\Symfony\X509SupportCallableService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Translation\Translator;

final class RollerworksX509ValidatorExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $container->registerAttributeForAutoconfiguration(X509SupportCallableService::class, static function (ChildDefinition $definition, X509SupportCallableService $attribute): void {
            $definition->addTag('rollerworks_x509.support_callable_service');
        });
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (class_exists(Translator::class)) {
            $container->prependExtensionConfig('framework', [
                'translator' => [
                    'paths' => [
                        \dirname((new \ReflectionClass(CertificateValidator::class))->getFileName(), 2) . '/Resources/translations',
                    ],
                ],
            ]);
        }
    }
}
