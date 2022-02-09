<?php

namespace SSRC\RAMP;

class ResearchTopic {
	protected $data = [
		'post_id'      => null,
		'display_name' => '',
	];

	protected $meta_keys = [];

	public static function get_instance( $research_topic_id ) {
		$instance = new self();
		$instance->fill( $research_topic_id );
		return $instance;
	}

	public function exists() {
		return ! is_null( $this->data['post_id'] );
	}

	protected function fill( $research_topic_id ) {
		$post = get_post( $research_topic_id );

		if ( ! $post || 'ramp_topic' !== $post->post_type ) {
			return;
		}

		$this->data['post_id']      = $post->ID;
		$this->data['display_name'] = $post->post_title;

		foreach ( $this->meta_keys as $meta ) {
			$this->data[ $meta ] = get_post_meta( $post->ID, $meta, true );
		}
	}

	public function save() {
		foreach ( $this->meta_keys as $meta ) {
			update_post_meta( $this->data['post_id'], $meta, $this->data[ $meta ] );
		}
	}

	public function set( $key, $value ) {
		if ( in_array( $key, $this->meta_keys, true ) ) {
			$this->data[ $key ] = $value;
		}
	}

	public function get_post_id() {
		return (int) $this->data['post_id'];
	}

	public function get_meta_keys() {
		return $this->meta_keys;
	}

	public function get_display_name() {
		return $this->data['display_name'];
	}

	public function get_rt_term_id() {
		$rt_map = disinfo_app()->get_cpttax_map( 'research_topic' );
		return $rt_map->get_term_id_for_post_id( $this->get_post_id() );
	}

	public function get_articles( $args = [] ) {
		$query_args = array_merge(
			[
				'post_type'      => 'ramp_article',
				'posts_per_page' => -1,
				'tax_query'      => [
					[
						'taxonomy' => 'ramp_assoc_topic',
						'terms'    => $this->get_rt_term_id(),
						'field'    => 'term_id',
					],
				],
				'orderby'        => [ 'date' => 'DESC' ],
			],
			$args
		);

		return get_posts( $query_args );
	}

	public function get_research_reviews( $args = [] ) {
		$query_args = array_merge(
			[
				'post_type'      => 'ramp_review',
				'posts_per_page' => -1,
				'tax_query'      => [
					[
						'taxonomy' => 'ramp_assoc_topic',
						'terms'    => $this->get_rt_term_id(),
						'field'    => 'term_id',
					],
				],
			],
			$args
		);

		return get_posts( $query_args );
	}

	public function get_research_review() {
		$reviews = $this->get_research_reviews();
		if ( $reviews ) {
			return $reviews[0];
		} else {
			return false;
		}
	}
}
