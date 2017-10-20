<section class="section section_campaign">
    <div class="section__container">
        <div class="image__block"><img src="img/laptop.png" alt="" class="image"/></div>
        <div class="section__text">
            <p class="section__header section__header_main">Estimate Your Campaign</p>
            <div class="section__text__main">
                <p class="section__header">What is Your Budget?</p>
                <div class="inputs">
                    <div class="input__block">
                        <input id="first" type="text" class="form__input"/>
                        <label for="first" class="form__input__label">i.e. $500 - $50,000+</label>
                    </div>
                    <div class="input__block">
                        <input id="second" type="text" class="form__input"/>
                        <label for="second" class="form__input__label">i.e. $500 - $50,000+</label>
                    </div>
                    <div class="input__block">
                        <input id="third" type="text" class="form__input"/>
                        <label for="third" class="form__input__label">i.e. $500 - $50,000+</label>
                    </div>
                </div>
                <div class="last-block">
                    <div class="last-block__left">
                        <p class="section__header">What Channels? (Check All That Apply)</p>
                        <div class="checkbox__block">
                            <div class="checkbox">
                                <input id="first-check" type="checkbox" class="form__checkbox"/>
                                <label for="first-check" class="checkbox__label">Checked</label>
                            </div>
                            <div class="checkbox">
                                <input id="second-check" type="checkbox" class="form__checkbox"/>
                                <label for="second-check" class="checkbox__label">Unhecked</label>
                            </div>
                            <div class="checkbox">
                                <input id="third-check" type="checkbox" disabled="disabled" class="form__checkbox"/>
                                <label for="third-check" class="checkbox__label">Disabled</label>
                            </div>
                        </div>
                    </div>
                    <div class="last-block__right"><a href="#" class="button button_green">Estimate Campaign</a></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section_campaigns" id="job-manager-job-dashboard">
    <div class="section__container">
        <?php if ( ! $jobs ) : ?>
                <?php esc_html_e( 'You do not have any active listings.', 'workscout' ); ?>
        <?php else : ?>
            <div class="table">
                <div class="table__head">
                    <div class="table__row table__row_header">
                        <div class="table__header">
                            <p class="table__text">Campaign</p>
                        </div>
                        <div class="table__header">
                            <p class="table__text">Location</p>
                        </div>
                        <div class="table__header">
                            <p class="table__text">Campaign Date</p>
                        </div>
                        <div class="table__header">
                            <p class="table__text">Campaign Description</p>
                        </div>
                        <div class="table__header">
                            <p class="table__text">Influencers</p>
                        </div>
                    </div>
                </div>
                <div class="table__body">
                    <?php foreach ( $jobs as $job ) : ?>
                        <div class="table__row table__row_body">
                            <div class="table__data">
                                <p class="table__text"><a href="<?php echo get_permalink( $job->ID ); ?>"><?php echo esc_html($job->post_title); ?></a></p>
                            </div>
                            <div class="table__data">
                                <p class="table__text">Seattle, Wa</p>
                            </div>
                            <div class="table__data">
                                <p class="table__text"> <?php echo date_i18n( 'M d, Y  h:i A', strtotime( $job->post_date ) ); ?></p>
                            </div>
                            <div class="table__data">
                                <p class="table__text">Lorem Ipsum, Lorem Ipsum. Lorem Ipsum Lorem Ipsum, Lorem Ipsum. Lorem Ipsum Lorem Ipsum, Lorem Ipsum.</p>
                            </div>
                            <div class="table__data">
                                <div class="table__influencers">
                                    <div class="table__influencer"></div>
                                    <div class="table__influencer"></div>
                                    <div class="table__influencer"></div>
                                    <div class="table__influencer"></div>
                                    <div class="table__influencer"></div>
                                    <div class="table__influencer">
                                        <div class="table__influencer__number">+<?php echo ( get_job_application_count( $job->ID ) )?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table__data">
                                <div class="table__buttons">
                                    <div class="button button_green"><a href="<?php echo get_permalink( $job->ID ); ?>">View Campaign</a></div>
                                    <div class="button button_white"><?php echo ( $count = get_job_application_count( $job->ID ) ) ? '<a class="button" href="' . add_query_arg( array( 'action' => 'show_applications', 'job_id' => $job->ID ), get_permalink( $post->ID ) ) . '">'.__('View Pitches','workscout').'</a>' : '&ndash;';?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div><!--table__body-->
            </div><!--table-->
        <?php endif; ?>
        <div class="after-table">
            <div class="button button_green">View All Campaigns</div>
        </div>
    </div>
</section>
