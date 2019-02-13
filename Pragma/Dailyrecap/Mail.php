<?php
namespace Pragma\Dailyrecap;

use Mail_mail as PearMail;
use Mail_mime;

class Mail{
	const DEFAULT_CHARSET = 'UTF-8';

	public function __construct($from, $to, $subject, $content, $category = 0){
		$this->from = $from;
		$this->to = $to;
		$this->subject = $subject;
		$this->content = $content;
		$this->category = $category;
	}

	/**
	 * the sender
	 * @var string
	 *
user@example.com
User <user@example.com>
	 */
	protected $from;

	/**
	 * Receiver, or receivers of the mail.
	 * @var array
	 *
[ user@example.com, anotheruser@example.com ]
[ User <user@example.com>, Another User <anotheruser@example.com> ]
	 */
	protected $to;

	/**
	 * Subject of the mail
	 * @var string
	 */
	protected $subject;

	/**
	 * Content of the mail
	 * Full html body for sending now or partial content for daily recap
	 * @var string
	 */
	protected $content;

	/**
	 * Content of the mail
	 * Full text body for sending now or partial content for daily recap
	 * @var string
	 */
	protected $text_content;

	/**
	 * Category of the mail for daily recap
	 * @var integer
	 */
	protected $category = 0;

	/**
	 * Files to add as attachment
	 * @var array
	 */
	protected $files = [];

    /**
     * Custom headers, need to be prefixed by "X-" OR using Cc/Bcc/...
     * @var array
     */
    protected $specificHeaders = [];

	/**
	 * Gets the sender
user@example.com
User <user@example.com>.
	 *
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * Sets the sender
user@example.com
User <user@example.com>.
	 *
	 * @param string $from the from
	 *
	 * @return self
	 */
	public function setFrom($from)
	{
		$this->from = $from;

		return $this;
	}

	/**
	 * Gets the Receiver, or receivers of the mail
[ user@example.com, anotheruser@example.com ]
[ User <user@example.com>, Another User <anotheruser@example.com> ].
	 *
	 * @return array
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * Sets the Receiver, or receivers of the mail
[ user@example.com, anotheruser@example.com ]
[ User <user@example.com>, Another User <anotheruser@example.com> ].
	 *
	 * @param array $to the to
	 *
	 * @return self
	 */
	public function setTo(array $to)
	{
		$this->to = $to;

		return $this;
	}

	/**
	 * Gets the Subject of the mail.
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Sets the Subject of the mail.
	 *
	 * @param string $subject the subject
	 *
	 * @return self
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Gets the Content of the mail.
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Sets the Content of the mail.
	 *
	 * @param string $content the content
	 *
	 * @return self
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Gets the Content text of the mail.
	 *
	 * @return string
	 */
	public function getTextContent()
	{
		return $this->text_content;
	}

	/**
	 * Sets the Content text of the mail.
	 *
	 * @param string $content the text content
	 *
	 * @return self
	 */
	public function setTextContent($textContent)
	{
		$this->text_content = $textContent;

		return $this;
	}

	/**
	 * Gets the Category of the mail for daily recap.
	 *
	 * @return integer
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Sets the Category of the mail for daily recap.
	 *
	 * @param integer $category the category
	 *
	 * @return self
	 */
	public function setCategory($category = 0)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * Adds file as attachment
	 *
	 * @param array $file [ path, content_type, name ] The file to add
	 *
	 * @return self
	 */
	public function addAttachment($file)
	{
		$this->files[] = $file;

		return $this;
	}

    /**
     * Gets the Specfic Headers of the mail
     * @return array
     */
    public function getSpecificHeaders()
    {
        return $this->specificHeaders;
    }

    /**
     * Sets the Specfic Headers of the mail
     * @return self
     */
    public function setSpecificHeaders(Array $headers = [])
    {
        $this->specificHeaders = $headers;
        return $this;
    }

	/**
	 * Send email or store it for sending later
	 * @param  string $when A date/time string. See http://php.net/strtotime
	 */
	public function sendMail($when = "now") {
		$when = strtotime($when);
		if($when <= time()){
			$this->sendMailNow();
		}else{
			$this->queueMail($when);
		}
	}

