<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="page message">
	<div class="content">
		<div class="container <?php  echo $type;?>">
			<div class="icon-area"><i class="icon icon-icon"></i></div>
			<div class="text-area">
				<h2 class="msg-title"><?php  echo $title;?></h2>
				<div class="desc"><?php  echo $message;?></div>
			</div>
			<div class="btn-area">
				<p>
					<a href="<?php  echo $redirect;?>" class="button"><?php  if(!empty($btn_text)) { ?><?php  echo $btn_text;?><?php  } else { ?>确定<?php  } ?></a>
				</p>
			</div>
			<div class="extra-area">
				<a href="<?php  echo ivurl('pages/home/index', array(), true);?>">返回首页</a>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	<?php  if(!empty($close)) { ?>
		$('.btn-area a').click(function(){
			wx.ready(function(){
				wx.closeWindow();
			});
		});
	<?php  } ?>
});
</script>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>