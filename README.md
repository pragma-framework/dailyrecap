# Pragma Dailyrecap

Sending immediat mail or delayed mail.

## Configuration

In config.php add:

	define('PRAGMA_MODULES','core,dailyrecap');
	define('PRAGMA_RETURN_MAIL', 'return email if delivery error');
	define('PRAGMA_MAIL_TEMPLATE', 'path/to/template/dailyrecap.php'); // optional
	define('PRAGMA_MAIL_SUBJECT', 'subject for daily recap'); // optional
	define('PRAGMA_REPLY_MAIL', 'reply to email'); // optional, default is from

And run in cron:

	php public/index.php dailyrecap:send

for sending email recap.
