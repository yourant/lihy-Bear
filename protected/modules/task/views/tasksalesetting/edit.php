<?php Yii::app()->clientscript->scriptMap['jquery.js'] = false; ?>
<style>
    ul li {
        float: left;
        width: 120px
    }
	ul li span{
        float: left;
		margin:2px 5px;
    }

</style>
<script>
    $(function () { 
        $("#save_data").click(function () {
            var mode = $("#mode").val(); //0为修改 1为新增
            var seller_user_id = [];
            var id = [];
            id = $("#id").val();
            var seller_uid = $('input[name="seller_uid"]:checked').val();
            if (undefined != seller_uid) {
                seller_user_id.push(seller_uid); //空的时候是批量，有值的时候是单一
            } else {
                $('input[name="seller_user_id"]:checked').each(function () {
                    seller_user_id.push($(this).val());
                });
            }

            var site_list = [];
			<?php if(Platform::CODE_EBAY == $platform) {?>
            $('input[name="site_id"]:checked').each(function () {
                site_list.push($(this).val());
            });
			<?php }?>
            var account_list = [];
            $('input[name="account_id"]:checked').each(function () {
                account_list.push($(this).val());
            })

            var sales_amount = [];
            $("input[name^='sales_amount[]']").each(function () {
                sales_amount.push($(this).val());
            });

            var profit_amount = [];
            $("input[name^='profit_amount[]']").each(function () {
                profit_amount.push($(this).val());
            });

			<?php if(Platform::CODE_EBAY == $platform) {?>
            if (0 == site_list.length) {
                alertMsg.error('至少选择一个站点');
                return false;
            }
			<?php }?>

            if (0 == account_list.length) {
                alertMsg.error('至少选择一个账号');
                return false;
            }

            $.ajax({
                type: 'post',
                url: 'task/tasksalesetting/update',
                data: {
                    id: id,
                    mode: mode,
                    seller_user_id: seller_user_id,
                    site_list: site_list,
                    account_list: account_list,
                    sales_amount: sales_amount,
                    profit_amount : profit_amount,
                    param_key: 'sales',
                },
                success: function (result) {
                    if (200 == result.statusCode) {
                        $.pdialog.closeCurrent();
                        navTab.reload(result.forward, {navTabId: result.navTabId});
                    }
                },
                dataType: 'json'
            });
        });

        $("input[name='select_all_seller']").click(function () {
            if ($(this).is(":checked")) {
                $("input[name='seller_user_id']").prop("checked", true);
            } else {
                $("input[name='seller_user_id']").prop("checked", false);
            }
        });

        $("input[name='select_all_site']").click(function () {
            if ($(this).is(":checked")) {
                $("input[name='site_id']").prop("checked", true);
            } else {
                $("input[name='site_id']").prop("checked", false);
            }
        });

        $("input[name='select_all_account']").click(function () {
            if ($(this).is(":checked")) {
                $("input[name='account_id']").prop("checked", true);
            } else {
                $("input[name='account_id']").prop("checked", false);
            }
        });
    })
