
    class Text_Halftone_Effects_3s2fdi24 {
        constructor() {
            this.Text_Halftone_Effects_3s2fdi24_56xo7x25();
        }
    
        Text_Halftone_Effects_3s2fdi24_56xo7x25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Url,Pmgc_Toggle,Pmgc_Color,Pmgc_Background,Pmgc_Range,Pmgc_Repeater,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Typography,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-3s2fdi24', {
        title: __('Text Halftone Effects'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-underline tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Halftone'),__('Texture'),__('Text Layars'),__('Gradient'),__('Stroke'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_pb0vso24Function = (unit, type) => {
                var g_slider_pb0vso24_list = [];
                g_slider_pb0vso24_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 0.1 };
g_slider_pb0vso24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_pb0vso24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_pb0vso24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_pb0vso24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_pb0vso24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_pb0vso24_list[unit][type];
            };
const slider_pbqwn824Function = (unit, type) => {
                var g_slider_pbqwn824_list = [];
                g_slider_pbqwn824_list['px'] = { "type": "px", "min": -100, "max": 100, "step": 1 };
g_slider_pbqwn824_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_pbqwn824_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_pbqwn824_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_pbqwn824_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_pbqwn824_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_pbqwn824_list[unit][type];
            };
const slider_hywd2724Function = (unit, type) => {
                var g_slider_hywd2724_list = [];
                g_slider_hywd2724_list['px'] = { "type": "px", "min": -100, "max": 100, "step": 1 };
g_slider_hywd2724_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_hywd2724_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_hywd2724_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_hywd2724_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_hywd2724_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_hywd2724_list[unit][type];
            };
const slider_baqn3q23Function = (unit, type) => {
                var g_slider_baqn3q23_list = [];
                g_slider_baqn3q23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_baqn3q23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_baqn3q23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_baqn3q23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_baqn3q23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_baqn3q23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_baqn3q23_list[unit][type];
            };
const slider_67d1ly23Function = (unit, type) => {
                var g_slider_67d1ly23_list = [];
                g_slider_67d1ly23_list['px'] = { "type": "px", "min": -100, "max": 100, "step": 1 };
g_slider_67d1ly23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_67d1ly23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_67d1ly23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_67d1ly23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_67d1ly23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_67d1ly23_list[unit][type];
            };
