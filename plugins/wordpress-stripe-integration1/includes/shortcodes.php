<?php
if (!session_id()) session_start();

function base_stripe_cashout_form() {

    $user_meta = wp_get_current_user();

    ?>
    <style>
        .payout_destination{display: none;}
    </style>
    <div style="text-align: center;">
        <div style="display: inline-block; text-align: left">
            <?php
            if( (isset($_GET['payment']) && $_GET['payment'] == 'paid')
                && (isset($_SESSION['success']) && $_SESSION['success'] == 'ok') ) {?>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {

                        $.magnificPopup.open({
                        items: {
                            src: '<div  class="small-dialog zoom-anim-dialog">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("Thank you! You Cash Out was successfully!","workscout"); ?></p></div>'+
                            '</div>',
                            type: 'inline'
                        }
                    });
                });
            </script>
            <?php
            } else { ?>
                <div class="payment-errors">
                    <?php
                    if(isset($_GET['payment']) && $_GET['payment'] == 'failed'){
                        if ( isset ($_SESSION['error']))
                            echo $_SESSION['error'];
                        unset ($_SESSION['error']);
                    }
                    ?>
                </div>

                <form class="credit-card" method="POST" id="stripe-cashout-form">
                    <div class="form-body">
                        <div class="form-row">
                            <label for="amount"><h4>How much would you like to cash out?</h4></label>
                            <input type="text" name="amount">
                        </div>

                        <div class="form-row">
                            <h4 class="title">Personal Details</h4>
                        </div>

                        <div class="form-row form-row-first">
                            <input placeholder="First Name" name = "first_name" class="card-name-first" type="text" value="<?php echo $user_meta->first_name ? $user_meta->first_name: '';?>">
                        </div>
                        <div class="form-row form-row-last">
                            <input placeholder="Last Name" name = "last_name" class="card-name-last" type="text" value="<?php echo $user_meta->last_name ? $user_meta->last_name: '';?>">
                        </div>
                        <div class="clear"></div>

                        <div class="date-field form-row">
                            <div class="styled-select sm" style="display: inline-block;">
                                <select name="dob_day">
                                    <?php
                                    for($i=1; $i<=31; $i++)
                                        echo  '<option value="'.$i.'">'.$i.'</option>';
                                    ?>
                                </select>
                            </div>
                            <div class="styled-select slate" style="display: inline-block;">
                                <select name="dob_month">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="styled-select sm" style="display: inline-block;">
                                <select name="dob_year">
                                    <?php
                                    for ( $year = date("Y")-100; $year < date("Y")-18; $year++ )
                                        echo('<option value="'.$year.'">'.$year.'</option>');
                                    ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-row form-row-first">
                            <input type="text" name="last_ssn" placeholder="Last 4 digits of SSN" >
                        </div>
                        <div class="form-row form-row-last">
                            <input type="text" name="full_ssn" placeholder="SSN" >
                        </div>

                        <div class="clear"></div>

                        <div class="form-row form-row-first">
                            <input type="text" name="address_city" placeholder="Address city" >
                        </div>
                        <div class="form-row form-row-last">
                            <div class="styled-select whild" style="display: inline-block;">
                                <select name="address_country">
                                    <option>Address country</option>
                                    <option value="US">US</option>
                                </select>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="form-row form-row-first">
                            <input type="text" name="address_line1" placeholder="Address line 1" >
                        </div>
                        <div class="form-row form-row-last">
                            <input type="text" name="address_line2" placeholder="Address line 2" >
                        </div>
                        <div class="clear"></div>

                        <div class="form-row form-row-first">
                            <input type="text" name="address_state" placeholder="State" >
                        </div>
                        <div class="form-row form-row-last">
                            <input type="text" name="address_zip" placeholder="Address ZIP" >
                        </div>
                        <div class="clear"></div>
                        <div class="form-row">
                            <label for = "payout_destination"><h4>Where would you like to get you cash out?</h4></label>
                            <div class = "styled-select whild">
                                <select name="payout_destination" id="payout_destination">
                                    <!--                            <option value="stripe">Stripe account</option>-->
                                    <option>Select destination</option>
                                    <option value="credit">Credit Card</option>
                                    <option value="bank">Bank Account</option>
                                </select>
                            </div>
                        </div>

                        <div class="credit payout_destination">
                            <div class="form-row">
                                <h4 class="title">Credit Card Details</h4>
                            </div>
                            <div class="form-row">
                                <input type="text" placeholder="Card Number" autocomplete="off" class="card-number" value="">
                            </div>

                            <div class="date-field form-row form-row-first">
                                <div class="month styled-select slate" style="display: inline-block;">
                                    <select class="card-expiry-month">
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="year styled-select" style="display: inline-block;">
                                    <select class="card-expiry-year">
                                        <?php
                                        for ( $year = date("Y"); $year < date("Y")+10; $year++ )
                                        {
                                            echo('<option value="'.$year.'">'.$year.'</option>');
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="card-verification form-row form-row-last">
                                <div class="cvv-input">
                                    <input type="text" size="4" placeholder="CVV" autocomplete="off" class="card-cvc" value="">
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="stripe payout_destination">
                            <div class="form-row">
                                <h4 class="title">Connected stripe account ID</h4>

                                <input type="text" placeholder="Stripe account ID" autocomplete="off">
                            </div>
                        </div>
                        <div class="bank payout_destination">
                            <div class="form-row">
                                <h4 class="title">Bank Account Information</h4>
                                <input type="text" placeholder="Account holder name" class="acc_hold_nm" >
                            </div>
                            <div class="form-row form-row-first">
                                <input type="text" placeholder="The bank routing number" autocomplete="off" class="br_nm">
                            </div>
                            <div class="form-row form-row-last">
                                <input type="text" placeholder="The bank account number" autocomplete="off" class="ba_nm">
                            </div>
                            <div class="form-row">
                                <input type="hidden" value="individual" placeholder="Account holder type" autocomplete="off" class="acc_hold_tp">
                            </div>
                        </div>
                        <div class="clear"></div>

                        <input type="hidden" name="action" value="stripe"/>
                        <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
                        <input type="hidden" name="stripe_nonce" value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
                        <br>
                        <button type="submit" id="stripe-submit"><?php _e('Submit', 'stripe_domain'); ?></button>
                    </div>
                </form>
            <?php
            }?>
        </div>
    </div>
<?php
}
add_shortcode('cashout_form', 'base_stripe_cashout_form');