<?
/**
 * Heartland Portico Gateway interface classes.
 *
 * Primary abstract class 'portico' supplied in this file will allow connection to the Heartland Porico Gateway. Extension classes include:
 *  1. BatchClose
 *  2. CreditAddToBatch
 *  3. CreditAuth
 *  4. CreditReturn
 *  5. CreditSale
 *  6. CreditVoid
 *  7. ReportBatchDetail
 *  8. ReportBatchSummary
 *  9. ReportTxnDetail
 *
 * 
 * @author Michael Rice <rice.michaelt@gmail.com>
 * 
 * @copyright 2015 Michael Rice 
 * @license http://www.gnu.org/licenses/ GNU Lesser Public License (LGPL)
 *
 * @version 0.5.0
 *
 * This program is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU Lesser Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or  
 * (at your option) any later version.  
 *   
 * This program is distributed in the hope that it will be useful,  
 * but WITHOUT ANY WARRANTY; without even the implied warranty of  
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the  
 * GNU Lesser Public License for more details.  
 *   
 * You should have received a copy of the GNU Lesser Public License  
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.  
 *
 */
##*******************************************************************************************##
/**
 * Automatically load '.class.php' files located in the same directory as portico.class.php
 * porticoAutoload() is then passed to spl_autoload_register() to preserve correct casing; default 
 * spl_autoload() forces class names to lowercase before attempting to locate the file
 *
 * @param string $className PHP automatically supplies $className when class is instantiated (using 'new')
 * 
 * @package porticoAPI
 *
 */
function porticoAutoload($className)
{
	@require_once(__DIR__.DIRECTORY_SEPARATOR.$className.'.class.php');
}
spl_autoload_register('porticoAutoload');
##*******************************************************************************************##
/**
 * porticoAutoloadFail will return custom failure instead of E_COMPILE_ERROR on missing porticoAPI child classes
 *
 * @package porticoAPI
 *
 */
function porticoAutoloadFail()
{
	$e = error_get_last();
	$bool = $e['type'] == E_COMPILE_ERROR && 
			substr($e['message'], 0, 12) == 'require_once' && 
			strpos($e['message'], '.class.php') > 0
			?true:false;

	if($bool === true)
	{
		preg_match('/portico\/(?P<className>.*)\.class\.php/',$e['message'],$m);
		die('API Error: '.$m['className'].' not found in porticoAPI directory. (Class names are case sensitive.)');
	}else{
		return false;
	}
}
register_shutdown_function('porticoAutoloadFail');
##*******************************************************************************************##
/**
 * Abstract Heartland Portico gateway interface
 *
 * Heartland Portico Gateway (fka POSGateway) generic interface abstract class
 *
 * @package porticoAPI
 *
 */