const slider_j1hq5823Function = (unit, type) => {
                var g_slider_j1hq5823_list = [];
                g_slider_j1hq5823_list['px'] = { "type": "px", "min": -100, "max": 100, "step": 1 };
g_slider_j1hq5823_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_j1hq5823_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_j1hq5823_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_j1hq5823_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_j1hq5823_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_j1hq5823_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_q4ppee23,
url_6eq7uw24,
switcher_zfcj0m24,
repeater_5k4fcf24,
switcher_vq3o2k23,
switcher_l9n3jd24,
rawhtml_ouez9c25,
rawhtml_u5tfcc25,
choose_yehcen24,
slider_baqn3q23,
typography_5991mw23,
color_lx008a23,
color_on85zj23,
textshadow_t1vf7623,
slider_67d1ly23,
slider_j1hq5823,
background_peph5824,

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
                        main_function_3s2fdi24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_3s2fdi24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_q4ppee23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_q4ppee23: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_6eq7uw24,
                dynamic: [true, 'url_6eq7uw24'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_6eq7uw24: value }),
            }),
), React.createElement(PanelBody, { title: __("Layers"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Layers`),
            
            value: switcher_zfcj0m24,
            
            onChange: (value) => setAttributes({ switcher_zfcj0m24: value }),
            }), 
( switcher_zfcj0m24 ) && React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Select Layer`),
            value: repeater_5k4fcf24,
            attributeName: 'repeater_5k4fcf24',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_5k4fcf24: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                        ( !value.switcher_h4vi6a24 ) && React.createElement(Pmgc_Color, {
            label: __(`Fill Color`),
            value: value.color_9ly20l24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_9ly20l24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Gradient Fill Color`),
            
            value: value.switcher_h4vi6a24,
            
            onChange: v => { value.switcher_h4vi6a24 = (v == true ? 'yes' : v); onChange(value); },
            }), 
( value.switcher_h4vi6a24 ) && React.createElement(Pmgc_Background, {
            
            value: value.background_khoudh24,
            sources: ["gradient"],
            separator:'default',
            
            onChange: v => { value.background_khoudh24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: value.color_l962ur24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_l962ur24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: value.slider_pb0vso24,
            
            min: value.slider_pb0vso24 && value.slider_pb0vso24.unit ? slider_pb0vso24Function(value.slider_pb0vso24.unit, 'min') : 0,
            max: value.slider_pb0vso24 && value.slider_pb0vso24.unit ? slider_pb0vso24Function(value.slider_pb0vso24.unit, 'max') : 100,
            step: value.slider_pb0vso24 && value.slider_pb0vso24.unit ? slider_pb0vso24Function(value.slider_pb0vso24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: v => { value.slider_pb0vso24 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: value.slider_pbqwn824,
            
            min: value.slider_pbqwn824 && value.slider_pbqwn824.unit ? slider_pbqwn824Function(value.slider_pbqwn824.unit, 'min') : 0,
            max: value.slider_pbqwn824 && value.slider_pbqwn824.unit ? slider_pbqwn824Function(value.slider_pbqwn824.unit, 'max') : 100,
            step: value.slider_pbqwn824 && value.slider_pbqwn824.unit ? slider_pbqwn824Function(value.slider_pbqwn824.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: v => { value.slider_pbqwn824 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: value.slider_hywd2724,
            
            min: value.slider_hywd2724 && value.slider_hywd2724.unit ? slider_hywd2724Function(value.slider_hywd2724.unit, 'min') : 0,
            max: value.slider_hywd2724 && value.slider_hywd2724.unit ? slider_hywd2724Function(value.slider_hywd2724.unit, 'max') : 100,
            step: value.slider_hywd2724 && value.slider_hywd2724.unit ? slider_hywd2724Function(value.slider_hywd2724.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: v => { value.slider_hywd2724 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Stroke `),
            
            value: switcher_vq3o2k23,
            
            onChange: (value) => setAttributes({ switcher_vq3o2k23: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Texture`),
            
            value: switcher_l9n3jd24,
            
            onChange: (value) => setAttributes({ switcher_l9n3jd24: value }),
            }), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ouez9c25,
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
Kindly do Fill Color Transparent When Texture is On.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_ouez9c25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_u5tfcc25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/text-halftone-effects-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_u5tfcc25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right '), value: 'right', title: __('Right '), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_yehcen24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_yehcen24: value }),
            }), 
( switcher_vq3o2k23 ) && React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_baqn3q23,
            
            min: slider_baqn3q23 && slider_baqn3q23.unit ? slider_baqn3q23Function(slider_baqn3q23.unit, 'min') : 0,
            max: slider_baqn3q23 && slider_baqn3q23.unit ? slider_baqn3q23Function(slider_baqn3q23.unit, 'max') : 100,
            step: slider_baqn3q23 && slider_baqn3q23.unit ? slider_baqn3q23Function(slider_baqn3q23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_baqn3q23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_5991mw23,
            onChange: (value) => setAttributes({ typography_5991mw23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Fill Color`),
            value: color_lx008a23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_lx008a23: value }),
            }), 
( switcher_vq3o2k23 ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_on85zj23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_on85zj23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_t1vf7623,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_t1vf7623: value }),
            }), 
),( switcher_l9n3jd24 ) && React.createElement(PanelBody, { title: __("Texture"), initialOpen: false },
( switcher_l9n3jd24 ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_67d1ly23,
            
            min: slider_67d1ly23 && slider_67d1ly23.unit ? slider_67d1ly23Function(slider_67d1ly23.unit, 'min') : 0,
            max: slider_67d1ly23 && slider_67d1ly23.unit ? slider_67d1ly23Function(slider_67d1ly23.unit, 'max') : 100,
            step: slider_67d1ly23 && slider_67d1ly23.unit ? slider_67d1ly23Function(slider_67d1ly23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_67d1ly23: value }),
            }), 
( switcher_l9n3jd24 ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_j1hq5823,
            
            min: slider_j1hq5823 && slider_j1hq5823.unit ? slider_j1hq5823Function(slider_j1hq5823.unit, 'min') : 0,
            max: slider_j1hq5823 && slider_j1hq5823.unit ? slider_j1hq5823Function(slider_j1hq5823.unit, 'max') : 100,
            step: slider_j1hq5823 && slider_j1hq5823.unit ? slider_j1hq5823Function(slider_j1hq5823.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_j1hq5823: value }),
            }), 
( switcher_l9n3jd24 ) && React.createElement(Pmgc_Background, {
            
            value: background_peph5824,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_peph5824: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-3s2fdi24", block_id, false, props.clientId);
                }
            }

            
