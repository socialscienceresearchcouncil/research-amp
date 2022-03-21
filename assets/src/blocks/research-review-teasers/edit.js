import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	Spinner,
	Toolbar,
	ToolbarButton,
	ToolbarGroup
} from '@wordpress/components'

import {
	BlockControls,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element'

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

import ContentModeControl from '../../components/ContentModeControl'
import PublicationDateToggle from '../../components/PublicationDateToggle'

import { GridIcon } from '../../icons/Grid'
import { ListIcon } from '../../icons/List'

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

						<PanelRow>
							<SelectControl
								label={ __( 'Order', 'ramp' ) }
								options={ [
									{ label: __( 'Alphabetical', 'ramp' ), value: 'alphabetical' },
									{ label: __( 'Recently Added', 'ramp' ), value: 'latest' },
									{ label: __( 'Random', 'ramp' ), value: 'random' }
								] }
								value={ order }
								onChange={ ( order ) => setAttributes( { order } ) }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'ramp' ) }
					>
						<PanelRow>
							<PublicationDateToggle
								onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
								showPublicationDate={ showPublicationDate }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ ListIcon }
						isActive={ 'list' === variationType }
						label={ __( 'List', 'ramp' ) }
						onClick={ () => setAttributes( { variationType: 'list' } ) }
					/>
					<ToolbarButton
						icon={ GridIcon }
						isActive={ 'grid' === variationType }
						label={ __( 'Grid', 'ramp' ) }
						onClick={ () => setAttributes( { variationType: 'grid' } ) }
					/>
				</ToolbarGroup>
			</BlockControls>

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
