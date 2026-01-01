
    class Rainbow_Button_q6s5kl25 {
        constructor() {
            this.Rainbow_Button_q6s5kl25_n5glmo25();
        }
    
        Rainbow_Button_q6s5kl25_n5glmo25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Url,Pmgc_IconList,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-q6s5kl25', {
        title: __('Rainbow Button'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-rainbow tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Rainbow Button'),__('CTA Button'),__('Color Gradient'),__('Animated Button'),__('Gutenberg Block'),__('Interactive Button'),__('Dynamic Button Effect'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_5jfm5325Function = (unit, type) => {
                var g_slider_5jfm5325_list = [];
                g_slider_5jfm5325_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_5jfm5325_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_5jfm5325_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5jfm5325_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5jfm5325_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5jfm5325_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5jfm5325_list[unit][type];
            };
const slider_1ww9yc24Function = (unit, type) => {
                var g_slider_1ww9yc24_list = [];
                g_slider_1ww9yc24_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_1ww9yc24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_1ww9yc24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_1ww9yc24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_1ww9yc24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_1ww9yc24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_1ww9yc24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_rzhtdf24,
url_ch78tn24,
iconscontrol_nmhs8g24,
choose_w61zfl24,
rawhtml_xzxyv925,
dimension_9bzkio24,
typography_p25eoi24,
color_sd14kl24,
background_22dzru24,
border_z9p0ji24,
dimension_nt5fto24,
boxshadow_7fs8rj24,
color_yy1dlm24,
background_owycnd24,
border_v0b6cq24,
boxshadow_lsjokn24,
normalhover_9tteh324,
slider_5jfm5325,
slider_1ww9yc24,
color_40dqfl25,
color_ma8l3625,
normalhover_e4iish25,

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
                        main_function_q6s5kl25(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_q6s5kl25 = ($scope) => {
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
                value: text_rzhtdf24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_rzhtdf24: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_ch78tn24,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_ch78tn24: value }),
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_nmhs8g24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_nmhs8g24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_w61zfl24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_w61zfl24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_xzxyv925,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/rainbow-button-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_xzxyv925: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Button"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_9bzkio24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_9bzkio24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_p25eoi24,
            onChange: (value) => setAttributes({ typography_p25eoi24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_sd14kl24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_sd14kl24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_22dzru24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_22dzru24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_z9p0ji24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_z9p0ji24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_nt5fto24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_nt5fto24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_7fs8rj24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_7fs8rj24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_yy1dlm24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yy1dlm24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_owycnd24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_owycnd24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_v0b6cq24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_v0b6cq24: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lsjokn24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lsjokn24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_5jfm5325,
            
            min: slider_5jfm5325 && slider_5jfm5325.unit ? slider_5jfm5325Function(slider_5jfm5325.unit, 'min') : 0,
            max: slider_5jfm5325 && slider_5jfm5325.unit ? slider_5jfm5325Function(slider_5jfm5325.unit, 'max') : 100,
            step: slider_5jfm5325 && slider_5jfm5325.unit ? slider_5jfm5325Function(slider_5jfm5325.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5jfm5325: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_1ww9yc24,
            
            min: slider_1ww9yc24 && slider_1ww9yc24.unit ? slider_1ww9yc24Function(slider_1ww9yc24.unit, 'min') : 0,
            max: slider_1ww9yc24 && slider_1ww9yc24.unit ? slider_1ww9yc24Function(slider_1ww9yc24.unit, 'max') : 100,
            step: slider_1ww9yc24 && slider_1ww9yc24.unit ? slider_1ww9yc24Function(slider_1ww9yc24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_1ww9yc24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_40dqfl25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_40dqfl25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_ma8l3625,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ma8l3625: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-q6s5kl25", block_id, false, props.clientId);
                }
            }

            
let g_text_rzhtdf24 = text_rzhtdf24 && text_rzhtdf24 != undefined  ? text_rzhtdf24 : "";
let g_url_ch78tn24_url = url_ch78tn24?.url && url_ch78tn24?.url != undefined ? url_ch78tn24.url : "";
let g_url_ch78tn24_target = url_ch78tn24?.target && url_ch78tn24?.target != undefined ? url_ch78tn24.target : "";
let g_url_ch78tn24_nofollow = url_ch78tn24?.nofollow && url_ch78tn24?.nofollow != undefined ? url_ch78tn24.nofollow : "";
let g_url_ch78tn24_ctmArt = url_ch78tn24?.attr != undefined ? url_ch78tn24.attr : "";
                    let g_url_ch78tn24_attr = ''

                    if (g_url_ch78tn24_ctmArt) {
                        let main_array = g_url_ch78tn24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_ch78tn24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_nmhs8g24 = iconscontrol_nmhs8g24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_nmhs8g24+'"></i></span>' : '';

let g_choose_w61zfl24 = choose_w61zfl24 && choose_w61zfl24 != undefined  ? choose_w61zfl24 : "";
let g_rawhtml_xzxyv925 = rawhtml_xzxyv925 && rawhtml_xzxyv925 != undefined  ? rawhtml_xzxyv925 : "";
let g_dimension_9bzkio24 = dimension_9bzkio24 && dimension_9bzkio24 != undefined  ? dimension_9bzkio24 : "";
let g_typography_p25eoi24 = typography_p25eoi24 && typography_p25eoi24 != undefined  ? typography_p25eoi24 : "";
let g_color_sd14kl24 = color_sd14kl24 && color_sd14kl24 != undefined  ? color_sd14kl24 : "";
let g_background_22dzru24 = background_22dzru24 && background_22dzru24 != undefined  ? background_22dzru24 : "";
let g_border_z9p0ji24 = border_z9p0ji24 && border_z9p0ji24 != undefined  ? border_z9p0ji24 : "";
let g_dimension_nt5fto24 = dimension_nt5fto24 && dimension_nt5fto24 != undefined  ? dimension_nt5fto24 : "";
let g_boxshadow_7fs8rj24 = boxshadow_7fs8rj24 && boxshadow_7fs8rj24 != undefined  ? boxshadow_7fs8rj24 : "";
let g_color_yy1dlm24 = color_yy1dlm24 && color_yy1dlm24 != undefined  ? color_yy1dlm24 : "";
let g_background_owycnd24 = background_owycnd24 && background_owycnd24 != undefined  ? background_owycnd24 : "";
let g_border_v0b6cq24 = border_v0b6cq24 && border_v0b6cq24 != undefined  ? border_v0b6cq24 : "";
let g_boxshadow_lsjokn24 = boxshadow_lsjokn24 && boxshadow_lsjokn24 != undefined  ? boxshadow_lsjokn24 : "";
let g_color_40dqfl25 = color_40dqfl25 && color_40dqfl25 != undefined  ? color_40dqfl25 : "";
let g_color_ma8l3625 = color_ma8l3625 && color_ma8l3625 != undefined  ? color_ma8l3625 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_q6s5kl25 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-rbtn-container">
    <div class="wkit-rbtn-inner">
        <a href="${g_url_ch78tn24_url}" class="wkit-rbtn" target="${g_url_ch78tn24_target}" rel="${g_url_ch78tn24_nofollow} noopener">${g_text_rzhtdf24}<span class="wkit-rainbow-btn-icon">${g_iconscontrol_nmhs8g24}</span></a>
    </div>
</div>`
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
            text_rzhtdf24,
url_ch78tn24,
iconscontrol_nmhs8g24,
choose_w61zfl24,
rawhtml_xzxyv925,
dimension_9bzkio24,
typography_p25eoi24,
color_sd14kl24,
background_22dzru24,
border_z9p0ji24,
dimension_nt5fto24,
boxshadow_7fs8rj24,
color_yy1dlm24,
background_owycnd24,
border_v0b6cq24,
boxshadow_lsjokn24,
normalhover_9tteh324,
slider_5jfm5325,
slider_1ww9yc24,
color_40dqfl25,
color_ma8l3625,
normalhover_e4iish25,

            block_id,
        } = attributes;

        

        

        

        
let g_text_rzhtdf24 = text_rzhtdf24 && text_rzhtdf24 != undefined  ? text_rzhtdf24 : "";
let g_url_ch78tn24_url = url_ch78tn24?.url && url_ch78tn24?.url != undefined ? url_ch78tn24.url : "";
let g_url_ch78tn24_target = url_ch78tn24?.target && url_ch78tn24?.target != undefined ? url_ch78tn24.target : "";
let g_url_ch78tn24_nofollow = url_ch78tn24?.nofollow && url_ch78tn24?.nofollow != undefined ? url_ch78tn24.nofollow : "";
let g_url_ch78tn24_ctmArt = url_ch78tn24?.attr != undefined ? url_ch78tn24.attr : "";
                    let g_url_ch78tn24_attr = ''

                    if (g_url_ch78tn24_ctmArt) {
                        let main_array = g_url_ch78tn24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_ch78tn24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_nmhs8g24 = iconscontrol_nmhs8g24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_nmhs8g24+'"></i></span>' : '';

let g_choose_w61zfl24 = choose_w61zfl24 && choose_w61zfl24 != undefined  ? choose_w61zfl24 : "";
let g_rawhtml_xzxyv925 = rawhtml_xzxyv925 && rawhtml_xzxyv925 != undefined  ? rawhtml_xzxyv925 : "";
let g_dimension_9bzkio24 = dimension_9bzkio24 && dimension_9bzkio24 != undefined  ? dimension_9bzkio24 : "";
let g_typography_p25eoi24 = typography_p25eoi24 && typography_p25eoi24 != undefined  ? typography_p25eoi24 : "";
let g_color_sd14kl24 = color_sd14kl24 && color_sd14kl24 != undefined  ? color_sd14kl24 : "";
let g_background_22dzru24 = background_22dzru24 && background_22dzru24 != undefined  ? background_22dzru24 : "";
let g_border_z9p0ji24 = border_z9p0ji24 && border_z9p0ji24 != undefined  ? border_z9p0ji24 : "";
let g_dimension_nt5fto24 = dimension_nt5fto24 && dimension_nt5fto24 != undefined  ? dimension_nt5fto24 : "";
let g_boxshadow_7fs8rj24 = boxshadow_7fs8rj24 && boxshadow_7fs8rj24 != undefined  ? boxshadow_7fs8rj24 : "";
let g_color_yy1dlm24 = color_yy1dlm24 && color_yy1dlm24 != undefined  ? color_yy1dlm24 : "";
let g_background_owycnd24 = background_owycnd24 && background_owycnd24 != undefined  ? background_owycnd24 : "";
let g_border_v0b6cq24 = border_v0b6cq24 && border_v0b6cq24 != undefined  ? border_v0b6cq24 : "";
let g_boxshadow_lsjokn24 = boxshadow_lsjokn24 && boxshadow_lsjokn24 != undefined  ? boxshadow_lsjokn24 : "";
let g_color_40dqfl25 = color_40dqfl25 && color_40dqfl25 != undefined  ? color_40dqfl25 : "";
let g_color_ma8l3625 = color_ma8l3625 && color_ma8l3625 != undefined  ? color_ma8l3625 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-q6s5kl25", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_q6s5kl25 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-rbtn-container">
    <div class="wkit-rbtn-inner">
        <a href="${g_url_ch78tn24_url}" class="wkit-rbtn" target="${g_url_ch78tn24_target}" rel="${g_url_ch78tn24_nofollow} noopener">${g_text_rzhtdf24}<span class="wkit-rainbow-btn-icon">${g_iconscontrol_nmhs8g24}</span></a>
    </div>
</div>`
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Rainbow_Button_q6s5kl25();