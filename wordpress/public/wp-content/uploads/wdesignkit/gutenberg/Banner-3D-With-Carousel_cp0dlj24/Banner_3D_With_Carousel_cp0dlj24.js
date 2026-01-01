
    class Banner_3D_With_Carousel_cp0dlj24 {
        constructor() {
            this.Banner_3D_With_Carousel_cp0dlj24_t4ukb725();
        }
    
        Banner_3D_With_Carousel_cp0dlj24_t4ukb725() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Repeater,Pmgc_Text,Pmgc_IconList,Pmgc_Url,Pmgc_Toggle,Pmgc_Select,Pmgc_Range,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_Dimension,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-cp0dlj24', {
        title: __('Banner 3D With Carousel'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-pager tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Carousel Functionality'),__('3D Banner Carousel'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_4ti6kk23Function = (unit, type) => {
                var g_slider_4ti6kk23_list = [];
                g_slider_4ti6kk23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_4ti6kk23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_4ti6kk23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_4ti6kk23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_4ti6kk23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_4ti6kk23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_4ti6kk23_list[unit][type];
            };
const slider_5hzq2r23Function = (unit, type) => {
                var g_slider_5hzq2r23_list = [];
                g_slider_5hzq2r23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_5hzq2r23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_5hzq2r23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5hzq2r23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5hzq2r23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5hzq2r23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5hzq2r23_list[unit][type];
            };
const slider_t8ll0e23Function = (unit, type) => {
                var g_slider_t8ll0e23_list = [];
                g_slider_t8ll0e23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_t8ll0e23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_t8ll0e23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_t8ll0e23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_t8ll0e23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_t8ll0e23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_t8ll0e23_list[unit][type];
            };
const slider_pa81nu23Function = (unit, type) => {
                var g_slider_pa81nu23_list = [];
                g_slider_pa81nu23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_pa81nu23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_pa81nu23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_pa81nu23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_pa81nu23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_pa81nu23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_pa81nu23_list[unit][type];
            };
const slider_r5rjhe23Function = (unit, type) => {
                var g_slider_r5rjhe23_list = [];
                g_slider_r5rjhe23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_r5rjhe23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_r5rjhe23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_r5rjhe23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_r5rjhe23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_r5rjhe23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_r5rjhe23_list[unit][type];
            };
const slider_2jq1bd23Function = (unit, type) => {
                var g_slider_2jq1bd23_list = [];
                g_slider_2jq1bd23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_2jq1bd23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_2jq1bd23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2jq1bd23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2jq1bd23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2jq1bd23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2jq1bd23_list[unit][type];
            };
const slider_1434fi23Function = (unit, type) => {
                var g_slider_1434fi23_list = [];
                g_slider_1434fi23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_1434fi23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_1434fi23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_1434fi23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_1434fi23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_1434fi23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_1434fi23_list[unit][type];
            };
const slider_11yb9r24Function = (unit, type) => {
                var g_slider_11yb9r24_list = [];
                g_slider_11yb9r24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_11yb9r24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_11yb9r24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_11yb9r24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_11yb9r24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_11yb9r24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_11yb9r24_list[unit][type];
            };
const slider_gg19h423Function = (unit, type) => {
                var g_slider_gg19h423_list = [];
                g_slider_gg19h423_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_gg19h423_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_gg19h423_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_gg19h423_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_gg19h423_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_gg19h423_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_gg19h423_list[unit][type];
            };
const slider_77vw0223Function = (unit, type) => {
                var g_slider_77vw0223_list = [];
                g_slider_77vw0223_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_77vw0223_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_77vw0223_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_77vw0223_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_77vw0223_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_77vw0223_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_77vw0223_list[unit][type];
            };
const slider_p79h6523Function = (unit, type) => {
                var g_slider_p79h6523_list = [];
                g_slider_p79h6523_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_p79h6523_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_p79h6523_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_p79h6523_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_p79h6523_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_p79h6523_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_p79h6523_list[unit][type];
            };
const slider_02718v23Function = (unit, type) => {
                var g_slider_02718v23_list = [];
                g_slider_02718v23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_02718v23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_02718v23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_02718v23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_02718v23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_02718v23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_02718v23_list[unit][type];
            };
const slider_a7vfig23Function = (unit, type) => {
                var g_slider_a7vfig23_list = [];
                g_slider_a7vfig23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_a7vfig23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_a7vfig23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_a7vfig23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_a7vfig23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_a7vfig23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_a7vfig23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_27pwea23,
text_69qbko23,
text_hrlhn723,
iconscontrol_rj2zaq23,
url_awskqt23,
switcher_bwncwo23,
select_zelziu23,
slider_4ti6kk23,
slider_5hzq2r23,
typography_fp3w7s23,
color_8jyod623,
slider_t8ll0e23,
slider_pa81nu23,
background_zyhwm223,
border_1etufw23,
dimension_9mou2f23,
typography_q6qnt923,
slider_r5rjhe23,
slider_2jq1bd23,
slider_1434fi23,
slider_11yb9r24,
slider_gg19h423,
slider_77vw0223,
color_38ls0223,
color_a8b17k23,
background_qm19p523,
border_hcof4z23,
boxshadow_78qwh323,
color_2ogt6b23,
color_q3t4d323,
background_ogbchk23,
border_kqx21a23,
boxshadow_hchi9h23,
normalhover_e59se623,
slider_p79h6523,
slider_02718v23,
slider_a7vfig23,
color_8pfe5y23,
background_u64xdg23,
border_agg1jv23,
dimension_ybjj7223,
boxshadow_73n6ak23,
color_5cu1qd23,
background_lxtc3b23,
border_pk1a8w23,
dimension_zs3olu23,
boxshadow_kit0yu23,
normalhover_wz9lkp23,

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
                        main_function_cp0dlj24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_cp0dlj24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                VanillaTilt.init($scope[0].querySelectorAll(".banner-3d-inner"));
let splidea = $scope[0].querySelector(".wkit-banner-3d-with-carousel");
 var unid = Math.random().toString(32).slice(2);
splidea.setAttribute("id",'wkit'+unid);
$scope[0].style.width = '100%';

new Splide( '#wkit'+unid,  {
  type   : 'loop',
  pagination : false,
  drag: false,
  arrows: true,
  perPage : 1
}).mount();
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(` Images`),
            value: repeater_27pwea23,
            attributeName: 'repeater_27pwea23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_27pwea23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_d8rrzd23,
                dynamic: [true, 'media_d8rrzd23'],
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_d8rrzd23 = v; onChange(value); },
            }), 

                    )]
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_69qbko23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_69qbko23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Title`),
                type: "text",
                value: text_hrlhn723,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_hrlhn723: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_rj2zaq23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_rj2zaq23: value }),
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`Url`),
                type: "url",
                value: url_awskqt23,
                dynamic: [true, 'url_awskqt23'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_awskqt23: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Hide Arrow`),
            
            value: switcher_bwncwo23,
            
            onChange: (value) => setAttributes({ switcher_bwncwo23: value }),
            }), 
