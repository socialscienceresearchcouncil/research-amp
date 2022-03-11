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

import { Fragment } from '@wordpress/element'

import ResearchTopicSelector from '../../components/ResearchTopicSelector'
import LoadMoreToggle from '../../components/LoadMoreToggle'

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
	const { numberOfItems, researchTopic, showLoadMore } = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'research-topic-' + researchTopic )

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
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose Profiles will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>

					<PanelBody
						title={ __( 'Load More', 'ramp' ) }
					>
						<LoadMoreToggle
							showLoadMore={ showLoadMore }
							onChangeCallback={ ( showLoadMore ) => setAttributes( { showLoadMore } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/profile-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
