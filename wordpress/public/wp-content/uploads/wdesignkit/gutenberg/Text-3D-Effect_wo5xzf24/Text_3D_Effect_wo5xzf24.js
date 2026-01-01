
    class Text_3D_Effect_wo5xzf24 {
        constructor() {
            this.Text_3D_Effect_wo5xzf24_zofbeo25();
        }
    
        Text_3D_Effect_wo5xzf24_zofbeo25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Toggle,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Typography,Pmgc_Color,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-wo5xzf24', {
        title: __('Text 3D Effect'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-pencil-alt tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Text'),__('3D'),__('Third Dimension'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_4zwpyc23Function = (unit, type) => {
                var g_slider_4zwpyc23_list = [];
                g_slider_4zwpyc23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_4zwpyc23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_4zwpyc23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_4zwpyc23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_4zwpyc23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_4zwpyc23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_4zwpyc23_list[unit][type];
            };
const slider_ifwopm25Function = (unit, type) => {
                var g_slider_ifwopm25_list = [];
                g_slider_ifwopm25_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ifwopm25_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_ifwopm25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ifwopm25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ifwopm25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ifwopm25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ifwopm25_list[unit][type];
            };
const slider_d3gpc223Function = (unit, type) => {
                var g_slider_d3gpc223_list = [];
                g_slider_d3gpc223_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_d3gpc223_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_d3gpc223_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_d3gpc223_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_d3gpc223_list['deg'] = { "type": "deg", "min": -360, "max": 360, "step": 1 };
g_slider_d3gpc223_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_d3gpc223_list[unit][type];
            };
const slider_kw0d5e23Function = (unit, type) => {
                var g_slider_kw0d5e23_list = [];
                g_slider_kw0d5e23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_kw0d5e23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_kw0d5e23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_kw0d5e23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_kw0d5e23_list['deg'] = { "type": "deg", "min": -360, "max": 360, "step": 1 };
g_slider_kw0d5e23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_kw0d5e23_list[unit][type];
            };
const slider_ytfzfy23Function = (unit, type) => {
                var g_slider_ytfzfy23_list = [];
                g_slider_ytfzfy23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_ytfzfy23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_ytfzfy23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ytfzfy23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ytfzfy23_list['deg'] = { "type": "deg", "min": -360, "max": 360, "step": 1 };
g_slider_ytfzfy23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ytfzfy23_list[unit][type];
            };
const slider_ey3pkf23Function = (unit, type) => {
                var g_slider_ey3pkf23_list = [];
                g_slider_ey3pkf23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_ey3pkf23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_ey3pkf23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ey3pkf23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ey3pkf23_list['deg'] = { "type": "deg", "min": -360, "max": 360, "step": 1 };
g_slider_ey3pkf23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ey3pkf23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_l92fen23,
switcher_g098ts23,
number_42n98423,
rawhtml_7ll19k25,
choose_nvemg123,
typography_l2r7c323,
color_eflma823,
textshadow_wiuxx023,
color_krvpi223,
textshadow_po7j0u23,
normalhover_ykc1n423,
slider_4zwpyc23,
slider_ifwopm25,
color_7bou7i23,
color_xzwt8b23,
color_5zpfhk23,
color_w1vo3623,
normalhover_rj4dsp23,
slider_d3gpc223,
slider_kw0d5e23,
slider_ytfzfy23,
slider_ey3pkf23,
normalhover_d5vlln23,

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
                        main_function_wo5xzf24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_wo5xzf24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getStage = $scope[0].querySelector('.wkit-3d-stage');
let getDuration = getStage.getAttribute('data-duration');
if(getDuration){
    let getLayers = getStage.querySelectorAll('.w-3d-layer');
    getLayers.forEach((el)=>{
        el.style.animationDuration = getDuration+'s';
    })
}
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Text`),
                type: "text",
                value: text_l92fen23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_l92fen23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Animation`),
            
            value: switcher_g098ts23,
            
            onChange: (value) => setAttributes({ switcher_g098ts23: value }),
            }), 
