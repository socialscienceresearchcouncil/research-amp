<?php

$number_of_items = isset( $args['numberOfItems'] ) ? (int) $args['numberOfItems'] : 3;

$selection_type = isset( $args['selectionType'] ) ? $args['selectionType'] : 'random';
if ( ! in_array( $selection_type, [ 'alphabetical', 'latest', 'random', 'specific' ], true ) ) {
	$selection_type = 'random';
}

$post_args = [
	'post_type'      => 'ssrc_restop_pt',
	'posts_per_page' => $number_of_items,
	'fields'         => 'ids',
];

switch ( $selection_type ) {
	case 'alphabetical' :
		$post_orderby = [ 'title' => 'ASC' ];
	break;

	case 'latest' :
		$post_orderby = [ 'date' => 'DESC' ];
	break;

	case 'specific' :
		$post_orderby = 'post__in';
	break;

	case 'random' :
	default :
		$post_orderby = 'rand';
	break;
}

$post__in = null;
if ( 'specific' === $selection_type ) {
	$post__in = [
		(int) $args['slot1'],
		(int) $args['slot2'],
		(int) $args['slot3'],
	];
}

if ( $post__in ) {
	$post_args['post__in'] = $post__in;
}

$post_args['orderby'] = $post_orderby;

$research_topics = get_posts( $post_args );

?>

<ul class="item-type-list item-type-list-flex item-type-list-research-topics">
	<?php foreach ( $research_topics as $research_topic ) : ?>
		<li>
			<?php ramp_get_template_part( 'teasers/research-topic', [ 'id' => $research_topic ] ); ?>
		</li>
	<?php endforeach; ?>
</ul>
