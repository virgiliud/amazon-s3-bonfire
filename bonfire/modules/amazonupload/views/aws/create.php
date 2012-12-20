
<?php if (validation_errors()) : ?>
<div class="alert alert-block alert-error fade in ">
  <a class="close" data-dismiss="alert">&times;</a>
  <h4 class="alert-heading">Please fix the following errors :</h4>
 <?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<?php // Change the css classes to suit your needs
if( isset($amazonupload) ) {
    $amazonupload = (array)$amazonupload;
}
$id = isset($amazonupload['id']) ? $amazonupload['id'] : '';
?>
<div class="admin-box">
    <h3>AmazonUpload</h3>
<?php echo form_open_multipart($this->uri->uri_string(), 'class="form-horizontal"'); ?>
    <fieldset>
        <div class="control-group">
            <?php echo form_label('Upload image', 'userfile', array('class' => "control-label") ); ?>
            <div class='controls'>
        <input type="file" name="userfile" />
        <span class="help-inline"></span>
        </div>

        </div>



        <div class="form-actions">
            <br/>
            <input type="submit" name="save" class="btn btn-primary" value="Create AmazonUpload" />
            or <?php echo anchor(SITE_AREA .'/aws/amazonupload', lang('amazonupload_cancel'), 'class="btn btn-warning"'); ?>
            
        </div>
    </fieldset>
    <?php echo form_close(); ?>


</div>
