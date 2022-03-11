<?php

if ( ! is_admin() ) {
	wp_enqueue_script( 'ramp-homepage-slides' );
}

$slides = get_posts(
	[
		'post_type'      => 'ramp_homepage_slide',
		'posts_per_page' => 5,
		'orderby'        => [
			'menu_order' => 'ASC',
			'post_date'  => 'DESC',
		],
	]
);

$slide_count = count( $slides );
?>

<div class="glide homepage-slider">
	<div class="glide__track" data-glide-el="track">
		<div class="glide__slides">
			<?php foreach ( $slides as $slide ) : ?>
				<?php
				$slide_title   = $slide->post_title;
				$slide_text    = $slide->post_content;
				$slide_img     = get_the_post_thumbnail_url( $slide, 'x-large' );
				$slide_img_alt = trim( wp_strip_all_tags( get_post_meta( $slide->ID, '_wp_attachment_image_alt', true ) ) );

				$slide_meta_text   = get_post_meta( $slide->ID, 'ramp_slide_meta_text', true );
				$slide_button_text = get_post_meta( $slide->ID, 'ramp_slide_button_text', true );
				$slide_button_url  = get_post_meta( $slide->ID, 'ramp_slide_button_url', true );
				?>
				<article class="glide__slide">
					<div class="homepage-slide">
						<div class="homepage-slide-left">
							<img class="homepage-slide-img" src="<?php echo esc_attr( $slide_img ); ?>" alt="" />

							<div class="homepage-slider-bullets homepage-slider-bullets-mobile container">
								<div class="glide__bullets" data-glide-el="controls[nav]">
									<?php for ( $slide_no = 1; $slide_no <= $slide_count; $slide_no++ ) : ?>
										<?php
										$slide_no_padded = strlen( $slide_no ) < 2 ? '0' . $slide_no : $slide_no;
										?>
										<a href="#" class="glide__bullet" data-glide-dir="=<?php echo esc_attr( $slide_no - 1 ); ?>"><?php echo esc_html( $slide_no_padded ); ?></a>
									<?php endfor; ?>
								</div>
							</div>
						</div>

						<div class="homepage-slide-right">
							<?php if ( $slide_meta_text ) : ?>
								<div class="tag-plain">
									<?php echo esc_html( $slide_meta_text ); ?>
								</div>
							<?php endif; ?>

							<h2 class="homepage-slide-title">
								<?php echo esc_html( $slide_title ); ?>
							</h2>

							<div class="homepage-slide-text">
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo apply_filters( 'the_content', $slide_text ); ?>
							</div>

							<?php if ( $slide_button_url && $slide_button_text ) : ?>
								<div class="homepage-slide-button">
									<a class="arrow-button slide-button" href="<?php echo esc_attr( $slide_button_url ); ?>"><?php echo esc_html( $slide_button_text ); ?></a>
								</div>
							<?php endif; ?>
						</div>
					</div><!-- .homepage-slide -->
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="homepage-slider-bullets homepage-slider-bullets-desktop container">
		<div class="glide__bullets" data-glide-el="controls[nav]">
			<?php for ( $slide_no = 1; $slide_no <= $slide_count; $slide_no++ ) : ?>
				<?php
				$slide_no_padded = strlen( $slide_no ) < 2 ? '0' . $slide_no : $slide_no;
				?>
				<a class="glide__bullet" data-glide-dir="=<?php echo esc_attr( $slide_no - 1 ); ?>"><?php echo esc_html( $slide_no_padded ); ?></a>
			<?php endfor; ?>
		</div>
	</div>
</div>
