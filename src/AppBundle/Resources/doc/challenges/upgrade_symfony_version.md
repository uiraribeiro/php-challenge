Upgrade Symfony Version
========================
Challenge.
----------
Upgrade the symfony version from 2.8 to a newer version (which is up to you, but it should be something near LTS), you should also try upgrade all dependencies, after you are done outline the steps required to modernize the application. 

Note
----
At some point you might need to upgrade the php version, bumping the alpine image to a higher version should do the trick.

The symfony deprecation helper which comes with simple-phpunit has been disabled. To enable remove the env variable from app/phpunit.xml

```
    <php>
        <ini name="error_reporting" value="-1" />
        <!--
            <server name="KERNEL_DIR" value="/path/to/your/app/" />
        -->
        <env name="SYMFONY_DEPRECATIONS_HELPER" value="disabled" />
    </php>
``` 