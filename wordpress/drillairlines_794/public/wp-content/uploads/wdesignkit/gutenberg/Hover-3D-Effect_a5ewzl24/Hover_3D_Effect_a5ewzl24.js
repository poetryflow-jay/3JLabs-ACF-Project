
    class Hover_3D_Effect_a5ewzl24 {
        constructor() {
            this.Hover_3D_Effect_a5ewzl24_chxu2225();
        }
    
        Hover_3D_Effect_a5ewzl24_chxu2225() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_Media,Pmgc_Url,Pmgc_Toggle,Pmgc_RadioAdvanced,Pmgc_Note,Pmgc_Typography,Pmgc_Color,Pmgc_Tabs,Pmgc_Dimension,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-a5ewzl24', {
        title: __('Hover 3D Effect'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fab fa-unity tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('3D Effect'),__('Interactive Design'),__('Three Dimensional Animation'),__('Hover Effects'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_yg9i7m23Function = (unit, type) => {
                var g_slider_yg9i7m23_list = [];
                g_slider_yg9i7m23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_yg9i7m23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_yg9i7m23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_yg9i7m23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_yg9i7m23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_yg9i7m23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_yg9i7m23_list[unit][type];
            };
const slider_259zwi23Function = (unit, type) => {
                var g_slider_259zwi23_list = [];
                g_slider_259zwi23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_259zwi23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_259zwi23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_259zwi23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_259zwi23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_259zwi23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_259zwi23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_q1dkxv23,
text_f33d9d23,
text_9zhu6023,
media_9q7zy323,
number_6c07ds23,
number_kfwyrk23,
number_s0qncc23,
number_8h56x523,
url_fyl83823,
switcher_hg44e825,
switcher_3g4yqc25,
choose_x0y5od24,
rawhtml_9ea0fs25,
typography_y0ji3t23,
color_p8bn3223,
color_tjm16p23,
normalhover_ridqm823,
typography_yab5j723,
color_3hwuec24,
color_xflbg724,
normalhover_8zyypr24,
dimension_cg8tx423,
slider_yg9i7m23,
slider_259zwi23,
color_qcn9t824,
color_im6piv23,
color_9h0e8723,

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
                        main_function_a5ewzl24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_a5ewzl24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let selectimg = $scope[0].querySelector(".wkit-img-box.style-1");
let hover3dEffect = $scope[0].querySelector(".wkit-imghover-wrapper");

if (selectimg && typeof VanillaTilt !== "undefined") {
    let max = Number(selectimg.getAttribute('data-max')) || 10,
        speed = Number(selectimg.getAttribute('data-speed')) || 10,
        perspective = Number(selectimg.getAttribute('data-perspective')) || 10,
        scale = Number(selectimg.getAttribute('data-scale')) || 1;

    VanillaTilt.init(selectimg, {
        max: max,
        speed: speed,
        perspective: perspective,
        scale: scale,
        reset: true,
    });
}

if (hover3dEffect) {
    let link = hover3dEffect.querySelector(".hover-link");
    const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
    const isMobile = window.innerWidth <= 767;

    if (link) {
        link.addEventListener('click', (e) => {
            if (
                (isMobile && !hover3dEffect.classList.contains('disable-mobile-link')) ||
                (isTablet && !hover3dEffect.classList.contains('disable-tablet-link'))
            ) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
}

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],],
                separator:"default",
                
                
                value: select_q1dkxv23,
                onChange: (value) => {setAttributes({ select_q1dkxv23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_f33d9d23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_f33d9d23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Description`),
                type: "text",
                value: text_9zhu6023,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_9zhu6023: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_9q7zy323,
                
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_9q7zy323: value }),
            }), 
),( select_q1dkxv23 == "style-1" ) && React.createElement(PanelBody, { title: __("Animation"), initialOpen: false },
( select_q1dkxv23 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`Max Tilt`),
                type: "number",
                value: number_6c07ds23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_6c07ds23: value }) },
            }),
( select_q1dkxv23 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`Speed`),
                type: "number",
                value: number_kfwyrk23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_kfwyrk23: value }) },
            }),
( select_q1dkxv23 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`Perspective`),
                type: "number",
                value: number_s0qncc23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_s0qncc23: value }) },
            }),
( select_q1dkxv23 == "style-1" ) && React.createElement(Pmgc_Text, {
                label: __(`Scale`),
                type: "number",
                value: number_8h56x523,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_8h56x523: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_fyl83823,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_fyl83823: value }),
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_hg44e825,
            
            onChange: (value) => setAttributes({ switcher_hg44e825: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_3g4yqc25,
            
            onChange: (value) => setAttributes({ switcher_3g4yqc25: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Content Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_x0y5od24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_x0y5od24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_9ea0fs25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/hover-3d-effect-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_9ea0fs25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_Typography, {
            
            value: typography_y0ji3t23,
            onChange: (value) => setAttributes({ typography_y0ji3t23: value }),
            separator:'after',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_tjm16p23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_tjm16p23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_p8bn3223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_p8bn3223: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Typography, {
            
            value: typography_yab5j723,
            onChange: (value) => setAttributes({ typography_yab5j723: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_xflbg724,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_xflbg724: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_3hwuec24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3hwuec24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Card"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_cg8tx423,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_cg8tx423: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_yg9i7m23,
            
            min: slider_yg9i7m23 && slider_yg9i7m23.unit ? slider_yg9i7m23Function(slider_yg9i7m23.unit, 'min') : 0,
            max: slider_yg9i7m23 && slider_yg9i7m23.unit ? slider_yg9i7m23Function(slider_yg9i7m23.unit, 'max') : 100,
            step: slider_yg9i7m23 && slider_yg9i7m23.unit ? slider_yg9i7m23Function(slider_yg9i7m23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_yg9i7m23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_259zwi23,
            
            min: slider_259zwi23 && slider_259zwi23.unit ? slider_259zwi23Function(slider_259zwi23.unit, 'min') : 0,
            max: slider_259zwi23 && slider_259zwi23.unit ? slider_259zwi23Function(slider_259zwi23.unit, 'max') : 100,
            step: slider_259zwi23 && slider_259zwi23.unit ? slider_259zwi23Function(slider_259zwi23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_259zwi23: value }),
            }), 
( select_q1dkxv23 == "style-2" ) && React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_qcn9t824,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qcn9t824: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Flash Color 1`),
            value: color_im6piv23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_im6piv23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Flash Color 2`),
            value: color_9h0e8723,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9h0e8723: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-a5ewzl24", block_id, false, props.clientId);
                }
            }

            
let g_select_q1dkxv23 = select_q1dkxv23 && select_q1dkxv23 != undefined  ? select_q1dkxv23 : "";
let g_text_f33d9d23 = text_f33d9d23 && text_f33d9d23 != undefined  ? text_f33d9d23 : "";
let g_text_9zhu6023 = text_9zhu6023 && text_9zhu6023 != undefined  ? text_9zhu6023 : "";
let g_media_9q7zy323 = media_9q7zy323 && media_9q7zy323.url && media_9q7zy323.url != undefined  ? media_9q7zy323.url : "";
let g_number_6c07ds23 = number_6c07ds23 && number_6c07ds23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_6c07ds23 : "";
let g_number_kfwyrk23 = number_kfwyrk23 && number_kfwyrk23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_kfwyrk23 : "";
let g_number_s0qncc23 = number_s0qncc23 && number_s0qncc23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_s0qncc23 : "";
let g_number_8h56x523 = number_8h56x523 && number_8h56x523 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_8h56x523 : "";
let g_url_fyl83823_url = url_fyl83823?.url && url_fyl83823?.url != undefined ? url_fyl83823.url : "";
let g_url_fyl83823_target = url_fyl83823?.target && url_fyl83823?.target != undefined ? url_fyl83823.target : "";
let g_url_fyl83823_nofollow = url_fyl83823?.nofollow && url_fyl83823?.nofollow != undefined ? url_fyl83823.nofollow : "";
let g_url_fyl83823_ctmArt = url_fyl83823?.attr != undefined ? url_fyl83823.attr : "";
                    let g_url_fyl83823_attr = ''

                    if (g_url_fyl83823_ctmArt) {
                        let main_array = g_url_fyl83823_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_fyl83823_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_hg44e825 = switcher_hg44e825 && switcher_hg44e825 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_3g4yqc25 = switcher_3g4yqc25 && switcher_3g4yqc25 != undefined  ? 'disable-mobile-link' : "";
let g_choose_x0y5od24 = choose_x0y5od24 && choose_x0y5od24 != undefined  ? choose_x0y5od24 : "";
let g_rawhtml_9ea0fs25 = rawhtml_9ea0fs25 && rawhtml_9ea0fs25 != undefined  ? rawhtml_9ea0fs25 : "";
let g_typography_y0ji3t23 = typography_y0ji3t23 && typography_y0ji3t23 != undefined  ? typography_y0ji3t23 : "";
let g_color_p8bn3223 = color_p8bn3223 && color_p8bn3223 != undefined  ? color_p8bn3223 : "";
let g_color_tjm16p23 = color_tjm16p23 && color_tjm16p23 != undefined  ? color_tjm16p23 : "";
let g_typography_yab5j723 = typography_yab5j723 && typography_yab5j723 != undefined  ? typography_yab5j723 : "";
let g_color_3hwuec24 = color_3hwuec24 && color_3hwuec24 != undefined  ? color_3hwuec24 : "";
let g_color_xflbg724 = color_xflbg724 && color_xflbg724 != undefined  ? color_xflbg724 : "";
let g_dimension_cg8tx423 = dimension_cg8tx423 && dimension_cg8tx423 != undefined  ? dimension_cg8tx423 : "";
let g_color_qcn9t824 = color_qcn9t824 && color_qcn9t824 != undefined && ( (select_q1dkxv23 == "style-2") ) ? color_qcn9t824 : "";
let g_color_im6piv23 = color_im6piv23 && color_im6piv23 != undefined  ? color_im6piv23 : "";
let g_color_9h0e8723 = color_9h0e8723 && color_9h0e8723 != undefined  ? color_9h0e8723 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_a5ewzl24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-imghover-wrapper ${g_switcher_hg44e825} ${g_switcher_3g4yqc25}">
    <a href="${g_url_fyl83823_url}" target="${g_url_fyl83823_target}" rel="${g_url_fyl83823_nofollow} noopener" class="hover-link">
    <div class="wkit-img-box ${g_select_q1dkxv23}" data-max="${g_number_6c07ds23}" data-speed="${g_number_kfwyrk23}" data-perspective="${g_number_s0qncc23}" data-scale="${g_number_8h56x523}">
        <div class="wkit-img-glitch" style="--img:url(${g_media_9q7zy323})">
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
        </div> 
        <div class="wkit-img-cover">
            <div class="wkit-bg-img" style="background-image: url(${g_media_9q7zy323});"></div>
            <div class="wkit-img-content">
                <div class="wkit-img-title" data-title="${g_text_f33d9d23}">${g_text_f33d9d23}</div>
                <div class="wkit-img-label" data-label="${g_text_9zhu6023}">${g_text_9zhu6023}</div>
            </div>
         </div>
    </div>
    </a>
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
            select_q1dkxv23,
text_f33d9d23,
text_9zhu6023,
media_9q7zy323,
number_6c07ds23,
number_kfwyrk23,
number_s0qncc23,
number_8h56x523,
url_fyl83823,
switcher_hg44e825,
switcher_3g4yqc25,
choose_x0y5od24,
rawhtml_9ea0fs25,
typography_y0ji3t23,
color_p8bn3223,
color_tjm16p23,
normalhover_ridqm823,
typography_yab5j723,
color_3hwuec24,
color_xflbg724,
normalhover_8zyypr24,
dimension_cg8tx423,
slider_yg9i7m23,
slider_259zwi23,
color_qcn9t824,
color_im6piv23,
color_9h0e8723,

            block_id,
        } = attributes;

        

        

        

        
let g_select_q1dkxv23 = select_q1dkxv23 && select_q1dkxv23 != undefined  ? select_q1dkxv23 : "";
let g_text_f33d9d23 = text_f33d9d23 && text_f33d9d23 != undefined  ? text_f33d9d23 : "";
let g_text_9zhu6023 = text_9zhu6023 && text_9zhu6023 != undefined  ? text_9zhu6023 : "";
let g_media_9q7zy323 = media_9q7zy323 && media_9q7zy323.url && media_9q7zy323.url != undefined  ? media_9q7zy323.url : "";
let g_number_6c07ds23 = number_6c07ds23 && number_6c07ds23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_6c07ds23 : "";
let g_number_kfwyrk23 = number_kfwyrk23 && number_kfwyrk23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_kfwyrk23 : "";
let g_number_s0qncc23 = number_s0qncc23 && number_s0qncc23 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_s0qncc23 : "";
let g_number_8h56x523 = number_8h56x523 && number_8h56x523 != undefined && ( (select_q1dkxv23 == "style-1") ) ? number_8h56x523 : "";
let g_url_fyl83823_url = url_fyl83823?.url && url_fyl83823?.url != undefined ? url_fyl83823.url : "";
let g_url_fyl83823_target = url_fyl83823?.target && url_fyl83823?.target != undefined ? url_fyl83823.target : "";
let g_url_fyl83823_nofollow = url_fyl83823?.nofollow && url_fyl83823?.nofollow != undefined ? url_fyl83823.nofollow : "";
let g_url_fyl83823_ctmArt = url_fyl83823?.attr != undefined ? url_fyl83823.attr : "";
                    let g_url_fyl83823_attr = ''

                    if (g_url_fyl83823_ctmArt) {
                        let main_array = g_url_fyl83823_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_fyl83823_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_hg44e825 = switcher_hg44e825 && switcher_hg44e825 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_3g4yqc25 = switcher_3g4yqc25 && switcher_3g4yqc25 != undefined  ? 'disable-mobile-link' : "";
let g_choose_x0y5od24 = choose_x0y5od24 && choose_x0y5od24 != undefined  ? choose_x0y5od24 : "";
let g_rawhtml_9ea0fs25 = rawhtml_9ea0fs25 && rawhtml_9ea0fs25 != undefined  ? rawhtml_9ea0fs25 : "";
let g_typography_y0ji3t23 = typography_y0ji3t23 && typography_y0ji3t23 != undefined  ? typography_y0ji3t23 : "";
let g_color_p8bn3223 = color_p8bn3223 && color_p8bn3223 != undefined  ? color_p8bn3223 : "";
let g_color_tjm16p23 = color_tjm16p23 && color_tjm16p23 != undefined  ? color_tjm16p23 : "";
let g_typography_yab5j723 = typography_yab5j723 && typography_yab5j723 != undefined  ? typography_yab5j723 : "";
let g_color_3hwuec24 = color_3hwuec24 && color_3hwuec24 != undefined  ? color_3hwuec24 : "";
let g_color_xflbg724 = color_xflbg724 && color_xflbg724 != undefined  ? color_xflbg724 : "";
let g_dimension_cg8tx423 = dimension_cg8tx423 && dimension_cg8tx423 != undefined  ? dimension_cg8tx423 : "";
let g_color_qcn9t824 = color_qcn9t824 && color_qcn9t824 != undefined && ( (select_q1dkxv23 == "style-2") ) ? color_qcn9t824 : "";
let g_color_im6piv23 = color_im6piv23 && color_im6piv23 != undefined  ? color_im6piv23 : "";
let g_color_9h0e8723 = color_9h0e8723 && color_9h0e8723 != undefined  ? color_9h0e8723 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-a5ewzl24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_a5ewzl24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-imghover-wrapper ${g_switcher_hg44e825} ${g_switcher_3g4yqc25}">
    <a href="${g_url_fyl83823_url}" target="${g_url_fyl83823_target}" rel="${g_url_fyl83823_nofollow} noopener" class="hover-link">
    <div class="wkit-img-box ${g_select_q1dkxv23}" data-max="${g_number_6c07ds23}" data-speed="${g_number_kfwyrk23}" data-perspective="${g_number_s0qncc23}" data-scale="${g_number_8h56x523}">
        <div class="wkit-img-glitch" style="--img:url(${g_media_9q7zy323})">
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
            <div class="wkit-glitch"></div>
        </div> 
        <div class="wkit-img-cover">
            <div class="wkit-bg-img" style="background-image: url(${g_media_9q7zy323});"></div>
            <div class="wkit-img-content">
                <div class="wkit-img-title" data-title="${g_text_f33d9d23}">${g_text_f33d9d23}</div>
                <div class="wkit-img-label" data-label="${g_text_9zhu6023}">${g_text_9zhu6023}</div>
            </div>
         </div>
    </div>
    </a>
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
    new Hover_3D_Effect_a5ewzl24();