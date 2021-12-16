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

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content article-teaser-content">
		<h1 class="item-title article-item-title"><a href="<?php echo esc_url( $citation_url ); ?>"><?php echo esc_html( $citation_object->get_title() ); ?></a></h1>

		<?php if ( $author || $publication || $citation_year ) : ?>
		<dl>
			<?php if ( $author ) : ?>
				<div>
					<dt><?php esc_html_e( 'Author(s):', 'ramp' ); ?></dt>
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
