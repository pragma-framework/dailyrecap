<?php
namespace Pragma\Dailyrecap;

use Mail as PearMail;
use Mail_mime;

class Mail{
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
	 * Category of the mail for daily recap
	 * @var integer
	 */
	protected $category = 0;

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

    	$mimeHeaders = array(
    		'From' => $this->from, // define default from
    		'Reply-to' => $this->from,
    		'To' => implode(', ',$this->to),
    	);

    	$mime->setHTMLBody($this->content);
    	$mime->setTXTBody(strip_tags(html_entity_decode($this->content)));
    	$mime->setSubject($this->subject);

    	$body = $mime->get(array(
    		'text_charset' => 'UTF-8',
    		'html_charset' => 'UTF-8',
    		'head_charset' => 'UTF-8',
    	));
    	$headers = $mime->headers($mimeHeaders);

    	$mail = PearMail::factory('mail', '-f no-reply@pragma-framework.fr'); // define _MONITORING_RETURN_MAIL
    	return $mail->send($mimeHeaders['To'], $headers, $body);
    }

    protected function queueMail($when){
    	$mq = MailQueue::build(array(
			'from' => $this->from,
			'to' => json_encode($this->to),
			'subject' => $this->subject,
			'category' => $this->category,
			'html' => $this->content,
			'when' => date('Y-m-d',$when),
		))->save();
    }
}