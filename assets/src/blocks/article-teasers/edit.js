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

import ServerSideRender from '@wordpress/server-side-render'

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
	const { researchTopic } = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'research-topic-' + researchTopic )

		return useBlockProps( {
			className: classNames
		} )
	}

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Research Topic', 'ramp' ) }
					>
						<ResearchTopicSelector
							label={ __( 'Select the Research Topic whose Article will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ attributes }
					block="ramp/article-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
