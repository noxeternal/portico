<?
/**
 * Heartland Portico Gateway CreditSale class.
 *
 * Class includes CreditSale functionality for both swiped and manually entered card information.
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
 * CreditSale extends portico for Credit Card transactions, both Manual Entry and Swiped
 *
 * @package porticoAPI
 *
 */
class CreditSale extends portico{
	/**
	 * Swipe is set to true if using TrackData from MSR card read and set to false if using Manually Entered card data.
	 * 
	 * @var bool 
	 */
	protected $swipe = false;
	/**
	 * Array of required properties.
	 *
	 * @var array
	 */
	protected $required = [
		'CreditSale' => [
			'Amt',
			'AllowDup',
			'AllowPartialAuth',
			// 'Version',
			'ClientTxnId',
			'DirectMktInvoiceNbr',
			'DirectMktShipDay',
			'DirectMktShipMonth',
			'CardNbr',
			'ExpMonth',
			'ExpYear',
			'CardPresent',
			'ReaderPresent',
			'CVV2',
			'CardHolderFirstName',
			'CardHolderLastName',
			'CardHolderZip'
		],
		'CreditSaleSwipe' => [
			'Amt',
			'AllowDup',
			'AllowPartialAuth',
			// 'Version',
			'ClientTxnId',
			'DirectMktInvoiceNbr',
			'DirectMktShipDay',
			'DirectMktShipMonth',
			'TrackData'
		]
	];
	/**
	 * Amount requested for authorization
	 * 
	 * @var string  
	 */
	var $Amt;
	/**
	 * Important in cases where the client processes a large number of similar transactions in 
	 * a very short period of time; sending "Y" will skip duplicate checking on this transaction
	 * 
	 * @var string  
	 */
	var $AllowDup;
	/**
	 * Indicates whether or not a partial authorization is supported by terminal
	 * 
	 * @var string  
	 */
	var $AllowPartialAuth;
	/**
	 * The encryption version used on the supplied data
	 * 
	 * @var string  
	 */
	var $Version;
	/**
	 * Client generated transaction identifier sent in the request of the original transaction
	 * 
	 * @var string  
	 */
	var $ClientTxnId;
	/**
	 * Invoice Number
	 * 
	 * @var string  
	 */
	var $DirectMktInvoiceNbr;
	/**
	 * Ship Day
	 * 
	 * @var string  
	 */
	var $DirectMktShipDay;
	/**
	 * Ship Month
	 * 
	 * @var string  
	 */
	var $DirectMktShipMonth;
	/**
	 * Card number; also referred to as Primary Account Number (PAN)
	 *
	 * @var string 
	 */
	var $CardNbr;
	/**
	 * Card expiration month
	 * 
	 * @var string  
	 */
	var $ExpMonth;
	/**
	 * Card expiration year
	 * 
	 * @var string  
	 */
	var $ExpYear;
	/**
	 * Indicates whether or not the card was present at the time of the transaction
	 * 
	 * @var string  
	 */
	var $CardPresent;
	/**
	 * Indicates whether or not a reader was present at the time of the transaction
	 * 
	 * @var string  
	 */
	var $ReaderPresent;
	/**
	 * CVV Number ("Card Verification Value"); 3 digits on VISA, MasterCard and Discover and 4 on American Express
	 * 
	 * @var string  
	 */
	var $CVV2;
	/**
	 * Cardholder First Name
	 *
	 * @var string  
	 */
	var $CardHolderFirstName;
	/**
	 * Cardholder Last Name
	 * 
	 * @var string  
	 */
	var $CardHolderLastName;
	/**
	 * Cardholder Billing Zip Code
	 * 
	 * @var string  
	 */
	var $CardHolderZip;
	/**
	 * Full magnetic stripe data
	 *
	 * @var string  
	 */
	var $TrackData;

/*********************************************************************************************/
###############################################################################################
	/**
	 * Builds CreditSale request and submit it to portico::Transaction
	 *
	 * @return CreditSaleResponse|FailureResponse formatted response from Heartland SOAP interface
	 */
	public function doTransaction($swipe = false)
	{
		$this->swipe = $swipe;

		$this->initRequest();

		$this->setTransaction([
			'CreditSale' => [
				'Block1' => array_merge(
					$this->buildBlock1(),
					$this->buildCardData()
				)
			]
		]);

		return $this->Transaction();
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets values for the 'Block1' array and returns the array
	 *
	 * @return array
	 */
	private function buildBlock1()
	{
		return [
			'Amt' 				=> $this->Amt,
			'AllowDup'			=> $this->AllowDup,
			'AllowPartialAuth'	=> $this->AllowPartialAuth,
			// 'EncryptionData' => [
			// 	'Version' => $this->Version
			// ],
			'DirectMktData' => [
				'DirectMktInvoiceNbr' 	=> $this->DirectMktInvoiceNbr,
				'DirectMktShipDay' 		=> $this->DirectMktShipDay,
				'DirectMktShipMonth' 	=> $this->DirectMktShipMonth
			]
		];
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Sets values for the 'CardData' array (and potentially 'CardHolderData' array) and returns the array
	 *
	 * @uses portico::swipe
	 *
	 * @return array 
	 */
	private function buildCardData()
	{
		return $this->swipe?[
			'CardData' => [
				'TrackData' => $this->TrackData,
				'method'	=> 'swipe'
			]
		]:[
			'CardData' => [
				'ManualEntry' => [
					'CardNbr' 		=> $this->CardNbr,
					'ExpMonth'	 	=> $this->ExpMonth,
					'ExpYear' 		=> $this->ExpYear,
					'CardPresent' 	=> $this->CardPresent,
					'ReaderPresent' => $this->ReaderPresent
				],
			],
			'CardHolderData' => [
				'CardHolderFirstName' 	=> $this->CardHolderFirstName,
				'CardHolderLastName' 	=> $this->CardHolderLastName,
				'CardHolderAddr' 		=> isset($this->CardHolderAddr)?$this->CardHolderAddr:null,
				'CardNbr' 				=> $this->CardNbr,
				'CardHolderState' 		=> isset($this->CardHolderState)?$this->CardHolderState:null,
				'CardHolderZip' 		=> $this->CardHolderZip,
				'CardHolderPhone' 		=> isset($this->CardHolderPhone)?$this->CardHolderPhone:null,
				'CardHolderEmail' 		=> isset($this->CardHolderEmail)?$this->CardHolderEmail:null
			]
		];
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Validates CreditSale object before attempting transaction
	 * 
	 * @throws Exception on invalid data
	 */
	protected function validate()
	{
		if($this->swipe)
			return true;

		$cardTypes = [
			3 => 'AMEX',
			4 => 'VISA',
			5 => 'MC',
			6 => 'DISC'
		];

		if(!in_array(substr($this->CardNbr, 0, 1), ['3','4','5','6']))
			throw new Exception('Invalid card brand identifier: '.substr($this->CardNbr, 0, 1));
		
		$ex = [];

		if($this->ExpYear < 2000)
			$this->ExpYear += 2000;
		
		switch($cardTypes[substr($this->CardNbr, 0, 1)])
		{
			case 'AMEX':
				if(strlen($this->CardNbr) != 15)
					$ex[] = 'Invalid AMEX card number length';
				if(strlen($this->CVV2) != 4)
					$ex[] = 'Invalid AMEX CVV length';
				break;

			case 'VISA':
			case 'MC':
			case 'DISC':
				if(strlen($this->CardNbr) != 16)
					$ex[] = 'Invalid '.$cardTypes[substr($this->CardNbr, 0, 1)].' card number length';
				if(strlen($this->CVV2) != 3)
					$ex[] = 'Invalid '.$cardTypes[substr($this->CardNbr, 0, 1)].' CVV length';
				break;

		}

		if($this->ExpYear.str_pad($this->ExpMonth, 2, '0', STR_PAD_LEFT) < date('Ym'))
			$ex[] = 'Expired Card';

		if(!in_array(strlen($this->CardHolderZip), [5,9]))
			$ex[] = 'Invalid Billing ZIP Code';

		if(count($ex) > 0)
			throw new Exception(implode("\n", $ex));

		return true;
	}
/*********************************************************************************************/
###############################################################################################
	/**
	 * Child class specific function to check for Transaction failures
	 *
	 * @param  object $response Raw Heartland Portico Gateway response
	 *
	 * @return object|boolean
	 */
	protected function checkFailure($response)
	{
		if(isset($response->{'Ver1.0'}->Transaction->CreditSale->RspCode) && $response->{'Ver1.0'}->Transaction->CreditSale->RspCode != 0)
		{	
			$failure = new FailureResponse($response, $this->request);
			$failure->Success  = false;
			$failure->FailType = 'Transaction';
			$failure->RspCode  = $response->{'Ver1.0'}->Transaction->CreditSale->RspCode;
			$failure->RspText  = $response->{'Ver1.0'}->Transaction->CreditSale->RspText;

			return $failure;
		}else{
			return false;
		}
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico CreditSaleResponse class
 *
 * Response object for CreditSale
 *
 * @package porticoAPI
 *
 */
class CreditSaleResponse extends porticoResponse{
	/**
	 * Authorization Code returned for successful transaction 
	 * 
	 * @var string
	 */
	var $AuthCode;
	/**
	 * Set to AMEX,DISC,MC, or VISA depending on card type processed 
	 * 
	 * @var string
	 */
	var $CardType;
	/**
	 * Response Code of transaction usually set to 0 
	 * 
	 * @var string
	 */
	var $TransRspCode;
	/**
	 * Response Code of transaction usually set to 'APPROVAL' 
	 * 
	 * @var string
	 */
	var $TransRspText;
	/**
	 * Check response and builds formatted object.
	 *
	 * @param array $response Request array generated by portico and sent to Heartland Portico Gateway
	 * @param string $request Complete SOAP XML from Heartland Portico Gateway
	 */
	function __construct($response, $request)
	{
		parent::__construct($response, $request);

		$this->AuthCode 	= $response->{'Ver1.0'}->Transaction->CreditSale->AuthCode;
		$this->CardType 	= $response->{'Ver1.0'}->Transaction->CreditSale->CardType;
		$this->TransRspCode = $response->{'Ver1.0'}->Transaction->CreditSale->RspCode;
		$this->TransRspText = $response->{'Ver1.0'}->Transaction->CreditSale->RspText;

	}
}

?>