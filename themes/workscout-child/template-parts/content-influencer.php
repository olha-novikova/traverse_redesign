<div class="carousel__influencer influencer-<?php the_ID();?>">
    <div class="carousel__influencer__top"></div>
    <div class="carousel__influencer__person"><?php output_candidate_photo(); ?>
        <p class="carousel__influencer__name"><a href="<?php echo get_permalink()?>"><?php the_title(); ?></a></p>
    </div>
    <div class="carousel__influencer__info">
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_campaigns_count( get_the_ID() ); ?></p>
            <p class="carousel__influencer__description"><?php echo  _n( 'Campaign', 'Campaigns', output_candidate_campaigns_count( get_post_field('post_author', get_the_ID() ) ) ); ?></p>
        </div>
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_audience( get_the_ID() );?></p>
            <p class="carousel__influencer__description">Audience</p>
        </div>
        <div class="carousel__influencer__info-block">
            <p class="carousel__influencer__number"><?php echo output_candidate_channels_count(get_the_ID());?></p>
            <p class="carousel__influencer__description"><?php echo  _n( 'Channel', 'Channels', output_candidate_channels_count(get_the_ID()) ); ?></p>
        </div>
    </div>
    <div class="carousel__influencer__buttons">
<!--        <div class="carousel__influencer__button carousel__influencer__button_star"></div>-->
<!--        <div class="carousel__influencer__button carousel__influencer__button_message"></div>-->
    </div>
</div>