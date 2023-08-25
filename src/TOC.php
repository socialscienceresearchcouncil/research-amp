<?php

namespace SSRC\RAMP;

use ezTOC;

/**
 * Helper methods for easy-table-of-contents integration.
 */
class TOC {
	public function init() {
		if ( ! class_exists( 'ezTOC' ) ) {
			return;
		}

		add_filter( 'the_content', [ $this, 'filter_the_content' ] );

		add_filter( 'ez_toc_get_option_show_heading_text', '__return_false' );
		add_filter(
			'ez_toc_get_option_counter',
			function () {
				return 'none';
			}
		);
	}

	public function filter_the_content( $content ) {
		$post_types = [
			'ramp_review',
			'ramp_article',
		];

		if ( ! is_singular( $post_types ) ) {
			return $content;
		}

		remove_filter( 'the_content', [ $this, 'filter_the_content' ] );
		$content = self::process_for_toc( $content );
		add_filter( 'the_content', [ $this, 'filter_the_content' ] );

		return $content;
	}

	public static function process_for_toc( $content, $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$post = ezTOC::get( $post_id );

		// Bail if no headings found.
		if ( ! $post->hasTOCItems() ) {
			return \Easy_Plugins\Table_Of_Contents\Debug::log()->appendTo( $content );
		}

		$find    = $post->getHeadings();
		$replace = $post->getHeadingsWithAnchors();
		$toc     = $post->getTOC();

		$GLOBALS['the_toc'] = $toc;

		return str_replace( $find, $replace, $content );
	}
}
