<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="clearfix">
	<div class="panel panel-stat">
		<div class="panel-heading">
			<h3>外卖订单概况</h3>
		</div>
		<div class="panel-body">
			<div class="col-md-3">
				<div class="title">待接单</div>
				<div class="num-wrapper">
					<a class="num" href="<?php  echo iurl('order/takeout/list', array('status' => 1));?>"><?php  echo $stat['total_wait_handel'];?></a>
				</div>
			</div>
			<div class="col-md-3">
				<div class="title">待配送</div>
				<div class="num-wrapper">
					<a class="num" href="<?php  echo iurl('order/takeout/list', array('status' => 3));?>"><?php  echo $stat['total_wait_delivery'];?></a>
				</div>
			</div>
			<div class="col-md-3">
				<div class="title">有催单</div>
				<div class="num-wrapper">
					<a class="num" href="<?php  echo iurl('order/takeout/list', array('is_remind' => 1, 'filter_type' => 'is_remind'));?>"><?php  echo $stat['total_wait_reply'];?></a>
				</div>
			</div>
			<div class="col-md-3">
				<div class="title">待退款</div>
				<div class="num-wrapper">
					<a class="num" href="<?php  echo iurl('order/takeout/list', array('refund_status' => 1, 'filter_type' => 'refund_status'));?>"><?php  echo $stat['total_wait_refund'];?></a>
				</div>
			</div>
		</div>
	</div>

</div>
<script>
</script>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>
