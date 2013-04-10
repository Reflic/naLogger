naLogger
==============================


Usage
-----
### Directly

### With Composer
Create a composer.json with this content:


If you already have a composer.json add this to the *require* section: ``


Then to autoload the libary add this to your main PHP file. (In most frameworks like Laravel or Symfony this file is already loaded.)
```php
<?php
require 'vendor/autoload.php';

$logger = new naLogger('etc/log.txt', naLogger::DEBUG);

$logger->logEmerg('Database crashed.', 'Database');
$logger->logAlert('Loadtime over 5s!', 'Server');
$logger->logCrit('Diskspace under 2MB.', 'Drive');
$logger->logErr('Could not upload file: "test_04.pdf"', 'Uploader');
$logger->logWarn('Variable $user_id was not initalized.', 'PHP');
$logger->logNotice('User Login failed.', 'User');
$logger->logInfo('Cronjob "test" started.', 'Cronjob');
$logger->logDebug('User-Hash: 838hshf82bd01()', 'Usermodule');
```
