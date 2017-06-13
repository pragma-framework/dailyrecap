# Pragma Dailyrecap

Sending immediat mail or delayed mail.

## Configuration

In config.php add:

	define('PRAGMA_MODULES','core,dailyrecap');
	define('PRAGMA_MAIL_TEMPLATE', 'path/to/template/dailyrecap.php'); // optional
	define('PRAGMA_MAIL_SUBJECT', 'subject for daily recap'); // optional

And run in cron:

	php public/index.php dailyrecap:send

for sending email recap.
