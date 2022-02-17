import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	Spinner
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	setAttributes,
} ) {
	const blockProps = () => {
		return useBlockProps( {
			className: []
		} )
	}

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<div { ...blockProps() }>
			<ServerSideRender
				attributes={ serverSideAtts }
				block="ramp/citation-library-filters"
				httpMethod="GET"
			/>
		</div>
	)
}
