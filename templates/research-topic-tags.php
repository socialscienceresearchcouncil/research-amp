<?php

$associated_research_topics = get_the_terms( $args['item_id'], 'ramp_assoc_topic' );

$display_type = ! empty( $args['display_type'] ) && 'plain' === $args['display_type'] ? 'plain' : 'bubble';

?>

<?php if ( $associated_research_topics ) : ?>
	<div class="research-topic-tags">
		<?php foreach ( $associated_research_topics as $associated_research_topic ) : ?>
			<?php
			ramp_get_template_part(
				'research-topic-tag',
				[
					'display_type' => $display_type,
					'term_id'      => $associated_research_topic->term_id,
				]
			);
			?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
