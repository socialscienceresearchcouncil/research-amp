<div class="citation-library-count">
	<?php
	$citation_count = \SSRC\RAMP\CitationLibrary::get_citation_count();
	printf(
		esc_html(
			// translators: Number of citations
			_n(
				'%s Citation in the linked library on Zotero',
				'%s Citations in the linked library on Zotero',
				$citation_count,
				'ramp'
			)
		),
		esc_html( number_format_i18n( $citation_count ) )
	);
	?>
</div>
