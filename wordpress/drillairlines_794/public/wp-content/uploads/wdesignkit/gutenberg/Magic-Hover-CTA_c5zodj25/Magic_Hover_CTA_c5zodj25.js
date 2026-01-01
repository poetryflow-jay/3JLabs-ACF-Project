
    class Magic_Hover_CTA_c5zodj25 {
        constructor() {
            this.Magic_Hover_CTA_c5zodj25_53em4325();
        }
    
        Magic_Hover_CTA_c5zodj25_53em4325() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_TextArea,Pmgc_IconList,Pmgc_Url,Pmgc_Color,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Dimension,Pmgc_Typography,Pmgc_Range,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-c5zodj25', {
        title: __('Magic Hover CTA'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-hand-sparkles tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Magic Hover CTA'),__('Hover Effects'),__('Call to Action'),__('Interactive Design'),__('Button Animation'),__('User Engagement'),__('Promotional Tools'),__('Gutenberg Block'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_hn9ctu24Function = (unit, type) => {
                var g_slider_hn9ctu24_list = [];
                g_slider_hn9ctu24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_hn9ctu24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_hn9ctu24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_hn9ctu24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_hn9ctu24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_hn9ctu24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_hn9ctu24_list[unit][type];
            };
const slider_p6x6aa24Function = (unit, type) => {
                var g_slider_p6x6aa24_list = [];
                g_slider_p6x6aa24_list['px'] = { "type": "px", "min": 0, "max": 1140, "step": 1 };
g_slider_p6x6aa24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_p6x6aa24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_p6x6aa24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_p6x6aa24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_p6x6aa24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_p6x6aa24_list[unit][type];
            };
const slider_5v5gcc24Function = (unit, type) => {
                var g_slider_5v5gcc24_list = [];
                g_slider_5v5gcc24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_5v5gcc24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_5v5gcc24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5v5gcc24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5v5gcc24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5v5gcc24_list['vh'] = { "type": "vh", "min": 0, "max": 100, "step": 1 };

                return g_slider_5v5gcc24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               title_22o96g24,
descarea_va04vf24,
iconscontrol_5ogz2q24,
btntext_af3w9224,
url_04b0z724,
color_vmdio024,
color_fw1sjo24,
color_fyu6d324,
number_7ulqur25,
number_m6kdhq25,
rawhtml_lby1jh25,
rawhtml_5ju7i325,
choose_4mgz8d24,
dimension_9srg4y24,
typography_5jo5q324,
color_osiroq24,
dimension_c5q6ka24,
typography_n22svy24,
color_bel38a24,
slider_hn9ctu24,
color_sxinvm24,
dimension_t0yzyz24,
typography_556az724,
color_vg24xc24,
background_o53zgn24,
border_gsoax424,
dimension_5ngaw524,
boxshadow_ifjk3b24,
color_9gk35224,
background_c2v67s24,
border_0kvzn924,
boxshadow_cz5s9224,
normalhover_lfr6y424,
dimension_gxtvn624,
slider_p6x6aa24,
slider_5v5gcc24,
background_pb4d4824,
border_24b8i624,
dimension_4lwgcl24,
boxshadow_2tmaij24,
border_z0pxba24,
boxshadow_f37dfv24,
normalhover_zb2x4h24,

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
                        main_function_c5zodj25(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_c5zodj25 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let container = $scope[0].querySelector('.wkit-magic-hover-cta');

const fillEffect = container.querySelector('.wkit-mhcta-fill-effect');
const title = container.querySelector('.w-magic-title');
const desc = container.querySelector('.w-magic-desc');
const btn = container.querySelector('.w-magic-btn');
const iconContainer = container.querySelector('.w-magic-icon');
const icon = iconContainer.querySelector('svg') || iconContainer.querySelector('i');

// Colors
let color = fillEffect.getAttribute('data-color') || '#836fff';
let strokeColor = title.getAttribute('data-stroke') || '#836fff';
let iconStrokeColor = iconContainer.getAttribute('data-icon-stroke') || '#836fff';

let minOpacity = parseFloat(container.getAttribute('data-minopa')) || 0;
let maxOpacity = parseFloat(container.getAttribute('data-maxopa')) || 1;

const maxTitleStrokeWidth = 2;
const maxIconStrokeWidth = 15;
const minRadius = 0;
const maxRadius = 50;

container.addEventListener('mousemove', function (e) {
  const rect = container.getBoundingClientRect();
  const mouseX = e.clientX - rect.left;
  const mouseY = e.clientY - rect.top;
  const containerWidth = container.offsetWidth;
  const containerHeight = container.offsetHeight;
  const centerX = containerWidth / 2;
  const centerY = containerHeight / 2;

  const distance = Math.sqrt(Math.pow(mouseX - centerX, 2) + Math.pow(mouseY - centerY, 2));
  const maxDistance = Math.sqrt(Math.pow(containerWidth / 2, 2) + Math.pow(containerHeight / 2, 2));
  const normalizedDistance = Math.min(1, distance / maxDistance);

  const radius = maxRadius - normalizedDistance * (maxRadius - minRadius);
  const titleStrokeWidth = maxTitleStrokeWidth * (1 - normalizedDistance);
  const iconStrokeWidth = maxIconStrokeWidth * (1 - normalizedDistance);

  fillEffect.style.background = `radial-gradient(circle at ${mouseX}px ${mouseY}px, ${color} ${radius}px, transparent)`;
  const currentOpacity = minOpacity + (maxOpacity - minOpacity) * normalizedDistance;

  title.style.color = `rgba(255, 255, 255, ${currentOpacity})`;
  title.style.webkitTextStroke = `${titleStrokeWidth}px ${strokeColor}`;
  desc.style.opacity = currentOpacity;

  if (icon.tagName.toLowerCase() === 'svg') {
    icon.style.stroke = hexToRgba(iconStrokeColor, 1 - normalizedDistance);
    icon.style.strokeWidth = '1px';
  } else if (icon.tagName.toLowerCase() === 'i') {
    icon.style.webkitTextStroke = `${iconStrokeWidth / 5}px ${iconStrokeColor}`;
  }
});

container.addEventListener('mouseleave', function () {
  fillEffect.style.background = 'transparent';
  title.style.color = '';
  title.style.webkitTextStroke = '0px transparent';
  desc.style.opacity = maxOpacity;

  if (icon.tagName.toLowerCase() === 'svg') {
    icon.style.stroke = iconStrokeColor;
    icon.style.strokeWidth = '1px';
  } else if (icon.tagName.toLowerCase() === 'i') {
    icon.style.webkitTextStroke = '0px transparent';
    icon.style.color = '';
  }
});

function hexToRgba(hex, opacity) {
  const r = parseInt(hex.slice(1, 3), 16);
  const g = parseInt(hex.slice(3, 5), 16);
  const b = parseInt(hex.slice(5, 7), 16);
  return `rgba(${r}, ${g}, ${b}, ${opacity})`;
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
                value: title_22o96g24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ title_22o96g24: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                
                
                rows:"2",
                value: descarea_va04vf24,
                dynamic: true,
                onChange: (value) => setAttributes({ descarea_va04vf24: value }),
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_5ogz2q24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_5ogz2q24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: btntext_af3w9224,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ btntext_af3w9224: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_04b0z724,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_04b0z724: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Color, {
            label: __(`Mouse Hover Color`),
            value: color_vmdio024,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vmdio024: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Title Stroke Color`),
            value: color_fw1sjo24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_fw1sjo24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Stroke Color`),
            value: color_fyu6d324,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_fyu6d324: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Minimum Opacity`),
                type: "number",
                value: number_7ulqur25,
                
                
                
                separator:"before",
                
                onChange: (value) => {setAttributes({ number_7ulqur25: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Maximum Opacity`),
                type: "number",
                value: number_m6kdhq25,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_m6kdhq25: value }) },
            }),
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_lby1jh25,
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
For hover effect use opacity options to manage it's opacity.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_lby1jh25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_5ju7i325,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/magic-hover-cta-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_5ju7i325: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_4mgz8d24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_4mgz8d24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Title"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_9srg4y24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_9srg4y24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_5jo5q324,
            onChange: (value) => setAttributes({ typography_5jo5q324: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_osiroq24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_osiroq24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_c5q6ka24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_c5q6ka24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_n22svy24,
            onChange: (value) => setAttributes({ typography_n22svy24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_bel38a24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_bel38a24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_hn9ctu24,
            
            min: slider_hn9ctu24 && slider_hn9ctu24.unit ? slider_hn9ctu24Function(slider_hn9ctu24.unit, 'min') : 0,
            max: slider_hn9ctu24 && slider_hn9ctu24.unit ? slider_hn9ctu24Function(slider_hn9ctu24.unit, 'max') : 100,
            step: slider_hn9ctu24 && slider_hn9ctu24.unit ? slider_hn9ctu24Function(slider_hn9ctu24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_hn9ctu24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_sxinvm24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_sxinvm24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_t0yzyz24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_t0yzyz24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_556az724,
            onChange: (value) => setAttributes({ typography_556az724: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_vg24xc24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vg24xc24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_o53zgn24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_o53zgn24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_gsoax424,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_gsoax424: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_5ngaw524,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5ngaw524: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_ifjk3b24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_ifjk3b24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_9gk35224,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9gk35224: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_c2v67s24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_c2v67s24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_0kvzn924,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_0kvzn924: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cz5s9224,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cz5s9224: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_gxtvn624,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_gxtvn624: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_p6x6aa24,
            
            min: slider_p6x6aa24 && slider_p6x6aa24.unit ? slider_p6x6aa24Function(slider_p6x6aa24.unit, 'min') : 0,
            max: slider_p6x6aa24 && slider_p6x6aa24.unit ? slider_p6x6aa24Function(slider_p6x6aa24.unit, 'max') : 100,
            step: slider_p6x6aa24 && slider_p6x6aa24.unit ? slider_p6x6aa24Function(slider_p6x6aa24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_p6x6aa24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_5v5gcc24,
            
            min: slider_5v5gcc24 && slider_5v5gcc24.unit ? slider_5v5gcc24Function(slider_5v5gcc24.unit, 'min') : 0,
            max: slider_5v5gcc24 && slider_5v5gcc24.unit ? slider_5v5gcc24Function(slider_5v5gcc24.unit, 'max') : 100,
            step: slider_5v5gcc24 && slider_5v5gcc24.unit ? slider_5v5gcc24Function(slider_5v5gcc24.unit, 'step') : 1,
            
                unit: ['px', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5v5gcc24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_pb4d4824,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_pb4d4824: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_24b8i624,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_24b8i624: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_4lwgcl24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_4lwgcl24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_2tmaij24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_2tmaij24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_z0pxba24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_z0pxba24: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_f37dfv24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_f37dfv24: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-c5zodj25", block_id, false, props.clientId);
                }
            }

            
let g_title_22o96g24 = title_22o96g24 && title_22o96g24 != undefined  ? title_22o96g24 : "";
let g_descarea_va04vf24 = descarea_va04vf24 && descarea_va04vf24 != undefined  ? descarea_va04vf24 : "";
let g_iconscontrol_5ogz2q24 = iconscontrol_5ogz2q24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_5ogz2q24+'"></i></span>' : '';

let g_btntext_af3w9224 = btntext_af3w9224 && btntext_af3w9224 != undefined  ? btntext_af3w9224 : "";
let g_url_04b0z724_url = url_04b0z724?.url && url_04b0z724?.url != undefined ? url_04b0z724.url : "";
let g_url_04b0z724_target = url_04b0z724?.target && url_04b0z724?.target != undefined ? url_04b0z724.target : "";
let g_url_04b0z724_nofollow = url_04b0z724?.nofollow && url_04b0z724?.nofollow != undefined ? url_04b0z724.nofollow : "";
let g_url_04b0z724_ctmArt = url_04b0z724?.attr != undefined ? url_04b0z724.attr : "";
                    let g_url_04b0z724_attr = ''

                    if (g_url_04b0z724_ctmArt) {
                        let main_array = g_url_04b0z724_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_04b0z724_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_color_vmdio024 = color_vmdio024 && color_vmdio024 != undefined  ? color_vmdio024 : "";
let g_color_fw1sjo24 = color_fw1sjo24 && color_fw1sjo24 != undefined  ? color_fw1sjo24 : "";
let g_color_fyu6d324 = color_fyu6d324 && color_fyu6d324 != undefined  ? color_fyu6d324 : "";
let g_number_7ulqur25 = number_7ulqur25 && number_7ulqur25 != undefined  ? number_7ulqur25 : "";
let g_number_m6kdhq25 = number_m6kdhq25 && number_m6kdhq25 != undefined  ? number_m6kdhq25 : "";
let g_rawhtml_lby1jh25 = rawhtml_lby1jh25 && rawhtml_lby1jh25 != undefined  ? rawhtml_lby1jh25 : "";
let g_rawhtml_5ju7i325 = rawhtml_5ju7i325 && rawhtml_5ju7i325 != undefined  ? rawhtml_5ju7i325 : "";
let g_choose_4mgz8d24 = choose_4mgz8d24 && choose_4mgz8d24 != undefined  ? choose_4mgz8d24 : "";
let g_dimension_9srg4y24 = dimension_9srg4y24 && dimension_9srg4y24 != undefined  ? dimension_9srg4y24 : "";
let g_typography_5jo5q324 = typography_5jo5q324 && typography_5jo5q324 != undefined  ? typography_5jo5q324 : "";
let g_color_osiroq24 = color_osiroq24 && color_osiroq24 != undefined  ? color_osiroq24 : "";
let g_dimension_c5q6ka24 = dimension_c5q6ka24 && dimension_c5q6ka24 != undefined  ? dimension_c5q6ka24 : "";
let g_typography_n22svy24 = typography_n22svy24 && typography_n22svy24 != undefined  ? typography_n22svy24 : "";
let g_color_bel38a24 = color_bel38a24 && color_bel38a24 != undefined  ? color_bel38a24 : "";
let g_color_sxinvm24 = color_sxinvm24 && color_sxinvm24 != undefined  ? color_sxinvm24 : "";
let g_dimension_t0yzyz24 = dimension_t0yzyz24 && dimension_t0yzyz24 != undefined  ? dimension_t0yzyz24 : "";
let g_typography_556az724 = typography_556az724 && typography_556az724 != undefined  ? typography_556az724 : "";
let g_color_vg24xc24 = color_vg24xc24 && color_vg24xc24 != undefined  ? color_vg24xc24 : "";
let g_background_o53zgn24 = background_o53zgn24 && background_o53zgn24 != undefined  ? background_o53zgn24 : "";
let g_border_gsoax424 = border_gsoax424 && border_gsoax424 != undefined  ? border_gsoax424 : "";
let g_dimension_5ngaw524 = dimension_5ngaw524 && dimension_5ngaw524 != undefined  ? dimension_5ngaw524 : "";
let g_boxshadow_ifjk3b24 = boxshadow_ifjk3b24 && boxshadow_ifjk3b24 != undefined  ? boxshadow_ifjk3b24 : "";
let g_color_9gk35224 = color_9gk35224 && color_9gk35224 != undefined  ? color_9gk35224 : "";
let g_background_c2v67s24 = background_c2v67s24 && background_c2v67s24 != undefined  ? background_c2v67s24 : "";
let g_border_0kvzn924 = border_0kvzn924 && border_0kvzn924 != undefined  ? border_0kvzn924 : "";
let g_boxshadow_cz5s9224 = boxshadow_cz5s9224 && boxshadow_cz5s9224 != undefined  ? boxshadow_cz5s9224 : "";
let g_dimension_gxtvn624 = dimension_gxtvn624 && dimension_gxtvn624 != undefined  ? dimension_gxtvn624 : "";
let g_background_pb4d4824 = background_pb4d4824 && background_pb4d4824 != undefined  ? background_pb4d4824 : "";
let g_border_24b8i624 = border_24b8i624 && border_24b8i624 != undefined  ? border_24b8i624 : "";
let g_dimension_4lwgcl24 = dimension_4lwgcl24 && dimension_4lwgcl24 != undefined  ? dimension_4lwgcl24 : "";
let g_boxshadow_2tmaij24 = boxshadow_2tmaij24 && boxshadow_2tmaij24 != undefined  ? boxshadow_2tmaij24 : "";
let g_border_z0pxba24 = border_z0pxba24 && border_z0pxba24 != undefined  ? border_z0pxba24 : "";
let g_boxshadow_f37dfv24 = boxshadow_f37dfv24 && boxshadow_f37dfv24 != undefined  ? boxshadow_f37dfv24 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_c5zodj25 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-magic-hover-cta" data-minopa="${g_number_7ulqur25}" data-maxopa="${g_number_m6kdhq25}">
    <div class="w-magic-hover-container">
        <div class="w-magic-icon" data-icon-stroke="${g_color_fyu6d324}">
            ${g_iconscontrol_5ogz2q24}
        </div>
        <h1 class="w-magic-title" data-stroke="${g_color_fw1sjo24}">${g_title_22o96g24}</h1>
        <p class="w-magic-desc">${g_descarea_va04vf24}</p>
        <a class="w-magic-btn" href="${g_url_04b0z724_url}" target="${g_url_04b0z724_target}" rel="${g_url_04b0z724_nofollow} noopener">${g_btntext_af3w9224}</a>
        <div class="wkit-mhcta-fill-effect" data-color="${g_color_vmdio024}"></div>
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
            title_22o96g24,
descarea_va04vf24,
iconscontrol_5ogz2q24,
btntext_af3w9224,
url_04b0z724,
color_vmdio024,
color_fw1sjo24,
color_fyu6d324,
number_7ulqur25,
number_m6kdhq25,
rawhtml_lby1jh25,
rawhtml_5ju7i325,
choose_4mgz8d24,
dimension_9srg4y24,
typography_5jo5q324,
color_osiroq24,
dimension_c5q6ka24,
typography_n22svy24,
color_bel38a24,
slider_hn9ctu24,
color_sxinvm24,
dimension_t0yzyz24,
typography_556az724,
color_vg24xc24,
background_o53zgn24,
border_gsoax424,
dimension_5ngaw524,
boxshadow_ifjk3b24,
color_9gk35224,
background_c2v67s24,
border_0kvzn924,
boxshadow_cz5s9224,
normalhover_lfr6y424,
dimension_gxtvn624,
slider_p6x6aa24,
slider_5v5gcc24,
background_pb4d4824,
border_24b8i624,
dimension_4lwgcl24,
boxshadow_2tmaij24,
border_z0pxba24,
boxshadow_f37dfv24,
normalhover_zb2x4h24,

            block_id,
        } = attributes;

        

        

        

        
let g_title_22o96g24 = title_22o96g24 && title_22o96g24 != undefined  ? title_22o96g24 : "";
let g_descarea_va04vf24 = descarea_va04vf24 && descarea_va04vf24 != undefined  ? descarea_va04vf24 : "";
let g_iconscontrol_5ogz2q24 = iconscontrol_5ogz2q24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_5ogz2q24+'"></i></span>' : '';

let g_btntext_af3w9224 = btntext_af3w9224 && btntext_af3w9224 != undefined  ? btntext_af3w9224 : "";
let g_url_04b0z724_url = url_04b0z724?.url && url_04b0z724?.url != undefined ? url_04b0z724.url : "";
let g_url_04b0z724_target = url_04b0z724?.target && url_04b0z724?.target != undefined ? url_04b0z724.target : "";
let g_url_04b0z724_nofollow = url_04b0z724?.nofollow && url_04b0z724?.nofollow != undefined ? url_04b0z724.nofollow : "";
let g_url_04b0z724_ctmArt = url_04b0z724?.attr != undefined ? url_04b0z724.attr : "";
                    let g_url_04b0z724_attr = ''

                    if (g_url_04b0z724_ctmArt) {
                        let main_array = g_url_04b0z724_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_04b0z724_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_color_vmdio024 = color_vmdio024 && color_vmdio024 != undefined  ? color_vmdio024 : "";
let g_color_fw1sjo24 = color_fw1sjo24 && color_fw1sjo24 != undefined  ? color_fw1sjo24 : "";
let g_color_fyu6d324 = color_fyu6d324 && color_fyu6d324 != undefined  ? color_fyu6d324 : "";
let g_number_7ulqur25 = number_7ulqur25 && number_7ulqur25 != undefined  ? number_7ulqur25 : "";
let g_number_m6kdhq25 = number_m6kdhq25 && number_m6kdhq25 != undefined  ? number_m6kdhq25 : "";
let g_rawhtml_lby1jh25 = rawhtml_lby1jh25 && rawhtml_lby1jh25 != undefined  ? rawhtml_lby1jh25 : "";
let g_rawhtml_5ju7i325 = rawhtml_5ju7i325 && rawhtml_5ju7i325 != undefined  ? rawhtml_5ju7i325 : "";
let g_choose_4mgz8d24 = choose_4mgz8d24 && choose_4mgz8d24 != undefined  ? choose_4mgz8d24 : "";
let g_dimension_9srg4y24 = dimension_9srg4y24 && dimension_9srg4y24 != undefined  ? dimension_9srg4y24 : "";
let g_typography_5jo5q324 = typography_5jo5q324 && typography_5jo5q324 != undefined  ? typography_5jo5q324 : "";
let g_color_osiroq24 = color_osiroq24 && color_osiroq24 != undefined  ? color_osiroq24 : "";
let g_dimension_c5q6ka24 = dimension_c5q6ka24 && dimension_c5q6ka24 != undefined  ? dimension_c5q6ka24 : "";
let g_typography_n22svy24 = typography_n22svy24 && typography_n22svy24 != undefined  ? typography_n22svy24 : "";
let g_color_bel38a24 = color_bel38a24 && color_bel38a24 != undefined  ? color_bel38a24 : "";
let g_color_sxinvm24 = color_sxinvm24 && color_sxinvm24 != undefined  ? color_sxinvm24 : "";
let g_dimension_t0yzyz24 = dimension_t0yzyz24 && dimension_t0yzyz24 != undefined  ? dimension_t0yzyz24 : "";
let g_typography_556az724 = typography_556az724 && typography_556az724 != undefined  ? typography_556az724 : "";
let g_color_vg24xc24 = color_vg24xc24 && color_vg24xc24 != undefined  ? color_vg24xc24 : "";
let g_background_o53zgn24 = background_o53zgn24 && background_o53zgn24 != undefined  ? background_o53zgn24 : "";
let g_border_gsoax424 = border_gsoax424 && border_gsoax424 != undefined  ? border_gsoax424 : "";
let g_dimension_5ngaw524 = dimension_5ngaw524 && dimension_5ngaw524 != undefined  ? dimension_5ngaw524 : "";
let g_boxshadow_ifjk3b24 = boxshadow_ifjk3b24 && boxshadow_ifjk3b24 != undefined  ? boxshadow_ifjk3b24 : "";
let g_color_9gk35224 = color_9gk35224 && color_9gk35224 != undefined  ? color_9gk35224 : "";
let g_background_c2v67s24 = background_c2v67s24 && background_c2v67s24 != undefined  ? background_c2v67s24 : "";
let g_border_0kvzn924 = border_0kvzn924 && border_0kvzn924 != undefined  ? border_0kvzn924 : "";
let g_boxshadow_cz5s9224 = boxshadow_cz5s9224 && boxshadow_cz5s9224 != undefined  ? boxshadow_cz5s9224 : "";
let g_dimension_gxtvn624 = dimension_gxtvn624 && dimension_gxtvn624 != undefined  ? dimension_gxtvn624 : "";
let g_background_pb4d4824 = background_pb4d4824 && background_pb4d4824 != undefined  ? background_pb4d4824 : "";
let g_border_24b8i624 = border_24b8i624 && border_24b8i624 != undefined  ? border_24b8i624 : "";
let g_dimension_4lwgcl24 = dimension_4lwgcl24 && dimension_4lwgcl24 != undefined  ? dimension_4lwgcl24 : "";
let g_boxshadow_2tmaij24 = boxshadow_2tmaij24 && boxshadow_2tmaij24 != undefined  ? boxshadow_2tmaij24 : "";
let g_border_z0pxba24 = border_z0pxba24 && border_z0pxba24 != undefined  ? border_z0pxba24 : "";
let g_boxshadow_f37dfv24 = boxshadow_f37dfv24 && boxshadow_f37dfv24 != undefined  ? boxshadow_f37dfv24 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-c5zodj25", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_c5zodj25 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-magic-hover-cta" data-minopa="${g_number_7ulqur25}" data-maxopa="${g_number_m6kdhq25}">
    <div class="w-magic-hover-container">
        <div class="w-magic-icon" data-icon-stroke="${g_color_fyu6d324}">
            ${g_iconscontrol_5ogz2q24}
        </div>
        <h1 class="w-magic-title" data-stroke="${g_color_fw1sjo24}">${g_title_22o96g24}</h1>
        <p class="w-magic-desc">${g_descarea_va04vf24}</p>
        <a class="w-magic-btn" href="${g_url_04b0z724_url}" target="${g_url_04b0z724_target}" rel="${g_url_04b0z724_nofollow} noopener">${g_btntext_af3w9224}</a>
        <div class="wkit-mhcta-fill-effect" data-color="${g_color_vmdio024}"></div>
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
    new Magic_Hover_CTA_c5zodj25();