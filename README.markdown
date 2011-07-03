Kunena
======

*Kunena* is a native Joomla forum and communications component. *Kunena* enables forums, bulletin board, support forums, discussions and comments for a Jooomla base website.

*Kunena* is developed by an Open Source team of collaborators and distributed under the GPL2 license. There is no commercial entity behind this. We do this in our spare time and for fun. 


Requirements
------------

*Kunena* 2.x requires (this section is work in progress until Kunena 2.0 has been released)

    Joomla 1.5.23+ or Joomla 1.6.3+
    PHP 5.3+
    MySQL 5.x+

Our installer will check for minimal version requirements and will abort the install if they are no met.

In addition we recommend the following PHP settings:

    max_execution_time     >= 30
    memory_limit           >= 16M  (>= 64M recommended) - depends on other Joomla extensions used
    safe_mode               = off
    upload_max_filesize    >= 3M
    GD, DOM, JSON support installed

*Kunena* requires the following Joomla settings:

    * Mootools 1.2 compatible template
    * Upgraded to latest versions all extensions that claim to integrate with Kunena 1.6
    * No plugins or modules that were developed for previous versions of Kunena or Fireboard


Examples
--------

If you are looking for examples on how *Kunena* works or can be installed, we recommend you checkout our team site at www.kunena.org

We have setup a Playground for users and developers to try out various features of Kunena: http://www.kunena.org/playground

Most of our modules and Kunena extensions are installed at: http://www.kunena.org/showcase


Installation
------------

*Kunena* is installed via the Joomla component/extension installer. You may download our builds/packages from joomlacode.org via: http://joomlacode.org/gf/project/kunena/frs/ or from our downloads manager at: http://www.kunena.org/download

The Joomla installers allows you to directly install *Kunena* via package URL or from a local download of yours.

As long as the minimum requirements are met, the Kunena install should take only a few moments. We have spent a great amount of time to automate the entire installation process.

Upgrades are performed just like new installs. There is no need to uninstall Kunena to perform an upgrade. We STRONGLY recommend that you perform a backup before and new software install or upgrade. The upgrade procedure is identical to a new install and is performed via the Joomla extension installer. *Kunena* will detect all prior version of Kunena and will perform the necessary upgrade tasks fully automatic. 


Support
-------

*Kunena* is a community built and maintained package. There is no commercial entity behind the project. We provide all our work for free and donate generous amounts of our time into the project.

As such *Kunena* itself does not offer commercial or paid for support. We provide our community with a support forum and in general you will find what you need on there. You can find the community support forums here: www.kunena.org/forum

If you are interested in commercial grade support we encourage you to check the Joomla Resource Directory at: http://resources.joomla.org/

The *Kunena* projects thrives on contributions from the community. Our dedicated volunteers spend countless hours every week to help the community.

If you have something to contribute, we encourage you to do so at www.kunena.org or on github at: https://github.com/Kunena