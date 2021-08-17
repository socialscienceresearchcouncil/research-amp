<?php

namespace SSRC\Disinfo\Zotero;

class TranslationFetcher {
	protected $url;

	public function set_server_url( $url ) {
		$this->url = $url;
	}

	public function fetch( $item_url ) {
		$result = wp_remote_post(
			$this->url,
			[
				'body' => $item_url,
				'timeout' => 10,
				'headers' => [
					'Content-Type' => 'text/plain',
				]
			]
		);

		$response_code = wp_remote_retrieve_response_code( $result );
		if ( 200 !== $response_code ) {
			// @todo
			// save timestamp
			// we'll check later
			// maybe best to do this in client
			return null;
		}

		// Decode to an array.
		$json = json_decode( wp_remote_retrieve_body( $result ), true );

		return $json;
	}

	public static function get_item_data( $post_id ) {
		$zt_data = get_post_meta( $post_id, 'zt_data', true );

		return $zt_data;
	}
}
