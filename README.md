[![Kunena](https://www.kunena.org/images/logo.png)](https://www.kunena.org)  [![Build Status](https://travis-ci.org/Kunena/Kunena-Forum.svg?branch=K5.0)](https://travis-ci.org/Kunena/Kunena-Forum)

## ABOUT

*Kunena* is a native Joomla forum and communications component written in PHP. *Kunena* enables forums, bulletin board, support forums, discussions and comments for a Joomla base website.


## REQUIREMENTS

*Kunena* 5.1 requires

    Joomla: version 3.7.0 or greater (>= 3.7.0 recommended)
    PHP: version 5.6.8 or greater (>= 7.0.17 recommended)
    MySQL: version 5.1 or greater (>= 5.5 recommended)

Our installer will check for minimal version requirements and will abort the install if they are no met.

In addition we recommend the following PHP settings:

    max_execution_time     >= 30
    memory_limit           >= 32M  (>= 64M recommended) - depends on other Joomla extensions used
    safe_mode               = off
    allow_url_fopen		   = on
    upload_max_filesize    >= 3M
    GD, DOM, Mbstring, JSON support installed and OpenSSL only to embedded tweets

*Kunena* requires the following Joomla settings:

    * Bootstrap 2 compatible template (Crypsis Template)
    * Upgraded to latest versions all extensions that claim to integrate with Kunena 5.0
    * No plugins or modules that were developed for previous versions of Kunena or Fireboard


## EXAMPLES

If you are looking for examples on how *Kunena* works or can be installed, we recommend you checkout our team site at https://www.kunena.org


## INSTALLATION

*Kunena* is installed via the Joomla component/extension installer. You may download our releases from our downloads page at: https://www.kunena.org/download

The Joomla installers allows you to directly install *Kunena* via package URL or from a local download of yours.

As long as the minimum requirements are met, the Kunena install should take only a few moments. We have spent a great amount of time to automate the entire installation process.

Upgrades are performed just like new installs. There is no need to uninstall Kunena to perform an upgrade. We STRONGLY recommend that you perform a backup before and new software install or upgrade. The upgrade procedure is identical to a new install and is performed via the Joomla extension installer. *Kunena* will detect all prior version of Kunena and will perform the necessary upgrade tasks fully automatic.


## COMMUNITY

*Kunena* is a community built and maintained project. There is no commercial entity behind *Kunena*. We provide all our work for free and donate generous amounts of our time into the project.

As such *Kunena* itself does not offer commercial or paid for support. We provide our community with a support forum and in general you will find what you need on there. You can find the community support forums here: https://www.kunena.org/forum

If you are interested in commercial grade support we encourage you to check the Joomla Resource Directory at: http://resources.joomla.org/

The *Kunena* projects thrives on contributions from the community. Our dedicated volunteers spend countless hours every week to help the community.


## CONTRIBUTE

1. [Create an account on kunena.org](https://www.kunena.org/registration)
2. [Participate in our forum](https://www.kunena.org/forum)
3. [Checkout our Git repositories on github](https://github.com/Kunena)
4. [Read our documentation](https://docs.kunena.org)
5. [Read our developer wiki](https://github.com/Kunena/Kunena-Forum/wiki)
6. Send us a pull request


## LICENSE

[GNU General Public License v3 or later](https://www.gnu.org/copyleft/gpl.html)