	protected function sendMailNow(){
		$mime = new Mail_mime();

		defined('PRAGMA_RETURN_MAIL') || define('PRAGMA_RETURN_MAIL', 'no-reply@pragma-framework.fr');

		$mimeHeaders = array_merge(
            $this->specificHeaders,
            array(
                'Return-Path' => PRAGMA_RETURN_MAIL,
        		'From' => $this->from, // define default from
        		'Reply-to' => $this->from,
        		'To' => implode(', ',$this->to),
            )
    	);

		// Test & change email for debug
		if (defined('DEBUG_EMAIL_ADDRESS') && DEBUG_EMAIL_ADDRESS != '') {
			$this->subject .= " (Original recipient: ".$mimeHeaders['To'].")";
			$mimeHeaders['To'] = DEBUG_EMAIL_ADDRESS;
		}
		if(defined('PRAGMA_PREFIX_MAIL') && !empty(PRAGMA_PREFIX_MAIL)){
			$this->subject = trim(trim(PRAGMA_PREFIX_MAIL)." ".$this->subject);
		}

		// Deprecated: use specificHeaders
		if(defined('PRAGMA_COPY_EMAIL') && !empty(PRAGMA_COPY_EMAIL)) {
			$mimeHeaders['Bcc'] = PRAGMA_COPY_EMAIL;
		}

		$this->replaceHtmlImages($mime);

		$mime->setHTMLBody($this->content);
		if(empty($this->text_content)){
			$this->text_content = strip_tags(html_entity_decode($this->content));
		}
		$mime->setTXTBody($this->text_content);
		$mime->setSubject($this->subject);

		if (sizeof($this->files)) {
			foreach ($this->files as $file) {
				$mime->addAttachment($file['path'], $file['c_type'], $file['name']);
			}
		}

		$body = $mime->get(array(
			'text_charset' => self::DEFAULT_CHARSET,
			'html_charset' => self::DEFAULT_CHARSET,
			'head_charset' => self::DEFAULT_CHARSET,
		));
		$headers = $mime->headers($mimeHeaders);

		$mail = PearMail::factory('mail', '-f '.PRAGMA_RETURN_MAIL);
		return $mail->send($mimeHeaders['To'], $headers, $body);
	}

	protected function replaceHtmlImages(&$mime){
		// Add local images as attachment
		preg_match_all('/<img(.*?)src=("|\'|)(.*?)("|\'| )(.*?)>/s', $this->content, $images);
		if(!empty($images[3])){
			$replaceFiles = [];
			foreach($images[3] as $img){
				if(file_exists($img) && !parse_url($img, PHP_URL_SCHEME) && !isset($replaceFiles[$img])){ // we take only local file
					$cid = preg_replace('/[^0-9a-zA-Z]/', '', uniqid(time(), true));
					$filename = pathinfo($img, PATHINFO_BASENAME);
					if(!isset($replaceFiles[$img])){
						$mime->addHTMLImage($img, mime_content_type($img), $filename, true, $cid);
						$replaceFiles[$img] = "cid:".$cid;
					}
				}else{ // Test base64
					// data:image/png;base64,
					$imgE = explode(';', $img);
					if(count($imgE) == 2 && strpos(trim($imgE[0]), 'data:image/') === 0 && strpos(trim($imgE[1]), 'base64') === 0){
						$imgB64 = explode(',', $img);
						$imgB64 = trim(end($imgB64));
						if(!empty($imgB64)){
							$ext = trim(substr($imgE[0], 11));
							$cid = preg_replace('/[^0-9a-zA-Z]/', '', uniqid(time(), true));
							if(!isset($replaceFiles[$img])){
								$mime->addHTMLImage(base64_decode($imgB64), trim(substr($imgE[0], 5)), $cid.".".$ext, false, $cid);
								$replaceFiles[$img] = "cid:".$cid;
							}
						}
					}

				}
			}
			if(!empty($replaceFiles)){
				$this->content = str_replace(array_keys($replaceFiles), $replaceFiles, $this->content);
			}
		}
		return $mime;
	}

	protected function queueMail($when){
		return MailQueue::build(array(
			'from' => $this->from,
			'to' => json_encode($this->to),
			'subject' => $this->subject,
			'category' => $this->category,
			'html' => $this->content,
			'when' => date('Y-m-d',$when),
		))->save();
	}
}
