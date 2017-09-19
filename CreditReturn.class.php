<?
/**
 * Heartland Portico Gateway CreditReturn class.
 *
 * Class includes CreditReturn functionality using original GatewayTxnId and Amt to return.
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
 * CreditReturn extends portico to return partial or full amounts to previously completed, successful transaction  
 * regardless of current batch status (but recommended only for Closed Batches).
 *
 * @package porticoAPI
 *
*/
class CreditReturn extends portico{
	/**
	 * Array of required properties.
	 *
	 * @var array
	 */
	protected $required = [
		'CreditReturn' => [
			'GatewayTxnId','Amt'
		]
	];
	/**
	 * GatewayTxnId identifying transaction to return
	 * 
	 * @var string  
	 */
	var $GatewayTxnId;		
	/**
	 * Amount to return to cardholder
	 * 
	 * @var string  
	 */
	var $Amt;
/*********************************************************************************************/
###############################################################################################
	/**
	 * Builds CreditReturn request and submit it to portico::Transaction
	 *
	 * @return CreditReturnResponse|FailureResponse formatted response from Heartland SOAP interface
	 */
	public function doTransaction()
	{
		$this->initRequest();

		$this->setTransaction([
			'CreditReturn' => [
				'Block1' => [
					'GatewayTxnId' 	=> $this->GatewayTxnId,
					'Amt'			=> $this->Amt
				]
			]
		]);
		
		return $this->Transaction();
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico CreditReturnResponse class
 *
 * Response object for CreditReturn
 *
 * @package porticoAPI
 *
 */
class CreditReturnResponse extends porticoResponse{
}

?>