<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/account_notifications.php');

  $Qglobal = $OSCOM_Db->prepare('select global_product_notifications from :table_customers_info where customers_info_id = :customers_info_id');
  $Qglobal->bindInt(':customers_info_id', $_SESSION['customer_id']);
  $Qglobal->execute();

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    if (isset($_POST['product_global']) && is_numeric($_POST['product_global']) && in_array($_POST['product_global'], ['0', '1'])) {
      $product_global = (int)$_POST['product_global'];
    } else {
      $product_global = 0;
    }

    (array)$products = $_POST['products'];

    if ($product_global !== $Qglobal->valueInt('global_product_notifications')) {
      $product_global = ($Qglobal->valueInt('global_product_notifications') === 1) ? 0 : 1;

      $OSCOM_Db->save('customers_info', ['global_product_notifications' => $product_global], ['customers_info_id' => $_SESSION['customer_id']]);
    } elseif (sizeof($products) > 0) {
      $products_parsed = array();
      foreach ($products as $value) {
        if (is_numeric($value) && !in_array($value, $products_parsed)) {
          $products_parsed[] = $value;
        }
      }

      if (sizeof($products_parsed) > 0) {
        $products_id_in = array_map(function($k) {
          return ':products_id_' . $k;
        }, array_keys($products_parsed));

        $Qcheck = $OSCOM_Db->prepare('select products_id from :table_products_notifications where customers_id = :customers_id and products_id not in (' . implode(', ', $products_id_in) . ') limit 1');
        $Qcheck->bindInt(':customers_id', $_SESSION['customer_id']);

        foreach ($products_parsed as $k => $v) {
          $Qcheck->bindInt(':products_id_' . $k, $v);
        }

        $Qcheck->execute();

        if ($Qcheck->fetch() !== false) {
          $Qdelete = $OSCOM_Db->prepare('delete from :table_products_notifications where customers_id = :customers_id and products_id not in (' . implode(', ', $products_id_in) . ')');
          $Qdelete->bindInt(':customers_id', $_SESSION['customer_id']);

          foreach ($products_parsed as $k => $v) {
            $Qdelete->bindInt(':products_id_' . $k, $v);
          }

          $Qdelete->execute();
        }
      }
    } else {
      $Qcheck = $OSCOM_Db->prepare('select products_id from :table_products_notifications where customers_id = :customers_id limit 1');
      $Qcheck->bindInt(':customers_id', $_SESSION['customer_id']);
      $Qcheck->execute();

      if ($Qcheck->fetch() !== false) {
        $OSCOM_Db->delete('products_notifications', ['customers_id' => $_SESSION['customer_id']]);
      }
    }

    $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_notifications.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php echo tep_draw_form('account_notifications', tep_href_link('account_notifications.php', '', 'SSL'), 'post', 'class="form-horizontal" role="form"', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">
  <div class="alert alert-info">
    <?php echo MY_NOTIFICATIONS_DESCRIPTION; ?>
  </div>

  <div class="contentText">
    <div class="form-group">
      <label class="control-label col-sm-4"><?php echo GLOBAL_NOTIFICATIONS_TITLE; ?></label>
      <div class="col-sm-8">
        <div class="checkbox">
          <label>
            <?php echo tep_draw_checkbox_field('product_global', '1', (($Qglobal->valueInt('global_product_notifications') === 1) ? true : false)); ?>
            <?php if (tep_not_null(GLOBAL_NOTIFICATIONS_DESCRIPTION)) echo ' ' . GLOBAL_NOTIFICATIONS_DESCRIPTION; ?>
          </label>
        </div>
      </div>
    </div>
  </div>

<?php
  if ($Qglobal->valueInt('global_product_notifications') !== 1) {
?>

  <div class="contentText">

<?php
    $Qcheck = $OSCOM_Db->prepare('select products_id from :table_products_notifications where customers_id = :customers_id limit 1');
    $Qcheck->bindInt(':customers_id', $_SESSION['customer_id']);
    $Qcheck->execute();

    if ($Qcheck->fetch() !== false) {
?>

    <div class="clearfix"></div>
    <div class="alert alert-warning"><?php echo NOTIFICATIONS_DESCRIPTION; ?></div>

    <div class="contentText">
      <div class="form-group">
        <label class="control-label col-sm-4"><?php echo MY_NOTIFICATIONS_TITLE; ?></label>
        <div class="col-sm-8">

<?php
      $counter = 0;

      $Qproducts = $OSCOM_Db->prepare('select pd.products_id, pd.products_name from :table_products_description pd, :table_products_notifications pn where pn.customers_id = :customers_id and pn.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
      $Qproducts->bindInt(':customers_id', $_SESSION['customer_id']);
      $Qproducts->bindInt(':language_id', $_SESSION['languages_id']);
      $Qproducts->execute();

      while ($Qproducts->fetch()) {
?>
      <div class="checkbox">
        <label>
          <?php echo tep_draw_checkbox_field('products[' . $counter . ']', $Qproducts->valueInt('products_id'), true) . $Qproducts->value('products_name'); ?>
        </label>
      </div>
<?php
        $counter++;
      }
?>

        </div>
      </div>
    </div>

<?php
    } else {
?>

    <div class="alert alert-warning">
      <?php echo NOTIFICATIONS_NON_EXISTING; ?>
    </div>

<?php
    }
?>

  </div>

<?php
  }
?>

  <div class="row">
    <div class="col-sm-6 text-right pull-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'glyphicon glyphicon-chevron-right', null, 'primary', null, 'btn-success'); ?></div>
    <div class="col-sm-6"><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'glyphicon glyphicon-chevron-left', tep_href_link('account.php', '', 'SSL')); ?></div>
  </div>
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
