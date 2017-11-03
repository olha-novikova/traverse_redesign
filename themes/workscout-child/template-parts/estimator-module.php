<section class="section section_campaign">
    <div class="section__container">
        <div class="image__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/laptop.png" alt="" class="image"/></div>
        <div class="section__text">
            <p class="section__header section__header_main">Estimate Your Campaign</p>
            <form id="estimator_module">
                <div class="section__text__main">
                    <p class="section__header">What is Your Budget?</p>
                    <div class="inputs">
                        <div class="input__block">
                            <input id="first" name="target_budget" type="text" class="form__input"/>
                            <label for="first" class="form__input__label">i.e. $500 - $50,000+</label>
                        </div>
                    </div>
                    <div class="last-block">
                        <div class="last-block__left">
                            <p class="section__header">What Channels? (Check All That Apply)</p>
                            <div class="checkbox__block">
                                <div class="checkbox">
                                    <input type="checkbox" name="fb_channel" id="fb">
                                    <label for="fb" class="checkbox__label">Facebook</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="ig_channel" id="ig">
                                    <label for="ig" class="checkbox__label">Instagram</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="yt_channel" id="yt">
                                    <label for="yt" class="checkbox__label">YouTube</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="tw_channel" id="tw">
                                    <label for="tw" class="checkbox__label">Twitter</label>
                                </div>
                            </div>
                            <?php
                            $args = array(
                                'taxonomy' => 'job_listing_category',
                                'hide_empty' => false,
                            );
                            $listing_categories = get_terms( $args );
                            ?>
                            <?php wp_enqueue_script( 'wp-job-manager-multiselect' ); ?>

                            <div class="input__block">
                                <p class="section__header">Campaign   Category (Select all that apply)</p>
                                <select name="traveler_type[]" class="job-manager-multiselect" multiple="multiple" data-no_results_text="<?php _e( 'No results match', 'wp-job-manager' ); ?>" data-multiple_text="<?php _e( ' Select all that apply', 'wp-job-manager' ); ?>">
                                    <?php

                                    if( $listing_categories && ! is_wp_error($listing_categories) ){
                                        foreach ($listing_categories as $listing_category){ ?>
                                            <option value="<?php echo $listing_category->slug; ?>"><?php echo $listing_category->name; ?></option>
                                        <?php }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="last-block__right">
                            <p class="section__header">Exclude</p>
                            <div class="checkbox__block">
                                <div class="checkbox">
                                    <input type="checkbox" name="micro_exclude" id="micro">
                                    <label for="micro" class="checkbox__label">Micro</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="growth_exclude" id="growth">
                                    <label for="growth" class="checkbox__label">Growth</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="pro_exclude" id="pro">
                                    <label for="pro" class="checkbox__label">Pro</label>
                                </div>
                            </div>
                            <div class="button__box">
                                <a href="#" id="do_esimate" class="button button_green">Estimate Campaign</a>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</section>