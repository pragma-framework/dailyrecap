# Pragma Dailyrecap

Sending immediat mail or delayed mail.

## Configuration

In config.php add:

	define('PRAGMA_MODULES','core,dailyrecap');

And run in cron:

	php public/index.php dailyrecap:send

for sending email recap.
