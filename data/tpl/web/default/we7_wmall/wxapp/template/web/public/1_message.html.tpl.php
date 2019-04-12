<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="panel panel-default panel-message">
	<div class="panel-body">
		<div class="message-icon pull-left">
			<i class="fa fa-5x fa-<?php  if($label=='success') { ?>check-circle<?php  } ?><?php  if($label=='danger') { ?>times-circle<?php  } ?><?php  if($label=='info') { ?>info-circle<?php  } ?><?php  if($label=='warning') { ?>exclamation-triangle<?php  } ?>"></i>
		</div>
		<div class="pull-left">
			<?php  if(is_array($msg)) { ?>
				<?php  if(!empty($msg['sql'])) { ?>
					<h2>MYSQL 错误：</h2>
					<p><?php  echo cutstr($msg['sql'], 300, 1);?></p>
					<p><b><?php  echo $msg['error']['0'];?> <?php  echo $msg['error']['1'];?>：</b><?php  echo $msg['error']['2'];?></p>
				<?php  } else { ?>
					<p class="message-text"><?php  echo $msg['message'];?></p>
				<?php  } ?>
			<?php  } else { ?>
				<p class="message-text"><?php  echo $msg;?></p>
			<?php  } ?>
			<?php  if($redirect) { ?>
				<p><a href="<?php  echo $redirect;?>">如果你的浏览器没有自动跳转，请点击此链接</a></p>
				<script type="text/javascript">
					setTimeout(function () {
						location.href = "<?php  echo $redirect;?>";
					}, 2000);
				</script>
			<?php  } else { ?>
				<p>[<a href="javascript:history.go(-1);">点击这里返回上一页</a>] &nbsp; [<a href="./?refresh">首页</a>]</p>
			<?php  } ?>
		</div>
	</div>
</div>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>