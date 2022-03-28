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
		className: [ 'sidebar-section' ]
	})

	const headingTextValue = headingText ?? __( 'Version', 'ramp' )

	const fakeTOC = (
		<div id="ez-toc-container" className="ez-toc-v2_0_17 counter-hierarchy ez-toc-grey">
			<nav>
				<ul className="ez-toc-list ez-toc-list-level-1">
					<li className="ez-toc-page-1 ez-toc-heading-level-2">
						<span className="ez-toc-link ez-toc-heading-1">Introduction</span>
					</li>

					<li className="ez-toc-page-1 ez-toc-heading-level-2">
						<span className="ez-toc-link ez-toc-heading-1">Some background on the project</span>
					</li>

					<li className="ez-toc-page-1 ez-toc-heading-level-2">
						<span className="ez-toc-link ez-toc-heading-1">An overview of the literature</span>

						<ul className="ez-toc-list-level-3">
							<li className="ez-toc-heading-level-3">
								<span className="ez-toc-link ez-toc-heading-6">United States</span>
							</li>

							<li className="ez-toc-heading-level-3">
								<span className="ez-toc-link ez-toc-heading-6">Europe</span>
							</li>
						</ul>
					</li>

					<li className="ez-toc-page-1 ez-toc-heading-level-2">
						<span className="ez-toc-link ez-toc-heading-1">Conclusion</span>
					</li>
				</ul>
			</nav>
		</div>
	)

	return (
		<>
			<div { ...blockProps }>
				<RichText
					className="sidebar-section-title"
					onChange={ (headingText) => setAttributes( { headingText } ) }
					tagName="h3"
					value={ headingTextValue }
				/>

				{ fakeTOC }
			</div>
		</>
	)
}
