import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	Spinner,
	ToggleControl
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

import { Fragment } from '@wordpress/element'

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
		numberOfItems,
		researchTopic,
		showFilters
	} = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'number-of-items-' + numberOfItems )
		classNames.push( 'research-topic-' + researchTopic )
		classNames.push( 'show-filters-' + ( showFilters ? 'on' : 'off' ) )

		return useBlockProps( {
			className: classNames
		} )
	}

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Number of Items', 'ramp' ) }
					>
						<NumberControl
							label={ __( 'Number of Citations to show', 'ramp' ) }
							value={ numberOfItems }
							min={ 0 }
							max={ 5 }
							step={ 1 }
							onChange={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Filters', 'ramp' ) }
					>
						<ToggleControl
							label={ __( 'Show Citation Library directory filters?', 'ramp' ) }
							checked={ showFilters }
							onChange={ ( showFilters ) => setAttributes( { showFilters } ) }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose Citations will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/citation-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
