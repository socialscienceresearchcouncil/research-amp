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

import { Fragment } from '@wordpress/element'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

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
	const { numberOfItems } = attributes

	const blockProps = () => {
		let classNames = []

		// This is here to force the 'dirty' state.
		classNames.push( 'number-of-items-' . numberOfItems )

		return useBlockProps( {
			className: classNames
		} )
	}

	const spinner = <Spinner />

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Number of Items', 'ramp' ) }
					>
						<NumberControl
							label={ __( 'Number of Research Topics to show', 'ramp' ) }
							value={ numberOfItems }
							min={ 0 }
							max={ 5 }
							step={ 1 }
							onChange={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ attributes }
					block="ramp/research-topics"
					httpMethod="GET"
					LoadingResponsePlaceholder={ Spinner }
				/>
			</div>
		</Fragment>
	)
}
