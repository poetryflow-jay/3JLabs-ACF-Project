
    class Gradient_Stroke_Text_ltsce624 {
        constructor() {
            this.Gradient_Stroke_Text_ltsce624_vi772525();
        }
    
        Gradient_Stroke_Text_ltsce624_vi772525() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Select,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Range,Pmgc_Typography,Pmgc_Background,Pmgc_Color,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-ltsce624', {
        title: __('Gradient Stroke Text'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-text-width tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Gradient'),__('Animated'),__('Stroke'),__('Text'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_jkxnvz23Function = (unit, type) => {
                var g_slider_jkxnvz23_list = [];
                g_slider_jkxnvz23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_jkxnvz23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_jkxnvz23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_jkxnvz23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_jkxnvz23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_jkxnvz23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_jkxnvz23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_q6ba9v23,
select_kg2gpc23,
select_4o5d2h23,
rawhtml_hultjj25,
choose_3tehqu23,
slider_jkxnvz23,
typography_9gk5vp23,
background_lfzu8j23,
background_wogx9y23,
color_tfmvtp23,
color_24l5lb23,
color_0m1wt023,
color_lgxnxq23,
textshadow_q8gik824,
textshadow_74zolz24,
normalhover_jmpyhm23,
rawhtml_hhkqux25,
color_90ky9123,
color_cohr5p23,
color_2rr6me23,
color_774bux23,
color_x7tag823,
color_xygt9b23,
color_7aaz9623,
color_m791k923,
color_btontx23,
color_nbwwtw23,
normalhover_oadun925,

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
                        main_function_ltsce624(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_ltsce624 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Text`),
                type: "text",
                value: text_q6ba9v23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_q6ba9v23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Type`),
                options:[['normal',__('Normal')],['animated',__('Animated')],],
                separator:"default",
                
                
                value: select_kg2gpc23,
                onChange: (value) => {setAttributes({ select_kg2gpc23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Gradient`),
                options:[['fill',__('Fill')],['stroke',__('Stroke')],],
                separator:"default",
                
                
                value: select_4o5d2h23,
                onChange: (value) => {setAttributes({ select_4o5d2h23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_hultjj25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/gradient-stroke-text-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_hultjj25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_3tehqu23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_3tehqu23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_jkxnvz23,
            
            min: slider_jkxnvz23 && slider_jkxnvz23.unit ? slider_jkxnvz23Function(slider_jkxnvz23.unit, 'min') : 0,
            max: slider_jkxnvz23 && slider_jkxnvz23.unit ? slider_jkxnvz23Function(slider_jkxnvz23.unit, 'max') : 100,
            step: slider_jkxnvz23 && slider_jkxnvz23.unit ? slider_jkxnvz23Function(slider_jkxnvz23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_jkxnvz23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_9gk5vp23,
            onChange: (value) => setAttributes({ typography_9gk5vp23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( select_kg2gpc23 == "normal" ) && React.createElement(Pmgc_Background, {
            
            value: background_lfzu8j23,
            sources: ["gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lfzu8j23: value }),
            }), 
( select_4o5d2h23 == "stroke" ) && React.createElement(Pmgc_Color, {
            label: __(`Fill Color`),
            value: color_tfmvtp23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_tfmvtp23: value }),
            }), 
( select_4o5d2h23 == "fill" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_24l5lb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_24l5lb23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_q8gik824,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_q8gik824: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( select_kg2gpc23 == "normal" ) && React.createElement(Pmgc_Background, {
            
            value: background_wogx9y23,
            sources: ["gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_wogx9y23: value }),
            }), 
( select_4o5d2h23 == "stroke" ) && React.createElement(Pmgc_Color, {
            label: __(`Fill Color`),
            value: color_0m1wt023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0m1wt023: value }),
            }), 
( select_4o5d2h23 == "fill" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_lgxnxq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_lgxnxq23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_74zolz24,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_74zolz24: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_hhkqux25,
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
Stroke color will work only on fill gradient.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_hhkqux25: value }),
            }), 
),( select_kg2gpc23 == "animated" ) && React.createElement(PanelBody, { title: __("Animated Colors"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 1`),
            value: color_90ky9123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_90ky9123: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 2`),
            value: color_cohr5p23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cohr5p23: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 3`),
            value: color_2rr6me23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_2rr6me23: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 4`),
            value: color_774bux23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_774bux23: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 5`),
            value: color_nbwwtw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_nbwwtw23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 1`),
            value: color_x7tag823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_x7tag823: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 2`),
            value: color_xygt9b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_xygt9b23: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 3`),
            value: color_7aaz9623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7aaz9623: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 4`),
            value: color_m791k923,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_m791k923: value }),
            }), 
