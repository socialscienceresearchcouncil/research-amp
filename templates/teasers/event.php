<?php
$event_id = $args['id'];

$start_date = tribe_get_start_date( $event_id, false, 'F j, Y' );
$end_date   = tribe_get_end_date( $event_id, false, 'F j, Y' );
if ( $start_date === $end_date ) {
	$event_date = $start_date;
} else {
	$event_date = sprintf( '%s - %s', $start_date, $end_date );
}

$article_classes = [ 'teaser' ];

?>

<article class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>">
	<div class="teaser-content news-item-teaser-content">
		<h1 class="item-title news-item-item-title"><a href="<?php echo esc_attr( get_permalink( $event_id ) ); ?>"><?php echo esc_html( get_the_title( $event_id ) ); ?></a></h1>

		<div class="item-footer-meta"><img role="presentation" alt="" src="<?php echo esc_attr( get_stylesheet_directory_uri() ); ?>/assets/icons/calendar-icon.svg" class="event-icon" /><?php echo esc_html( $event_date ); ?></div>
	</div>
</article>
