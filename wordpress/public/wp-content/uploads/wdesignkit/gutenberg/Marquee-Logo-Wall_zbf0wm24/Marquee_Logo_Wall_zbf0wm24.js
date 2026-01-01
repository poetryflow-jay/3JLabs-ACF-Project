
    class Marquee_Logo_Wall_zbf0wm24 {
        constructor() {
            this.Marquee_Logo_Wall_zbf0wm24_1smthu25();
        }
    
        Marquee_Logo_Wall_zbf0wm24_1smthu25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_IconList,Pmgc_Media,Pmgc_Url,Pmgc_Label_Heading,Pmgc_Color,Pmgc_Background,Pmgc_Repeater,Pmgc_Toggle,Pmgc_Note,Pmgc_Typography,Pmgc_Tabs,Pmgc_Range,Pmgc_Border,Pmgc_Dimension,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-zbf0wm24', {
        title: __('Marquee Logo Wall'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-grip-horizontal tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Marquee Logo'),__('Logo Slider'),__('Logo Showcase'),__('Logo Marquee'),__('Dynamic Logo Wall'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_coqhac24Function = (unit, type) => {
                var g_slider_coqhac24_list = [];
                g_slider_coqhac24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_coqhac24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_coqhac24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_coqhac24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_coqhac24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_coqhac24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_coqhac24_list[unit][type];
            };
const slider_fqfod723Function = (unit, type) => {
                var g_slider_fqfod723_list = [];
                g_slider_fqfod723_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_fqfod723_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_fqfod723_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_fqfod723_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_fqfod723_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_fqfod723_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_fqfod723_list[unit][type];
            };
const slider_4iex6x25Function = (unit, type) => {
                var g_slider_4iex6x25_list = [];
                g_slider_4iex6x25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_4iex6x25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_4iex6x25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_4iex6x25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_4iex6x25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_4iex6x25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_4iex6x25_list[unit][type];
            };
const slider_hhss1p24Function = (unit, type) => {
                var g_slider_hhss1p24_list = [];
                g_slider_hhss1p24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_hhss1p24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_hhss1p24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_hhss1p24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_hhss1p24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_hhss1p24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_hhss1p24_list[unit][type];
            };
const slider_l6ijd623Function = (unit, type) => {
                var g_slider_l6ijd623_list = [];
                g_slider_l6ijd623_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_l6ijd623_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_l6ijd623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_l6ijd623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_l6ijd623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_l6ijd623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_l6ijd623_list[unit][type];
            };
const slider_ych76024Function = (unit, type) => {
                var g_slider_ych76024_list = [];
                g_slider_ych76024_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_ych76024_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ych76024_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ych76024_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ych76024_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ych76024_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ych76024_list[unit][type];
            };
const slider_vt06g823Function = (unit, type) => {
                var g_slider_vt06g823_list = [];
                g_slider_vt06g823_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 10 };
g_slider_vt06g823_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_vt06g823_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_vt06g823_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_vt06g823_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_vt06g823_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_vt06g823_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_ow3fri23,
repeater_s5r6ra23,
switcher_diik1b23,
number_lgdjdh24,
rawhtml_p9udv325,
typography_alb1fr24,
color_8ksqga23,
color_nrpsjn23,
normalhover_26ktfz23,
rawhtml_44a06j25,
slider_coqhac24,
color_rqb89w24,
color_rfh6cw24,
normalhover_4lfmne24,
rawhtml_4vdjzi25,
slider_fqfod723,
slider_4iex6x25,
slider_hhss1p24,
slider_l6ijd623,
slider_ych76024,
background_hts4b323,
border_gq9ti623,
dimension_1jngxw23,
boxshadow_kfql9o23,
background_b5ehh023,
border_hvcmip23,
boxshadow_whvpqu23,
normalhover_ru2or823,
rawhtml_9cbjwy25,
slider_vt06g823,

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
                        main_function_zbf0wm24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_zbf0wm24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let mainWrap = $scope[0].querySelector('.wkit-marquee-main-wrap');
let getSpeed = (mainWrap.getAttribute('data-speed')) ? mainWrap.getAttribute('data-speed') : 20;
let grpInner = mainWrap.querySelector('.wkit-marquee-inn-wrap');

grpInner.style.animationDuration = getSpeed + 's';
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['marq-hz',__('Style 1')],['marq-vl',__('Style 2')],],
                separator:"default",
                
                
                value: select_ow3fri23,
                onChange: (value) => {setAttributes({ select_ow3fri23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Logo`),
            value: repeater_s5r6ra23,
            attributeName: 'repeater_s5r6ra23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_s5r6ra23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Select, {
                label: __(`Select`),
                options:[['txt',__('Text')],['icon',__('Icon')],['image',__('Image')],],
                separator:"default",
                
                
                value: value.select_x3pc8523,
                onChange: v => { value.select_x3pc8523 = v; onChange(value); },
            }),
( value.select_x3pc8523 == "txt" ) && React.createElement(Pmgc_Text, {
                label: __(`Text`),
                type: "text",
                value: value.text_o31ruz24,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_o31ruz24 = v; onChange(value); },
            }),
( value.select_x3pc8523 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_5z9ltq23,
            separator:'default',
            onChange: v => { value.iconscontrol_5z9ltq23 = v; onChange(value); },
            }), 
( value.select_x3pc8523 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_wviw9223,
                
                
                type: ["image","svg"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_wviw9223 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: value.url_8i85qa23,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.url_8i85qa23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_gft40r23,
            separator:"before",
            inlineblock: true,
            }), 
( value.select_x3pc8523 == "txt" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: value.color_jl1b9o23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_jl1b9o23 = v; onChange(value); },
            }), 
( value.select_x3pc8523 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_fd1smn24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_fd1smn24 = v; onChange(value); },
            }), 
( value.select_x3pc8523 != "none" ) && React.createElement(Pmgc_Background, {
            
            value: value.background_uqmkr523,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_uqmkr523 = v; onChange(value); },
            }), 
( value.select_x3pc8523 != "none" ) && React.createElement(Pmgc_Color, {
            label: __(`Border Color`),
            value: value.color_p2hzs023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_p2hzs023 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_q0hnbe23,
            separator:"before",
            inlineblock: true,
            }), 
( value.select_x3pc8523 == "txt" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: value.color_c6gmps23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_c6gmps23 = v; onChange(value); },
            }), 
( value.select_x3pc8523 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_1lbyxh24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_1lbyxh24 = v; onChange(value); },
            }), 
( value.select_x3pc8523 != "none" ) && React.createElement(Pmgc_Background, {
            
            value: value.background_ow672r23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_ow672r23 = v; onChange(value); },
            }), 
( value.select_x3pc8523 != "none" ) && React.createElement(Pmgc_Color, {
            label: __(`Border Color`),
            value: value.color_iazgb123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_iazgb123 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Reverse Scroll`),
            
            value: switcher_diik1b23,
            
            onChange: (value) => setAttributes({ switcher_diik1b23: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Speed`),
                type: "number",
                value: number_lgdjdh24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_lgdjdh24: value }) },
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_p9udv325,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/marquee-logo-wall-2/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_p9udv325: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Text"), initialOpen: true },
 React.createElement(Pmgc_Typography, {
            
            value: typography_alb1fr24,
            onChange: (value) => setAttributes({ typography_alb1fr24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_8ksqga23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8ksqga23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_nrpsjn23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_nrpsjn23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_44a06j25,
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
If text color is applied in the main card, the color from here will not be applied.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_44a06j25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_coqhac24,
            
            min: slider_coqhac24 && slider_coqhac24.unit ? slider_coqhac24Function(slider_coqhac24.unit, 'min') : 0,
            max: slider_coqhac24 && slider_coqhac24.unit ? slider_coqhac24Function(slider_coqhac24.unit, 'max') : 100,
            step: slider_coqhac24 && slider_coqhac24.unit ? slider_coqhac24Function(slider_coqhac24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_coqhac24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_rqb89w24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rqb89w24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_rfh6cw24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rfh6cw24: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_4vdjzi25,
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
If color is applied in the main card, the color from here will not be applied.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_4vdjzi25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_fqfod723,
            
            min: slider_fqfod723 && slider_fqfod723.unit ? slider_fqfod723Function(slider_fqfod723.unit, 'min') : 0,
            max: slider_fqfod723 && slider_fqfod723.unit ? slider_fqfod723Function(slider_fqfod723.unit, 'max') : 100,
            step: slider_fqfod723 && slider_fqfod723.unit ? slider_fqfod723Function(slider_fqfod723.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_fqfod723: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_4iex6x25,
            
            min: slider_4iex6x25 && slider_4iex6x25.unit ? slider_4iex6x25Function(slider_4iex6x25.unit, 'min') : 0,
            max: slider_4iex6x25 && slider_4iex6x25.unit ? slider_4iex6x25Function(slider_4iex6x25.unit, 'max') : 100,
            step: slider_4iex6x25 && slider_4iex6x25.unit ? slider_4iex6x25Function(slider_4iex6x25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_4iex6x25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Items"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_hhss1p24,
            
            min: slider_hhss1p24 && slider_hhss1p24.unit ? slider_hhss1p24Function(slider_hhss1p24.unit, 'min') : 0,
            max: slider_hhss1p24 && slider_hhss1p24.unit ? slider_hhss1p24Function(slider_hhss1p24.unit, 'max') : 100,
            step: slider_hhss1p24 && slider_hhss1p24.unit ? slider_hhss1p24Function(slider_hhss1p24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_hhss1p24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_l6ijd623,
            
            min: slider_l6ijd623 && slider_l6ijd623.unit ? slider_l6ijd623Function(slider_l6ijd623.unit, 'min') : 0,
            max: slider_l6ijd623 && slider_l6ijd623.unit ? slider_l6ijd623Function(slider_l6ijd623.unit, 'max') : 100,
            step: slider_l6ijd623 && slider_l6ijd623.unit ? slider_l6ijd623Function(slider_l6ijd623.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_l6ijd623: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_ych76024,
            
            min: slider_ych76024 && slider_ych76024.unit ? slider_ych76024Function(slider_ych76024.unit, 'min') : 0,
            max: slider_ych76024 && slider_ych76024.unit ? slider_ych76024Function(slider_ych76024.unit, 'max') : 100,
            step: slider_ych76024 && slider_ych76024.unit ? slider_ych76024Function(slider_ych76024.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ych76024: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_hts4b323,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_hts4b323: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_gq9ti623,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_gq9ti623: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_1jngxw23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_1jngxw23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_kfql9o23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_kfql9o23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_b5ehh023,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_b5ehh023: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_hvcmip23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_hvcmip23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_whvpqu23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_whvpqu23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_9cbjwy25,
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
If item-related properties are applied in the main card, they will not be applied from here.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_9cbjwy25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Section Height`),
            separator:'default',
            value: slider_vt06g823,
            
            min: slider_vt06g823 && slider_vt06g823.unit ? slider_vt06g823Function(slider_vt06g823.unit, 'min') : 0,
            max: slider_vt06g823 && slider_vt06g823.unit ? slider_vt06g823Function(slider_vt06g823.unit, 'max') : 100,
            step: slider_vt06g823 && slider_vt06g823.unit ? slider_vt06g823Function(slider_vt06g823.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_vt06g823: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-zbf0wm24", block_id, false, props.clientId);
                }
            }

            
let g_select_ow3fri23 = select_ow3fri23 && select_ow3fri23 != undefined  ? select_ow3fri23 : "";
let g_switcher_diik1b23 = switcher_diik1b23 && switcher_diik1b23 != undefined  ? 'yes' : "";
let g_number_lgdjdh24 = number_lgdjdh24 && number_lgdjdh24 != undefined  ? number_lgdjdh24 : "";
let g_rawhtml_p9udv325 = rawhtml_p9udv325 && rawhtml_p9udv325 != undefined  ? rawhtml_p9udv325 : "";
let g_typography_alb1fr24 = typography_alb1fr24 && typography_alb1fr24 != undefined  ? typography_alb1fr24 : "";
let g_color_8ksqga23 = color_8ksqga23 && color_8ksqga23 != undefined  ? color_8ksqga23 : "";
let g_color_nrpsjn23 = color_nrpsjn23 && color_nrpsjn23 != undefined  ? color_nrpsjn23 : "";
let g_rawhtml_44a06j25 = rawhtml_44a06j25 && rawhtml_44a06j25 != undefined  ? rawhtml_44a06j25 : "";
let g_color_rqb89w24 = color_rqb89w24 && color_rqb89w24 != undefined  ? color_rqb89w24 : "";
let g_color_rfh6cw24 = color_rfh6cw24 && color_rfh6cw24 != undefined  ? color_rfh6cw24 : "";
let g_rawhtml_4vdjzi25 = rawhtml_4vdjzi25 && rawhtml_4vdjzi25 != undefined  ? rawhtml_4vdjzi25 : "";
let g_background_hts4b323 = background_hts4b323 && background_hts4b323 != undefined  ? background_hts4b323 : "";
let g_border_gq9ti623 = border_gq9ti623 && border_gq9ti623 != undefined  ? border_gq9ti623 : "";
let g_dimension_1jngxw23 = dimension_1jngxw23 && dimension_1jngxw23 != undefined  ? dimension_1jngxw23 : "";
let g_boxshadow_kfql9o23 = boxshadow_kfql9o23 && boxshadow_kfql9o23 != undefined  ? boxshadow_kfql9o23 : "";
let g_background_b5ehh023 = background_b5ehh023 && background_b5ehh023 != undefined  ? background_b5ehh023 : "";
let g_border_hvcmip23 = border_hvcmip23 && border_hvcmip23 != undefined  ? border_hvcmip23 : "";
let g_boxshadow_whvpqu23 = boxshadow_whvpqu23 && boxshadow_whvpqu23 != undefined  ? boxshadow_whvpqu23 : "";
let g_rawhtml_9cbjwy25 = rawhtml_9cbjwy25 && rawhtml_9cbjwy25 != undefined  ? rawhtml_9cbjwy25 : "";
            
let repeater_s5r6ra23_at25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_at25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 } 
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_3u25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_3u25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_ow25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_ow25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_nt25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_nt25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_je25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_je25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_vn25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_vn25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_fu25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_fu25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_am25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_am25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_ba25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_ba25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_4f25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_4f25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_tv25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_tv25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_t825 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_t825 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_zbf0wm24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-marquee-main-wrap" data-speed="${g_number_lgdjdh24}">
    <div class="wkit-marquee-inn-wrap wkit-${g_select_ow3fri23} wkit-marquee-reverse-${g_switcher_diik1b23}">
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_at25}
        </div>
        <div class="wkit-marquee-group"> 
            ${repeater_s5r6ra23_3u25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_ow25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_nt25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_je25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_vn25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_fu25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_am25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_ba25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_4f25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_tv25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_t825}
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
            select_ow3fri23,
