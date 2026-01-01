/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { addQueryArgs } from '@wordpress/url';
// import { useState } from '@wordpress/element';
import { ExternalLink } from '@wordpress/components';
// import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import edit from './edit';

const attributes = Object.assign( {}, metadata.attributes );

const toShortcode = (e) => {
	if ( e.hash && e.hash.indexOf('[') == 0 )
		return e.hash;

  let t = "[advanced-members]";
  return (
    e.hash ? (t = t.replace(/\]$/, ` form="${e.hash}"]`)) : e.form && (t = t.replace(/\]$/, ` form="${e.form}"]`)),
    e.title && (t = t.replace(/\]$/, ` title="${e.title}"]`)),
    t
  );
};

registerBlockType( 'amem/form', {
	title: __( 'Adv. Members Form', 'advanced-members' ),
	// icon: 'people',
	icon: {
		src: <><svg height="100%" strokeMiterlimit="10" version="1.1" viewBox="0 0 113 113" width="100%" xmlSpace="preserve" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
			<g>
				<path d="M56.25 0C25.169 0 0 25.169 0 56.25C0 87.279 25.169 112.5 56.25 112.5C87.279 112.5 112.5 87.279 112.5 56.25C112.5 25.169 87.279 0 56.25 0ZM56.25 7.84375C82.936 7.84375 104.656 29.512 104.656 56.25C104.656 82.936 82.936 104.656 56.25 104.656C29.512 104.656 7.84375 82.936 7.84375 56.25C7.84375 29.512 29.512 7.84375 56.25 7.84375ZM40.3125 68.0625C39.3051 68.1214 38.3315 68.564 37.625 69.375C36.16 70.944 36.3155 73.462 37.9375 74.875L40.5312 71.9062L40.5625 71.9375L37.9375 74.875C37.9375 74.875 38 74.8855 38 74.9375C38.053 74.9905 38.145 75.02 38.25 75.125C38.459 75.282 38.779 75.561 39.25 75.875C40.087 76.451 41.2738 77.1837 42.8438 77.9688C45.9307 79.4338 50.4428 80.9375 56.0938 80.9375L56.0938 80.7812C56.1711 80.9362 56.25 81.0937 56.25 81.0938C61.849 81.0938 66.361 79.5795 69.5 78.0625C71.018 77.2775 72.2568 76.5655 73.0938 75.9375C73.5117 75.6235 73.8317 75.334 74.0938 75.125C74.1988 75.02 74.2908 74.927 74.3438 74.875C74.3438 74.823 74.4062 74.7813 74.4062 74.7812L74.5 74.875L74.5625 74.9375C76.1325 73.4725 76.288 70.997 74.875 69.375C73.4529 67.8005 71.1206 67.6561 69.5 68.9375C69.4662 68.8993 69.1875 68.5937 69.1875 68.5938L69.1875 68.375C69.1875 68.322 69.125 68.375 69.125 68.375C69.073 68.375 69.0208 68.3855 68.9688 68.4375C68.8118 68.4895 68.6057 68.666 68.3438 68.875C67.7687 69.242 66.8698 69.7365 65.7188 70.3125C63.4167 71.4105 60.061 72.5313 55.875 72.5312L55.875 73.0625C51.7549 73.0192 48.5074 71.9215 46.25 70.8438C45.099 70.2677 44.2 69.7628 43.625 69.3438C43.3738 69.1766 43.242 69.0694 43.125 68.9688L43.1562 69.0625C43.1381 69.0461 43.1122 69.0473 43.0938 69.0312L43.125 68.9688C43.0957 68.9436 43.0208 68.896 43 68.875C42.895 68.823 42.8958 68.75 42.8438 68.75L42.8125 68.6875L42.7188 68.8125C41.9842 68.3084 41.1566 68.0132 40.3125 68.0625Z" fill="#28303f" fillRule="nonzero" opacity="1" stroke="none"/>
			</g>
		</svg></>
	},
	attributes,
	edit,
	save: ({ attributes }) => {
	  const shortCode = toShortcode(attributes);
	  const blockProps = useBlockProps.save();

	  return (
	  	<div { ...blockProps }>
	  		{ shortCode }
	  	</div>
	  );
	}
} );
