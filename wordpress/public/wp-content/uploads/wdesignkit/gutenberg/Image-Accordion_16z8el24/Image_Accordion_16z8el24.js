
    class Image_Accordion_16z8el24 {
        constructor() {
            this.Image_Accordion_16z8el24_18yke625();
        }
    
        Image_Accordion_16z8el24_18yke625() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_TextArea,Pmgc_Media,Pmgc_Color,Pmgc_Label_Heading,Pmgc_Background,Pmgc_Range,Pmgc_Repeater,Pmgc_Toggle,Pmgc_RadioAdvanced,Pmgc_Dimension,Pmgc_Typography,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-16z8el24', {
        title: __('Image Accordion'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-images tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Accordion Layout'),__('Accordion Slider'),__('Image Accordion'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_u5qjpo25Function = (unit, type) => {
                var g_slider_u5qjpo25_list = [];
                g_slider_u5qjpo25_list['px'] = { "type": "px", "min": 0, "max": 99, "step": 1 };
g_slider_u5qjpo25_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_u5qjpo25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_u5qjpo25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_u5qjpo25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_u5qjpo25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_u5qjpo25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_5soxy925,
switcher_ndy3n025,
choose_bif8k123,
dimension_233f5323,
typography_1h3ige23,
color_n20mja23,
color_3c9xeb23,
background_8ogj2523,
background_mx4zmv23,
border_zktpcq25,
border_o3pr2p25,
dimension_9bnmu323,
dimension_lkfruq23,
boxshadow_53icu923,
boxshadow_cxzbii23,
color_3o5j1323,
background_pyssax23,
border_bigkmu25,
dimension_86eja723,
boxshadow_lkm4x623,
normalhover_4kfgwq23,
choose_3z6knn23,
dimension_b0h9jc23,
typography_vzlmnv23,
color_680ioy23,
color_w4r0a323,
background_k6fbox23,
background_mc4qm323,
border_zhnveg25,
border_u2ryg125,
dimension_v44q3m23,
dimension_nm4ole23,
boxshadow_0i4x0723,
boxshadow_157mrw23,
color_89cfrb23,
background_x8w26v23,
border_dxl2pf25,
dimension_g1pgu623,
boxshadow_6ql7u123,
normalhover_wt4ofn23,
background_aspodx23,
border_4x0w2w25,
dimension_topbdb23,
background_49etgq23,
border_id88ei25,
dimension_pviklb23,
background_jrkmwe23,
border_7qh4n025,
dimension_acct6i23,
boxshadow_lksfpm23,
boxshadow_68jo8123,
boxshadow_semqb423,
normalhover_y7pfes23,
dimension_n07xme23,
dimension_jm7euq23,
dimension_5yu1lh23,
background_oxyjao23,
background_k1erq723,
border_gagobg25,
border_4wubbb25,
dimension_7l2uiy23,
dimension_hscjnl23,
boxshadow_aao2ma23,
boxshadow_km1yt823,
background_qn45xl23,
border_e9dbnc25,
dimension_3c6vmm23,
boxshadow_g92rip23,
normalhover_x4k9vo23,
heading_yiezxy23,
color_1116ag23,
color_n7pjum23,
color_9lb9qf23,
normalhover_zcwqza23,

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
                        main_function_16z8el24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_16z8el24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let mainEl = $scope[0].querySelector('.wkit-img-accrod-main-wrap'); 
let allItems = $scope[0].querySelectorAll('.wkit-img-accord-item');

var unid = Math.random().toString(32).slice(2);
if(mainEl.getAttribute('data-unique')){
    return;
}else{
    mainEl.setAttribute('data-unique', unid)
}

let getSplide = $scope[0].querySelector('.splide')

let splidewv = new Splide(getSplide, {
    type: 'loop',
    arrows: false,
    autoWidth: true,
});

splidewv.mount();

allItems.forEach((aia, index)=>{
    if(index==0){
        aia.classList.add('wkit-item-active'); 
    }
});

function handleItemClick(ev) {
    let activeItem = mainEl.querySelectorAll('.wkit-item-active');

    if (ev.currentTarget.classList.contains('wkit-item-active')) {
        ev.currentTarget.classList.remove('wkit-item-active');
    } else {
        ev.currentTarget.classList.add('wkit-item-active');
        if (activeItem) {
            activeItem.forEach((aia) => {
                aia.classList.remove('wkit-item-active');
            });
        }
    }
}

function applyClickEvents() {
    let allSlides = getSplide.querySelectorAll('.splide__slide');
    allSlides.forEach((slide) => {
        let getInner = slide.querySelector('.wkit-img-accord-item');
        getInner.removeEventListener('click', handleItemClick);
        getInner.addEventListener('click', handleItemClick);
    });
}

applyClickEvents();

splidewv.on('mounted moved', () => {
    applyClickEvents();
});
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Items`),
            value: repeater_5soxy925,
            attributeName: 'repeater_5soxy925',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_5soxy925: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_zp6ggl25,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_zp6ggl25 = v; onChange(value); },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"2",
                value: value.textarea_nli4fl25,
                
                onChange: v => { value.textarea_nli4fl25 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Choose Image`),
                multiple: false,
                separator:'default',
                value: value.media_r33bn225,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_r33bn225 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: value.color_kenc8y25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_kenc8y25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Hover Title Color`),
            value: value.color_v9ifi625,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_v9ifi625 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: value.color_on9fie25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_on9fie25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Hover Description Color`),
            value: value.color_m8tsz925,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_m8tsz925 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Bottom Background`),
            value: value.heading_3pg6p825,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Background, {
            
            value: value.background_pg495j25,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_pg495j25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Background, {
            
            value: value.background_773ajn25,
            sources: ["color","image","gradient"],
            separator:'before',
            
            onChange: v => { value.background_773ajn25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Border Color`),
            value: value.color_balhl625,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_balhl625 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Hover Border Color`),
            value: value.color_shybie25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_shybie25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Offset`),
            separator:'default',
            value: value.slider_u5qjpo25,
            
            min: value.slider_u5qjpo25 && value.slider_u5qjpo25.unit ? slider_u5qjpo25Function(value.slider_u5qjpo25.unit, 'min') : 0,
            max: value.slider_u5qjpo25 && value.slider_u5qjpo25.unit ? slider_u5qjpo25Function(value.slider_u5qjpo25.unit, 'max') : 100,
            step: value.slider_u5qjpo25 && value.slider_u5qjpo25.unit ? slider_u5qjpo25Function(value.slider_u5qjpo25.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: v => { value.slider_u5qjpo25 = v; onChange(value); },
            }), 

                    )]
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Hide Dots`),
            
            value: switcher_ndy3n025,
            
            onChange: (value) => setAttributes({ switcher_ndy3n025: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_bif8k123,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_bif8k123: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_233f5323,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_233f5323: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_1h3ige23,
            onChange: (value) => setAttributes({ typography_1h3ige23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_n20mja23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_n20mja23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_8ogj2523,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_8ogj2523: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_zktpcq25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_zktpcq25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_9bnmu323,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_9bnmu323: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cxzbii23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cxzbii23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_3c9xeb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3c9xeb23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_mx4zmv23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_mx4zmv23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_o3pr2p25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_o3pr2p25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_lkfruq23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_lkfruq23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_53icu923,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_53icu923: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_3o5j1323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3o5j1323: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_pyssax23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_pyssax23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_bigkmu25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_bigkmu25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_86eja723,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_86eja723: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lkm4x623,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lkm4x623: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_3z6knn23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_3z6knn23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_b0h9jc23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_b0h9jc23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_vzlmnv23,
            onChange: (value) => setAttributes({ typography_vzlmnv23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_680ioy23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_680ioy23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_k6fbox23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_k6fbox23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_zhnveg25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_zhnveg25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_v44q3m23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_v44q3m23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_0i4x0723,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_0i4x0723: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_w4r0a323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_w4r0a323: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_mc4qm323,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_mc4qm323: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_u2ryg125,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_u2ryg125: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_nm4ole23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_nm4ole23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_157mrw23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_157mrw23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_89cfrb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_89cfrb23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_x8w26v23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_x8w26v23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_dxl2pf25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_dxl2pf25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_g1pgu623,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_g1pgu623: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_6ql7u123,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_6ql7u123: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Image Background"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_aspodx23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_aspodx23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_4x0w2w25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_4x0w2w25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_topbdb23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_topbdb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lksfpm23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lksfpm23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_49etgq23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_49etgq23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_id88ei25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_id88ei25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_pviklb23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_pviklb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_68jo8123,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_68jo8123: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_jrkmwe23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_jrkmwe23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_7qh4n025,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_7qh4n025: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_acct6i23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_acct6i23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_semqb423,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_semqb423: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Box Bottom Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Outer Padding`),
            value: dimension_n07xme23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_n07xme23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Inner Padding`),
            value: dimension_jm7euq23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jm7euq23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_5yu1lh23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5yu1lh23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_oxyjao23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_oxyjao23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_gagobg25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_gagobg25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_7l2uiy23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_7l2uiy23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_aao2ma23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_aao2ma23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_k1erq723,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_k1erq723: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_4wubbb25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_4wubbb25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_hscjnl23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_hscjnl23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_km1yt823,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_km1yt823: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_qn45xl23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qn45xl23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_e9dbnc25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_e9dbnc25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_3c6vmm23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_3c6vmm23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_g92rip23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_g92rip23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
( !switcher_ndy3n025 ) && React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_yiezxy23,
            separator:"default",
            inlineblock: true,
            }), 
( !switcher_ndy3n025 ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_1116ag23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1116ag23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_n7pjum23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_n7pjum23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_9lb9qf23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9lb9qf23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-16z8el24", block_id, false, props.clientId);
                }
            }

            
let g_switcher_ndy3n025 = switcher_ndy3n025 && switcher_ndy3n025 != undefined  ? 'yes' : "";
let g_choose_bif8k123 = choose_bif8k123 && choose_bif8k123 != undefined  ? choose_bif8k123 : "";
let g_dimension_233f5323 = dimension_233f5323 && dimension_233f5323 != undefined  ? dimension_233f5323 : "";
let g_typography_1h3ige23 = typography_1h3ige23 && typography_1h3ige23 != undefined  ? typography_1h3ige23 : "";
let g_color_n20mja23 = color_n20mja23 && color_n20mja23 != undefined  ? color_n20mja23 : "";
let g_color_3c9xeb23 = color_3c9xeb23 && color_3c9xeb23 != undefined  ? color_3c9xeb23 : "";
let g_background_8ogj2523 = background_8ogj2523 && background_8ogj2523 != undefined  ? background_8ogj2523 : "";
let g_background_mx4zmv23 = background_mx4zmv23 && background_mx4zmv23 != undefined  ? background_mx4zmv23 : "";
let g_border_zktpcq25 = border_zktpcq25 && border_zktpcq25 != undefined  ? border_zktpcq25 : "";
let g_border_o3pr2p25 = border_o3pr2p25 && border_o3pr2p25 != undefined  ? border_o3pr2p25 : "";
let g_dimension_9bnmu323 = dimension_9bnmu323 && dimension_9bnmu323 != undefined  ? dimension_9bnmu323 : "";
let g_dimension_lkfruq23 = dimension_lkfruq23 && dimension_lkfruq23 != undefined  ? dimension_lkfruq23 : "";
let g_boxshadow_53icu923 = boxshadow_53icu923 && boxshadow_53icu923 != undefined  ? boxshadow_53icu923 : "";
let g_boxshadow_cxzbii23 = boxshadow_cxzbii23 && boxshadow_cxzbii23 != undefined  ? boxshadow_cxzbii23 : "";
let g_color_3o5j1323 = color_3o5j1323 && color_3o5j1323 != undefined  ? color_3o5j1323 : "";
let g_background_pyssax23 = background_pyssax23 && background_pyssax23 != undefined  ? background_pyssax23 : "";
let g_border_bigkmu25 = border_bigkmu25 && border_bigkmu25 != undefined  ? border_bigkmu25 : "";
let g_dimension_86eja723 = dimension_86eja723 && dimension_86eja723 != undefined  ? dimension_86eja723 : "";
let g_boxshadow_lkm4x623 = boxshadow_lkm4x623 && boxshadow_lkm4x623 != undefined  ? boxshadow_lkm4x623 : "";
let g_choose_3z6knn23 = choose_3z6knn23 && choose_3z6knn23 != undefined  ? choose_3z6knn23 : "";
let g_dimension_b0h9jc23 = dimension_b0h9jc23 && dimension_b0h9jc23 != undefined  ? dimension_b0h9jc23 : "";
let g_typography_vzlmnv23 = typography_vzlmnv23 && typography_vzlmnv23 != undefined  ? typography_vzlmnv23 : "";
let g_color_680ioy23 = color_680ioy23 && color_680ioy23 != undefined  ? color_680ioy23 : "";
let g_color_w4r0a323 = color_w4r0a323 && color_w4r0a323 != undefined  ? color_w4r0a323 : "";
let g_background_k6fbox23 = background_k6fbox23 && background_k6fbox23 != undefined  ? background_k6fbox23 : "";
let g_background_mc4qm323 = background_mc4qm323 && background_mc4qm323 != undefined  ? background_mc4qm323 : "";
let g_border_zhnveg25 = border_zhnveg25 && border_zhnveg25 != undefined  ? border_zhnveg25 : "";
let g_border_u2ryg125 = border_u2ryg125 && border_u2ryg125 != undefined  ? border_u2ryg125 : "";
let g_dimension_v44q3m23 = dimension_v44q3m23 && dimension_v44q3m23 != undefined  ? dimension_v44q3m23 : "";
let g_dimension_nm4ole23 = dimension_nm4ole23 && dimension_nm4ole23 != undefined  ? dimension_nm4ole23 : "";
let g_boxshadow_0i4x0723 = boxshadow_0i4x0723 && boxshadow_0i4x0723 != undefined  ? boxshadow_0i4x0723 : "";
let g_boxshadow_157mrw23 = boxshadow_157mrw23 && boxshadow_157mrw23 != undefined  ? boxshadow_157mrw23 : "";
let g_color_89cfrb23 = color_89cfrb23 && color_89cfrb23 != undefined  ? color_89cfrb23 : "";
let g_background_x8w26v23 = background_x8w26v23 && background_x8w26v23 != undefined  ? background_x8w26v23 : "";
let g_border_dxl2pf25 = border_dxl2pf25 && border_dxl2pf25 != undefined  ? border_dxl2pf25 : "";
let g_dimension_g1pgu623 = dimension_g1pgu623 && dimension_g1pgu623 != undefined  ? dimension_g1pgu623 : "";
let g_boxshadow_6ql7u123 = boxshadow_6ql7u123 && boxshadow_6ql7u123 != undefined  ? boxshadow_6ql7u123 : "";
let g_background_aspodx23 = background_aspodx23 && background_aspodx23 != undefined  ? background_aspodx23 : "";
let g_border_4x0w2w25 = border_4x0w2w25 && border_4x0w2w25 != undefined  ? border_4x0w2w25 : "";
let g_dimension_topbdb23 = dimension_topbdb23 && dimension_topbdb23 != undefined  ? dimension_topbdb23 : "";
let g_background_49etgq23 = background_49etgq23 && background_49etgq23 != undefined  ? background_49etgq23 : "";
let g_border_id88ei25 = border_id88ei25 && border_id88ei25 != undefined  ? border_id88ei25 : "";
let g_dimension_pviklb23 = dimension_pviklb23 && dimension_pviklb23 != undefined  ? dimension_pviklb23 : "";
let g_background_jrkmwe23 = background_jrkmwe23 && background_jrkmwe23 != undefined  ? background_jrkmwe23 : "";
let g_border_7qh4n025 = border_7qh4n025 && border_7qh4n025 != undefined  ? border_7qh4n025 : "";
let g_dimension_acct6i23 = dimension_acct6i23 && dimension_acct6i23 != undefined  ? dimension_acct6i23 : "";
let g_boxshadow_lksfpm23 = boxshadow_lksfpm23 && boxshadow_lksfpm23 != undefined  ? boxshadow_lksfpm23 : "";
let g_boxshadow_68jo8123 = boxshadow_68jo8123 && boxshadow_68jo8123 != undefined  ? boxshadow_68jo8123 : "";
let g_boxshadow_semqb423 = boxshadow_semqb423 && boxshadow_semqb423 != undefined  ? boxshadow_semqb423 : "";
let g_dimension_n07xme23 = dimension_n07xme23 && dimension_n07xme23 != undefined  ? dimension_n07xme23 : "";
let g_dimension_jm7euq23 = dimension_jm7euq23 && dimension_jm7euq23 != undefined  ? dimension_jm7euq23 : "";
let g_dimension_5yu1lh23 = dimension_5yu1lh23 && dimension_5yu1lh23 != undefined  ? dimension_5yu1lh23 : "";
let g_background_oxyjao23 = background_oxyjao23 && background_oxyjao23 != undefined  ? background_oxyjao23 : "";
let g_background_k1erq723 = background_k1erq723 && background_k1erq723 != undefined  ? background_k1erq723 : "";
let g_border_gagobg25 = border_gagobg25 && border_gagobg25 != undefined  ? border_gagobg25 : "";
let g_border_4wubbb25 = border_4wubbb25 && border_4wubbb25 != undefined  ? border_4wubbb25 : "";
let g_dimension_7l2uiy23 = dimension_7l2uiy23 && dimension_7l2uiy23 != undefined  ? dimension_7l2uiy23 : "";
let g_dimension_hscjnl23 = dimension_hscjnl23 && dimension_hscjnl23 != undefined  ? dimension_hscjnl23 : "";
let g_boxshadow_aao2ma23 = boxshadow_aao2ma23 && boxshadow_aao2ma23 != undefined  ? boxshadow_aao2ma23 : "";
let g_boxshadow_km1yt823 = boxshadow_km1yt823 && boxshadow_km1yt823 != undefined  ? boxshadow_km1yt823 : "";
let g_background_qn45xl23 = background_qn45xl23 && background_qn45xl23 != undefined  ? background_qn45xl23 : "";
let g_border_e9dbnc25 = border_e9dbnc25 && border_e9dbnc25 != undefined  ? border_e9dbnc25 : "";
let g_dimension_3c6vmm23 = dimension_3c6vmm23 && dimension_3c6vmm23 != undefined  ? dimension_3c6vmm23 : "";
let g_boxshadow_g92rip23 = boxshadow_g92rip23 && boxshadow_g92rip23 != undefined  ? boxshadow_g92rip23 : "";
let g_heading_yiezxy23 = heading_yiezxy23 && heading_yiezxy23 != undefined && ( !switcher_ndy3n025 ) ? heading_yiezxy23 : "";
let g_color_1116ag23 = color_1116ag23 && color_1116ag23 != undefined  ? color_1116ag23 : "";
let g_color_n7pjum23 = color_n7pjum23 && color_n7pjum23 != undefined  ? color_n7pjum23 : "";
let g_color_9lb9qf23 = color_9lb9qf23 && color_9lb9qf23 != undefined  ? color_9lb9qf23 : "";
            
let repeater_5soxy925_zv25 = "";
                            
repeater_5soxy925  && repeater_5soxy925.map((r_item, index) => {
                                
let grnp_text_zp6ggl25 = r_item.text_zp6ggl25  ? r_item.text_zp6ggl25 : "";
let grnp_textarea_nli4fl25 = r_item.textarea_nli4fl25  ? r_item.textarea_nli4fl25 : "";
let grnp_media_r33bn225 = r_item?.media_r33bn225?.url != undefined  ? r_item?.media_r33bn225.url : "";
let grnp_color_kenc8y25 = r_item.color_kenc8y25  ? r_item.color_kenc8y25 : "";
let grnp_color_v9ifi625 = r_item.color_v9ifi625  ? r_item.color_v9ifi625 : "";
let grnp_color_on9fie25 = r_item.color_on9fie25  ? r_item.color_on9fie25 : "";
let grnp_color_m8tsz925 = r_item.color_m8tsz925  ? r_item.color_m8tsz925 : "";
let grnp_heading_3pg6p825 = r_item.heading_3pg6p825  ? r_item.heading_3pg6p825 : "";
let grnp_background_pg495j25 = r_item.background_pg495j25  ? r_item.background_pg495j25 : "";
let grnp_background_773ajn25 = r_item.background_773ajn25  ? r_item.background_773ajn25 : "";
let grnp_color_balhl625 = r_item.color_balhl625  ? r_item.color_balhl625 : "";
let grnp_color_shybie25 = r_item.color_shybie25  ? r_item.color_shybie25 : "";
                                repeater_5soxy925_zv25 += `<div class="tp-repeater-item-${r_item._key} owl-item splide__slide" data-repeater_5soxy925="{repeater_5soxy925}">
                    <div class="wkit-img-accord-item" style="background-image: url(${grnp_media_r33bn225});">
                        <div class="wkit-img-accord-items">
                            <h3 class="wkit-img-accord-item-title">${grnp_text_zp6ggl25}</h3>
                            <span class="wkit-img-accord-item-desc">${grnp_textarea_nli4fl25}</span>
                        </div> 
                    </div>
                </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_16z8el24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-img-accrod-main-wrap">
    <div class="owl-carousel custom-carousel splide owl-theme owl-loaded owl-drag wkit-custom-dots-${g_switcher_ndy3n025}">
        <div class="owl-stage-outer splide__track">
            <div class="owl-stage splide__list">
                ${repeater_5soxy925_zv25}
            </div>
        </div>
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
            repeater_5soxy925,