abstract class portico{
	/** 
	 * Allows portico system to be put in to DEBUG mode for additional testing:
	 * Affects porticoResponse::FullRequest
	 */
	const DEBUG 	= false;
	/**
	 * GATEWAY Heartland Portico Gateway SOAP Interface address
	 */
	const GATEWAY 	= 'https://posgateway.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx?wsdl';
	/** 
	 * Will override default gateway for certifications or testing
	 *
	 * @var string
	 */
	protected $GATEWAY;
	/** 
	 * Request to be sent to Gateway
	 *
	 * @var array
	 */
	protected $request;
	/**
	 * String containing name of functional child class called
	 * 
	 * @var string
	 */
	protected $class;
	/**
	 * Common list of required properties
	 *
	 * @var array
	 */
	protected $requiredCommon	= [
		'SiteId',
		'DeviceId',
		'LicenseId',
		'UserName',
		'Password',
		'DeveloperId',
		'VersionNbr'
	];
	/**
	 * SiteId Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $SiteId;
	/**
	 * LicenseId Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $LicenseId;
	/**
	 * DeviceId Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $DeviceId;
	/**
	 * UserName Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $UserName;
	/**
	 * Password Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $Password;
	/**
	 * DeveloperId Issued by Heartland Payment Systems
	 *
	 * @var string
	 */
	var $DeveloperId;
	/**
	 * VersionNbr Issued by Heartland Payment Systems
	 * 
	 * @var string
	 */
	var $VersionNbr;
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets portico::class and set initial values for class variables from optional $defaults array
	 *
	 * @param array $defaults Array of key => value pairs to set default or generic values
	 * 
	 * @return void 
	 */
	public function __construct($defaults = [])
	{
		$this->class = get_class($this);

		$vars = array_keys(get_class_vars(get_class($this)));

		if(count($defaults) > 0)
			foreach($defaults as $k => $v)
				if(in_array($k, $vars))
					$this->$k = $v;

	}
###############################################################################################
	/**
	 * Sets initial values in $request including header
	 *
	 * @return void
	 */
	protected function initRequest()
	{
		$this->validateFields();

		$this->request = [
			'PosRequest' => [
				'Ver1.0' => [ 
					'Header' => $this->buildHeader(),
					'Transaction' => []
				]
			]
		];

		return;
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets the 'Header' array and returns the array directly
	 *
	 * @return array Containing values for Header array
	 */
	private function buildHeader()
	{
		return 	[
			'SiteId' 		=> $this->SiteId,
			'DeviceId' 		=> $this->DeviceId,
			'LicenseId' 	=> $this->LicenseId,
			'UserName' 		=> $this->UserName,
			'Password' 		=> $this->Password,
			'DeveloperId'	=> $this->DeveloperId,
			'VersionNbr'	=> $this->VersionNbr
		];
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets the 'Transaction' array directly to the $request object based on $transaction sent
	 *
	 * @param array $transaction transaction element from child class doTransaction() function
	 *
	 * @return void
	 */
	protected function setTransaction($transaction)
	{
		$this->request['PosRequest']['Ver1.0']['Transaction'] = $transaction;
		return;
	}
/********************************************************************************************* /
	private function debugTransaction()
	{
		return;
		$return = false;

		try{
			$client = new SoapClient(self::GATEWAY, array('trace'=>1, 'exceptions'=>1)); 
	       	$response = $client->__soapCall('DoTransaction',$this->request);
		}catch(SoapFault $sf){
			$return = [
				'request' 	=> $client->__getLastRequest(),
				'response'	=> $sf->getTrace()
			];
		}

		return $return?$return:[
			'request' 	=> $client->__getLastRequest(),
			'response'	=> $client->__getLastResponse()
		];
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sends transaction $request to Heartland Portico Gateway for processing after validating fields
	 *
	 * @return porticoResponse Formatted response based on child class {className}Resposne object
	 */
	protected function Transaction()
	{
		/* return $this->debugTransaction(); */

		if(isset($this->GATEWAY) && $this->GATEWAY != '')
			$gw = $this->GATEWAY;
		else
			$gw = self::GATEWAY;

		// die(json_encode(['default' => self::GATEWAY, 'set' => $this->GATEWAY, 'used' => $gw]));

		try{
			$client = new SoapClient($gw, array('trace'=>1, 'exceptions'=>1)); 
	       	$response = $client->__soapCall('DoTransaction',$this->request);
		}catch(Exception $e){
			return new FailureResponse($e, null);
		}catch(SoapFault $sf){
			return new FailureResponse($sf, $client->__getLastRequest());
		}

		/* GATEWAY FAILURE */
		if(isset($response->{'Ver1.0'}->Header->GatewayRspCode) && $response->{'Ver1.0'}->Header->GatewayRspCode != 0)
			return new FailureResponse($response, $this->request);

		/* CHECK FOR FAILURES use function in child classes */
		$failure = method_exists($this, 'checkFailure') && is_callable([$this, 'checkFailure'])?$this->checkFailure($response):false;

		if($failure !== false)
			return $failure;

		$responseClass = $this->class.'Response';

		return new $responseClass($response, $this->request);
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Validates $request object by verifying that all required fields have values,
	 * then testing those values through validation for current class instance
	 *
	 * @uses array merged(portico::requiredCommon, child class::required) as array of required field names
	 *
	 * @return void
	 *
	 * @throws Exception if required field is not set, empty, or fails validate function (if exists)
	 */
	protected function validateFields()
	{
		$function = $this->class;
		if($function == 'CreditSale' && $this->swipe)
			$function .= 'Swipe';

		$fields = array_merge($this->requiredCommon,$this->required[$function]);

		foreach($fields as $var)
			if(!isset($this->$var) || empty($var))
				throw new Exception($function.' borked: '.$var);

		if(method_exists($this, 'validate') && is_callable([$this,'validate']))
			$this->validate();

		return;
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets GATEWAY to override default in cases of testing or certification
	 *
	 * @param string $gateway link to new online gateway
	 *
	 * @return void
	 */
	public function setGateway($gateway)
	{
		if($gateway != '')
			$this->GATEWAY = $gateway;

		return;
	}
/*********************************************************************************************/
###############################################################################################
}
##*******************************************************************************************##
/**
 * Abstract Heartland Portico Response class
 *
 * Common response object
 *
 * @package porticoAPI
 *
 */
abstract class porticoResponse{
	/**
	 * Set to true if transaction was successful, false if transaction failed or error
	 * 
	 * @var boolean 
	 */
	var $Success;
	/**
	 * Set to transaction type submitted to the gateway
	 * 
	 * @var string 
	 */
	var $TransType;
	/**
	 * Gateway Response code for failure 
	 * 
	 * @var int 
	 */
	var $RspCode;
	/**
	 * Gateway Response string for failure 
	 * 
	 * @var string 
	 */
	var $RspText;
	/**
	 * Gateway Transaction ID 
 	 * 
	 * @var string 
	 */
	var $TxnId;
	/**
	 * Complete SOAP XML from Heartland Portico Gateway (Only set if portico is in DEBUG mode) 
	 * 
	 * @var string 
	 */
	var $FullResponse;
	/**
	 * Request array generated by portico and sent to Heartland Portico Gateway (Only set if portico is in DEBUG mode) 
	 * 
	 * @var array 
	 */
	var $FullRequest;
	/**
	 * Called from child classes to set common response properties
	 *
	 * @uses portico::DEBUG Includes FullRequest property if portico is in DEBUG mode
	 * 
	 * @param array $response Request array generated by portico and sent to Heartland Portico Gateway
	 * @param string $request Complete SOAP XML from Heartland Portico Gateway
	 */
	protected function __construct($response, $request = '')
	{
		$this->Success      = true;
		$this->TransType    = preg_replace('/Response$/', '', get_class($this));
		$this->RspCode      = $response->{'Ver1.0'}->Header->GatewayRspCode;
		$this->RspText      = $response->{'Ver1.0'}->Header->GatewayRspMsg;
		$this->TxnId        = $response->{'Ver1.0'}->Header->GatewayTxnId;
		$this->FullResponse = $response;

		if(portico::DEBUG)
		{
			$this->FullRequest  = $request;
		}
	}
##*******************************************************************************************##
	/**
	 * Formats Portico UTC dates to YYYY-MM-DD hh:mm:ss format.
	 *
	 * @param string UTC formatted date-time to be reformatted
	 * 
	 * @return string
	 *
	 */
	protected function formatUTCDate($date)
	{
		return date('Y-m-d H:i:s', strtotime($date));
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico FailureResponse class extending porticoResponse
 *
 * Response object for transaction failures
 *
 * @package porticoAPI
 *
 */
class FailureResponse extends porticoResponse{
	/**
	 * Contains one of 'SOAPFault', 'Gateway', or 'Transaction'
	 * 
	 * @var string 
	 */
	var $FailType;
	/**
	 * Check response and builds formatted object.
	 *
	 * @uses portico::DEBUG Includes FullResponse property if portico is in DEBUG mode
	 * 
	 * @param array $response Request array generated by portico and sent to Heartland Portico Gateway
	 * @param string $request Complete SOAP XML from Heartland Portico Gateway
	 */
	function __construct($response, $request = '')
	{
		parent::__construct($response, $request);
		$this->Success  = false;

		if(is_object($response) && get_class($response) == 'SoapFault')
		{
			$this->FailType = 'SOAPFault';
			$this->RspCode  = $response->faultcode;
			$this->RspText  = $response->faultstring;
			$this->TxnId    = NULL;
			if(portico::DEBUG)
			{
				$this->FullResponse = $response->getTrace();
				$this->FullRequest = $request;
			}
		}

		if(!isset($this->FailType))
			$this->FailType = 'Gateway';

		// ELSE MANUALLY SET VIA checkFailure() //
	}
}

?>