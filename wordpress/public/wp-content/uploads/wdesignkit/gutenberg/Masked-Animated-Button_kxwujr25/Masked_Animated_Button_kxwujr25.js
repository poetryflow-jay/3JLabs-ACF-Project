
    class Masked_Animated_Button_kxwujr25 {
        constructor() {
            this.Masked_Animated_Button_kxwujr25_2i9ozc25();
        }
    
        Masked_Animated_Button_kxwujr25_2i9ozc25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_IconList,Pmgc_Url,Pmgc_RadioAdvanced,Pmgc_Range,Pmgc_Toggle,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-kxwujr25', {
        title: __('Masked Animated Button'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-mask tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('CTA Buttons'),__('Animated Button Block'),__('Gutenberg Button Animation'),__('Portfolio Links'),__('Call to Action Enhancer'),__('Masking Animation'),__('Interactive Web Elements'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_70mq0c24Function = (unit, type) => {
                var g_slider_70mq0c24_list = [];
                g_slider_70mq0c24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_70mq0c24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_70mq0c24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_70mq0c24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_70mq0c24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_70mq0c24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_70mq0c24_list[unit][type];
            };
const slider_xqua3z25Function = (unit, type) => {
                var g_slider_xqua3z25_list = [];
                g_slider_xqua3z25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_xqua3z25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_xqua3z25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_xqua3z25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_xqua3z25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_xqua3z25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_xqua3z25_list[unit][type];
            };
const slider_a4hsdi25Function = (unit, type) => {
                var g_slider_a4hsdi25_list = [];
                g_slider_a4hsdi25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_a4hsdi25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_a4hsdi25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_a4hsdi25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_a4hsdi25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_a4hsdi25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_a4hsdi25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_xmaghz24,
text_1djdcp24,
iconscontrol_u7r8i124,
url_126azl24,
choose_iz2kmq24,
slider_70mq0c24,
switcher_eyhwvn25,
switcher_4i20c625,
rawhtml_2sjik225,
dimension_xzzusu25,
typography_1qc22q24,
color_j8zyk324,
color_1dlare24,
background_sqm4pc25,
border_g06v8r24,
dimension_ow0d7m24,
boxshadow_p6qi3a24,
background_yfmfll25,
border_9fnemp24,
boxshadow_l37yw224,
normalhover_uq140w24,
slider_xqua3z25,
slider_a4hsdi25,
color_lnjuje25,
color_2t50ys25,
normalhover_zraiww25,

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
                        main_function_kxwujr25(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_kxwujr25 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let maskBtn = $scope[0].querySelector('.wdk-masked-ani-btn'); // fixed class name
let btn = maskBtn.querySelector('.wdk-mas');
let link = maskBtn.querySelector('.wdk-masked-btn');

const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

link.addEventListener('click', (e) => {
    if (
        (isMobile && !btn.classList.contains('disable-mobile-link')) ||
        (isTablet && !btn.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['style-one',__('Style 1')],['style-two',__('Style 2')],['style-three',__('Style 3')],],
                separator:"default",
                
                
                value: select_xmaghz24,
                onChange: (value) => {setAttributes({ select_xmaghz24: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_1djdcp24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_1djdcp24: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_u7r8i124,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_u7r8i124: value }),
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_126azl24,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_126azl24: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_iz2kmq24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_iz2kmq24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_70mq0c24,
            
            min: slider_70mq0c24 && slider_70mq0c24.unit ? slider_70mq0c24Function(slider_70mq0c24.unit, 'min') : 0,
            max: slider_70mq0c24 && slider_70mq0c24.unit ? slider_70mq0c24Function(slider_70mq0c24.unit, 'max') : 100,
            step: slider_70mq0c24 && slider_70mq0c24.unit ? slider_70mq0c24Function(slider_70mq0c24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_70mq0c24: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_eyhwvn25,
            
            onChange: (value) => setAttributes({ switcher_eyhwvn25: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_4i20c625,
            
            onChange: (value) => setAttributes({ switcher_4i20c625: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_2sjik225,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/masked-animated-button-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_2sjik225: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_xzzusu25,
            noLock: false,
            unit: ['px','%','em',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_xzzusu25: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_1qc22q24,
            onChange: (value) => setAttributes({ typography_1qc22q24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1dlare24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1dlare24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_sqm4pc25,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_sqm4pc25: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_g06v8r24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_g06v8r24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ow0d7m24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ow0d7m24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_p6qi3a24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_p6qi3a24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_j8zyk324,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_j8zyk324: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_yfmfll25,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_yfmfll25: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_9fnemp24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_9fnemp24: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_l37yw224,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_l37yw224: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_xqua3z25,
            
            min: slider_xqua3z25 && slider_xqua3z25.unit ? slider_xqua3z25Function(slider_xqua3z25.unit, 'min') : 0,
            max: slider_xqua3z25 && slider_xqua3z25.unit ? slider_xqua3z25Function(slider_xqua3z25.unit, 'max') : 100,
            step: slider_xqua3z25 && slider_xqua3z25.unit ? slider_xqua3z25Function(slider_xqua3z25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_xqua3z25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_a4hsdi25,
            
            min: slider_a4hsdi25 && slider_a4hsdi25.unit ? slider_a4hsdi25Function(slider_a4hsdi25.unit, 'min') : 0,
            max: slider_a4hsdi25 && slider_a4hsdi25.unit ? slider_a4hsdi25Function(slider_a4hsdi25.unit, 'max') : 100,
            step: slider_a4hsdi25 && slider_a4hsdi25.unit ? slider_a4hsdi25Function(slider_a4hsdi25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_a4hsdi25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_lnjuje25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_lnjuje25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_2t50ys25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_2t50ys25: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-kxwujr25", block_id, false, props.clientId);
                }
            }

            
let g_select_xmaghz24 = select_xmaghz24 && select_xmaghz24 != undefined  ? select_xmaghz24 : "";
let g_text_1djdcp24 = text_1djdcp24 && text_1djdcp24 != undefined  ? text_1djdcp24 : "";
let g_iconscontrol_u7r8i124 = iconscontrol_u7r8i124 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_u7r8i124+'"></i></span>' : '';

let g_url_126azl24_url = url_126azl24?.url && url_126azl24?.url != undefined ? url_126azl24.url : "";
let g_url_126azl24_target = url_126azl24?.target && url_126azl24?.target != undefined ? url_126azl24.target : "";
let g_url_126azl24_nofollow = url_126azl24?.nofollow && url_126azl24?.nofollow != undefined ? url_126azl24.nofollow : "";
let g_url_126azl24_ctmArt = url_126azl24?.attr != undefined ? url_126azl24.attr : "";
                    let g_url_126azl24_attr = ''

                    if (g_url_126azl24_ctmArt) {
                        let main_array = g_url_126azl24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_126azl24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_choose_iz2kmq24 = choose_iz2kmq24 && choose_iz2kmq24 != undefined  ? choose_iz2kmq24 : "";
let g_switcher_eyhwvn25 = switcher_eyhwvn25 && switcher_eyhwvn25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_4i20c625 = switcher_4i20c625 && switcher_4i20c625 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_2sjik225 = rawhtml_2sjik225 && rawhtml_2sjik225 != undefined  ? rawhtml_2sjik225 : "";
let g_dimension_xzzusu25 = dimension_xzzusu25 && dimension_xzzusu25 != undefined  ? dimension_xzzusu25 : "";
let g_typography_1qc22q24 = typography_1qc22q24 && typography_1qc22q24 != undefined  ? typography_1qc22q24 : "";
let g_color_j8zyk324 = color_j8zyk324 && color_j8zyk324 != undefined  ? color_j8zyk324 : "";
let g_color_1dlare24 = color_1dlare24 && color_1dlare24 != undefined  ? color_1dlare24 : "";
let g_background_sqm4pc25 = background_sqm4pc25 && background_sqm4pc25 != undefined  ? background_sqm4pc25 : "";
let g_border_g06v8r24 = border_g06v8r24 && border_g06v8r24 != undefined  ? border_g06v8r24 : "";
let g_dimension_ow0d7m24 = dimension_ow0d7m24 && dimension_ow0d7m24 != undefined  ? dimension_ow0d7m24 : "";
let g_boxshadow_p6qi3a24 = boxshadow_p6qi3a24 && boxshadow_p6qi3a24 != undefined  ? boxshadow_p6qi3a24 : "";
let g_background_yfmfll25 = background_yfmfll25 && background_yfmfll25 != undefined  ? background_yfmfll25 : "";
let g_border_9fnemp24 = border_9fnemp24 && border_9fnemp24 != undefined  ? border_9fnemp24 : "";
let g_boxshadow_l37yw224 = boxshadow_l37yw224 && boxshadow_l37yw224 != undefined  ? boxshadow_l37yw224 : "";
let g_color_lnjuje25 = color_lnjuje25 && color_lnjuje25 != undefined  ? color_lnjuje25 : "";
let g_color_2t50ys25 = color_2t50ys25 && color_2t50ys25 != undefined  ? color_2t50ys25 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_kxwujr25 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wdk-masked-ani-btn">
    <div class="wdk-masbtn-cover">
        <div class="wdk-mas ${g_switcher_eyhwvn25} ${g_switcher_4i20c625}">${g_text_1djdcp24}<span class="wdk-sm-icon">${g_iconscontrol_u7r8i124}</span></div>
        <a href="${g_url_126azl24_url}" target="${g_url_126azl24_target}" class="wdk-masked-btn ${g_select_xmaghz24}" rel="noopener">${g_text_1djdcp24}<span class="wdk-sm-icon">${g_iconscontrol_u7r8i124}</span></a>
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
            select_xmaghz24,
text_1djdcp24,
iconscontrol_u7r8i124,
url_126azl24,
choose_iz2kmq24,
slider_70mq0c24,
switcher_eyhwvn25,
switcher_4i20c625,
rawhtml_2sjik225,
dimension_xzzusu25,
typography_1qc22q24,
color_j8zyk324,
color_1dlare24,
background_sqm4pc25,
border_g06v8r24,
dimension_ow0d7m24,
boxshadow_p6qi3a24,
background_yfmfll25,
border_9fnemp24,
boxshadow_l37yw224,
normalhover_uq140w24,
slider_xqua3z25,
slider_a4hsdi25,
color_lnjuje25,
color_2t50ys25,
normalhover_zraiww25,

            block_id,
        } = attributes;

        

        

        

        
let g_select_xmaghz24 = select_xmaghz24 && select_xmaghz24 != undefined  ? select_xmaghz24 : "";
let g_text_1djdcp24 = text_1djdcp24 && text_1djdcp24 != undefined  ? text_1djdcp24 : "";
let g_iconscontrol_u7r8i124 = iconscontrol_u7r8i124 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_u7r8i124+'"></i></span>' : '';

let g_url_126azl24_url = url_126azl24?.url && url_126azl24?.url != undefined ? url_126azl24.url : "";
let g_url_126azl24_target = url_126azl24?.target && url_126azl24?.target != undefined ? url_126azl24.target : "";
let g_url_126azl24_nofollow = url_126azl24?.nofollow && url_126azl24?.nofollow != undefined ? url_126azl24.nofollow : "";
let g_url_126azl24_ctmArt = url_126azl24?.attr != undefined ? url_126azl24.attr : "";
                    let g_url_126azl24_attr = ''

                    if (g_url_126azl24_ctmArt) {
                        let main_array = g_url_126azl24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_126azl24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_choose_iz2kmq24 = choose_iz2kmq24 && choose_iz2kmq24 != undefined  ? choose_iz2kmq24 : "";
let g_switcher_eyhwvn25 = switcher_eyhwvn25 && switcher_eyhwvn25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_4i20c625 = switcher_4i20c625 && switcher_4i20c625 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_2sjik225 = rawhtml_2sjik225 && rawhtml_2sjik225 != undefined  ? rawhtml_2sjik225 : "";
let g_dimension_xzzusu25 = dimension_xzzusu25 && dimension_xzzusu25 != undefined  ? dimension_xzzusu25 : "";
let g_typography_1qc22q24 = typography_1qc22q24 && typography_1qc22q24 != undefined  ? typography_1qc22q24 : "";
let g_color_j8zyk324 = color_j8zyk324 && color_j8zyk324 != undefined  ? color_j8zyk324 : "";
let g_color_1dlare24 = color_1dlare24 && color_1dlare24 != undefined  ? color_1dlare24 : "";
let g_background_sqm4pc25 = background_sqm4pc25 && background_sqm4pc25 != undefined  ? background_sqm4pc25 : "";
let g_border_g06v8r24 = border_g06v8r24 && border_g06v8r24 != undefined  ? border_g06v8r24 : "";
let g_dimension_ow0d7m24 = dimension_ow0d7m24 && dimension_ow0d7m24 != undefined  ? dimension_ow0d7m24 : "";
let g_boxshadow_p6qi3a24 = boxshadow_p6qi3a24 && boxshadow_p6qi3a24 != undefined  ? boxshadow_p6qi3a24 : "";
let g_background_yfmfll25 = background_yfmfll25 && background_yfmfll25 != undefined  ? background_yfmfll25 : "";
let g_border_9fnemp24 = border_9fnemp24 && border_9fnemp24 != undefined  ? border_9fnemp24 : "";
let g_boxshadow_l37yw224 = boxshadow_l37yw224 && boxshadow_l37yw224 != undefined  ? boxshadow_l37yw224 : "";
let g_color_lnjuje25 = color_lnjuje25 && color_lnjuje25 != undefined  ? color_lnjuje25 : "";
let g_color_2t50ys25 = color_2t50ys25 && color_2t50ys25 != undefined  ? color_2t50ys25 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-kxwujr25", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_kxwujr25 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wdk-masked-ani-btn">
    <div class="wdk-masbtn-cover">
        <div class="wdk-mas ${g_switcher_eyhwvn25} ${g_switcher_4i20c625}">${g_text_1djdcp24}<span class="wdk-sm-icon">${g_iconscontrol_u7r8i124}</span></div>
        <a href="${g_url_126azl24_url}" target="${g_url_126azl24_target}" class="wdk-masked-btn ${g_select_xmaghz24}" rel="noopener">${g_text_1djdcp24}<span class="wdk-sm-icon">${g_iconscontrol_u7r8i124}</span></a>
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
    new Masked_Animated_Button_kxwujr25();