
    class Card_Slider_3D_h0knvd24 {
        constructor() {
            this.Card_Slider_3D_h0knvd24_mkfuty25();
        }
    
        Card_Slider_3D_h0knvd24_mkfuty25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Text,Pmgc_Repeater,Pmgc_Toggle,Pmgc_IconList,Pmgc_Label_Heading,Pmgc_Range,Pmgc_CssFilter,Pmgc_Tabs,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,
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
   
    registerBlockType('wdkit/wb-h0knvd24', {
        title: __('Card Slider 3D'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-film tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('3D Content Slider'),__('Product Showcase'),__('Image Gallery'),__('WooCommerce Slider'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_a7xwz323Function = (unit, type) => {
                var g_slider_a7xwz323_list = [];
                g_slider_a7xwz323_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_a7xwz323_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_a7xwz323_list['em'] = { "type": "em", "min": 0, "max": 100, "step": 1 };
g_slider_a7xwz323_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_a7xwz323_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_a7xwz323_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_a7xwz323_list[unit][type];
            };
const slider_mw7cvx23Function = (unit, type) => {
                var g_slider_mw7cvx23_list = [];
                g_slider_mw7cvx23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_mw7cvx23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_mw7cvx23_list['em'] = { "type": "em", "min": 0, "max": 100, "step": 1 };
g_slider_mw7cvx23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mw7cvx23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mw7cvx23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mw7cvx23_list[unit][type];
            };
const slider_0m9cpt23Function = (unit, type) => {
                var g_slider_0m9cpt23_list = [];
                g_slider_0m9cpt23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_0m9cpt23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_0m9cpt23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_0m9cpt23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_0m9cpt23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_0m9cpt23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_0m9cpt23_list[unit][type];
            };
const slider_1v0op923Function = (unit, type) => {
                var g_slider_1v0op923_list = [];
                g_slider_1v0op923_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_1v0op923_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_1v0op923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_1v0op923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_1v0op923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_1v0op923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_1v0op923_list[unit][type];
            };
const slider_abpyok23Function = (unit, type) => {
                var g_slider_abpyok23_list = [];
                g_slider_abpyok23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_abpyok23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_abpyok23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_abpyok23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_abpyok23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_abpyok23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_abpyok23_list[unit][type];
            };
const slider_5hxcyx23Function = (unit, type) => {
                var g_slider_5hxcyx23_list = [];
                g_slider_5hxcyx23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_5hxcyx23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_5hxcyx23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5hxcyx23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5hxcyx23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5hxcyx23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5hxcyx23_list[unit][type];
            };
const slider_z2m5eh23Function = (unit, type) => {
                var g_slider_z2m5eh23_list = [];
                g_slider_z2m5eh23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_z2m5eh23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_z2m5eh23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_z2m5eh23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_z2m5eh23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_z2m5eh23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_z2m5eh23_list[unit][type];
            };
const slider_oe3do423Function = (unit, type) => {
                var g_slider_oe3do423_list = [];
                g_slider_oe3do423_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_oe3do423_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_oe3do423_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_oe3do423_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_oe3do423_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_oe3do423_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_oe3do423_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_71q5n523,
switcher_f0nzaz23,
switcher_khmwnj23,
iconscontrol_dh9nd023,
iconscontrol_edu4rx23,
heading_z5ug2n23,
slider_a7xwz323,
slider_mw7cvx23,
slider_0m9cpt23,
cssfilter_scfclt23,
cssfilter_gjj9ie23,
normalhover_qlvtk823,
dimension_ixev8y23,
heading_hsdmfk23,
typography_u6sifs23,
color_upccoq23,
heading_u35ze923,
dimension_jiu18123,
typography_4bn02r23,
color_kvusvd23,
heading_llnnvj23,
slider_1v0op923,
slider_abpyok23,
color_j5oevx23,
heading_mjbet923,
slider_5hxcyx23,
slider_z2m5eh23,
slider_oe3do423,
color_uwboip23,
color_vwzghk23,
color_9p710323,
color_glkk4e23,
color_4qy7sc24,
color_hyuhgg23,
normalhover_tjlrl823,

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
                        main_function_h0knvd24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_h0knvd24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getSlide = $scope[0].querySelector(".wkit-cardslide-wrapper");
var unid = Math.random().toString(32).slice(2);
if(getSlide.getAttribute('data-unique')){
    return;
}else{
    getSlide.setAttribute('data-unique', unid)
}
if(getSlide){
    var unid = Math.random().toString(32).slice(2);
    let swClss = getSlide.querySelector('.wkit-slider');
    let swiperPag=getSlide.querySelector(".swiper-pagination");
        swiperPag.classList.add('swiper-pag-'+unid);
    let swiperbtnnext=getSlide.querySelector(".swiper-button-next");
        swiperbtnnext.classList.add('swiper-nxt-'+unid);
    let swiperbtnprev=getSlide.querySelector(".swiper-button-prev");
        swiperbtnprev.classList.add('swiper-pre-'+unid);
    var TrandingSlider = new Swiper(swClss, {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        draggable:true,
        slidesPerView: 'auto',
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 200,
            modifier: 3,
        },
        pagination: {
            el: '.swiper-pag-'+unid,
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-nxt-'+unid,
            prevEl: '.swiper-pre-'+unid,
        }
    }); 
}


            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Card Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Add Card`),
            value: repeater_71q5n523,
            attributeName: 'repeater_71q5n523',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_71q5n523: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Media, {
                label: __(`Media`),
                multiple: false,
                separator:'default',
                value: value.media_iq86lv23,
                
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_iq86lv23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_u6ukfd23,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_u6ukfd23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Description`),
                type: "text",
                value: value.text_qgpkp423,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_qgpkp423 = v; onChange(value); },
            }),

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Dots`),
            
            value: switcher_f0nzaz23,
            
            onChange: (value) => setAttributes({ switcher_f0nzaz23: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Arrows`),
            
            value: switcher_khmwnj23,
            
            onChange: (value) => setAttributes({ switcher_khmwnj23: value }),
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_IconList, {
            label: __(`Left`),
            
            value: iconscontrol_dh9nd023,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_dh9nd023: value }),
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_IconList, {
            label: __(`Right`),
            
            value: iconscontrol_edu4rx23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_edu4rx23: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Slide"), initialOpen: true },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Slider Image `),
            value: heading_z5ug2n23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_a7xwz323,
            
            min: slider_a7xwz323 && slider_a7xwz323.unit ? slider_a7xwz323Function(slider_a7xwz323.unit, 'min') : 0,
            max: slider_a7xwz323 && slider_a7xwz323.unit ? slider_a7xwz323Function(slider_a7xwz323.unit, 'max') : 100,
            step: slider_a7xwz323 && slider_a7xwz323.unit ? slider_a7xwz323Function(slider_a7xwz323.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_a7xwz323: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_mw7cvx23,
            
            min: slider_mw7cvx23 && slider_mw7cvx23.unit ? slider_mw7cvx23Function(slider_mw7cvx23.unit, 'min') : 0,
            max: slider_mw7cvx23 && slider_mw7cvx23.unit ? slider_mw7cvx23Function(slider_mw7cvx23.unit, 'max') : 100,
            step: slider_mw7cvx23 && slider_mw7cvx23.unit ? slider_mw7cvx23Function(slider_mw7cvx23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mw7cvx23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_0m9cpt23,
            
            min: slider_0m9cpt23 && slider_0m9cpt23.unit ? slider_0m9cpt23Function(slider_0m9cpt23.unit, 'min') : 0,
            max: slider_0m9cpt23 && slider_0m9cpt23.unit ? slider_0m9cpt23Function(slider_0m9cpt23.unit, 'max') : 100,
            step: slider_0m9cpt23 && slider_0m9cpt23.unit ? slider_0m9cpt23Function(slider_0m9cpt23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_0m9cpt23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_scfclt23,
            onChange: (value) => setAttributes({ cssfilter_scfclt23: value }),
            separator:'default',
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_gjj9ie23,
            onChange: (value) => setAttributes({ cssfilter_gjj9ie23: value }),
            separator:'default',
            }), 
), 
), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border radius`),
            value: dimension_ixev8y23,
            noLock: false,
            unit: ['px','%',],
            separator:"before",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ixev8y23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_hsdmfk23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_u6sifs23,
            onChange: (value) => setAttributes({ typography_u6sifs23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_upccoq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_upccoq23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_u35ze923,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_jiu18123,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jiu18123: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_4bn02r23,
            onChange: (value) => setAttributes({ typography_4bn02r23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_kvusvd23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kvusvd23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_llnnvj23,
            separator:"before",
            inlineblock: true,
            }), 
( switcher_f0nzaz23 ) && React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_1v0op923,
            
            min: slider_1v0op923 && slider_1v0op923.unit ? slider_1v0op923Function(slider_1v0op923.unit, 'min') : 0,
            max: slider_1v0op923 && slider_1v0op923.unit ? slider_1v0op923Function(slider_1v0op923.unit, 'max') : 100,
            step: slider_1v0op923 && slider_1v0op923.unit ? slider_1v0op923Function(slider_1v0op923.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_1v0op923: value }),
            }), 
( switcher_f0nzaz23 ) && React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_abpyok23,
            
            min: slider_abpyok23 && slider_abpyok23.unit ? slider_abpyok23Function(slider_abpyok23.unit, 'min') : 0,
            max: slider_abpyok23 && slider_abpyok23.unit ? slider_abpyok23Function(slider_abpyok23.unit, 'max') : 100,
            step: slider_abpyok23 && slider_abpyok23.unit ? slider_abpyok23Function(slider_abpyok23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_abpyok23: value }),
            }), 
( switcher_f0nzaz23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dots Color`),
            value: color_j5oevx23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_j5oevx23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Arrows`),
            value: heading_mjbet923,
            separator:"before",
            inlineblock: true,
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_Range, {
            label: __(`Arrow Size`),
            separator:'default',
            value: slider_5hxcyx23,
            
            min: slider_5hxcyx23 && slider_5hxcyx23.unit ? slider_5hxcyx23Function(slider_5hxcyx23.unit, 'min') : 0,
            max: slider_5hxcyx23 && slider_5hxcyx23.unit ? slider_5hxcyx23Function(slider_5hxcyx23.unit, 'max') : 100,
            step: slider_5hxcyx23 && slider_5hxcyx23.unit ? slider_5hxcyx23Function(slider_5hxcyx23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5hxcyx23: value }),
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_Range, {
            label: __(`Arrow Width`),
            separator:'default',
            value: slider_z2m5eh23,
            
            min: slider_z2m5eh23 && slider_z2m5eh23.unit ? slider_z2m5eh23Function(slider_z2m5eh23.unit, 'min') : 0,
            max: slider_z2m5eh23 && slider_z2m5eh23.unit ? slider_z2m5eh23Function(slider_z2m5eh23.unit, 'max') : 100,
            step: slider_z2m5eh23 && slider_z2m5eh23.unit ? slider_z2m5eh23Function(slider_z2m5eh23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_z2m5eh23: value }),
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_Range, {
            label: __(`Arrow Height`),
            separator:'default',
            value: slider_oe3do423,
            
            min: slider_oe3do423 && slider_oe3do423.unit ? slider_oe3do423Function(slider_oe3do423.unit, 'min') : 0,
            max: slider_oe3do423 && slider_oe3do423.unit ? slider_oe3do423Function(slider_oe3do423.unit, 'max') : 100,
            step: slider_oe3do423 && slider_oe3do423.unit ? slider_oe3do423Function(slider_oe3do423.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_oe3do423: value }),
            }), 
( switcher_khmwnj23 ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_uwboip23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_uwboip23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Arrow Background Color`),
            value: color_glkk4e23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_glkk4e23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_vwzghk23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vwzghk23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Arrow Background Color`),
            value: color_4qy7sc24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_4qy7sc24: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-h0knvd24", block_id, false, props.clientId);
                }
            }

            
let g_switcher_f0nzaz23 = switcher_f0nzaz23 && switcher_f0nzaz23 != undefined  ? 'swiper-pagination-yes' : "";
let g_switcher_khmwnj23 = switcher_khmwnj23 && switcher_khmwnj23 != undefined  ? 'wkit-arrow-show' : "";
let g_iconscontrol_dh9nd023 = iconscontrol_dh9nd023 != undefined && ( switcher_khmwnj23 ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_dh9nd023+'"></i></span>' : '';

let g_iconscontrol_edu4rx23 = iconscontrol_edu4rx23 != undefined && ( switcher_khmwnj23 ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_edu4rx23+'"></i></span>' : '';

let g_heading_z5ug2n23 = heading_z5ug2n23 && heading_z5ug2n23 != undefined  ? heading_z5ug2n23 : "";
let g_cssfilter_scfclt23 = cssfilter_scfclt23 && cssfilter_scfclt23 != undefined  ? cssfilter_scfclt23 : "";
let g_cssfilter_gjj9ie23 = cssfilter_gjj9ie23 && cssfilter_gjj9ie23 != undefined  ? cssfilter_gjj9ie23 : "";
let g_dimension_ixev8y23 = dimension_ixev8y23 && dimension_ixev8y23 != undefined  ? dimension_ixev8y23 : "";
let g_heading_hsdmfk23 = heading_hsdmfk23 && heading_hsdmfk23 != undefined  ? heading_hsdmfk23 : "";
let g_typography_u6sifs23 = typography_u6sifs23 && typography_u6sifs23 != undefined  ? typography_u6sifs23 : "";
let g_color_upccoq23 = color_upccoq23 && color_upccoq23 != undefined  ? color_upccoq23 : "";
let g_heading_u35ze923 = heading_u35ze923 && heading_u35ze923 != undefined  ? heading_u35ze923 : "";
let g_dimension_jiu18123 = dimension_jiu18123 && dimension_jiu18123 != undefined  ? dimension_jiu18123 : "";
let g_typography_4bn02r23 = typography_4bn02r23 && typography_4bn02r23 != undefined  ? typography_4bn02r23 : "";
let g_color_kvusvd23 = color_kvusvd23 && color_kvusvd23 != undefined  ? color_kvusvd23 : "";
let g_heading_llnnvj23 = heading_llnnvj23 && heading_llnnvj23 != undefined  ? heading_llnnvj23 : "";
let g_color_j5oevx23 = color_j5oevx23 && color_j5oevx23 != undefined && ( switcher_f0nzaz23 ) ? color_j5oevx23 : "";
let g_heading_mjbet923 = heading_mjbet923 && heading_mjbet923 != undefined  ? heading_mjbet923 : "";
let g_color_uwboip23 = color_uwboip23 && color_uwboip23 != undefined  ? color_uwboip23 : "";
let g_color_vwzghk23 = color_vwzghk23 && color_vwzghk23 != undefined  ? color_vwzghk23 : "";
let g_color_9p710323 = color_9p710323 && color_9p710323 != undefined  ? color_9p710323 : "";
let g_color_glkk4e23 = color_glkk4e23 && color_glkk4e23 != undefined  ? color_glkk4e23 : "";
let g_color_4qy7sc24 = color_4qy7sc24 && color_4qy7sc24 != undefined  ? color_4qy7sc24 : "";
let g_color_hyuhgg23 = color_hyuhgg23 && color_hyuhgg23 != undefined  ? color_hyuhgg23 : "";
            
let repeater_71q5n523_a825 = "";
                            
repeater_71q5n523  && repeater_71q5n523.map((r_item, index) => {
                                
let grnp_media_iq86lv23 = r_item?.media_iq86lv23?.url != undefined  ? r_item?.media_iq86lv23.url : "";
let grnp_text_u6ukfd23 = r_item.text_u6ukfd23  ? r_item.text_u6ukfd23 : "";
let grnp_text_qgpkp423 = r_item.text_qgpkp423  ? r_item.text_qgpkp423 : "";
                                repeater_71q5n523_a825 += `<div class="swiper-slide wkit-slide {loop-class}" data-repeater_71q5n523="{repeater_71q5n523}">
                <div class="wkit-slide-img">
                    <img src="${grnp_media_iq86lv23}" alt="Card Image">
                </div>
                <div class="wkit-slide-content">
                    <div class="wkit-slide-title">${grnp_text_u6ukfd23}</div>
                    <div class="wkit-slide-desc">${grnp_text_qgpkp423}</div>
                </div>
            </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_h0knvd24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div id="tranding" class="wkit-cardslide-wrapper">
    <div class="swiper wkit-slider">
        <div class="swiper-wrapper">
            ${repeater_71q5n523_a825}
        </div>
    </div> 
    <div class="wkit-slider-control">
        <div class="swiper-button-prev slider-arrow ${g_switcher_khmwnj23}">
           ${g_iconscontrol_dh9nd023}
        </div>
        <div class="swiper-button-next slider-arrow ${g_switcher_khmwnj23}">
           ${g_iconscontrol_edu4rx23}
        </div>
        <div class="swiper-pagination ${g_switcher_f0nzaz23} "></div>
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
            repeater_71q5n523,