switcher_ndy3n025,
choose_bif8k123,
dimension_233f5323,
typography_1h3ige23,
color_n20mja23,
color_3c9xeb23,
background_8ogj2523,
background_mx4zmv23,
border_zktpcq25,
border_o3pr2p25,
dimension_9bnmu323,
dimension_lkfruq23,
boxshadow_53icu923,
boxshadow_cxzbii23,
color_3o5j1323,
background_pyssax23,
border_bigkmu25,
dimension_86eja723,
boxshadow_lkm4x623,
normalhover_4kfgwq23,
choose_3z6knn23,
dimension_b0h9jc23,
typography_vzlmnv23,
color_680ioy23,
color_w4r0a323,
background_k6fbox23,
background_mc4qm323,
border_zhnveg25,
border_u2ryg125,
dimension_v44q3m23,
dimension_nm4ole23,
boxshadow_0i4x0723,
boxshadow_157mrw23,
color_89cfrb23,
background_x8w26v23,
border_dxl2pf25,
dimension_g1pgu623,
boxshadow_6ql7u123,
normalhover_wt4ofn23,
background_aspodx23,
border_4x0w2w25,
dimension_topbdb23,
background_49etgq23,
border_id88ei25,
dimension_pviklb23,
background_jrkmwe23,
border_7qh4n025,
dimension_acct6i23,
boxshadow_lksfpm23,
boxshadow_68jo8123,
boxshadow_semqb423,
normalhover_y7pfes23,
dimension_n07xme23,
dimension_jm7euq23,
dimension_5yu1lh23,
background_oxyjao23,
background_k1erq723,
border_gagobg25,
border_4wubbb25,
dimension_7l2uiy23,
dimension_hscjnl23,
boxshadow_aao2ma23,
boxshadow_km1yt823,
background_qn45xl23,
border_e9dbnc25,
dimension_3c6vmm23,
boxshadow_g92rip23,
normalhover_x4k9vo23,
heading_yiezxy23,
color_1116ag23,
color_n7pjum23,
color_9lb9qf23,
normalhover_zcwqza23,

            block_id,
        } = attributes;

        

        

        

        
