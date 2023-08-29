<?php

$r = array_merge(
	[
		'id'                   => 0,
		'is_edit_mode'         => false,
		'show_item_type_label' => false,
		'show_research_topics' => false,
	],
	$args
);

$citation_id = $r['id'];

$citation_url = '';
if ( $citation_id ) {
	$citation_object = SSRC\RAMP\Citation::get_from_post_id( $citation_id );
	$citation_url    = $citation_object->get_preview_url();
}

$article_classes = [ 'teaser' ];

$is_edit_mode = $r['is_edit_mode'];

$article_classes = [ 'citation-teaser', 'teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<?php if ( $r['show_item_type_label'] ) : ?>
		<?php ramp_get_template_part( 'item-type-label', [ 'label' => __( 'Citation', 'research-amp' ) ] ); ?>
	<?php endif; ?>

	<div class="teaser-content citation-teaser-content">
		<?php if ( $r['show_research_topics'] ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tags',
				[
					'is_edit_mode' => $is_edit_mode,
					'item_id'      => $citation_id,
				]
			);
			?>
		<?php endif; ?>

		<h3 class="has-h-4-font-size item-title citation-item-title enforce-reading-width">
			<?php if ( ! $is_edit_mode ) : ?>
				<a href="<?php echo esc_url( $citation_url ); ?>">
			<?php endif; ?>

			<?php echo esc_html( $citation_object->get_title() ); ?>

			<?php if ( ! $is_edit_mode ) : ?>
				</a>
			<?php endif; ?>
		</h3>

		<?php ramp_get_template_part( 'citation-info', [ 'id' => $citation_id ] ); ?>
	</div>
</article>
