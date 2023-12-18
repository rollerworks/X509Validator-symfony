Rollerworks X509Validator Symfony Bridge
========================================

This package provides the Symfony validator/bundle integration for the 
[X509 TLS certificate/private-key validators][x509-validators].

The X509 TLS certificate/private-key validators validate the following:

* CA chain completeness
* PrivateKey bits length
* Signature algorithm
* OCSP Revocation status (requires internet access)
* Certificate purpose
* Certificate general validity (private-key compatibility, not expired, readable)
* Certificate hostname pattern supported, and protection
  against global wildcards of public-suffix length violations

## Installation

To install this package, add `rollerworks/x509-validator-symfony` to your composer.json:

```bash
php composer.phar require rollerworks/x509-validator-symfony
```

Now, [Composer][composer] will automatically download all required files,
and install them for you.

[Symfony Flex][flex] (with contrib) is assumed to enable the Bundle and add
required configuration. https://symfony.com/doc/current/bundles.html

Otherwise enable the following bundles:

* `Rollerworks\Component\PdbSfBridge\Bundle\RollerworksPdbBundle`
* `Rollerworks\Component\X509Validator\Symfony\Bundle\RollerworksX509ValidatorBundle`

And add the following configuration:

<details>

```yaml
# config/packages/rollerworks_pdb.yaml

rollerworks_pdb:
    cache_pool: 'rollerworks.cache.public_prefix_db'
    #manager: http # either: 'http' (default), 'static' (requires manual updates) or 'mock'

framework:
    cache:
        pools:
            # This name can be changed by setting `rollerworks_pdb.cache_pool` (**Don't reuse an existing cache pool!**)
            rollerworks.cache.public_prefix_db:
                adapter: cache.adapter.array # use a persistent adapter that can be easily invalidated like cache.adapter.memcached or cache.adapter.pdo
                default_lifetime: 604800 # one week, the cache should be automatically refreshed, unless manager=static is used
```

</details>

## Requirements

You need at least PHP 8.2, internet access is required if you want to
validate a certificate's OCSP status or automatically update the 
PublicSuffixList.

The public-suffix and top-level domain needs to manually updated from time
to time. When internet access is available the PdbManager (provided by `rollerworks/pdb-symfony-bridge`) 
will automatically download the list and store it in the cache.

If no internet access is available, the local cache needs to refreshed manually.
See https://github.com/rollerworks/PdbSfBridge#offline-usage for instructions.

## Basic Usage

* [Constraints](doc/constraints.md)
* [Registering validators (manual integration)](doc/integration.md)

## Versioning

For transparency and insight into the release cycle, and for striving to
maintain backward compatibility, this package is maintained under the
Semantic Versioning guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

For more information on SemVer, please visit <http://semver.org/>.

## License

This library is released under the [MIT license](LICENSE).

## Contributing

This is an open source project. If you'd like to contribute,
please read the [Contributing Guidelines][contributing]. If you're submitting
a pull request, please follow the guidelines in the [Submitting a Patch][patches] section.

[composer]: https://getcomposer.org/doc/00-intro.md
[x509-validators]: https://github.com/rollerworks/X509Validator
[flex]: https://symfony.com/doc/current/setup/flex.html
[contributing]: https://contributing.rollerscapes.net/
[patches]: https://contributing.rollerscapes.net/latest/patches.html