switcher_f0nzaz23,
switcher_khmwnj23,
iconscontrol_dh9nd023,
iconscontrol_edu4rx23,
heading_z5ug2n23,
slider_a7xwz323,
slider_mw7cvx23,
slider_0m9cpt23,
cssfilter_scfclt23,
cssfilter_gjj9ie23,
normalhover_qlvtk823,
dimension_ixev8y23,
heading_hsdmfk23,
typography_u6sifs23,
color_upccoq23,
heading_u35ze923,
dimension_jiu18123,
typography_4bn02r23,
color_kvusvd23,
heading_llnnvj23,
slider_1v0op923,
slider_abpyok23,
color_j5oevx23,
heading_mjbet923,
slider_5hxcyx23,
slider_z2m5eh23,
slider_oe3do423,
color_uwboip23,
color_vwzghk23,
color_9p710323,
color_glkk4e23,
color_4qy7sc24,
color_hyuhgg23,
normalhover_tjlrl823,

            block_id,
        } = attributes;

        

        

        

        
let g_switcher_f0nzaz23 = switcher_f0nzaz23 && switcher_f0nzaz23 != undefined  ? 'swiper-pagination-yes' : "";
let g_switcher_khmwnj23 = switcher_khmwnj23 && switcher_khmwnj23 != undefined  ? 'wkit-arrow-show' : "";
let g_iconscontrol_dh9nd023 = iconscontrol_dh9nd023 != undefined && ( switcher_khmwnj23 ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_dh9nd023+'"></i></span>' : '';

let g_iconscontrol_edu4rx23 = iconscontrol_edu4rx23 != undefined && ( switcher_khmwnj23 ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_edu4rx23+'"></i></span>' : '';

let g_heading_z5ug2n23 = heading_z5ug2n23 && heading_z5ug2n23 != undefined  ? heading_z5ug2n23 : "";
let g_cssfilter_scfclt23 = cssfilter_scfclt23 && cssfilter_scfclt23 != undefined  ? cssfilter_scfclt23 : "";
let g_cssfilter_gjj9ie23 = cssfilter_gjj9ie23 && cssfilter_gjj9ie23 != undefined  ? cssfilter_gjj9ie23 : "";
let g_dimension_ixev8y23 = dimension_ixev8y23 && dimension_ixev8y23 != undefined  ? dimension_ixev8y23 : "";
let g_heading_hsdmfk23 = heading_hsdmfk23 && heading_hsdmfk23 != undefined  ? heading_hsdmfk23 : "";
let g_typography_u6sifs23 = typography_u6sifs23 && typography_u6sifs23 != undefined  ? typography_u6sifs23 : "";
let g_color_upccoq23 = color_upccoq23 && color_upccoq23 != undefined  ? color_upccoq23 : "";
let g_heading_u35ze923 = heading_u35ze923 && heading_u35ze923 != undefined  ? heading_u35ze923 : "";
let g_dimension_jiu18123 = dimension_jiu18123 && dimension_jiu18123 != undefined  ? dimension_jiu18123 : "";
let g_typography_4bn02r23 = typography_4bn02r23 && typography_4bn02r23 != undefined  ? typography_4bn02r23 : "";
let g_color_kvusvd23 = color_kvusvd23 && color_kvusvd23 != undefined  ? color_kvusvd23 : "";
let g_heading_llnnvj23 = heading_llnnvj23 && heading_llnnvj23 != undefined  ? heading_llnnvj23 : "";
let g_color_j5oevx23 = color_j5oevx23 && color_j5oevx23 != undefined && ( switcher_f0nzaz23 ) ? color_j5oevx23 : "";
let g_heading_mjbet923 = heading_mjbet923 && heading_mjbet923 != undefined  ? heading_mjbet923 : "";
let g_color_uwboip23 = color_uwboip23 && color_uwboip23 != undefined  ? color_uwboip23 : "";
let g_color_vwzghk23 = color_vwzghk23 && color_vwzghk23 != undefined  ? color_vwzghk23 : "";
let g_color_9p710323 = color_9p710323 && color_9p710323 != undefined  ? color_9p710323 : "";
let g_color_glkk4e23 = color_glkk4e23 && color_glkk4e23 != undefined  ? color_glkk4e23 : "";
let g_color_4qy7sc24 = color_4qy7sc24 && color_4qy7sc24 != undefined  ? color_4qy7sc24 : "";
let g_color_hyuhgg23 = color_hyuhgg23 && color_hyuhgg23 != undefined  ? color_hyuhgg23 : "";
        
let repeater_71q5n523_a825 = "";
                            
repeater_71q5n523  && repeater_71q5n523.map((r_item, index) => {
                                
let grnp_media_iq86lv23 = r_item?.media_iq86lv23?.url != undefined  ? r_item?.media_iq86lv23.url : "";
let grnp_text_u6ukfd23 = r_item.text_u6ukfd23  ? r_item.text_u6ukfd23 : "";
let grnp_text_qgpkp423 = r_item.text_qgpkp423  ? r_item.text_qgpkp423 : "";
                                repeater_71q5n523_a825 += `<div class="swiper-slide wkit-slide {loop-class}" data-repeater_71q5n523="{repeater_71q5n523}">
                <div class="wkit-slide-img">
                    <img src="${grnp_media_iq86lv23}" alt="Card Image">
                </div>
                <div class="wkit-slide-content">
                    <div class="wkit-slide-title">${grnp_text_u6ukfd23}</div>
                    <div class="wkit-slide-desc">${grnp_text_qgpkp423}</div>
                </div>
            </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-h0knvd24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_h0knvd24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div id="tranding" class="wkit-cardslide-wrapper">
    <div class="swiper wkit-slider">
        <div class="swiper-wrapper">
            ${repeater_71q5n523_a825}
        </div>
    </div> 
    <div class="wkit-slider-control">
        <div class="swiper-button-prev slider-arrow ${g_switcher_khmwnj23}">
           ${g_iconscontrol_dh9nd023}
        </div>
        <div class="swiper-button-next slider-arrow ${g_switcher_khmwnj23}">
           ${g_iconscontrol_edu4rx23}
        </div>
        <div class="swiper-pagination ${g_switcher_f0nzaz23} "></div>
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
    new Card_Slider_3D_h0knvd24();