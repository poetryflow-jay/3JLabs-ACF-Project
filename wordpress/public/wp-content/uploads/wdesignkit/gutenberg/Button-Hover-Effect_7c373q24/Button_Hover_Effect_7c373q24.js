
    class Button_Hover_Effect_7c373q24 {
        constructor() {
            this.Button_Hover_Effect_7c373q24_ipqppg25();
        }
    
        Button_Hover_Effect_7c373q24_ipqppg25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_IconList,Pmgc_Url,Pmgc_Toggle,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-7c373q24', {
        title: __('Button Hover Effect'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-hand-pointer tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Button Hover Effects'),__('Interactive Buttons'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_0tox5e25Function = (unit, type) => {
                var g_slider_0tox5e25_list = [];
                g_slider_0tox5e25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_0tox5e25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_0tox5e25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_0tox5e25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_0tox5e25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_0tox5e25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_0tox5e25_list[unit][type];
            };
const slider_hiu54w25Function = (unit, type) => {
                var g_slider_hiu54w25_list = [];
                g_slider_hiu54w25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_hiu54w25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_hiu54w25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_hiu54w25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_hiu54w25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_hiu54w25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_hiu54w25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_b4b4v923,
text_r41uaa23,
iconscontrol_3dtya523,
url_5lwf3f24,
select_3081sf23,
switcher_log9ol25,
switcher_bpu64g25,
choose_e2qcqg24,
rawhtml_ov3cx925,
dimension_d066om23,
typography_272b2923,
color_dvvn0y23,
color_ut160v23,
background_9hyvsf23,
background_k470jq23,
border_7ovaie23,
dimension_5l8cq223,
border_0hahn623,
boxshadow_5r75hr23,
boxshadow_cr8hb323,
normalhover_si9ygc23,
slider_0tox5e25,
slider_hiu54w25,
color_38lybz23,
color_e5m6p623,
normalhover_ad0sl625,

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
                        main_function_7c373q24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_7c373q24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                $scope[0].querySelectorAll('.wkit-button-effect .btn-hover-txt').forEach(button => {
    button.innerHTML = '<div><span>' + button.textContent.trim().split('').join('</span><span>') + '</span></div>';
});

// Define device check functions
const isMobile = window.innerWidth <= 767;
const isTablet = window.innerWidth > 767 && window.innerWidth <= 1024;

$scope[0].querySelectorAll('.wkit-button-effect').forEach(buttonWrap => {
    buttonWrap.addEventListener('click', (e) => {
        const parent = buttonWrap.closest('.wkit-button-h-effect');

        

        const disableForMobile = isMobile && !parent.classList.contains('disable-mobile-link');
        const disableForTablet = isTablet && !parent.classList.contains('disable-tablet-link');

        if (disableForMobile || disableForTablet) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['jumping',__('Jumping')],['smoke',__('Smoke')],['drive',__('Drive')],],
                separator:"default",
                
                
                value: select_b4b4v923,
                onChange: (value) => {setAttributes({ select_b4b4v923: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_r41uaa23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_r41uaa23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_3dtya523,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_3dtya523: value }),
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_5lwf3f24,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_5lwf3f24: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Icon Position`),
                options:[['after-text',__('After')],['before-text',__('Before')],],
                separator:"default",
                
                
                value: select_3081sf23,
                onChange: (value) => {setAttributes({ select_3081sf23: value }) },
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_log9ol25,
            
            onChange: (value) => setAttributes({ switcher_log9ol25: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_bpu64g25,
            
            onChange: (value) => setAttributes({ switcher_bpu64g25: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_e2qcqg24,
            
            
            onChange: (value) => setAttributes({ choose_e2qcqg24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ov3cx925,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/button-hover-effect-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_ov3cx925: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Button"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_d066om23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_d066om23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_272b2923,
            onChange: (value) => setAttributes({ typography_272b2923: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_dvvn0y23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_dvvn0y23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_9hyvsf23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_9hyvsf23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_7ovaie23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_7ovaie23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_5l8cq223,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5l8cq223: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_5r75hr23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_5r75hr23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ut160v23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ut160v23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_k470jq23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_k470jq23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_0hahn623,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_0hahn623: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cr8hb323,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cr8hb323: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_0tox5e25,
            
            min: slider_0tox5e25 && slider_0tox5e25.unit ? slider_0tox5e25Function(slider_0tox5e25.unit, 'min') : 0,
            max: slider_0tox5e25 && slider_0tox5e25.unit ? slider_0tox5e25Function(slider_0tox5e25.unit, 'max') : 100,
            step: slider_0tox5e25 && slider_0tox5e25.unit ? slider_0tox5e25Function(slider_0tox5e25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_0tox5e25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_hiu54w25,
            
            min: slider_hiu54w25 && slider_hiu54w25.unit ? slider_hiu54w25Function(slider_hiu54w25.unit, 'min') : 0,
            max: slider_hiu54w25 && slider_hiu54w25.unit ? slider_hiu54w25Function(slider_hiu54w25.unit, 'max') : 100,
            step: slider_hiu54w25 && slider_hiu54w25.unit ? slider_hiu54w25Function(slider_hiu54w25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_hiu54w25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( iconscontrol_3dtya523 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_38lybz23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_38lybz23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( iconscontrol_3dtya523 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_e5m6p623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_e5m6p623: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-7c373q24", block_id, false, props.clientId);
                }
            }

            
let g_select_b4b4v923 = select_b4b4v923 && select_b4b4v923 != undefined  ? select_b4b4v923 : "";
let g_text_r41uaa23 = text_r41uaa23 && text_r41uaa23 != undefined  ? text_r41uaa23 : "";
let g_iconscontrol_3dtya523 = iconscontrol_3dtya523 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_3dtya523+'"></i></span>' : '';

let g_url_5lwf3f24_url = url_5lwf3f24?.url && url_5lwf3f24?.url != undefined ? url_5lwf3f24.url : "";
let g_url_5lwf3f24_target = url_5lwf3f24?.target && url_5lwf3f24?.target != undefined ? url_5lwf3f24.target : "";
let g_url_5lwf3f24_nofollow = url_5lwf3f24?.nofollow && url_5lwf3f24?.nofollow != undefined ? url_5lwf3f24.nofollow : "";
let g_url_5lwf3f24_ctmArt = url_5lwf3f24?.attr != undefined ? url_5lwf3f24.attr : "";
                    let g_url_5lwf3f24_attr = ''

                    if (g_url_5lwf3f24_ctmArt) {
                        let main_array = g_url_5lwf3f24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_5lwf3f24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_select_3081sf23 = select_3081sf23 && select_3081sf23 != undefined  ? select_3081sf23 : "";
let g_switcher_log9ol25 = switcher_log9ol25 && switcher_log9ol25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_bpu64g25 = switcher_bpu64g25 && switcher_bpu64g25 != undefined  ? 'disable-mobile-link' : "";
let g_choose_e2qcqg24 = choose_e2qcqg24 && choose_e2qcqg24 != undefined  ? choose_e2qcqg24 : "";
let g_rawhtml_ov3cx925 = rawhtml_ov3cx925 && rawhtml_ov3cx925 != undefined  ? rawhtml_ov3cx925 : "";
let g_dimension_d066om23 = dimension_d066om23 && dimension_d066om23 != undefined  ? dimension_d066om23 : "";
let g_typography_272b2923 = typography_272b2923 && typography_272b2923 != undefined  ? typography_272b2923 : "";
let g_color_dvvn0y23 = color_dvvn0y23 && color_dvvn0y23 != undefined  ? color_dvvn0y23 : "";
let g_color_ut160v23 = color_ut160v23 && color_ut160v23 != undefined  ? color_ut160v23 : "";
let g_background_9hyvsf23 = background_9hyvsf23 && background_9hyvsf23 != undefined  ? background_9hyvsf23 : "";
let g_background_k470jq23 = background_k470jq23 && background_k470jq23 != undefined  ? background_k470jq23 : "";
let g_border_7ovaie23 = border_7ovaie23 && border_7ovaie23 != undefined  ? border_7ovaie23 : "";
let g_dimension_5l8cq223 = dimension_5l8cq223 && dimension_5l8cq223 != undefined  ? dimension_5l8cq223 : "";
let g_border_0hahn623 = border_0hahn623 && border_0hahn623 != undefined  ? border_0hahn623 : "";
let g_boxshadow_5r75hr23 = boxshadow_5r75hr23 && boxshadow_5r75hr23 != undefined  ? boxshadow_5r75hr23 : "";
let g_boxshadow_cr8hb323 = boxshadow_cr8hb323 && boxshadow_cr8hb323 != undefined  ? boxshadow_cr8hb323 : "";
let g_color_38lybz23 = color_38lybz23 && color_38lybz23 != undefined && ( (iconscontrol_3dtya523 != "") ) ? color_38lybz23 : "";
let g_color_e5m6p623 = color_e5m6p623 && color_e5m6p623 != undefined && ( (iconscontrol_3dtya523 != "") ) ? color_e5m6p623 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7c373q24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-button-h-effect ${g_select_3081sf23} ${g_switcher_log9ol25} ${g_switcher_bpu64g25}">
     <a href="${g_url_5lwf3f24_url}" target="${g_url_5lwf3f24_target}" rel="${g_url_5lwf3f24_nofollow} noopener" class="wkit-button-effect ${g_select_b4b4v923}"><span class="btn-hover-txt">${g_text_r41uaa23}</span>${g_iconscontrol_3dtya523}</a> 
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
            select_b4b4v923,
text_r41uaa23,
iconscontrol_3dtya523,
url_5lwf3f24,
select_3081sf23,
switcher_log9ol25,
switcher_bpu64g25,
choose_e2qcqg24,
rawhtml_ov3cx925,
dimension_d066om23,
typography_272b2923,
color_dvvn0y23,
color_ut160v23,
background_9hyvsf23,
background_k470jq23,
border_7ovaie23,
dimension_5l8cq223,
border_0hahn623,
boxshadow_5r75hr23,
boxshadow_cr8hb323,
normalhover_si9ygc23,
slider_0tox5e25,
slider_hiu54w25,
color_38lybz23,
color_e5m6p623,
normalhover_ad0sl625,

            block_id,
        } = attributes;

        

        

        

        
let g_select_b4b4v923 = select_b4b4v923 && select_b4b4v923 != undefined  ? select_b4b4v923 : "";
let g_text_r41uaa23 = text_r41uaa23 && text_r41uaa23 != undefined  ? text_r41uaa23 : "";
let g_iconscontrol_3dtya523 = iconscontrol_3dtya523 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_3dtya523+'"></i></span>' : '';

let g_url_5lwf3f24_url = url_5lwf3f24?.url && url_5lwf3f24?.url != undefined ? url_5lwf3f24.url : "";
let g_url_5lwf3f24_target = url_5lwf3f24?.target && url_5lwf3f24?.target != undefined ? url_5lwf3f24.target : "";
let g_url_5lwf3f24_nofollow = url_5lwf3f24?.nofollow && url_5lwf3f24?.nofollow != undefined ? url_5lwf3f24.nofollow : "";
let g_url_5lwf3f24_ctmArt = url_5lwf3f24?.attr != undefined ? url_5lwf3f24.attr : "";
                    let g_url_5lwf3f24_attr = ''

                    if (g_url_5lwf3f24_ctmArt) {
                        let main_array = g_url_5lwf3f24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_5lwf3f24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_select_3081sf23 = select_3081sf23 && select_3081sf23 != undefined  ? select_3081sf23 : "";
let g_switcher_log9ol25 = switcher_log9ol25 && switcher_log9ol25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_bpu64g25 = switcher_bpu64g25 && switcher_bpu64g25 != undefined  ? 'disable-mobile-link' : "";
let g_choose_e2qcqg24 = choose_e2qcqg24 && choose_e2qcqg24 != undefined  ? choose_e2qcqg24 : "";
let g_rawhtml_ov3cx925 = rawhtml_ov3cx925 && rawhtml_ov3cx925 != undefined  ? rawhtml_ov3cx925 : "";
let g_dimension_d066om23 = dimension_d066om23 && dimension_d066om23 != undefined  ? dimension_d066om23 : "";
let g_typography_272b2923 = typography_272b2923 && typography_272b2923 != undefined  ? typography_272b2923 : "";
let g_color_dvvn0y23 = color_dvvn0y23 && color_dvvn0y23 != undefined  ? color_dvvn0y23 : "";
let g_color_ut160v23 = color_ut160v23 && color_ut160v23 != undefined  ? color_ut160v23 : "";
let g_background_9hyvsf23 = background_9hyvsf23 && background_9hyvsf23 != undefined  ? background_9hyvsf23 : "";
let g_background_k470jq23 = background_k470jq23 && background_k470jq23 != undefined  ? background_k470jq23 : "";
let g_border_7ovaie23 = border_7ovaie23 && border_7ovaie23 != undefined  ? border_7ovaie23 : "";
let g_dimension_5l8cq223 = dimension_5l8cq223 && dimension_5l8cq223 != undefined  ? dimension_5l8cq223 : "";
let g_border_0hahn623 = border_0hahn623 && border_0hahn623 != undefined  ? border_0hahn623 : "";
let g_boxshadow_5r75hr23 = boxshadow_5r75hr23 && boxshadow_5r75hr23 != undefined  ? boxshadow_5r75hr23 : "";
let g_boxshadow_cr8hb323 = boxshadow_cr8hb323 && boxshadow_cr8hb323 != undefined  ? boxshadow_cr8hb323 : "";
let g_color_38lybz23 = color_38lybz23 && color_38lybz23 != undefined && ( (iconscontrol_3dtya523 != "") ) ? color_38lybz23 : "";
let g_color_e5m6p623 = color_e5m6p623 && color_e5m6p623 != undefined && ( (iconscontrol_3dtya523 != "") ) ? color_e5m6p623 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-7c373q24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7c373q24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-button-h-effect ${g_select_3081sf23} ${g_switcher_log9ol25} ${g_switcher_bpu64g25}">
     <a href="${g_url_5lwf3f24_url}" target="${g_url_5lwf3f24_target}" rel="${g_url_5lwf3f24_nofollow} noopener" class="wkit-button-effect ${g_select_b4b4v923}"><span class="btn-hover-txt">${g_text_r41uaa23}</span>${g_iconscontrol_3dtya523}</a> 
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
    new Button_Hover_Effect_7c373q24();