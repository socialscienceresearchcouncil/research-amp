import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	Spinner
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element'

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import ContentModeControl from '../../components/ContentModeControl'
import PublicationDateToggle from '../../components/PublicationDateToggle'

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
		contentModeProfileId,
		contentModeResearchTopicId,
		numberOfItems,
		order,
		showLoadMore,
		showPublicationDate,
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
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'ramp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							changeProfileIdCallback={ ( profileObj ) => setAttributes( { contentModeProfileId: profileObj.id } ) }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							legend={ __( 'Determine which Research Reviews will be shown in this block.', 'ramp' ) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'ramp' ) }
					>
						<PanelRow>
							<SelectControl
								label={ __( 'Layout', 'ramp' ) }
								options={ [
									{ label: __( 'Grid', 'ramp' ), value: 'grid' },
									{ label: __( 'List', 'ramp' ), value: 'list' },
								] }
								selected={ variationType }
								onChange={ ( variationType ) => setAttributes( { variationType } ) }
							/>
						</PanelRow>

						<PanelRow>
							<PublicationDateToggle
								onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
								showPublicationDate={ showPublicationDate }
							/>
						</PanelRow>
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
