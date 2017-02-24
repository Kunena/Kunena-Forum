# Detecting Kunena Version

This document is directed to extension developers who want to integrate their Joomla component, module or plugin with
Kunena. This document describes how to detect different versions of Kunena and how to make sure that you're safe to use
our API.

This document will only consider Kunena 2.0 or later versions. To detect older versions, please see
[our wiki article](https://www.kunena.org/docs/Detecting_Kunena_Version).

## Use Standard Methods

It is very important to use our standard methods to detect *Kunena*. Other methods may or may not work depending on the
installation.

Failing to detect Kunena version can lead to *various bugs*, *fatal errors* or in the worst case *data corruption*.
While the current version may work as expected, a simple upgrade may render your customers site totally unusable
because of a backwards incompatible change.

All the Kunena versions the same manifest file, which can be found from:

    administrator/components/com_kunena/kunena.xml

While the manifest file can be used to detect Kunena version, it's not the recommended to do so. Kunena uses
a system plugin to bootstrap our framework and we provide standard way to detect Kunena.

## Get Major Version

Usually it's enough to detect only major versions of Kunena. While there may be some new features introduced in
bugfix releases, its generally safe to assume that your existing code will also work on any bugfix release
(+0.0.1) that follows the version you have tested with.

If you only need to know major version of Kunena, here is the fastest way to detect all versions of Kunena.

## Check if Kunena is Online

*Kunena* integration should only be used if Kunena is online. This allows administrators to have one switch to
disable all Kunena integration, modules and plugins.

**WARNING:** When forum is offline, using Kunena API can be dangerous. For example administrator might be installing
a new version of Kunena, causing our API and database to be in undefined state. In most cases the effects of using
Kunena API can be seen as a white error pages, but any database update may also cause data corruptions.

If you want to keep the integration working even when ''Kunena Forum'' is offline, there's a way to check if Kunena
installer is running:

    // Kunena 2.0+
    $isInstalled = KunenaForum::installed();

**Note** that Kunena forum is automatically put also offline during the installation process.

### Kunena 2.0 / Kunena 3.0

In Kunena 2.0 or Kunena 3.0 the task of finding out if *Kunena* is set to be online is very easy.

    // Kunena 2.0+
    $isEnabled = KunenaForum::enabled();

## Minor Version Detection

Kunena 2.0 / Kunena 3.0 has *System - Kunena* plugin which automatically loads *KunenaForum* class, autoloader and
some defines. Kunena requires this plugin to be enabled at all times, so it is our official way to detect all newer
versions of Kunena.

    // Detects Kunena 2.0 or later version.
    if (class_exists('KunenaForum')) {

        // Get major version (x.y)
        $major = (string) KunenaForum::versionMajor();

        // Get current version (x.y.z)
        $version = (string) KunenaForum::version();

        // Make sure that your extension works with installed version of Kunena (use minimal required version),
        // if you want detect Kunena 3.0, just replace 2.0 by 3.0, You can also use version number like 2.0.4
        $compatible = (bool) KunenaForum::isCompatible('2.0');

        // Never try to use Kunena when its not fully installed!
        $isInstalled = (bool) KunenaForum::installed();

        // It's also a good idea to avoid using Kunena integration if Kunena is offline.
        // Function returns false also during installation.
        $enabled = (bool) KunenaForum::enabled();
    }

You can compare different Kunena versions by using PHP function version_compare().

**WARNING:** Do not use this code inside a system plugin contructor.