let g_switcher_ndy3n025 = switcher_ndy3n025 && switcher_ndy3n025 != undefined  ? 'yes' : "";
let g_choose_bif8k123 = choose_bif8k123 && choose_bif8k123 != undefined  ? choose_bif8k123 : "";
let g_dimension_233f5323 = dimension_233f5323 && dimension_233f5323 != undefined  ? dimension_233f5323 : "";
let g_typography_1h3ige23 = typography_1h3ige23 && typography_1h3ige23 != undefined  ? typography_1h3ige23 : "";
let g_color_n20mja23 = color_n20mja23 && color_n20mja23 != undefined  ? color_n20mja23 : "";
let g_color_3c9xeb23 = color_3c9xeb23 && color_3c9xeb23 != undefined  ? color_3c9xeb23 : "";
let g_background_8ogj2523 = background_8ogj2523 && background_8ogj2523 != undefined  ? background_8ogj2523 : "";
let g_background_mx4zmv23 = background_mx4zmv23 && background_mx4zmv23 != undefined  ? background_mx4zmv23 : "";
let g_border_zktpcq25 = border_zktpcq25 && border_zktpcq25 != undefined  ? border_zktpcq25 : "";
let g_border_o3pr2p25 = border_o3pr2p25 && border_o3pr2p25 != undefined  ? border_o3pr2p25 : "";
let g_dimension_9bnmu323 = dimension_9bnmu323 && dimension_9bnmu323 != undefined  ? dimension_9bnmu323 : "";
let g_dimension_lkfruq23 = dimension_lkfruq23 && dimension_lkfruq23 != undefined  ? dimension_lkfruq23 : "";
let g_boxshadow_53icu923 = boxshadow_53icu923 && boxshadow_53icu923 != undefined  ? boxshadow_53icu923 : "";
let g_boxshadow_cxzbii23 = boxshadow_cxzbii23 && boxshadow_cxzbii23 != undefined  ? boxshadow_cxzbii23 : "";
let g_color_3o5j1323 = color_3o5j1323 && color_3o5j1323 != undefined  ? color_3o5j1323 : "";
let g_background_pyssax23 = background_pyssax23 && background_pyssax23 != undefined  ? background_pyssax23 : "";
let g_border_bigkmu25 = border_bigkmu25 && border_bigkmu25 != undefined  ? border_bigkmu25 : "";
let g_dimension_86eja723 = dimension_86eja723 && dimension_86eja723 != undefined  ? dimension_86eja723 : "";
let g_boxshadow_lkm4x623 = boxshadow_lkm4x623 && boxshadow_lkm4x623 != undefined  ? boxshadow_lkm4x623 : "";
let g_choose_3z6knn23 = choose_3z6knn23 && choose_3z6knn23 != undefined  ? choose_3z6knn23 : "";
let g_dimension_b0h9jc23 = dimension_b0h9jc23 && dimension_b0h9jc23 != undefined  ? dimension_b0h9jc23 : "";
let g_typography_vzlmnv23 = typography_vzlmnv23 && typography_vzlmnv23 != undefined  ? typography_vzlmnv23 : "";
let g_color_680ioy23 = color_680ioy23 && color_680ioy23 != undefined  ? color_680ioy23 : "";
let g_color_w4r0a323 = color_w4r0a323 && color_w4r0a323 != undefined  ? color_w4r0a323 : "";
let g_background_k6fbox23 = background_k6fbox23 && background_k6fbox23 != undefined  ? background_k6fbox23 : "";
let g_background_mc4qm323 = background_mc4qm323 && background_mc4qm323 != undefined  ? background_mc4qm323 : "";
let g_border_zhnveg25 = border_zhnveg25 && border_zhnveg25 != undefined  ? border_zhnveg25 : "";
let g_border_u2ryg125 = border_u2ryg125 && border_u2ryg125 != undefined  ? border_u2ryg125 : "";
let g_dimension_v44q3m23 = dimension_v44q3m23 && dimension_v44q3m23 != undefined  ? dimension_v44q3m23 : "";
let g_dimension_nm4ole23 = dimension_nm4ole23 && dimension_nm4ole23 != undefined  ? dimension_nm4ole23 : "";
let g_boxshadow_0i4x0723 = boxshadow_0i4x0723 && boxshadow_0i4x0723 != undefined  ? boxshadow_0i4x0723 : "";
let g_boxshadow_157mrw23 = boxshadow_157mrw23 && boxshadow_157mrw23 != undefined  ? boxshadow_157mrw23 : "";
let g_color_89cfrb23 = color_89cfrb23 && color_89cfrb23 != undefined  ? color_89cfrb23 : "";
let g_background_x8w26v23 = background_x8w26v23 && background_x8w26v23 != undefined  ? background_x8w26v23 : "";
let g_border_dxl2pf25 = border_dxl2pf25 && border_dxl2pf25 != undefined  ? border_dxl2pf25 : "";
let g_dimension_g1pgu623 = dimension_g1pgu623 && dimension_g1pgu623 != undefined  ? dimension_g1pgu623 : "";
let g_boxshadow_6ql7u123 = boxshadow_6ql7u123 && boxshadow_6ql7u123 != undefined  ? boxshadow_6ql7u123 : "";
let g_background_aspodx23 = background_aspodx23 && background_aspodx23 != undefined  ? background_aspodx23 : "";
let g_border_4x0w2w25 = border_4x0w2w25 && border_4x0w2w25 != undefined  ? border_4x0w2w25 : "";
let g_dimension_topbdb23 = dimension_topbdb23 && dimension_topbdb23 != undefined  ? dimension_topbdb23 : "";
let g_background_49etgq23 = background_49etgq23 && background_49etgq23 != undefined  ? background_49etgq23 : "";
let g_border_id88ei25 = border_id88ei25 && border_id88ei25 != undefined  ? border_id88ei25 : "";
let g_dimension_pviklb23 = dimension_pviklb23 && dimension_pviklb23 != undefined  ? dimension_pviklb23 : "";
let g_background_jrkmwe23 = background_jrkmwe23 && background_jrkmwe23 != undefined  ? background_jrkmwe23 : "";
let g_border_7qh4n025 = border_7qh4n025 && border_7qh4n025 != undefined  ? border_7qh4n025 : "";
let g_dimension_acct6i23 = dimension_acct6i23 && dimension_acct6i23 != undefined  ? dimension_acct6i23 : "";
let g_boxshadow_lksfpm23 = boxshadow_lksfpm23 && boxshadow_lksfpm23 != undefined  ? boxshadow_lksfpm23 : "";
let g_boxshadow_68jo8123 = boxshadow_68jo8123 && boxshadow_68jo8123 != undefined  ? boxshadow_68jo8123 : "";
let g_boxshadow_semqb423 = boxshadow_semqb423 && boxshadow_semqb423 != undefined  ? boxshadow_semqb423 : "";
let g_dimension_n07xme23 = dimension_n07xme23 && dimension_n07xme23 != undefined  ? dimension_n07xme23 : "";
let g_dimension_jm7euq23 = dimension_jm7euq23 && dimension_jm7euq23 != undefined  ? dimension_jm7euq23 : "";
let g_dimension_5yu1lh23 = dimension_5yu1lh23 && dimension_5yu1lh23 != undefined  ? dimension_5yu1lh23 : "";
let g_background_oxyjao23 = background_oxyjao23 && background_oxyjao23 != undefined  ? background_oxyjao23 : "";
let g_background_k1erq723 = background_k1erq723 && background_k1erq723 != undefined  ? background_k1erq723 : "";
let g_border_gagobg25 = border_gagobg25 && border_gagobg25 != undefined  ? border_gagobg25 : "";
let g_border_4wubbb25 = border_4wubbb25 && border_4wubbb25 != undefined  ? border_4wubbb25 : "";
let g_dimension_7l2uiy23 = dimension_7l2uiy23 && dimension_7l2uiy23 != undefined  ? dimension_7l2uiy23 : "";
let g_dimension_hscjnl23 = dimension_hscjnl23 && dimension_hscjnl23 != undefined  ? dimension_hscjnl23 : "";
let g_boxshadow_aao2ma23 = boxshadow_aao2ma23 && boxshadow_aao2ma23 != undefined  ? boxshadow_aao2ma23 : "";
let g_boxshadow_km1yt823 = boxshadow_km1yt823 && boxshadow_km1yt823 != undefined  ? boxshadow_km1yt823 : "";
let g_background_qn45xl23 = background_qn45xl23 && background_qn45xl23 != undefined  ? background_qn45xl23 : "";
let g_border_e9dbnc25 = border_e9dbnc25 && border_e9dbnc25 != undefined  ? border_e9dbnc25 : "";
let g_dimension_3c6vmm23 = dimension_3c6vmm23 && dimension_3c6vmm23 != undefined  ? dimension_3c6vmm23 : "";
let g_boxshadow_g92rip23 = boxshadow_g92rip23 && boxshadow_g92rip23 != undefined  ? boxshadow_g92rip23 : "";
let g_heading_yiezxy23 = heading_yiezxy23 && heading_yiezxy23 != undefined && ( !switcher_ndy3n025 ) ? heading_yiezxy23 : "";
let g_color_1116ag23 = color_1116ag23 && color_1116ag23 != undefined  ? color_1116ag23 : "";
let g_color_n7pjum23 = color_n7pjum23 && color_n7pjum23 != undefined  ? color_n7pjum23 : "";
let g_color_9lb9qf23 = color_9lb9qf23 && color_9lb9qf23 != undefined  ? color_9lb9qf23 : "";
        
