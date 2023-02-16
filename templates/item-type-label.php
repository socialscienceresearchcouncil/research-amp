<?php

$is_edit_mode = ! empty( $args['is_edit_mode'] );

$label = $args['label'];
$url   = isset( $args['url'] ) ? $args['url'] : '';

$do_link = $url && ! $is_edit_mode;

?>

<div class="tag-plain item-type-label">
	<?php if ( $do_link ) : ?>
		<a href="<?php echo esc_html( $url ); ?>">
	<?php endif; ?>

	<?php echo esc_html( $label ); ?>

	<?php if ( $do_link ) : ?>
		</a>
	<?php endif; ?>
</div>
