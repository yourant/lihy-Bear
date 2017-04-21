<?php
/**
 * @desc amazon海外仓线上库存置零
 * @author hanxy
 *
 */
class AmazonOverseasWarehouseZeroStockSku extends AmazonModel {
		
	const STATUS_PENGDING = 0;//待处理
	const STATUS_SUCCESS = 1;//处理成功
	const STATUS_FAILURE = 2;//处理失败

	public static $accountPairs = array();
	
	public function tableName() {
		return 'ueb_overseas_warehouse_zero_stock_sku';
	}
	
	
	public static function model($className = __CLASS__) {
		return parent::model($className);		
	}
	
	/**
	 * @desc 保存信息
	 * @param unknown $params
	 * @return boolean|Ambigous <number, boolean>
	 */
	public function saveData($params){
		if(empty($params)) return false;
		return $this->getDbConnection()->createCommand()
		->insert($this->tableName(), $params);
	}
	
	
	/**
	 * @desc 更新
	 * @param unknown $data
	 * @param unknown $wheres
	 * @return Ambigous <number, boolean>
	 */
	public function updateData($data, $wheres){
		return $this->getDbConnection()
					->createCommand()
					->update($this->tableName(), $data, $wheres);
	}


	/**
	 * @desc  检测当天是否已经运行了
	 * @param unknown $sellerSku
	 * @param unknown $accountID
	 * @param number $siteID
	 * @return boolean
	 */
	public function checkHadRunningForDay($sellerSku, $accountID, $listingID = NULL){
		$todayStart = date("Y-m-d 00:00:00");
		$todayEnd = date("Y-m-d 23:59:59");
		$command = $this->getDbConnection()
						->createCommand()
						->from($this->tableName())
						->select('id')
						->where("seller_sku=:seller_sku AND account_id=:account_id",
								array(':seller_sku'=>$sellerSku, ':account_id'=>$accountID))
						->andWhere("create_time>=:begin AND create_time<=:end", array(':begin'=>$todayStart, ':end'=>$todayEnd))
						->andWhere("status=".self::STATUS_SUCCESS);
		if($listingID != NULL){
			$command->andWhere("amazon_listing_id=:amazon_listing_id", array(":amazon_listing_id"=>$listingID));
		}
		$res = $command->queryRow();
		if($res)
			return true;
		else
			return false;
	}


	// =========== start: search ==================
	public function attributeLabels() {
		return array(
				'id'                            => Yii::t('system', 'No.'),
				'sku'							=> 'SKU',
				'seller_sku'					=> '线上SKU',
				'account_id'					=> '账号',
				'account_name'					=> '账号名称',
				'type'							=> '类型',
				'status'						=> '处理状态',
				'create_time'					=> '创建时间',
				'msg'							=> '提示',
				'amazon_listing_id'             => 'LISTING_ID',
				'is_restore'					=> '是否恢复',
				'restore_time'                  => '恢复时间',
				'restore_num'                   => '恢复次数'
		);
	}
	
	public function getStatusOptions($status = null){
		//@todo 后续语言处理
		$statusOptions = array(
				self::STATUS_PENGDING=>'待处理',
				self::STATUS_SUCCESS=>'成功',
				self::STATUS_FAILURE=>'失败'
		);
		if($status !== null)
			return isset($statusOptions[$status])?$statusOptions[$status]:'';
		return $statusOptions;
	}
	
	public function getTypeOptions($type = null){
		//@todo 后续语言处理
		$typeOptions = array(
				0=>'仓库库存<=1',
				1=>'滞销、待清除',
				2=>'欠货待处理',
				3=>'unkown',
				4=>'amazon指定listing',
				5=>'手动导入sku',
				8=>'库存恢复',
		);
		if($type !== null)
			return isset($typeOptions[$type])?$typeOptions[$type]:'';
		return $typeOptions;
	}
	
	public function addtions($datas){
		if(empty($datas)) return $datas;
		foreach ($datas as &$data){
			//账号名称
			$data['account_id'] = self::$accountPairs[$data['account_id']];
			//状态
			$data['status'] = $this->getStatusOptions($data['status']);
			//是否恢复
			$data['is_restore'] = $this->getRestoreStatusOptions($data['is_restore']);
		}
		return $datas;
	}
	
	
	/**
	 * get search info
	 */
	public function search() {
		$sort = new CSort();
		$sort->attributes = array(
				'defaultOrder'  => 'id',
		);
		$dataProvider = parent::search(get_class($this), $sort);
		$data = $this->addtions($dataProvider->data);
		$dataProvider->setData($data);
		return $dataProvider;
	}
	
	/**
	 * filter search options
	 * @return type
	 */
	public function filterOptions() {
		$type = Yii::app()->request->getParam('type');
		$status = Yii::app()->request->getParam('status');
		$restoreStatus = Yii::app()->request->getParam('is_restore');
		$result = array(
				array(
						'name'=>'sku',
						'type'=>'text',
						'search'=>'LIKE',
						'htmlOption' => array(
								'size' => '22',
						)
				),
				array(
						'name'=>'seller_sku',
						'type'=>'text',
						'search'=>'LIKE',
						'htmlOption' => array(
								'size' => '22',
						)
				),
				array(
    					'name'=>'account_id',
    					'type'=>'dropDownList',
    					'search'=>'=',
    					'data'=>$this->getAccountList()
    			),
				array(
						'name'=>'status',
						'type'=>'dropDownList',
						'search'=>'=',
						'data'=>$this->getStatusOptions(),
						'value'=>$status
				),
				array(
						'name'=>'is_restore',
						'type'=>'dropDownList',
						'search'=>'=',
						'data'=>$this->getRestoreStatusOptions(),
						'value'=>$restoreStatus
				),
		);
		return $result;
	}
	
	/**
	 * @desc  获取公司账号
	 */
	public function getAccountList(){
		if(self::$accountPairs == null)
			self::$accountPairs = self::model('AmazonAccount')->getIdNamePairs();
		return self::$accountPairs;
	}
	// =========== end: search ==================
	

	public function getRestoreStatusOptions($restoreStatus = null){
		$restoreStatusOptions = array(
				0=>'待恢复',
				1=>'恢复成功',
				2=>'恢复失败',
		);	
		if($restoreStatus !== null)
			return isset($restoreStatusOptions[$restoreStatus]) ? $restoreStatusOptions[$restoreStatus] : '';
		return $restoreStatusOptions;
	}
}