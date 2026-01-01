
    class Swipe_Button_gq644y24 {
        constructor() {
            this.Swipe_Button_gq644y24_73znkb25();
        }
    
        Swipe_Button_gq644y24_73znkb25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Url,Pmgc_IconList,Pmgc_Select,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-gq644y24', {
        title: __('Swipe Button'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fab fa-leanpub tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Swipe Button'),__('CTA'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_s7a16l23Function = (unit, type) => {
                var g_slider_s7a16l23_list = [];
                g_slider_s7a16l23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_s7a16l23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_s7a16l23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_s7a16l23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_s7a16l23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_s7a16l23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_s7a16l23_list[unit][type];
            };
const slider_xltx9t23Function = (unit, type) => {
                var g_slider_xltx9t23_list = [];
                g_slider_xltx9t23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_xltx9t23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_xltx9t23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_xltx9t23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_xltx9t23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_xltx9t23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_xltx9t23_list[unit][type];
            };
const slider_dnehhx25Function = (unit, type) => {
                var g_slider_dnehhx25_list = [];
                g_slider_dnehhx25_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 0.5 };
g_slider_dnehhx25_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_dnehhx25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_dnehhx25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_dnehhx25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_dnehhx25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_dnehhx25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_a2tl8t23,
url_xhyz9y23,
iconscontrol_dj5gjt23,
select_gga2vw23,
choose_ioive023,
rawhtml_muqxjj25,
dimension_frabre23,
typography_r0bhug23,
color_bryhtd23,
background_g840zn23,
color_fdyk2623,
rawhtml_oumtve25,
border_fr0u3f23,
background_bccjjx23,
rawhtml_e9rzr525,
border_mhzvwb23,
dimension_bp67dx23,
boxshadow_3wzbi523,
boxshadow_d3tc3a23,
normalhover_bvoxjo23,
slider_s7a16l23,
slider_xltx9t23,
slider_dnehhx25,
color_cxxh2623,
color_kuiyv223,
color_pos1qt25,
color_iil56a25,
normalhover_6h5z7925,

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
                        main_function_gq644y24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_gq644y24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_a2tl8t23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_a2tl8t23: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_xhyz9y23,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_xhyz9y23: value }),
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_dj5gjt23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_dj5gjt23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
( iconscontrol_dj5gjt23 != "" ) && React.createElement(Pmgc_Select, {
                label: __(`Icon Position`),
                options:[['before',__('Before')],['after',__('After')],],
                separator:"default",
                
                
                value: select_gga2vw23,
                onChange: (value) => {setAttributes({ select_gga2vw23: value }) },
            }),
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_ioive023,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_ioive023: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_muqxjj25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/swipe-button-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_muqxjj25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Button"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_frabre23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_frabre23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_r0bhug23,
            onChange: (value) => setAttributes({ typography_r0bhug23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_bryhtd23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_bryhtd23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_g840zn23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_g840zn23: value }),
            }), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_oumtve25,
            description: `<div class="wdk-note" style="align-items: flex-start;
    background: #f0f3f4;
    border-top: 2px solid #1717cc;
    color: #010101a6;
    display: flex;
    flex-direction: column;
    font-size: 12px;
    font-style: italic;
    margin: 15px 0;
    padding: 10px;">
    <b style="color:#000;"> Note </b>
Gradient color should be 50% on both side and angle 90%.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_oumtve25: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_fr0u3f23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_fr0u3f23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_bp67dx23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_bp67dx23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_3wzbi523,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_3wzbi523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_fdyk2623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_fdyk2623: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_bccjjx23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_bccjjx23: value }),
            }), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_e9rzr525,
            description: `<div class="wdk-note" style="align-items: flex-start;
    background: #f0f3f4;
    border-top: 2px solid #1717cc;
    color: #010101a6;
    display: flex;
    flex-direction: column;
    font-size: 12px;
    font-style: italic;
    margin: 15px 0;
    padding: 10px;">
    <b style="color:#000;"> Note </b>
