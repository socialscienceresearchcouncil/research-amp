<?php
$r = array_merge(
	[
		'headingText'   => '',
		'itemType'      => 'article',
		'isEditMode'    => false,
		'numberOfItems' => 3,
		'postId'        => get_queried_object_id(), // limit based on current item taxonomy?
	],
	$args
);

if ( $r['isEditMode'] ) {
	$citation = $r['citationText'];
} else {
	$citation = get_post_meta( $r['postId'], 'formatted_citation', true );
}

$item_type = 'news-item' === $r['itemType'] ? 'news-item' : 'article';
switch ( $item_type ) {
	case 'news-item' :
		$default_heading_text = __( 'Suggested News Items', 'ramp' );
		$teaser_block         = 'news-item-teasers';
	break;

	case 'article' :
	default :
		$default_heading_text = __( 'Suggested Articles', 'ramp' );
		$teaser_block         = 'article-teasers';
	break;
}

$heading_text = $r['headingText'] ? $r['headingText'] : $default_heading_text;

$teaser_block_atts = [
	'contentMode'         => 'all',
	'numberOfItems'       => $r['numberOfItems'],
	'order'               => 'recent',
	'showByline'          => true,
	'showPublicationDate' => false,
	'showImage'           => false,
	'showResearchTopics'  => false,
	'showRowRules'        => false,
	'titleSize'           => 'h-5',
	'variationType'       => 'list',
];

$classnames = [
	'wp-block-ramp-suggested-items',
	'sidebar-section',
];

?>

<div class="<?php echo esc_attr( implode( ' ', $classnames ) ); ?>">
	<h3 class="sidebar-section-title"><?php echo esc_html( $heading_text ); ?></h3>

	<div class="section-content">
		<?php ramp_get_template_part( 'blocks/' . $teaser_block, $teaser_block_atts ); ?>
	</div>
</div>
