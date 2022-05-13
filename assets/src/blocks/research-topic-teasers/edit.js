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

import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'
import HorizontalSwipeToggle from '../../components/HorizontalSwipeToggle'

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

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
		horizontalSwipe,
		numberOfItems,
		selectionType,
		showLoadMore,
		slot1,
		slot2,
		slot3,
		variationType
	} = attributes

	const blockProps = () => {
		let classNames = []

		// This is here to force the 'dirty' state.
		classNames.push( 'number-of-items-' + numberOfItems )
		classNames.push( 'selection-type-' + selectionType )
		classNames.push( 'slots-' + slot1 + '-' + slot2 + '-' + slot3 )

		return useBlockProps( {
			className: classNames
		} )
	}

	const spinner = <Spinner />

	const { researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'research-amp' ).getResearchTopics()

		return {
			researchTopics
		}
	} )

	let researchTopicsOptions = researchTopics.map( ( topic ) => {
		return {
			label: topic.title.rendered,
			value: topic.id
		}
	} )

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
	} )

	researchTopicsOptions.unshift( { label: __( 'Select a Research Topic', 'research-amp' ), value: 0 } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Order and Pagination', 'research-amp' ) }
					>
						<SelectControl
							label={ __( 'Order', 'research-amp' ) }
							value={ selectionType }
							options={ [
								{ label: __( 'Alphabetical', 'research-amp' ), value: 'alphabetical' },
								{ label: __( 'Recently Added', 'research-amp' ), value: 'latest' },
								{ label: __( 'Random', 'research-amp' ), value: 'random' },
								{ label: __( 'Specific', 'research-amp' ), value: 'specific' },
							] }
							onChange={ ( selectionType ) => setAttributes( { selectionType } ) }
						/>

						{ 'specific' === selectionType &&
							<fieldset>
								<legend>{ __( 'Select a Research Topic for each slot.', 'research-amp' ) }</legend>
								<ul>
									<li>
										<SelectControl
											label={ __( 'Slot 1', 'research-amp' ) }
											labelPosition="side"
											value={ slot1 }
											options={ researchTopicsOptions }
											onChange={ ( slot1 ) => setAttributes( { slot1 } ) }
										/>
									</li>

									<li>
										<SelectControl
											label={ __( 'Slot 2', 'research-amp' ) }
											labelPosition="side"
											value={ slot2 }
											options={ researchTopicsOptions }
											onChange={ ( slot2 ) => setAttributes( { slot2 } ) }
										/>
									</li>

									<li>
										<SelectControl
											label={ __( 'Slot 3', 'research-amp' ) }
											labelPosition="side"
											value={ slot3 }
											options={ researchTopicsOptions }
											onChange={ ( slot3 ) => setAttributes( { slot3 } ) }
										/>
									</li>
								</ul>
							</fieldset>
						}

						{ 'specific' !== selectionType &&
							<>
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
							</>
						}
					</PanelBody>

				</Panel>

				{ 'grid' === variationType && (
					<Panel>
						<PanelBody
							title={ __( 'Display Options', 'research-amp' ) }
						>
								<PanelRow>
									<HorizontalSwipeToggle
										onChangeCallback={ ( horizontalSwipe ) => setAttributes( { horizontalSwipe } ) }
										horizontalSwipe={ horizontalSwipe }
									/>
								</PanelRow>
						</PanelBody>
					</Panel>
				) }
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
					block="ramp/research-topic-teasers"
					httpMethod="GET"
					LoadingResponsePlaceholder={ Spinner }
				/>
			</div>
		</Fragment>
	)
}
