<?php
use OSC\OM\HTML;
use OSC\OM\OSCOM;
?>
<div class="contentContainer <?php echo (MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <h2><?php echo MODULE_CONTENT_LOGIN_HEADING_RETURNING_CUSTOMER; ?></h2>

  <div class="contentText">
    <div class="alert alert-success">
      <p><?php echo MODULE_CONTENT_LOGIN_TEXT_RETURNING_CUSTOMER; ?></p>
    </div>

    <?php echo HTML::form('login', OSCOM::link('login.php', 'action=process', 'SSL'), 'post', 'class="form-horizontal" role="form"', ['tokenize' => true]); ?>
    
    <div class="form-group">
      <label for="inputEmail" class="control-label col-xs-4"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>
      <div class="col-xs-8">
        <?php echo HTML::inputField('email_address', NULL, 'autofocus="autofocus" required aria-required="true" id="inputEmail" placeholder="' . ENTRY_EMAIL_ADDRESS_TEXT . '"', 'email'); ?>
      </div>
    </div>

    <div class="form-group">
      <label for="inputPassword" class="control-label col-xs-4"><?php echo ENTRY_PASSWORD; ?></label>
      <div class="col-xs-8">
        <?php echo HTML::passwordField('password', NULL, 'required aria-required="true" id="inputPassword" placeholder="' . ENTRY_PASSWORD_TEXT . '"'); ?>
      </div>
    </div>

    <p class="text-right"><?php echo HTML::button(IMAGE_BUTTON_LOGIN, 'glyphicon glyphicon-log-in', null, 'primary', null, 'btn-success btn-block'); ?></p>

    </form>
    
    <hr>
    
    <p><?php echo '<a href="' . OSCOM::link('password_forgotten.php', '', 'SSL') . '">' . MODULE_CONTENT_LOGIN_TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></p>
    
  </div>
</div>
