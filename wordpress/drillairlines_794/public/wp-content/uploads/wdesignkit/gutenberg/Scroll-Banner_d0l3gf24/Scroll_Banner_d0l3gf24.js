
    class Scroll_Banner_d0l3gf24 {
        constructor() {
            this.Scroll_Banner_d0l3gf24_k6w2tr25();
        }
    
        Scroll_Banner_d0l3gf24_k6w2tr25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_TextArea,Pmgc_Url,Pmgc_IconList,Pmgc_RadioAdvanced,Pmgc_Label_Heading,Pmgc_Typography,Pmgc_Color,Pmgc_Range,Pmgc_Dimension,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-d0l3gf24', {
        title: __('Scroll Banner'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-scroll tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Scroll'),__('Banner'),__('Scale Effect'),__('Scroll Triggered Animation'),__('Scroll Banner'),__('Scroll Animation'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_krejxj24Function = (unit, type) => {
                var g_slider_krejxj24_list = [];
                g_slider_krejxj24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_krejxj24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_krejxj24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_krejxj24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_krejxj24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_krejxj24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_krejxj24_list[unit][type];
            };
const slider_zojp0v24Function = (unit, type) => {
                var g_slider_zojp0v24_list = [];
                g_slider_zojp0v24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_zojp0v24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_zojp0v24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_zojp0v24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_zojp0v24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_zojp0v24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_zojp0v24_list[unit][type];
            };
const slider_d07uf624Function = (unit, type) => {
                var g_slider_d07uf624_list = [];
                g_slider_d07uf624_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_d07uf624_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_d07uf624_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_d07uf624_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_d07uf624_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_d07uf624_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_d07uf624_list[unit][type];
            };
const slider_xknxkv24Function = (unit, type) => {
                var g_slider_xknxkv24_list = [];
                g_slider_xknxkv24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_xknxkv24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_xknxkv24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_xknxkv24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_xknxkv24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_xknxkv24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_xknxkv24_list[unit][type];
            };
const slider_wpyc8b24Function = (unit, type) => {
                var g_slider_wpyc8b24_list = [];
                g_slider_wpyc8b24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_wpyc8b24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_wpyc8b24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_wpyc8b24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_wpyc8b24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_wpyc8b24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_wpyc8b24_list[unit][type];
            };
const slider_lph0je24Function = (unit, type) => {
                var g_slider_lph0je24_list = [];
                g_slider_lph0je24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_lph0je24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_lph0je24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_lph0je24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_lph0je24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_lph0je24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_lph0je24_list[unit][type];
            };
const slider_2n6tro24Function = (unit, type) => {
                var g_slider_2n6tro24_list = [];
                g_slider_2n6tro24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_2n6tro24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_2n6tro24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2n6tro24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2n6tro24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2n6tro24_list['vh'] = { "type": "vh", "min": 0, "max": 100, "step": 1 };

                return g_slider_2n6tro24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_maa9l124,
text_yrgf8h23,
textarea_5s0pvo24,
text_wjncvf24,
url_8nbad724,
iconscontrol_ysenzi24,
choose_dayp7w24,
choose_m609eh24,
number_3g0yfs24,
number_fthuwx24,
number_3aeero24,
heading_6bn6va24,
heading_sp6a3x24,
typography_z30reh24,
color_9mrdfv24,
slider_krejxj24,
heading_iuiaj924,
typography_b3qci424,
color_kpvmxz24,
slider_zojp0v24,
slider_d07uf624,
dimension_3hx4sb24,
choose_30vc8f24,
slider_xknxkv24,
slider_wpyc8b24,
slider_lph0je24,
dimension_2ivujb24,
typography_o6gdfm24,
color_mds39e24,
color_ti4a4a24,
color_hdswtq24,
background_hcwr3z24,
border_rd4der24,
dimension_t60onu24,
boxshadow_nzl2ji24,
color_8w9wmg24,
color_06or9n24,
color_ca5les24,
background_37b2um24,
border_91b75824,
dimension_vlqyk224,
boxshadow_aol69g24,
normalhover_wvpd0124,
slider_2n6tro24,
dimension_fakwy624,
background_6kkskj24,
border_83ln3024,
dimension_qqh70n24,
boxshadow_7eywv524,

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
                        main_function_d0l3gf24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_d0l3gf24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
gsap.registerPlugin(ScrollTrigger);

let getEl =$scope[0].querySelector('.wkit-scroll-wrapper');
let getBanner = getEl.querySelector('.wkit-scroll-banner');

let deskScale = (getEl.getAttribute('desktop-scale')) ? Number(getEl.getAttribute('desktop-scale')) : 0.6; 
let tabScale = (getEl.getAttribute('tablet-scale')) ? Number(getEl.getAttribute('tablet-scale')) : 0.6; 
let mobScale = (getEl.getAttribute('mobile-scale')) ? Number(getEl.getAttribute('mobile-scale')) : 0.5; 
let screenWidth = screen.width;
let scaleVal = 0.6;
if (screenWidth >= 1024) {
    scaleVal = deskScale;
} else if (screenWidth < 1024 && screenWidth >= 768) {
    scaleVal = tabScale;
} else if (screenWidth < 768) {
    scaleVal = mobScale;
}

if(!$scope[0].closest('.wp-block')){
    let getCnt = getBanner.querySelectorAll('.wkit-scroll-banner > *');
    
    const tlll = gsap.timeline().to(getBanner, {scale: scaleVal}).fromTo(getCnt,{y: 100, opacity: 0},{y: 0, opacity: 1, stagger: 0.3})
    
    
    ScrollTrigger.create({
    	trigger: getEl,
    	animation: tlll,
    	pin: true,
    	start: 'center center',
    	end: 'bottom top',
    	scrub: 1, 
    })
    let text1 = getBanner.querySelector('.wkit-title');   
}

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Select`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],],
                separator:"default",
                
                
                value: select_maa9l124,
                onChange: (value) => {setAttributes({ select_maa9l124: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_yrgf8h23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_yrgf8h23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_5s0pvo24,
                dynamic: true,
                onChange: (value) => setAttributes({ textarea_5s0pvo24: value }),
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_wjncvf24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_wjncvf24: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`Url`),
                type: "url",
                value: url_8nbad724,
                dynamic: [true, 'url_8nbad724'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_8nbad724: value }),
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_ysenzi24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_ysenzi24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_dayp7w24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_dayp7w24: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Layout`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
{ label: __('Space Between'), value: 'space-between', title: __('Space Between'), icon: 'fas fa-align-justify', svg: '' }, 
],
            value: choose_m609eh24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_m609eh24: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Desktop Scale`),
                type: "number",
                value: number_3g0yfs24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_3g0yfs24: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tablet Scale`),
                type: "number",
                value: number_fthuwx24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_fthuwx24: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Mobile Scale`),
                type: "number",
                value: number_3aeero24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_3aeero24: value }) },
            }),
), React.createElement(PanelBody, { title: __("Note"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Animation is currently disabled in the backend. To view the animations, please switch to the frontend of the site.`),
            value: heading_6bn6va24,
            separator:"default",
            inlineblock: true,
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_sp6a3x24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_z30reh24,
            onChange: (value) => setAttributes({ typography_z30reh24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_9mrdfv24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9mrdfv24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_krejxj24,
            
            min: slider_krejxj24 && slider_krejxj24.unit ? slider_krejxj24Function(slider_krejxj24.unit, 'min') : 0,
            max: slider_krejxj24 && slider_krejxj24.unit ? slider_krejxj24Function(slider_krejxj24.unit, 'max') : 100,
            step: slider_krejxj24 && slider_krejxj24.unit ? slider_krejxj24Function(slider_krejxj24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_krejxj24: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_iuiaj924,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_b3qci424,
            onChange: (value) => setAttributes({ typography_b3qci424: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_kpvmxz24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kpvmxz24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_zojp0v24,
            
            min: slider_zojp0v24 && slider_zojp0v24.unit ? slider_zojp0v24Function(slider_zojp0v24.unit, 'min') : 0,
            max: slider_zojp0v24 && slider_zojp0v24.unit ? slider_zojp0v24Function(slider_zojp0v24.unit, 'max') : 100,
            step: slider_zojp0v24 && slider_zojp0v24.unit ? slider_zojp0v24Function(slider_zojp0v24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_zojp0v24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_d07uf624,
            
            min: slider_d07uf624 && slider_d07uf624.unit ? slider_d07uf624Function(slider_d07uf624.unit, 'min') : 0,
            max: slider_d07uf624 && slider_d07uf624.unit ? slider_d07uf624Function(slider_d07uf624.unit, 'max') : 100,
            step: slider_d07uf624 && slider_d07uf624.unit ? slider_d07uf624Function(slider_d07uf624.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_d07uf624: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_3hx4sb24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_3hx4sb24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
( select_maa9l124 == "style-2" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_30vc8f24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_30vc8f24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_xknxkv24,
            
            min: slider_xknxkv24 && slider_xknxkv24.unit ? slider_xknxkv24Function(slider_xknxkv24.unit, 'min') : 0,
            max: slider_xknxkv24 && slider_xknxkv24.unit ? slider_xknxkv24Function(slider_xknxkv24.unit, 'max') : 100,
            step: slider_xknxkv24 && slider_xknxkv24.unit ? slider_xknxkv24Function(slider_xknxkv24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_xknxkv24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_wpyc8b24,
            
            min: slider_wpyc8b24 && slider_wpyc8b24.unit ? slider_wpyc8b24Function(slider_wpyc8b24.unit, 'min') : 0,
            max: slider_wpyc8b24 && slider_wpyc8b24.unit ? slider_wpyc8b24Function(slider_wpyc8b24.unit, 'max') : 100,
            step: slider_wpyc8b24 && slider_wpyc8b24.unit ? slider_wpyc8b24Function(slider_wpyc8b24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_wpyc8b24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`SVG Width`),
            separator:'default',
            value: slider_lph0je24,
            
            min: slider_lph0je24 && slider_lph0je24.unit ? slider_lph0je24Function(slider_lph0je24.unit, 'min') : 0,
            max: slider_lph0je24 && slider_lph0je24.unit ? slider_lph0je24Function(slider_lph0je24.unit, 'max') : 100,
            step: slider_lph0je24 && slider_lph0je24.unit ? slider_lph0je24Function(slider_lph0je24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_lph0je24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_2ivujb24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_2ivujb24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_o6gdfm24,
            onChange: (value) => setAttributes({ typography_o6gdfm24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_mds39e24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_mds39e24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_ti4a4a24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ti4a4a24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`SVG Color`),
            value: color_hdswtq24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_hdswtq24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_hcwr3z24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_hcwr3z24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_rd4der24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_rd4der24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Bodrer Radius`),
            value: dimension_t60onu24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_t60onu24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_nzl2ji24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_nzl2ji24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_8w9wmg24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8w9wmg24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_06or9n24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_06or9n24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`SVG Color`),
            value: color_ca5les24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ca5les24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_37b2um24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_37b2um24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_91b75824,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_91b75824: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_vlqyk224,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_vlqyk224: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_aol69g24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_aol69g24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_2n6tro24,
            
            min: slider_2n6tro24 && slider_2n6tro24.unit ? slider_2n6tro24Function(slider_2n6tro24.unit, 'min') : 0,
            max: slider_2n6tro24 && slider_2n6tro24.unit ? slider_2n6tro24Function(slider_2n6tro24.unit, 'max') : 100,
            step: slider_2n6tro24 && slider_2n6tro24.unit ? slider_2n6tro24Function(slider_2n6tro24.unit, 'step') : 1,
            
                unit: ['px', '%', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2n6tro24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_fakwy624,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_fakwy624: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_6kkskj24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_6kkskj24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_83ln3024,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_83ln3024: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_qqh70n24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_qqh70n24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_7eywv524,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_7eywv524: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-d0l3gf24", block_id, false, props.clientId);
                }
            }

            
let g_select_maa9l124 = select_maa9l124 && select_maa9l124 != undefined  ? select_maa9l124 : "";
let g_text_yrgf8h23 = text_yrgf8h23 && text_yrgf8h23 != undefined  ? text_yrgf8h23 : "";
let g_textarea_5s0pvo24 = textarea_5s0pvo24 && textarea_5s0pvo24 != undefined  ? textarea_5s0pvo24 : "";
let g_text_wjncvf24 = text_wjncvf24 && text_wjncvf24 != undefined  ? text_wjncvf24 : "";
let g_url_8nbad724_url = url_8nbad724?.url && url_8nbad724?.url != undefined ? url_8nbad724.url : "";
let g_url_8nbad724_target = url_8nbad724?.target && url_8nbad724?.target != undefined ? url_8nbad724.target : "";
let g_url_8nbad724_nofollow = url_8nbad724?.nofollow && url_8nbad724?.nofollow != undefined ? url_8nbad724.nofollow : "";
let g_url_8nbad724_ctmArt = url_8nbad724?.attr != undefined ? url_8nbad724.attr : "";
                    let g_url_8nbad724_attr = ''

                    if (g_url_8nbad724_ctmArt) {
                        let main_array = g_url_8nbad724_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_8nbad724_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_ysenzi24 = iconscontrol_ysenzi24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_ysenzi24+'"></i></span>' : '';

let g_choose_dayp7w24 = choose_dayp7w24 && choose_dayp7w24 != undefined  ? choose_dayp7w24 : "";
let g_choose_m609eh24 = choose_m609eh24 && choose_m609eh24 != undefined  ? choose_m609eh24 : "";
let g_number_3g0yfs24 = number_3g0yfs24 && number_3g0yfs24 != undefined  ? number_3g0yfs24 : "";
let g_number_fthuwx24 = number_fthuwx24 && number_fthuwx24 != undefined  ? number_fthuwx24 : "";
let g_number_3aeero24 = number_3aeero24 && number_3aeero24 != undefined  ? number_3aeero24 : "";
let g_heading_6bn6va24 = heading_6bn6va24 && heading_6bn6va24 != undefined  ? heading_6bn6va24 : "";
let g_heading_sp6a3x24 = heading_sp6a3x24 && heading_sp6a3x24 != undefined  ? heading_sp6a3x24 : "";
let g_typography_z30reh24 = typography_z30reh24 && typography_z30reh24 != undefined  ? typography_z30reh24 : "";
let g_color_9mrdfv24 = color_9mrdfv24 && color_9mrdfv24 != undefined  ? color_9mrdfv24 : "";
let g_heading_iuiaj924 = heading_iuiaj924 && heading_iuiaj924 != undefined  ? heading_iuiaj924 : "";
let g_typography_b3qci424 = typography_b3qci424 && typography_b3qci424 != undefined  ? typography_b3qci424 : "";
let g_color_kpvmxz24 = color_kpvmxz24 && color_kpvmxz24 != undefined  ? color_kpvmxz24 : "";
let g_dimension_3hx4sb24 = dimension_3hx4sb24 && dimension_3hx4sb24 != undefined  ? dimension_3hx4sb24 : "";
let g_choose_30vc8f24 = choose_30vc8f24 && choose_30vc8f24 != undefined && ( (select_maa9l124 == "style-2") ) ? choose_30vc8f24 : "";
let g_dimension_2ivujb24 = dimension_2ivujb24 && dimension_2ivujb24 != undefined  ? dimension_2ivujb24 : "";
let g_typography_o6gdfm24 = typography_o6gdfm24 && typography_o6gdfm24 != undefined  ? typography_o6gdfm24 : "";
let g_color_mds39e24 = color_mds39e24 && color_mds39e24 != undefined  ? color_mds39e24 : "";
let g_color_ti4a4a24 = color_ti4a4a24 && color_ti4a4a24 != undefined  ? color_ti4a4a24 : "";
let g_color_hdswtq24 = color_hdswtq24 && color_hdswtq24 != undefined  ? color_hdswtq24 : "";
let g_background_hcwr3z24 = background_hcwr3z24 && background_hcwr3z24 != undefined  ? background_hcwr3z24 : "";
let g_border_rd4der24 = border_rd4der24 && border_rd4der24 != undefined  ? border_rd4der24 : "";
let g_dimension_t60onu24 = dimension_t60onu24 && dimension_t60onu24 != undefined  ? dimension_t60onu24 : "";
let g_boxshadow_nzl2ji24 = boxshadow_nzl2ji24 && boxshadow_nzl2ji24 != undefined  ? boxshadow_nzl2ji24 : "";
let g_color_8w9wmg24 = color_8w9wmg24 && color_8w9wmg24 != undefined  ? color_8w9wmg24 : "";
let g_color_06or9n24 = color_06or9n24 && color_06or9n24 != undefined  ? color_06or9n24 : "";
let g_color_ca5les24 = color_ca5les24 && color_ca5les24 != undefined  ? color_ca5les24 : "";
let g_background_37b2um24 = background_37b2um24 && background_37b2um24 != undefined  ? background_37b2um24 : "";
let g_border_91b75824 = border_91b75824 && border_91b75824 != undefined  ? border_91b75824 : "";
let g_dimension_vlqyk224 = dimension_vlqyk224 && dimension_vlqyk224 != undefined  ? dimension_vlqyk224 : "";
let g_boxshadow_aol69g24 = boxshadow_aol69g24 && boxshadow_aol69g24 != undefined  ? boxshadow_aol69g24 : "";
let g_dimension_fakwy624 = dimension_fakwy624 && dimension_fakwy624 != undefined  ? dimension_fakwy624 : "";
let g_background_6kkskj24 = background_6kkskj24 && background_6kkskj24 != undefined  ? background_6kkskj24 : "";
let g_border_83ln3024 = border_83ln3024 && border_83ln3024 != undefined  ? border_83ln3024 : "";
let g_dimension_qqh70n24 = dimension_qqh70n24 && dimension_qqh70n24 != undefined  ? dimension_qqh70n24 : "";
let g_boxshadow_7eywv524 = boxshadow_7eywv524 && boxshadow_7eywv524 != undefined  ? boxshadow_7eywv524 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_d0l3gf24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-scroll-wrapper" desktop-scale="${g_number_3g0yfs24}" tablet-scale="${g_number_fthuwx24}" mobile-scale="${g_number_3aeero24}">
    <div class="wkit-scroll-banner">
        <h2 class="wkit-title" data-title="${g_text_yrgf8h23}">${g_text_yrgf8h23}</h2>
        <div class="wkit-scroll-banner-title-style ${g_select_maa9l124}">
         <p class="wkit-description " data-desc="${g_textarea_5s0pvo24}">${g_textarea_5s0pvo24}</p>
         <div class="wkit-scroll-banner-btn-wrap"><a href="${g_url_8nbad724_url}" target="${g_url_8nbad724_target}" class="wkit-read-more" rel="noopener">${g_text_wjncvf24}<div class="wkit-read-icon">${g_iconscontrol_ysenzi24}</div></a></div>
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
            select_maa9l124,
text_yrgf8h23,
textarea_5s0pvo24,
text_wjncvf24,
url_8nbad724,
iconscontrol_ysenzi24,
choose_dayp7w24,
choose_m609eh24,
number_3g0yfs24,
number_fthuwx24,
number_3aeero24,
heading_6bn6va24,
heading_sp6a3x24,
typography_z30reh24,
color_9mrdfv24,
slider_krejxj24,
heading_iuiaj924,
typography_b3qci424,
color_kpvmxz24,
slider_zojp0v24,
slider_d07uf624,
dimension_3hx4sb24,
choose_30vc8f24,
slider_xknxkv24,
slider_wpyc8b24,
slider_lph0je24,
dimension_2ivujb24,
typography_o6gdfm24,
color_mds39e24,
color_ti4a4a24,
color_hdswtq24,
background_hcwr3z24,
border_rd4der24,
dimension_t60onu24,
boxshadow_nzl2ji24,
color_8w9wmg24,
color_06or9n24,
color_ca5les24,
background_37b2um24,
border_91b75824,
dimension_vlqyk224,
boxshadow_aol69g24,
normalhover_wvpd0124,
slider_2n6tro24,
dimension_fakwy624,
background_6kkskj24,
border_83ln3024,
dimension_qqh70n24,
boxshadow_7eywv524,

            block_id,
        } = attributes;

        

        

        

        
let g_select_maa9l124 = select_maa9l124 && select_maa9l124 != undefined  ? select_maa9l124 : "";
let g_text_yrgf8h23 = text_yrgf8h23 && text_yrgf8h23 != undefined  ? text_yrgf8h23 : "";
let g_textarea_5s0pvo24 = textarea_5s0pvo24 && textarea_5s0pvo24 != undefined  ? textarea_5s0pvo24 : "";
let g_text_wjncvf24 = text_wjncvf24 && text_wjncvf24 != undefined  ? text_wjncvf24 : "";
let g_url_8nbad724_url = url_8nbad724?.url && url_8nbad724?.url != undefined ? url_8nbad724.url : "";
let g_url_8nbad724_target = url_8nbad724?.target && url_8nbad724?.target != undefined ? url_8nbad724.target : "";
let g_url_8nbad724_nofollow = url_8nbad724?.nofollow && url_8nbad724?.nofollow != undefined ? url_8nbad724.nofollow : "";
let g_url_8nbad724_ctmArt = url_8nbad724?.attr != undefined ? url_8nbad724.attr : "";
                    let g_url_8nbad724_attr = ''

                    if (g_url_8nbad724_ctmArt) {
                        let main_array = g_url_8nbad724_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_8nbad724_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_ysenzi24 = iconscontrol_ysenzi24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_ysenzi24+'"></i></span>' : '';

let g_choose_dayp7w24 = choose_dayp7w24 && choose_dayp7w24 != undefined  ? choose_dayp7w24 : "";
let g_choose_m609eh24 = choose_m609eh24 && choose_m609eh24 != undefined  ? choose_m609eh24 : "";
let g_number_3g0yfs24 = number_3g0yfs24 && number_3g0yfs24 != undefined  ? number_3g0yfs24 : "";
let g_number_fthuwx24 = number_fthuwx24 && number_fthuwx24 != undefined  ? number_fthuwx24 : "";
let g_number_3aeero24 = number_3aeero24 && number_3aeero24 != undefined  ? number_3aeero24 : "";
let g_heading_6bn6va24 = heading_6bn6va24 && heading_6bn6va24 != undefined  ? heading_6bn6va24 : "";
let g_heading_sp6a3x24 = heading_sp6a3x24 && heading_sp6a3x24 != undefined  ? heading_sp6a3x24 : "";
let g_typography_z30reh24 = typography_z30reh24 && typography_z30reh24 != undefined  ? typography_z30reh24 : "";
let g_color_9mrdfv24 = color_9mrdfv24 && color_9mrdfv24 != undefined  ? color_9mrdfv24 : "";
let g_heading_iuiaj924 = heading_iuiaj924 && heading_iuiaj924 != undefined  ? heading_iuiaj924 : "";
let g_typography_b3qci424 = typography_b3qci424 && typography_b3qci424 != undefined  ? typography_b3qci424 : "";
let g_color_kpvmxz24 = color_kpvmxz24 && color_kpvmxz24 != undefined  ? color_kpvmxz24 : "";
let g_dimension_3hx4sb24 = dimension_3hx4sb24 && dimension_3hx4sb24 != undefined  ? dimension_3hx4sb24 : "";
let g_choose_30vc8f24 = choose_30vc8f24 && choose_30vc8f24 != undefined && ( (select_maa9l124 == "style-2") ) ? choose_30vc8f24 : "";
let g_dimension_2ivujb24 = dimension_2ivujb24 && dimension_2ivujb24 != undefined  ? dimension_2ivujb24 : "";
let g_typography_o6gdfm24 = typography_o6gdfm24 && typography_o6gdfm24 != undefined  ? typography_o6gdfm24 : "";
let g_color_mds39e24 = color_mds39e24 && color_mds39e24 != undefined  ? color_mds39e24 : "";
let g_color_ti4a4a24 = color_ti4a4a24 && color_ti4a4a24 != undefined  ? color_ti4a4a24 : "";
let g_color_hdswtq24 = color_hdswtq24 && color_hdswtq24 != undefined  ? color_hdswtq24 : "";
let g_background_hcwr3z24 = background_hcwr3z24 && background_hcwr3z24 != undefined  ? background_hcwr3z24 : "";
let g_border_rd4der24 = border_rd4der24 && border_rd4der24 != undefined  ? border_rd4der24 : "";
let g_dimension_t60onu24 = dimension_t60onu24 && dimension_t60onu24 != undefined  ? dimension_t60onu24 : "";
let g_boxshadow_nzl2ji24 = boxshadow_nzl2ji24 && boxshadow_nzl2ji24 != undefined  ? boxshadow_nzl2ji24 : "";
let g_color_8w9wmg24 = color_8w9wmg24 && color_8w9wmg24 != undefined  ? color_8w9wmg24 : "";
let g_color_06or9n24 = color_06or9n24 && color_06or9n24 != undefined  ? color_06or9n24 : "";
let g_color_ca5les24 = color_ca5les24 && color_ca5les24 != undefined  ? color_ca5les24 : "";
let g_background_37b2um24 = background_37b2um24 && background_37b2um24 != undefined  ? background_37b2um24 : "";
let g_border_91b75824 = border_91b75824 && border_91b75824 != undefined  ? border_91b75824 : "";
let g_dimension_vlqyk224 = dimension_vlqyk224 && dimension_vlqyk224 != undefined  ? dimension_vlqyk224 : "";
let g_boxshadow_aol69g24 = boxshadow_aol69g24 && boxshadow_aol69g24 != undefined  ? boxshadow_aol69g24 : "";
let g_dimension_fakwy624 = dimension_fakwy624 && dimension_fakwy624 != undefined  ? dimension_fakwy624 : "";
let g_background_6kkskj24 = background_6kkskj24 && background_6kkskj24 != undefined  ? background_6kkskj24 : "";
let g_border_83ln3024 = border_83ln3024 && border_83ln3024 != undefined  ? border_83ln3024 : "";
let g_dimension_qqh70n24 = dimension_qqh70n24 && dimension_qqh70n24 != undefined  ? dimension_qqh70n24 : "";
let g_boxshadow_7eywv524 = boxshadow_7eywv524 && boxshadow_7eywv524 != undefined  ? boxshadow_7eywv524 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-d0l3gf24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_d0l3gf24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-scroll-wrapper" desktop-scale="${g_number_3g0yfs24}" tablet-scale="${g_number_fthuwx24}" mobile-scale="${g_number_3aeero24}">
    <div class="wkit-scroll-banner">
        <h2 class="wkit-title" data-title="${g_text_yrgf8h23}">${g_text_yrgf8h23}</h2>
        <div class="wkit-scroll-banner-title-style ${g_select_maa9l124}">
         <p class="wkit-description " data-desc="${g_textarea_5s0pvo24}">${g_textarea_5s0pvo24}</p>
         <div class="wkit-scroll-banner-btn-wrap"><a href="${g_url_8nbad724_url}" target="${g_url_8nbad724_target}" class="wkit-read-more" rel="noopener">${g_text_wjncvf24}<div class="wkit-read-icon">${g_iconscontrol_ysenzi24}</div></a></div>
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
    new Scroll_Banner_d0l3gf24();