( !switcher_bwncwo23 ) && React.createElement(Pmgc_Select, {
                label: __(`Arrow Style`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],],
                separator:"default",
                
                
                value: select_zelziu23,
                onChange: (value) => {setAttributes({ select_zelziu23: value }) },
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
( text_69qbko23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_4ti6kk23,
            
            min: slider_4ti6kk23 && slider_4ti6kk23.unit ? slider_4ti6kk23Function(slider_4ti6kk23.unit, 'min') : 0,
            max: slider_4ti6kk23 && slider_4ti6kk23.unit ? slider_4ti6kk23Function(slider_4ti6kk23.unit, 'max') : 100,
            step: slider_4ti6kk23 && slider_4ti6kk23.unit ? slider_4ti6kk23Function(slider_4ti6kk23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_4ti6kk23: value }),
            }), 
( text_69qbko23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_5hzq2r23,
            
            min: slider_5hzq2r23 && slider_5hzq2r23.unit ? slider_5hzq2r23Function(slider_5hzq2r23.unit, 'min') : 0,
            max: slider_5hzq2r23 && slider_5hzq2r23.unit ? slider_5hzq2r23Function(slider_5hzq2r23.unit, 'max') : 100,
            step: slider_5hzq2r23 && slider_5hzq2r23.unit ? slider_5hzq2r23Function(slider_5hzq2r23.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5hzq2r23: value }),
            }), 
