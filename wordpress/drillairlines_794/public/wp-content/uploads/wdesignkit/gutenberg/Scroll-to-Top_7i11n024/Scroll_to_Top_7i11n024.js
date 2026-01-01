
    class Scroll_to_Top_7i11n024 {
        constructor() {
            this.Scroll_to_Top_7i11n024_w7sg0u25();
        }
    
        Scroll_to_Top_7i11n024_w7sg0u25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Select,Pmgc_IconList,Pmgc_Media,Pmgc_RadioAdvanced,Pmgc_Label_Heading,Pmgc_Range,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_Dimension,Pmgc_Tabs,Pmgc_Typography,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-7i11n024', {
        title: __('Scroll to Top'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-level-up-alt tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Scroll to Top'),__('Back to Top'),__('Page Navigation'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_novwyy23Function = (unit, type) => {
                var g_slider_novwyy23_list = [];
                g_slider_novwyy23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_novwyy23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_novwyy23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_novwyy23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_novwyy23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_novwyy23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_novwyy23_list[unit][type];
            };
const slider_tum7ix23Function = (unit, type) => {
                var g_slider_tum7ix23_list = [];
                g_slider_tum7ix23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_tum7ix23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_tum7ix23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_tum7ix23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_tum7ix23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_tum7ix23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_tum7ix23_list[unit][type];
            };
const slider_sw0lj923Function = (unit, type) => {
                var g_slider_sw0lj923_list = [];
                g_slider_sw0lj923_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_sw0lj923_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_sw0lj923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_sw0lj923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_sw0lj923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_sw0lj923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_sw0lj923_list[unit][type];
            };
const slider_nn09fl24Function = (unit, type) => {
                var g_slider_nn09fl24_list = [];
                g_slider_nn09fl24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_nn09fl24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_nn09fl24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_nn09fl24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_nn09fl24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_nn09fl24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_nn09fl24_list[unit][type];
            };
const slider_nv168324Function = (unit, type) => {
                var g_slider_nv168324_list = [];
                g_slider_nv168324_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_nv168324_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_nv168324_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_nv168324_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_nv168324_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_nv168324_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_nv168324_list[unit][type];
            };
const slider_bvoss823Function = (unit, type) => {
                var g_slider_bvoss823_list = [];
                g_slider_bvoss823_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_bvoss823_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_bvoss823_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_bvoss823_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_bvoss823_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_bvoss823_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_bvoss823_list[unit][type];
            };
const slider_0xu7cs23Function = (unit, type) => {
                var g_slider_0xu7cs23_list = [];
                g_slider_0xu7cs23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_0xu7cs23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_0xu7cs23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_0xu7cs23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_0xu7cs23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_0xu7cs23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_0xu7cs23_list[unit][type];
            };
const slider_lk6xgl23Function = (unit, type) => {
                var g_slider_lk6xgl23_list = [];
                g_slider_lk6xgl23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_lk6xgl23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_lk6xgl23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_lk6xgl23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_lk6xgl23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_lk6xgl23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_lk6xgl23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_84wm4g23,
select_llua2q23,
select_f8h0cz23,
select_lwh4n325,
iconscontrol_4w93sa23,
media_7uav9v23,
select_j6s46n23,
choose_1c8ct825,
select_7r268k24,
text_9yzuwi24,
select_qf9zw924,
heading_4b4qzx24,
number_pv2byx23,
number_7bs6qp23,
number_ao6cgb23,
heading_3zyj7o24,
slider_novwyy23,
slider_tum7ix23,
slider_sw0lj923,
color_rbsesr23,
background_qmlxm923,
border_wcck2y25,
dimension_ip34nn23,
color_13qqje23,
background_dg1yyt23,
border_h1qezt25,
dimension_jj2irr23,
normalhover_m8ze9t23,
typography_yhmtpu23,
choose_56a2em24,
slider_nn09fl24,
slider_nv168324,
slider_bvoss823,
slider_0xu7cs23,
slider_lk6xgl23,
dimension_94pekm23,
color_8y24hh23,
background_t0baor23,
border_6m6kaq25,
dimension_j9uani23,
boxshadow_y7k2qr23,
color_ylaper23,
background_17oavs23,
border_hmeirc25,
dimension_t76rta23,
boxshadow_vw9akj23,
normalhover_3x4bzq23,

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
                        main_function_7i11n024(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_7i11n024 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                if($scope && $scope[0]){
    
    let button = $scope[0].querySelector('.wkit-scrolltotop');
    
    let getBack = document.querySelector('.interface-navigable-region.interface-interface-skeleton__content');
    console.log(getBack);
    
    let applyTo = (button.getAttribute("data-applyto")) ? button.getAttribute("data-applyto") : 'body' ;
    let cid = (button.getAttribute("data-cid")) ? button.getAttribute("data-cid") : '' ;
    
    
    let btnOffset = (button.getAttribute("data-desk")) ? Number(button.getAttribute("data-desk")) : 100 ;
    let btnOffsetTab = (button.getAttribute("data-tab")) ? Number(button.getAttribute("data-tab")) : btnOffset ;
    let btnOffsetMob = (button.getAttribute("data-mob")) ? Number(button.getAttribute("data-mob")) : btnOffsetTab ;
    
    let btnOffsetN = "", winScroll = '', conTopOff = 0, topOff = 0;
    
    let width = screen.width;
    if (width >= 1024) {
        btnOffsetN = btnOffset;
    } else if (width < 1024 && width >= 768) {
        btnOffsetN = btnOffsetTab;
    } else if (width < 768){
        btnOffsetN = btnOffsetMob;
    }
    if(applyTo == 'container'){
        winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        let getSelector = '';
        if(cid){
            getSelector = document.querySelector(cid);
        }else{
            getSelector = $scope[0].closest('.e-con-boxed, .e-con-full');
        }
        if(getSelector){
            conTopOff = getSelector.offsetTop;   
        }
        topOff = conTopOff;
        btnOffsetN = btnOffsetN + conTopOff;
    }
    
    
    if(window.wp && window.wp.blocks && getBack){
        getBack.addEventListener('scroll',(e) => {
            let topOffset = getBack.scrollTop;
            console.log(topOffset)
            if(topOffset >= btnOffsetN){
                button.style.visibility = "visible";
            }else{
                button.style.visibility = "hidden";
            }
        });
    }else{
        window.addEventListener('scroll',(e) => {
            if(window.scrollY >= btnOffsetN){
                button.style.visibility = "visible";
            }else{
                button.style.visibility = "hidden";
            }
        });
    }
    
    button.addEventListener('click',() => {
        if(window.wp && window.wp.blocks && getBack){
            getBack.scrollTo({
                top: topOff,
                behavior: 'smooth'
            }); 
        }else{
            window.scroll({
                top: topOff,
                behavior: 'smooth'
            }); 
        }
    });
}
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_84wm4g23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_84wm4g23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Select Icon`),
                options:[['icon',__('Icon')],['image',__('Image')],['none',__('None')],],
                separator:"default",
                
                
                value: select_llua2q23,
                onChange: (value) => {setAttributes({ select_llua2q23: value }) },
            }),
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Select, {
                label: __(`Icon Position`),
                options:[['after-text',__('After')],['before-text',__('Before')],],
                separator:"default",
                
                
                value: select_f8h0cz23,
                onChange: (value) => {setAttributes({ select_f8h0cz23: value }) },
            }),
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Select, {
                label: __(`Icon Direction`),
                options:[['inline',__('Inline')],['column',__('Column')],],
                separator:"default",
                
                
                value: select_lwh4n325,
                onChange: (value) => {setAttributes({ select_lwh4n325: value }) },
            }),
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(` Icon`),
            
            value: iconscontrol_4w93sa23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_4w93sa23: value }),
            }), 
( select_llua2q23 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Image`),
                multiple: false,
                separator:'default',
                value: media_7uav9v23,
                dynamic: [true, 'media_7uav9v23'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_7uav9v23: value }),
            }), 
 React.createElement(Pmgc_Select, {
                label: __(`Select Side`),
                options:[['horizontal',__('Horizontal')],['vertical',__('Vertical')],],
                separator:"default",
                
                
                value: select_j6s46n23,
                onChange: (value) => {setAttributes({ select_j6s46n23: value }) },
            }),
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('label'), value: 'flex-start', title: __('label'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('label1'), value: 'center', title: __('label1'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('label2'), value: 'flex-end', title: __('label2'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_1c8ct825,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_1c8ct825: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Apply To`),
                options:[['body',__('Body')],['container',__('Container')],],
                separator:"default",
                
                
                value: select_7r268k24,
                onChange: (value) => {setAttributes({ select_7r268k24: value }) },
            }),
( select_7r268k24 == "container" ) && React.createElement(Pmgc_Text, {
                label: __(`Connection ID/Class`),
                type: "text",
                value: text_9yzuwi24,
                placeholder:`#id or .classname`,
                
                help: `If you leave the Connection ID empty, it will target the closest container.`,
                separator:"default",
                inlineblock:false,
                onChange: (value) => {setAttributes({ text_9yzuwi24: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Select Position`),
                options:[['relative',__('Relative')],['fixed',__('Fixed')],],
                separator:"default",
                
                
                value: select_qf9zw924,
                onChange: (value) => {setAttributes({ select_qf9zw924: value }) },
            }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Offset`),
            value: heading_4b4qzx24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Desktop`),
                type: "number",
                value: number_pv2byx23,
                dynamic: true,
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_pv2byx23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tablet`),
                type: "number",
                value: number_7bs6qp23,
                dynamic: true,
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_7bs6qp23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Mobile`),
                type: "number",
                value: number_ao6cgb23,
                dynamic: true,
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_ao6cgb23: value }) },
            }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`When you select a container, the offset value starts from the container`),
            value: heading_3zyj7o24,
            separator:"default",
            inlineblock: true,
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Icon"), initialOpen: true },
( select_llua2q23 == "image" ) && React.createElement(Pmgc_Range, {
            label: __(`Image Width`),
            separator:'default',
            value: slider_novwyy23,
            
            min: slider_novwyy23 && slider_novwyy23.unit ? slider_novwyy23Function(slider_novwyy23.unit, 'min') : 0,
            max: slider_novwyy23 && slider_novwyy23.unit ? slider_novwyy23Function(slider_novwyy23.unit, 'max') : 100,
            step: slider_novwyy23 && slider_novwyy23.unit ? slider_novwyy23Function(slider_novwyy23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_novwyy23: value }),
            }), 
( select_llua2q23 == "image" ) && React.createElement(Pmgc_Range, {
            label: __(`Image Height`),
            separator:'default',
            value: slider_tum7ix23,
            
            min: slider_tum7ix23 && slider_tum7ix23.unit ? slider_tum7ix23Function(slider_tum7ix23.unit, 'min') : 0,
            max: slider_tum7ix23 && slider_tum7ix23.unit ? slider_tum7ix23Function(slider_tum7ix23.unit, 'max') : 100,
            step: slider_tum7ix23 && slider_tum7ix23.unit ? slider_tum7ix23Function(slider_tum7ix23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_tum7ix23: value }),
            }), 
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_sw0lj923,
            
            min: slider_sw0lj923 && slider_sw0lj923.unit ? slider_sw0lj923Function(slider_sw0lj923.unit, 'min') : 0,
            max: slider_sw0lj923 && slider_sw0lj923.unit ? slider_sw0lj923Function(slider_sw0lj923.unit, 'max') : 100,
            step: slider_sw0lj923 && slider_sw0lj923.unit ? slider_sw0lj923Function(slider_sw0lj923.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_sw0lj923: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_rbsesr23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rbsesr23: value }),
            }), 
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Background, {
            
            value: background_qmlxm923,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qmlxm923: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_wcck2y25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_wcck2y25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ip34nn23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ip34nn23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_13qqje23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_13qqje23: value }),
            }), 
( select_llua2q23 == "icon" ) && React.createElement(Pmgc_Background, {
            
            value: background_dg1yyt23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_dg1yyt23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_h1qezt25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_h1qezt25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_jj2irr23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jj2irr23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
), 
), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Typography, {
            
            value: typography_yhmtpu23,
            onChange: (value) => setAttributes({ typography_yhmtpu23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_7r268k24 == "relative" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_56a2em24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_56a2em24: value }),
            }), 
( select_7r268k24 == "fixed" ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_nn09fl24,
            
            min: slider_nn09fl24 && slider_nn09fl24.unit ? slider_nn09fl24Function(slider_nn09fl24.unit, 'min') : 0,
            max: slider_nn09fl24 && slider_nn09fl24.unit ? slider_nn09fl24Function(slider_nn09fl24.unit, 'max') : 100,
            step: slider_nn09fl24 && slider_nn09fl24.unit ? slider_nn09fl24Function(slider_nn09fl24.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_nn09fl24: value }),
            }), 
( select_7r268k24 == "fixed" ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_nv168324,
            
            min: slider_nv168324 && slider_nv168324.unit ? slider_nv168324Function(slider_nv168324.unit, 'min') : 0,
            max: slider_nv168324 && slider_nv168324.unit ? slider_nv168324Function(slider_nv168324.unit, 'max') : 100,
            step: slider_nv168324 && slider_nv168324.unit ? slider_nv168324Function(slider_nv168324.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_nv168324: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Button Width`),
            separator:'default',
            value: slider_bvoss823,
            
            min: slider_bvoss823 && slider_bvoss823.unit ? slider_bvoss823Function(slider_bvoss823.unit, 'min') : 0,
            max: slider_bvoss823 && slider_bvoss823.unit ? slider_bvoss823Function(slider_bvoss823.unit, 'max') : 100,
            step: slider_bvoss823 && slider_bvoss823.unit ? slider_bvoss823Function(slider_bvoss823.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_bvoss823: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Button Height`),
            separator:'default',
            value: slider_0xu7cs23,
            
            min: slider_0xu7cs23 && slider_0xu7cs23.unit ? slider_0xu7cs23Function(slider_0xu7cs23.unit, 'min') : 0,
            max: slider_0xu7cs23 && slider_0xu7cs23.unit ? slider_0xu7cs23Function(slider_0xu7cs23.unit, 'max') : 100,
            step: slider_0xu7cs23 && slider_0xu7cs23.unit ? slider_0xu7cs23Function(slider_0xu7cs23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_0xu7cs23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_lk6xgl23,
            
            min: slider_lk6xgl23 && slider_lk6xgl23.unit ? slider_lk6xgl23Function(slider_lk6xgl23.unit, 'min') : 0,
            max: slider_lk6xgl23 && slider_lk6xgl23.unit ? slider_lk6xgl23Function(slider_lk6xgl23.unit, 'max') : 100,
            step: slider_lk6xgl23 && slider_lk6xgl23.unit ? slider_lk6xgl23Function(slider_lk6xgl23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_lk6xgl23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_94pekm23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_94pekm23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_8y24hh23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8y24hh23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_t0baor23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_t0baor23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_6m6kaq25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_6m6kaq25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_j9uani23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_j9uani23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_y7k2qr23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_y7k2qr23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ylaper23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ylaper23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_17oavs23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_17oavs23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_hmeirc25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_hmeirc25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_t76rta23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_t76rta23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_vw9akj23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_vw9akj23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-7i11n024", block_id, false, props.clientId);
                }
            }

            
let g_text_84wm4g23 = text_84wm4g23 && text_84wm4g23 != undefined  ? text_84wm4g23 : "";
let g_select_llua2q23 = select_llua2q23 && select_llua2q23 != undefined  ? select_llua2q23 : "";
let g_select_f8h0cz23 = select_f8h0cz23 && select_f8h0cz23 != undefined && ( (select_llua2q23 == "icon") ) ? select_f8h0cz23 : "";
let g_select_lwh4n325 = select_lwh4n325 && select_lwh4n325 != undefined && ( (select_llua2q23 == "icon") ) ? select_lwh4n325 : "";
let g_iconscontrol_4w93sa23 = iconscontrol_4w93sa23 != undefined && ( (select_llua2q23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_4w93sa23+'"></i></span>' : '';

let g_media_7uav9v23 = media_7uav9v23 && media_7uav9v23.url && media_7uav9v23.url != undefined && ( (select_llua2q23 == "image") ) ? media_7uav9v23.url : "";
let g_select_j6s46n23 = select_j6s46n23 && select_j6s46n23 != undefined  ? select_j6s46n23 : "";
let g_choose_1c8ct825 = choose_1c8ct825 && choose_1c8ct825 != undefined  ? choose_1c8ct825 : "";
let g_select_7r268k24 = select_7r268k24 && select_7r268k24 != undefined  ? select_7r268k24 : "";
let g_text_9yzuwi24 = text_9yzuwi24 && text_9yzuwi24 != undefined && ( (select_7r268k24 == "container") ) ? text_9yzuwi24 : "";
let g_select_qf9zw924 = select_qf9zw924 && select_qf9zw924 != undefined  ? select_qf9zw924 : "";
let g_heading_4b4qzx24 = heading_4b4qzx24 && heading_4b4qzx24 != undefined  ? heading_4b4qzx24 : "";
let g_number_pv2byx23 = number_pv2byx23 && number_pv2byx23 != undefined  ? number_pv2byx23 : "";
let g_number_7bs6qp23 = number_7bs6qp23 && number_7bs6qp23 != undefined  ? number_7bs6qp23 : "";
let g_number_ao6cgb23 = number_ao6cgb23 && number_ao6cgb23 != undefined  ? number_ao6cgb23 : "";
let g_heading_3zyj7o24 = heading_3zyj7o24 && heading_3zyj7o24 != undefined  ? heading_3zyj7o24 : "";
let g_color_rbsesr23 = color_rbsesr23 && color_rbsesr23 != undefined && ( (select_llua2q23 == "icon") ) ? color_rbsesr23 : "";
let g_background_qmlxm923 = background_qmlxm923 && background_qmlxm923 != undefined && ( (select_llua2q23 == "icon") ) ? background_qmlxm923 : "";
let g_border_wcck2y25 = border_wcck2y25 && border_wcck2y25 != undefined  ? border_wcck2y25 : "";
let g_dimension_ip34nn23 = dimension_ip34nn23 && dimension_ip34nn23 != undefined  ? dimension_ip34nn23 : "";
let g_color_13qqje23 = color_13qqje23 && color_13qqje23 != undefined && ( (select_llua2q23 == "icon") ) ? color_13qqje23 : "";
let g_background_dg1yyt23 = background_dg1yyt23 && background_dg1yyt23 != undefined && ( (select_llua2q23 == "icon") ) ? background_dg1yyt23 : "";
let g_border_h1qezt25 = border_h1qezt25 && border_h1qezt25 != undefined  ? border_h1qezt25 : "";
let g_dimension_jj2irr23 = dimension_jj2irr23 && dimension_jj2irr23 != undefined  ? dimension_jj2irr23 : "";
let g_typography_yhmtpu23 = typography_yhmtpu23 && typography_yhmtpu23 != undefined  ? typography_yhmtpu23 : "";
let g_choose_56a2em24 = choose_56a2em24 && choose_56a2em24 != undefined && ( (select_7r268k24 == "relative") ) ? choose_56a2em24 : "";
let g_dimension_94pekm23 = dimension_94pekm23 && dimension_94pekm23 != undefined  ? dimension_94pekm23 : "";
let g_color_8y24hh23 = color_8y24hh23 && color_8y24hh23 != undefined  ? color_8y24hh23 : "";
let g_background_t0baor23 = background_t0baor23 && background_t0baor23 != undefined  ? background_t0baor23 : "";
let g_border_6m6kaq25 = border_6m6kaq25 && border_6m6kaq25 != undefined  ? border_6m6kaq25 : "";
let g_dimension_j9uani23 = dimension_j9uani23 && dimension_j9uani23 != undefined  ? dimension_j9uani23 : "";
let g_boxshadow_y7k2qr23 = boxshadow_y7k2qr23 && boxshadow_y7k2qr23 != undefined  ? boxshadow_y7k2qr23 : "";
let g_color_ylaper23 = color_ylaper23 && color_ylaper23 != undefined  ? color_ylaper23 : "";
let g_background_17oavs23 = background_17oavs23 && background_17oavs23 != undefined  ? background_17oavs23 : "";
let g_border_hmeirc25 = border_hmeirc25 && border_hmeirc25 != undefined  ? border_hmeirc25 : "";
let g_dimension_t76rta23 = dimension_t76rta23 && dimension_t76rta23 != undefined  ? dimension_t76rta23 : "";
let g_boxshadow_vw9akj23 = boxshadow_vw9akj23 && boxshadow_vw9akj23 != undefined  ? boxshadow_vw9akj23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7i11n024 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-scrolltotop-main ${g_select_f8h0cz23} side-${g_select_j6s46n23} pos-${g_select_qf9zw924}">
    <button class="wkit-scrolltotop type-${g_select_llua2q23} btn-dir-${g_select_lwh4n325}" data-desk="${g_number_pv2byx23}" data-tab="${g_number_7bs6qp23}" data-mob="${g_number_ao6cgb23}" data-applyto="${g_select_7r268k24}" data-cid="${g_text_9yzuwi24}">
        ${g_iconscontrol_4w93sa23}
        <img class="tp-title-icon" src="${g_media_7uav9v23}">
        <span class="scrolltotop-text" data-text="${g_text_84wm4g23}">${g_text_84wm4g23}</span>
    </button>
</div>  `
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
            text_84wm4g23,
select_llua2q23,
select_f8h0cz23,
select_lwh4n325,
iconscontrol_4w93sa23,
media_7uav9v23,
select_j6s46n23,
choose_1c8ct825,
select_7r268k24,
text_9yzuwi24,
select_qf9zw924,
heading_4b4qzx24,
number_pv2byx23,
number_7bs6qp23,
number_ao6cgb23,
heading_3zyj7o24,
slider_novwyy23,
slider_tum7ix23,
slider_sw0lj923,
color_rbsesr23,
background_qmlxm923,
border_wcck2y25,
dimension_ip34nn23,
color_13qqje23,
background_dg1yyt23,
border_h1qezt25,
dimension_jj2irr23,
normalhover_m8ze9t23,
typography_yhmtpu23,
choose_56a2em24,
slider_nn09fl24,
slider_nv168324,
slider_bvoss823,
slider_0xu7cs23,
slider_lk6xgl23,
dimension_94pekm23,
color_8y24hh23,
background_t0baor23,
border_6m6kaq25,
dimension_j9uani23,
boxshadow_y7k2qr23,
color_ylaper23,
background_17oavs23,
border_hmeirc25,
dimension_t76rta23,
boxshadow_vw9akj23,
normalhover_3x4bzq23,

            block_id,
        } = attributes;

        

        

        

        
let g_text_84wm4g23 = text_84wm4g23 && text_84wm4g23 != undefined  ? text_84wm4g23 : "";
let g_select_llua2q23 = select_llua2q23 && select_llua2q23 != undefined  ? select_llua2q23 : "";
let g_select_f8h0cz23 = select_f8h0cz23 && select_f8h0cz23 != undefined && ( (select_llua2q23 == "icon") ) ? select_f8h0cz23 : "";
let g_select_lwh4n325 = select_lwh4n325 && select_lwh4n325 != undefined && ( (select_llua2q23 == "icon") ) ? select_lwh4n325 : "";
let g_iconscontrol_4w93sa23 = iconscontrol_4w93sa23 != undefined && ( (select_llua2q23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_4w93sa23+'"></i></span>' : '';

let g_media_7uav9v23 = media_7uav9v23 && media_7uav9v23.url && media_7uav9v23.url != undefined && ( (select_llua2q23 == "image") ) ? media_7uav9v23.url : "";
let g_select_j6s46n23 = select_j6s46n23 && select_j6s46n23 != undefined  ? select_j6s46n23 : "";
let g_choose_1c8ct825 = choose_1c8ct825 && choose_1c8ct825 != undefined  ? choose_1c8ct825 : "";
let g_select_7r268k24 = select_7r268k24 && select_7r268k24 != undefined  ? select_7r268k24 : "";
let g_text_9yzuwi24 = text_9yzuwi24 && text_9yzuwi24 != undefined && ( (select_7r268k24 == "container") ) ? text_9yzuwi24 : "";
let g_select_qf9zw924 = select_qf9zw924 && select_qf9zw924 != undefined  ? select_qf9zw924 : "";
let g_heading_4b4qzx24 = heading_4b4qzx24 && heading_4b4qzx24 != undefined  ? heading_4b4qzx24 : "";
let g_number_pv2byx23 = number_pv2byx23 && number_pv2byx23 != undefined  ? number_pv2byx23 : "";
let g_number_7bs6qp23 = number_7bs6qp23 && number_7bs6qp23 != undefined  ? number_7bs6qp23 : "";
let g_number_ao6cgb23 = number_ao6cgb23 && number_ao6cgb23 != undefined  ? number_ao6cgb23 : "";
let g_heading_3zyj7o24 = heading_3zyj7o24 && heading_3zyj7o24 != undefined  ? heading_3zyj7o24 : "";
let g_color_rbsesr23 = color_rbsesr23 && color_rbsesr23 != undefined && ( (select_llua2q23 == "icon") ) ? color_rbsesr23 : "";
let g_background_qmlxm923 = background_qmlxm923 && background_qmlxm923 != undefined && ( (select_llua2q23 == "icon") ) ? background_qmlxm923 : "";
let g_border_wcck2y25 = border_wcck2y25 && border_wcck2y25 != undefined  ? border_wcck2y25 : "";
let g_dimension_ip34nn23 = dimension_ip34nn23 && dimension_ip34nn23 != undefined  ? dimension_ip34nn23 : "";
let g_color_13qqje23 = color_13qqje23 && color_13qqje23 != undefined && ( (select_llua2q23 == "icon") ) ? color_13qqje23 : "";
let g_background_dg1yyt23 = background_dg1yyt23 && background_dg1yyt23 != undefined && ( (select_llua2q23 == "icon") ) ? background_dg1yyt23 : "";
let g_border_h1qezt25 = border_h1qezt25 && border_h1qezt25 != undefined  ? border_h1qezt25 : "";
let g_dimension_jj2irr23 = dimension_jj2irr23 && dimension_jj2irr23 != undefined  ? dimension_jj2irr23 : "";
let g_typography_yhmtpu23 = typography_yhmtpu23 && typography_yhmtpu23 != undefined  ? typography_yhmtpu23 : "";
let g_choose_56a2em24 = choose_56a2em24 && choose_56a2em24 != undefined && ( (select_7r268k24 == "relative") ) ? choose_56a2em24 : "";
let g_dimension_94pekm23 = dimension_94pekm23 && dimension_94pekm23 != undefined  ? dimension_94pekm23 : "";
let g_color_8y24hh23 = color_8y24hh23 && color_8y24hh23 != undefined  ? color_8y24hh23 : "";
let g_background_t0baor23 = background_t0baor23 && background_t0baor23 != undefined  ? background_t0baor23 : "";
let g_border_6m6kaq25 = border_6m6kaq25 && border_6m6kaq25 != undefined  ? border_6m6kaq25 : "";
let g_dimension_j9uani23 = dimension_j9uani23 && dimension_j9uani23 != undefined  ? dimension_j9uani23 : "";
let g_boxshadow_y7k2qr23 = boxshadow_y7k2qr23 && boxshadow_y7k2qr23 != undefined  ? boxshadow_y7k2qr23 : "";
let g_color_ylaper23 = color_ylaper23 && color_ylaper23 != undefined  ? color_ylaper23 : "";
let g_background_17oavs23 = background_17oavs23 && background_17oavs23 != undefined  ? background_17oavs23 : "";
let g_border_hmeirc25 = border_hmeirc25 && border_hmeirc25 != undefined  ? border_hmeirc25 : "";
let g_dimension_t76rta23 = dimension_t76rta23 && dimension_t76rta23 != undefined  ? dimension_t76rta23 : "";
let g_boxshadow_vw9akj23 = boxshadow_vw9akj23 && boxshadow_vw9akj23 != undefined  ? boxshadow_vw9akj23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-7i11n024", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7i11n024 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-scrolltotop-main ${g_select_f8h0cz23} side-${g_select_j6s46n23} pos-${g_select_qf9zw924}">
    <button class="wkit-scrolltotop type-${g_select_llua2q23} btn-dir-${g_select_lwh4n325}" data-desk="${g_number_pv2byx23}" data-tab="${g_number_7bs6qp23}" data-mob="${g_number_ao6cgb23}" data-applyto="${g_select_7r268k24}" data-cid="${g_text_9yzuwi24}">
        ${g_iconscontrol_4w93sa23}
        <img class="tp-title-icon" src="${g_media_7uav9v23}">
        <span class="scrolltotop-text" data-text="${g_text_84wm4g23}">${g_text_84wm4g23}</span>
    </button>
</div>  `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Scroll_to_Top_7i11n024();