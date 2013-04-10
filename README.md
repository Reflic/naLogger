naLogger
==============================


Usage
-----
### Directly
Download the `src/` directory and place it into your working directory.
then include the file `naLogger.php`

```php
<?php
require 'src/naLogger.php';
...
```

### With Composer
Create a composer.json with this content:

```json
{
    "require": {
        "reflic/na-logger": "v1.0.0"
    }
}
```

If you already have a composer.json add this to the *require* section: `"reflic/na-logger": "v1.0.0`


Then to autoload the libary add this to your main PHP file. (In most frameworks like Laravel or Symfony this file is already loaded.)
```php
<?php
require 'vendor/autoload.php';

// Create the log object.
$logger = new naLogger('etc/log.txt', naLogger::DEBUG);

// Log some messages.
$logger->logEmerg('Database crashed.', 'Database');
$logger->logAlert('Loadtime over 5s!', 'Server');
$logger->logNotice('User Login failed.', 'User');
$logger->logInfo('Cronjob "test" started.', 'Cronjob');
$logger->logDebug('User-Hash: 838hshf82bd01()', 'Usermodule');
```
