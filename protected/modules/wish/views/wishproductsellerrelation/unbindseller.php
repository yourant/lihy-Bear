<?php
Yii::app()->clientscript->scriptMap['jquery.js'] = false;
$row = 0;
$options = array(
	'id' => 'wishproductunbindsellerrelation-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'toolBar' => array(
			array (
					'text' => Yii::t ( 'system', 'Add' ),
					'url' => '/wish/wishproductsellerrelation/import',
					'htmlOptions' => array (
							'class' => 'add',
							'target' => 'dialog',
							'rel' => 'wishproductunbindsellerrelation-grid',
							'postType' => '',
							'callback' => '',
							'height' => '480',
							'width' => '650'
					)
			),
			array (
					'text' => Yii::t ( 'system', '设置账号销售'),
					'url' => '/wish/wishproductsellerrelation/batchchangeunbindtoseller',
					'htmlOptions' => array (
							'class' => 'add',
							'target' => 'dialog',
							'rel' => 'wishproductunbindsellerrelation-grid',
							'postType' => '',
							'callback' => '',
							'height' => '480',
							'width' => '650'
					)
			),
			
			array (
					'text' => Yii::t ( 'system', '批量SKU设置'),
					'url' => 'javascript:void(0)',
					'htmlOptions' => array (
							'class' => 'add',
							'rel' => 'wishproductunbindsellerrelation-grid',
							'onclick' => 'showChangeUnbindSkuSeller()'
					)
			),	

			array (
					'text' => Yii::t ( 'system', '批量导出'),
					'url' => 'javascript:void(0)',
					'htmlOptions' => array (
							'class' => 'add',
							'target' => '',
							'rel' => '',
							'onclick' => 'unbindSkuSellerDownCSV()'
					)
			),			
		
	),
	'columns'=>array(
				array(
						'class' => 'CCheckBoxColumn',
						'selectableRows' =>2,
						'value'	=> '$data->id',
						'htmlOptions'=>array(
								'style' => 'width:60px;'
						)
				),
				
			
				array(
						'name' => 'product_id',
						'value' => '$data->product_id',
						'type'  => 'raw',
						'htmlOptions' => array('style' => 'width:180px;'),
				),
				array(
						'name' => 'sku',
						'value' => '$data->sku',
				        'type'  => 'raw',
						'htmlOptions' => array('style' => 'width:180px;'),
				),
	
             	array(
						'name'  => 'online_sku',
						'value' => '$data->online_sku',
						'type'  => 'raw',
						'htmlOptions' => array(
								'style' => 'text-align:center;width:160px;',
						),
				),
				

			

				array(
						'name'  => 'account_id',
						'value' => '$data->account_name',
						'htmlOptions' => array(
								'style' => 'text-align:center;width:90px;',
						),
				),
				
		
			/* array(
					'header' => Yii::t('system', 'Operation'),
					'class' => 'CButtonColumn',
					'template' => '{edit}',
					'htmlOptions' => array(
							'style' => 'text-align:center;width:150px;',
					),
					'buttons' => array(
							'edit' => array(
									'url'       => 'Yii::app()->createUrl("/wish/wishproductsellerrelation/update", array("id" => $data->id))',
									'label'     => Yii::t('wish_listing', '修改'),
									'options'   => array(
											'target'    => 'dialog',
											'class'     => 'btnEdit',
											'rel' 		=> 'wishproductsellerrelation-grid',
											'postType' 	=> '',
											'callback' 	=> '',
											'height' 	=> '',
											'width' 	=> ''
									),
							),
					),
			), */
			
		),
	'tableOptions' 	=> array(
			'layoutH' 	=> 90,
	),
	'pager' 		=> array(),
);

$this->widget('UGridView', $options);

?>

<script type="text/javascript">
function showChangeUnbindSkuSeller(){

    var ids = "";
    var arrChk= $("input[name='wishproductunbindsellerrelation-grid_c0[]']:checked");
    if(arrChk.length==0){
        alertMsg.error('<?php echo Yii::t('system', 'Please Select'); ?>');
        return false;
    }
    for (var i=0;i<arrChk.length;i++)
    {
        ids += arrChk[i].value+',';
    }

    var url ='/wish/wishproductsellerrelation/batchchangeunbindskutoseller';
    var param = {'ids':ids};
	$.pdialog.open(url, 'showChangeUnbindSkuSeller', '批量设置SKU', {width:600, height:400});
	$.pdialog.reload(url,{data:param})

    return false;
}


function unbindSkuSellerDownCSV(){
    var request = "<?php echo $request; ?>";
    var url ='/wish/wishproductsellerrelation/unbindsellerexportxls?' + request;
    var ajaxurl ='/wish/wishproductsellerrelation/unbindsellerexportxlsajax?' + request;
    var checkType = 0;

    htmlobj=$.ajax({url:ajaxurl,async:false});
    checkType = parseInt(htmlobj.responseText);
	if(checkType == 1){
		window.open(url);
	}else{
		alertMsg.error('无数据');
		return;
	}
	
}
</script>