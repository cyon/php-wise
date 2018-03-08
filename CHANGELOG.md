# Changelog

All notable changes to `php-wise` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]

### Changed
- Fixed wrong PHP `use` in docs about usage

## [2.0.1] - 2018-03-05

### Added
- Added optimize-native-functions-fixer for php-cs-fixer to optimize code for OpCache lookups

### Changed
- Prefixes native PHP functions
- Increases travis 'composer install' timeout

## [2.0.0] - 2018-02-18

### Added
- BC Break: Drop support for PHP 5.3. PHP 5.4 required
- Made compatible with Symfony 3.0/4.0 
- Basic editorconfig
- New .gitattributes file for export and release
- Optimized testing with virtual filesystem vfsStream testing library
- Tests runs with multiple PHP versions and Symfony versions
- Added php-cs-fixer to auto format source code
- Test code compliance with php-cs-fixer in travis CI step

### Changed
- Rearranched folder structure of source and test files
- Rearranched vendor folder. Defaul to vendor
- Anhanced phpunit.xml.dist. Reduce clutter and enable code coverage in build folder
- Updated PHPUnit version
- Make compatible with Symfony 2.7, 3.0, 4.0
- Changes autoloader structure from PSR-0 to PSR-4
- Renamed composer package from herrera-io/wise to cyon/wise to publish on packagist.org
- Updated documenation due to package renaming

### Removed
- Replaced Herrera\PHPUnit\TestCase with PHPUnit\TestCase and removed composer dependency
- Removed Herrera\Json composer dependency

## [1.4.0] - 2013-08-15
Last release of https://github.com/kherge-abandoned/php-wise. Project was forked of this repository.

[Unreleased]: https://github.com/cyon/php-wise/compare/2.0.1...HEAD
[2.0.1]: https://github.com/cyon/php-wise/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/cyon/php-wise/compare/1.4.0...2.0.0
[1.4.0]: https://github.com/cyon/php-wise/compare/1.3.6...1.4.0
