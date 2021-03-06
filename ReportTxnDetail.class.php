<?
/**
 * Heartland Portico Gateway ReportTxnDetail class.
 *
 * Class includes ReportTxnDetail functionality using original TxnId
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
 * ReportTxnDetail extends portico to return previous transaction details.
 *
 * @package porticoAPI
 *
*/
class ReportTxnDetail extends portico{
	/**
	 * Array of required properties.
	 *
	 * @var array
	 */
	protected $required = [
		'ReportTxnDetail' => [
			'TxnId'
		]
	];
	/**
	 * TxnId identifying transaction to void
	 * 
	 * @var string  
	 */
	var $TxnId;		
/*********************************************************************************************/
###############################################################################################
	/**
	 * Builds ReportTxnDetail request and submit it to portico::Transaction
	 *
	 * @return ReportTxnDetailResponse|FailureResponse formatted response from Heartland SOAP interface
	 */
	public function doTransaction()
	{
		$this->initRequest();

		$this->setTransaction([
			'ReportTxnDetail' => [
				'TxnId' => $this->TxnId
			]
		]);
		
		return $this->Transaction();
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico ReportTxnDetailResponse class
 *
 * Response object for ReportTxnDetail
 *
 * @package porticoAPI
 *
 */
class ReportTxnDetailResponse extends porticoResponse{
	/**
	 * Check response and builds formatted object.
	 *
	 * @param array $response Request array generated by portico and sent to Heartland Portico Gateway
	 * @param string $request Complete SOAP XML from Heartland Portico Gateway
	 */
	function __construct($response, $request)
	{
		parent::__construct($response, $request);
	}
}


?>