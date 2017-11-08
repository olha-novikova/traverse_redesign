<?php

/*
* Template Name: New Homepage
*/


get_header("newhomepage");


?>
<div class="wrapper">
    <div class="sections">

        <div class="section section_firstscreen">
            <div class="container container_firstscreen">
                <div class="section__text-block">
                    <h1 class="section__header">Welcome to the #1 Influencer Platform in Travel</h1>
                    <p class="section__description">Range Influence is a platform for brands to discover and hire leading travel influencers.
                  </ br>
It's free to join.  Once you're signup up, you can create a campaign and discover thousands of travel, outdoor and lifestyle influencers </p>
                  <!--  <div class="section__buttons"><a href="#" class="section__button">I’M A BRAND / AGENCY</a><a href="#" class="section__button">I’M AN INFLUENCER</a></div> -->
                </div>
                <?php if ( !is_user_logged_in() ) { ?>
                <div class="firstscreen__form form">
                    <div class="form__head">
                        <div class="form__head__left form__head__active">
                              <a class="form__head__login"/>Login</a>
                         </div>
                          <div class="form__head__right">
                              <a class="form__head__signup"/>Sign Up</a>
                          </div>
                    </div>
                    <div class="form__main">
                        <div class="login">
                            <p class="form__main__head">Login to Range Influence</p>
                                <form method="post" class="login">
                                    <?php do_action( 'woocommerce_login_form_start' ); ?>
                                    <div class="form__main__body">
                                        <div class="input__block">
                                            <input id="email" type="text" name="username" class="form__input" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>"/>
                                            <label for="email" class="form__input__label">Your Email</label>
                                        </div>
                                        <div class="input__block">
                                            <input id="password" type="password" name="password" class="form__input"/>
                                            <label for="password" class="form__input__label">Your Password</label>
                                        </div>
                                        <?php do_action( 'woocommerce_login_form' ); ?>
                                        <div class="checkboxes">
                                            <div class="checkbox__block">
                                                <input id="remember" type="checkbox" name="rememberme" class="form__checkbox"/>
                                                <label for="remember" class="checkbox__label">Remember Me</label>
                                            </div><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="login__recover-pass">Forgot my Password </a>
                                        </div>
                                        <?php wp_nonce_field( 'woocommerce-login' ); ?>
                                        <input type="hidden" name="login" value="login"/>
                                        <button type="submit"  class="form__button form__button_orange">Login</button>

                                        <div class="form__devider">
                                            <div class="devider__block"></div>
                                            <p class="form__devider__text">or</p>
                                            <div class="devider__block"></div>
                                        </div><a href="#" class="form__button form__button_facebook">Login with Facebook</a><a href="#" class="form__button form__button_twitter">Login with Twitter</a>
                                        <p class="register-text">Don’t you have an account? <a href="#" class="checkbox__link toggle__link">Register Now!</a> It’s really simple and you can start enjoing all the benefits!</p>
                                    </div>
                                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                                </form>
                        </div>
                        <div class="register">
                            <p class="form__main__head">Register on Range Influence</p>
                            <?php

                            $registration_enabled = get_option('users_can_register');

                            if($registration_enabled) { ?>
                            <form method="post" class="newhomapage_register">
                                <?php do_action( 'woocommerce_register_form_start' ); ?>
                                <div class="form__main__body">
                                    <div class="input__block">
                                        <input id="firstname" name="firstname" type="text" class="form__input"/>
                                        <label for="firstname" class="form__input__label">First Name</label>
                                    </div>
                                    <div class="input__block">
                                        <input id="lastname" name="lastname" type="text" class="form__input"/>
                                        <label for="lastname" class="form__input__label">Last Name</label>
                                    </div>
                                    <div class="input__block">
                                        <input id="email" type="email" name="email" class="form__input"/>
                                        <label for="email" class="form__input__label">Your Email</label>
                                    </div>
                                    <div class="input__block">
                                        <input id="password" type="password"  name="password" class="form__input"/>
                                        <label for="password" class="form__input__label">Your Password</label>
                                    </div>
                                    <p class="form__text">Who are you?</p>
                                    <div class="checboxes">
                                        <div class="checkbox__block">
                                            <input id="influencer" type="radio" value="candidate" name="role" class="form__checkbox"/>
                                            <label for="influencer" class="checkbox__label">I’m an Influencer</label>
                                        </div>
                                        <div class="checkbox__block">
                                            <input id="brand" type="radio" value="employer" name="role" class="form__checkbox"/>
                                            <label for="brand" class="checkbox__label">I’m a Brand</label>
                                        </div>
                                        <div class="checkbox__block">
                                            <input id="agency" type="radio" value="employer" name="role" class="form__checkbox"/>
                                            <label for="agency" class="checkbox__label">I’m an Agency</label>
                                        </div>
                                        <div class="checkbox__block">
                                            <input id="terms" type="checkbox" name="agreement" class="form__checkbox"/>
                                            <label for="terms" class="checkbox__label">I accept the <a target="_blank" href="<?php echo home_url('/terms-of-service');?>" class="checkbox__link"> Terms and Conditions </a> of the website</label>
                                        </div>
                                    </div>
                                    <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'workscout' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

                                    <?php do_action( 'woocommerce_register_form' ); ?>
                                    <?php wp_nonce_field( 'woocommerce-register' ); ?>
                                    <button type="submit" class="form__button">Complete Registration!</button>
                                    <div class="form__erorrs"></div>
                                    <?php do_action( 'woocommerce_register_form_end' ); ?>
                                </div>
                            </form>
                            <?php } ?>
                        </div> <!-- register -->
                    </div><!-- form__main -->
                </div><!-- firstscreen__form -->
            <?php }?>
            </div> <!-- container_firstscreen -->
        </div> <!-- section_firstscreen -->

        <section class="section section_brands">
            <div class="container container_special">
                <p class="brands__header">Brands we work with</p>
                <ul class="brands">
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/SkyViewLogo.png" alt="" class="brand__img"/></li>
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/PureCycles.png" alt="" class="brand__img"/></li>
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/Heeltop.png" alt="" class="brand__img"/></li>
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/GrowlerWerks.png" alt="" class="brand__img"/></li>
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/aloftLogo.png" alt="" class="brand__img"/></li>
                    <li class="brand"><img src="http://traverseinfluence.com/wp-content/uploads/2017/10/AlaskaAirlines.png" alt="" class="brand__img"/></li>
                </ul>
            </div>
        </section>

        <section class="section section_results">
            <div class="container container_special container_results">
                <div class="video">
                    <p class="video__header">ALOFT HOTELS</p>
                    <p class="video__descrtiption">30 Second Video with an Influencer to uniquely show things to do in their hotel, especially if it rains.</p>
                    <div class="wrapper_youtube">
                        <div data-embed="rQylzGaW0Zk" class="youtube">
                            <div class="play-button"></div>
                        </div>
                    </div>
                </div>
                <div class="results">
                    <div class="quote">
                        <p class="quote__text">Range Influence made it easy to get the specs needed, plan around my travels with Aloft and to accomplish the project at hand </p>
                        <div class="quote__author">
                            <div class="quote__author__image-block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/guy.png" alt="" class="quote__author__image"/></div>
                            <p class="quote__author__name">Barret Huie</p>
                        </div>
                    </div>
                    <div class="results__campaign">
                        <p class="results__header">CAMPAIGN RESULTS</p>
                        <ul class="results__list">
                            <li class="results__item"><img src="<?php echo get_stylesheet_directory_uri();?>/img/reach.png" alt="" class="result__icon"/>
                                <p class="result__figure">155K</p>
                                <p class="result__description">REACH</p>
                            </li>
                            <li class="results__item"><img src="<?php echo get_stylesheet_directory_uri();?>/img/plays.png" alt="" class="result__icon"/>
                                <p class="result__figure">35K</p>
                                <p class="result__description">PLAYS</p>
                            </li>
                            <li class="results__item"><img src="<?php echo get_stylesheet_directory_uri();?>/img/shares.png" alt="" class="result__icon"/>
                                <p class="result__figure">2.5K</p>
                                <p class="result__description">SHARES</p>
                            </li>
                            <li class="results__item"><img src="<?php echo get_stylesheet_directory_uri();?>/img/likes.png" alt="" class="result__icon"/>
                                <p class="result__figure">1.9K</p>
                                <p class="result__description">LIKES</p>
                            </li>
                        </ul><a href="#" class="button button_results">Find Out More</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section_ready">
            <div class="container container_ready">
                <p class="section__header section__header_ready">Are you ready to sign up?</p>
                <p class="section__description section__description_ready">It’s absolutely free to join.</p>
                <div class="buttons__ready"><a href="#" class="button button_results button_ready">I'm A Brand</a><a href="#" class="button button_results button_ready">I'm An Agency</a><a href="#" class="button button_results button_ready">I'm An Influencer</a></div>
                <p class="section__description section__description_small">* We don’t share your personal info with anyone. Check out our <a href="#" class="ready__link">Privacy Policy</a> for more information.</p>
            </div>
        </section>

        <section class="section section_faq">
            <div class="container container_faq">
                <p class="section__header section__header_faq">Frequently Asked Questions</p>
                <ul class="questions">
                    <li class="question"><img src="<?php echo get_stylesheet_directory_uri();?>/img/question.png" alt="" class="question__icon"/>
                        <div class="question__text">
                            <p class="question__question">Does your platform support Agencies?</p>
                            <p class="question__asnswer">Yes, we have agency partners to ensure our platform and tools serve the needs of small brands and large agencies alike. We are here to save you time</p>
                        </div>
                    </li>
                    <li class="question"><img src="<?php echo get_stylesheet_directory_uri();?>/img/question.png" alt="" class="question__icon"/>
                        <div class="question__text">
                            <p class="question__question">What categories do you serve?</p>
                            <p class="question__asnswer">We serve all categories inside of travel. Some of the categories but not limited to: Adventure, Backpacker, Photographers, Videographers, Cannabis/Beverage, Food, Lifestyle and many more to find or share your expertise. </p>
                        </div>
                    </li>
                    <li class="question"><img src="<?php echo get_stylesheet_directory_uri();?>/img/question.png" alt="" class="question__icon"/>
                        <div class="question__text">
                            <p class="question__question">How long does is it take to launch a campaign?</p>
                            <p class="question__asnswer">If you have all of your assets ready, you can create a new campaign in less than 10 minutes. Go ahead, go collect your materials then give it a shot and let us know how long it took you.</p>
                        </div>
                    </li>
                    <li class="question"><img src="<?php echo get_stylesheet_directory_uri();?>/img/question.png" alt="" class="question__icon"/>
                        <div class="question__text">
                            <p class="question__question">Im an influencer, how long does it take for payments to process?</p>
                            <p class="question__asnswer">After you complete your campaign requirements, the brand will approve. They have 7 business days to confirm or request changes, once approved, you will be paid within 2-3 business days.</p>
                        </div>
                    </li>
                </ul>
                <p class="section__description section__description_faq">Still have questions? Write on <a href="mailto:help@localeinfluence.com" class="faq__link">help@RangeInfluence.com</a> for answers.</p>
            </div>
        </section>

        <section class="section section_reviews">
            <div class="container container_reviews">
                <p class="section__header section__header_review">What Are People Saying</p>
                <div class="carousel">
                    <div class="carousel__item">
                        <div class="carousel__item__wrap">
                            <div class="quote quote_review">
                                <p class="quote__text">Range made it easy for us to find experienced and professional influencers for our campaign. </p>
                                <div class="quote__author">
                                    <div class="quote__author__image-block"><img src="http://rangeinfluence.com/wp-content/uploads/2017/10/GWQuote.png" alt="" class="quote__author__image"/></div>
                                    <p class="quote__author__name">GrowlerWerks</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel__item">
                        <div class="carousel__item__wrap">
                            <div class="quote quote_review">
                                <p class="quote__text">Range Influence made it easy to get the specs needed, plan around my travels with Aloft and to accomplish the project at hand </p>
                                <div class="quote__author">
                                    <div class="quote__author__image-block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/guy.png" alt="" class="quote__author__image"/></div>
                                    <p class="quote__author__name">Barret Huie</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel__item">
                        <div class="carousel__item__wrap">
                            <div class="quote quote_review">
                                <p class="quote__text">Range Influence made it easy to get the specs needed, plan around my travels with Aloft and to accomplish the project at hand </p>
                                <div class="quote__author">
                                    <div class="quote__author__image-block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/guy.png" alt="" class="quote__author__image"/></div>
                                    <p class="quote__author__name">Barret Huie</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel__item">
                        <div class="carousel__item__wrap">
                            <div class="quote quote_review">
                                <p class="quote__text">Range Influence made it easy to get the specs needed, plan around my travels with Aloft and to accomplish the project at hand </p>
                                <div class="quote__author">
                                    <div class="quote__author__image-block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/guy.png" alt="" class="quote__author__image"/></div>
                                    <p class="quote__author__name">Barret Huie</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> 

        <section class="section section_insta">
            <ul class="insta__photos">
              <?php $photos = scrape_insta('jrrny'); foreach ($photos as $elem) : ?>
                <li class="insta__photo"><img src="<?php echo $elem->display_src ?>" alt="" class="insta__photo__image"/></li>
                <?php endforeach; ?>
            </ul><a href="http://www.instagram.com/jrrny" class="button button_results button__insta">Follow us on Instagram</a>
        </section>

        <section class="section section_contact">
            <div class="container container_contact">
                <p class="section__header section__header_contact">Contact us</p>
                <p class="section__description section__description_contact">The Range Influence team is here to provide you with more information, answer any questions you may have and create an effective solution for influencer campaign needs.</p>
                <div class="contacts">
                    <div class="contacts__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/marker.png" alt="" class="contacts__icon"/>
                        <p class="contacts__text">Seattle, Washington</p>
                    </div>
                    <div class="contacts__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/mobile.png" alt="" class="contacts__icon"/>
                        <p class="contacts__text">(323)539-7301 help@RangeInfluence.com</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php get_footer("newhomepage"); ?>

