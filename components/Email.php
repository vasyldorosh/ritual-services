<?php

namespace app\components;

use yii\base\Component;

class Email extends Component {
	/**
	 * @var array attach files
	 */
	public $files = array();
	/**
	 * @var string Type of email.  Options include "text/html" and "text/plain"
	 */
	public $type = 'text/html';
	/**
	 * @var string Receiver, or receivers of the mail.
	 */
	public $to = null;
	
	/**
	 * @var string Email subject
	 */
	public $subject = '';
	
	/**
	 * @var string from address
	 */
	public $from = null;
	
	/**
	 * @var string Reply-to address
	 */
	public $replyTo = null;
	
	/**
	 * @var string Return-path address
	 */
	public $returnPath = null;
	
	/**
	 * @var string Carbon Copy
	 *
	 * List of email's that should receive a copy of the email.
	 * The Recipient WILL be able to see this list
	 */
	public $cc = null;

	/**
	 * @var string Blind Carbon Copy
	 *
	 * List of email's that should receive a copy of the email.
	 * The Recipient WILL NOT be able to see this list
	 */
	public $bcc = null;
	
	/**
	 * @var string Main content
	 */
	public $message = '';
	
	/**
	 * @var string Delivery type.  If set to 'php' it will use php's mail() function, and if set to 'debug'
	 * it will not actually send it but output it to the screen
	 */
	public $delivery = 'php';
	
	/**
	 * @var string language to encode the message in (eg "Japanese", "ja", "English", "en" and "uni" (UTF-8))
	 */
	public $language= 'uni';
	
	/**
	 * @var string the content-type of the email
	 */
	public $contentType= 'utf-8';
	
	/**
	 * @var string The view to use as the content of the email, as an alternative to setting $this->message.
	 * Must be located in application.views.email directory.  This email object is availiable within the view
	 * through $email, thus letting you define things such as the subject within the view (helps maintain 
	 * seperation of logic and output).
	 */
	public $view = null;
	
	/**
	 * @var array Variable to be sent to the view.
	 */
	public $viewVars = null;	
	
	/**
	 * @var string The layout for the view to be imbedded in. Must be located in
	 * application.views.email.layouts directory.  Not required even if you are using a view
	 */
	public $layout = null;
	
	/**
	 * @var integer line length of email as per RFC2822 Section 2.1.1
	 */
	public $lineLength = 70;
	
	public function __construct() {
	}
	
	/**
	 * Sends email.
	 * @param mixed the content of the email, or variables to be sent to the view.
	 * If not set, it will use $this->message instead for the content of the email
	 */
	public function send() {
		//process 'to' attribute
		$to = $this->processAddresses($this->to);
		
		return $this->mail($to, $this->subject, $this->message);
	}
	
	private function mail($to, $subject, $message) {

		$message = wordwrap($message, $this->lineLength);
		mb_language($this->language);

		//$to = "dorosh_2009@meta.ua"; //Кому
		//$from = "noreply@atlant-m.com.ua"; //От кого
		$charset = 'UTF-8';

		// include the from email in the headers
		$headers = "From: $this->from";
		$headers.= "\ncc: {$this->processAddresses($this->cc)}";
		//$headers.= "\nbcc: {$this->processAddresses($this->bcc)}";
		$headers.= "\nreplyTo: {$this->processAddresses($this->replyTo)}";

		// boundary
		$time = md5(time());
		$boundary = "==Multipart_Boundary_x{$time}x";

		// headers used for send attachment with email
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$boundary}\"";
			
		// multipart boundary
		$message = "--{$boundary}\n" . "Content-Type: text/html; charset=\"$charset\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		$message .= "--{$boundary}\n";

		foreach ($this->files as $filepath => $filename) {
			if (is_file($filepath)) {
				$file = fopen($filepath,"r");
				$content = fread($file,filesize($filepath));
				fclose($file);
				$content = chunk_split(base64_encode($content));
				$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$filename\"\n" . "Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" . "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
				$message .= "--{$boundary}\n";
			}
		}
		
		return mail($to, $subject, $message, $headers);
	}
	private function createHeaders() {
		$headers = array();
		
		//maps class variable names to header names
		$map = array(
			'from' => 'From',
			'cc' => 'Cc',
			'bcc' => 'Bcc',
			'replyTo' => 'Reply-To',
			'returnPath' => 'Return-Path',
		);
		foreach ($map as $key => $value) {
			if (isset($this->$key))
			$headers[] = "$value: {$this->processAddresses($this->$key)}";
		}
		$headers[] = "Content-Type: {$this->type}; charset=".$this->contentType;
		$headers[] = "MIME-Version: 1.0";
		
		return $headers;
	}
	private function processAddresses($addresses) {
		return (is_array($addresses)) ? implode(', ', $addresses) : $addresses;
	}
}