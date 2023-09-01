import { __ } from '@wordpress/i18n';

import {
	BlockControls,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	Spinner,
	ToolbarButton,
	ToolbarGroup
} from '@wordpress/components';

import { edit } from '@wordpress/icons';
import { useState } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

import { TaxonomyTermSearchControl } from '../../components/TaxonomyTermSearchControl';
import LoadMoreToggle from '../../components/LoadMoreToggle';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to set attributes.
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	const [ isEditing, setIsEditing ] = useState( false );

	const { focusTag, showLoadMore } = attributes;

	const showEditButton = true;

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					{ showEditButton && (
						<ToolbarButton
							className="components-toolbar__control"
							label={ __( 'Edit URL' ) }
							icon={ edit }
							onClick={ () => setIsEditing( ! isEditing ) }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Display Options', 'research-amp' ) }
					>
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
				{ isEditing && (
					<div className="focus-tag-selector">
						<h3>{ __( 'Focus Tag Browser', 'research-amp' ) }</h3>
						<p>
							{
								__( 'Content on your Research AMP site can be organized by topic using Focus Tags. Use the selector below to choose a Focus Tag, whose related content will be displayed in this block.', 'research-amp' )
							}
						</p>

						<div className="focus-tag-selector-input">
							<TaxonomyTermSearchControl
								taxonomy="ramp_focus_tag"
								value={ focusTag }
								onSelect={ ( termSlug ) => {
									setAttributes( { focusTag: termSlug } ) }
								}
								label={ __( 'Focus Tag', 'research-amp' ) }
							/>

							<Button
								className="focus-tag-selector-input__button"
								onClick={ () => setIsEditing( false ) }
								variant="secondary"
							>
								{ __( 'OK', 'research-amp' ) }
							</Button>
						</div>
					</div>
				) }

				{ ! isEditing && (
					<div className="focus-tag-display">
						<ServerSideRender
							attributes={ attributes }
							block="research-amp/focus-tag-content"
							httpMethod="GET"
							LoadingResponsePlaceholder={ Spinner }
						/>
						Displaying focus tags
					</div>
				) }
			</div>
		</>
	);
}
