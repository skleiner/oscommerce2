<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\HTML;

  if (!isset($process)) $process = false;
?>

  <div class="contentText">
  
<?php
  if (ACCOUNT_GENDER == 'true') {
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
      $female = ($gender == 'f') ? true : false;
    } else {
      $male = false;
      $female = false;
    }
?>

    <div class="form-group">
      <label class="control-label col-sm-3"><?php echo ENTRY_GENDER; ?></label>
      <div class="col-sm-9">
        <label class="radio-inline">
          <?php echo HTML::radioField('gender', 'm', $male) . ' ' . MALE; ?>
        </label>
        <label class="radio-inline">
          <?php echo HTML::radioField('gender', 'f', $female) . ' ' . FEMALE; ?>
        </label>
        <?php if (tep_not_null(ENTRY_GENDER_TEXT)) echo '<span class="help-block">' . ENTRY_GENDER_TEXT . '</span>'; ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group">
      <label for="inputFirstName" class="control-label col-sm-3"><?php echo ENTRY_FIRST_NAME; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('firstname', NULL, 'id="inputFirstName" placeholder="' . ENTRY_FIRST_NAME_TEXT . '"');
        ?>
      </div>
    </div>
    <div class="form-group">
      <label for="inputLastName" class="control-label col-sm-3"><?php echo ENTRY_LAST_NAME; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('lastname', NULL, 'id="inputLastName" placeholder="' . ENTRY_LAST_NAME_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  if (ACCOUNT_COMPANY == 'true') {
?>

    <div class="form-group">
      <label for="inputCompany" class="control-label col-sm-3"><?php echo ENTRY_COMPANY; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('company', NULL, 'id="inputCompany" placeholder="' . ENTRY_COMPANY_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group">
      <label for="inputStreet" class="control-label col-sm-3"><?php echo ENTRY_STREET_ADDRESS; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('street_address', NULL, 'id="inputStreet" placeholder="' . ENTRY_STREET_ADDRESS_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  if (ACCOUNT_SUBURB == 'true') {
?>

    <div class="form-group">
      <label for="inputSuburb" class="control-label col-sm-3"><?php echo ENTRY_SUBURB; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('suburb', NULL, 'id="inputSuburb" placeholder="' . ENTRY_SUBURB_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group">
      <label for="inputZip" class="control-label col-sm-3"><?php echo ENTRY_POST_CODE; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('postcode', NULL, 'id="inputZip" placeholder="' . ENTRY_POST_CODE_TEXT . '"');
        ?>
      </div>
    </div>
    <div class="form-group">
      <label for="inputCity" class="control-label col-sm-3"><?php echo ENTRY_CITY; ?></label>
      <div class="col-sm-9">
        <?php
        echo HTML::inputField('city', NULL, 'id="inputCity" placeholder="' . ENTRY_CITY_TEXT . '"');
        ?>
      </div>
    </div>

    

<?php
  if (ACCOUNT_STATE == 'true') {
?>

    <div class="form-group">
      <label for="inputState" class="control-label col-sm-3"><?php echo ENTRY_STATE; ?></label>
      <div class="col-sm-9">
        <?php
        if ($process == true) {
          if ($entry_state_has_zones == true) {
            $zones_array = array();
            $Qzones = $OSCOM_Db->get('zones', 'zone_name', ['zone_country_id' => $country], 'zone_name');
            while ($Qzones->fetch()) {
              $zones_array[] = array('id' => $Qzones->value('zone_name'), 'text' => $Qzones->value('zone_name'));
            }
            echo HTML::selectField('state', $zones_array, 0, 'id="inputState"');
          } else {
            echo HTML::inputField('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
          }
        } else {
          echo HTML::inputField('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
        }
        ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group">
      <label for="inputCountry" class="control-label col-sm-3"><?php echo ENTRY_COUNTRY; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_get_country_list('country', STORE_COUNTRY, 0, 'id="inputCountry"');
        if (tep_not_null(ENTRY_COUNTRY_TEXT)) echo '<span class="help-block">' . ENTRY_COUNTRY_TEXT . '</span>';
        ?>
      </div>
    </div>
</div>
