<?
/**
 * Heartland Portico Gateway BatchClose class.
 *
 * This file includes the class for BatchClose functionality linked to SiteId and DeviceId supplied in the header
 * as well as the BatchCloseResponse response object
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
 * BatchClose extends portico to close current open batch associated with account and device
 *
 * @package porticoAPI
 *
 */
class BatchClose extends portico{
	/**
	 * Array of required properties.
	 * 
	 * @var array 
	 */
	protected $required = [
		'BatchClose' => []
	];
/*********************************************************************************************/
###############################################################################################
	/**
	 * Builds BatchClose request and submit it to portico::Transaction
	 *
	 * @return BatchCloseResponse|FailureResponse formatted response from Heartland SOAP interface
	 */
	public function doTransaction()
	{
		$this->initRequest();

		$this->setTransaction([
			'BatchClose' => []
		]);
		
		return $this->Transaction();
	}
}
##*******************************************************************************************##
/**
 * Heartland Portico BatchCloseResponse class
 *
 * Response object for BatchClose
 *
 * @package porticoAPI
 *
 */
class BatchCloseResponse extends porticoResponse{
}

?>