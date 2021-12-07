import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	SelectControl,
	Spinner
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import { Fragment } from '@wordpress/element'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

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
						title={ __( 'Number of Items', 'ramp' ) }
					>
						<NumberControl
							label={ __( 'Number of Research Topics to show', 'ramp' ) }
							value={ numberOfItems }
							min={ 0 }
							max={ 5 }
							step={ 1 }
							onChange={ ( numberOfItems ) => setAttributes( { numberOfItems } ) }
						/>
					</PanelBody>
				</Panel>

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
