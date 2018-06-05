# Migrating from Joomla! 1.5

Migrating Kunena (or FireBoard) from the old site contains the following six steps:

* Migrating users (SQL tables)
* Migrating forums (SQL tables)
* Migrating media files (attachments, avatars etc)
* Installing Kunena 3.0
* Changing category permissions
* Cleanup

As Joomla users cannot be just copied from Joomla! 1.5 into Joomla! 2.5 or 3.x, you always need to start by using
one of the Joomla migration tools in
[Joomla! Extensions Directory](https://extensions.joomla.org/extensions/migration-a-conversion/joomla-migration).

From the listed extensions, only latest versions of JUpgrade are able to migrate Kunena 2.0 automatically to your new
site. The tool also has newer PRO version available. If you choose to use JUpgrade and the migration succeeds without
any issues, you do not need to read any further. Just install Kunena 3.0 into your new site and you're done!

## Manual migration

If you had issues with JUpgrade or choose to make a new site instead of importing all the data, you will need to
migrate Kunena manually.

I recommend you to try out the migration and make some notes before putting the site offline and doing the real
upgrade. In that way you can create a test site for users to play and to report all potential issues to you.

When you're confident that everything will work out, make sure that you have enough time to complete the migration and
do the same for your live site. It's very important to use the latest user and forum data, so please put your site
offline and do yet another user data migration before you continue migrating the forum data.

### Step 1: Migrate Joomla! 1.5 content and users

Migrating Joomla and its users is out of scope of this document. Please refer to the documentation of migration tools
on how to do it.

### Step 2: Copy Kunena tables

Once you have your new Joomla! 2.5 / 3.x site with all the content and template up and running, manually copy in all
the Kunena tables and rename jos to the new prefix of your choice. All the tables have the following naming:

    jos_kunena_* (Kunena 1.6, 1.7 and 2.0)
    jos_fb_* (FireBoard and Kunena 1.0, 1.5)

### Step 3: Copy Kunena media files

Next task is to copy all the media files to the new installation. These files include avatars and attachments.

    media/kunena
    images/fbfiles (FireBoard and Kunena < 1.6 only)
    components/com_fireboard (FireBoard < 1.0.5 only)

Easiest way is to find and copy all existing directories listed above. During the installation Kunena will copy all the
files to a new location, so directories belonging to FireBoard can be removed later during the cleanup phase.

Please make sure that all the copied files and directories have the correct permissions. If Joomla doesn't have read
and write permissions to all the files, Kunena installer will fail to copy the files.

### Step 4: Install Kunena 3.0

With the tables and directories copied make another backup of the files and the database so you can go back in case
something goes wrong.

Now simply install Kunena 3.0 on the new site. Kunena will do a nice upgrade and will find all the files, posts and
categories.

### Step 5: Changing category permissions

After previous step you might have noticed that most of your categories are not visible in the forum. That's what we
expect to have as Joomla has changed how the permissions work somewhere between J!1.5 and J!2.5.

To fix the issue, you need to visit every category and change it's permissions to match the ones you have in your
new site.

### Step 6: Cleanup

If you copied the following directories, just remove them as they aren't needed anymore:

    images/fbfiles
    components/com_fireboard

If you upgraded from older version of Kunena or FireBoard, go to your database and remove all the matching tables:

    xxx_fb_* (where xxx is your prefix)

That's it!