repeater_s5r6ra23,
switcher_diik1b23,
number_lgdjdh24,
rawhtml_p9udv325,
typography_alb1fr24,
color_8ksqga23,
color_nrpsjn23,
normalhover_26ktfz23,
rawhtml_44a06j25,
slider_coqhac24,
color_rqb89w24,
color_rfh6cw24,
normalhover_4lfmne24,
rawhtml_4vdjzi25,
slider_fqfod723,
slider_4iex6x25,
slider_hhss1p24,
slider_l6ijd623,
slider_ych76024,
background_hts4b323,
border_gq9ti623,
dimension_1jngxw23,
boxshadow_kfql9o23,
background_b5ehh023,
border_hvcmip23,
boxshadow_whvpqu23,
normalhover_ru2or823,
rawhtml_9cbjwy25,
slider_vt06g823,

            block_id,
        } = attributes;

        

        

        

        
let g_select_ow3fri23 = select_ow3fri23 && select_ow3fri23 != undefined  ? select_ow3fri23 : "";
let g_switcher_diik1b23 = switcher_diik1b23 && switcher_diik1b23 != undefined  ? 'yes' : "";
let g_number_lgdjdh24 = number_lgdjdh24 && number_lgdjdh24 != undefined  ? number_lgdjdh24 : "";
let g_rawhtml_p9udv325 = rawhtml_p9udv325 && rawhtml_p9udv325 != undefined  ? rawhtml_p9udv325 : "";
let g_typography_alb1fr24 = typography_alb1fr24 && typography_alb1fr24 != undefined  ? typography_alb1fr24 : "";
let g_color_8ksqga23 = color_8ksqga23 && color_8ksqga23 != undefined  ? color_8ksqga23 : "";
let g_color_nrpsjn23 = color_nrpsjn23 && color_nrpsjn23 != undefined  ? color_nrpsjn23 : "";
let g_rawhtml_44a06j25 = rawhtml_44a06j25 && rawhtml_44a06j25 != undefined  ? rawhtml_44a06j25 : "";
let g_color_rqb89w24 = color_rqb89w24 && color_rqb89w24 != undefined  ? color_rqb89w24 : "";
let g_color_rfh6cw24 = color_rfh6cw24 && color_rfh6cw24 != undefined  ? color_rfh6cw24 : "";
let g_rawhtml_4vdjzi25 = rawhtml_4vdjzi25 && rawhtml_4vdjzi25 != undefined  ? rawhtml_4vdjzi25 : "";
let g_background_hts4b323 = background_hts4b323 && background_hts4b323 != undefined  ? background_hts4b323 : "";
let g_border_gq9ti623 = border_gq9ti623 && border_gq9ti623 != undefined  ? border_gq9ti623 : "";
let g_dimension_1jngxw23 = dimension_1jngxw23 && dimension_1jngxw23 != undefined  ? dimension_1jngxw23 : "";
let g_boxshadow_kfql9o23 = boxshadow_kfql9o23 && boxshadow_kfql9o23 != undefined  ? boxshadow_kfql9o23 : "";
let g_background_b5ehh023 = background_b5ehh023 && background_b5ehh023 != undefined  ? background_b5ehh023 : "";
let g_border_hvcmip23 = border_hvcmip23 && border_hvcmip23 != undefined  ? border_hvcmip23 : "";
let g_boxshadow_whvpqu23 = boxshadow_whvpqu23 && boxshadow_whvpqu23 != undefined  ? boxshadow_whvpqu23 : "";
let g_rawhtml_9cbjwy25 = rawhtml_9cbjwy25 && rawhtml_9cbjwy25 != undefined  ? rawhtml_9cbjwy25 : "";
        
