# Abmundi Database Commands Bundle #

This bundle provides a way to run a series of cdatabase commands in your Symfony application.
It provides one command line for our console, and 5 capifony tasks.

## Installation ##

Add the following code to your deps file:

    [ABMundiDatabaseCommandsBundle]
        git=git://github.com/ABMundi/DatabaseCommands.git
        target=bundles/Abmundi/DatabaseCommandsBundle

And then run the vendors install command:

    $ ./bin/vendors install

Then register the bundle in the `AppKernel.php` file:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Abmundi\DatabaseCommandsBundle\AbmundiDatabaseCommandsBundle(),
            ...
        );

        return $bundles;
    }

Then register the autoloader in the `autoload.php` file:

    $loader->registerNamespaces(array(
        ...
        'Abmundi'                       => __DIR__.'/../vendor/bundles',
        ...
    ));

## Requirements ##

This bundle needs (in local and remote server)

* mysql (command line)
* mysqldump (commandline)
* bunzip2 (commandline)

## Command line ##

Now from your console you can run

    ./app/console db:dump

and see that a new file has been saved in folder /app/fmp/dump with an hard link to the newest one.

## Capifony ##

If you have installed capifony this bundle provides to you 6 nice tasks:

* cap db:init - Set remote folders (run it first time)
* cap db:dump - Create a dump of db in remote folder
* cap db:download - Download last dump file in local folder
* cap db:import:production - Import remote db in local production db
* cap db:import:testing - Import remote db in local testing db
