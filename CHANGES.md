# Kunena 5.2 Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.2.3-dev]
### Fixed
* [#7919](https://github.com/Kunena/Kunena-Forum/issues/7919): Trying to get property 'userid' of non-object profile.php
* [#7917](https://github.com/Kunena/Kunena-Forum/issues/7917): Trying to get property 'userid' of non-object profile.php
* [#7913](https://github.com/Kunena/Kunena-Forum/issues/7913): Topic Template:Call undefined method KunenaUser::getName()
* [#7869](https://github.com/Kunena/Kunena-Forum/issues/7869): Topic template - Options from the modal will not Implement
* [#7845](https://github.com/Kunena/Kunena-Forum/issues/7845): enabling CB integration plugin errors (Part 2)
* Update part to check Php extensions because was duplicated
* Not possible to display a tweet on a message with blue eagle
* Missing setting for custom config file in crypsisb3 and b4
* [#7857](https://github.com/Kunena/Kunena-Forum/issues/7857) : Crypsisb3 and b4 | The poll symbol not available category
* Check for ckeditor custom config file was wrong template (Part 2)
* Check for ckeditor custom config file was wrong template params
* Undefined variable: bigicon in /kunena/icons/icons.php line 1357
* Disable SpellChecker from Ckeditor to let the browser handle it
* Fix in case using parameters on CBForums plugin to sideprofile

### Added
* The changelog is a PHP file
* [#7845](https://github.com/Kunena/Kunena-Forum/issues/7845): enabling CB integration plugin errors
* Add check for php extensions during Kunena installation
* Add a template params to load a custom config file for ckeditor

### Changed
* Update CKeditor to 4.16.0
* Avoid to put line return on icons in Ckeditor
* Update fontawesome to 5.15.2

### Removed
* Remove uneeded plugins files for CKeditor

## [5.2.2] - 2021-01-13

## [5.2.1] - 2020-12-27

## [5.2.0] - 2020-12-09