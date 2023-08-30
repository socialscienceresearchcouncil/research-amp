import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	Spinner,
	ToggleControl,
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
 * @param  root0
 * @param  root0.attributes
 * @param  root0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const {
		contentMode,
		contentModeProfileId,
		contentModeResearchTopicId,
		numberOfItems,
		order,
		researchTopic,
		showLoadMore,
	} = attributes;

	const blockProps = () => {
		const classNames = [];

		classNames.push( 'number-of-items-' + numberOfItems );
		classNames.push( 'content-mode-' + contentMode );

		return useBlockProps( {
			className: classNames,
		} );
	};

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
							changeCallback={ ( contentMode ) =>
								setAttributes( { contentMode } )
							}
							changeProfileIdCallback={ (
								contentModeProfileId
							) => setAttributes( { contentModeProfileId } ) }
							changeResearchTopicIdCallback={ (
								contentModeResearchTopicId
							) =>
								setAttributes( { contentModeResearchTopicId } )
							}
							glossAuto={ __(
								'Show Citations relevant to the current Research Topic or Profile context.',
								'research-amp'
							) }
							glossAll={ __(
								'Pull from all Citations.',
								'research-amp'
							) }
							glossAdvanced={ __(
								'Show Citations associated with a specific Research Topic or Profile.',
								'research-amp'
							) }
							labelAuto={ __(
								'Relevant Citations',
								'research-amp'
							) }
							labelAll={ __( 'All Citations', 'research-amp' ) }
							legend={ __(
								'Determine which Citations will be shown in this block.',
								'research-amp'
							) }
							selectedMode={ contentMode }
							selectedProfileId={ contentModeProfileId }
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
										label: __(
											'Date Added',
											'research-amp'
										),
										value: 'addedDate',
									},
									{
										label: __(
											'Publication Date',
											'research-amp'
										),
										value: 'publicationDate',
									},
								] }
								value={ order }
								onChange={ ( order ) =>
									setAttributes( { order } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<NumberOfItemsControl
								numberOfItems={ numberOfItems }
								onChangeCallback={ ( numberOfItems ) =>
									setAttributes( { numberOfItems } )
								}
							/>
						</PanelRow>

						<PanelRow>
							<LoadMoreToggle
								showLoadMore={ showLoadMore }
								onChangeCallback={ ( showLoadMore ) =>
									setAttributes( { showLoadMore } )
								}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ serverSideAtts }
					block="research-amp/citation-teasers"
					httpMethod="GET"
				/>
			</div>
		</Fragment>
	);
}
