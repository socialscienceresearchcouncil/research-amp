import { __ } from '@wordpress/i18n'
import { useBlockProps } from '@wordpress/block-editor'

import './editor.scss'

export default function edit( {
	attributes,
	setAttributes
} ) {
	return (
		<div { ...useBlockProps() }>
			<div className="citation-link-zotero">
				{ __( 'See citation in Zotero library', 'ramp' ) }
			</div>

			<div className="citation-link-source">
				{ __( 'Go to citation source.', 'ramp' ) }
			</div>
		</div>
	)
}