</script>
<div class="pageContent">
	<?php
	$form = $this->beginWidget('ActiveForm', array(
		'id' => 'modify_task',
		'enableAjaxValidation' => false,
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
			'validateOnChange' => true,
			'validateOnType' => false,
			'afterValidate' => 'js:afterValidate',
		),
		'action' => Yii::app()->createUrl($this->route),
		'htmlOptions' => array(
			'class' => 'pageForm',
		)
	));
	?>
    <div class="pageFormContent" layoutH="56">
        <div class="pd5" id="div">
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>
            <type="hidden" name="mode" id="mode" value="<?php echo $mode; ?>"/>
            <!-- 卖家信息 -->
            <div style="display: <?php echo (0 == $mode) ? "none" : "block"; ?>" class="row" id="seller_div">
                <div style="height:<?php echo !empty($id) ? 0 : 100; ?>; width:120px;">
					<?php echo CHtml::label(Yii::t('task', 'Seller'), 'seller_user_name'); ?>
                </div>
                <!-- 如果是不是批量操作的，则没有全选 -->
				<?php if (empty($id)) { ?>
                    <div>
                        <input type="checkbox" name="select_all_seller" id="select_all_seller" value=""> 全选
                    </div>
				<?php } ?>
                <div style="width:932px; float: right; margin-top:<?php echo !empty($id) ? 0 : -100; ?>">
                    <!-- 如果是修改个人的，则列出此人负责的账号，站点信息 -->
					<?php if (!empty($id)) { ?>
                        <ul>
							<?php foreach ($seller_user_list as $seller_user_id => $user_name) { ?>
                                <li>
                                    <input type="radio" checked disabled="disabled" id="seller_uid" name="seller_uid"
                                           value="<?php echo $seller_user_id; ?>"><?php echo $user_name; ?>
                                </li>
							<?php } ?>
                        </ul>
						<?php
					} else { ?>
                        <ul>
							<?php foreach ($seller_user_list as $seller_user_id => $user_name): ?>
                                <li>
                                    <input type="checkbox"
                                           name="seller_user_id" <?php if (in_array($seller_user_id, $checkSeller)) { ?> checked <?php } ?>
                                           id="seller_user_id_<?php echo $seller_user_id; ?>"
                                           value="<?php echo $seller_user_id; ?>">
									<?php echo $user_name; ?>
                                </li>
							<?php endforeach; ?>
                        </ul>
					<?php } ?>
                </div>
            </div>

			<?php if (Platform::CODE_EBAY == $platform) { ?>
                <!-- 站点信息 -->
                <div style="display: <?php echo (0 == $mode) ? "none" : "block"; ?>" class="row" id="site_div">
                    <div style="height:<?php echo !empty($id) ? 0 : 100; ?>; width:120px;">
						<?php echo CHtml::label(Yii::t('task', 'Site Name'), "site_id"); ?>
                    </div>
					<?php if (empty($id)) { ?>
                        <div>
                            <input type="checkbox" name="select_all_site" id="select_all_site" value=""> 全选
                        </div>
					<?php } ?>
                    <div>
                        <ul><?php if (empty($id)) { ?>
								<?php foreach ($sites_arr as $site_id => $site): ?>
                                    <li>
                                        <input type="checkbox" name="site_id" id="site_id_<?php echo $site_id; ?>"
                                               value="<?php echo $site_id; ?>"
											<?php if (in_array($site_id, $checkSite)) { ?> checked  <?php } ?> /> <?php echo $site; ?>
                                    </li>
								<?php endforeach; ?>
							<?php } else { ?>
                                <li>
                                    <input type="radio" name="site_id" id="site_id_<?php echo $detail['site_id']; ?>"
                                           value="<?php echo $detail['site_id']; ?>" checked
                                           disabled/> <?php echo $sites_arr[$detail['site_id']]; ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
			<?php } ?>

            <div style="display: <?php echo (0 == $mode) ? "none" : "block"; ?>" class="row" id="account_div">
                <div style="height:<?php echo !empty($id) ? 0 : 100; ?>; width:120px;"><?php echo CHtml::label(Yii::t('task', 'Account Name'), "account_id"); ?></div>
                <div style="width:932px; float: right; margin-top:<?php echo !empty($id) ? 0 : -100; ?>;">
					<?php if (empty($id)) { ?>
                        <div>
                            <input type="checkbox" name="select_all_account" id="select_all_account" value=""> 全选
                        </div>
					<?php } ?>
                    <div>
                        <ul>
							<?php if (empty($id)) { ?>
								<?php foreach ($account_list as $id => $name): ?>
                                    <li>
                                        <input type="checkbox" name="account_id" id="account_id_<?php echo $id; ?>"
											<?php if (in_array($id, $checkAccount)) { ?> checked  <?php } ?>
                                               value="<?php echo $id; ?>">
										<?php echo $name ?>
                                    </li>
								<?php endforeach; ?>
							<?php } else { ?>
                                <li>
                                    <input type="radio" name="account_id"
                                           id="account_id_<?php echo $detail['account_id']; ?>"
                                           value="<?php echo $detail['account_id']; ?>" checked
                                           disabled/> <?php echo $account_list[$detail['account_id']]; ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="list_seller">
                <ul>
					<?php foreach ($rows as $seller_name => $seller_val) { ?>
                        <li style="width: 800px; line-height: 20px; height: 20px;margin:5px 0;float:none;">
							<?php echo $seller_name; ?>：
							<?php foreach ($seller_val as $account_name => $sVal) {
								echo $account_name;
								echo "（";
								echo join("，", $sVal);
								echo "）";
							} ?>
                        </li>
					<?php } ?>
                </ul>
            </div>

            <div class="row">
                <!-- 设置销售额 -->
                <div>
					<?php echo CHtml::label(Yii::t('task', 'Sales Target'), "sales_target"); ?>
                    <ul>
						<?php foreach ($sales_profit_detail as $sk => $sv) { ?>
                            <li>
								<span><?php echo Yii::t('task', $sv['month']);?></span>
                                <input type="text" name="sales_amount[]" id="sales_amount[]"
                                       value="<?php echo isset($sv['sales_amount']) ? sprintf("%d", $sv['sales_amount']) : 0; ?>"
                                       maxlength="10" size="6" onkeyup="this.value=this.value.replace(/\D/g,'')"
                                       onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;&nbsp;
                            </li>
						<?php } ?>
                    </ul><br/><br/><br/>
                </div><br/><br/><br/>
                <!-- 设置净利润 -->
                <div>
					<?php echo CHtml::label(Yii::t('task', 'Profit Target'), "sales_target"); ?>
                    <ul>
						<?php foreach ($sales_profit_detail as $sk => $sv) { ?>
                            <li>
								<span><?php echo Yii::t('task', $sv['month']);?></span>
                                <input type="text" name="profit_amount[]" id="profit_amount[]"
                                       value="<?php echo isset($sv['profit_amount']) ? sprintf("%d", $sv['profit_amount']) : 0; ?>"
                                       maxlength="10" size="6" onkeyup="this.value=this.value.replace(/\D/g,'')"
                                       onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;&nbsp;
                            </li>
						<?php } ?>
                    </ul>
                    <br />
                </div>
            </div>
        </div>
    </div>

    <div class="formBar">
        <ul>
            <li>
                <div class="buttonActive">
                    <div class="buttonContent">
                        <button type="button" id="save_data"><?php echo Yii::t('system', '提交') ?></button>
                    </div>
                </div>
            </li>
            <li>
                <div class="button">
                    <div class="buttonContent">
                        <button type="button" class="close"><?php echo Yii::t('system', 'Cancel') ?></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
	<?php $this->endWidget(); ?>
</div>