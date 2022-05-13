<?php

$r = array_merge(
	[
		'id' => 0,
	],
	$args
);

$citation_id = $r['id'];

$author        = '';
$publication   = '';
$citation_year = '';

if ( $citation_id ) {
	$citation_object = SSRC\RAMP\Citation::get_from_post_id( $citation_id );

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

<div class="citation-info">
	<?php if ( $author || $publication || $citation_year ) : ?>
		<dl>
			<?php if ( $author ) : ?>
				<div>
					<dt><?php esc_html_e( 'Author:', 'research-amp' ); ?></dt>
					<dd><?php echo esc_html( $author ); ?></dd>
				</div>
			<?php endif; ?>

			<?php if ( $publication ) : ?>
				<div>
					<dt><?php esc_html_e( 'Publication:', 'research-amp' ); ?></dt>
					<dd><?php echo esc_html( $publication ); ?></dd>
				</div>
			<?php endif; ?>

			<?php if ( $citation_year ) : ?>
				<div>
					<dt><?php esc_html_e( 'Year:', 'research-amp' ); ?></dt>
					<dd><?php echo esc_html( $citation_year ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>
	<?php endif; ?>
</div>
