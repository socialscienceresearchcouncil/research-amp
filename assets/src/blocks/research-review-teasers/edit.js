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

	const { researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'ramp' ).getResearchTopics()

		return {
			researchTopics
		}
	} )

	let researchTopicsOptions = researchTopics.map( ( topic ) => {
		return {
			label: topic.title.rendered,
			value: topic.id.toString()
		}
	} )

	researchTopicsOptions.unshift( { label: __( 'Use current Research Topic', 'ramp' ), value: 'auto' } )
	researchTopicsOptions.unshift( { label: __( 'Select a Research Topic', 'ramp' ), value: '' } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select the Research Topic whose Research Reviews will be shown in this block.', 'ramp' ) }
							value={ researchTopic }
							options={ researchTopicsOptions }
							onChange={ ( researchTopic ) => setAttributes( { researchTopic } ) }
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
					attributes={ attributes }
					block="ramp/research-review-teasers"
					httpMethod="GET"
					LoadingResponsePlaceholder={ Spinner }
				/>
			</div>
		</Fragment>
	)
}
