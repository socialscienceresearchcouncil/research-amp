<?php

if ( ! isset( $args['block']->context['postId'] ) ) {
	return '';
}

if ( 'ramp_citation' !== $args['block']->context['postType'] ) {
	return '';
}

$is_edit_mode = ! empty( $args['is_edit_mode'] );

if ( $is_edit_mode ) {
	$citation = SSRC\RAMP\Citation::get_from_post_id( $args['block']->context['postId'] );
} else {
	$citation = SSRC\RAMP\Citation::get_from_post_id( get_the_ID() );
}

$zotero_url = $citation->get_zotero_url();
$source_url = $citation->get_source_url();

$zotero_library = $citation->get_zotero_library();

?>

<div class="wp-block-research-amp-citation-links">
	<?php if ( $zotero_url ) : ?>
		<div class="citation-link-zotero">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_url( $zotero_url ); ?>">
			<?php endif; ?>

			<?php if ( $zotero_library ) : ?>
				<?php
				echo esc_html(
					sprintf(
						// translators: Zotero library name
						__( 'See citation in &lsquo;%s&rsquo; Zotero library', 'research-amp' ),
						$zotero_library->get_name()
					)
				);
				?>
			<?php else : ?>
				<?php esc_html_e( 'See citation in Zotero library', 'research-amp' ); ?>
			<?php endif; ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $source_url ) : ?>
		<div class="citation-link-source">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_url( $source_url ); ?>">
			<?php endif; ?>

			<?php esc_html_e( 'Go to citation source', 'research-amp' ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
