<style>
  #balance-table .table__data:before {
    content: '';
  }
</style>

<section class="section section_opportuinities">
	<div class="section__container section__container_top">
		<p class="section__header">ACCOUNT BALANCE</p>
	</div>
		<?php if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :?>
  <div class="section__container section__container_top">
    <p class="section__header">ACCOUNT BALANCE</p>
  </div>
  <div class="section__container section__container_bottom">
      <div class="table">
        <div class="table__head">
          <div class="table__row table__row_header">
            <div class="table__header">
              <p class="table__text">Listing Name</p>
            </div>
            <div class="table__header">
              <p class="table__text">Listing Status</p>
            </div>
            <div class="table__header">
              <p class="table__text">Pitch Status</p>
            </div>
            <div class="table__header">
              <p class="table__text">Salary</p>
            </div>
          </div>
        </div>
        <?php $sum = 0; foreach ($applications_list as $application) : ?>
            <div class="table__row table__row_body">
              <div class="table__data">
                <?php echo $application['job_title']?>
              </div>
              <div class="table__data">
                <p class="table__text"><?php echo $application['job_status']?></p>
              </div>
              <div class="table__data">
                <p class="table__text"><?php echo $application['application_status']?></p>
              </div>
              <div class="table__data">
                <p class="table__text"><?php echo $currency . ($application['job_price'] == '' ? 0 : $application['job_price'])?></p>
              </div>
            </div>
          <?php $sum += $application['job_price'];?>
          <?php endforeach; ?>
          <div class="table__row table__row_body">
            <div class="table__data">
          Sum: <?php echo $currency.$sum;?>
            </div>
          </div>
        </div>
    </div>
    <?php endif; ?>

	<?php if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :?>
  <div class="section__container section__container_top">
    <p class="section__header">LISTING PAYMENT HISTORY</p>
  </div>
  <div class="section__container section__container_bottom">
    <div class="table">
    <div class="table__head">
      <div class="table__row table__row_header">
        <div class="table__header">
          <p class="table__text">Package Name</p>
        </div>
        <div class="table__header">
          <p class="table__text">Package Price</p>
        </div>
        <div class="table__header">
          <p class="table__text">Listing Name</p>
        </div>
      </div>
    </div>
	  <?php $sum = 0;
	  foreach ($listings_list as $listing) :
	  $listing_object = $listing['listing'][0];
	  ?>
    <div class="table__row table__row_body">
          <div class="table__data">
	          <?php echo $listing['name']?>
          </div>
          <div class="table__data">
            <p class="table__text"><?php echo $listing['currency'].$listing['price'];?></p>
          </div>
          <div class="table__data">
            <p class="table__text"><?php if ($listing_object ) echo '<a href="'.get_permalink($listing_object->ID).'" >'.get_the_title($listing_object->ID).'</a>'; else echo "-";?></p>
          </div>
        </div>
	  <?php $sum += $listing['price'];?>
	  <?php endforeach; ?>
    <div class="table__row table__row_body">
      <div class="table__data">
        Sum: <?php echo $listing['currency'].$sum;?>
      </div>
    </div>
  </div>
  </div>
  <div class="section__container section__container_top">
    <p class="section__header">INFLUENCER PAYMENT HISTORY</p>
  </div>
  <div class="section__container section__container_bottom">
    <div class="table">
      <div class="table__head">
        <div class="table__row table__row_header">
          <div class="table__header">
            <p class="table__text">Listing Name</p>
          </div>
          <div class="table__header">
            <p class="table__text">Status</p>
          </div>
          <div class="table__header">
            <p class="table__text">Paid</p>
          </div>
          <div class="table__header">
            <p class="table__text">Influencer</p>
          </div>
        </div>
      </div>
	    <?php $sum = 0; foreach ($operations_list as $operation) : ?>
      <div class="table__row table__row_body">
            <div class="table__data">
	            <?php echo $operation['job_title']?>
            </div>
            <div class="table__data">
              <p class="table__text"><?php echo $operation['application_status']?></p>
            </div>
            <div class="table__data">
              <p class="table__text"><?php echo $operation['currency'].$operation['job_price']?></p>
            </div>
        <div class="table__data">
          <p class="table__text"><?php echo '<a href="'.get_permalink($operation['influencer_id']).'">'.get_the_title($operation['influencer_id']).'</a>'?></p>
        </div>
          </div>
	      <?php $sum += $operation['job_price'];?>
    <?php endforeach; ?>
      <div class="table__row table__row_body">
        <div class="table__data">
          Sum: <?php echo $operation['currency'].$sum;?>
        </div>
      </div>
    </div>
  </div>
	<?php endif; ?>
</section>