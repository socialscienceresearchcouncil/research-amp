import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	SelectControl,
	Spinner
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import ResearchTopicSelector from '../../components/ResearchTopicSelector'

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
	const {
		order,
		researchTopic
	} = attributes

	const blockProps = () => {
		let classNames = []

		// This is here to force the 'dirty' state.
		classNames.push( 'order' + order )
		classNames.push( 'research-topic-' + researchTopic )

		return useBlockProps( {
			className: classNames
		} )
	}

	const spinner = <Spinner />

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose Research Reviews will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select the order of Research Reviews', 'ramp' ) }
							value={ order }
							options={ [
								{ label: __( 'Alphabetical', 'ramp' ), value: 'alphabetical' },
								{ label: __( 'Recently Added', 'ramp' ), value: 'latest' },
								{ label: __( 'Random', 'ramp' ), value: 'random' }
							] }
							onChange={ ( selectionType ) => setAttributes( { selectionType } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/research-review-teasers"
					httpMethod="GET"
					LoadingResponsePlaceholder={ Spinner }
				/>
			</div>
		</Fragment>
	)
}
