<?php

namespace Ushahidi\App\DataSource\Twilio;

/**
 * Twilio Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSource;
use Ushahidi\Core\Entity\Contact;
use Services_Twilio, Services_Twilio_RestException;
use Log;

class Twilio extends DataSource {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::PHONE;

	/**
	 * Sets the FROM parameter for the provider
	 *
	 * @return int
	 */
	public function from()
	{
		// Get provider phone (FROM)
		// Replace non-numeric
		return preg_replace("/[^0-9,.]/", "", parent::from());
	}

	/**
	 * Client to talk to the Twilio API
	 *
	 * @var Services_Twilio
	 */
	private $_client;

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		if ( ! isset($this->_client))
		{
			$this->_client = new Services_Twilio($this->_options['account_sid'], $this->_options['auth_token']);
		}

		// Send!
		try
		{
			$message = $this->_client->account->messages->sendMessage($this->from(), '+'.$to, $message);
			return array(DataSource\Message\Status::SENT, $message->sid);
		}
		catch (Services_Twilio_RestException $e)
		{
			Log::error($e->getMessage());
		}

		return array(DataSource\Message\Status::FAILED, FALSE);
	}

}