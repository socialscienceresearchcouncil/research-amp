import './editor.scss'
import '../../../css/directory-filters.css'

import classNames from 'classnames'

import { __, sprintf } from '@wordpress/i18n'

import {
	InnerBlocks,
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { store as coreStore } from '@wordpress/core-data'
import { store as postStore } from '@wordpress/editor'

import { useSelect } from '@wordpress/data'

import FakeSelect from '../../components/FakeSelect'

import PublicationDateToggle from '../../components/PublicationDateToggle'

export default function edit( {
	attributes,
	setAttributes
} ) {
	const {
		buttonText,
		label,
		placeholder,
		typeSelectorLabel
	} = attributes

	const blockProps = useBlockProps()

	const inputClassnames = classNames( {
		'wp-block-search__input': true,
		'search-input': true
	} )

	return (
		<div { ...blockProps }>
			<div className="search-form-top">
				<RichText
					className="wp-block-search__label search-form-label has-h-3-font-size"
					aria-label={ __( 'Label text', 'research-amp' ) }
					placeholder={ __( 'Add label…', 'research-amp' ) }
					withoutInteractiveFormatting
					value={ label }
					onChange={ ( html ) => setAttributes( { label: html } ) }
				/>

				<input
					className={ inputClassnames }
					aria-label={ __( 'Optional placeholder text', 'research-amp' ) }
					placeholder={
						placeholder ? undefined : __( 'Optional placeholder…', 'research-amp' )
					}
					value={ placeholder }
					onChange={ ( event ) =>
						setAttributes( { placeholder: event.target.value } )
					}
				/>
			</div>

			<div className="search-form-bottom">
				<div className="search-type-container-label">
					<RichText
						aria-label={ __( 'Label text', 'research-amp' ) }
						placeholder={ __( 'Add label…', 'research-amp' ) }
						withoutInteractiveFormatting
						value={ typeSelectorLabel }
						onChange={ ( typeSelectorLabel ) => setAttributes( { typeSelectorLabel } ) }
						multiline={ false }
					/>
				</div>

				<div className="search-type-select-container">
					<FakeSelect text={ __( 'All content types', 'research-amp' ) } />
				</div>

				<div className="wp-block-button is-style-primary">
					<RichText
						aria-label={ __( 'Button text', 'research-amp' ) }
						className="wp-block-button__link"
						placeholder={ __( 'Add label…', 'research-amp' ) }
						withoutInteractiveFormatting
						value={ buttonText }
						onChange={ ( buttonText ) => setAttributes( { buttonText } ) }
						multiline={ false }
					/>
				</div>
			</div>
		</div>
	)
}
