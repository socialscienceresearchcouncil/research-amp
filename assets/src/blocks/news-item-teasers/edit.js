import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	SelectControl,
	Spinner,
	TextControl
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
	const {
		featuredItemId,
		researchTopic,
		variationType
	} = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'featured-item-id-' + featuredItemId )
		classNames.push( 'research-topic-' + researchTopic )
		classNames.push( 'variation-type-' + variationType )

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
							label={ __( 'Select the Research Topic whose News Items will be shown in this block.', 'ramp' ) }
							selected={ researchTopic }
							onChangeCallback={ ( researchTopic ) => setAttributes( { researchTopic } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Display', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select the format to be used when displaying News Items.', 'ramp' ) }
							options={ [
								{ label: __( 'Single row', 'ramp' ), value: 'single' },
								{ label: __( 'Two rows', 'ramp' ), value: 'two' },
								{ label: __( 'Featured + Two rows', 'ramp' ), value: 'three' },
							] }
							selected={ variationType }
							value={ variationType }
							onChange={ ( variationType ) => setAttributes( { variationType } ) }
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>

			{'three' === variationType &&
				<InspectorControls>
					<Panel>
						<PanelBody
							title={ __( 'Featured News Item', 'ramp' ) }
						>
							<TextControl
								label={ __( 'Enter the ID of the post you want to feature.', 'ramp' ) }
								value={ featuredItemId }
								onChange={ ( featuredItemId ) => setAttributes( { featuredItemId } ) }
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>
			}

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="ramp/news-item-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	)
}
