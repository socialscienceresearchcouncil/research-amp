import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl
} from '@wordpress/components';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

import { Fragment } from '@wordpress/element';

import ContentModeControl from '../../components/ContentModeControl';
import LoadMoreToggle from '../../components/LoadMoreToggle';
import NumberOfItemsControl from '../../components/NumberOfItemsControl';
import HorizontalSwipeToggle from '../../components/HorizontalSwipeToggle';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param {Object}   props Component props.
 * @param {Object}   props.attributes Block attributes.
 * @param {Function} props.setAttributes Block attributes setter.
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const {
		contentMode,
		contentModeResearchTopicId,
		horizontalSwipe,
		numberOfItems,
		order,
		showLoadMore,
	} = attributes;

	const classNames = [ 'content-mode-' + contentMode ];
	const blockProps = useBlockProps( { className: classNames } );

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
	} );

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'research-amp' ) }
					>
						<ContentModeControl
							changeCallback={ ( newContentMode ) =>
								setAttributes( { newContentMode } )
							}
							disabledTypes={ { profile: true } }
							changeResearchTopicIdCallback={ ( newContentModeResearchTopicId ) =>
								setAttributes( { newContentModeResearchTopicId } )
							}
							glossAuto={ __( 'Show Profiles relevant to the current Research Topic or Profile context.', 'research-amp' ) }
							glossAll={ __( 'Pull from all Profiles.', 'research-amp' ) }
							glossAdvanced={ __( 'Show Profiles associated with a specific Research Topic or Profile.', 'research-amp' ) }
							labelAuto={ __( 'Relevant Profiles', 'research-amp' ) }
							labelAll={ __( 'All Profiles', 'research-amp' ) }
							legend={ __( 'Determine which Profiles will be shown in this block.', 'research-amp' ) }
							selectedMode={ contentMode }
							selectedResearchTopicId={
								contentModeResearchTopicId
							}
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
									{
										label: __( 'Alphabetical', 'research-amp' ),
										value: 'alphabetical',
									},
									{
										label: __( 'Recently Added', 'research-amp' ),
										value: 'latest',
									},
									{
										label: __( 'Random', 'research-amp' ),
										value: 'random',
									},
								] }
								value={ order }
								onChange={ ( newOrder ) =>
									setAttributes( { newOrder } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<NumberOfItemsControl
								numberOfItems={ numberOfItems }
								onChangeCallback={ ( newNumberOfItems ) =>
									setAttributes( { newNumberOfItems } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<LoadMoreToggle
								showLoadMore={ showLoadMore }
								onChangeCallback={ ( newShowLoadMore ) =>
									setAttributes( { newShowLoadMore } )
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>

				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'research-amp' ) }
					>
						<PanelRow>
							<HorizontalSwipeToggle
								onChangeCallback={ ( newHorizontalSwipe ) =>
									setAttributes( { newHorizontalSwipe } )
								}
								horizontalSwipe={ horizontalSwipe }
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="research-amp/profile-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	);
}
