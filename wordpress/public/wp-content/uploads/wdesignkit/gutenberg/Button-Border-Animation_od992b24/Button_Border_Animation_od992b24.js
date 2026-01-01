
    class Button_Border_Animation_od992b24 {
        constructor() {
            this.Button_Border_Animation_od992b24_y0sa9o25();
        }
    
        Button_Border_Animation_od992b24_y0sa9o25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_IconList,Pmgc_Url,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Range,Pmgc_Color,Pmgc_Background,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-od992b24', {
        title: __('Button Border Animation'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-border-style tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Border Animation'),__('Button'),__('CTA'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_n68kl624Function = (unit, type) => {
                var g_slider_n68kl624_list = [];
                g_slider_n68kl624_list['px'] = { "type": "px", "min": 0, "max": 20, "step": 1 };
g_slider_n68kl624_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_n68kl624_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_n68kl624_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_n68kl624_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_n68kl624_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_n68kl624_list[unit][type];
            };
const slider_z9nuar25Function = (unit, type) => {
                var g_slider_z9nuar25_list = [];
                g_slider_z9nuar25_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_z9nuar25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_z9nuar25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_z9nuar25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_z9nuar25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_z9nuar25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_z9nuar25_list[unit][type];
            };
const slider_oujvhj24Function = (unit, type) => {
                var g_slider_oujvhj24_list = [];
                g_slider_oujvhj24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_oujvhj24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_oujvhj24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_oujvhj24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_oujvhj24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_oujvhj24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_oujvhj24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_adwel223,
iconscontrol_e6jy4m24,
url_i6584224,
choose_hxfi6x24,
rawhtml_5e51kh25,
dimension_bl6pma24,
typography_ja5d8223,
slider_n68kl624,
color_znix5z23,
background_zufe5323,
color_z7jnsw23,
background_y5u0h423,
color_gg9hk724,
color_odugzw24,
dimension_lf0egz24,
boxshadow_cmt2cy25,
boxshadow_me7bcy25,
normalhover_kf3ety23,
slider_z9nuar25,
slider_oujvhj24,
color_1l3ax925,
color_9xrf6i25,
normalhover_rsiuq325,

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
                        main_function_od992b24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_od992b24 = ($scope) => {
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
                value: text_adwel223,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_adwel223: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_e6jy4m24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_e6jy4m24: value }),
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_i6584224,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_i6584224: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_hxfi6x24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_hxfi6x24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_5e51kh25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/button-border-animation-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_5e51kh25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Button"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_bl6pma24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_bl6pma24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_ja5d8223,
            onChange: (value) => setAttributes({ typography_ja5d8223: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Border Width`),
            separator:'default',
            value: slider_n68kl624,
            
            min: slider_n68kl624 && slider_n68kl624.unit ? slider_n68kl624Function(slider_n68kl624.unit, 'min') : 0,
            max: slider_n68kl624 && slider_n68kl624.unit ? slider_n68kl624Function(slider_n68kl624.unit, 'max') : 100,
            step: slider_n68kl624 && slider_n68kl624.unit ? slider_n68kl624Function(slider_n68kl624.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_n68kl624: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_znix5z23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_znix5z23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_zufe5323,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_zufe5323: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Animate Border Color`),
            value: color_gg9hk724,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_gg9hk724: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_lf0egz24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_lf0egz24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cmt2cy25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cmt2cy25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_z7jnsw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_z7jnsw23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_y5u0h423,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_y5u0h423: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Animate Border Color`),
            value: color_odugzw24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_odugzw24: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_me7bcy25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_me7bcy25: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_z9nuar25,
            
            min: slider_z9nuar25 && slider_z9nuar25.unit ? slider_z9nuar25Function(slider_z9nuar25.unit, 'min') : 0,
            max: slider_z9nuar25 && slider_z9nuar25.unit ? slider_z9nuar25Function(slider_z9nuar25.unit, 'max') : 100,
            step: slider_z9nuar25 && slider_z9nuar25.unit ? slider_z9nuar25Function(slider_z9nuar25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_z9nuar25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_oujvhj24,
            
            min: slider_oujvhj24 && slider_oujvhj24.unit ? slider_oujvhj24Function(slider_oujvhj24.unit, 'min') : 0,
            max: slider_oujvhj24 && slider_oujvhj24.unit ? slider_oujvhj24Function(slider_oujvhj24.unit, 'max') : 100,
            step: slider_oujvhj24 && slider_oujvhj24.unit ? slider_oujvhj24Function(slider_oujvhj24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_oujvhj24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_1l3ax925,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1l3ax925: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_9xrf6i25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9xrf6i25: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-od992b24", block_id, false, props.clientId);
                }
            }

            
let g_text_adwel223 = text_adwel223 && text_adwel223 != undefined  ? text_adwel223 : "";
let g_iconscontrol_e6jy4m24 = iconscontrol_e6jy4m24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_e6jy4m24+'"></i></span>' : '';

let g_url_i6584224_url = url_i6584224?.url && url_i6584224?.url != undefined ? url_i6584224.url : "";
let g_url_i6584224_target = url_i6584224?.target && url_i6584224?.target != undefined ? url_i6584224.target : "";
let g_url_i6584224_nofollow = url_i6584224?.nofollow && url_i6584224?.nofollow != undefined ? url_i6584224.nofollow : "";
let g_url_i6584224_ctmArt = url_i6584224?.attr != undefined ? url_i6584224.attr : "";
                    let g_url_i6584224_attr = ''

                    if (g_url_i6584224_ctmArt) {
                        let main_array = g_url_i6584224_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_i6584224_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_choose_hxfi6x24 = choose_hxfi6x24 && choose_hxfi6x24 != undefined  ? choose_hxfi6x24 : "";
let g_rawhtml_5e51kh25 = rawhtml_5e51kh25 && rawhtml_5e51kh25 != undefined  ? rawhtml_5e51kh25 : "";
let g_dimension_bl6pma24 = dimension_bl6pma24 && dimension_bl6pma24 != undefined  ? dimension_bl6pma24 : "";
let g_typography_ja5d8223 = typography_ja5d8223 && typography_ja5d8223 != undefined  ? typography_ja5d8223 : "";
let g_color_znix5z23 = color_znix5z23 && color_znix5z23 != undefined  ? color_znix5z23 : "";
let g_background_zufe5323 = background_zufe5323 && background_zufe5323 != undefined  ? background_zufe5323 : "";
let g_color_z7jnsw23 = color_z7jnsw23 && color_z7jnsw23 != undefined  ? color_z7jnsw23 : "";
let g_background_y5u0h423 = background_y5u0h423 && background_y5u0h423 != undefined  ? background_y5u0h423 : "";
let g_color_gg9hk724 = color_gg9hk724 && color_gg9hk724 != undefined  ? color_gg9hk724 : "";
let g_color_odugzw24 = color_odugzw24 && color_odugzw24 != undefined  ? color_odugzw24 : "";
let g_dimension_lf0egz24 = dimension_lf0egz24 && dimension_lf0egz24 != undefined  ? dimension_lf0egz24 : "";
let g_boxshadow_cmt2cy25 = boxshadow_cmt2cy25 && boxshadow_cmt2cy25 != undefined  ? boxshadow_cmt2cy25 : "";
let g_boxshadow_me7bcy25 = boxshadow_me7bcy25 && boxshadow_me7bcy25 != undefined  ? boxshadow_me7bcy25 : "";
let g_color_1l3ax925 = color_1l3ax925 && color_1l3ax925 != undefined  ? color_1l3ax925 : "";
let g_color_9xrf6i25 = color_9xrf6i25 && color_9xrf6i25 != undefined  ? color_9xrf6i25 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_od992b24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-button-border-animation-wrapper">
    <a href="${g_url_i6584224_url}" target="${g_url_i6584224_target}" class="wkit-button-border-animtion-wrap" rel="noopener">
        <div class="wkit-button-border-animation">
            <span class="wkit-button-border-animation-inner">
            <span class="wkit-button-inner"> </span></span>
            <span class="wkit-button-border-text"> ${g_text_adwel223} </span>
            <span class="wkit-border-icon">${g_iconscontrol_e6jy4m24}</span>
        </div>
    </a>   
</div>    `
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
            text_adwel223,
iconscontrol_e6jy4m24,
url_i6584224,
choose_hxfi6x24,
rawhtml_5e51kh25,
dimension_bl6pma24,
typography_ja5d8223,
slider_n68kl624,
color_znix5z23,
background_zufe5323,
color_z7jnsw23,
background_y5u0h423,
color_gg9hk724,
color_odugzw24,
dimension_lf0egz24,
boxshadow_cmt2cy25,
boxshadow_me7bcy25,
normalhover_kf3ety23,
slider_z9nuar25,
slider_oujvhj24,
color_1l3ax925,
color_9xrf6i25,
normalhover_rsiuq325,

            block_id,
        } = attributes;

        

        

        

        
let g_text_adwel223 = text_adwel223 && text_adwel223 != undefined  ? text_adwel223 : "";
let g_iconscontrol_e6jy4m24 = iconscontrol_e6jy4m24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_e6jy4m24+'"></i></span>' : '';

let g_url_i6584224_url = url_i6584224?.url && url_i6584224?.url != undefined ? url_i6584224.url : "";
let g_url_i6584224_target = url_i6584224?.target && url_i6584224?.target != undefined ? url_i6584224.target : "";
let g_url_i6584224_nofollow = url_i6584224?.nofollow && url_i6584224?.nofollow != undefined ? url_i6584224.nofollow : "";
let g_url_i6584224_ctmArt = url_i6584224?.attr != undefined ? url_i6584224.attr : "";
                    let g_url_i6584224_attr = ''

                    if (g_url_i6584224_ctmArt) {
                        let main_array = g_url_i6584224_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_i6584224_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_choose_hxfi6x24 = choose_hxfi6x24 && choose_hxfi6x24 != undefined  ? choose_hxfi6x24 : "";
let g_rawhtml_5e51kh25 = rawhtml_5e51kh25 && rawhtml_5e51kh25 != undefined  ? rawhtml_5e51kh25 : "";
let g_dimension_bl6pma24 = dimension_bl6pma24 && dimension_bl6pma24 != undefined  ? dimension_bl6pma24 : "";
let g_typography_ja5d8223 = typography_ja5d8223 && typography_ja5d8223 != undefined  ? typography_ja5d8223 : "";
let g_color_znix5z23 = color_znix5z23 && color_znix5z23 != undefined  ? color_znix5z23 : "";
let g_background_zufe5323 = background_zufe5323 && background_zufe5323 != undefined  ? background_zufe5323 : "";
let g_color_z7jnsw23 = color_z7jnsw23 && color_z7jnsw23 != undefined  ? color_z7jnsw23 : "";
let g_background_y5u0h423 = background_y5u0h423 && background_y5u0h423 != undefined  ? background_y5u0h423 : "";
let g_color_gg9hk724 = color_gg9hk724 && color_gg9hk724 != undefined  ? color_gg9hk724 : "";
let g_color_odugzw24 = color_odugzw24 && color_odugzw24 != undefined  ? color_odugzw24 : "";
let g_dimension_lf0egz24 = dimension_lf0egz24 && dimension_lf0egz24 != undefined  ? dimension_lf0egz24 : "";
let g_boxshadow_cmt2cy25 = boxshadow_cmt2cy25 && boxshadow_cmt2cy25 != undefined  ? boxshadow_cmt2cy25 : "";
let g_boxshadow_me7bcy25 = boxshadow_me7bcy25 && boxshadow_me7bcy25 != undefined  ? boxshadow_me7bcy25 : "";
let g_color_1l3ax925 = color_1l3ax925 && color_1l3ax925 != undefined  ? color_1l3ax925 : "";
let g_color_9xrf6i25 = color_9xrf6i25 && color_9xrf6i25 != undefined  ? color_9xrf6i25 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-od992b24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_od992b24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-button-border-animation-wrapper">
    <a href="${g_url_i6584224_url}" target="${g_url_i6584224_target}" class="wkit-button-border-animtion-wrap" rel="noopener">
        <div class="wkit-button-border-animation">
            <span class="wkit-button-border-animation-inner">
            <span class="wkit-button-inner"> </span></span>
            <span class="wkit-button-border-text"> ${g_text_adwel223} </span>
            <span class="wkit-border-icon">${g_iconscontrol_e6jy4m24}</span>
        </div>
    </a>   
</div>    `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Button_Border_Animation_od992b24();