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

import ContentModePanel from '../../components/ContentModePanel'

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
		contentMode,
		contentModeProfile,
		contentModeResearchTopic,
		numberOfItems,
		order,
		variationType
	} = attributes

	const blockProps = () => {
		let classNames = []

		// This is here to force the 'dirty' state.
		classNames.push( 'variation-type-' + variationType )
		classNames.push( 'order-' + order )
		classNames.push( 'content-mode-' + contentMode )

		return useBlockProps( {
			className: classNames
		} )
	}

	const spinner = <Spinner />

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<Fragment>
			<InspectorControls>
				<ContentModePanel
					changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
					changeProfileCallback={ ( profileObj ) => setAttributes( { contentModeProfile: profileObj.id } ) }
					changeResearchTopicCallback={ ( contentModeResearchTopic ) => setAttributes( { contentModeResearchTopic } ) }
					legend={ __( 'Determine which Research Reviews will be shown in this block.', 'ramp' ) }
					selectedMode={ contentMode }
					selectedProfile={ contentModeProfile }
					selectedResearchTopic={ contentModeResearchTopic }
				/>

				<Panel>
					<PanelBody
						title={ __( 'Display Variations', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select the display variation', 'ramp' ) }
							options={ [
								{ label: __( 'Horizontal', 'ramp' ), value: 'horizontal' },
								{ label: __( 'Teasers', 'ramp' ), value: 'teasers' },
							] }
							selected={ variationType }
							onChange={ ( variationType ) => setAttributes( { variationType } ) }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Select the order of Research Reviews', 'ramp' ) }
							options={ [
								{ label: __( 'Alphabetical', 'ramp' ), value: 'alphabetical' },
								{ label: __( 'Recently Added', 'ramp' ), value: 'latest' },
								{ label: __( 'Random', 'ramp' ), value: 'random' }
							] }
							selected={ order }
							onChange={ ( order ) => setAttributes( { order } ) }
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
