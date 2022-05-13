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
import HorizontalSwipeToggle from '../../components/HorizontalSwipeToggle'
import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'

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
		horizontalSwipe,
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
						title={ __( 'Content Settings', 'research-amp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							changeProfileIdCallback={ ( contentModeProfileId ) => setAttributes( { contentModeProfileId } ) }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							glossAuto={ __( 'Show Research Reviews relevant to the current Research Topic or Profile context.', 'research-amp' ) }
							glossAll={ __( 'Pull from all Research Reviews.', 'research-amp' ) }
							glossAdvanced={__( 'Show Research Reviews associated with a specific Research Topic or Profile.', 'research-amp' )}
							labelAuto={ __( 'Relevant Research Reviews', 'research-amp' ) }
							labelAll={ __( 'All Research Reviews', 'research-amp' ) }
							legend={ __( 'Determine which Research Reviews will be shown in this block.', 'research-amp' ) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order and Pagination', 'research-amp' ) }
					>
						<PanelRow>
							<SelectControl
								label={ __( 'Order', 'research-amp' ) }
								options={ [
									{ label: __( 'Alphabetical', 'research-amp' ), value: 'alphabetical' },
									{ label: __( 'Recently Added', 'research-amp' ), value: 'latest' },
									{ label: __( 'Random', 'research-amp' ), value: 'random' }
								] }
								value={ order }
								onChange={ ( order ) => setAttributes( { order } ) }
							/>
						</PanelRow>

						<PanelRow>
							<NumberOfItemsControl
								numberOfItems={ numberOfItems }
								onChangeCallback={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
							/>
						</PanelRow>

						<PanelRow>
							<LoadMoreToggle
								showLoadMore={ showLoadMore }
								onChangeCallback={ ( showLoadMore ) => setAttributes( { showLoadMore } ) }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'research-amp' ) }
					>
						<PanelRow>
							<PublicationDateToggle
								onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
								showPublicationDate={ showPublicationDate }
							/>
						</PanelRow>

						{ 'grid' === variationType && (
							<PanelRow>
								<HorizontalSwipeToggle
									onChangeCallback={ ( horizontalSwipe ) => setAttributes( { horizontalSwipe } ) }
									horizontalSwipe={ horizontalSwipe }
								/>
							</PanelRow>
						) }
					</PanelBody>
				</Panel>
			</InspectorControls>

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ ListIcon }
						isActive={ 'list' === variationType }
						label={ __( 'List', 'research-amp' ) }
						onClick={ () => setAttributes( { variationType: 'list' } ) }
					/>
					<ToolbarButton
						icon={ GridIcon }
						isActive={ 'grid' === variationType }
						label={ __( 'Grid', 'research-amp' ) }
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
