import { __ } from '@wordpress/i18n'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor'

import {
	PanelBody
} from '@wordpress/components'

/**
 * Editor styles.
 */
import './editor.scss'

/**
 * Edit function.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function edit( props ) {
	const blockProps = () => {
		const classNames = [ 'ramp-zotero-library-info-help' ]

		return useBlockProps( {
			className: classNames
		} )
	}

	return (
		<>
			<div { ...blockProps() }>
				<p>{ __( 'Enter the name of your Zotero Library above. This is for your reference only.', 'ramp' ) }</p>
				<p>{ __( 'In the sidebar, find the "Library Settings" section under the "Zotero Library" tab.', 'ramp' ) }</p>
				<p>{ __( 'Enter your Zotero group URL, group ID, and API key in the provided fields.', 'ramp' ) }</p>
			</div>
		</>
	);
}
