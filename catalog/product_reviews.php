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

  if (!isset($_GET['products_id'])) {
    OSCOM::redirect('reviews.php');
  }

  $Qcheck = $OSCOM_Db->prepare('select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
  $Qcheck->bindInt(':products_id', $_GET['products_id']);
  $Qcheck->bindInt(':language_id', $_SESSION['languages_id']);
  $Qcheck->execute();

  if ( $Qcheck->fetch() === false ) {
    OSCOM::redirect('reviews.php');
  }

  if ( $new_price = tep_get_products_special_price($Qcheck->valueInt('products_id')) ) {
    $products_price = '<del>' . $currencies->display_price($Qcheck->valueDecimal('products_price'), tep_get_tax_rate($Qcheck->valueInt('products_tax_class_id'))) . '</del> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($Qcheck->valueInt('products_tax_class_id'))) . '</span>';
  } else {
    $products_price = $currencies->display_price($Qcheck->valueDecimal('products_price'), tep_get_tax_rate($Qcheck->valueInt('products_tax_class_id')));
  }

  $products_name = $Qcheck->value('products_name');

  if ( !empty($Qcheck->value('products_model')) ) {
    $products_name .= ' <small>[' . $Qcheck->value('products_model') . ']</small>';
  }

  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/product_reviews.php');

  $breadcrumb->add(NAVBAR_TITLE, OSCOM::link('product_reviews.php', tep_get_all_get_params()));

  if ( !empty($Qcheck->value('products_model')) ) {
    // add the products model to the breadcrumb trail
    $breadcrumb->add($Qcheck->value('products_model'), OSCOM::link('product_info.php', 'cPath=' . $cPath . '&products_id=' . $Qcheck->valueInt('products_id')));
  }
  require('includes/template_top.php');
?>

<?php
  if ($messageStack->size('product_reviews') > 0) {
    echo $messageStack->output('product_reviews');
  }
?>

<div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product">

<div class="page-header">
  <div class="row">
    <h1 class="col-sm-8" itemprop="name"><?php echo $products_name; ?></h1>
    <h1 class="col-sm-4 text-right-not-xs"><?php echo $products_price; ?></h1>
  </div>
</div>

<div class="contentContainer">

  <div class="row">
    <?php
    $Qa = $OSCOM_Db->prepare('select AVG(r.reviews_rating) as average, COUNT(r.reviews_rating) as count from :table_reviews r left join :table_reviews_description rd on r.reviews_id = rd.reviews_id where r.products_id = :products_id and r.reviews_status = 1 and rd.languages_id = :languages_id');
    $Qa->bindInt(':products_id', $Qcheck->valueInt('products_id'));
    $Qa->bindInt(':languages_id', $_SESSION['languages_id']);
//    $Qa->setCache('product_reviews_avg-' . $_SESSION['language'] . '-p' . $Qcheck->valueInt('products_id'));
    $Qa->execute();

    echo '<div class="col-sm-8 text-center alert alert-success" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
    echo '  <meta itemprop="ratingValue" content="' . max(1, (int)round($Qa->value('average'))) . '" />';
    echo '  <meta itemprop="bestRating" content="5" />';
    echo    sprintf(REVIEWS_TEXT_AVERAGE, $Qa->valueInt('count'), HTML::stars(round($Qa->value('average'))));
    echo '</div>';
    ?>

<?php
  if ( tep_not_null($Qcheck->value('products_image')) ) {
?>

    <div class="col-sm-4 text-center">
      <?php echo '<a href="' . OSCOM::link('product_info.php', 'products_id=' . $Qcheck->valueInt('products_id')) . '">' . HTML::image(DIR_WS_IMAGES . $Qcheck->value('products_image'), $Qcheck->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>

      <p><?php echo HTML::button(IMAGE_BUTTON_IN_CART, 'glyphicon glyphicon-shopping-cart', OSCOM::link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now'), null, null, 'btn-success btn-block'); ?></p>
    </div>

    <div class="clearfix"></div>

    <hr>

    <div class="clearfix"></div>

<?php
  }
?>
  </div>
<?php

  $Qreviews = $OSCOM_Db->prepare('select SQL_CALC_FOUND_ROWS r.reviews_id, reviews_text, r.reviews_rating, r.date_added, r.customers_name from :table_reviews r, :table_reviews_description rd where r.products_id = :products_id and r.reviews_id = rd.reviews_id and rd.languages_id = :languages_id and r.reviews_status = 1 order by r.reviews_rating desc limit :page_set_offset, :page_set_max_results');
  $Qreviews->bindInt(':products_id', $Qcheck->valueInt('products_id'));
  $Qreviews->bindInt(':languages_id', $_SESSION['languages_id']);
  $Qreviews->setPageSet(MAX_DISPLAY_NEW_REVIEWS);
  $Qreviews->execute();

  if ($Qreviews->getPageSetTotalRows() > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>

  <div class="row">
    <div class="col-sm-6 pagenumber hidden-xs">
      <?php echo $Qreviews->getPageSetLabel(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
    </div>
    <div class="col-sm-6">
      <div class="pull-right pagenav"><?php echo $Qreviews->getPageSetLinks(tep_get_all_get_params(array('page', 'info'))); ?></div>
      <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
    </div>
  </div>

  <div class="clearfix"></div>

<?php
    }
?>
  <div class="reviews">
<?php
    while ( $Qreviews->fetch() ) {
?>

    <blockquote class="col-sm-6" itemprop="review" itemscope itemtype="http://schema.org/Review">
      <p itemprop="reviewBody"><?php echo nl2br($Qreviews->valueProtected('reviews_text')); ?></p>
      <meta itemprop="datePublished" content="<?php echo $Qreviews->value('date_added'); ?>">
      <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
        <meta itemprop="ratingValue" content="<?php echo $Qreviews->value('reviews_rating'); ?>">
      </span>
      <footer>
        <?php
        $review_name = $Qreviews->valueProtected('customers_name');
        echo sprintf(REVIEWS_TEXT_RATED, HTML::stars($Qreviews->value('reviews_rating')), $review_name, $review_name);
        ?>
      </footer>
    </blockquote>

<?php
    }
?>
  </div>

<?php
    if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>

  <div class="clearfix"></div>

  <div class="row">
    <div class="col-sm-6 pagenumber hidden-xs">
      <?php echo $Qreviews->getPageSetLabel(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
    </div>
    <div class="col-sm-6">
      <div class="pull-right pagenav"><?php echo $Qreviews->getPageSetLinks(tep_get_all_get_params(array('page', 'info'))); ?></div>
      <span class="pull-right"><?php echo TEXT_RESULT_PAGE; ?></span>
    </div>
  </div>

<?php
    }
  } else {
?>

  <div class="contentText">
    <div class="alert alert-info">
      <?php echo TEXT_NO_REVIEWS; ?>
    </div>
  </div>

<?php
  }
?>

  <div class="clearfix"></div>

  <div class="row">
    <div class="col-sm-6 text-right pull-right"><?php echo HTML::button(IMAGE_BUTTON_WRITE_REVIEW, 'glyphicon glyphicon-comment', OSCOM::link('product_reviews_write.php', tep_get_all_get_params()), 'primary', null, 'btn-success'); ?></div>
    <div class="col-sm-6"><?php echo HTML::button(IMAGE_BUTTON_BACK, 'glyphicon glyphicon-chevron-left', OSCOM::link('product_info.php', tep_get_all_get_params())); ?></div>
  </div>
</div>

</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
