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

import ServerSideRender from '@wordpress/server-side-render'

import { Fragment } from '@wordpress/element'

import ContentModeControl from '../../components/ContentModeControl'
import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'
import HorizontalSwipeToggle from '../../components/HorizontalSwipeToggle'

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
		contentModeResearchTopicId,
		horizontalSwipe,
		numberOfItems,
		order,
		showLoadMore
	} = attributes

	const blockProps = () => {
		let classNames = []

		classNames.push( 'content-mode-' + contentMode )

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
						title={ __( 'Content Settings', 'ramp' ) }
					>
						<ContentModeControl
							changeCallback={ ( contentMode ) => setAttributes( { contentMode } ) }
							disabledTypes={ { 'profile': true } }
							changeResearchTopicIdCallback={ ( contentModeResearchTopicId ) => setAttributes( { contentModeResearchTopicId } ) }
							glossAuto={ __( 'Show Profiles relevant to the current Research Topic or Profile context.', 'ramp' ) }
							glossAll={ __( 'Pull from all Profiles.', 'ramp' ) }
							glossAdvanced={__( 'Show Profiles associated with a specific Research Topic or Profile.', 'ramp' )}
							labelAuto={ __( 'Relevant Profiles', 'ramp' ) }
							labelAll={ __( 'All Profiles', 'ramp' ) }
							legend={ __( 'Determine which Profiles will be shown in this block.', 'ramp' ) }
							selectedMode={ contentMode }
							selectedResearchTopicId={ contentModeResearchTopicId }
						/>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Order and Pagination', 'ramp' ) }
					>
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
						title={ __( 'Display Options', 'ramp' ) }
					>
						<PanelRow>
							<HorizontalSwipeToggle
								onChangeCallback={ ( horizontalSwipe ) => setAttributes( { horizontalSwipe } ) }
								horizontalSwipe={ horizontalSwipe }
							/>
						</PanelRow>
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
