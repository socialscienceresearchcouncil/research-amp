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

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param {Object}     props Component     props.
 * @param {Object}     props.attributes    Block attributes.
 * @param {Function}   props.setAttributes Block attributes setter.
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const {
		contentMode,
		contentModeProfileId,
		contentModeResearchTopicId,
		numberOfItems,
		order,
		showLoadMore,
	} = attributes;

	const customClassNames = [
		'number-of-items-' + numberOfItems,
		'content-mode-' + contentMode,
	];

	const blockProps = useBlockProps( { className: customClassNames } );

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
								setAttributes( { contentMode: newContentMode } )
							}
							changeProfileIdCallback={ ( newContentModeProfileId ) => 
								setAttributes( { contentModeProfileId: newContentModeProfileId } ) 
							}
							changeResearchTopicIdCallback={ ( newContentModeResearchTopicId ) =>
								setAttributes( { contentModeResearchTopicId: newContentModeResearchTopicId } )
							}
							enabledModes={ [ 'auto', 'all', 'advanced', 'featured' ] }
							glossAuto={ __( 'Show Citations relevant to the current Research Topic or Profile context.', 'research-amp' ) }
							glossAll={ __( 'Pull from all Citations.', 'research-amp' ) }
							glossAdvanced={ __( 'Show Citations associated with a specific Research Topic or Profile.', 'research-amp' ) }
							glossFeatured={ __( 'Show Citations marked as Featured.', 'research-amp' ) }
							labelAuto={ __( 'Relevant Citations', 'research-amp' ) }
							labelAll={ __( 'All Citations', 'research-amp' ) }
							legend={ __( 'Determine which Citations will be shown in this block.', 'research-amp' ) }
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
									{
										label: __( 'Date Added', 'research-amp' ),
										value: 'addedDate',
									},
									{
										label: __( 'Publication Date', 'research-amp' ),
										value: 'publicationDate',
									},
								] }
								value={ order }
								onChange={ ( newOrder ) =>
									setAttributes( { order: newOrder } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<NumberOfItemsControl
								numberOfItems={ numberOfItems }
								onChangeCallback={ ( newNumberOfItems ) =>
									setAttributes( { numberOfItems: newNumberOfItems } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<LoadMoreToggle
								showLoadMore={ showLoadMore }
								onChangeCallback={ ( newShowLoadMore ) =>
									setAttributes( { showLoadMore: newShowLoadMore } )
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="research-amp/citation-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	);
}
