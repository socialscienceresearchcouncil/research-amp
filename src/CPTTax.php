<?php

namespace SSRC\RAMP;

class CPTTax {
	protected $post_type;
	protected $taxonomy;

	public function __construct( $post_type, $taxonomy ) {
		$this->post_type = $post_type;
		$this->taxonomy  = $taxonomy;

		add_action( 'save_post', [ $this, 'maybe_create_tax_term' ], 15, 3 );
		add_action( 'before_delete_post', [ $this, 'maybe_delete_tax_term' ] );
	}

	public function maybe_create_tax_term( $post_id, $post, $update ) {
		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		$create = false;
		if ( $update ) {
			$term_id = $this->get_term_id_for_post_id( $post_id );
			if ( $term_id ) {
				$term = get_term( $term_id, $this->taxonomy );
				if ( $term->slug !== $post->post_name || $term->name !== $post->post_title ) {
					wp_update_term(
						$term->term_id,
						$this->taxonomy,
						[
							'name' => $post->post_title,
							'slug' => $post->post_name,
						]
					);
				}
			} else {
				$create = true;
			}
		} else {
			$create = true;
		}

		if ( $create ) {
			$term = wp_insert_term(
				$post->post_title,
				$this->taxonomy,
				[
					'slug' => $post->post_name
				]
			);

			$term_id = $term['term_id'];

			update_term_meta( $term_id, 'post_id', $post_id );
			update_post_meta( $post_id, 'term_id', $term_id );
		}
	}

	public function maybe_delete_tax_term( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || $this->post_type !== $post->post_type ) {
			return;
		}

		$term_id = $this->get_term_id_for_post_id( $post_id );
		if ( ! $term_id ) {
			return;
		}

		wp_delete_term( $term_id, $this->taxonomy );
	}

	public function get_term_id_for_post_id( $post_id ) {
		return (int) get_post_meta( $post_id, 'term_id', true );
	}

	public function get_post_id_for_term_id( $term_id ) {
		return (int) get_term_meta( $term_id, 'post_id', true );
	}
}