Gradient color should be 50% on both side and angle 90%.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_e9rzr525: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_mhzvwb23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_mhzvwb23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_d3tc3a23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_d3tc3a23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_s7a16l23,
            
            min: slider_s7a16l23 && slider_s7a16l23.unit ? slider_s7a16l23Function(slider_s7a16l23.unit, 'min') : 0,
            max: slider_s7a16l23 && slider_s7a16l23.unit ? slider_s7a16l23Function(slider_s7a16l23.unit, 'max') : 100,
            step: slider_s7a16l23 && slider_s7a16l23.unit ? slider_s7a16l23Function(slider_s7a16l23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_s7a16l23: value }),
            }), 
( iconscontrol_dj5gjt23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_xltx9t23,
            
            min: slider_xltx9t23 && slider_xltx9t23.unit ? slider_xltx9t23Function(slider_xltx9t23.unit, 'min') : 0,
            max: slider_xltx9t23 && slider_xltx9t23.unit ? slider_xltx9t23Function(slider_xltx9t23.unit, 'max') : 100,
            step: slider_xltx9t23 && slider_xltx9t23.unit ? slider_xltx9t23Function(slider_xltx9t23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_xltx9t23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Stroke Width`),
            separator:'default',
            value: slider_dnehhx25,
            
            min: slider_dnehhx25 && slider_dnehhx25.unit ? slider_dnehhx25Function(slider_dnehhx25.unit, 'min') : 0,
            max: slider_dnehhx25 && slider_dnehhx25.unit ? slider_dnehhx25Function(slider_dnehhx25.unit, 'max') : 100,
            step: slider_dnehhx25 && slider_dnehhx25.unit ? slider_dnehhx25Function(slider_dnehhx25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_dnehhx25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( iconscontrol_dj5gjt23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_cxxh2623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cxxh2623: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Stroke Color`),
            value: color_pos1qt25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_pos1qt25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( iconscontrol_dj5gjt23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_kuiyv223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kuiyv223: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Stroke Color`),
            value: color_iil56a25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_iil56a25: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-gq644y24", block_id, false, props.clientId);
                }
            }

            
let g_text_a2tl8t23 = text_a2tl8t23 && text_a2tl8t23 != undefined  ? text_a2tl8t23 : "";
let g_url_xhyz9y23_url = url_xhyz9y23?.url && url_xhyz9y23?.url != undefined ? url_xhyz9y23.url : "";
let g_url_xhyz9y23_target = url_xhyz9y23?.target && url_xhyz9y23?.target != undefined ? url_xhyz9y23.target : "";
let g_url_xhyz9y23_nofollow = url_xhyz9y23?.nofollow && url_xhyz9y23?.nofollow != undefined ? url_xhyz9y23.nofollow : "";
let g_url_xhyz9y23_ctmArt = url_xhyz9y23?.attr != undefined ? url_xhyz9y23.attr : "";
                    let g_url_xhyz9y23_attr = ''

                    if (g_url_xhyz9y23_ctmArt) {
                        let main_array = g_url_xhyz9y23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_xhyz9y23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_dj5gjt23 = iconscontrol_dj5gjt23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_dj5gjt23+'"></i></span>' : '';

let g_select_gga2vw23 = select_gga2vw23 && select_gga2vw23 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? select_gga2vw23 : "";
let g_choose_ioive023 = choose_ioive023 && choose_ioive023 != undefined  ? choose_ioive023 : "";
let g_rawhtml_muqxjj25 = rawhtml_muqxjj25 && rawhtml_muqxjj25 != undefined  ? rawhtml_muqxjj25 : "";
let g_dimension_frabre23 = dimension_frabre23 && dimension_frabre23 != undefined  ? dimension_frabre23 : "";
let g_typography_r0bhug23 = typography_r0bhug23 && typography_r0bhug23 != undefined  ? typography_r0bhug23 : "";
let g_color_bryhtd23 = color_bryhtd23 && color_bryhtd23 != undefined  ? color_bryhtd23 : "";
let g_background_g840zn23 = background_g840zn23 && background_g840zn23 != undefined  ? background_g840zn23 : "";
let g_color_fdyk2623 = color_fdyk2623 && color_fdyk2623 != undefined  ? color_fdyk2623 : "";
let g_rawhtml_oumtve25 = rawhtml_oumtve25 && rawhtml_oumtve25 != undefined  ? rawhtml_oumtve25 : "";
let g_border_fr0u3f23 = border_fr0u3f23 && border_fr0u3f23 != undefined  ? border_fr0u3f23 : "";
let g_background_bccjjx23 = background_bccjjx23 && background_bccjjx23 != undefined  ? background_bccjjx23 : "";
let g_rawhtml_e9rzr525 = rawhtml_e9rzr525 && rawhtml_e9rzr525 != undefined  ? rawhtml_e9rzr525 : "";
let g_border_mhzvwb23 = border_mhzvwb23 && border_mhzvwb23 != undefined  ? border_mhzvwb23 : "";
let g_dimension_bp67dx23 = dimension_bp67dx23 && dimension_bp67dx23 != undefined  ? dimension_bp67dx23 : "";
let g_boxshadow_3wzbi523 = boxshadow_3wzbi523 && boxshadow_3wzbi523 != undefined  ? boxshadow_3wzbi523 : "";
let g_boxshadow_d3tc3a23 = boxshadow_d3tc3a23 && boxshadow_d3tc3a23 != undefined  ? boxshadow_d3tc3a23 : "";
let g_color_cxxh2623 = color_cxxh2623 && color_cxxh2623 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? color_cxxh2623 : "";
let g_color_kuiyv223 = color_kuiyv223 && color_kuiyv223 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? color_kuiyv223 : "";
let g_color_pos1qt25 = color_pos1qt25 && color_pos1qt25 != undefined  ? color_pos1qt25 : "";
let g_color_iil56a25 = color_iil56a25 && color_iil56a25 != undefined  ? color_iil56a25 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_gq644y24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-swipe-btn-main">
    <a class="wkit-swipe-btn-link" href="${g_url_xhyz9y23_url}" target="${g_url_xhyz9y23_target}" rel="${g_url_xhyz9y23_nofollow} noopener">
        <div class="wkit-swipe-btn ${g_select_gga2vw23}">
            <div class="wkit-swipe-btn-text">${g_text_a2tl8t23}</div>
            <div class="wkit-swipe-icon">${g_iconscontrol_dj5gjt23} </div>
        </div>
   </a>
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
            text_a2tl8t23,
url_xhyz9y23,
iconscontrol_dj5gjt23,
select_gga2vw23,
choose_ioive023,
rawhtml_muqxjj25,
dimension_frabre23,
typography_r0bhug23,
color_bryhtd23,
background_g840zn23,
color_fdyk2623,
rawhtml_oumtve25,
border_fr0u3f23,
background_bccjjx23,
rawhtml_e9rzr525,
border_mhzvwb23,
dimension_bp67dx23,
boxshadow_3wzbi523,
boxshadow_d3tc3a23,
normalhover_bvoxjo23,
slider_s7a16l23,
slider_xltx9t23,
slider_dnehhx25,
color_cxxh2623,
color_kuiyv223,
color_pos1qt25,
color_iil56a25,
normalhover_6h5z7925,

            block_id,
        } = attributes;

        

        

        

        
let g_text_a2tl8t23 = text_a2tl8t23 && text_a2tl8t23 != undefined  ? text_a2tl8t23 : "";
let g_url_xhyz9y23_url = url_xhyz9y23?.url && url_xhyz9y23?.url != undefined ? url_xhyz9y23.url : "";
let g_url_xhyz9y23_target = url_xhyz9y23?.target && url_xhyz9y23?.target != undefined ? url_xhyz9y23.target : "";
let g_url_xhyz9y23_nofollow = url_xhyz9y23?.nofollow && url_xhyz9y23?.nofollow != undefined ? url_xhyz9y23.nofollow : "";
let g_url_xhyz9y23_ctmArt = url_xhyz9y23?.attr != undefined ? url_xhyz9y23.attr : "";
                    let g_url_xhyz9y23_attr = ''

                    if (g_url_xhyz9y23_ctmArt) {
                        let main_array = g_url_xhyz9y23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_xhyz9y23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_dj5gjt23 = iconscontrol_dj5gjt23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_dj5gjt23+'"></i></span>' : '';

let g_select_gga2vw23 = select_gga2vw23 && select_gga2vw23 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? select_gga2vw23 : "";
let g_choose_ioive023 = choose_ioive023 && choose_ioive023 != undefined  ? choose_ioive023 : "";
let g_rawhtml_muqxjj25 = rawhtml_muqxjj25 && rawhtml_muqxjj25 != undefined  ? rawhtml_muqxjj25 : "";
let g_dimension_frabre23 = dimension_frabre23 && dimension_frabre23 != undefined  ? dimension_frabre23 : "";
let g_typography_r0bhug23 = typography_r0bhug23 && typography_r0bhug23 != undefined  ? typography_r0bhug23 : "";
let g_color_bryhtd23 = color_bryhtd23 && color_bryhtd23 != undefined  ? color_bryhtd23 : "";
let g_background_g840zn23 = background_g840zn23 && background_g840zn23 != undefined  ? background_g840zn23 : "";
let g_color_fdyk2623 = color_fdyk2623 && color_fdyk2623 != undefined  ? color_fdyk2623 : "";
let g_rawhtml_oumtve25 = rawhtml_oumtve25 && rawhtml_oumtve25 != undefined  ? rawhtml_oumtve25 : "";
let g_border_fr0u3f23 = border_fr0u3f23 && border_fr0u3f23 != undefined  ? border_fr0u3f23 : "";
let g_background_bccjjx23 = background_bccjjx23 && background_bccjjx23 != undefined  ? background_bccjjx23 : "";
let g_rawhtml_e9rzr525 = rawhtml_e9rzr525 && rawhtml_e9rzr525 != undefined  ? rawhtml_e9rzr525 : "";
let g_border_mhzvwb23 = border_mhzvwb23 && border_mhzvwb23 != undefined  ? border_mhzvwb23 : "";
let g_dimension_bp67dx23 = dimension_bp67dx23 && dimension_bp67dx23 != undefined  ? dimension_bp67dx23 : "";
let g_boxshadow_3wzbi523 = boxshadow_3wzbi523 && boxshadow_3wzbi523 != undefined  ? boxshadow_3wzbi523 : "";
let g_boxshadow_d3tc3a23 = boxshadow_d3tc3a23 && boxshadow_d3tc3a23 != undefined  ? boxshadow_d3tc3a23 : "";
let g_color_cxxh2623 = color_cxxh2623 && color_cxxh2623 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? color_cxxh2623 : "";
let g_color_kuiyv223 = color_kuiyv223 && color_kuiyv223 != undefined && ( (iconscontrol_dj5gjt23 != "") ) ? color_kuiyv223 : "";
let g_color_pos1qt25 = color_pos1qt25 && color_pos1qt25 != undefined  ? color_pos1qt25 : "";
let g_color_iil56a25 = color_iil56a25 && color_iil56a25 != undefined  ? color_iil56a25 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-gq644y24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_gq644y24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-swipe-btn-main">
    <a class="wkit-swipe-btn-link" href="${g_url_xhyz9y23_url}" target="${g_url_xhyz9y23_target}" rel="${g_url_xhyz9y23_nofollow} noopener">
        <div class="wkit-swipe-btn ${g_select_gga2vw23}">
            <div class="wkit-swipe-btn-text">${g_text_a2tl8t23}</div>
            <div class="wkit-swipe-icon">${g_iconscontrol_dj5gjt23} </div>
        </div>
   </a>
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
    new Swipe_Button_gq644y24();