let repeater_s5r6ra23_at25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_at25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 } 
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_3u25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_3u25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_ow25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_ow25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_nt25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_nt25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_je25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_je25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_vn25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_vn25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_fu25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_fu25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_am25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_am25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_ba25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_ba25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_4f25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_4f25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_tv25 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_tv25 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })
let repeater_s5r6ra23_t825 = "";
                            
repeater_s5r6ra23  && repeater_s5r6ra23.map((r_item, index) => {
                                
let grnp_select_x3pc8523 = r_item.select_x3pc8523  ? r_item.select_x3pc8523 : "";
let grnp_text_o31ruz24 = r_item.text_o31ruz24 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.text_o31ruz24 : "";
let grnp_iconscontrol_5z9ltq23 = r_item?.iconscontrol_5z9ltq23 != undefined && ( (r_item?.select_x3pc8523 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_5z9ltq23+'"></i></span>' : '';

let grnp_media_wviw9223 = r_item?.media_wviw9223?.url != undefined && ( (r_item?.select_x3pc8523 == "image") ) ? r_item?.media_wviw9223.url : "";
let grnp_url_8i85qa23_url = r_item?.url_8i85qa23?.url && r_item?.url_8i85qa23?.url != undefined ?  r_item?.url_8i85qa23.url : "";
let grnp_url_8i85qa23_target = r_item?.url_8i85qa23?.target && r_item?.url_8i85qa23?.target != undefined ?  r_item?.url_8i85qa23.target : "";
let grnp_url_8i85qa23_nofollow = r_item?.url_8i85qa23?.nofollow && r_item?.url_8i85qa23?.nofollow != undefined ?  r_item?.url_8i85qa23.nofollow : "";
let grnp_url_8i85qa23_ctmArt = r_item?.url_8i85qa23?.attr && r_item?.url_8i85qa23?.attr != undefined ?  r_item?.url_8i85qa23.attr : "";
                    let grnp_url_8i85qa23_attr = ''

                    if (grnp_url_8i85qa23_ctmArt) {
                        let main_array = grnp_url_8i85qa23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_8i85qa23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_gft40r23 = r_item.heading_gft40r23  ? r_item.heading_gft40r23 : "";
let grnp_color_jl1b9o23 = r_item.color_jl1b9o23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_jl1b9o23 : "";
let grnp_color_fd1smn24 = r_item.color_fd1smn24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_fd1smn24 : "";
let grnp_background_uqmkr523 = r_item.background_uqmkr523 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_uqmkr523 : "";
let grnp_color_p2hzs023 = r_item.color_p2hzs023 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_p2hzs023 : "";
let grnp_heading_q0hnbe23 = r_item.heading_q0hnbe23  ? r_item.heading_q0hnbe23 : "";
let grnp_color_c6gmps23 = r_item.color_c6gmps23 && ( (r_item?.select_x3pc8523 == "txt") ) ? r_item.color_c6gmps23 : "";
let grnp_color_1lbyxh24 = r_item.color_1lbyxh24 && ( (r_item?.select_x3pc8523 == "icon") ) ? r_item.color_1lbyxh24 : "";
let grnp_background_ow672r23 = r_item.background_ow672r23 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.background_ow672r23 : "";
let grnp_color_iazgb123 = r_item.color_iazgb123 && ( (r_item?.select_x3pc8523 != "none") ) ? r_item.color_iazgb123 : "";
                                repeater_s5r6ra23_t825 += `<a class="tp-repeater-item-${r_item._key} wkit-marquee-item wkit-marquee-icon-effect-${grnp_select_x3pc8523}" href="${grnp_url_8i85qa23_url}" target="${grnp_url_8i85qa23_target}" rel="${grnp_url_8i85qa23_nofollow} noopener" data-repeater_s5r6ra23="{repeater_s5r6ra23}">
              ${grnp_iconscontrol_5z9ltq23 }
               <img src="${grnp_media_wviw9223}" class="tp-title-icon">
               <span class="wkit-marquee-icon-effect-${grnp_select_x3pc8523}">${grnp_text_o31ruz24}</span>
            </a>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-zbf0wm24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_zbf0wm24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-marquee-main-wrap" data-speed="${g_number_lgdjdh24}">
    <div class="wkit-marquee-inn-wrap wkit-${g_select_ow3fri23} wkit-marquee-reverse-${g_switcher_diik1b23}">
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_at25}
        </div>
        <div class="wkit-marquee-group"> 
            ${repeater_s5r6ra23_3u25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_ow25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_nt25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_je25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_vn25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_fu25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_am25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_ba25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_4f25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_tv25}
        </div>
        <div class="wkit-marquee-group">
            ${repeater_s5r6ra23_t825}
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
    new Marquee_Logo_Wall_zbf0wm24();