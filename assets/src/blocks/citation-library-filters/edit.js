import { __ } from '@wordpress/i18n';

import { Panel, PanelBody, Spinner } from '@wordpress/components';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param  root0
 * @param  root0.attributes
 * @param  root0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const blockProps = () => {
		return useBlockProps( {
			className: [],
		} );
	};

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
	} );

	return (
		<div { ...blockProps() }>
			<ServerSideRender
				attributes={ serverSideAtts }
				block="research-amp/citation-library-filters"
				httpMethod="GET"
			/>
		</div>
	);
}
