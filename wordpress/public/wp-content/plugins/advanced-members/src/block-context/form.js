// import defaultContext from './default';
import { __ } from '@wordpress/i18n';
import { defaultsDeep } from 'lodash';

const defaultContext = {};
const formContext = defaultsDeep( {
	id: 'form',
	/*supports: {
		responsiveTabs: false,
		settingsPanel: {
			enabled: true,
			icon: 'backgrounds',
		},
		spacing: {
			enabled: true,
			padding: true,
			margin: true,
		},
		borders: {
			enabled: true,
			borderColors: [
				{
					state: '',
					tooltip: __( 'Border', 'advanced-members' ),
					alpha: true,
				},
			],
			borderTop: true,
			borderRight: true,
			borderBottom: true,
			borderLeft: true,
			borderRadius: true,
		},
	},*/
}, defaultContext );

export default formContext;