( text_69qbko23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_fp3w7s23,
            onChange: (value) => setAttributes({ typography_fp3w7s23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( text_69qbko23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_8jyod623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8jyod623: value }),
            }), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_t8ll0e23,
            
            min: slider_t8ll0e23 && slider_t8ll0e23.unit ? slider_t8ll0e23Function(slider_t8ll0e23.unit, 'min') : 0,
            max: slider_t8ll0e23 && slider_t8ll0e23.unit ? slider_t8ll0e23Function(slider_t8ll0e23.unit, 'max') : 100,
            step: slider_t8ll0e23 && slider_t8ll0e23.unit ? slider_t8ll0e23Function(slider_t8ll0e23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_t8ll0e23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_pa81nu23,
            
            min: slider_pa81nu23 && slider_pa81nu23.unit ? slider_pa81nu23Function(slider_pa81nu23.unit, 'min') : 0,
            max: slider_pa81nu23 && slider_pa81nu23.unit ? slider_pa81nu23Function(slider_pa81nu23.unit, 'max') : 100,
            step: slider_pa81nu23 && slider_pa81nu23.unit ? slider_pa81nu23Function(slider_pa81nu23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_pa81nu23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_zyhwm223,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_zyhwm223: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_1etufw23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_1etufw23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_9mou2f23,
            noLock: false,
            unit: [],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_9mou2f23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Typography, {
            
            value: typography_q6qnt923,
            onChange: (value) => setAttributes({ typography_q6qnt923: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Width`),
            separator:'default',
            value: slider_r5rjhe23,
            
            min: slider_r5rjhe23 && slider_r5rjhe23.unit ? slider_r5rjhe23Function(slider_r5rjhe23.unit, 'min') : 0,
            max: slider_r5rjhe23 && slider_r5rjhe23.unit ? slider_r5rjhe23Function(slider_r5rjhe23.unit, 'max') : 100,
            step: slider_r5rjhe23 && slider_r5rjhe23.unit ? slider_r5rjhe23Function(slider_r5rjhe23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_r5rjhe23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Height`),
            separator:'default',
            value: slider_2jq1bd23,
            
            min: slider_2jq1bd23 && slider_2jq1bd23.unit ? slider_2jq1bd23Function(slider_2jq1bd23.unit, 'min') : 0,
            max: slider_2jq1bd23 && slider_2jq1bd23.unit ? slider_2jq1bd23Function(slider_2jq1bd23.unit, 'max') : 100,
            step: slider_2jq1bd23 && slider_2jq1bd23.unit ? slider_2jq1bd23Function(slider_2jq1bd23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2jq1bd23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_1434fi23,
            
            min: slider_1434fi23 && slider_1434fi23.unit ? slider_1434fi23Function(slider_1434fi23.unit, 'min') : 0,
            max: slider_1434fi23 && slider_1434fi23.unit ? slider_1434fi23Function(slider_1434fi23.unit, 'max') : 100,
            step: slider_1434fi23 && slider_1434fi23.unit ? slider_1434fi23Function(slider_1434fi23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_1434fi23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`SVG Size`),
            separator:'default',
            value: slider_11yb9r24,
            
            min: slider_11yb9r24 && slider_11yb9r24.unit ? slider_11yb9r24Function(slider_11yb9r24.unit, 'min') : 0,
            max: slider_11yb9r24 && slider_11yb9r24.unit ? slider_11yb9r24Function(slider_11yb9r24.unit, 'max') : 100,
            step: slider_11yb9r24 && slider_11yb9r24.unit ? slider_11yb9r24Function(slider_11yb9r24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_11yb9r24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_gg19h423,
            
            min: slider_gg19h423 && slider_gg19h423.unit ? slider_gg19h423Function(slider_gg19h423.unit, 'min') : 0,
            max: slider_gg19h423 && slider_gg19h423.unit ? slider_gg19h423Function(slider_gg19h423.unit, 'max') : 100,
            step: slider_gg19h423 && slider_gg19h423.unit ? slider_gg19h423Function(slider_gg19h423.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_gg19h423: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_77vw0223,
            
            min: slider_77vw0223 && slider_77vw0223.unit ? slider_77vw0223Function(slider_77vw0223.unit, 'min') : 0,
            max: slider_77vw0223 && slider_77vw0223.unit ? slider_77vw0223Function(slider_77vw0223.unit, 'max') : 100,
            step: slider_77vw0223 && slider_77vw0223.unit ? slider_77vw0223Function(slider_77vw0223.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_77vw0223: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_38ls0223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_38ls0223: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_a8b17k23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_a8b17k23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_qm19p523,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qm19p523: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_hcof4z23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_hcof4z23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_78qwh323,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_78qwh323: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_2ogt6b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_2ogt6b23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_q3t4d323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_q3t4d323: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ogbchk23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ogbchk23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_kqx21a23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_kqx21a23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_hchi9h23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_hchi9h23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Arrows"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Arrow Size`),
            separator:'default',
            value: slider_p79h6523,
            
            min: slider_p79h6523 && slider_p79h6523.unit ? slider_p79h6523Function(slider_p79h6523.unit, 'min') : 0,
            max: slider_p79h6523 && slider_p79h6523.unit ? slider_p79h6523Function(slider_p79h6523.unit, 'max') : 100,
            step: slider_p79h6523 && slider_p79h6523.unit ? slider_p79h6523Function(slider_p79h6523.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_p79h6523: value }),
            }), 
( select_zelziu23 == "style-1" ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_02718v23,
            
            min: slider_02718v23 && slider_02718v23.unit ? slider_02718v23Function(slider_02718v23.unit, 'min') : 0,
            max: slider_02718v23 && slider_02718v23.unit ? slider_02718v23Function(slider_02718v23.unit, 'max') : 100,
            step: slider_02718v23 && slider_02718v23.unit ? slider_02718v23Function(slider_02718v23.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_02718v23: value }),
            }), 
( select_zelziu23 == "style-1" ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_a7vfig23,
            
            min: slider_a7vfig23 && slider_a7vfig23.unit ? slider_a7vfig23Function(slider_a7vfig23.unit, 'min') : 0,
            max: slider_a7vfig23 && slider_a7vfig23.unit ? slider_a7vfig23Function(slider_a7vfig23.unit, 'max') : 100,
            step: slider_a7vfig23 && slider_a7vfig23.unit ? slider_a7vfig23Function(slider_a7vfig23.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_a7vfig23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Arrow Color`),
            value: color_8pfe5y23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8pfe5y23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_u64xdg23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_u64xdg23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_agg1jv23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_agg1jv23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ybjj7223,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ybjj7223: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_73n6ak23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_73n6ak23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Arrow Color`),
            value: color_5cu1qd23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_5cu1qd23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_lxtc3b23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lxtc3b23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_pk1a8w23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_pk1a8w23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_zs3olu23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_zs3olu23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_kit0yu23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_kit0yu23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-cp0dlj24", block_id, false, props.clientId);
                }
            }

            
let g_text_69qbko23 = text_69qbko23 && text_69qbko23 != undefined  ? text_69qbko23 : "";
let g_text_hrlhn723 = text_hrlhn723 && text_hrlhn723 != undefined  ? text_hrlhn723 : "";
let g_iconscontrol_rj2zaq23 = iconscontrol_rj2zaq23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_rj2zaq23+'"></i></span>' : '';

let g_url_awskqt23_url = url_awskqt23?.url && url_awskqt23?.url != undefined ? url_awskqt23.url : "";
let g_url_awskqt23_target = url_awskqt23?.target && url_awskqt23?.target != undefined ? url_awskqt23.target : "";
let g_url_awskqt23_nofollow = url_awskqt23?.nofollow && url_awskqt23?.nofollow != undefined ? url_awskqt23.nofollow : "";
let g_url_awskqt23_ctmArt = url_awskqt23?.attr != undefined ? url_awskqt23.attr : "";
                    let g_url_awskqt23_attr = ''

                    if (g_url_awskqt23_ctmArt) {
                        let main_array = g_url_awskqt23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_awskqt23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_bwncwo23 = switcher_bwncwo23 && switcher_bwncwo23 != undefined  ? 'yes' : "";
let g_select_zelziu23 = select_zelziu23 && select_zelziu23 != undefined && ( !switcher_bwncwo23 ) ? select_zelziu23 : "";
let g_typography_fp3w7s23 = typography_fp3w7s23 && typography_fp3w7s23 != undefined && ( (text_69qbko23 != "") ) ? typography_fp3w7s23 : "";
let g_color_8jyod623 = color_8jyod623 && color_8jyod623 != undefined && ( (text_69qbko23 != "") ) ? color_8jyod623 : "";
let g_background_zyhwm223 = background_zyhwm223 && background_zyhwm223 != undefined  ? background_zyhwm223 : "";
let g_border_1etufw23 = border_1etufw23 && border_1etufw23 != undefined  ? border_1etufw23 : "";
let g_dimension_9mou2f23 = dimension_9mou2f23 && dimension_9mou2f23 != undefined  ? dimension_9mou2f23 : "";
let g_typography_q6qnt923 = typography_q6qnt923 && typography_q6qnt923 != undefined  ? typography_q6qnt923 : "";
let g_color_38ls0223 = color_38ls0223 && color_38ls0223 != undefined  ? color_38ls0223 : "";
let g_color_a8b17k23 = color_a8b17k23 && color_a8b17k23 != undefined  ? color_a8b17k23 : "";
let g_background_qm19p523 = background_qm19p523 && background_qm19p523 != undefined  ? background_qm19p523 : "";
let g_border_hcof4z23 = border_hcof4z23 && border_hcof4z23 != undefined  ? border_hcof4z23 : "";
let g_boxshadow_78qwh323 = boxshadow_78qwh323 && boxshadow_78qwh323 != undefined  ? boxshadow_78qwh323 : "";
let g_color_2ogt6b23 = color_2ogt6b23 && color_2ogt6b23 != undefined  ? color_2ogt6b23 : "";
let g_color_q3t4d323 = color_q3t4d323 && color_q3t4d323 != undefined  ? color_q3t4d323 : "";
let g_background_ogbchk23 = background_ogbchk23 && background_ogbchk23 != undefined  ? background_ogbchk23 : "";
let g_border_kqx21a23 = border_kqx21a23 && border_kqx21a23 != undefined  ? border_kqx21a23 : "";
let g_boxshadow_hchi9h23 = boxshadow_hchi9h23 && boxshadow_hchi9h23 != undefined  ? boxshadow_hchi9h23 : "";
let g_color_8pfe5y23 = color_8pfe5y23 && color_8pfe5y23 != undefined  ? color_8pfe5y23 : "";
let g_background_u64xdg23 = background_u64xdg23 && background_u64xdg23 != undefined  ? background_u64xdg23 : "";
let g_border_agg1jv23 = border_agg1jv23 && border_agg1jv23 != undefined  ? border_agg1jv23 : "";
let g_dimension_ybjj7223 = dimension_ybjj7223 && dimension_ybjj7223 != undefined  ? dimension_ybjj7223 : "";
let g_boxshadow_73n6ak23 = boxshadow_73n6ak23 && boxshadow_73n6ak23 != undefined  ? boxshadow_73n6ak23 : "";
let g_color_5cu1qd23 = color_5cu1qd23 && color_5cu1qd23 != undefined  ? color_5cu1qd23 : "";
let g_background_lxtc3b23 = background_lxtc3b23 && background_lxtc3b23 != undefined  ? background_lxtc3b23 : "";
let g_border_pk1a8w23 = border_pk1a8w23 && border_pk1a8w23 != undefined  ? border_pk1a8w23 : "";
let g_dimension_zs3olu23 = dimension_zs3olu23 && dimension_zs3olu23 != undefined  ? dimension_zs3olu23 : "";
let g_boxshadow_kit0yu23 = boxshadow_kit0yu23 && boxshadow_kit0yu23 != undefined  ? boxshadow_kit0yu23 : "";
            
let repeater_27pwea23_2z25 = "";
                            
repeater_27pwea23  && repeater_27pwea23.map((r_item, index) => {
                                
let grnp_media_d8rrzd23 = r_item?.media_d8rrzd23?.url != undefined  ? r_item?.media_d8rrzd23.url : "";
                                repeater_27pwea23_2z25 += `<li class="tp-repeater-item-${r_item._key} splide__slide" data-repeater_27pwea23="{repeater_27pwea23}" style="width:100%">
                    <div class="banner-3d-inner-media">
                        <div class="banner-3d-inner-media-inner">
                            <div class="banner-3d-inner-media-inner-image" style="background-image:url(${grnp_media_d8rrzd23})"></div>
                        </div>
                    </div>
                  </li>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_cp0dlj24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-banner-3d-with-carousel arrow-hide-${g_switcher_bwncwo23} arrow-${g_select_zelziu23} splide">
    <div class="banner-3d-inner splide__track">
        <div class="banner-3d-inner-heading">
            <div class="banner-3d-inner-heading-inner">
                <p class="banner-3d-inner-heading-inner-text">${g_text_69qbko23}</p>
            </div>
        </div>
            <ul class="banner-slick-slider splide__list">
                   ${repeater_27pwea23_2z25}
            </ul>
        <div class="banner-3d-inner-btn">
            <a href="${g_url_awskqt23_url}" class="btn-banner" rel="noopener">
                ${g_iconscontrol_rj2zaq23}
                <span class="banner-3d-inner-btn-text">${g_text_hrlhn723}</span>
            </a>
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
            repeater_27pwea23,
text_69qbko23,
text_hrlhn723,
iconscontrol_rj2zaq23,
url_awskqt23,
switcher_bwncwo23,
select_zelziu23,
slider_4ti6kk23,
slider_5hzq2r23,
typography_fp3w7s23,
color_8jyod623,
slider_t8ll0e23,
slider_pa81nu23,
background_zyhwm223,
border_1etufw23,
dimension_9mou2f23,
typography_q6qnt923,
slider_r5rjhe23,
slider_2jq1bd23,
slider_1434fi23,
slider_11yb9r24,
slider_gg19h423,
slider_77vw0223,
color_38ls0223,
color_a8b17k23,
background_qm19p523,
border_hcof4z23,
boxshadow_78qwh323,
color_2ogt6b23,
color_q3t4d323,
background_ogbchk23,
border_kqx21a23,
boxshadow_hchi9h23,
normalhover_e59se623,
slider_p79h6523,
slider_02718v23,
slider_a7vfig23,
color_8pfe5y23,
background_u64xdg23,
border_agg1jv23,
dimension_ybjj7223,
boxshadow_73n6ak23,
color_5cu1qd23,
background_lxtc3b23,
border_pk1a8w23,
dimension_zs3olu23,
boxshadow_kit0yu23,
normalhover_wz9lkp23,

            block_id,
        } = attributes;

        

        

        

        
let g_text_69qbko23 = text_69qbko23 && text_69qbko23 != undefined  ? text_69qbko23 : "";
let g_text_hrlhn723 = text_hrlhn723 && text_hrlhn723 != undefined  ? text_hrlhn723 : "";
let g_iconscontrol_rj2zaq23 = iconscontrol_rj2zaq23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_rj2zaq23+'"></i></span>' : '';

let g_url_awskqt23_url = url_awskqt23?.url && url_awskqt23?.url != undefined ? url_awskqt23.url : "";
let g_url_awskqt23_target = url_awskqt23?.target && url_awskqt23?.target != undefined ? url_awskqt23.target : "";
let g_url_awskqt23_nofollow = url_awskqt23?.nofollow && url_awskqt23?.nofollow != undefined ? url_awskqt23.nofollow : "";
let g_url_awskqt23_ctmArt = url_awskqt23?.attr != undefined ? url_awskqt23.attr : "";
                    let g_url_awskqt23_attr = ''

                    if (g_url_awskqt23_ctmArt) {
                        let main_array = g_url_awskqt23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_awskqt23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_bwncwo23 = switcher_bwncwo23 && switcher_bwncwo23 != undefined  ? 'yes' : "";
let g_select_zelziu23 = select_zelziu23 && select_zelziu23 != undefined && ( !switcher_bwncwo23 ) ? select_zelziu23 : "";
let g_typography_fp3w7s23 = typography_fp3w7s23 && typography_fp3w7s23 != undefined && ( (text_69qbko23 != "") ) ? typography_fp3w7s23 : "";
let g_color_8jyod623 = color_8jyod623 && color_8jyod623 != undefined && ( (text_69qbko23 != "") ) ? color_8jyod623 : "";
let g_background_zyhwm223 = background_zyhwm223 && background_zyhwm223 != undefined  ? background_zyhwm223 : "";
let g_border_1etufw23 = border_1etufw23 && border_1etufw23 != undefined  ? border_1etufw23 : "";
let g_dimension_9mou2f23 = dimension_9mou2f23 && dimension_9mou2f23 != undefined  ? dimension_9mou2f23 : "";
let g_typography_q6qnt923 = typography_q6qnt923 && typography_q6qnt923 != undefined  ? typography_q6qnt923 : "";
let g_color_38ls0223 = color_38ls0223 && color_38ls0223 != undefined  ? color_38ls0223 : "";
let g_color_a8b17k23 = color_a8b17k23 && color_a8b17k23 != undefined  ? color_a8b17k23 : "";
let g_background_qm19p523 = background_qm19p523 && background_qm19p523 != undefined  ? background_qm19p523 : "";
let g_border_hcof4z23 = border_hcof4z23 && border_hcof4z23 != undefined  ? border_hcof4z23 : "";
let g_boxshadow_78qwh323 = boxshadow_78qwh323 && boxshadow_78qwh323 != undefined  ? boxshadow_78qwh323 : "";
let g_color_2ogt6b23 = color_2ogt6b23 && color_2ogt6b23 != undefined  ? color_2ogt6b23 : "";
let g_color_q3t4d323 = color_q3t4d323 && color_q3t4d323 != undefined  ? color_q3t4d323 : "";
let g_background_ogbchk23 = background_ogbchk23 && background_ogbchk23 != undefined  ? background_ogbchk23 : "";
let g_border_kqx21a23 = border_kqx21a23 && border_kqx21a23 != undefined  ? border_kqx21a23 : "";
let g_boxshadow_hchi9h23 = boxshadow_hchi9h23 && boxshadow_hchi9h23 != undefined  ? boxshadow_hchi9h23 : "";
let g_color_8pfe5y23 = color_8pfe5y23 && color_8pfe5y23 != undefined  ? color_8pfe5y23 : "";
let g_background_u64xdg23 = background_u64xdg23 && background_u64xdg23 != undefined  ? background_u64xdg23 : "";
let g_border_agg1jv23 = border_agg1jv23 && border_agg1jv23 != undefined  ? border_agg1jv23 : "";
let g_dimension_ybjj7223 = dimension_ybjj7223 && dimension_ybjj7223 != undefined  ? dimension_ybjj7223 : "";
let g_boxshadow_73n6ak23 = boxshadow_73n6ak23 && boxshadow_73n6ak23 != undefined  ? boxshadow_73n6ak23 : "";
let g_color_5cu1qd23 = color_5cu1qd23 && color_5cu1qd23 != undefined  ? color_5cu1qd23 : "";
let g_background_lxtc3b23 = background_lxtc3b23 && background_lxtc3b23 != undefined  ? background_lxtc3b23 : "";
let g_border_pk1a8w23 = border_pk1a8w23 && border_pk1a8w23 != undefined  ? border_pk1a8w23 : "";
let g_dimension_zs3olu23 = dimension_zs3olu23 && dimension_zs3olu23 != undefined  ? dimension_zs3olu23 : "";
let g_boxshadow_kit0yu23 = boxshadow_kit0yu23 && boxshadow_kit0yu23 != undefined  ? boxshadow_kit0yu23 : "";
        
let repeater_27pwea23_2z25 = "";
                            
repeater_27pwea23  && repeater_27pwea23.map((r_item, index) => {
                                
let grnp_media_d8rrzd23 = r_item?.media_d8rrzd23?.url != undefined  ? r_item?.media_d8rrzd23.url : "";
                                repeater_27pwea23_2z25 += `<li class="tp-repeater-item-${r_item._key} splide__slide" data-repeater_27pwea23="{repeater_27pwea23}" style="width:100%">
                    <div class="banner-3d-inner-media">
                        <div class="banner-3d-inner-media-inner">
                            <div class="banner-3d-inner-media-inner-image" style="background-image:url(${grnp_media_d8rrzd23})"></div>
                        </div>
                    </div>
                  </li>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-cp0dlj24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_cp0dlj24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-banner-3d-with-carousel arrow-hide-${g_switcher_bwncwo23} arrow-${g_select_zelziu23} splide">
    <div class="banner-3d-inner splide__track">
        <div class="banner-3d-inner-heading">
            <div class="banner-3d-inner-heading-inner">
                <p class="banner-3d-inner-heading-inner-text">${g_text_69qbko23}</p>
            </div>
        </div>
            <ul class="banner-slick-slider splide__list">
                   ${repeater_27pwea23_2z25}
            </ul>
        <div class="banner-3d-inner-btn">
            <a href="${g_url_awskqt23_url}" class="btn-banner" rel="noopener">
                ${g_iconscontrol_rj2zaq23}
                <span class="banner-3d-inner-btn-text">${g_text_hrlhn723}</span>
            </a>
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
    new Banner_3D_With_Carousel_cp0dlj24();