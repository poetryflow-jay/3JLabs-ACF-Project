
    class Movie_Ticket_1xl24b24 {
        constructor() {
            this.Movie_Ticket_1xl24b24_qpkzke25();
        }
    
        Movie_Ticket_1xl24b24_qpkzke25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Text,Pmgc_Url,Pmgc_Note,Pmgc_Range,Pmgc_Border,Pmgc_Dimension,Pmgc_BoxShadow,Pmgc_Typography,Pmgc_Color,Pmgc_RadioAdvanced,Pmgc_Background,
       Pmgc_EditReusable,
       Pmgc_Global,
       Pmgc_HelperFunction,
       Pmgc_CssGenerator,
    } = wp.Pmgc_Components;
   
    const {
       Component,
       Fragment
    } = wp.element;
   
    const {
       InspectorControls,
       InnerBlocks,
       RichText,
    } = wp.blockEditor;
   
    registerBlockType('wdkit/wb-1xl24b24', {
        title: __('Movie Ticket'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-ticket-alt tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Content Showcase'),__('Movie Tickets'),__('Discounts'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_el20dn23Function = (unit, type) => {
                var g_slider_el20dn23_list = [];
                g_slider_el20dn23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_el20dn23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_el20dn23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_el20dn23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_el20dn23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_el20dn23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_el20dn23_list[unit][type];
            };
const slider_da86n223Function = (unit, type) => {
                var g_slider_da86n223_list = [];
                g_slider_da86n223_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_da86n223_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_da86n223_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_da86n223_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_da86n223_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_da86n223_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_da86n223_list[unit][type];
            };
const slider_3gpds123Function = (unit, type) => {
                var g_slider_3gpds123_list = [];
                g_slider_3gpds123_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_3gpds123_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_3gpds123_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_3gpds123_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_3gpds123_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_3gpds123_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_3gpds123_list[unit][type];
            };
const slider_wwmma923Function = (unit, type) => {
                var g_slider_wwmma923_list = [];
                g_slider_wwmma923_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_wwmma923_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_wwmma923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_wwmma923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_wwmma923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_wwmma923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_wwmma923_list[unit][type];
            };
const slider_9fdm6h23Function = (unit, type) => {
                var g_slider_9fdm6h23_list = [];
                g_slider_9fdm6h23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_9fdm6h23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_9fdm6h23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_9fdm6h23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_9fdm6h23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_9fdm6h23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_9fdm6h23_list[unit][type];
            };
const slider_qs9qfk23Function = (unit, type) => {
                var g_slider_qs9qfk23_list = [];
                g_slider_qs9qfk23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_qs9qfk23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_qs9qfk23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_qs9qfk23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_qs9qfk23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_qs9qfk23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_qs9qfk23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               media_5ew97023,
text_nj60eg23,
text_l04wm823,
text_3vks9823,
text_q1iku523,
text_18lpdk23,
url_tt1jdu23,
rawhtml_ev421p25,
slider_el20dn23,
slider_da86n223,
slider_3gpds123,
border_7qfj5523,
dimension_5lo1ji23,
boxshadow_kx0prm23,
dimension_c6ix7t23,
typography_jndkwi23,
color_x7r6v825,
dimension_08bop623,
typography_jhb47r23,
color_yy2n0a23,
choose_nb8bjc23,
dimension_1yoc6h23,
typography_7uujav23,
color_bclpzw23,
dimension_mlzfrb23,
typography_jlm39a23,
color_3lj9q123,
dimension_06cbx123,
typography_cta7no23,
color_ocrj2723,
color_q337ix23,
border_cjr70k24,
dimension_pqd9mk23,
dimension_ah2cyr23,
slider_wwmma923,
slider_9fdm6h23,
slider_qs9qfk23,
background_li9rdk23,
border_qmcgik23,
dimension_4ti40q23,
boxshadow_zszd4w23,

               block_id,
            } = attributes;

                var clientId = props.clientId.substr(0, 6)
                props.setAttributes({ block_id: clientId })

                
            useEffect(() => {
                const {
                    __experimentalGetPreviewDeviceType: getPreviewDeviceType,
                } = wp.data.select('core/edit-site') || wp.data.select('core/edit-post') || wp.data.select("core/edit-widgets");
                var selectDevices = '';
                if (getPreviewDeviceType() == 'Desktop') {
                    selectDevices = 'md'
                } else if (getPreviewDeviceType() == 'Tablet') {
                    selectDevices = 'sm'
                } else if (getPreviewDeviceType() == 'Mobile') {
                    selectDevices = 'xs'
                }
                setDevice(selectDevices)
            }, [])
            
            useEffect(() => {
                setTimeout(() => {
                    if(jQuery('.wdkit-block-' + block_id)?.length > 0){
                        main_function_1xl24b24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_1xl24b24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_5ew97023,
                dynamic: [true, 'media_5ew97023'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_5ew97023: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_nj60eg23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_nj60eg23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Description`),
                type: "text",
                value: text_l04wm823,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_l04wm823: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Current Price`),
                type: "text",
                value: text_3vks9823,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_3vks9823: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Old Price`),
                type: "text",
                value: text_q1iku523,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_q1iku523: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Title`),
                type: "text",
                value: text_18lpdk23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_18lpdk23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_tt1jdu23,
                dynamic: [true, 'url_tt1jdu23'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_tt1jdu23: value }),
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ev421p25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/movie-ticket-block/"
      target="_blank"
      rel="noopener noreferrer"
    >
      Read Docs
    </a>
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://roadmap.wdesignkit.com/boards/feature-requests"
      target="_blank"
      rel="noopener noreferrer"
    >
      Request Feature
    </a>
  <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px;  text-decoration: none; border-color: transparent"
      href="https://roadmap.wdesignkit.com/updates"
      target="_blank"
      rel="noopener noreferrer"
    >
      What's New
    </a>
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px;  text-decoration: none; border-color: transparent"
      href="https://roadmap.wdesignkit.com/roadmap"
      target="_blank"
      rel="noopener noreferrer"
    >
      Plugin Roadmap
    </a>
 
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://www.facebook.com/wdesignkit"
      target="_blank"
      rel="noopener noreferrer"
    >
      FB Community
    </a>
  <div class="need-help" style="display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 100%;">
    <a
      id="elementor-panel__editor__help__link"
      href="https://wordpress.org/support/plugin/wdesignkit/"
      target="_blank"
      style="
        align-items: center;
    border: 1px solid #1717cc;
    background: #1717cc;
    border-radius: 4px;
    -moz-column-gap: 5px;
    column-gap: 5px;
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    font-size: 12px;
    font-weight: 400;
    justify-content: center;
    letter-spacing: .3px;
    line-height: 22px;
    padding: 5px 12px;
    text-decoration: none; 
    width: 47.8%;"
      >Need Help <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><g clip-path="url(#a)"><path fill="#fff" fill-rule="evenodd" d="M7.72727 0H2.27273C1.01955 0 0 1.01955 0 2.27273v3.63636c0 1.09727.782273 2.01546 1.81818 2.22682v1.40954c0 .16773.09227.32182.24.40091.06727.03591.14091.05364.21455.05364.08818 0 .17591-.02591.25227-.07636l2.61273-1.74182h2.58954C8.98045 8.18182 10 7.16227 10 5.90909V2.27273C10 1.01955 8.98045 0 7.72727 0ZM4.74773 7.34909 2.72727 8.69591v-.96864c0-.25091-.20363-.45454-.45454-.45454-.75182 0-1.363639-.61182-1.363639-1.36364V2.27273c0-.75182.611819-1.363639 1.363639-1.363639h5.45454c.75182 0 1.36364.611819 1.36364 1.363639v3.63636c0 .75182-.61182 1.36364-1.36364 1.36364H5c-.03182-.00046-.07727.00272-.12909.01818-.05455.01591-.09591.03954-.12318.05818Zm2.97954-4.62182H2.27273c-.25091 0-.45455.20364-.45455.45455s.20364.45454.45455.45454h5.45454c.25091 0 .45455-.20363.45455-.45454 0-.25091-.20364-.45455-.45455-.45455ZM3.18182 4.54545h3.63636c.25091 0 .45455.20364.45455.45455s-.20364.45455-.45455.45455H3.18182c-.25091 0-.45455-.20364-.45455-.45455s.20364-.45455.45455-.45455Z" clip-rule="evenodd"></path></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h10v10H0z"></path></clipPath></defs></svg>
    </a>
  
</div>
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_ev421p25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Poster"), initialOpen: true },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_el20dn23,
            
            min: slider_el20dn23 && slider_el20dn23.unit ? slider_el20dn23Function(slider_el20dn23.unit, 'min') : 0,
            max: slider_el20dn23 && slider_el20dn23.unit ? slider_el20dn23Function(slider_el20dn23.unit, 'max') : 100,
            step: slider_el20dn23 && slider_el20dn23.unit ? slider_el20dn23Function(slider_el20dn23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_el20dn23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_da86n223,
            
            min: slider_da86n223 && slider_da86n223.unit ? slider_da86n223Function(slider_da86n223.unit, 'min') : 0,
            max: slider_da86n223 && slider_da86n223.unit ? slider_da86n223Function(slider_da86n223.unit, 'max') : 100,
            step: slider_da86n223 && slider_da86n223.unit ? slider_da86n223Function(slider_da86n223.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_da86n223: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_3gpds123,
            
            min: slider_3gpds123 && slider_3gpds123.unit ? slider_3gpds123Function(slider_3gpds123.unit, 'min') : 0,
            max: slider_3gpds123 && slider_3gpds123.unit ? slider_3gpds123Function(slider_3gpds123.unit, 'max') : 100,
            step: slider_3gpds123 && slider_3gpds123.unit ? slider_3gpds123Function(slider_3gpds123.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_3gpds123: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_7qfj5523,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_7qfj5523: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_5lo1ji23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5lo1ji23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_kx0prm23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_kx0prm23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Title"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_c6ix7t23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_c6ix7t23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_jndkwi23,
            onChange: (value) => setAttributes({ typography_jndkwi23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_x7r6v825,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_x7r6v825: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_08bop623,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_08bop623: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_jhb47r23,
            onChange: (value) => setAttributes({ typography_jhb47r23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_yy2n0a23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yy2n0a23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Current Price"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Price Style`),
            separator:'default',
            
            options : [{ label: __('Style-1'), value: 'row', title: __('Style-1'), icon: 'fas fa-align-right', svg: '' }, 
{ label: __('Style-2'), value: 'column', title: __('Style-2'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_nb8bjc23,
            
            
            onChange: (value) => setAttributes({ choose_nb8bjc23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_1yoc6h23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_1yoc6h23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_7uujav23,
            onChange: (value) => setAttributes({ typography_7uujav23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_bclpzw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_bclpzw23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Old Price"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_mlzfrb23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_mlzfrb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_jlm39a23,
            onChange: (value) => setAttributes({ typography_jlm39a23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_3lj9q123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3lj9q123: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_06cbx123,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_06cbx123: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_cta7no23,
            onChange: (value) => setAttributes({ typography_cta7no23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ocrj2723,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ocrj2723: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_q337ix23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_q337ix23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_cjr70k24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_cjr70k24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_pqd9mk23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_pqd9mk23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ah2cyr23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ah2cyr23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_wwmma923,
            
            min: slider_wwmma923 && slider_wwmma923.unit ? slider_wwmma923Function(slider_wwmma923.unit, 'min') : 0,
            max: slider_wwmma923 && slider_wwmma923.unit ? slider_wwmma923Function(slider_wwmma923.unit, 'max') : 100,
            step: slider_wwmma923 && slider_wwmma923.unit ? slider_wwmma923Function(slider_wwmma923.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_wwmma923: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_9fdm6h23,
            
            min: slider_9fdm6h23 && slider_9fdm6h23.unit ? slider_9fdm6h23Function(slider_9fdm6h23.unit, 'min') : 0,
            max: slider_9fdm6h23 && slider_9fdm6h23.unit ? slider_9fdm6h23Function(slider_9fdm6h23.unit, 'max') : 100,
            step: slider_9fdm6h23 && slider_9fdm6h23.unit ? slider_9fdm6h23Function(slider_9fdm6h23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_9fdm6h23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_qs9qfk23,
            
            min: slider_qs9qfk23 && slider_qs9qfk23.unit ? slider_qs9qfk23Function(slider_qs9qfk23.unit, 'min') : 0,
            max: slider_qs9qfk23 && slider_qs9qfk23.unit ? slider_qs9qfk23Function(slider_qs9qfk23.unit, 'max') : 100,
            step: slider_qs9qfk23 && slider_qs9qfk23.unit ? slider_qs9qfk23Function(slider_qs9qfk23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_qs9qfk23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_li9rdk23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_li9rdk23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_qmcgik23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_qmcgik23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_4ti40q23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_4ti40q23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_zszd4w23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_zszd4w23: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-1xl24b24", block_id, false, props.clientId);
                }
            }

            
let g_media_5ew97023 = media_5ew97023 && media_5ew97023.url && media_5ew97023.url != undefined  ? media_5ew97023.url : "";
let g_text_nj60eg23 = text_nj60eg23 && text_nj60eg23 != undefined  ? text_nj60eg23 : "";
let g_text_l04wm823 = text_l04wm823 && text_l04wm823 != undefined  ? text_l04wm823 : "";
let g_text_3vks9823 = text_3vks9823 && text_3vks9823 != undefined  ? text_3vks9823 : "";
let g_text_q1iku523 = text_q1iku523 && text_q1iku523 != undefined  ? text_q1iku523 : "";
let g_text_18lpdk23 = text_18lpdk23 && text_18lpdk23 != undefined  ? text_18lpdk23 : "";
let g_url_tt1jdu23_url = url_tt1jdu23?.url && url_tt1jdu23?.url != undefined ? url_tt1jdu23.url : "";
let g_url_tt1jdu23_target = url_tt1jdu23?.target && url_tt1jdu23?.target != undefined ? url_tt1jdu23.target : "";
let g_url_tt1jdu23_nofollow = url_tt1jdu23?.nofollow && url_tt1jdu23?.nofollow != undefined ? url_tt1jdu23.nofollow : "";
let g_url_tt1jdu23_ctmArt = url_tt1jdu23?.attr != undefined ? url_tt1jdu23.attr : "";
                    let g_url_tt1jdu23_attr = ''

                    if (g_url_tt1jdu23_ctmArt) {
                        let main_array = g_url_tt1jdu23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_tt1jdu23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_ev421p25 = rawhtml_ev421p25 && rawhtml_ev421p25 != undefined  ? rawhtml_ev421p25 : "";
let g_border_7qfj5523 = border_7qfj5523 && border_7qfj5523 != undefined  ? border_7qfj5523 : "";
let g_dimension_5lo1ji23 = dimension_5lo1ji23 && dimension_5lo1ji23 != undefined  ? dimension_5lo1ji23 : "";
let g_boxshadow_kx0prm23 = boxshadow_kx0prm23 && boxshadow_kx0prm23 != undefined  ? boxshadow_kx0prm23 : "";
let g_dimension_c6ix7t23 = dimension_c6ix7t23 && dimension_c6ix7t23 != undefined  ? dimension_c6ix7t23 : "";
let g_typography_jndkwi23 = typography_jndkwi23 && typography_jndkwi23 != undefined  ? typography_jndkwi23 : "";
let g_color_x7r6v825 = color_x7r6v825 && color_x7r6v825 != undefined  ? color_x7r6v825 : "";
let g_dimension_08bop623 = dimension_08bop623 && dimension_08bop623 != undefined  ? dimension_08bop623 : "";
let g_typography_jhb47r23 = typography_jhb47r23 && typography_jhb47r23 != undefined  ? typography_jhb47r23 : "";
let g_color_yy2n0a23 = color_yy2n0a23 && color_yy2n0a23 != undefined  ? color_yy2n0a23 : "";
let g_choose_nb8bjc23 = choose_nb8bjc23 && choose_nb8bjc23 != undefined  ? choose_nb8bjc23 : "";
let g_dimension_1yoc6h23 = dimension_1yoc6h23 && dimension_1yoc6h23 != undefined  ? dimension_1yoc6h23 : "";
let g_typography_7uujav23 = typography_7uujav23 && typography_7uujav23 != undefined  ? typography_7uujav23 : "";
let g_color_bclpzw23 = color_bclpzw23 && color_bclpzw23 != undefined  ? color_bclpzw23 : "";
let g_dimension_mlzfrb23 = dimension_mlzfrb23 && dimension_mlzfrb23 != undefined  ? dimension_mlzfrb23 : "";
let g_typography_jlm39a23 = typography_jlm39a23 && typography_jlm39a23 != undefined  ? typography_jlm39a23 : "";
let g_color_3lj9q123 = color_3lj9q123 && color_3lj9q123 != undefined  ? color_3lj9q123 : "";
let g_dimension_06cbx123 = dimension_06cbx123 && dimension_06cbx123 != undefined  ? dimension_06cbx123 : "";
let g_typography_cta7no23 = typography_cta7no23 && typography_cta7no23 != undefined  ? typography_cta7no23 : "";
let g_color_ocrj2723 = color_ocrj2723 && color_ocrj2723 != undefined  ? color_ocrj2723 : "";
let g_color_q337ix23 = color_q337ix23 && color_q337ix23 != undefined  ? color_q337ix23 : "";
let g_border_cjr70k24 = border_cjr70k24 && border_cjr70k24 != undefined  ? border_cjr70k24 : "";
let g_dimension_pqd9mk23 = dimension_pqd9mk23 && dimension_pqd9mk23 != undefined  ? dimension_pqd9mk23 : "";
let g_dimension_ah2cyr23 = dimension_ah2cyr23 && dimension_ah2cyr23 != undefined  ? dimension_ah2cyr23 : "";
let g_background_li9rdk23 = background_li9rdk23 && background_li9rdk23 != undefined  ? background_li9rdk23 : "";
let g_border_qmcgik23 = border_qmcgik23 && border_qmcgik23 != undefined  ? border_qmcgik23 : "";
let g_dimension_4ti40q23 = dimension_4ti40q23 && dimension_4ti40q23 != undefined  ? dimension_4ti40q23 : "";
let g_boxshadow_zszd4w23 = boxshadow_zszd4w23 && boxshadow_zszd4w23 != undefined  ? boxshadow_zszd4w23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_1xl24b24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-movie-ticket-wrapper">
    <div class="movie-ticket-container">
        <div class="movie-ticket-poster">
                <img class="movie-poster-image" src="${g_media_5ew97023}">
        </div> 
        <div class="ticket-container">
            <div class="ticket-content">
               <span class="ticket-title">${g_text_nj60eg23}</span>
               <span class="ticket-slogan">${g_text_l04wm823}</span>
               <div class="ticket-inner">
                    <span class="ticket-current-price">${g_text_3vks9823}</span>
                    <span class="ticket-old-price">${g_text_q1iku523}</span>
               </div>
               <a href="${g_url_tt1jdu23_url}" class="ticket-btn-link" target="${g_url_tt1jdu23_target}" rel="${g_url_tt1jdu23_nofollow} noopener">
                  <span class="ticket-btn-text">${g_text_18lpdk23}</span>
               </a>
            </div>
        </div>  
    </div> 
</div> `
                        }
                    })
                )
            );
        },
   
       save: (props) => {

        const {
            isSelected,
            attributes,
            setAttributes,
        } = props;

        const {
            media_5ew97023,
text_nj60eg23,
text_l04wm823,
text_3vks9823,
text_q1iku523,
text_18lpdk23,
url_tt1jdu23,
rawhtml_ev421p25,
slider_el20dn23,
slider_da86n223,
slider_3gpds123,
border_7qfj5523,
dimension_5lo1ji23,
boxshadow_kx0prm23,
dimension_c6ix7t23,
typography_jndkwi23,
color_x7r6v825,
dimension_08bop623,
typography_jhb47r23,
color_yy2n0a23,
choose_nb8bjc23,
dimension_1yoc6h23,
typography_7uujav23,
color_bclpzw23,
dimension_mlzfrb23,
typography_jlm39a23,
color_3lj9q123,
dimension_06cbx123,
typography_cta7no23,
color_ocrj2723,
color_q337ix23,
border_cjr70k24,
dimension_pqd9mk23,
dimension_ah2cyr23,
slider_wwmma923,
slider_9fdm6h23,
slider_qs9qfk23,
background_li9rdk23,
border_qmcgik23,
dimension_4ti40q23,
boxshadow_zszd4w23,

            block_id,
        } = attributes;

        

        

        

        
let g_media_5ew97023 = media_5ew97023 && media_5ew97023.url && media_5ew97023.url != undefined  ? media_5ew97023.url : "";
let g_text_nj60eg23 = text_nj60eg23 && text_nj60eg23 != undefined  ? text_nj60eg23 : "";
let g_text_l04wm823 = text_l04wm823 && text_l04wm823 != undefined  ? text_l04wm823 : "";
let g_text_3vks9823 = text_3vks9823 && text_3vks9823 != undefined  ? text_3vks9823 : "";
let g_text_q1iku523 = text_q1iku523 && text_q1iku523 != undefined  ? text_q1iku523 : "";
let g_text_18lpdk23 = text_18lpdk23 && text_18lpdk23 != undefined  ? text_18lpdk23 : "";
let g_url_tt1jdu23_url = url_tt1jdu23?.url && url_tt1jdu23?.url != undefined ? url_tt1jdu23.url : "";
let g_url_tt1jdu23_target = url_tt1jdu23?.target && url_tt1jdu23?.target != undefined ? url_tt1jdu23.target : "";
let g_url_tt1jdu23_nofollow = url_tt1jdu23?.nofollow && url_tt1jdu23?.nofollow != undefined ? url_tt1jdu23.nofollow : "";
let g_url_tt1jdu23_ctmArt = url_tt1jdu23?.attr != undefined ? url_tt1jdu23.attr : "";
                    let g_url_tt1jdu23_attr = ''

                    if (g_url_tt1jdu23_ctmArt) {
                        let main_array = g_url_tt1jdu23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_tt1jdu23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_ev421p25 = rawhtml_ev421p25 && rawhtml_ev421p25 != undefined  ? rawhtml_ev421p25 : "";
let g_border_7qfj5523 = border_7qfj5523 && border_7qfj5523 != undefined  ? border_7qfj5523 : "";
let g_dimension_5lo1ji23 = dimension_5lo1ji23 && dimension_5lo1ji23 != undefined  ? dimension_5lo1ji23 : "";
let g_boxshadow_kx0prm23 = boxshadow_kx0prm23 && boxshadow_kx0prm23 != undefined  ? boxshadow_kx0prm23 : "";
let g_dimension_c6ix7t23 = dimension_c6ix7t23 && dimension_c6ix7t23 != undefined  ? dimension_c6ix7t23 : "";
let g_typography_jndkwi23 = typography_jndkwi23 && typography_jndkwi23 != undefined  ? typography_jndkwi23 : "";
let g_color_x7r6v825 = color_x7r6v825 && color_x7r6v825 != undefined  ? color_x7r6v825 : "";
let g_dimension_08bop623 = dimension_08bop623 && dimension_08bop623 != undefined  ? dimension_08bop623 : "";
let g_typography_jhb47r23 = typography_jhb47r23 && typography_jhb47r23 != undefined  ? typography_jhb47r23 : "";
let g_color_yy2n0a23 = color_yy2n0a23 && color_yy2n0a23 != undefined  ? color_yy2n0a23 : "";
let g_choose_nb8bjc23 = choose_nb8bjc23 && choose_nb8bjc23 != undefined  ? choose_nb8bjc23 : "";
let g_dimension_1yoc6h23 = dimension_1yoc6h23 && dimension_1yoc6h23 != undefined  ? dimension_1yoc6h23 : "";
let g_typography_7uujav23 = typography_7uujav23 && typography_7uujav23 != undefined  ? typography_7uujav23 : "";
let g_color_bclpzw23 = color_bclpzw23 && color_bclpzw23 != undefined  ? color_bclpzw23 : "";
let g_dimension_mlzfrb23 = dimension_mlzfrb23 && dimension_mlzfrb23 != undefined  ? dimension_mlzfrb23 : "";
let g_typography_jlm39a23 = typography_jlm39a23 && typography_jlm39a23 != undefined  ? typography_jlm39a23 : "";
let g_color_3lj9q123 = color_3lj9q123 && color_3lj9q123 != undefined  ? color_3lj9q123 : "";
let g_dimension_06cbx123 = dimension_06cbx123 && dimension_06cbx123 != undefined  ? dimension_06cbx123 : "";
let g_typography_cta7no23 = typography_cta7no23 && typography_cta7no23 != undefined  ? typography_cta7no23 : "";
let g_color_ocrj2723 = color_ocrj2723 && color_ocrj2723 != undefined  ? color_ocrj2723 : "";
let g_color_q337ix23 = color_q337ix23 && color_q337ix23 != undefined  ? color_q337ix23 : "";
let g_border_cjr70k24 = border_cjr70k24 && border_cjr70k24 != undefined  ? border_cjr70k24 : "";
let g_dimension_pqd9mk23 = dimension_pqd9mk23 && dimension_pqd9mk23 != undefined  ? dimension_pqd9mk23 : "";
let g_dimension_ah2cyr23 = dimension_ah2cyr23 && dimension_ah2cyr23 != undefined  ? dimension_ah2cyr23 : "";
let g_background_li9rdk23 = background_li9rdk23 && background_li9rdk23 != undefined  ? background_li9rdk23 : "";
let g_border_qmcgik23 = border_qmcgik23 && border_qmcgik23 != undefined  ? border_qmcgik23 : "";
let g_dimension_4ti40q23 = dimension_4ti40q23 && dimension_4ti40q23 != undefined  ? dimension_4ti40q23 : "";
let g_boxshadow_zszd4w23 = boxshadow_zszd4w23 && boxshadow_zszd4w23 != undefined  ? boxshadow_zszd4w23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-1xl24b24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_1xl24b24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-movie-ticket-wrapper">
    <div class="movie-ticket-container">
        <div class="movie-ticket-poster">
                <img class="movie-poster-image" src="${g_media_5ew97023}">
        </div> 
        <div class="ticket-container">
            <div class="ticket-content">
               <span class="ticket-title">${g_text_nj60eg23}</span>
               <span class="ticket-slogan">${g_text_l04wm823}</span>
               <div class="ticket-inner">
                    <span class="ticket-current-price">${g_text_3vks9823}</span>
                    <span class="ticket-old-price">${g_text_q1iku523}</span>
               </div>
               <a href="${g_url_tt1jdu23_url}" class="ticket-btn-link" target="${g_url_tt1jdu23_target}" rel="${g_url_tt1jdu23_nofollow} noopener">
                  <span class="ticket-btn-text">${g_text_18lpdk23}</span>
               </a>
            </div>
        </div>  
    </div> 
</div> `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Movie_Ticket_1xl24b24();