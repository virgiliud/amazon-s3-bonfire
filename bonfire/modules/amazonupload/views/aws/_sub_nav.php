<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/aws/amazonupload') ?>" id="list"><?php echo lang('amazonupload_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('AmazonUpload.Aws.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/aws/amazonupload/create') ?>" id="create_new"><?php echo lang('amazonupload_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>