( select_kg2gpc23 == "animated" ) && React.createElement(Pmgc_Color, {
            label: __(`Color 5`),
            value: color_btontx23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_btontx23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-ltsce624", block_id, false, props.clientId);
                }
            }

            
let g_text_q6ba9v23 = text_q6ba9v23 && text_q6ba9v23 != undefined  ? text_q6ba9v23 : "";
let g_select_kg2gpc23 = select_kg2gpc23 && select_kg2gpc23 != undefined  ? select_kg2gpc23 : "";
let g_select_4o5d2h23 = select_4o5d2h23 && select_4o5d2h23 != undefined  ? select_4o5d2h23 : "";
let g_rawhtml_hultjj25 = rawhtml_hultjj25 && rawhtml_hultjj25 != undefined  ? rawhtml_hultjj25 : "";
let g_choose_3tehqu23 = choose_3tehqu23 && choose_3tehqu23 != undefined  ? choose_3tehqu23 : "";
let g_typography_9gk5vp23 = typography_9gk5vp23 && typography_9gk5vp23 != undefined  ? typography_9gk5vp23 : "";
let g_background_lfzu8j23 = background_lfzu8j23 && background_lfzu8j23 != undefined && ( (select_kg2gpc23 == "normal") ) ? background_lfzu8j23 : "";
let g_background_wogx9y23 = background_wogx9y23 && background_wogx9y23 != undefined && ( (select_kg2gpc23 == "normal") ) ? background_wogx9y23 : "";
let g_color_tfmvtp23 = color_tfmvtp23 && color_tfmvtp23 != undefined && ( (select_4o5d2h23 == "stroke") ) ? color_tfmvtp23 : "";
let g_color_24l5lb23 = color_24l5lb23 && color_24l5lb23 != undefined && ( (select_4o5d2h23 == "fill") ) ? color_24l5lb23 : "";
let g_color_0m1wt023 = color_0m1wt023 && color_0m1wt023 != undefined && ( (select_4o5d2h23 == "stroke") ) ? color_0m1wt023 : "";
let g_color_lgxnxq23 = color_lgxnxq23 && color_lgxnxq23 != undefined && ( (select_4o5d2h23 == "fill") ) ? color_lgxnxq23 : "";
let g_textshadow_q8gik824 = textshadow_q8gik824 && textshadow_q8gik824 != undefined  ? textshadow_q8gik824 : "";
let g_textshadow_74zolz24 = textshadow_74zolz24 && textshadow_74zolz24 != undefined  ? textshadow_74zolz24 : "";
let g_rawhtml_hhkqux25 = rawhtml_hhkqux25 && rawhtml_hhkqux25 != undefined  ? rawhtml_hhkqux25 : "";
let g_color_90ky9123 = color_90ky9123 && color_90ky9123 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_90ky9123 : "";
let g_color_cohr5p23 = color_cohr5p23 && color_cohr5p23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_cohr5p23 : "";
let g_color_2rr6me23 = color_2rr6me23 && color_2rr6me23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_2rr6me23 : "";
let g_color_774bux23 = color_774bux23 && color_774bux23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_774bux23 : "";
let g_color_x7tag823 = color_x7tag823 && color_x7tag823 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_x7tag823 : "";
let g_color_xygt9b23 = color_xygt9b23 && color_xygt9b23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_xygt9b23 : "";
let g_color_7aaz9623 = color_7aaz9623 && color_7aaz9623 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_7aaz9623 : "";
let g_color_m791k923 = color_m791k923 && color_m791k923 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_m791k923 : "";
let g_color_btontx23 = color_btontx23 && color_btontx23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_btontx23 : "";
let g_color_nbwwtw23 = color_nbwwtw23 && color_nbwwtw23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_nbwwtw23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ltsce624 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-grad-stroke-text text-gradient-${g_select_kg2gpc23} grad-as-${g_select_4o5d2h23}">
    <div class="grad-stroke-text-inner">
        <span class="grad-stroke-text">${g_text_q6ba9v23}</span>
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
            text_q6ba9v23,
