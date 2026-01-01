
    class Image_Stack_qnswav24 {
        constructor() {
            this.Image_Stack_qnswav24_0z3qgj25();
        }
    
        Image_Stack_qnswav24_0z3qgj25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_IconList,Pmgc_Media,Pmgc_Url,Pmgc_Text,Pmgc_Color,Pmgc_Background,Pmgc_Repeater,Pmgc_RadioAdvanced,Pmgc_Range,Pmgc_Toggle,Pmgc_Note,Pmgc_Border,Pmgc_Dimension,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Typography,
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
   
    registerBlockType('wdkit/wb-qnswav24', {
        title: __('Image Stack'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fab fa-stack-overflow tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Image Layouts'),__('Image Stacking'),__('Creative Design'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_wijenl25Function = (unit, type) => {
                var g_slider_wijenl25_list = [];
                g_slider_wijenl25_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_wijenl25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_wijenl25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_wijenl25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_wijenl25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_wijenl25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_wijenl25_list[unit][type];
            };
const slider_6mmriz25Function = (unit, type) => {
                var g_slider_6mmriz25_list = [];
                g_slider_6mmriz25_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_6mmriz25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_6mmriz25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_6mmriz25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6mmriz25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6mmriz25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6mmriz25_list[unit][type];
            };
const slider_p4birw24Function = (unit, type) => {
                var g_slider_p4birw24_list = [];
                g_slider_p4birw24_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_p4birw24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_p4birw24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_p4birw24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_p4birw24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_p4birw24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_p4birw24_list[unit][type];
            };
const slider_3s6ybd24Function = (unit, type) => {
                var g_slider_3s6ybd24_list = [];
                g_slider_3s6ybd24_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_3s6ybd24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_3s6ybd24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_3s6ybd24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_3s6ybd24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_3s6ybd24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_3s6ybd24_list[unit][type];
            };
const slider_6gixpu23Function = (unit, type) => {
                var g_slider_6gixpu23_list = [];
                g_slider_6gixpu23_list['px'] = { "type": "px", "min": -100, "max": 200, "step": 1 };
g_slider_6gixpu23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_6gixpu23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_6gixpu23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6gixpu23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6gixpu23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6gixpu23_list[unit][type];
            };
const slider_5tcccd23Function = (unit, type) => {
                var g_slider_5tcccd23_list = [];
                g_slider_5tcccd23_list['px'] = { "type": "px", "min": -100, "max": 200, "step": 1 };
g_slider_5tcccd23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_5tcccd23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5tcccd23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5tcccd23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5tcccd23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5tcccd23_list[unit][type];
            };
const slider_ia0q8t23Function = (unit, type) => {
                var g_slider_ia0q8t23_list = [];
                g_slider_ia0q8t23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ia0q8t23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_ia0q8t23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ia0q8t23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ia0q8t23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ia0q8t23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ia0q8t23_list[unit][type];
            };
const slider_f4dtud24Function = (unit, type) => {
                var g_slider_f4dtud24_list = [];
                g_slider_f4dtud24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_f4dtud24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_f4dtud24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_f4dtud24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_f4dtud24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_f4dtud24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_f4dtud24_list[unit][type];
            };
const slider_7txiow24Function = (unit, type) => {
                var g_slider_7txiow24_list = [];
                g_slider_7txiow24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_7txiow24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_7txiow24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_7txiow24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_7txiow24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_7txiow24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_7txiow24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_9p8fdj23,
choose_0rlnnb23,
slider_wijenl25,
slider_6mmriz25,
switcher_3uiacy25,
switcher_ioybgi25,
rawhtml_ixbrb325,
slider_p4birw24,
slider_3s6ybd24,
slider_6gixpu23,
slider_5tcccd23,
background_axpy1723,
border_ehuvwy23,
dimension_161nab23,
boxshadow_q3zdrm23,
background_dr4afo23,
border_g8z7vx23,
boxshadow_wfd8us23,
normalhover_oze7uu23,
rawhtml_mhr5y725,
slider_ia0q8t23,
color_in59z923,
color_c3gyg123,
normalhover_ro959m23,
slider_f4dtud24,
slider_7txiow24,
dimension_ype7ov23,
typography_2lfzej23,
color_btxeeu23,
color_oemd0023,
color_7f269w23,
border_733unm25,
dimension_2dcnaj23,
boxshadow_gpce3d23,

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
                        main_function_qnswav24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_qnswav24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getimage = $scope[0].querySelector(".wkit-image-stack");
let btn = getimage.querySelector('.wkit-img-stack-inner');
let links = getimage.querySelectorAll(".wkit-stack-item");

const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

links.forEach(link => {
    link.addEventListener('click', (e) => {
        if (
            (isMobile && !btn.classList.contains('disable-mobile-link')) ||
            (isTablet && !btn.classList.contains('disable-tablet-link'))
        ) {
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
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Stack`),
            value: repeater_9p8fdj23,
            attributeName: 'repeater_9p8fdj23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_9p8fdj23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Select, {
                label: __(`Icon Type`),
                options:[['icon',__('Icon')],['image',__('Image')],],
                separator:"default",
                
                
                value: value.select_l7xtcr23,
                onChange: v => { value.select_l7xtcr23 = v; onChange(value); },
            }),
( value.select_l7xtcr23 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_pl2cdy23,
            separator:'default',
            onChange: v => { value.iconscontrol_pl2cdy23 = v; onChange(value); },
            }), 
( value.select_l7xtcr23 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_fu34yt23,
                
                
                type: ["image","svg"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_fu34yt23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: value.url_gyefyc23,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.url_gyefyc23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tooltip Text`),
                type: "text",
                value: value.text_33si1x23,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_33si1x23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Tooltip Position`),
                options:[['top',__('Top')],['bottom',__('Bottom')],['left',__('Left')],['right',__('Right')],],
                separator:"default",
                
                
                value: value.select_6y68bz23,
                onChange: v => { value.select_6y68bz23 = v; onChange(value); },
            }),
( value.select_l7xtcr23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_5z824i23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_5z824i23 = v; onChange(value); },
            }), 
( value.select_l7xtcr23 == "icon" ) && React.createElement(Pmgc_Background, {
            
            value: value.background_usybbd23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: v => { value.background_usybbd23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Border Color`),
            value: value.color_bn7iy123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_bn7iy123 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_0rlnnb23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_0rlnnb23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_wijenl25,
            
            min: slider_wijenl25 && slider_wijenl25.unit ? slider_wijenl25Function(slider_wijenl25.unit, 'min') : 0,
            max: slider_wijenl25 && slider_wijenl25.unit ? slider_wijenl25Function(slider_wijenl25.unit, 'max') : 100,
            step: slider_wijenl25 && slider_wijenl25.unit ? slider_wijenl25Function(slider_wijenl25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_wijenl25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_6mmriz25,
            
            min: slider_6mmriz25 && slider_6mmriz25.unit ? slider_6mmriz25Function(slider_6mmriz25.unit, 'min') : 0,
            max: slider_6mmriz25 && slider_6mmriz25.unit ? slider_6mmriz25Function(slider_6mmriz25.unit, 'max') : 100,
            step: slider_6mmriz25 && slider_6mmriz25.unit ? slider_6mmriz25Function(slider_6mmriz25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6mmriz25: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_3uiacy25,
            
            onChange: (value) => setAttributes({ switcher_3uiacy25: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_ioybgi25,
            
            onChange: (value) => setAttributes({ switcher_ioybgi25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ixbrb325,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/image-stack-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_ixbrb325: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Items"), initialOpen: true },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_p4birw24,
            
            min: slider_p4birw24 && slider_p4birw24.unit ? slider_p4birw24Function(slider_p4birw24.unit, 'min') : 0,
            max: slider_p4birw24 && slider_p4birw24.unit ? slider_p4birw24Function(slider_p4birw24.unit, 'max') : 100,
            step: slider_p4birw24 && slider_p4birw24.unit ? slider_p4birw24Function(slider_p4birw24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_p4birw24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_3s6ybd24,
            
            min: slider_3s6ybd24 && slider_3s6ybd24.unit ? slider_3s6ybd24Function(slider_3s6ybd24.unit, 'min') : 0,
            max: slider_3s6ybd24 && slider_3s6ybd24.unit ? slider_3s6ybd24Function(slider_3s6ybd24.unit, 'max') : 100,
            step: slider_3s6ybd24 && slider_3s6ybd24.unit ? slider_3s6ybd24Function(slider_3s6ybd24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_3s6ybd24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Range, {
            label: __(`Space`),
            separator:'default',
            value: slider_6gixpu23,
            
            min: slider_6gixpu23 && slider_6gixpu23.unit ? slider_6gixpu23Function(slider_6gixpu23.unit, 'min') : 0,
            max: slider_6gixpu23 && slider_6gixpu23.unit ? slider_6gixpu23Function(slider_6gixpu23.unit, 'max') : 100,
            step: slider_6gixpu23 && slider_6gixpu23.unit ? slider_6gixpu23Function(slider_6gixpu23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6gixpu23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_axpy1723,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_axpy1723: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_ehuvwy23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_ehuvwy23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_161nab23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_161nab23: value }),
            
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_q3zdrm23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_q3zdrm23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Range, {
            label: __(`Space`),
            separator:'default',
            value: slider_5tcccd23,
            
            min: slider_5tcccd23 && slider_5tcccd23.unit ? slider_5tcccd23Function(slider_5tcccd23.unit, 'min') : 0,
            max: slider_5tcccd23 && slider_5tcccd23.unit ? slider_5tcccd23Function(slider_5tcccd23.unit, 'max') : 100,
            step: slider_5tcccd23 && slider_5tcccd23.unit ? slider_5tcccd23Function(slider_5tcccd23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5tcccd23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_dr4afo23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_dr4afo23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_g8z7vx23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_g8z7vx23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_wfd8us23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_wfd8us23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_mhr5y725,
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
If a style is applied from the main card, it should not override or apply to this section.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_mhr5y725: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icons"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_ia0q8t23,
            
            min: slider_ia0q8t23 && slider_ia0q8t23.unit ? slider_ia0q8t23Function(slider_ia0q8t23.unit, 'min') : 0,
            max: slider_ia0q8t23 && slider_ia0q8t23.unit ? slider_ia0q8t23Function(slider_ia0q8t23.unit, 'max') : 100,
            step: slider_ia0q8t23 && slider_ia0q8t23.unit ? slider_ia0q8t23Function(slider_ia0q8t23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ia0q8t23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_in59z923,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_in59z923: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_c3gyg123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_c3gyg123: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width `),
            separator:'default',
            value: slider_f4dtud24,
            
            min: slider_f4dtud24 && slider_f4dtud24.unit ? slider_f4dtud24Function(slider_f4dtud24.unit, 'min') : 0,
            max: slider_f4dtud24 && slider_f4dtud24.unit ? slider_f4dtud24Function(slider_f4dtud24.unit, 'max') : 100,
            step: slider_f4dtud24 && slider_f4dtud24.unit ? slider_f4dtud24Function(slider_f4dtud24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_f4dtud24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_7txiow24,
            
            min: slider_7txiow24 && slider_7txiow24.unit ? slider_7txiow24Function(slider_7txiow24.unit, 'min') : 0,
            max: slider_7txiow24 && slider_7txiow24.unit ? slider_7txiow24Function(slider_7txiow24.unit, 'max') : 100,
            step: slider_7txiow24 && slider_7txiow24.unit ? slider_7txiow24Function(slider_7txiow24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_7txiow24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Tooltip"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ype7ov23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ype7ov23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_2lfzej23,
            onChange: (value) => setAttributes({ typography_2lfzej23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_btxeeu23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_btxeeu23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_oemd0023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_oemd0023: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Arrow Color`),
            value: color_7f269w23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7f269w23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_733unm25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_733unm25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_2dcnaj23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_2dcnaj23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_gpce3d23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_gpce3d23: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-qnswav24", block_id, false, props.clientId);
                }
            }

            
let g_choose_0rlnnb23 = choose_0rlnnb23 && choose_0rlnnb23 != undefined  ? choose_0rlnnb23 : "";
let g_switcher_3uiacy25 = switcher_3uiacy25 && switcher_3uiacy25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_ioybgi25 = switcher_ioybgi25 && switcher_ioybgi25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_ixbrb325 = rawhtml_ixbrb325 && rawhtml_ixbrb325 != undefined  ? rawhtml_ixbrb325 : "";
let g_background_axpy1723 = background_axpy1723 && background_axpy1723 != undefined  ? background_axpy1723 : "";
let g_border_ehuvwy23 = border_ehuvwy23 && border_ehuvwy23 != undefined  ? border_ehuvwy23 : "";
let g_dimension_161nab23 = dimension_161nab23 && dimension_161nab23 != undefined  ? dimension_161nab23 : "";
let g_boxshadow_q3zdrm23 = boxshadow_q3zdrm23 && boxshadow_q3zdrm23 != undefined  ? boxshadow_q3zdrm23 : "";
let g_background_dr4afo23 = background_dr4afo23 && background_dr4afo23 != undefined  ? background_dr4afo23 : "";
let g_border_g8z7vx23 = border_g8z7vx23 && border_g8z7vx23 != undefined  ? border_g8z7vx23 : "";
let g_boxshadow_wfd8us23 = boxshadow_wfd8us23 && boxshadow_wfd8us23 != undefined  ? boxshadow_wfd8us23 : "";
let g_rawhtml_mhr5y725 = rawhtml_mhr5y725 && rawhtml_mhr5y725 != undefined  ? rawhtml_mhr5y725 : "";
let g_color_in59z923 = color_in59z923 && color_in59z923 != undefined  ? color_in59z923 : "";
let g_color_c3gyg123 = color_c3gyg123 && color_c3gyg123 != undefined  ? color_c3gyg123 : "";
let g_dimension_ype7ov23 = dimension_ype7ov23 && dimension_ype7ov23 != undefined  ? dimension_ype7ov23 : "";
let g_typography_2lfzej23 = typography_2lfzej23 && typography_2lfzej23 != undefined  ? typography_2lfzej23 : "";
let g_color_btxeeu23 = color_btxeeu23 && color_btxeeu23 != undefined  ? color_btxeeu23 : "";
let g_color_oemd0023 = color_oemd0023 && color_oemd0023 != undefined  ? color_oemd0023 : "";
let g_color_7f269w23 = color_7f269w23 && color_7f269w23 != undefined  ? color_7f269w23 : "";
let g_border_733unm25 = border_733unm25 && border_733unm25 != undefined  ? border_733unm25 : "";
let g_dimension_2dcnaj23 = dimension_2dcnaj23 && dimension_2dcnaj23 != undefined  ? dimension_2dcnaj23 : "";
let g_boxshadow_gpce3d23 = boxshadow_gpce3d23 && boxshadow_gpce3d23 != undefined  ? boxshadow_gpce3d23 : "";
            
let repeater_9p8fdj23_jv25 = "";
                            
repeater_9p8fdj23  && repeater_9p8fdj23.map((r_item, index) => {
                                
let grnp_select_l7xtcr23 = r_item.select_l7xtcr23  ? r_item.select_l7xtcr23 : "";
let grnp_iconscontrol_pl2cdy23 = r_item?.iconscontrol_pl2cdy23 != undefined && ( (r_item?.select_l7xtcr23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_pl2cdy23+'"></i></span>' : '';

let grnp_media_fu34yt23 = r_item?.media_fu34yt23?.url != undefined && ( (r_item?.select_l7xtcr23 == "image") ) ? r_item?.media_fu34yt23.url : "";
let grnp_url_gyefyc23_url = r_item?.url_gyefyc23?.url && r_item?.url_gyefyc23?.url != undefined ?  r_item?.url_gyefyc23.url : "";
let grnp_url_gyefyc23_target = r_item?.url_gyefyc23?.target && r_item?.url_gyefyc23?.target != undefined ?  r_item?.url_gyefyc23.target : "";
let grnp_url_gyefyc23_nofollow = r_item?.url_gyefyc23?.nofollow && r_item?.url_gyefyc23?.nofollow != undefined ?  r_item?.url_gyefyc23.nofollow : "";
let grnp_url_gyefyc23_ctmArt = r_item?.url_gyefyc23?.attr && r_item?.url_gyefyc23?.attr != undefined ?  r_item?.url_gyefyc23.attr : "";
                    let grnp_url_gyefyc23_attr = ''

                    if (grnp_url_gyefyc23_ctmArt) {
                        let main_array = grnp_url_gyefyc23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_gyefyc23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_text_33si1x23 = r_item.text_33si1x23  ? r_item.text_33si1x23 : "";
let grnp_select_6y68bz23 = r_item.select_6y68bz23  ? r_item.select_6y68bz23 : "";
let grnp_color_5z824i23 = r_item.color_5z824i23 && ( (r_item?.select_l7xtcr23 == "icon") ) ? r_item.color_5z824i23 : "";
let grnp_background_usybbd23 = r_item.background_usybbd23 && ( (r_item?.select_l7xtcr23 == "icon") ) ? r_item.background_usybbd23 : "";
let grnp_color_bn7iy123 = r_item.color_bn7iy123  ? r_item.color_bn7iy123 : "";
                                repeater_9p8fdj23_jv25 += `<a href="${grnp_url_gyefyc23_url}" target="${grnp_url_gyefyc23_target}" rel="${grnp_url_gyefyc23_nofollow} noopener" tooltiptext="${grnp_text_33si1x23}" position="${grnp_select_6y68bz23}" class="tp-repeater-item-${r_item._key} wkit-stack-item stack-${grnp_select_l7xtcr23}" data-repeater_9p8fdj23="{repeater_9p8fdj23}">
            ${grnp_iconscontrol_pl2cdy23 }
            <img class="tp-title-icon" src="${grnp_media_fu34yt23}">
        </a>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_qnswav24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-image-stack">
    <div class="wkit-img-stack-inner ${g_switcher_3uiacy25} ${g_switcher_ioybgi25}">
        ${repeater_9p8fdj23_jv25}
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
            repeater_9p8fdj23,
choose_0rlnnb23,
slider_wijenl25,
slider_6mmriz25,
switcher_3uiacy25,
switcher_ioybgi25,
rawhtml_ixbrb325,
slider_p4birw24,
slider_3s6ybd24,
slider_6gixpu23,
slider_5tcccd23,
background_axpy1723,
border_ehuvwy23,
dimension_161nab23,
boxshadow_q3zdrm23,
background_dr4afo23,
border_g8z7vx23,
boxshadow_wfd8us23,
normalhover_oze7uu23,
rawhtml_mhr5y725,
slider_ia0q8t23,
color_in59z923,
color_c3gyg123,
normalhover_ro959m23,
slider_f4dtud24,
slider_7txiow24,
dimension_ype7ov23,
typography_2lfzej23,
color_btxeeu23,
color_oemd0023,
color_7f269w23,
border_733unm25,
dimension_2dcnaj23,
boxshadow_gpce3d23,

            block_id,
        } = attributes;

        

        

        

        
let g_choose_0rlnnb23 = choose_0rlnnb23 && choose_0rlnnb23 != undefined  ? choose_0rlnnb23 : "";
let g_switcher_3uiacy25 = switcher_3uiacy25 && switcher_3uiacy25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_ioybgi25 = switcher_ioybgi25 && switcher_ioybgi25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_ixbrb325 = rawhtml_ixbrb325 && rawhtml_ixbrb325 != undefined  ? rawhtml_ixbrb325 : "";
let g_background_axpy1723 = background_axpy1723 && background_axpy1723 != undefined  ? background_axpy1723 : "";
let g_border_ehuvwy23 = border_ehuvwy23 && border_ehuvwy23 != undefined  ? border_ehuvwy23 : "";
let g_dimension_161nab23 = dimension_161nab23 && dimension_161nab23 != undefined  ? dimension_161nab23 : "";
let g_boxshadow_q3zdrm23 = boxshadow_q3zdrm23 && boxshadow_q3zdrm23 != undefined  ? boxshadow_q3zdrm23 : "";
let g_background_dr4afo23 = background_dr4afo23 && background_dr4afo23 != undefined  ? background_dr4afo23 : "";
let g_border_g8z7vx23 = border_g8z7vx23 && border_g8z7vx23 != undefined  ? border_g8z7vx23 : "";
let g_boxshadow_wfd8us23 = boxshadow_wfd8us23 && boxshadow_wfd8us23 != undefined  ? boxshadow_wfd8us23 : "";
let g_rawhtml_mhr5y725 = rawhtml_mhr5y725 && rawhtml_mhr5y725 != undefined  ? rawhtml_mhr5y725 : "";
let g_color_in59z923 = color_in59z923 && color_in59z923 != undefined  ? color_in59z923 : "";
let g_color_c3gyg123 = color_c3gyg123 && color_c3gyg123 != undefined  ? color_c3gyg123 : "";
let g_dimension_ype7ov23 = dimension_ype7ov23 && dimension_ype7ov23 != undefined  ? dimension_ype7ov23 : "";
let g_typography_2lfzej23 = typography_2lfzej23 && typography_2lfzej23 != undefined  ? typography_2lfzej23 : "";
let g_color_btxeeu23 = color_btxeeu23 && color_btxeeu23 != undefined  ? color_btxeeu23 : "";
let g_color_oemd0023 = color_oemd0023 && color_oemd0023 != undefined  ? color_oemd0023 : "";
let g_color_7f269w23 = color_7f269w23 && color_7f269w23 != undefined  ? color_7f269w23 : "";
let g_border_733unm25 = border_733unm25 && border_733unm25 != undefined  ? border_733unm25 : "";
let g_dimension_2dcnaj23 = dimension_2dcnaj23 && dimension_2dcnaj23 != undefined  ? dimension_2dcnaj23 : "";
let g_boxshadow_gpce3d23 = boxshadow_gpce3d23 && boxshadow_gpce3d23 != undefined  ? boxshadow_gpce3d23 : "";
        
let repeater_9p8fdj23_jv25 = "";
                            
repeater_9p8fdj23  && repeater_9p8fdj23.map((r_item, index) => {
                                
let grnp_select_l7xtcr23 = r_item.select_l7xtcr23  ? r_item.select_l7xtcr23 : "";
let grnp_iconscontrol_pl2cdy23 = r_item?.iconscontrol_pl2cdy23 != undefined && ( (r_item?.select_l7xtcr23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_pl2cdy23+'"></i></span>' : '';

let grnp_media_fu34yt23 = r_item?.media_fu34yt23?.url != undefined && ( (r_item?.select_l7xtcr23 == "image") ) ? r_item?.media_fu34yt23.url : "";
let grnp_url_gyefyc23_url = r_item?.url_gyefyc23?.url && r_item?.url_gyefyc23?.url != undefined ?  r_item?.url_gyefyc23.url : "";
let grnp_url_gyefyc23_target = r_item?.url_gyefyc23?.target && r_item?.url_gyefyc23?.target != undefined ?  r_item?.url_gyefyc23.target : "";
let grnp_url_gyefyc23_nofollow = r_item?.url_gyefyc23?.nofollow && r_item?.url_gyefyc23?.nofollow != undefined ?  r_item?.url_gyefyc23.nofollow : "";
let grnp_url_gyefyc23_ctmArt = r_item?.url_gyefyc23?.attr && r_item?.url_gyefyc23?.attr != undefined ?  r_item?.url_gyefyc23.attr : "";
                    let grnp_url_gyefyc23_attr = ''

                    if (grnp_url_gyefyc23_ctmArt) {
                        let main_array = grnp_url_gyefyc23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_gyefyc23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_text_33si1x23 = r_item.text_33si1x23  ? r_item.text_33si1x23 : "";
let grnp_select_6y68bz23 = r_item.select_6y68bz23  ? r_item.select_6y68bz23 : "";
let grnp_color_5z824i23 = r_item.color_5z824i23 && ( (r_item?.select_l7xtcr23 == "icon") ) ? r_item.color_5z824i23 : "";
let grnp_background_usybbd23 = r_item.background_usybbd23 && ( (r_item?.select_l7xtcr23 == "icon") ) ? r_item.background_usybbd23 : "";
let grnp_color_bn7iy123 = r_item.color_bn7iy123  ? r_item.color_bn7iy123 : "";
                                repeater_9p8fdj23_jv25 += `<a href="${grnp_url_gyefyc23_url}" target="${grnp_url_gyefyc23_target}" rel="${grnp_url_gyefyc23_nofollow} noopener" tooltiptext="${grnp_text_33si1x23}" position="${grnp_select_6y68bz23}" class="tp-repeater-item-${r_item._key} wkit-stack-item stack-${grnp_select_l7xtcr23}" data-repeater_9p8fdj23="{repeater_9p8fdj23}">
            ${grnp_iconscontrol_pl2cdy23 }
            <img class="tp-title-icon" src="${grnp_media_fu34yt23}">
        </a>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-qnswav24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_qnswav24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-image-stack">
    <div class="wkit-img-stack-inner ${g_switcher_3uiacy25} ${g_switcher_ioybgi25}">
        ${repeater_9p8fdj23_jv25}
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
    new Image_Stack_qnswav24();