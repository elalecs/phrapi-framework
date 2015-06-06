<? defined("PHRAPI") or die("Direct access not allowed!");

final class Mail{

	static function makeEmail($user_config = array()) {
		$global_config = isset($GLOBALS['config']['smtp']) && is_array($GLOBALS['config']['smtp']) ? $GLOBALS['config']['smtp'] : array();

		$mail_config = $user_config + $global_config + array(
			'from' => array(),
			'to' => array(),
			'cc' => array(),
			'bc' => array(),
			'reply' => array(),
			'subject' => '',
			'body' => '',
			'attach' => array(),
			'html' => true,
			'host' => 'localhost',
			'pass' => '',
		);

		$mail = new mail_PHPMailer();
		$mail->Host     = $mail_config['host'];
		$mail->SMTPAuth = true;
		$mail->Username = current($mail_config['from']);
		$mail->Password = $mail_config['pass'];

		$mail->From     = current($mail_config['from']);
		$mail->FromName = key($mail_config['from']);

		if (isset($mail_config['port'])) {
			$mail->Port = $mail_config['port'];
		}

		if(isset($mail_config['to']) && is_array($mail_config['to']) && sizeof($mail_config['to']))
			foreach ($mail_config['to'] as $_name => $_email)
				$mail->AddAddress($_email, $_name);

		if(isset($mail_config['cc']) && is_array($mail_config['cc']) && sizeof($mail_config['cc']))
			foreach ($mail_config['cc'] as $_name => $_email)
				$mail->AddCC($_email, $_name);

		if(isset($mail_config['bc']) && is_array($mail_config['bc']) && sizeof($mail_config['bc']))
			foreach ($mail_config['bc'] as $_name => $_email)
				$mail->AddBCC($_email, $_name);

		if(isset($mail_config['reply']) && is_array($mail_config['reply']) && sizeof($mail_config['reply']))
			foreach ($mail_config['reply'] as $_name => $_email)
				$mail->AddReplyTo($_email, $_name);

		if(isset($mail_config['attach']) && is_array($mail_config['attach']) && sizeof($mail_config['attach']))
			foreach ($mail_config['attach'] as $_name => $_filename)
				$mail->AddAttachment($_filename, $_name);

		$mail->Subject = $mail_config['subject'];

		$mail->Body = $mail_config['body'];

		$mail->IsHTML($mail_config['html']);

		$result = $mail->Send();

		return $result ? $result : $mail->ErrorInfo;
	}

	/**
	 * http://stackoverflow.com/questions/997078/email-regular-expression
	 * @param string $email
	 * @param boolean $skipDNS
	 * @return boolean
	 */
	static function validEmail($email, $skipDNS = false)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64)
			{
				// local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255)
			{
				// domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.')
			{
				// local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local))
			{
				// local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
				// character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain))
			{
				// domain part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
			{
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
				{
					$isValid = false;
				}
			}

			if(!$skipDNS)
			{
				if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
				{
					// domain not found in DNS
					$isValid = false;
				}
			}
		}
		return $isValid;
	}
}