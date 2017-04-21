<?php
/**
 * @desc joom 接口请求池
 * @author zhangF
 *
 */
class JoomOrderDetailPrimaryKey extends JoomModel {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @desc 数据库表名
	 * @see CActiveRecord::tableName()
	 */
	public function tableName() {
		return 'ueb_joom_order_detail_primary_key';
	}
	
	/**
	 * @desc 获取到主键ID
	 */
	public function getOrderDetailPrimaryKeyID(){
		if($this->getDbConnection()->createCommand()->insert($this->tableName(), array('detail_id'=>null)))
			return $this->getDbConnection()->getLastInsertID();
		return false;
	}
}