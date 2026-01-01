
    class Scroll_Accordion_b5bsl824 {
        constructor() {
            this.Scroll_Accordion_b5bsl824_xt8q2f25();
        }
    
        Scroll_Accordion_b5bsl824_xt8q2f25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Select,Pmgc_TextArea,Pmgc_Media,Pmgc_Url,Pmgc_Label_Heading,Pmgc_Color,Pmgc_Background,Pmgc_Dimension,Pmgc_Repeater,Pmgc_RadioAdvanced,Pmgc_Typography,Pmgc_Tabs,Pmgc_Range,Pmgc_Border,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-b5bsl824', {
        title: __('Scroll Accordion'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-mouse tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Horizontal Display'),__('Horizontal Scroll'),__('Accordion Display'),__('Content Showcase'),__('Scroll Accordion'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_dadjzn24Function = (unit, type) => {
                var g_slider_dadjzn24_list = [];
                g_slider_dadjzn24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_dadjzn24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_dadjzn24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_dadjzn24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_dadjzn24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_dadjzn24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_dadjzn24_list[unit][type];
            };
const slider_61vm4524Function = (unit, type) => {
                var g_slider_61vm4524_list = [];
                g_slider_61vm4524_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_61vm4524_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_61vm4524_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_61vm4524_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_61vm4524_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_61vm4524_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_61vm4524_list[unit][type];
            };
const slider_lry7lr24Function = (unit, type) => {
                var g_slider_lry7lr24_list = [];
                g_slider_lry7lr24_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_lry7lr24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_lry7lr24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_lry7lr24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_lry7lr24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_lry7lr24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_lry7lr24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_sr5arl24,
select_uvzud924,
repeater_82xaam24,
select_g0b8j924,
choose_f3twee24,
dimension_5tx2gj24,
dimension_6ri5z524,
typography_5cclro24,
color_2oxujw24,
color_e9tuk824,
background_c8jnih24,
background_jzrf0e24,
normalhover_424w5f24,
choose_jkx7ef24,
dimension_8cz5tn24,
dimension_bfidii24,
typography_uzq28724,
color_n8do6w24,
color_e6thi224,
normalhover_uuj0b724,
dimension_syvcdy24,
dimension_88a3l424,
slider_dadjzn24,
slider_61vm4524,
border_nhzam524,
border_lfv4px24,
dimension_tbkhcb24,
dimension_y6h5yw24,
normalhover_fid59f24,
choose_96mpyw24,
dimension_dcgp7u24,
dimension_c08m6824,
typography_qcwze924,
color_nl61id24,
color_zk1n9524,
normalhover_wit2qz24,
choose_50ax2524,
dimension_opn0d924,
slider_lry7lr24,
typography_lhzh4l24,
color_cgwrf324,
color_zbg5cx24,
background_0a5udy24,
background_8jayu424,
border_rny3ed24,
border_6zvy5c24,
dimension_7j8ntw24,
dimension_0dbqon24,
boxshadow_7id4y524,
boxshadow_76sgz524,
normalhover_booiwq24,
dimension_f753p524,
dimension_rhsk2l24,
background_pxirnm24,
background_soejiz24,
border_nlojk324,
border_qre92y24,
dimension_lqn2id24,
dimension_7asgy324,
boxshadow_xw2d9u24,
boxshadow_8s5pc224,
normalhover_0myfhr24,

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
                        main_function_b5bsl824(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_b5bsl824 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let accordSlide = $scope[0].querySelectorAll('.wkit-scroll-accord-slide');

let totalHgt = window.innerHeight;

let getHgt = Number( totalHgt * 7) / 100;

let totalLength = Number(accordSlide.length - 1);

accordSlide.forEach((accordion, index) => {
    let getItemStyle = window.getComputedStyle(accordion);
    let getPadding = parseInt(getItemStyle.paddingTop);
    
    let accordTitle = accordion.querySelector('.accord-title');
    let accordTitleMain = accordion.querySelector('.wkit-scroll-accord-title');
    let halfTitleHeight = Number(accordTitle.offsetHeight);

    let ttlHgt = Number(accordTitle.offsetHeight) / 2; 
  
    let transY = (index - totalLength) * getHgt;
        transY = transY - ttlHgt * (accordSlide.length - index);
      
   accordion.style.transform = 'translateY(' + transY + 'px)';
   accordion.style.marginBottom = -accordTitleMain.offsetHeight+'px';
  
   let img = accordion.querySelector('.wkit-scroll-accord-img');
   let imgCls = accordion.querySelector('.wkit-scroll-accord-img .accord-img');
  
    if (!imgCls.getAttribute('src') || imgCls.getAttribute('src') === '') {
        img.style.display = 'none';
    }
});

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Main Title`),
                type: "text",
                value: text_sr5arl24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_sr5arl24: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Content Layout`),
                options:[['style-1',__('Horizontal')],['style-2',__('Vertical')],],
                separator:"default",
                
                
                value: select_uvzud924,
                onChange: (value) => {setAttributes({ select_uvzud924: value }) },
            }),
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Slides`),
            value: repeater_82xaam24,
            attributeName: 'repeater_82xaam24',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_82xaam24: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_ir03rf24,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.text_ir03rf24 = v; onChange(value); },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: value.textarea_w4jlfm24,
                
                onChange: v => { value.textarea_w4jlfm24 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_9vreus24,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_9vreus24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Button`),
                type: "text",
                value: value.text_wztag624,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_wztag624 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`Url`),
                type: "url",
                value: value.url_id69sx24,
                dynamic: [true, 'url_id69sx24'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.url_id69sx24 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: value.heading_e5z66j24,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_441ttj24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_a7q9fd24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_a7q9fd24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_6vrit624,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_ikh7vi24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_ikh7vi24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: value.heading_c3kuxj24,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_w3zdgr24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_l547ii24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_l547ii24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_78n9ht24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_3d7hig24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_3d7hig24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Button`),
            value: value.heading_k1mdsl24,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_n15t1524,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_booq9a24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_booq9a24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Background, {
            
            value: value.background_ut3i3724,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_ut3i3724 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_bxyvsj24,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: value.color_hmcucp24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_hmcucp24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Background, {
            
            value: value.background_nxkkq324,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_nxkkq324 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Slide`),
            value: value.heading_npukp724,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Background, {
            
            value: value.background_nb5afc24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_nb5afc24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: value.dimension_fwhbmq24,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: v => { value.dimension_fwhbmq24 = v; onChange(value); },
            
        }),

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Content Style`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],],
                separator:"default",
                
                
                value: select_g0b8j924,
                onChange: (value) => {setAttributes({ select_g0b8j924: value }) },
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Main Title"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_f3twee24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_f3twee24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_5tx2gj24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5tx2gj24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_6ri5z524,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_6ri5z524: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_5cclro24,
            onChange: (value) => setAttributes({ typography_5cclro24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_2oxujw24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_2oxujw24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_c8jnih24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_c8jnih24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_e9tuk824,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_e9tuk824: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_jzrf0e24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_jzrf0e24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Title"), initialOpen: false },
( select_uvzud924 == "style-2" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_jkx7ef24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_jkx7ef24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_8cz5tn24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_8cz5tn24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_bfidii24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_bfidii24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_uzq28724,
            onChange: (value) => setAttributes({ typography_uzq28724: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_n8do6w24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_n8do6w24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_e6thi224,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_e6thi224: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_syvcdy24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_syvcdy24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_88a3l424,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_88a3l424: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width `),
            separator:'default',
            value: slider_dadjzn24,
            
            min: slider_dadjzn24 && slider_dadjzn24.unit ? slider_dadjzn24Function(slider_dadjzn24.unit, 'min') : 0,
            max: slider_dadjzn24 && slider_dadjzn24.unit ? slider_dadjzn24Function(slider_dadjzn24.unit, 'max') : 100,
            step: slider_dadjzn24 && slider_dadjzn24.unit ? slider_dadjzn24Function(slider_dadjzn24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_dadjzn24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height `),
            separator:'default',
            value: slider_61vm4524,
            
            min: slider_61vm4524 && slider_61vm4524.unit ? slider_61vm4524Function(slider_61vm4524.unit, 'min') : 0,
            max: slider_61vm4524 && slider_61vm4524.unit ? slider_61vm4524Function(slider_61vm4524.unit, 'max') : 100,
            step: slider_61vm4524 && slider_61vm4524.unit ? slider_61vm4524Function(slider_61vm4524.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_61vm4524: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_nhzam524,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_nhzam524: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_tbkhcb24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_tbkhcb24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_lfv4px24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_lfv4px24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_y6h5yw24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_y6h5yw24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), 
), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
( select_uvzud924 == "style-2" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_96mpyw24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_96mpyw24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_dcgp7u24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_dcgp7u24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_c08m6824,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_c08m6824: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_qcwze924,
            onChange: (value) => setAttributes({ typography_qcwze924: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_nl61id24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_nl61id24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_zk1n9524,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_zk1n9524: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
( select_uvzud924 == "style-2" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_50ax2524,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_50ax2524: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_opn0d924,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_opn0d924: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Top Space (px)`),
            separator:'default',
            value: slider_lry7lr24,
            
            min: slider_lry7lr24 && slider_lry7lr24.unit ? slider_lry7lr24Function(slider_lry7lr24.unit, 'min') : 0,
            max: slider_lry7lr24 && slider_lry7lr24.unit ? slider_lry7lr24Function(slider_lry7lr24.unit, 'max') : 100,
            step: slider_lry7lr24 && slider_lry7lr24.unit ? slider_lry7lr24Function(slider_lry7lr24.unit, 'step') : 1,
            
            onChange: (value) => setAttributes({ slider_lry7lr24: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_lhzh4l24,
            onChange: (value) => setAttributes({ typography_lhzh4l24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_cgwrf324,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cgwrf324: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_0a5udy24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_0a5udy24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_rny3ed24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_rny3ed24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_7j8ntw24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_7j8ntw24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_7id4y524,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_7id4y524: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_zbg5cx24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_zbg5cx24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_8jayu424,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_8jayu424: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_6zvy5c24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_6zvy5c24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_0dbqon24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_0dbqon24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_76sgz524,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_76sgz524: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_f753p524,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_f753p524: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_rhsk2l24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_rhsk2l24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_soejiz24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_soejiz24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_nlojk324,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_nlojk324: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_lqn2id24,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_lqn2id24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_xw2d9u24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_xw2d9u24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_pxirnm24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_pxirnm24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_qre92y24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_qre92y24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_7asgy324,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_7asgy324: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_8s5pc224,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_8s5pc224: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-b5bsl824", block_id, false, props.clientId);
                }
            }

            
let g_text_sr5arl24 = text_sr5arl24 && text_sr5arl24 != undefined  ? text_sr5arl24 : "";
let g_select_uvzud924 = select_uvzud924 && select_uvzud924 != undefined  ? select_uvzud924 : "";
let g_select_g0b8j924 = select_g0b8j924 && select_g0b8j924 != undefined  ? select_g0b8j924 : "";
let g_choose_f3twee24 = choose_f3twee24 && choose_f3twee24 != undefined  ? choose_f3twee24 : "";
let g_dimension_5tx2gj24 = dimension_5tx2gj24 && dimension_5tx2gj24 != undefined  ? dimension_5tx2gj24 : "";
let g_dimension_6ri5z524 = dimension_6ri5z524 && dimension_6ri5z524 != undefined  ? dimension_6ri5z524 : "";
let g_typography_5cclro24 = typography_5cclro24 && typography_5cclro24 != undefined  ? typography_5cclro24 : "";
let g_color_2oxujw24 = color_2oxujw24 && color_2oxujw24 != undefined  ? color_2oxujw24 : "";
let g_color_e9tuk824 = color_e9tuk824 && color_e9tuk824 != undefined  ? color_e9tuk824 : "";
let g_background_c8jnih24 = background_c8jnih24 && background_c8jnih24 != undefined  ? background_c8jnih24 : "";
let g_background_jzrf0e24 = background_jzrf0e24 && background_jzrf0e24 != undefined  ? background_jzrf0e24 : "";
let g_choose_jkx7ef24 = choose_jkx7ef24 && choose_jkx7ef24 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_jkx7ef24 : "";
let g_dimension_8cz5tn24 = dimension_8cz5tn24 && dimension_8cz5tn24 != undefined  ? dimension_8cz5tn24 : "";
let g_dimension_bfidii24 = dimension_bfidii24 && dimension_bfidii24 != undefined  ? dimension_bfidii24 : "";
let g_typography_uzq28724 = typography_uzq28724 && typography_uzq28724 != undefined  ? typography_uzq28724 : "";
let g_color_n8do6w24 = color_n8do6w24 && color_n8do6w24 != undefined  ? color_n8do6w24 : "";
let g_color_e6thi224 = color_e6thi224 && color_e6thi224 != undefined  ? color_e6thi224 : "";
let g_dimension_syvcdy24 = dimension_syvcdy24 && dimension_syvcdy24 != undefined  ? dimension_syvcdy24 : "";
let g_dimension_88a3l424 = dimension_88a3l424 && dimension_88a3l424 != undefined  ? dimension_88a3l424 : "";
let g_border_nhzam524 = border_nhzam524 && border_nhzam524 != undefined  ? border_nhzam524 : "";
let g_border_lfv4px24 = border_lfv4px24 && border_lfv4px24 != undefined  ? border_lfv4px24 : "";
let g_dimension_tbkhcb24 = dimension_tbkhcb24 && dimension_tbkhcb24 != undefined  ? dimension_tbkhcb24 : "";
let g_dimension_y6h5yw24 = dimension_y6h5yw24 && dimension_y6h5yw24 != undefined  ? dimension_y6h5yw24 : "";
let g_choose_96mpyw24 = choose_96mpyw24 && choose_96mpyw24 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_96mpyw24 : "";
let g_dimension_dcgp7u24 = dimension_dcgp7u24 && dimension_dcgp7u24 != undefined  ? dimension_dcgp7u24 : "";
let g_dimension_c08m6824 = dimension_c08m6824 && dimension_c08m6824 != undefined  ? dimension_c08m6824 : "";
let g_typography_qcwze924 = typography_qcwze924 && typography_qcwze924 != undefined  ? typography_qcwze924 : "";
let g_color_nl61id24 = color_nl61id24 && color_nl61id24 != undefined  ? color_nl61id24 : "";
let g_color_zk1n9524 = color_zk1n9524 && color_zk1n9524 != undefined  ? color_zk1n9524 : "";
let g_choose_50ax2524 = choose_50ax2524 && choose_50ax2524 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_50ax2524 : "";
let g_dimension_opn0d924 = dimension_opn0d924 && dimension_opn0d924 != undefined  ? dimension_opn0d924 : "";
let g_typography_lhzh4l24 = typography_lhzh4l24 && typography_lhzh4l24 != undefined  ? typography_lhzh4l24 : "";
let g_color_cgwrf324 = color_cgwrf324 && color_cgwrf324 != undefined  ? color_cgwrf324 : "";
let g_color_zbg5cx24 = color_zbg5cx24 && color_zbg5cx24 != undefined  ? color_zbg5cx24 : "";
let g_background_0a5udy24 = background_0a5udy24 && background_0a5udy24 != undefined  ? background_0a5udy24 : "";
let g_background_8jayu424 = background_8jayu424 && background_8jayu424 != undefined  ? background_8jayu424 : "";
let g_border_rny3ed24 = border_rny3ed24 && border_rny3ed24 != undefined  ? border_rny3ed24 : "";
let g_border_6zvy5c24 = border_6zvy5c24 && border_6zvy5c24 != undefined  ? border_6zvy5c24 : "";
let g_dimension_7j8ntw24 = dimension_7j8ntw24 && dimension_7j8ntw24 != undefined  ? dimension_7j8ntw24 : "";
let g_dimension_0dbqon24 = dimension_0dbqon24 && dimension_0dbqon24 != undefined  ? dimension_0dbqon24 : "";
let g_boxshadow_7id4y524 = boxshadow_7id4y524 && boxshadow_7id4y524 != undefined  ? boxshadow_7id4y524 : "";
let g_boxshadow_76sgz524 = boxshadow_76sgz524 && boxshadow_76sgz524 != undefined  ? boxshadow_76sgz524 : "";
let g_dimension_f753p524 = dimension_f753p524 && dimension_f753p524 != undefined  ? dimension_f753p524 : "";
let g_dimension_rhsk2l24 = dimension_rhsk2l24 && dimension_rhsk2l24 != undefined  ? dimension_rhsk2l24 : "";
let g_background_pxirnm24 = background_pxirnm24 && background_pxirnm24 != undefined  ? background_pxirnm24 : "";
let g_background_soejiz24 = background_soejiz24 && background_soejiz24 != undefined  ? background_soejiz24 : "";
let g_border_nlojk324 = border_nlojk324 && border_nlojk324 != undefined  ? border_nlojk324 : "";
let g_border_qre92y24 = border_qre92y24 && border_qre92y24 != undefined  ? border_qre92y24 : "";
let g_dimension_lqn2id24 = dimension_lqn2id24 && dimension_lqn2id24 != undefined  ? dimension_lqn2id24 : "";
let g_dimension_7asgy324 = dimension_7asgy324 && dimension_7asgy324 != undefined  ? dimension_7asgy324 : "";
let g_boxshadow_xw2d9u24 = boxshadow_xw2d9u24 && boxshadow_xw2d9u24 != undefined  ? boxshadow_xw2d9u24 : "";
let g_boxshadow_8s5pc224 = boxshadow_8s5pc224 && boxshadow_8s5pc224 != undefined  ? boxshadow_8s5pc224 : "";
            
let repeater_82xaam24_5725 = "";
                            
repeater_82xaam24  && repeater_82xaam24.map((r_item, index) => {
                                
let grnp_text_ir03rf24 = r_item.text_ir03rf24  ? r_item.text_ir03rf24 : "";
let grnp_textarea_w4jlfm24 = r_item.textarea_w4jlfm24  ? r_item.textarea_w4jlfm24 : "";
let grnp_media_9vreus24 = r_item?.media_9vreus24?.url != undefined  ? r_item?.media_9vreus24.url : "";
let grnp_text_wztag624 = r_item.text_wztag624  ? r_item.text_wztag624 : "";
let grnp_url_id69sx24_url = r_item?.url_id69sx24?.url && r_item?.url_id69sx24?.url != undefined ?  r_item?.url_id69sx24.url : "";
let grnp_url_id69sx24_target = r_item?.url_id69sx24?.target && r_item?.url_id69sx24?.target != undefined ?  r_item?.url_id69sx24.target : "";
let grnp_url_id69sx24_nofollow = r_item?.url_id69sx24?.nofollow && r_item?.url_id69sx24?.nofollow != undefined ?  r_item?.url_id69sx24.nofollow : "";
let grnp_url_id69sx24_ctmArt = r_item?.url_id69sx24?.attr && r_item?.url_id69sx24?.attr != undefined ?  r_item?.url_id69sx24.attr : "";
                    let grnp_url_id69sx24_attr = ''

                    if (grnp_url_id69sx24_ctmArt) {
                        let main_array = grnp_url_id69sx24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_id69sx24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_e5z66j24 = r_item.heading_e5z66j24  ? r_item.heading_e5z66j24 : "";
let grnp_heading_441ttj24 = r_item.heading_441ttj24  ? r_item.heading_441ttj24 : "";
let grnp_color_a7q9fd24 = r_item.color_a7q9fd24  ? r_item.color_a7q9fd24 : "";
let grnp_heading_6vrit624 = r_item.heading_6vrit624  ? r_item.heading_6vrit624 : "";
let grnp_color_ikh7vi24 = r_item.color_ikh7vi24  ? r_item.color_ikh7vi24 : "";
let grnp_heading_c3kuxj24 = r_item.heading_c3kuxj24  ? r_item.heading_c3kuxj24 : "";
let grnp_heading_w3zdgr24 = r_item.heading_w3zdgr24  ? r_item.heading_w3zdgr24 : "";
let grnp_color_l547ii24 = r_item.color_l547ii24  ? r_item.color_l547ii24 : "";
let grnp_heading_78n9ht24 = r_item.heading_78n9ht24  ? r_item.heading_78n9ht24 : "";
let grnp_color_3d7hig24 = r_item.color_3d7hig24  ? r_item.color_3d7hig24 : "";
let grnp_heading_k1mdsl24 = r_item.heading_k1mdsl24  ? r_item.heading_k1mdsl24 : "";
let grnp_heading_n15t1524 = r_item.heading_n15t1524  ? r_item.heading_n15t1524 : "";
let grnp_color_booq9a24 = r_item.color_booq9a24  ? r_item.color_booq9a24 : "";
let grnp_background_ut3i3724 = r_item.background_ut3i3724  ? r_item.background_ut3i3724 : "";
let grnp_heading_bxyvsj24 = r_item.heading_bxyvsj24  ? r_item.heading_bxyvsj24 : "";
let grnp_color_hmcucp24 = r_item.color_hmcucp24  ? r_item.color_hmcucp24 : "";
let grnp_background_nxkkq324 = r_item.background_nxkkq324  ? r_item.background_nxkkq324 : "";
let grnp_heading_npukp724 = r_item.heading_npukp724  ? r_item.heading_npukp724 : "";
let grnp_background_nb5afc24 = r_item.background_nb5afc24  ? r_item.background_nb5afc24 : "";
let grnp_dimension_fwhbmq24 = r_item.dimension_fwhbmq24  ? r_item.dimension_fwhbmq24 : "";
                                repeater_82xaam24_5725 += `<div class="tp-repeater-item-${r_item._key} wkit-scroll-accord-slide" data-repeater_82xaam24="{repeater_82xaam24}">
            <div class="wkit-scroll-accord-content  scroll-cnt-${g_select_uvzud924}">
                <div class="wkit-scroll-accord-title">
                  <span class="accord-title" data-ttl="${grnp_text_ir03rf24}">${grnp_text_ir03rf24}</span>
                </div>
                <div class="wkit-scroll-accord-dib cnt-${g_select_g0b8j924}">
                    <div class="wkit-scroll-accord-img" data-img="${grnp_media_9vreus24}">
                        <img class="accord-img" src="${grnp_media_9vreus24}">
                    </div>
                    <div class="wkit-scroll-accord-desc">
                      <p class="accord-desc" data-desc="${grnp_textarea_w4jlfm24}">${grnp_textarea_w4jlfm24}</p>
                      <a class="wkit-scroll-accord-btn" data-btntext="${grnp_text_wztag624}" href="${grnp_url_id69sx24_url}" target="${grnp_url_id69sx24_target}" rel="${grnp_url_id69sx24_nofollow} noopener">
                        ${grnp_text_wztag624}
                      </a>
                    </div>
                </div>
            </div>
        </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_b5bsl824 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-scroll-accord-wrapper">
    <div class="wkit-scroll-main-title">
        <span class="scroll-main-title" data-mttl="${g_text_sr5arl24}">${g_text_sr5arl24}</span>
    </div>
    <div class="wkit-scroll-accord-inn-wrapper">
        ${repeater_82xaam24_5725}
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
            text_sr5arl24,
select_uvzud924,
repeater_82xaam24,
select_g0b8j924,
choose_f3twee24,
dimension_5tx2gj24,
dimension_6ri5z524,
typography_5cclro24,
color_2oxujw24,
color_e9tuk824,
background_c8jnih24,
background_jzrf0e24,
normalhover_424w5f24,
choose_jkx7ef24,
dimension_8cz5tn24,
dimension_bfidii24,
typography_uzq28724,
color_n8do6w24,
color_e6thi224,
normalhover_uuj0b724,
dimension_syvcdy24,
dimension_88a3l424,
slider_dadjzn24,
slider_61vm4524,
border_nhzam524,
border_lfv4px24,
dimension_tbkhcb24,
dimension_y6h5yw24,
normalhover_fid59f24,
choose_96mpyw24,
dimension_dcgp7u24,
dimension_c08m6824,
typography_qcwze924,
color_nl61id24,
color_zk1n9524,
normalhover_wit2qz24,
choose_50ax2524,
dimension_opn0d924,
slider_lry7lr24,
typography_lhzh4l24,
color_cgwrf324,
color_zbg5cx24,
background_0a5udy24,
background_8jayu424,
border_rny3ed24,
border_6zvy5c24,
dimension_7j8ntw24,
dimension_0dbqon24,
boxshadow_7id4y524,
boxshadow_76sgz524,
normalhover_booiwq24,
dimension_f753p524,
dimension_rhsk2l24,
background_pxirnm24,
background_soejiz24,
border_nlojk324,
border_qre92y24,
dimension_lqn2id24,
dimension_7asgy324,
boxshadow_xw2d9u24,
boxshadow_8s5pc224,
normalhover_0myfhr24,

            block_id,
        } = attributes;

        

        

        

        
let g_text_sr5arl24 = text_sr5arl24 && text_sr5arl24 != undefined  ? text_sr5arl24 : "";
let g_select_uvzud924 = select_uvzud924 && select_uvzud924 != undefined  ? select_uvzud924 : "";
let g_select_g0b8j924 = select_g0b8j924 && select_g0b8j924 != undefined  ? select_g0b8j924 : "";
let g_choose_f3twee24 = choose_f3twee24 && choose_f3twee24 != undefined  ? choose_f3twee24 : "";
let g_dimension_5tx2gj24 = dimension_5tx2gj24 && dimension_5tx2gj24 != undefined  ? dimension_5tx2gj24 : "";
let g_dimension_6ri5z524 = dimension_6ri5z524 && dimension_6ri5z524 != undefined  ? dimension_6ri5z524 : "";
let g_typography_5cclro24 = typography_5cclro24 && typography_5cclro24 != undefined  ? typography_5cclro24 : "";
let g_color_2oxujw24 = color_2oxujw24 && color_2oxujw24 != undefined  ? color_2oxujw24 : "";
let g_color_e9tuk824 = color_e9tuk824 && color_e9tuk824 != undefined  ? color_e9tuk824 : "";
let g_background_c8jnih24 = background_c8jnih24 && background_c8jnih24 != undefined  ? background_c8jnih24 : "";
let g_background_jzrf0e24 = background_jzrf0e24 && background_jzrf0e24 != undefined  ? background_jzrf0e24 : "";
let g_choose_jkx7ef24 = choose_jkx7ef24 && choose_jkx7ef24 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_jkx7ef24 : "";
let g_dimension_8cz5tn24 = dimension_8cz5tn24 && dimension_8cz5tn24 != undefined  ? dimension_8cz5tn24 : "";
let g_dimension_bfidii24 = dimension_bfidii24 && dimension_bfidii24 != undefined  ? dimension_bfidii24 : "";
let g_typography_uzq28724 = typography_uzq28724 && typography_uzq28724 != undefined  ? typography_uzq28724 : "";
let g_color_n8do6w24 = color_n8do6w24 && color_n8do6w24 != undefined  ? color_n8do6w24 : "";
let g_color_e6thi224 = color_e6thi224 && color_e6thi224 != undefined  ? color_e6thi224 : "";
let g_dimension_syvcdy24 = dimension_syvcdy24 && dimension_syvcdy24 != undefined  ? dimension_syvcdy24 : "";
let g_dimension_88a3l424 = dimension_88a3l424 && dimension_88a3l424 != undefined  ? dimension_88a3l424 : "";
let g_border_nhzam524 = border_nhzam524 && border_nhzam524 != undefined  ? border_nhzam524 : "";
let g_border_lfv4px24 = border_lfv4px24 && border_lfv4px24 != undefined  ? border_lfv4px24 : "";
let g_dimension_tbkhcb24 = dimension_tbkhcb24 && dimension_tbkhcb24 != undefined  ? dimension_tbkhcb24 : "";
let g_dimension_y6h5yw24 = dimension_y6h5yw24 && dimension_y6h5yw24 != undefined  ? dimension_y6h5yw24 : "";
let g_choose_96mpyw24 = choose_96mpyw24 && choose_96mpyw24 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_96mpyw24 : "";
let g_dimension_dcgp7u24 = dimension_dcgp7u24 && dimension_dcgp7u24 != undefined  ? dimension_dcgp7u24 : "";
let g_dimension_c08m6824 = dimension_c08m6824 && dimension_c08m6824 != undefined  ? dimension_c08m6824 : "";
let g_typography_qcwze924 = typography_qcwze924 && typography_qcwze924 != undefined  ? typography_qcwze924 : "";
let g_color_nl61id24 = color_nl61id24 && color_nl61id24 != undefined  ? color_nl61id24 : "";
let g_color_zk1n9524 = color_zk1n9524 && color_zk1n9524 != undefined  ? color_zk1n9524 : "";
let g_choose_50ax2524 = choose_50ax2524 && choose_50ax2524 != undefined && ( (select_uvzud924 == "style-2") ) ? choose_50ax2524 : "";
let g_dimension_opn0d924 = dimension_opn0d924 && dimension_opn0d924 != undefined  ? dimension_opn0d924 : "";
let g_typography_lhzh4l24 = typography_lhzh4l24 && typography_lhzh4l24 != undefined  ? typography_lhzh4l24 : "";
let g_color_cgwrf324 = color_cgwrf324 && color_cgwrf324 != undefined  ? color_cgwrf324 : "";
let g_color_zbg5cx24 = color_zbg5cx24 && color_zbg5cx24 != undefined  ? color_zbg5cx24 : "";
let g_background_0a5udy24 = background_0a5udy24 && background_0a5udy24 != undefined  ? background_0a5udy24 : "";
let g_background_8jayu424 = background_8jayu424 && background_8jayu424 != undefined  ? background_8jayu424 : "";
let g_border_rny3ed24 = border_rny3ed24 && border_rny3ed24 != undefined  ? border_rny3ed24 : "";
let g_border_6zvy5c24 = border_6zvy5c24 && border_6zvy5c24 != undefined  ? border_6zvy5c24 : "";
let g_dimension_7j8ntw24 = dimension_7j8ntw24 && dimension_7j8ntw24 != undefined  ? dimension_7j8ntw24 : "";
let g_dimension_0dbqon24 = dimension_0dbqon24 && dimension_0dbqon24 != undefined  ? dimension_0dbqon24 : "";
let g_boxshadow_7id4y524 = boxshadow_7id4y524 && boxshadow_7id4y524 != undefined  ? boxshadow_7id4y524 : "";
let g_boxshadow_76sgz524 = boxshadow_76sgz524 && boxshadow_76sgz524 != undefined  ? boxshadow_76sgz524 : "";
let g_dimension_f753p524 = dimension_f753p524 && dimension_f753p524 != undefined  ? dimension_f753p524 : "";
let g_dimension_rhsk2l24 = dimension_rhsk2l24 && dimension_rhsk2l24 != undefined  ? dimension_rhsk2l24 : "";
let g_background_pxirnm24 = background_pxirnm24 && background_pxirnm24 != undefined  ? background_pxirnm24 : "";
let g_background_soejiz24 = background_soejiz24 && background_soejiz24 != undefined  ? background_soejiz24 : "";
let g_border_nlojk324 = border_nlojk324 && border_nlojk324 != undefined  ? border_nlojk324 : "";
let g_border_qre92y24 = border_qre92y24 && border_qre92y24 != undefined  ? border_qre92y24 : "";
let g_dimension_lqn2id24 = dimension_lqn2id24 && dimension_lqn2id24 != undefined  ? dimension_lqn2id24 : "";
let g_dimension_7asgy324 = dimension_7asgy324 && dimension_7asgy324 != undefined  ? dimension_7asgy324 : "";
let g_boxshadow_xw2d9u24 = boxshadow_xw2d9u24 && boxshadow_xw2d9u24 != undefined  ? boxshadow_xw2d9u24 : "";
let g_boxshadow_8s5pc224 = boxshadow_8s5pc224 && boxshadow_8s5pc224 != undefined  ? boxshadow_8s5pc224 : "";
        
let repeater_82xaam24_5725 = "";
                            
repeater_82xaam24  && repeater_82xaam24.map((r_item, index) => {
                                
let grnp_text_ir03rf24 = r_item.text_ir03rf24  ? r_item.text_ir03rf24 : "";
let grnp_textarea_w4jlfm24 = r_item.textarea_w4jlfm24  ? r_item.textarea_w4jlfm24 : "";
let grnp_media_9vreus24 = r_item?.media_9vreus24?.url != undefined  ? r_item?.media_9vreus24.url : "";
let grnp_text_wztag624 = r_item.text_wztag624  ? r_item.text_wztag624 : "";
let grnp_url_id69sx24_url = r_item?.url_id69sx24?.url && r_item?.url_id69sx24?.url != undefined ?  r_item?.url_id69sx24.url : "";
let grnp_url_id69sx24_target = r_item?.url_id69sx24?.target && r_item?.url_id69sx24?.target != undefined ?  r_item?.url_id69sx24.target : "";
let grnp_url_id69sx24_nofollow = r_item?.url_id69sx24?.nofollow && r_item?.url_id69sx24?.nofollow != undefined ?  r_item?.url_id69sx24.nofollow : "";
let grnp_url_id69sx24_ctmArt = r_item?.url_id69sx24?.attr && r_item?.url_id69sx24?.attr != undefined ?  r_item?.url_id69sx24.attr : "";
                    let grnp_url_id69sx24_attr = ''

                    if (grnp_url_id69sx24_ctmArt) {
                        let main_array = grnp_url_id69sx24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_id69sx24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_heading_e5z66j24 = r_item.heading_e5z66j24  ? r_item.heading_e5z66j24 : "";
let grnp_heading_441ttj24 = r_item.heading_441ttj24  ? r_item.heading_441ttj24 : "";
let grnp_color_a7q9fd24 = r_item.color_a7q9fd24  ? r_item.color_a7q9fd24 : "";
let grnp_heading_6vrit624 = r_item.heading_6vrit624  ? r_item.heading_6vrit624 : "";
let grnp_color_ikh7vi24 = r_item.color_ikh7vi24  ? r_item.color_ikh7vi24 : "";
let grnp_heading_c3kuxj24 = r_item.heading_c3kuxj24  ? r_item.heading_c3kuxj24 : "";
let grnp_heading_w3zdgr24 = r_item.heading_w3zdgr24  ? r_item.heading_w3zdgr24 : "";
let grnp_color_l547ii24 = r_item.color_l547ii24  ? r_item.color_l547ii24 : "";
let grnp_heading_78n9ht24 = r_item.heading_78n9ht24  ? r_item.heading_78n9ht24 : "";
let grnp_color_3d7hig24 = r_item.color_3d7hig24  ? r_item.color_3d7hig24 : "";
let grnp_heading_k1mdsl24 = r_item.heading_k1mdsl24  ? r_item.heading_k1mdsl24 : "";
let grnp_heading_n15t1524 = r_item.heading_n15t1524  ? r_item.heading_n15t1524 : "";
let grnp_color_booq9a24 = r_item.color_booq9a24  ? r_item.color_booq9a24 : "";
let grnp_background_ut3i3724 = r_item.background_ut3i3724  ? r_item.background_ut3i3724 : "";
let grnp_heading_bxyvsj24 = r_item.heading_bxyvsj24  ? r_item.heading_bxyvsj24 : "";
let grnp_color_hmcucp24 = r_item.color_hmcucp24  ? r_item.color_hmcucp24 : "";
let grnp_background_nxkkq324 = r_item.background_nxkkq324  ? r_item.background_nxkkq324 : "";
let grnp_heading_npukp724 = r_item.heading_npukp724  ? r_item.heading_npukp724 : "";
let grnp_background_nb5afc24 = r_item.background_nb5afc24  ? r_item.background_nb5afc24 : "";
let grnp_dimension_fwhbmq24 = r_item.dimension_fwhbmq24  ? r_item.dimension_fwhbmq24 : "";
                                repeater_82xaam24_5725 += `<div class="tp-repeater-item-${r_item._key} wkit-scroll-accord-slide" data-repeater_82xaam24="{repeater_82xaam24}">
            <div class="wkit-scroll-accord-content  scroll-cnt-${g_select_uvzud924}">
                <div class="wkit-scroll-accord-title">
                  <span class="accord-title" data-ttl="${grnp_text_ir03rf24}">${grnp_text_ir03rf24}</span>
                </div>
                <div class="wkit-scroll-accord-dib cnt-${g_select_g0b8j924}">
                    <div class="wkit-scroll-accord-img" data-img="${grnp_media_9vreus24}">
                        <img class="accord-img" src="${grnp_media_9vreus24}">
                    </div>
                    <div class="wkit-scroll-accord-desc">
                      <p class="accord-desc" data-desc="${grnp_textarea_w4jlfm24}">${grnp_textarea_w4jlfm24}</p>
                      <a class="wkit-scroll-accord-btn" data-btntext="${grnp_text_wztag624}" href="${grnp_url_id69sx24_url}" target="${grnp_url_id69sx24_target}" rel="${grnp_url_id69sx24_nofollow} noopener">
                        ${grnp_text_wztag624}
                      </a>
                    </div>
                </div>
            </div>
        </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-b5bsl824", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_b5bsl824 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-scroll-accord-wrapper">
    <div class="wkit-scroll-main-title">
        <span class="scroll-main-title" data-mttl="${g_text_sr5arl24}">${g_text_sr5arl24}</span>
    </div>
    <div class="wkit-scroll-accord-inn-wrapper">
        ${repeater_82xaam24_5725}
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
    new Scroll_Accordion_b5bsl824();