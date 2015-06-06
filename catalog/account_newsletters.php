<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\HTML;
  use OSC\OM\OSCOM;

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    OSCOM::redirect('login.php', '', 'SSL');
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/account_newsletters.php');

  $Qnewsletter = $OSCOM_Db->prepare('select customers_newsletter from :table_customers where customers_id = :customers_id');
  $Qnewsletter->bindInt(':customers_id', $_SESSION['customer_id']);
  $Qnewsletter->execute();

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    $newsletter_general = (isset($_POST['newsletter_general']) && ($_POST['newsletter_general'] == '1')) ? 1 : 0;

    if ($newsletter_general !== $Qnewsletter->valueInt('customers_newsletter')) {
      $newsletter_general = ($Qnewsletter->valueInt('customers_newsletter') === 1) ? 0 : 1;

      $OSCOM_Db->save('customers', ['customers_newsletter' => $newsletter_general], ['customers_id' => $_SESSION['customer_id']]);
    }

    $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

    OSCOM::redirect('account.php', '', 'SSL');
  }

  $breadcrumb->add(NAVBAR_TITLE_1, OSCOM::link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, OSCOM::link('account_newsletters.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php echo HTML::form('account_newsletter', OSCOM::link('account_newsletters.php', '', 'SSL'), 'post', 'class="form-horizontal" role="form"', ['tokenize' => true, 'action' => 'process']); ?>

<div class="contentContainer">

  <div class="contentText">
    <div class="form-group">
      <label class="control-label col-sm-4"><?php echo MY_NEWSLETTERS_GENERAL_NEWSLETTER; ?></label>
      <div class="col-sm-8">
        <div class="checkbox">
          <label>
            <?php echo HTML::checkboxField('newsletter_general', '1', (($Qnewsletter->value('customers_newsletter') == '1') ? true : false)); ?>
            <?php if (tep_not_null(MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION)) echo ' ' . MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION; ?>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6 text-right pull-right"><?php echo HTML::button(IMAGE_BUTTON_CONTINUE, 'glyphicon glyphicon-chevron-right', null, 'primary', null, 'btn-success'); ?></div>
    <div class="col-sm-6"><?php echo HTML::button(IMAGE_BUTTON_BACK, 'glyphicon glyphicon-chevron-left', OSCOM::link('account.php', '', 'SSL')); ?></div>
  </div>

</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
