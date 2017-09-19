<?
/**
 * Heartland Portico Gateway CreditAddToBatch class.
 *
 * Class includes CreditAddToBatch functionality for authorized but not batched transactions.
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
 * CreditAddToBatch extends portico to add previously authorized Credit Card transactions to the current batch.
 *
 * @package porticoAPI
 *
 */
class CreditAddToBatch extends portico{
	/**
	 * Array of required properties.
	 *
	 * @var array
	 */
	protected $required = [
		'CreditAddToBatch' => [
			'GatewayTxnId', 'Amt'
		]
	];
	/**
	 * GatewayTxnId identifying authorization to add to current batch
	 * 
	 * @var string  
	 */
	var $GatewayTxnId;		
	/**
	 * Updated amount for settlement greater than 0 but less than original authorized amount
	 * 
	 * @var string  
	 */
	var $Amt;
/*********************************************************************************************/
###############################################################################################
	/**
	 * Builds CreditAddToBatch request and submit it to portico::Transaction
	 *
	 * @return CreditAddToBatchResponse|FailureResponse formatted response from Heartland SOAP interface
	 */
	public function doTransaction()
	{
		$this->initRequest();

		$this->setTransaction([
			'CreditAddToBatch' => [
				'GatewayTxnId' 	=> $this->GatewayTxnId,
				'Amt'			=> $this->Amt
			]
		]);
		
		return $this->Transaction();
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico CreditAddToBatchResponse class
 *
 * Response object for CreditAddToBatch
 *
 * @package porticoAPI
 *
 */
class CreditAddToBatchResponse extends porticoResponse{
}

?>