<?php
	require_once 'naLogger.php';

	$logger = new naLogger('etc/log.txt', naLogger::DEBUG);

	$logger->logEmerg('Database crashed.', 'Database');
	$logger->logAlert('Loadtime over 5s!', 'Server');
	$logger->logCrit('Diskspace under 2MB.', 'Drive');
	$logger->logErr('Could not upload file: "test_04.pdf"', 'Uploader');
	$logger->logWarn('Variable $user_id was not initalized.', 'PHP');
	$logger->logNotice('User Login failed.', 'User');
	$logger->logInfo('Cronjob "test" started.', 'Cronjob');
	$logger->logDebug('User-Hash: 838hshf82bd01()', 'Usermodule');



?>