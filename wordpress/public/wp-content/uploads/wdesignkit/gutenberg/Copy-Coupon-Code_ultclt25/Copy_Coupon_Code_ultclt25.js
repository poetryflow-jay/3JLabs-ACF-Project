
    class Copy_Coupon_Code_ultclt25 {
        constructor() {
            this.Copy_Coupon_Code_ultclt25_p23zl725();
        }
    
        Copy_Coupon_Code_ultclt25_p23zl725() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_IconList,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Dimension,Pmgc_Range,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-ultclt25', {
        title: __('Copy Coupon Code'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "far fa-copy tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Discount Code'),__('Promo Code'),__('eCommerce'),__('Coupon Code'),__('Marketing Tool'),__('Copy Code Block'),__('Coupon Block'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_tx5yv124Function = (unit, type) => {
                var g_slider_tx5yv124_list = [];
                g_slider_tx5yv124_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_tx5yv124_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_tx5yv124_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_tx5yv124_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_tx5yv124_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_tx5yv124_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_tx5yv124_list[unit][type];
            };
const slider_2iqmte24Function = (unit, type) => {
                var g_slider_2iqmte24_list = [];
                g_slider_2iqmte24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_2iqmte24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_2iqmte24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2iqmte24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2iqmte24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2iqmte24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2iqmte24_list[unit][type];
            };
const slider_gd6ym724Function = (unit, type) => {
                var g_slider_gd6ym724_list = [];
                g_slider_gd6ym724_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_gd6ym724_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_gd6ym724_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_gd6ym724_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_gd6ym724_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_gd6ym724_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_gd6ym724_list[unit][type];
            };
const slider_k90vna24Function = (unit, type) => {
                var g_slider_k90vna24_list = [];
                g_slider_k90vna24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_k90vna24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_k90vna24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_k90vna24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_k90vna24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_k90vna24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_k90vna24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_7zsrd024,
text_fxbxos24,
text_0nh8x024,
text_fxwh2r24,
text_0okh1q24,
iconscontrol_btnyb324,
choose_lu222g24,
rawhtml_b1mtup25,
choose_fvffil24,
dimension_36hkus24,
slider_tx5yv124,
slider_2iqmte24,
typography_kuepvc24,
color_e1tnvn24,
background_rc9bw924,
border_ooq38u25,
dimension_fpccxy24,
boxshadow_1lno9y25,
dimension_2yb4y524,
slider_gd6ym724,
typography_t8e95n24,
color_reuphl24,
background_yk768e24,
border_2frpeo24,
dimension_3feyr324,
boxshadow_gej4yj24,
color_gvi1mv24,
background_qami4r24,
border_13wh7a24,
boxshadow_9alk5p24,
normalhover_ee40be24,
slider_k90vna24,
color_bhx92i24,
color_whb2xr24,
normalhover_bdcc7b25,
typography_va2q3024,
color_8q05jw24,

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
                        main_function_ultclt25(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_ultclt25 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let copymain = $scope[0].querySelector(".wkit-coupon-row");
let copybtn = copymain.querySelector(".copybtn");
let copybtntext=copymain.querySelector(".copy-btn-text");
let copybtn1 = copymain.querySelector(".copybtn1");
let copytextEf = copymain.querySelector(".copiedtext");
copybtn.addEventListener("click", (e) => {
    let copyInput = copymain.querySelector("#copyvalue"),
        copyText = (copyInput.getAttribute('data-copytext')) ? copyInput.getAttribute('data-copytext') : 'Copy',
        afterText = (copyInput.getAttribute('data-aftertext')) ? copyInput.getAttribute('data-aftertext') : 'Copied';
    copyInput.select();
    document.execCommand("copy");
    copybtntext.textContent = afterText;
      
     setTimeout(()=>{
        copybtntext.textContent = copyText;
    }, 3000);
});

copybtn1.addEventListener("click", (e) => {
    // let copymain1 = $scope[0].querySelector(".wkit-coupon-row");
    let copytext1 = copymain.querySelector(".copiedinner");
    console.log(copytext1);
    copytext1.select();
    document.execCommand("copy");
    copytextEf.classList.add("copied");
    setTimeout(() => {
        copytextEf.classList.remove("copied");
    }, 300);
});
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],],
                separator:"default",
                
                
                value: select_7zsrd024,
                onChange: (value) => {setAttributes({ select_7zsrd024: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Code`),
                type: "text",
                value: text_fxbxos24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_fxbxos24: value }) },
            }),
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_0nh8x024,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_0nh8x024: value }) },
            }),
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`After Copy Text`),
                type: "text",
                value: text_fxwh2r24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_fxwh2r24: value }) },
            }),
( select_7zsrd024 == "style-2" ) && React.createElement(Pmgc_Text, {
                label: __(`Animation Text`),
                type: "text",
                value: text_0okh1q24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_0okh1q24: value }) },
            }),
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_btnyb324,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_btnyb324: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_lu222g24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_lu222g24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(`  `),
            value: rawhtml_b1mtup25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/copy-coupon-code-block/"
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
      href="https://store.posimyth.com/helpdesk/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_b1mtup25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                           ( select_7zsrd024 == "style-1" ) && React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Text Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_fvffil24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_fvffil24: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_36hkus24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_36hkus24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_tx5yv124,
            
            min: slider_tx5yv124 && slider_tx5yv124.unit ? slider_tx5yv124Function(slider_tx5yv124.unit, 'min') : 0,
            max: slider_tx5yv124 && slider_tx5yv124.unit ? slider_tx5yv124Function(slider_tx5yv124.unit, 'max') : 100,
            step: slider_tx5yv124 && slider_tx5yv124.unit ? slider_tx5yv124Function(slider_tx5yv124.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_tx5yv124: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_2iqmte24,
            
            min: slider_2iqmte24 && slider_2iqmte24.unit ? slider_2iqmte24Function(slider_2iqmte24.unit, 'min') : 0,
            max: slider_2iqmte24 && slider_2iqmte24.unit ? slider_2iqmte24Function(slider_2iqmte24.unit, 'max') : 100,
            step: slider_2iqmte24 && slider_2iqmte24.unit ? slider_2iqmte24Function(slider_2iqmte24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2iqmte24: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_kuepvc24,
            onChange: (value) => setAttributes({ typography_kuepvc24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_e1tnvn24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_e1tnvn24: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Background, {
            
            value: background_rc9bw924,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_rc9bw924: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Border, {
            
            value: border_ooq38u25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_ooq38u25: value }),
            }), 
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_fpccxy24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_fpccxy24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_1lno9y25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_1lno9y25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_2yb4y524,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_2yb4y524: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_gd6ym724,
            
            min: slider_gd6ym724 && slider_gd6ym724.unit ? slider_gd6ym724Function(slider_gd6ym724.unit, 'min') : 0,
            max: slider_gd6ym724 && slider_gd6ym724.unit ? slider_gd6ym724Function(slider_gd6ym724.unit, 'max') : 100,
            step: slider_gd6ym724 && slider_gd6ym724.unit ? slider_gd6ym724Function(slider_gd6ym724.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_gd6ym724: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_t8e95n24,
            onChange: (value) => setAttributes({ typography_t8e95n24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_reuphl24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_reuphl24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_yk768e24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_yk768e24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_2frpeo24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_2frpeo24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_3feyr324,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_3feyr324: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_gej4yj24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_gej4yj24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_gvi1mv24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_gvi1mv24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_qami4r24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qami4r24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_13wh7a24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_13wh7a24: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_9alk5p24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_9alk5p24: value }),
            }), 
), 
), 
),( select_7zsrd024 == "style-1" ) && React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_k90vna24,
            
            min: slider_k90vna24 && slider_k90vna24.unit ? slider_k90vna24Function(slider_k90vna24.unit, 'min') : 0,
            max: slider_k90vna24 && slider_k90vna24.unit ? slider_k90vna24Function(slider_k90vna24.unit, 'max') : 100,
            step: slider_k90vna24 && slider_k90vna24.unit ? slider_k90vna24Function(slider_k90vna24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_k90vna24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_bhx92i24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_bhx92i24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( select_7zsrd024 == "style-1" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_whb2xr24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_whb2xr24: value }),
            }), 
), 
), 
),( select_7zsrd024 == "style-2" ) && React.createElement(PanelBody, { title: __("Animation Text"), initialOpen: false },
( select_7zsrd024 == "style-2" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_va2q3024,
            onChange: (value) => setAttributes({ typography_va2q3024: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_7zsrd024 == "style-2" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_8q05jw24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8q05jw24: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-ultclt25", block_id, false, props.clientId);
                }
            }

            
let g_select_7zsrd024 = select_7zsrd024 && select_7zsrd024 != undefined  ? select_7zsrd024 : "";
let g_text_fxbxos24 = text_fxbxos24 && text_fxbxos24 != undefined  ? text_fxbxos24 : "";
let g_text_0nh8x024 = text_0nh8x024 && text_0nh8x024 != undefined && ( (select_7zsrd024 == "style-1") ) ? text_0nh8x024 : "";
let g_text_fxwh2r24 = text_fxwh2r24 && text_fxwh2r24 != undefined && ( (select_7zsrd024 == "style-1") ) ? text_fxwh2r24 : "";
let g_text_0okh1q24 = text_0okh1q24 && text_0okh1q24 != undefined && ( (select_7zsrd024 == "style-2") ) ? text_0okh1q24 : "";
let g_iconscontrol_btnyb324 = iconscontrol_btnyb324 != undefined && ( (select_7zsrd024 == "style-1") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_btnyb324+'"></i></span>' : '';

let g_choose_lu222g24 = choose_lu222g24 && choose_lu222g24 != undefined  ? choose_lu222g24 : "";
let g_rawhtml_b1mtup25 = rawhtml_b1mtup25 && rawhtml_b1mtup25 != undefined  ? rawhtml_b1mtup25 : "";
let g_choose_fvffil24 = choose_fvffil24 && choose_fvffil24 != undefined && ( (select_7zsrd024 == "style-1") ) ? choose_fvffil24 : "";
let g_dimension_36hkus24 = dimension_36hkus24 && dimension_36hkus24 != undefined && ( (select_7zsrd024 == "style-1") ) ? dimension_36hkus24 : "";
let g_typography_kuepvc24 = typography_kuepvc24 && typography_kuepvc24 != undefined && ( (select_7zsrd024 == "style-1") ) ? typography_kuepvc24 : "";
let g_color_e1tnvn24 = color_e1tnvn24 && color_e1tnvn24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_e1tnvn24 : "";
let g_background_rc9bw924 = background_rc9bw924 && background_rc9bw924 != undefined && ( (select_7zsrd024 == "style-1") ) ? background_rc9bw924 : "";
let g_border_ooq38u25 = border_ooq38u25 && border_ooq38u25 != undefined && ( (select_7zsrd024 == "style-1") ) ? border_ooq38u25 : "";
let g_dimension_fpccxy24 = dimension_fpccxy24 && dimension_fpccxy24 != undefined && ( (select_7zsrd024 == "style-1") ) ? dimension_fpccxy24 : "";
let g_boxshadow_1lno9y25 = boxshadow_1lno9y25 && boxshadow_1lno9y25 != undefined  ? boxshadow_1lno9y25 : "";
let g_dimension_2yb4y524 = dimension_2yb4y524 && dimension_2yb4y524 != undefined  ? dimension_2yb4y524 : "";
let g_typography_t8e95n24 = typography_t8e95n24 && typography_t8e95n24 != undefined  ? typography_t8e95n24 : "";
let g_color_reuphl24 = color_reuphl24 && color_reuphl24 != undefined  ? color_reuphl24 : "";
let g_background_yk768e24 = background_yk768e24 && background_yk768e24 != undefined  ? background_yk768e24 : "";
let g_border_2frpeo24 = border_2frpeo24 && border_2frpeo24 != undefined  ? border_2frpeo24 : "";
let g_dimension_3feyr324 = dimension_3feyr324 && dimension_3feyr324 != undefined  ? dimension_3feyr324 : "";
let g_boxshadow_gej4yj24 = boxshadow_gej4yj24 && boxshadow_gej4yj24 != undefined  ? boxshadow_gej4yj24 : "";
let g_color_gvi1mv24 = color_gvi1mv24 && color_gvi1mv24 != undefined  ? color_gvi1mv24 : "";
let g_background_qami4r24 = background_qami4r24 && background_qami4r24 != undefined  ? background_qami4r24 : "";
let g_border_13wh7a24 = border_13wh7a24 && border_13wh7a24 != undefined  ? border_13wh7a24 : "";
let g_boxshadow_9alk5p24 = boxshadow_9alk5p24 && boxshadow_9alk5p24 != undefined  ? boxshadow_9alk5p24 : "";
let g_color_bhx92i24 = color_bhx92i24 && color_bhx92i24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_bhx92i24 : "";
let g_color_whb2xr24 = color_whb2xr24 && color_whb2xr24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_whb2xr24 : "";
let g_typography_va2q3024 = typography_va2q3024 && typography_va2q3024 != undefined && ( (select_7zsrd024 == "style-2") ) ? typography_va2q3024 : "";
let g_color_8q05jw24 = color_8q05jw24 && color_8q05jw24 != undefined && ( (select_7zsrd024 == "style-2") ) ? color_8q05jw24 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ultclt25 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-coupon-row ${g_select_7zsrd024}">
     <div class="wkit-coupon-code-style-1">
          <input id="copyvalue" type="text" readonly="" value="${g_text_fxbxos24}" data-copytext="${g_text_0nh8x024}" data-aftertext="${g_text_fxwh2r24}">
          <button class="copybtn">
              <span class="copy-btn-icon">${g_iconscontrol_btnyb324}</span><span class="copy-btn-text">${g_text_0nh8x024}</span>
            </button>
     </div>
     <div class="wkit-coupon-code-style-2">
         <button class="copybtn1">
            <span class="copiedtext">${g_text_0okh1q24}</span> 
            <input type="text" readonly="" class="copiedinner" value="${g_text_fxbxos24}">
         </button>
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
            select_7zsrd024,
text_fxbxos24,
text_0nh8x024,
text_fxwh2r24,
text_0okh1q24,
iconscontrol_btnyb324,
choose_lu222g24,
rawhtml_b1mtup25,
choose_fvffil24,
dimension_36hkus24,
slider_tx5yv124,
slider_2iqmte24,
typography_kuepvc24,
color_e1tnvn24,
background_rc9bw924,
border_ooq38u25,
dimension_fpccxy24,
boxshadow_1lno9y25,
dimension_2yb4y524,
slider_gd6ym724,
typography_t8e95n24,
color_reuphl24,
background_yk768e24,
border_2frpeo24,
dimension_3feyr324,
boxshadow_gej4yj24,
color_gvi1mv24,
background_qami4r24,
border_13wh7a24,
boxshadow_9alk5p24,
normalhover_ee40be24,
slider_k90vna24,
color_bhx92i24,
color_whb2xr24,
normalhover_bdcc7b25,
typography_va2q3024,
color_8q05jw24,

            block_id,
        } = attributes;

        

        

        

        
let g_select_7zsrd024 = select_7zsrd024 && select_7zsrd024 != undefined  ? select_7zsrd024 : "";
let g_text_fxbxos24 = text_fxbxos24 && text_fxbxos24 != undefined  ? text_fxbxos24 : "";
let g_text_0nh8x024 = text_0nh8x024 && text_0nh8x024 != undefined && ( (select_7zsrd024 == "style-1") ) ? text_0nh8x024 : "";
let g_text_fxwh2r24 = text_fxwh2r24 && text_fxwh2r24 != undefined && ( (select_7zsrd024 == "style-1") ) ? text_fxwh2r24 : "";
let g_text_0okh1q24 = text_0okh1q24 && text_0okh1q24 != undefined && ( (select_7zsrd024 == "style-2") ) ? text_0okh1q24 : "";
let g_iconscontrol_btnyb324 = iconscontrol_btnyb324 != undefined && ( (select_7zsrd024 == "style-1") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_btnyb324+'"></i></span>' : '';

let g_choose_lu222g24 = choose_lu222g24 && choose_lu222g24 != undefined  ? choose_lu222g24 : "";
let g_rawhtml_b1mtup25 = rawhtml_b1mtup25 && rawhtml_b1mtup25 != undefined  ? rawhtml_b1mtup25 : "";
let g_choose_fvffil24 = choose_fvffil24 && choose_fvffil24 != undefined && ( (select_7zsrd024 == "style-1") ) ? choose_fvffil24 : "";
let g_dimension_36hkus24 = dimension_36hkus24 && dimension_36hkus24 != undefined && ( (select_7zsrd024 == "style-1") ) ? dimension_36hkus24 : "";
let g_typography_kuepvc24 = typography_kuepvc24 && typography_kuepvc24 != undefined && ( (select_7zsrd024 == "style-1") ) ? typography_kuepvc24 : "";
let g_color_e1tnvn24 = color_e1tnvn24 && color_e1tnvn24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_e1tnvn24 : "";
let g_background_rc9bw924 = background_rc9bw924 && background_rc9bw924 != undefined && ( (select_7zsrd024 == "style-1") ) ? background_rc9bw924 : "";
let g_border_ooq38u25 = border_ooq38u25 && border_ooq38u25 != undefined && ( (select_7zsrd024 == "style-1") ) ? border_ooq38u25 : "";
let g_dimension_fpccxy24 = dimension_fpccxy24 && dimension_fpccxy24 != undefined && ( (select_7zsrd024 == "style-1") ) ? dimension_fpccxy24 : "";
let g_boxshadow_1lno9y25 = boxshadow_1lno9y25 && boxshadow_1lno9y25 != undefined  ? boxshadow_1lno9y25 : "";
let g_dimension_2yb4y524 = dimension_2yb4y524 && dimension_2yb4y524 != undefined  ? dimension_2yb4y524 : "";
let g_typography_t8e95n24 = typography_t8e95n24 && typography_t8e95n24 != undefined  ? typography_t8e95n24 : "";
let g_color_reuphl24 = color_reuphl24 && color_reuphl24 != undefined  ? color_reuphl24 : "";
let g_background_yk768e24 = background_yk768e24 && background_yk768e24 != undefined  ? background_yk768e24 : "";
let g_border_2frpeo24 = border_2frpeo24 && border_2frpeo24 != undefined  ? border_2frpeo24 : "";
let g_dimension_3feyr324 = dimension_3feyr324 && dimension_3feyr324 != undefined  ? dimension_3feyr324 : "";
let g_boxshadow_gej4yj24 = boxshadow_gej4yj24 && boxshadow_gej4yj24 != undefined  ? boxshadow_gej4yj24 : "";
let g_color_gvi1mv24 = color_gvi1mv24 && color_gvi1mv24 != undefined  ? color_gvi1mv24 : "";
let g_background_qami4r24 = background_qami4r24 && background_qami4r24 != undefined  ? background_qami4r24 : "";
let g_border_13wh7a24 = border_13wh7a24 && border_13wh7a24 != undefined  ? border_13wh7a24 : "";
let g_boxshadow_9alk5p24 = boxshadow_9alk5p24 && boxshadow_9alk5p24 != undefined  ? boxshadow_9alk5p24 : "";
let g_color_bhx92i24 = color_bhx92i24 && color_bhx92i24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_bhx92i24 : "";
let g_color_whb2xr24 = color_whb2xr24 && color_whb2xr24 != undefined && ( (select_7zsrd024 == "style-1") ) ? color_whb2xr24 : "";
let g_typography_va2q3024 = typography_va2q3024 && typography_va2q3024 != undefined && ( (select_7zsrd024 == "style-2") ) ? typography_va2q3024 : "";
let g_color_8q05jw24 = color_8q05jw24 && color_8q05jw24 != undefined && ( (select_7zsrd024 == "style-2") ) ? color_8q05jw24 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-ultclt25", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ultclt25 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-coupon-row ${g_select_7zsrd024}">
     <div class="wkit-coupon-code-style-1">
          <input id="copyvalue" type="text" readonly="" value="${g_text_fxbxos24}" data-copytext="${g_text_0nh8x024}" data-aftertext="${g_text_fxwh2r24}">
          <button class="copybtn">
              <span class="copy-btn-icon">${g_iconscontrol_btnyb324}</span><span class="copy-btn-text">${g_text_0nh8x024}</span>
            </button>
     </div>
     <div class="wkit-coupon-code-style-2">
         <button class="copybtn1">
            <span class="copiedtext">${g_text_0okh1q24}</span> 
            <input type="text" readonly="" class="copiedinner" value="${g_text_fxbxos24}">
         </button>
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
    new Copy_Coupon_Code_ultclt25();