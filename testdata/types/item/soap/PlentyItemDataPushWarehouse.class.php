<?php

require_once ROOT.'lib/soap/call/PlentySoapCall.abstract.php';

/**
 * This class retrieves all the warehouses in plentymarkets.
 * 
 * @author phileon
 * @copyright plentymarkets GmbH www.plentymarkets.com
 */
class PlentyItemDataPushWarehouse extends PlentySoapCall
{
	private $warehouseList = array();

	/**
	 *
	 * @var PlentyItemDataPushWarehouse
	 */
	private static $instance = null;
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
	}
	
	/**
	 * singleton pattern
	 *
	 * @return PlentyItemDataPushWarehouse
	 */
	public static function getInstance()
	{
		if( !isset(self::$instance) || !(self::$instance instanceof PlentyItemDataPushWarehouse))
		{
			self::$instance = new PlentyItemDataPushWarehouse();
		}
	
		return self::$instance;
	}
	
	public function execute()
	{
		try
		{
			/*
			 * do soap call
			 */
			$response	=	$this->getPlentySoap()->GetWarehouseList(new PlentySoapRequest_GetWarehouseList);
	
			/*
			 * check soap response
			 */
			if( $response->Success == true )
			{
				$this->getLogger()->debug(__FUNCTION__.' request succeed');

				/*
				 * parse and save the data
				 */
				$this->parseResponse($response);
			}
			else
			{
				$this->getLogger()->debug(__FUNCTION__.' request error');
			}
		}
		catch(Exception $e)
		{
			$this->onExceptionAction($e);
		}
	}
	
	/**
	 * Parse the response
	 *
	 * @param PlentySoapResponse_GetWarehouseList $response
	 */
	private function parseResponse($response)
	{
		if(is_array($response->WarehouseList->item))
		{
			/*
			 * If more than one warehouse
			 */
			foreach ($response->WarehouseList->item as $warehouse)
			{
				if($warehouse->Type==0)
//				if($warehouse->item->Type==0)
				{
					$this->warehouseList[$warehouse->Name] = $warehouse->WarehouseID;
				}
			}
		}
		
		/*
		 * only one warehouse
		 */
		elseif (is_object($response->WarehouseList->item))
		{
			if($response->WarehouseList->item->Type==0)
			{
				$this->warehouseList[$response->WarehouseList->item->Name] = $response->WarehouseList->item->WarehouseID;
			}
		}
	}
	
	/**
	 * 
	 * @param string $name
	 * @return number warehouseId or 0 if not exists
	 */
	public function checkWarehouse($name)
	{
		if(isset($this->warehouseList[$name]))
		{
			return $this->warehouseList[$name];
		}
		
		return 0;
	}
	
	/**
	 * 
	 * @return number warehouseId or 0 if no one exists
	 */
	public function getFirstWarehouseId()
	{
		if(is_array($this->warehouseList))
		{
			$id = array_shift($this->warehouseList);
			reset($this->warehouseList);
			
			return $id;
		}
		
		return 0;
	}
	
	/**
	 * currently not available
	 * 
	 * @deprecated
	 * @param string $name
	 */
	public function saveNewWarehouse($name)
	{
		/*
		 * currently not available...
		 */
	}
	
}
?>