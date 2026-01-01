
    class CTA_with_Connected_Background_2zud4k24 {
        constructor() {
            this.CTA_with_Connected_Background_2zud4k24_x29mdu25();
        }
    
        CTA_with_Connected_Background_2zud4k24_x29mdu25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_TextArea,Pmgc_IconList,Pmgc_Url,Pmgc_Media,Pmgc_Repeater,Pmgc_RadioAdvanced,Pmgc_Toggle,Pmgc_Note,Pmgc_Range,Pmgc_Typography,Pmgc_Color,Pmgc_Dimension,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Label_Heading,Pmgc_CssFilter,
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
   
    registerBlockType('wdkit/wb-2zud4k24', {
        title: __('CTA with Connected Background'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-stream tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('CTA'),__('Background'),__('Hover Animation'),__('Image Animation'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_x47myl23Function = (unit, type) => {
                var g_slider_x47myl23_list = [];
                g_slider_x47myl23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_x47myl23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_x47myl23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_x47myl23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_x47myl23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_x47myl23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_x47myl23_list[unit][type];
            };
const slider_b78ag323Function = (unit, type) => {
                var g_slider_b78ag323_list = [];
                g_slider_b78ag323_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_b78ag323_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_b78ag323_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_b78ag323_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_b78ag323_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_b78ag323_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_b78ag323_list[unit][type];
            };
const slider_sgeyni23Function = (unit, type) => {
                var g_slider_sgeyni23_list = [];
                g_slider_sgeyni23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_sgeyni23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_sgeyni23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_sgeyni23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_sgeyni23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_sgeyni23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_sgeyni23_list[unit][type];
            };
const slider_8w867j25Function = (unit, type) => {
                var g_slider_8w867j25_list = [];
                g_slider_8w867j25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_8w867j25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_8w867j25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_8w867j25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_8w867j25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_8w867j25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_8w867j25_list[unit][type];
            };
const slider_o25dha25Function = (unit, type) => {
                var g_slider_o25dha25_list = [];
                g_slider_o25dha25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_o25dha25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_o25dha25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_o25dha25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_o25dha25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_o25dha25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_o25dha25_list[unit][type];
            };
const slider_mczafc23Function = (unit, type) => {
                var g_slider_mczafc23_list = [];
                g_slider_mczafc23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_mczafc23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_mczafc23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mczafc23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mczafc23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mczafc23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mczafc23_list[unit][type];
            };
const slider_mqqn4p23Function = (unit, type) => {
                var g_slider_mqqn4p23_list = [];
                g_slider_mqqn4p23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_mqqn4p23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_mqqn4p23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mqqn4p23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mqqn4p23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mqqn4p23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mqqn4p23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_j13meb23,
repeater_lcaihe23,
choose_2ngt9h25,
switcher_xuuqzy23,
rawhtml_8hmk3s25,
slider_x47myl23,
typography_htjkbt23,
color_wzqdjg23,
slider_b78ag323,
slider_sgeyni23,
typography_2ukchn23,
color_eb55o323,
dimension_xgiapc23,
typography_nrxkq123,
color_gu26ox23,
background_s96nkm23,
border_imnep823,
dimension_s6ajgn23,
boxshadow_h63ojg23,
background_yfdhom23,
border_1cjwo623,
boxshadow_y1gqz223,
normalhover_qaaugl23,
slider_8w867j25,
slider_o25dha25,
color_h8wxun25,
color_tv4uk525,
normalhover_o29t3625,
slider_mczafc23,
color_rc7mqv23,
heading_v2fk9n25,
slider_mqqn4p23,
color_d1zd0g23,
dimension_0xtgn424,
cssfilter_anjuwm23,

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
                        main_function_2zud4k24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_2zud4k24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let cBGAll = $scope[0].querySelector('.wkit-cta-with-con-bg');
let gtAllImg = cBGAll.querySelectorAll('.bg-image');
gtAllImg.forEach((el,index)=>{
    if(index==0){
        el.classList.add('active');
    }
});

let gtAllItem = cBGAll.querySelectorAll('.con-image-item');
gtAllItem.forEach((el,index)=>{
    if(index==0){
        el.classList.add('active');
    }
    
    el.addEventListener('mouseenter', (e, index)=>{
        let getbg = e.currentTarget.closest('.cta-with-bg-inner');
        
        let actImg = getbg.querySelector('.bg-image.active');
        if(actImg){
            actImg.classList.remove('active');
        }
        let actItem = getbg.querySelector('.con-image-item.active');
        if(actItem){
            actItem.classList.remove('active');
        }
        
        e.currentTarget.classList.add('active');
        
        let gtIndex = e.currentTarget.getAttribute('data-index');
        
        let newImg = getbg.querySelector('img[data-index="'+gtIndex+'"]');
        newImg.classList.add('active');
        
        console.log(newImg);
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
                options:[['horizontal',__('Style 1')],['vertical',__('Style 2')],],
                separator:"default",
                
                
                value: select_j13meb23,
                onChange: (value) => {setAttributes({ select_j13meb23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Slides`),
            value: repeater_lcaihe23,
            attributeName: 'repeater_lcaihe23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_lcaihe23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_bqwp3l23,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_bqwp3l23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: value.textarea_o2vj9h23,
                
                onChange: v => { value.textarea_o2vj9h23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: value.text_memb1g23,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_memb1g23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_y1qbkp25,
            separator:'default',
            onChange: v => { value.iconscontrol_y1qbkp25 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: value.url_3hvh9k23,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: v => { value.url_3hvh9k23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_uy9yec23,
                
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_uy9yec23 = v; onChange(value); },
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
            value: choose_2ngt9h25,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_2ngt9h25: value }),
            }), 
( select_j13meb23 == "horizontal" ) && React.createElement(Pmgc_Toggle, {
            label: __(`Vertical on Mobile`),
            
            value: switcher_xuuqzy23,
            
            onChange: (value) => setAttributes({ switcher_xuuqzy23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_8hmk3s25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/cta-with-connected-background-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_8hmk3s25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_x47myl23,
            
            min: slider_x47myl23 && slider_x47myl23.unit ? slider_x47myl23Function(slider_x47myl23.unit, 'min') : 0,
            max: slider_x47myl23 && slider_x47myl23.unit ? slider_x47myl23Function(slider_x47myl23.unit, 'max') : 100,
            step: slider_x47myl23 && slider_x47myl23.unit ? slider_x47myl23Function(slider_x47myl23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_x47myl23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_htjkbt23,
            onChange: (value) => setAttributes({ typography_htjkbt23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_wzqdjg23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_wzqdjg23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Top Space`),
            separator:'default',
            value: slider_b78ag323,
            
            min: slider_b78ag323 && slider_b78ag323.unit ? slider_b78ag323Function(slider_b78ag323.unit, 'min') : 0,
            max: slider_b78ag323 && slider_b78ag323.unit ? slider_b78ag323Function(slider_b78ag323.unit, 'max') : 100,
            step: slider_b78ag323 && slider_b78ag323.unit ? slider_b78ag323Function(slider_b78ag323.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_b78ag323: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Space`),
            separator:'default',
            value: slider_sgeyni23,
            
            min: slider_sgeyni23 && slider_sgeyni23.unit ? slider_sgeyni23Function(slider_sgeyni23.unit, 'min') : 0,
            max: slider_sgeyni23 && slider_sgeyni23.unit ? slider_sgeyni23Function(slider_sgeyni23.unit, 'max') : 100,
            step: slider_sgeyni23 && slider_sgeyni23.unit ? slider_sgeyni23Function(slider_sgeyni23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_sgeyni23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_2ukchn23,
            onChange: (value) => setAttributes({ typography_2ukchn23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_eb55o323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_eb55o323: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_xgiapc23,
            noLock: false,
            unit: ['px','em','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_xgiapc23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_nrxkq123,
            onChange: (value) => setAttributes({ typography_nrxkq123: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_gu26ox23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_gu26ox23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_s96nkm23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_s96nkm23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_imnep823,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_imnep823: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_s6ajgn23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_s6ajgn23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_h63ojg23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_h63ojg23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_yfdhom23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_yfdhom23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_1cjwo623,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_1cjwo623: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_y1gqz223,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_y1gqz223: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Button Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_8w867j25,
            
            min: slider_8w867j25 && slider_8w867j25.unit ? slider_8w867j25Function(slider_8w867j25.unit, 'min') : 0,
            max: slider_8w867j25 && slider_8w867j25.unit ? slider_8w867j25Function(slider_8w867j25.unit, 'max') : 100,
            step: slider_8w867j25 && slider_8w867j25.unit ? slider_8w867j25Function(slider_8w867j25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_8w867j25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_o25dha25,
            
            min: slider_o25dha25 && slider_o25dha25.unit ? slider_o25dha25Function(slider_o25dha25.unit, 'min') : 0,
            max: slider_o25dha25 && slider_o25dha25.unit ? slider_o25dha25Function(slider_o25dha25.unit, 'max') : 100,
            step: slider_o25dha25 && slider_o25dha25.unit ? slider_o25dha25Function(slider_o25dha25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_o25dha25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_h8wxun25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_h8wxun25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_tv4uk525,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_tv4uk525: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Slides"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_mczafc23,
            
            min: slider_mczafc23 && slider_mczafc23.unit ? slider_mczafc23Function(slider_mczafc23.unit, 'min') : 0,
            max: slider_mczafc23 && slider_mczafc23.unit ? slider_mczafc23Function(slider_mczafc23.unit, 'max') : 100,
            step: slider_mczafc23 && slider_mczafc23.unit ? slider_mczafc23Function(slider_mczafc23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mczafc23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Overlay Color`),
            value: color_rc7mqv23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rc7mqv23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Border`),
            value: heading_v2fk9n25,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Border Width`),
            separator:'default',
            value: slider_mqqn4p23,
            
            min: slider_mqqn4p23 && slider_mqqn4p23.unit ? slider_mqqn4p23Function(slider_mqqn4p23.unit, 'min') : 0,
            max: slider_mqqn4p23 && slider_mqqn4p23.unit ? slider_mqqn4p23Function(slider_mqqn4p23.unit, 'max') : 100,
            step: slider_mqqn4p23 && slider_mqqn4p23.unit ? slider_mqqn4p23Function(slider_mqqn4p23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mqqn4p23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Border Color`),
            value: color_d1zd0g23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_d1zd0g23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_0xtgn424,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_0xtgn424: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_anjuwm23,
            onChange: (value) => setAttributes({ cssfilter_anjuwm23: value }),
            separator:'default',
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-2zud4k24", block_id, false, props.clientId);
                }
            }

            
let g_select_j13meb23 = select_j13meb23 && select_j13meb23 != undefined  ? select_j13meb23 : "";
let g_choose_2ngt9h25 = choose_2ngt9h25 && choose_2ngt9h25 != undefined  ? choose_2ngt9h25 : "";
let g_switcher_xuuqzy23 = switcher_xuuqzy23 && switcher_xuuqzy23 != undefined && ( (select_j13meb23 == "horizontal") ) ? 'yes' : "";
let g_rawhtml_8hmk3s25 = rawhtml_8hmk3s25 && rawhtml_8hmk3s25 != undefined  ? rawhtml_8hmk3s25 : "";
let g_typography_htjkbt23 = typography_htjkbt23 && typography_htjkbt23 != undefined  ? typography_htjkbt23 : "";
let g_color_wzqdjg23 = color_wzqdjg23 && color_wzqdjg23 != undefined  ? color_wzqdjg23 : "";
let g_typography_2ukchn23 = typography_2ukchn23 && typography_2ukchn23 != undefined  ? typography_2ukchn23 : "";
let g_color_eb55o323 = color_eb55o323 && color_eb55o323 != undefined  ? color_eb55o323 : "";
let g_dimension_xgiapc23 = dimension_xgiapc23 && dimension_xgiapc23 != undefined  ? dimension_xgiapc23 : "";
let g_typography_nrxkq123 = typography_nrxkq123 && typography_nrxkq123 != undefined  ? typography_nrxkq123 : "";
let g_color_gu26ox23 = color_gu26ox23 && color_gu26ox23 != undefined  ? color_gu26ox23 : "";
let g_background_s96nkm23 = background_s96nkm23 && background_s96nkm23 != undefined  ? background_s96nkm23 : "";
let g_border_imnep823 = border_imnep823 && border_imnep823 != undefined  ? border_imnep823 : "";
let g_dimension_s6ajgn23 = dimension_s6ajgn23 && dimension_s6ajgn23 != undefined  ? dimension_s6ajgn23 : "";
let g_boxshadow_h63ojg23 = boxshadow_h63ojg23 && boxshadow_h63ojg23 != undefined  ? boxshadow_h63ojg23 : "";
let g_background_yfdhom23 = background_yfdhom23 && background_yfdhom23 != undefined  ? background_yfdhom23 : "";
let g_border_1cjwo623 = border_1cjwo623 && border_1cjwo623 != undefined  ? border_1cjwo623 : "";
let g_boxshadow_y1gqz223 = boxshadow_y1gqz223 && boxshadow_y1gqz223 != undefined  ? boxshadow_y1gqz223 : "";
let g_color_h8wxun25 = color_h8wxun25 && color_h8wxun25 != undefined  ? color_h8wxun25 : "";
let g_color_tv4uk525 = color_tv4uk525 && color_tv4uk525 != undefined  ? color_tv4uk525 : "";
let g_color_rc7mqv23 = color_rc7mqv23 && color_rc7mqv23 != undefined  ? color_rc7mqv23 : "";
let g_heading_v2fk9n25 = heading_v2fk9n25 && heading_v2fk9n25 != undefined  ? heading_v2fk9n25 : "";
let g_color_d1zd0g23 = color_d1zd0g23 && color_d1zd0g23 != undefined  ? color_d1zd0g23 : "";
let g_dimension_0xtgn424 = dimension_0xtgn424 && dimension_0xtgn424 != undefined  ? dimension_0xtgn424 : "";
let g_cssfilter_anjuwm23 = cssfilter_anjuwm23 && cssfilter_anjuwm23 != undefined  ? cssfilter_anjuwm23 : "";
            
let repeater_lcaihe23_zo25 = "";
                            
repeater_lcaihe23  && repeater_lcaihe23.map((r_item, index) => {
                                
let grnp_text_bqwp3l23 = r_item.text_bqwp3l23  ? r_item.text_bqwp3l23 : "";
let grnp_textarea_o2vj9h23 = r_item.textarea_o2vj9h23  ? r_item.textarea_o2vj9h23 : "";
let grnp_text_memb1g23 = r_item.text_memb1g23  ? r_item.text_memb1g23 : "";
let grnp_iconscontrol_y1qbkp25 = r_item?.iconscontrol_y1qbkp25 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_y1qbkp25+'"></i></span>' : '';

let grnp_url_3hvh9k23_url = r_item?.url_3hvh9k23?.url && r_item?.url_3hvh9k23?.url != undefined ?  r_item?.url_3hvh9k23.url : "";
let grnp_url_3hvh9k23_target = r_item?.url_3hvh9k23?.target && r_item?.url_3hvh9k23?.target != undefined ?  r_item?.url_3hvh9k23.target : "";
let grnp_url_3hvh9k23_nofollow = r_item?.url_3hvh9k23?.nofollow && r_item?.url_3hvh9k23?.nofollow != undefined ?  r_item?.url_3hvh9k23.nofollow : "";
let grnp_url_3hvh9k23_ctmArt = r_item?.url_3hvh9k23?.attr && r_item?.url_3hvh9k23?.attr != undefined ?  r_item?.url_3hvh9k23.attr : "";
                    let grnp_url_3hvh9k23_attr = ''

                    if (grnp_url_3hvh9k23_ctmArt) {
                        let main_array = grnp_url_3hvh9k23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_3hvh9k23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_media_uy9yec23 = r_item?.media_uy9yec23?.url != undefined  ? r_item?.media_uy9yec23.url : "";
                                repeater_lcaihe23_zo25 += `<img class="tp-repeater-item-${r_item._key} bg-image" data-repeater_lcaihe23="{repeater_lcaihe23}" src="${grnp_media_uy9yec23}" data-index="${index}">`;
                            })
let repeater_lcaihe23_q325 = "";
                            
repeater_lcaihe23  && repeater_lcaihe23.map((r_item, index) => {
                                
let grnp_text_bqwp3l23 = r_item.text_bqwp3l23  ? r_item.text_bqwp3l23 : "";
let grnp_textarea_o2vj9h23 = r_item.textarea_o2vj9h23  ? r_item.textarea_o2vj9h23 : "";
let grnp_text_memb1g23 = r_item.text_memb1g23  ? r_item.text_memb1g23 : "";
let grnp_iconscontrol_y1qbkp25 = r_item?.iconscontrol_y1qbkp25 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_y1qbkp25+'"></i></span>' : '';

let grnp_url_3hvh9k23_url = r_item?.url_3hvh9k23?.url && r_item?.url_3hvh9k23?.url != undefined ?  r_item?.url_3hvh9k23.url : "";
let grnp_url_3hvh9k23_target = r_item?.url_3hvh9k23?.target && r_item?.url_3hvh9k23?.target != undefined ?  r_item?.url_3hvh9k23.target : "";
let grnp_url_3hvh9k23_nofollow = r_item?.url_3hvh9k23?.nofollow && r_item?.url_3hvh9k23?.nofollow != undefined ?  r_item?.url_3hvh9k23.nofollow : "";
let grnp_url_3hvh9k23_ctmArt = r_item?.url_3hvh9k23?.attr && r_item?.url_3hvh9k23?.attr != undefined ?  r_item?.url_3hvh9k23.attr : "";
                    let grnp_url_3hvh9k23_attr = ''

                    if (grnp_url_3hvh9k23_ctmArt) {
                        let main_array = grnp_url_3hvh9k23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_3hvh9k23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_media_uy9yec23 = r_item?.media_uy9yec23?.url != undefined  ? r_item?.media_uy9yec23.url : "";
                                repeater_lcaihe23_q325 += `<div class="tp-repeater-item-${r_item._key} con-image-item" data-repeater_lcaihe23="{repeater_lcaihe23}" data-index="${index}">
            <div class="con-item-content">
                <span class="con-i-title" data-title="${grnp_text_bqwp3l23}">${grnp_text_bqwp3l23}</span>
                <p class="con-i-desc" data-desc="${grnp_textarea_o2vj9h23}">${grnp_textarea_o2vj9h23}</p>
                <a class="con-i-btn" href="${grnp_url_3hvh9k23_url}" data-btext="${grnp_text_memb1g23}" target="${grnp_url_3hvh9k23_target}" rel="${grnp_url_3hvh9k23_nofollow} noopener">${grnp_text_memb1g23}${grnp_iconscontrol_y1qbkp25 }</a>
            </div>
        </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_2zud4k24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-cta-with-con-bg">
    <div class="cta-with-bg-inner layout-con-${g_select_j13meb23} vertical-on-mobile-${g_switcher_xuuqzy23}">
        <div class="con-image-background">
            ${repeater_lcaihe23_zo25}
                
            
        </div>
        ${repeater_lcaihe23_q325}
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
            select_j13meb23,
repeater_lcaihe23,
choose_2ngt9h25,
switcher_xuuqzy23,
rawhtml_8hmk3s25,
slider_x47myl23,
typography_htjkbt23,
color_wzqdjg23,
slider_b78ag323,
slider_sgeyni23,
typography_2ukchn23,
color_eb55o323,
dimension_xgiapc23,
typography_nrxkq123,
color_gu26ox23,
background_s96nkm23,
border_imnep823,
dimension_s6ajgn23,
boxshadow_h63ojg23,
background_yfdhom23,
border_1cjwo623,
boxshadow_y1gqz223,
normalhover_qaaugl23,
slider_8w867j25,
slider_o25dha25,
color_h8wxun25,
color_tv4uk525,
normalhover_o29t3625,
slider_mczafc23,
color_rc7mqv23,
heading_v2fk9n25,
slider_mqqn4p23,
color_d1zd0g23,
dimension_0xtgn424,
cssfilter_anjuwm23,

            block_id,
        } = attributes;

        

        

        

        
let g_select_j13meb23 = select_j13meb23 && select_j13meb23 != undefined  ? select_j13meb23 : "";
let g_choose_2ngt9h25 = choose_2ngt9h25 && choose_2ngt9h25 != undefined  ? choose_2ngt9h25 : "";
let g_switcher_xuuqzy23 = switcher_xuuqzy23 && switcher_xuuqzy23 != undefined && ( (select_j13meb23 == "horizontal") ) ? 'yes' : "";
let g_rawhtml_8hmk3s25 = rawhtml_8hmk3s25 && rawhtml_8hmk3s25 != undefined  ? rawhtml_8hmk3s25 : "";
let g_typography_htjkbt23 = typography_htjkbt23 && typography_htjkbt23 != undefined  ? typography_htjkbt23 : "";
let g_color_wzqdjg23 = color_wzqdjg23 && color_wzqdjg23 != undefined  ? color_wzqdjg23 : "";
let g_typography_2ukchn23 = typography_2ukchn23 && typography_2ukchn23 != undefined  ? typography_2ukchn23 : "";
let g_color_eb55o323 = color_eb55o323 && color_eb55o323 != undefined  ? color_eb55o323 : "";
let g_dimension_xgiapc23 = dimension_xgiapc23 && dimension_xgiapc23 != undefined  ? dimension_xgiapc23 : "";
let g_typography_nrxkq123 = typography_nrxkq123 && typography_nrxkq123 != undefined  ? typography_nrxkq123 : "";
let g_color_gu26ox23 = color_gu26ox23 && color_gu26ox23 != undefined  ? color_gu26ox23 : "";
let g_background_s96nkm23 = background_s96nkm23 && background_s96nkm23 != undefined  ? background_s96nkm23 : "";
let g_border_imnep823 = border_imnep823 && border_imnep823 != undefined  ? border_imnep823 : "";
let g_dimension_s6ajgn23 = dimension_s6ajgn23 && dimension_s6ajgn23 != undefined  ? dimension_s6ajgn23 : "";
let g_boxshadow_h63ojg23 = boxshadow_h63ojg23 && boxshadow_h63ojg23 != undefined  ? boxshadow_h63ojg23 : "";
let g_background_yfdhom23 = background_yfdhom23 && background_yfdhom23 != undefined  ? background_yfdhom23 : "";
let g_border_1cjwo623 = border_1cjwo623 && border_1cjwo623 != undefined  ? border_1cjwo623 : "";
let g_boxshadow_y1gqz223 = boxshadow_y1gqz223 && boxshadow_y1gqz223 != undefined  ? boxshadow_y1gqz223 : "";
let g_color_h8wxun25 = color_h8wxun25 && color_h8wxun25 != undefined  ? color_h8wxun25 : "";
let g_color_tv4uk525 = color_tv4uk525 && color_tv4uk525 != undefined  ? color_tv4uk525 : "";
let g_color_rc7mqv23 = color_rc7mqv23 && color_rc7mqv23 != undefined  ? color_rc7mqv23 : "";
let g_heading_v2fk9n25 = heading_v2fk9n25 && heading_v2fk9n25 != undefined  ? heading_v2fk9n25 : "";
let g_color_d1zd0g23 = color_d1zd0g23 && color_d1zd0g23 != undefined  ? color_d1zd0g23 : "";
let g_dimension_0xtgn424 = dimension_0xtgn424 && dimension_0xtgn424 != undefined  ? dimension_0xtgn424 : "";
let g_cssfilter_anjuwm23 = cssfilter_anjuwm23 && cssfilter_anjuwm23 != undefined  ? cssfilter_anjuwm23 : "";
        
let repeater_lcaihe23_zo25 = "";
                            
repeater_lcaihe23  && repeater_lcaihe23.map((r_item, index) => {
                                
let grnp_text_bqwp3l23 = r_item.text_bqwp3l23  ? r_item.text_bqwp3l23 : "";
let grnp_textarea_o2vj9h23 = r_item.textarea_o2vj9h23  ? r_item.textarea_o2vj9h23 : "";
let grnp_text_memb1g23 = r_item.text_memb1g23  ? r_item.text_memb1g23 : "";
let grnp_iconscontrol_y1qbkp25 = r_item?.iconscontrol_y1qbkp25 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_y1qbkp25+'"></i></span>' : '';

let grnp_url_3hvh9k23_url = r_item?.url_3hvh9k23?.url && r_item?.url_3hvh9k23?.url != undefined ?  r_item?.url_3hvh9k23.url : "";
let grnp_url_3hvh9k23_target = r_item?.url_3hvh9k23?.target && r_item?.url_3hvh9k23?.target != undefined ?  r_item?.url_3hvh9k23.target : "";
let grnp_url_3hvh9k23_nofollow = r_item?.url_3hvh9k23?.nofollow && r_item?.url_3hvh9k23?.nofollow != undefined ?  r_item?.url_3hvh9k23.nofollow : "";
let grnp_url_3hvh9k23_ctmArt = r_item?.url_3hvh9k23?.attr && r_item?.url_3hvh9k23?.attr != undefined ?  r_item?.url_3hvh9k23.attr : "";
                    let grnp_url_3hvh9k23_attr = ''

                    if (grnp_url_3hvh9k23_ctmArt) {
                        let main_array = grnp_url_3hvh9k23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_3hvh9k23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_media_uy9yec23 = r_item?.media_uy9yec23?.url != undefined  ? r_item?.media_uy9yec23.url : "";
                                repeater_lcaihe23_zo25 += `<img class="tp-repeater-item-${r_item._key} bg-image" data-repeater_lcaihe23="{repeater_lcaihe23}" src="${grnp_media_uy9yec23}" data-index="${index}">`;
                            })
let repeater_lcaihe23_q325 = "";
                            
repeater_lcaihe23  && repeater_lcaihe23.map((r_item, index) => {
                                
let grnp_text_bqwp3l23 = r_item.text_bqwp3l23  ? r_item.text_bqwp3l23 : "";
let grnp_textarea_o2vj9h23 = r_item.textarea_o2vj9h23  ? r_item.textarea_o2vj9h23 : "";
let grnp_text_memb1g23 = r_item.text_memb1g23  ? r_item.text_memb1g23 : "";
let grnp_iconscontrol_y1qbkp25 = r_item?.iconscontrol_y1qbkp25 != undefined  ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_y1qbkp25+'"></i></span>' : '';

let grnp_url_3hvh9k23_url = r_item?.url_3hvh9k23?.url && r_item?.url_3hvh9k23?.url != undefined ?  r_item?.url_3hvh9k23.url : "";
let grnp_url_3hvh9k23_target = r_item?.url_3hvh9k23?.target && r_item?.url_3hvh9k23?.target != undefined ?  r_item?.url_3hvh9k23.target : "";
let grnp_url_3hvh9k23_nofollow = r_item?.url_3hvh9k23?.nofollow && r_item?.url_3hvh9k23?.nofollow != undefined ?  r_item?.url_3hvh9k23.nofollow : "";
let grnp_url_3hvh9k23_ctmArt = r_item?.url_3hvh9k23?.attr && r_item?.url_3hvh9k23?.attr != undefined ?  r_item?.url_3hvh9k23.attr : "";
                    let grnp_url_3hvh9k23_attr = ''

                    if (grnp_url_3hvh9k23_ctmArt) {
                        let main_array = grnp_url_3hvh9k23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                grnp_url_3hvh9k23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let grnp_media_uy9yec23 = r_item?.media_uy9yec23?.url != undefined  ? r_item?.media_uy9yec23.url : "";
                                repeater_lcaihe23_q325 += `<div class="tp-repeater-item-${r_item._key} con-image-item" data-repeater_lcaihe23="{repeater_lcaihe23}" data-index="${index}">
            <div class="con-item-content">
                <span class="con-i-title" data-title="${grnp_text_bqwp3l23}">${grnp_text_bqwp3l23}</span>
                <p class="con-i-desc" data-desc="${grnp_textarea_o2vj9h23}">${grnp_textarea_o2vj9h23}</p>
                <a class="con-i-btn" href="${grnp_url_3hvh9k23_url}" data-btext="${grnp_text_memb1g23}" target="${grnp_url_3hvh9k23_target}" rel="${grnp_url_3hvh9k23_nofollow} noopener">${grnp_text_memb1g23}${grnp_iconscontrol_y1qbkp25 }</a>
            </div>
        </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-2zud4k24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_2zud4k24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-cta-with-con-bg">
    <div class="cta-with-bg-inner layout-con-${g_select_j13meb23} vertical-on-mobile-${g_switcher_xuuqzy23}">
        <div class="con-image-background">
            ${repeater_lcaihe23_zo25}
                
            
        </div>
        ${repeater_lcaihe23_q325}
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
    new CTA_with_Connected_Background_2zud4k24();