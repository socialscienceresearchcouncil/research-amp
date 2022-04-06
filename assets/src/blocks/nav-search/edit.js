import './editor.scss'

import classNames from 'classnames'

import { __ } from '@wordpress/i18n'

import { useBlockProps } from '@wordpress/block-editor'


export default function edit( {
	attributes,
	setAttributes
} ) {
	const blockProps = useBlockProps()

	return (
		<div { ...blockProps }>
			<div className="wp-block-buttons">
				<div className="wp-block-button nav-search-button">
					<span className="screen-reader-text">{ __( 'Click to search site', 'ramp' ) }</span>
				</div>
			</div>
		</div>
	)
}