let g_text_q4ppee23 = text_q4ppee23 && text_q4ppee23 != undefined  ? text_q4ppee23 : "";
let g_url_6eq7uw24_url = url_6eq7uw24?.url && url_6eq7uw24?.url != undefined ? url_6eq7uw24.url : "";
let g_url_6eq7uw24_target = url_6eq7uw24?.target && url_6eq7uw24?.target != undefined ? url_6eq7uw24.target : "";
let g_url_6eq7uw24_nofollow = url_6eq7uw24?.nofollow && url_6eq7uw24?.nofollow != undefined ? url_6eq7uw24.nofollow : "";
let g_url_6eq7uw24_ctmArt = url_6eq7uw24?.attr != undefined ? url_6eq7uw24.attr : "";
                    let g_url_6eq7uw24_attr = ''

                    if (g_url_6eq7uw24_ctmArt) {
                        let main_array = g_url_6eq7uw24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_6eq7uw24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_zfcj0m24 = switcher_zfcj0m24 && switcher_zfcj0m24 != undefined  ? 'yes' : "";
let g_switcher_vq3o2k23 = switcher_vq3o2k23 && switcher_vq3o2k23 != undefined  ? 'yes' : "";
let g_switcher_l9n3jd24 = switcher_l9n3jd24 && switcher_l9n3jd24 != undefined  ? 'yes' : "";
let g_rawhtml_ouez9c25 = rawhtml_ouez9c25 && rawhtml_ouez9c25 != undefined  ? rawhtml_ouez9c25 : "";
let g_rawhtml_u5tfcc25 = rawhtml_u5tfcc25 && rawhtml_u5tfcc25 != undefined  ? rawhtml_u5tfcc25 : "";
let g_choose_yehcen24 = choose_yehcen24 && choose_yehcen24 != undefined  ? choose_yehcen24 : "";
let g_typography_5991mw23 = typography_5991mw23 && typography_5991mw23 != undefined  ? typography_5991mw23 : "";
let g_color_lx008a23 = color_lx008a23 && color_lx008a23 != undefined  ? color_lx008a23 : "";
let g_color_on85zj23 = color_on85zj23 && color_on85zj23 != undefined && ( switcher_vq3o2k23 ) ? color_on85zj23 : "";
let g_textshadow_t1vf7623 = textshadow_t1vf7623 && textshadow_t1vf7623 != undefined  ? textshadow_t1vf7623 : "";
let g_background_peph5824 = background_peph5824 && background_peph5824 != undefined && ( switcher_l9n3jd24 ) ? background_peph5824 : "";
            
let repeater_5k4fcf24_8725 = "";
                            
repeater_5k4fcf24 && ( switcher_zfcj0m24 ) && repeater_5k4fcf24.map((r_item, index) => {
                                
let grnp_color_9ly20l24 = r_item.color_9ly20l24 && ( !r_item?.switcher_h4vi6a24 ) ? r_item.color_9ly20l24 : "";
let grnp_switcher_h4vi6a24 = r_item.switcher_h4vi6a24  ? 'yes' : "";
let grnp_background_khoudh24 = r_item.background_khoudh24 && ( r_item?.switcher_h4vi6a24 ) ? r_item.background_khoudh24 : "";
let grnp_color_l962ur24 = r_item.color_l962ur24  ? r_item.color_l962ur24 : "";
                                repeater_5k4fcf24_8725 += `<span class="tp-repeater-item-${r_item._key} wkit-shadow-inner layer-${g_switcher_zfcj0m24} gradient-bg-${grnp_switcher_h4vi6a24}" data-repeater_5k4fcf24="{repeater_5k4fcf24}">
             ${g_text_q4ppee23}
        </span>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_3s2fdi24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-text-halftone-effects">
   <div class="wkit-text-halftone-grid">
    <a class="wkit-stroke-${g_switcher_vq3o2k23} wkit-shadow halftone halftone-color texture-${g_switcher_l9n3jd24}" href="${g_url_6eq7uw24_url}" target="${g_url_6eq7uw24_target}" rel="${g_url_6eq7uw24_nofollow} noopener" data-content="${g_text_q4ppee23}">
        <span class="wkit-halftone-first">${g_text_q4ppee23}</span>
         ${repeater_5k4fcf24_8725}
    </a>
  </div>
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
            text_q4ppee23,
url_6eq7uw24,
switcher_zfcj0m24,
repeater_5k4fcf24,
switcher_vq3o2k23,
switcher_l9n3jd24,
rawhtml_ouez9c25,
rawhtml_u5tfcc25,
choose_yehcen24,
slider_baqn3q23,
typography_5991mw23,
color_lx008a23,
color_on85zj23,
textshadow_t1vf7623,
slider_67d1ly23,
slider_j1hq5823,
background_peph5824,

            block_id,
        } = attributes;

        

        

        

        
let g_text_q4ppee23 = text_q4ppee23 && text_q4ppee23 != undefined  ? text_q4ppee23 : "";
let g_url_6eq7uw24_url = url_6eq7uw24?.url && url_6eq7uw24?.url != undefined ? url_6eq7uw24.url : "";
let g_url_6eq7uw24_target = url_6eq7uw24?.target && url_6eq7uw24?.target != undefined ? url_6eq7uw24.target : "";
let g_url_6eq7uw24_nofollow = url_6eq7uw24?.nofollow && url_6eq7uw24?.nofollow != undefined ? url_6eq7uw24.nofollow : "";
let g_url_6eq7uw24_ctmArt = url_6eq7uw24?.attr != undefined ? url_6eq7uw24.attr : "";
                    let g_url_6eq7uw24_attr = ''

                    if (g_url_6eq7uw24_ctmArt) {
                        let main_array = g_url_6eq7uw24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_6eq7uw24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_zfcj0m24 = switcher_zfcj0m24 && switcher_zfcj0m24 != undefined  ? 'yes' : "";
let g_switcher_vq3o2k23 = switcher_vq3o2k23 && switcher_vq3o2k23 != undefined  ? 'yes' : "";
let g_switcher_l9n3jd24 = switcher_l9n3jd24 && switcher_l9n3jd24 != undefined  ? 'yes' : "";
let g_rawhtml_ouez9c25 = rawhtml_ouez9c25 && rawhtml_ouez9c25 != undefined  ? rawhtml_ouez9c25 : "";
let g_rawhtml_u5tfcc25 = rawhtml_u5tfcc25 && rawhtml_u5tfcc25 != undefined  ? rawhtml_u5tfcc25 : "";
let g_choose_yehcen24 = choose_yehcen24 && choose_yehcen24 != undefined  ? choose_yehcen24 : "";
let g_typography_5991mw23 = typography_5991mw23 && typography_5991mw23 != undefined  ? typography_5991mw23 : "";
let g_color_lx008a23 = color_lx008a23 && color_lx008a23 != undefined  ? color_lx008a23 : "";
let g_color_on85zj23 = color_on85zj23 && color_on85zj23 != undefined && ( switcher_vq3o2k23 ) ? color_on85zj23 : "";
let g_textshadow_t1vf7623 = textshadow_t1vf7623 && textshadow_t1vf7623 != undefined  ? textshadow_t1vf7623 : "";
let g_background_peph5824 = background_peph5824 && background_peph5824 != undefined && ( switcher_l9n3jd24 ) ? background_peph5824 : "";
        
let repeater_5k4fcf24_8725 = "";
                            
repeater_5k4fcf24 && ( switcher_zfcj0m24 ) && repeater_5k4fcf24.map((r_item, index) => {
                                
let grnp_color_9ly20l24 = r_item.color_9ly20l24 && ( !r_item?.switcher_h4vi6a24 ) ? r_item.color_9ly20l24 : "";
let grnp_switcher_h4vi6a24 = r_item.switcher_h4vi6a24  ? 'yes' : "";
let grnp_background_khoudh24 = r_item.background_khoudh24 && ( r_item?.switcher_h4vi6a24 ) ? r_item.background_khoudh24 : "";
let grnp_color_l962ur24 = r_item.color_l962ur24  ? r_item.color_l962ur24 : "";
                                repeater_5k4fcf24_8725 += `<span class="tp-repeater-item-${r_item._key} wkit-shadow-inner layer-${g_switcher_zfcj0m24} gradient-bg-${grnp_switcher_h4vi6a24}" data-repeater_5k4fcf24="{repeater_5k4fcf24}">
             ${g_text_q4ppee23}
        </span>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-3s2fdi24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_3s2fdi24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-text-halftone-effects">
   <div class="wkit-text-halftone-grid">
    <a class="wkit-stroke-${g_switcher_vq3o2k23} wkit-shadow halftone halftone-color texture-${g_switcher_l9n3jd24}" href="${g_url_6eq7uw24_url}" target="${g_url_6eq7uw24_target}" rel="${g_url_6eq7uw24_nofollow} noopener" data-content="${g_text_q4ppee23}">
        <span class="wkit-halftone-first">${g_text_q4ppee23}</span>
         ${repeater_5k4fcf24_8725}
    </a>
  </div>
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
    new Text_Halftone_Effects_3s2fdi24();