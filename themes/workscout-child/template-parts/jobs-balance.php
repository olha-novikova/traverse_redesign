<style>
  #balance-table .table__data:before {
    content: '';
  }
</style>

<section class="section section_opportuinities">
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
              <p class="table__text">Total $</p>
            </div>
          </div>
        </div>
        <?php $sum = 0; foreach ($applications_list as $application) : ?>
            <?php if ($application['application_status'] != '' && $application['application_status'] != 'new') : ?>
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
          <?php endif; endforeach; ?>
          <div class="table__row table__row_body">
            <div class="table__data">
          Sum: <?php echo $currency.$sum;?>
            </div>
          </div>
        </div>
    </div>
    <?php endif; ?>

</section>