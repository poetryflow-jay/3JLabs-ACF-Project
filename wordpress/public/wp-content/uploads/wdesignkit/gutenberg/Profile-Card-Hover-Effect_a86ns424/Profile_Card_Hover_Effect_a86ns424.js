
    class Profile_Card_Hover_Effect_a86ns424 {
        constructor() {
            this.Profile_Card_Hover_Effect_a86ns424_3qp5ov25();
        }
    
        Profile_Card_Hover_Effect_a86ns424_3qp5ov25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Media,Pmgc_IconList,Pmgc_Url,Pmgc_Repeater,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_Range,Pmgc_Tabs,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-a86ns424', {
        title: __('Profile Card Hover Effect'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-id-badge tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('User Interaction'),__('Profile Cards'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_ssl32e24Function = (unit, type) => {
                var g_slider_ssl32e24_list = [];
                g_slider_ssl32e24_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_ssl32e24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ssl32e24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ssl32e24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ssl32e24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ssl32e24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ssl32e24_list[unit][type];
            };
const slider_5mvodt24Function = (unit, type) => {
                var g_slider_5mvodt24_list = [];
                g_slider_5mvodt24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_5mvodt24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_5mvodt24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5mvodt24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5mvodt24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5mvodt24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5mvodt24_list[unit][type];
            };
const slider_g1famz24Function = (unit, type) => {
                var g_slider_g1famz24_list = [];
                g_slider_g1famz24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_g1famz24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_g1famz24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_g1famz24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_g1famz24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_g1famz24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_g1famz24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_itcils23,
text_gh3meu23,
media_dsfmhh23,
repeater_wwg5yt23,
rawhtml_an6tvo25,
dimension_6n2wp424,
typography_uewobu23,
color_1rzvkb23,
dimension_fyp4yb23,
typography_bt4s6s23,
color_p77afe23,
background_2lvv3t23,
border_7ejwlv23,
dimension_sj8v2423,
slider_ssl32e24,
color_h9jgtx23,
color_mvck9q23,
normalhover_2hl6uk23,
dimension_xm2z6j23,
typography_tu3dr323,
color_d1idf623,
color_5yh2z523,
color_mxxin023,
dimension_z26tl023,
boxshadow_44qzzr23,
dimension_arsr5s23,
slider_5mvodt24,
slider_g1famz24,
color_rh9fgn23,
color_qfaxuq23,
normalhover_097dd023,
border_we7ov125,
border_0578wv25,
dimension_0bbgj425,
normalhover_p0q0zw25,
border_ajb40e25,
border_x678uj25,
dimension_v9zx8u25,
boxshadow_scnpbl25,
boxshadow_ozmpjn25,
normalhover_roc3s925,

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
                        main_function_a86ns424(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_a86ns424 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_itcils23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_itcils23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Sub Title`),
                type: "text",
                value: text_gh3meu23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_gh3meu23: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_dsfmhh23,
                dynamic: [true, 'media_dsfmhh23'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_dsfmhh23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icons"), initialOpen: false },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(` `),
            value: repeater_wwg5yt23,
            attributeName: 'repeater_wwg5yt23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_wwg5yt23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_br23qo23,
            separator:'default',
            onChange: v => { value.iconscontrol_br23qo23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: value.url_ualqto23,
                dynamic: [true, 'url_ualqto23'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.url_ualqto23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tooltip Text`),
                type: "text",
                value: value.text_6a7np223,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_6a7np223 = v; onChange(value); },
            }),

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_an6tvo25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/profile-card-hover-effect-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_an6tvo25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_6n2wp424,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_6n2wp424: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_uewobu23,
            onChange: (value) => setAttributes({ typography_uewobu23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1rzvkb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1rzvkb23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Sub Title"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_fyp4yb23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_fyp4yb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_bt4s6s23,
            onChange: (value) => setAttributes({ typography_bt4s6s23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_p77afe23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_p77afe23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_2lvv3t23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_2lvv3t23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_7ejwlv23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_7ejwlv23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_sj8v2423,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_sj8v2423: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), React.createElement(PanelBody, { title: __("Icons"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_ssl32e24,
            
            min: slider_ssl32e24 && slider_ssl32e24.unit ? slider_ssl32e24Function(slider_ssl32e24.unit, 'min') : 0,
            max: slider_ssl32e24 && slider_ssl32e24.unit ? slider_ssl32e24Function(slider_ssl32e24.unit, 'max') : 100,
            step: slider_ssl32e24 && slider_ssl32e24.unit ? slider_ssl32e24Function(slider_ssl32e24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ssl32e24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_h9jgtx23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_h9jgtx23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_mvck9q23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_mvck9q23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Tooltip"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_xm2z6j23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_xm2z6j23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_tu3dr323,
            onChange: (value) => setAttributes({ typography_tu3dr323: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Arrow Color`),
            value: color_d1idf623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_d1idf623: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_5yh2z523,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_5yh2z523: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_mxxin023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_mxxin023: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_z26tl023,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_z26tl023: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_44qzzr23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_44qzzr23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_arsr5s23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_arsr5s23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_5mvodt24,
            
            min: slider_5mvodt24 && slider_5mvodt24.unit ? slider_5mvodt24Function(slider_5mvodt24.unit, 'min') : 0,
            max: slider_5mvodt24 && slider_5mvodt24.unit ? slider_5mvodt24Function(slider_5mvodt24.unit, 'max') : 100,
            step: slider_5mvodt24 && slider_5mvodt24.unit ? slider_5mvodt24Function(slider_5mvodt24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5mvodt24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_g1famz24,
            
            min: slider_g1famz24 && slider_g1famz24.unit ? slider_g1famz24Function(slider_g1famz24.unit, 'min') : 0,
            max: slider_g1famz24 && slider_g1famz24.unit ? slider_g1famz24Function(slider_g1famz24.unit, 'max') : 100,
            step: slider_g1famz24 && slider_g1famz24.unit ? slider_g1famz24Function(slider_g1famz24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_g1famz24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_rh9fgn23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rh9fgn23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_qfaxuq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qfaxuq23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Inner Border"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_we7ov125,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_we7ov125: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_0bbgj425,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_0bbgj425: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_0578wv25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_0578wv25: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Outer Border"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_x678uj25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_x678uj25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_v9zx8u25,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_v9zx8u25: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_scnpbl25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_scnpbl25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_ajb40e25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_ajb40e25: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_ozmpjn25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_ozmpjn25: value }),
            }), 
), 
), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-a86ns424", block_id, false, props.clientId);
                }
            }

            
let g_text_itcils23 = text_itcils23 && text_itcils23 != undefined  ? text_itcils23 : "";
let g_text_gh3meu23 = text_gh3meu23 && text_gh3meu23 != undefined  ? text_gh3meu23 : "";
let g_media_dsfmhh23 = media_dsfmhh23 && media_dsfmhh23.url && media_dsfmhh23.url != undefined  ? media_dsfmhh23.url : "";
let g_rawhtml_an6tvo25 = rawhtml_an6tvo25 && rawhtml_an6tvo25 != undefined  ? rawhtml_an6tvo25 : "";
let g_dimension_6n2wp424 = dimension_6n2wp424 && dimension_6n2wp424 != undefined  ? dimension_6n2wp424 : "";
let g_typography_uewobu23 = typography_uewobu23 && typography_uewobu23 != undefined  ? typography_uewobu23 : "";
let g_color_1rzvkb23 = color_1rzvkb23 && color_1rzvkb23 != undefined  ? color_1rzvkb23 : "";
let g_dimension_fyp4yb23 = dimension_fyp4yb23 && dimension_fyp4yb23 != undefined  ? dimension_fyp4yb23 : "";
let g_typography_bt4s6s23 = typography_bt4s6s23 && typography_bt4s6s23 != undefined  ? typography_bt4s6s23 : "";
let g_color_p77afe23 = color_p77afe23 && color_p77afe23 != undefined  ? color_p77afe23 : "";
let g_background_2lvv3t23 = background_2lvv3t23 && background_2lvv3t23 != undefined  ? background_2lvv3t23 : "";
let g_border_7ejwlv23 = border_7ejwlv23 && border_7ejwlv23 != undefined  ? border_7ejwlv23 : "";
let g_dimension_sj8v2423 = dimension_sj8v2423 && dimension_sj8v2423 != undefined  ? dimension_sj8v2423 : "";
let g_color_h9jgtx23 = color_h9jgtx23 && color_h9jgtx23 != undefined  ? color_h9jgtx23 : "";
let g_color_mvck9q23 = color_mvck9q23 && color_mvck9q23 != undefined  ? color_mvck9q23 : "";
let g_dimension_xm2z6j23 = dimension_xm2z6j23 && dimension_xm2z6j23 != undefined  ? dimension_xm2z6j23 : "";
let g_typography_tu3dr323 = typography_tu3dr323 && typography_tu3dr323 != undefined  ? typography_tu3dr323 : "";
let g_color_d1idf623 = color_d1idf623 && color_d1idf623 != undefined  ? color_d1idf623 : "";
let g_color_5yh2z523 = color_5yh2z523 && color_5yh2z523 != undefined  ? color_5yh2z523 : "";
let g_color_mxxin023 = color_mxxin023 && color_mxxin023 != undefined  ? color_mxxin023 : "";
let g_dimension_z26tl023 = dimension_z26tl023 && dimension_z26tl023 != undefined  ? dimension_z26tl023 : "";
let g_boxshadow_44qzzr23 = boxshadow_44qzzr23 && boxshadow_44qzzr23 != undefined  ? boxshadow_44qzzr23 : "";
let g_dimension_arsr5s23 = dimension_arsr5s23 && dimension_arsr5s23 != undefined  ? dimension_arsr5s23 : "";
let g_color_rh9fgn23 = color_rh9fgn23 && color_rh9fgn23 != undefined  ? color_rh9fgn23 : "";
let g_color_qfaxuq23 = color_qfaxuq23 && color_qfaxuq23 != undefined  ? color_qfaxuq23 : "";
let g_border_we7ov125 = border_we7ov125 && border_we7ov125 != undefined  ? border_we7ov125 : "";
let g_border_0578wv25 = border_0578wv25 && border_0578wv25 != undefined  ? border_0578wv25 : "";
let g_dimension_0bbgj425 = dimension_0bbgj425 && dimension_0bbgj425 != undefined  ? dimension_0bbgj425 : "";
let g_border_ajb40e25 = border_ajb40e25 && border_ajb40e25 != undefined  ? border_ajb40e25 : "";
let g_border_x678uj25 = border_x678uj25 && border_x678uj25 != undefined  ? border_x678uj25 : "";
let g_dimension_v9zx8u25 = dimension_v9zx8u25 && dimension_v9zx8u25 != undefined  ? dimension_v9zx8u25 : "";
let g_boxshadow_scnpbl25 = boxshadow_scnpbl25 && boxshadow_scnpbl25 != undefined  ? boxshadow_scnpbl25 : "";
let g_boxshadow_ozmpjn25 = boxshadow_ozmpjn25 && boxshadow_ozmpjn25 != undefined  ? boxshadow_ozmpjn25 : "";
            
let repeater_wwg5yt23_1p25 = "";
                            
repeater_wwg5yt23  && repeater_wwg5yt23.map((r_item, index) => {
                                
let grnp_iconscontrol_br23qo23 = r_item?.iconscontrol_br23qo23 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_br23qo23+'"></i></span>' : '';

let grnp_url_ualqto23_url = r_item?.url_ualqto23?.url && r_item?.url_ualqto23?.url != undefined ?  r_item?.url_ualqto23.url : "";
let grnp_url_ualqto23_target = r_item?.url_ualqto23?.target && r_item?.url_ualqto23?.target != undefined ?  r_item?.url_ualqto23.target : "";
let grnp_url_ualqto23_nofollow = r_item?.url_ualqto23?.nofollow && r_item?.url_ualqto23?.nofollow != undefined ?  r_item?.url_ualqto23.nofollow : "";
let grnp_url_ualqto23_ctmArt = r_item?.url_ualqto23?.attr && r_item?.url_ualqto23?.attr != undefined ?  r_item?.url_ualqto23.attr : "";
                    let grnp_url_ualqto23_attr = ''

                    if (grnp_url_ualqto23_ctmArt) {
                        let main_array = grnp_url_ualqto23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_ualqto23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_text_6a7np223 = r_item.text_6a7np223  ? r_item.text_6a7np223 : "";
                                repeater_wwg5yt23_1p25 += `<div class="tp-repeater-item-${r_item._key}" data-repeater_wwg5yt23="{repeater_wwg5yt23}">
                      <a href="${grnp_url_ualqto23_url}" class="profile-icons" target="${grnp_url_ualqto23_target}" rel="${grnp_url_ualqto23_nofollow} noopener">
                          ${grnp_iconscontrol_br23qo23 }
                          <span class="tooltiptext" data-tooltip="${grnp_text_6a7np223}">${grnp_text_6a7np223}</span>
                      </a>
                  </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_a86ns424 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-profile-card-hover-effect">
    <div class="wkit-profile-card-inner">
        <div class="wkit-profile-card-image" style="background-image:url(${g_media_dsfmhh23})">
          <div class="wkit-profile-card-border">
            <h2 class="profile-card-heading">${g_text_itcils23}</h2>
            <h4 class="profile-card-heading-sub-title">${g_text_gh3meu23}</h4>
              <div class="profile-card-icons">
                  ${repeater_wwg5yt23_1p25}
             </div>
         </div>
      </div>
    </div>
</div>
`
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
            text_itcils23,
text_gh3meu23,
media_dsfmhh23,
repeater_wwg5yt23,
rawhtml_an6tvo25,
dimension_6n2wp424,
typography_uewobu23,
color_1rzvkb23,
dimension_fyp4yb23,
typography_bt4s6s23,
color_p77afe23,
background_2lvv3t23,
border_7ejwlv23,
dimension_sj8v2423,
slider_ssl32e24,
color_h9jgtx23,
color_mvck9q23,
normalhover_2hl6uk23,
dimension_xm2z6j23,
typography_tu3dr323,
color_d1idf623,
color_5yh2z523,
color_mxxin023,
dimension_z26tl023,
boxshadow_44qzzr23,
dimension_arsr5s23,
slider_5mvodt24,
slider_g1famz24,
color_rh9fgn23,
color_qfaxuq23,
normalhover_097dd023,
border_we7ov125,
border_0578wv25,
dimension_0bbgj425,
normalhover_p0q0zw25,
border_ajb40e25,
border_x678uj25,
dimension_v9zx8u25,
boxshadow_scnpbl25,
boxshadow_ozmpjn25,
normalhover_roc3s925,

            block_id,
        } = attributes;

        

        

        

        
let g_text_itcils23 = text_itcils23 && text_itcils23 != undefined  ? text_itcils23 : "";
let g_text_gh3meu23 = text_gh3meu23 && text_gh3meu23 != undefined  ? text_gh3meu23 : "";
let g_media_dsfmhh23 = media_dsfmhh23 && media_dsfmhh23.url && media_dsfmhh23.url != undefined  ? media_dsfmhh23.url : "";
let g_rawhtml_an6tvo25 = rawhtml_an6tvo25 && rawhtml_an6tvo25 != undefined  ? rawhtml_an6tvo25 : "";
let g_dimension_6n2wp424 = dimension_6n2wp424 && dimension_6n2wp424 != undefined  ? dimension_6n2wp424 : "";
let g_typography_uewobu23 = typography_uewobu23 && typography_uewobu23 != undefined  ? typography_uewobu23 : "";
let g_color_1rzvkb23 = color_1rzvkb23 && color_1rzvkb23 != undefined  ? color_1rzvkb23 : "";
let g_dimension_fyp4yb23 = dimension_fyp4yb23 && dimension_fyp4yb23 != undefined  ? dimension_fyp4yb23 : "";
let g_typography_bt4s6s23 = typography_bt4s6s23 && typography_bt4s6s23 != undefined  ? typography_bt4s6s23 : "";
let g_color_p77afe23 = color_p77afe23 && color_p77afe23 != undefined  ? color_p77afe23 : "";
let g_background_2lvv3t23 = background_2lvv3t23 && background_2lvv3t23 != undefined  ? background_2lvv3t23 : "";
let g_border_7ejwlv23 = border_7ejwlv23 && border_7ejwlv23 != undefined  ? border_7ejwlv23 : "";
let g_dimension_sj8v2423 = dimension_sj8v2423 && dimension_sj8v2423 != undefined  ? dimension_sj8v2423 : "";
let g_color_h9jgtx23 = color_h9jgtx23 && color_h9jgtx23 != undefined  ? color_h9jgtx23 : "";
let g_color_mvck9q23 = color_mvck9q23 && color_mvck9q23 != undefined  ? color_mvck9q23 : "";
let g_dimension_xm2z6j23 = dimension_xm2z6j23 && dimension_xm2z6j23 != undefined  ? dimension_xm2z6j23 : "";
let g_typography_tu3dr323 = typography_tu3dr323 && typography_tu3dr323 != undefined  ? typography_tu3dr323 : "";
let g_color_d1idf623 = color_d1idf623 && color_d1idf623 != undefined  ? color_d1idf623 : "";
let g_color_5yh2z523 = color_5yh2z523 && color_5yh2z523 != undefined  ? color_5yh2z523 : "";
let g_color_mxxin023 = color_mxxin023 && color_mxxin023 != undefined  ? color_mxxin023 : "";
let g_dimension_z26tl023 = dimension_z26tl023 && dimension_z26tl023 != undefined  ? dimension_z26tl023 : "";
let g_boxshadow_44qzzr23 = boxshadow_44qzzr23 && boxshadow_44qzzr23 != undefined  ? boxshadow_44qzzr23 : "";
let g_dimension_arsr5s23 = dimension_arsr5s23 && dimension_arsr5s23 != undefined  ? dimension_arsr5s23 : "";
let g_color_rh9fgn23 = color_rh9fgn23 && color_rh9fgn23 != undefined  ? color_rh9fgn23 : "";
let g_color_qfaxuq23 = color_qfaxuq23 && color_qfaxuq23 != undefined  ? color_qfaxuq23 : "";
let g_border_we7ov125 = border_we7ov125 && border_we7ov125 != undefined  ? border_we7ov125 : "";
let g_border_0578wv25 = border_0578wv25 && border_0578wv25 != undefined  ? border_0578wv25 : "";
let g_dimension_0bbgj425 = dimension_0bbgj425 && dimension_0bbgj425 != undefined  ? dimension_0bbgj425 : "";
let g_border_ajb40e25 = border_ajb40e25 && border_ajb40e25 != undefined  ? border_ajb40e25 : "";
let g_border_x678uj25 = border_x678uj25 && border_x678uj25 != undefined  ? border_x678uj25 : "";
let g_dimension_v9zx8u25 = dimension_v9zx8u25 && dimension_v9zx8u25 != undefined  ? dimension_v9zx8u25 : "";
let g_boxshadow_scnpbl25 = boxshadow_scnpbl25 && boxshadow_scnpbl25 != undefined  ? boxshadow_scnpbl25 : "";
let g_boxshadow_ozmpjn25 = boxshadow_ozmpjn25 && boxshadow_ozmpjn25 != undefined  ? boxshadow_ozmpjn25 : "";
        
let repeater_wwg5yt23_1p25 = "";
                            
repeater_wwg5yt23  && repeater_wwg5yt23.map((r_item, index) => {
                                
let grnp_iconscontrol_br23qo23 = r_item?.iconscontrol_br23qo23 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_br23qo23+'"></i></span>' : '';

let grnp_url_ualqto23_url = r_item?.url_ualqto23?.url && r_item?.url_ualqto23?.url != undefined ?  r_item?.url_ualqto23.url : "";
let grnp_url_ualqto23_target = r_item?.url_ualqto23?.target && r_item?.url_ualqto23?.target != undefined ?  r_item?.url_ualqto23.target : "";
let grnp_url_ualqto23_nofollow = r_item?.url_ualqto23?.nofollow && r_item?.url_ualqto23?.nofollow != undefined ?  r_item?.url_ualqto23.nofollow : "";
let grnp_url_ualqto23_ctmArt = r_item?.url_ualqto23?.attr && r_item?.url_ualqto23?.attr != undefined ?  r_item?.url_ualqto23.attr : "";
                    let grnp_url_ualqto23_attr = ''

                    if (grnp_url_ualqto23_ctmArt) {
                        let main_array = grnp_url_ualqto23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_ualqto23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_text_6a7np223 = r_item.text_6a7np223  ? r_item.text_6a7np223 : "";
                                repeater_wwg5yt23_1p25 += `<div class="tp-repeater-item-${r_item._key}" data-repeater_wwg5yt23="{repeater_wwg5yt23}">
                      <a href="${grnp_url_ualqto23_url}" class="profile-icons" target="${grnp_url_ualqto23_target}" rel="${grnp_url_ualqto23_nofollow} noopener">
                          ${grnp_iconscontrol_br23qo23 }
                          <span class="tooltiptext" data-tooltip="${grnp_text_6a7np223}">${grnp_text_6a7np223}</span>
                      </a>
                  </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-a86ns424", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_a86ns424 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-profile-card-hover-effect">
    <div class="wkit-profile-card-inner">
        <div class="wkit-profile-card-image" style="background-image:url(${g_media_dsfmhh23})">
          <div class="wkit-profile-card-border">
            <h2 class="profile-card-heading">${g_text_itcils23}</h2>
            <h4 class="profile-card-heading-sub-title">${g_text_gh3meu23}</h4>
              <div class="profile-card-icons">
                  ${repeater_wwg5yt23_1p25}
             </div>
         </div>
      </div>
    </div>
</div>
`
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Profile_Card_Hover_Effect_a86ns424();