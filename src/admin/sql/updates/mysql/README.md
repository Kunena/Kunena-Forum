# Kunena database schema updates for MySQL

This directory contains files that are used to update Kunena database schema from older versions towards the latest version of Kunena.

Upgrade procedure supports previous Kunena releases untill 5.1 version, including all internal development versions.

## Automatic upgrade during installation

When new version of Kunena is installed to your Joomla! site, installer detects if the database contains an older version of Kunena. If older version was found, installer detects the installed version and performs all the nessessary steps to upgrade the database for you.
Data migration from older versions may take several minutes or even few hours in very large sites, resulting a fully working forum running the latest version of Kunena.

WARNING: If you have modified version of Kunena database, automatic installation may fail because of the database doesn't match to the assumed structure. In many cases the installer will be able to fix the issues just by running it the second time.

If automatic installation fails, you still have option to do manual database update. Manual update may also be nessessary in very large forums with millions of posts.

## How the installation works?

Database update contains two parts: structural schema updates and content updates. Both of them are run in sequel, starting from the installed version and moving version by version towards the latest one. 
For each version all the schema updates are run first following with the nessessary content updates (both SQL and PHP code). To make data migration faster, some tasks like recounting topics and posts are left to the final step of installation.

In short: Installing Kunena 6.0 into site which contains Kunena 5.2 database does the same as installing all the versions one by one.