( switcher_g098ts23 ) && React.createElement(Pmgc_Text, {
                label: __(`Duration (in second)`),
                type: "number",
                value: number_42n98423,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_42n98423: value }) },
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_7ll19k25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/text-3d-effect-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_7ll19k25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Text"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_nvemg123,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_nvemg123: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_l2r7c323,
            onChange: (value) => setAttributes({ typography_l2r7c323: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_eflma823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_eflma823: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_wiuxx023,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_wiuxx023: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_krvpi223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_krvpi223: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_po7j0u23,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_po7j0u23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Shadow"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_4zwpyc23,
            
            min: slider_4zwpyc23 && slider_4zwpyc23.unit ? slider_4zwpyc23Function(slider_4zwpyc23.unit, 'min') : 0,
            max: slider_4zwpyc23 && slider_4zwpyc23.unit ? slider_4zwpyc23Function(slider_4zwpyc23.unit, 'max') : 100,
            step: slider_4zwpyc23 && slider_4zwpyc23.unit ? slider_4zwpyc23Function(slider_4zwpyc23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_4zwpyc23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_ifwopm25,
            
            min: slider_ifwopm25 && slider_ifwopm25.unit ? slider_ifwopm25Function(slider_ifwopm25.unit, 'min') : 0,
            max: slider_ifwopm25 && slider_ifwopm25.unit ? slider_ifwopm25Function(slider_ifwopm25.unit, 'max') : 100,
            step: slider_ifwopm25 && slider_ifwopm25.unit ? slider_ifwopm25Function(slider_ifwopm25.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ifwopm25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Stroke Color 1`),
            value: color_7bou7i23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7bou7i23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Stroke Color 2`),
            value: color_xzwt8b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_xzwt8b23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Stroke Color 1`),
            value: color_5zpfhk23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_5zpfhk23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Stroke Color 2`),
            value: color_w1vo3623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_w1vo3623: value }),
            }), 
), 
), 
),( !switcher_g098ts23 ) && React.createElement(PanelBody, { title: __("Rotate"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Range, {
            label: __(`X`),
            separator:'default',
            value: slider_d3gpc223,
            
            min: slider_d3gpc223 && slider_d3gpc223.unit ? slider_d3gpc223Function(slider_d3gpc223.unit, 'min') : 0,
            max: slider_d3gpc223 && slider_d3gpc223.unit ? slider_d3gpc223Function(slider_d3gpc223.unit, 'max') : 100,
            step: slider_d3gpc223 && slider_d3gpc223.unit ? slider_d3gpc223Function(slider_d3gpc223.unit, 'step') : 1,
            
                unit: ['deg', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_d3gpc223: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Y`),
            separator:'default',
            value: slider_kw0d5e23,
            
            min: slider_kw0d5e23 && slider_kw0d5e23.unit ? slider_kw0d5e23Function(slider_kw0d5e23.unit, 'min') : 0,
            max: slider_kw0d5e23 && slider_kw0d5e23.unit ? slider_kw0d5e23Function(slider_kw0d5e23.unit, 'max') : 100,
            step: slider_kw0d5e23 && slider_kw0d5e23.unit ? slider_kw0d5e23Function(slider_kw0d5e23.unit, 'step') : 1,
            
                unit: ['deg', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_kw0d5e23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Range, {
            label: __(`X`),
            separator:'default',
            value: slider_ytfzfy23,
            
            min: slider_ytfzfy23 && slider_ytfzfy23.unit ? slider_ytfzfy23Function(slider_ytfzfy23.unit, 'min') : 0,
            max: slider_ytfzfy23 && slider_ytfzfy23.unit ? slider_ytfzfy23Function(slider_ytfzfy23.unit, 'max') : 100,
            step: slider_ytfzfy23 && slider_ytfzfy23.unit ? slider_ytfzfy23Function(slider_ytfzfy23.unit, 'step') : 1,
            
                unit: ['deg', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ytfzfy23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Y`),
            separator:'default',
            value: slider_ey3pkf23,
            
            min: slider_ey3pkf23 && slider_ey3pkf23.unit ? slider_ey3pkf23Function(slider_ey3pkf23.unit, 'min') : 0,
            max: slider_ey3pkf23 && slider_ey3pkf23.unit ? slider_ey3pkf23Function(slider_ey3pkf23.unit, 'max') : 100,
            step: slider_ey3pkf23 && slider_ey3pkf23.unit ? slider_ey3pkf23Function(slider_ey3pkf23.unit, 'step') : 1,
            
                unit: ['deg', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ey3pkf23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-wo5xzf24", block_id, false, props.clientId);
                }
            }

            
let g_text_l92fen23 = text_l92fen23 && text_l92fen23 != undefined  ? text_l92fen23 : "";
let g_switcher_g098ts23 = switcher_g098ts23 && switcher_g098ts23 != undefined  ? 'yes' : "";
let g_number_42n98423 = number_42n98423 && number_42n98423 != undefined && ( switcher_g098ts23 ) ? number_42n98423 : "";
let g_rawhtml_7ll19k25 = rawhtml_7ll19k25 && rawhtml_7ll19k25 != undefined  ? rawhtml_7ll19k25 : "";
let g_choose_nvemg123 = choose_nvemg123 && choose_nvemg123 != undefined  ? choose_nvemg123 : "";
let g_typography_l2r7c323 = typography_l2r7c323 && typography_l2r7c323 != undefined  ? typography_l2r7c323 : "";
let g_color_eflma823 = color_eflma823 && color_eflma823 != undefined  ? color_eflma823 : "";
let g_textshadow_wiuxx023 = textshadow_wiuxx023 && textshadow_wiuxx023 != undefined  ? textshadow_wiuxx023 : "";
let g_color_krvpi223 = color_krvpi223 && color_krvpi223 != undefined  ? color_krvpi223 : "";
let g_textshadow_po7j0u23 = textshadow_po7j0u23 && textshadow_po7j0u23 != undefined  ? textshadow_po7j0u23 : "";
let g_color_7bou7i23 = color_7bou7i23 && color_7bou7i23 != undefined  ? color_7bou7i23 : "";
let g_color_xzwt8b23 = color_xzwt8b23 && color_xzwt8b23 != undefined  ? color_xzwt8b23 : "";
let g_color_5zpfhk23 = color_5zpfhk23 && color_5zpfhk23 != undefined  ? color_5zpfhk23 : "";
let g_color_w1vo3623 = color_w1vo3623 && color_w1vo3623 != undefined  ? color_w1vo3623 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_wo5xzf24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-text-3d-effect">
    <div class="wkit-3d-stage continuous-animation-${g_switcher_g098ts23}" data-duration="${g_number_42n98423}">
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
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
            text_l92fen23,
switcher_g098ts23,
number_42n98423,
rawhtml_7ll19k25,
choose_nvemg123,
typography_l2r7c323,
color_eflma823,
textshadow_wiuxx023,
color_krvpi223,
textshadow_po7j0u23,
normalhover_ykc1n423,
slider_4zwpyc23,
slider_ifwopm25,
color_7bou7i23,
color_xzwt8b23,
color_5zpfhk23,
color_w1vo3623,
normalhover_rj4dsp23,
slider_d3gpc223,
slider_kw0d5e23,
slider_ytfzfy23,
slider_ey3pkf23,
normalhover_d5vlln23,

            block_id,
        } = attributes;

        

        

        

        
let g_text_l92fen23 = text_l92fen23 && text_l92fen23 != undefined  ? text_l92fen23 : "";
let g_switcher_g098ts23 = switcher_g098ts23 && switcher_g098ts23 != undefined  ? 'yes' : "";
let g_number_42n98423 = number_42n98423 && number_42n98423 != undefined && ( switcher_g098ts23 ) ? number_42n98423 : "";
let g_rawhtml_7ll19k25 = rawhtml_7ll19k25 && rawhtml_7ll19k25 != undefined  ? rawhtml_7ll19k25 : "";
let g_choose_nvemg123 = choose_nvemg123 && choose_nvemg123 != undefined  ? choose_nvemg123 : "";
let g_typography_l2r7c323 = typography_l2r7c323 && typography_l2r7c323 != undefined  ? typography_l2r7c323 : "";
let g_color_eflma823 = color_eflma823 && color_eflma823 != undefined  ? color_eflma823 : "";
let g_textshadow_wiuxx023 = textshadow_wiuxx023 && textshadow_wiuxx023 != undefined  ? textshadow_wiuxx023 : "";
let g_color_krvpi223 = color_krvpi223 && color_krvpi223 != undefined  ? color_krvpi223 : "";
let g_textshadow_po7j0u23 = textshadow_po7j0u23 && textshadow_po7j0u23 != undefined  ? textshadow_po7j0u23 : "";
let g_color_7bou7i23 = color_7bou7i23 && color_7bou7i23 != undefined  ? color_7bou7i23 : "";
let g_color_xzwt8b23 = color_xzwt8b23 && color_xzwt8b23 != undefined  ? color_xzwt8b23 : "";
let g_color_5zpfhk23 = color_5zpfhk23 && color_5zpfhk23 != undefined  ? color_5zpfhk23 : "";
let g_color_w1vo3623 = color_w1vo3623 && color_w1vo3623 != undefined  ? color_w1vo3623 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-wo5xzf24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_wo5xzf24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-text-3d-effect">
    <div class="wkit-3d-stage continuous-animation-${g_switcher_g098ts23}" data-duration="${g_number_42n98423}">
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
      <div class="w-3d-layer"><span class="w-later-span">${g_text_l92fen23}</span></div>
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
    new Text_3D_Effect_wo5xzf24();