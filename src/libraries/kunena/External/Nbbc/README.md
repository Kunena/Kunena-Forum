# The New BBCode Parser (NBBC)

[![Build Status](https://img.shields.io/travis/vanilla/nbbc.svg?style=flat-square)](https://travis-ci.org/vanilla/nbbc)
[![Packagist Version](https://img.shields.io/packagist/v/vanilla/nbbc.svg?style=flat-square)](https://packagist.org/packages/vanilla/nbbc)

## Overview

NBBC is a high-speed, extensible, easy-to-use validating BBCode parser that accepts BBCode as input and generates XHTML 1.0 Transitional-compliant markup as its output no matter how mangled the input. It includes built-in support for most common BBCode, as well as external-wiki support, image-library support, a standard library of smileys (emoticons), and via its powerful API it can even be transformed into a validating HTML parser!

NBBC is well-tested, with its output validated against a unit-test suite with over a hundred different tests. It is written entirely in PHP, using clean, namespace-friendly, object-oriented code, and it is compatible both PHP 5.4 and up.

While flexible and powerful, NBBC is also designed to be an easy to use, and is an easy drop-in replacement for most existing BBCode-parsing solutions. In many cases, it can be implemented in your own projects with only two or three lines of code. Only a single PHP source file is necessary in your projects, a compact, optimized file that is automatically generated from the included heavily-commented original PHP source files.

Unlike many open-source packages, it's well-documented, too! With over 50 printed pages of documentation, including a programmer's manual with many examples, and a fully-documented API, you'll never be lost using it.

NBBC is released under a BSD Open-Source License, and may be freely used in any project for any purpose, commercial, noncommercial, or otherwise. It was created from its author's frustration in dealing with the dubious quality of similar products.

In short, NBBC is the package you want for implementing BBCode on your site.

## Changes between NBBC v2.x and v1.x

Version 2.x breaks backwards-compatibility very slightly with the 1.x version of NBBC, which is why it has been given a major version-number update, even though no significant functionality has been added. Here are a summary of the differences:

- All core classes have been moved into the `Nbbc` namespace to support PSR-4 autoloading and become a full composer library.
- PHP 4 is no longer supported. The minimum version of PHP required is now PHP 5.4.
- All properties on the `BBCode` class have been protected and must now be accessed with getters/setters.
- The URL auto-detection has been rewritten. It supports more general cases, but it has removed support for some edge cases such as email addresses with an IP address domain.
- Images and smileys no longer check to see if files exist locally. This removes auto-generated image sizes too.

In addition to these backwards-compatibility breaking changes, there have been a few other changes that should not break backwards compatibility:

- Tests have been moved into a PHPUnit test suite.
- Calls to the `EmailAddressValidator` have been replaced with PHP's `filter_var()` function.
- Calls to the `Profiler` have been removed from the `BBCode` class. There are plenty of profiling tools out there now that don't bloat the code.
- The `@` error silencing operator has been removed wherever possible.

## Credits

- NBBC was originally developed by [Sean Werkema](https://github.com/seanofw) in 2008-10, and most of the core code is his.  He last officially worked on it in September 2010, after which it sat dormant for a few years while he did Other Things, mostly involving Gainful Employment and Wife and Kids.  His last commit was on v1.4.5.
- [Theyak](https://github.com/theyak) imported it from SourceForge into Github in 2013, and did some maintenance work, fixing bugs and adding some minor enhancements from 2013-5.  The Git history of this repository dates back to this point, condensing the prior Subversion history to a single commit.
- The [Vanilla Forums Team](https://github.com/vanilla) did some major work to upgrade Theyak's copy of NBBC to support modern PHP 5/6/7, and they're responsible for v2.x and later versions.

## License

As noted above, most of the NBBC was written by Sean Werkema and the copyright on that code remains his. There are files that also have a copyright assigned to Vanilla Forums Inc. That additional copyright only applies to the changes made by Vanilla Forums Inc.

This library will always be licensed under the BSD v2 open-source license, a copy of which can be found below:

> Copyright &copy; 2008-10, Sean Werkema. All rights reserved.
>
> Portions copyright &copy; Vanilla Forums Inc. All rights reserved.
>
> Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
>
> - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
>
> - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
>
> THIS SOFTWARE IS PROVIDED BY SEAN WERKEMA AND VANILLA FORUMS INC. "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
