<?php

$user = wp_get_current_user();
$currency = get_woocommerce_currency_symbol();
$applications_list = get_candidate_account_balance_info($user->ID);
$available_cash = get_candidate_cash_out_sum($user->ID);

?>

<section class="section section_cash">
		<?php $sum = 0;
		foreach ($applications_list as $application) {
			if($application['application_status'] != 'new') $sum += $application['job_price'];
		}
		?>

<div class="section__container">
	<div class="money">
		<div class="money__balance">
			<p class="money__balance__header">Work in progress</p>
			<p class="money__balance__amount"><?php echo $currency.$sum?></p>
		</div>
		<div class="money__available">
			<p class="money__available__header">Available to Cash Out</p>
			<p class="money__available__amount"><?php echo $currency.$available_cash;?></p>
		</div>
	</div>
	<div class="cash-out">
		<p class="cash-out__header">Cash Out</p>
        <?php

        if ( isset ($_SESSION['error'])){
            foreach ( $_SESSION['error'] as $error ){
                echo "<span class='woocommerce-error'>".$error."</span>";
            }
            unset ($_SESSION['error']);
        }elseif  (isset ($_SESSION['success'])){
            echo "<span class=''>"."You request was sent successfully"."</span>";
            unset ($_SESSION['success']);
        }

        ?>
		<form class="form form_cash" method="post">
			<div class="inputs">
				<div class="input__block">
					<input id="first" type="text" class="form__input"  name="amount" required="required"/>
					<label for="first" class="form__input__label">How much would you like to cash out?</label>
				</div>
				<div class="input__block">
					<input id="second" type="text" class="form__input <?php if ($user->user_email) echo "has-value";?>" type="email" name="payout_destination" value="<?php echo $user->user_email;?>" required="required"/>
					<label for="second" class="form__input__label">Please provide PayPal email for payment</label>
				</div>
			</div>
			<div class="buttons">
                <input type="hidden" name="action" value="send_payment_request"/>
                <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
                <input type="hidden" name="r_nonce" value="<?php echo wp_create_nonce('r-nonce'); ?>"/>
				<a href="<?php echo home_url('/my-balance');?>" class="button button_grey">View Payment History</a>
				<button type="submit" class="button button_orange">Cash Me Out</button>
			</div>
		</form>
	</div>
</div>
</section>