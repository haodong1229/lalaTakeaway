<?php defined('IN_IA') or exit('Access Denied');?>		</div>
	</div>
	<div class="footer">
		<ul class="links container-fluid">
			<li class="links_item">
			<p class="copyright">
				<?php  if(!empty($_W['we7_wmall']['global']['system']['copyright'])) { ?>
					<?php  echo $_W['we7_wmall']['global']['system']['copyright'];?>
				<?php  } else { ?>
					<?php  if(empty($_W['setting']['copyright']['footerleft']) && empty($_W['setting']['copyright']['footerright'])) { ?>
						Powered by <a href="http://www.we7.cc"><b>微擎</b></a> v<?php echo IMS_VERSION;?> &copy; 2014-2019 <a href="http://www.we7.cc">www.we7.cc</a>
					<?php  } else { ?>
						<?php  echo $_W['setting']['copyright']['footerleft'];?> <?php  echo $_W['setting']['copyright']['footerright'];?>
					<?php  } ?>
				<?php  } ?>
			</p>
			</li>
		</ul>
	</div>
	<div id="page-loading">
		<div>
			<div class="sk-spinner sk-spinner-three-bounce">
				<div class="sk-bounce1"></div>
				<div class="sk-bounce2"></div>
				<div class="sk-bounce3"></div>
			</div>
		</div>
	</div>
<?php  include itemplate('public/tiny', TEMPLATE_INCLUDEPATH);?>
<?php  include itemplate('public/footer-base', TEMPLATE_INCLUDEPATH);?>