let repeater_5soxy925_zv25 = "";
                            
repeater_5soxy925  && repeater_5soxy925.map((r_item, index) => {
                                
let grnp_text_zp6ggl25 = r_item.text_zp6ggl25  ? r_item.text_zp6ggl25 : "";
let grnp_textarea_nli4fl25 = r_item.textarea_nli4fl25  ? r_item.textarea_nli4fl25 : "";
let grnp_media_r33bn225 = r_item?.media_r33bn225?.url != undefined  ? r_item?.media_r33bn225.url : "";
let grnp_color_kenc8y25 = r_item.color_kenc8y25  ? r_item.color_kenc8y25 : "";
let grnp_color_v9ifi625 = r_item.color_v9ifi625  ? r_item.color_v9ifi625 : "";
let grnp_color_on9fie25 = r_item.color_on9fie25  ? r_item.color_on9fie25 : "";
let grnp_color_m8tsz925 = r_item.color_m8tsz925  ? r_item.color_m8tsz925 : "";
let grnp_heading_3pg6p825 = r_item.heading_3pg6p825  ? r_item.heading_3pg6p825 : "";
let grnp_background_pg495j25 = r_item.background_pg495j25  ? r_item.background_pg495j25 : "";
let grnp_background_773ajn25 = r_item.background_773ajn25  ? r_item.background_773ajn25 : "";
let grnp_color_balhl625 = r_item.color_balhl625  ? r_item.color_balhl625 : "";
let grnp_color_shybie25 = r_item.color_shybie25  ? r_item.color_shybie25 : "";
                                repeater_5soxy925_zv25 += `<div class="tp-repeater-item-${r_item._key} owl-item splide__slide" data-repeater_5soxy925="{repeater_5soxy925}">
                    <div class="wkit-img-accord-item" style="background-image: url(${grnp_media_r33bn225});">
                        <div class="wkit-img-accord-items">
                            <h3 class="wkit-img-accord-item-title">${grnp_text_zp6ggl25}</h3>
                            <span class="wkit-img-accord-item-desc">${grnp_textarea_nli4fl25}</span>
                        </div> 
                    </div>
                </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-16z8el24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_16z8el24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-img-accrod-main-wrap">
    <div class="owl-carousel custom-carousel splide owl-theme owl-loaded owl-drag wkit-custom-dots-${g_switcher_ndy3n025}">
        <div class="owl-stage-outer splide__track">
            <div class="owl-stage splide__list">
                ${repeater_5soxy925_zv25}
            </div>
        </div>
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
    new Image_Accordion_16z8el24();