select_kg2gpc23,
select_4o5d2h23,
rawhtml_hultjj25,
choose_3tehqu23,
slider_jkxnvz23,
typography_9gk5vp23,
background_lfzu8j23,
background_wogx9y23,
color_tfmvtp23,
color_24l5lb23,
color_0m1wt023,
color_lgxnxq23,
textshadow_q8gik824,
textshadow_74zolz24,
normalhover_jmpyhm23,
rawhtml_hhkqux25,
color_90ky9123,
color_cohr5p23,
color_2rr6me23,
color_774bux23,
color_x7tag823,
color_xygt9b23,
color_7aaz9623,
color_m791k923,
color_btontx23,
color_nbwwtw23,
normalhover_oadun925,

            block_id,
        } = attributes;

        

        

        

        
let g_text_q6ba9v23 = text_q6ba9v23 && text_q6ba9v23 != undefined  ? text_q6ba9v23 : "";
let g_select_kg2gpc23 = select_kg2gpc23 && select_kg2gpc23 != undefined  ? select_kg2gpc23 : "";
let g_select_4o5d2h23 = select_4o5d2h23 && select_4o5d2h23 != undefined  ? select_4o5d2h23 : "";
let g_rawhtml_hultjj25 = rawhtml_hultjj25 && rawhtml_hultjj25 != undefined  ? rawhtml_hultjj25 : "";
let g_choose_3tehqu23 = choose_3tehqu23 && choose_3tehqu23 != undefined  ? choose_3tehqu23 : "";
let g_typography_9gk5vp23 = typography_9gk5vp23 && typography_9gk5vp23 != undefined  ? typography_9gk5vp23 : "";
let g_background_lfzu8j23 = background_lfzu8j23 && background_lfzu8j23 != undefined && ( (select_kg2gpc23 == "normal") ) ? background_lfzu8j23 : "";
let g_background_wogx9y23 = background_wogx9y23 && background_wogx9y23 != undefined && ( (select_kg2gpc23 == "normal") ) ? background_wogx9y23 : "";
let g_color_tfmvtp23 = color_tfmvtp23 && color_tfmvtp23 != undefined && ( (select_4o5d2h23 == "stroke") ) ? color_tfmvtp23 : "";
let g_color_24l5lb23 = color_24l5lb23 && color_24l5lb23 != undefined && ( (select_4o5d2h23 == "fill") ) ? color_24l5lb23 : "";
let g_color_0m1wt023 = color_0m1wt023 && color_0m1wt023 != undefined && ( (select_4o5d2h23 == "stroke") ) ? color_0m1wt023 : "";
let g_color_lgxnxq23 = color_lgxnxq23 && color_lgxnxq23 != undefined && ( (select_4o5d2h23 == "fill") ) ? color_lgxnxq23 : "";
let g_textshadow_q8gik824 = textshadow_q8gik824 && textshadow_q8gik824 != undefined  ? textshadow_q8gik824 : "";
let g_textshadow_74zolz24 = textshadow_74zolz24 && textshadow_74zolz24 != undefined  ? textshadow_74zolz24 : "";
let g_rawhtml_hhkqux25 = rawhtml_hhkqux25 && rawhtml_hhkqux25 != undefined  ? rawhtml_hhkqux25 : "";
let g_color_90ky9123 = color_90ky9123 && color_90ky9123 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_90ky9123 : "";
let g_color_cohr5p23 = color_cohr5p23 && color_cohr5p23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_cohr5p23 : "";
let g_color_2rr6me23 = color_2rr6me23 && color_2rr6me23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_2rr6me23 : "";
let g_color_774bux23 = color_774bux23 && color_774bux23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_774bux23 : "";
let g_color_x7tag823 = color_x7tag823 && color_x7tag823 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_x7tag823 : "";
let g_color_xygt9b23 = color_xygt9b23 && color_xygt9b23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_xygt9b23 : "";
let g_color_7aaz9623 = color_7aaz9623 && color_7aaz9623 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_7aaz9623 : "";
let g_color_m791k923 = color_m791k923 && color_m791k923 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_m791k923 : "";
let g_color_btontx23 = color_btontx23 && color_btontx23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_btontx23 : "";
let g_color_nbwwtw23 = color_nbwwtw23 && color_nbwwtw23 != undefined && ( (select_kg2gpc23 == "animated") ) ? color_nbwwtw23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-ltsce624", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ltsce624 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-grad-stroke-text text-gradient-${g_select_kg2gpc23} grad-as-${g_select_4o5d2h23}">
    <div class="grad-stroke-text-inner">
        <span class="grad-stroke-text">${g_text_q6ba9v23}</span>
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
    new Gradient_Stroke_Text_ltsce624();