<?php
$citation_id = $args['id'];

$article_classes = [ 'teaser' ];

$citation_url  = '';
$author        = '';
$publication   = '';
$citation_year = '';

if ( $citation_id ) {
	$citation_object = SSRC\RAMP\Citation::get_from_post_id( $citation_id );
	$citation_url    = $citation_object->get_preview_url();

	$author_names = $citation_object->get_author_names();
	$author       = implode( '; ', $author_names );

	// @todo Need coverage for different itemTypes.
	$zotero_data = $citation_object->get_zotero_data();
	if ( ! empty( $zotero_data['publicationTitle'] ) ) {
		$publication = $zotero_data['publicationTitle'];
	}

	$citation_year = $citation_object->get_publication_year();
}

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$article_classes = [ 'citation-teaser', 'teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content citation-teaser-content">
		<h3 class="has-h-4-font-size item-title citation-item-title enforce-reading-width">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_url( $citation_url ); ?>">
			<?php endif; ?>

			<?php echo esc_html( $citation_object->get_title() ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<?php if ( $author || $publication || $citation_year ) : ?>
		<dl>
			<?php if ( $author ) : ?>
				<div>
					<dt><?php esc_html_e( 'Author:', 'ramp' ); ?></dt>
					<dd><?php echo esc_html( $author ); ?></dd>
				</div>
			<?php endif; ?>

			<?php if ( $publication ) : ?>
				<div>
					<dt><?php esc_html_e( 'Publication:', 'ramp' ); ?></dt>
					<dd><?php echo esc_html( $publication ); ?></dd>
				</div>
			<?php endif; ?>

			<?php if ( $citation_year ) : ?>
				<div>
					<dt><?php esc_html_e( 'Year:', 'ramp' ); ?></dt>
					<dd><?php echo esc_html( $citation_year ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>
		<?php endif; ?>
	</div>
</article>
