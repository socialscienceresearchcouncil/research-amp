import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import {
	InspectorControls,
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { store as coreStore } from '@wordpress/core-data'
import { store as postStore } from '@wordpress/editor'

import { useSelect } from '@wordpress/data'

import { PanelBody } from '@wordpress/components'

import PublicationDateToggle from '../../components/PublicationDateToggle'

export default function edit( {
	context: { postType, postId },
	attributes,
	setAttributes
} ) {
	const {
		headingText,
		showPublicationDate
	} = attributes

	const blockProps = useBlockProps({
		className: [ 'is-latest-version', 'sidebar-section' ]
	})

	const headingTextValue = headingText ?? __( 'Version', 'ramp' )

	const fakeSelect = (
		<div className="fake-select-container">
			<span className="select2-selection__rendered">1.1</span>
			<span className="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
		</div>
	)

	return (
		<>
			<div { ...blockProps }>
				<div className="version-selector">
					<RichText
						className="sidebar-section-title"
						onChange={ (headingText) => setAttributes( { headingText } ) }
						tagName="h3"
						value={ headingTextValue }
					/>

					{ fakeSelect }
				</div>
			</div>
		</>
	)
}
