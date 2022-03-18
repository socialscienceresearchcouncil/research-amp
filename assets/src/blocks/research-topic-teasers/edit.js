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

import LoadMoreToggle from '../../components/LoadMoreToggle'
import NumberOfItemsControl from '../../components/NumberOfItemsControl'

import ServerSideRender from '@wordpress/server-side-render'

import { useSelect } from '@wordpress/data'

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
		numberOfItems,
		selectionType,
		showLoadMore,
		slot1,
		slot2,
		slot3
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
		const researchTopics = select( 'ramp' ).getResearchTopics()

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

	researchTopicsOptions.unshift( { label: __( 'Select a Research Topic', 'ramp' ), value: 0 } )

	return (
		<Fragment>
			<InspectorControls>
				<Panel>
					<PanelBody
						title={ __( 'Content Settings', 'ramp' ) }
					>
						<SelectControl
							label={ __( 'Research Topics to show', 'ramp' ) }
							value={ selectionType }
							options={ [
								{ label: __( 'Alphabetical', 'ramp' ), value: 'alphabetical' },
								{ label: __( 'Recently Added', 'ramp' ), value: 'latest' },
								{ label: __( 'Random', 'ramp' ), value: 'random' },
								{ label: __( 'Specific', 'ramp' ), value: 'specific' },
							] }
							onChange={ ( selectionType ) => setAttributes( { selectionType } ) }
						/>

						{ 'specific' === selectionType &&
							<fieldset>
								<legend>{ __( 'Select a Research Topic for each slot.', 'ramp' ) }</legend>
								<ul>
									<li>
										<SelectControl
											label={ __( 'Slot 1', 'ramp' ) }
											labelPosition="side"
											value={ slot1 }
											options={ researchTopicsOptions }
											onChange={ ( slot1 ) => setAttributes( { slot1 } ) }
										/>
									</li>

									<li>
										<SelectControl
											label={ __( 'Slot 2', 'ramp' ) }
											labelPosition="side"
											value={ slot2 }
											options={ researchTopicsOptions }
											onChange={ ( slot2 ) => setAttributes( { slot2 } ) }
										/>
									</li>

									<li>
										<SelectControl
											label={ __( 'Slot 3', 'ramp' ) }
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
			</InspectorControls>

			<div { ...blockProps() }>
				<ServerSideRender
					attributes={ attributes }
					block="ramp/research-topic-teasers"
					httpMethod="GET"
					LoadingResponsePlaceholder={ Spinner }
				/>
			</div>
		</Fragment>
	)
}
