
    class CTA_Image_Box_sj5wmm24 {
        constructor() {
            this.CTA_Image_Box_sj5wmm24_y1vnx325();
        }
    
        CTA_Image_Box_sj5wmm24_y1vnx325() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_TextArea,Pmgc_Media,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Typography,Pmgc_Color,Pmgc_Label_Heading,Pmgc_Dimension,Pmgc_Range,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-sj5wmm24', {
        title: __('CTA Image Box'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-boxes tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Content Card Design'),__('Hover Effects'),__('Image Box'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_xxnafn23Function = (unit, type) => {
                var g_slider_xxnafn23_list = [];
                g_slider_xxnafn23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_xxnafn23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_xxnafn23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_xxnafn23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_xxnafn23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_xxnafn23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_xxnafn23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_8wm4ah23,
textarea_90fwfc23,
media_g1sl5u23,
rawhtml_bq2hy625,
choose_dtkucc23,
typography_05gw5v23,
color_sk0una23,
choose_ouoj6i25,
typography_xqhovj23,
color_3z99ek23,
heading_t7w26y23,
choose_jew2pz23,
heading_tsks1123,
choose_thjhop23,
dimension_tylq7223,
slider_xxnafn23,
background_bnilhn23,
border_cij2ye23,
border_97e15a23,
dimension_y8d75s23,
boxshadow_etwxh323,
boxshadow_e2ep8223,
normalhover_bawg9h23,

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
                        main_function_sj5wmm24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_sj5wmm24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let ctaimg = $scope[0].querySelectorAll(".wkit-cta-image-box");

ctaimg.forEach((e) => {
    e.parentElement.style.width = '100%';
});
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_8wm4ah23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_8wm4ah23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_90fwfc23,
                dynamic: true,
                onChange: (value) => setAttributes({ textarea_90fwfc23: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_g1sl5u23,
                dynamic: [true, 'media_g1sl5u23'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_g1sl5u23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_bq2hy625,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/cta-image-box-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_bq2hy625: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_dtkucc23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_dtkucc23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_05gw5v23,
            onChange: (value) => setAttributes({ typography_05gw5v23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_sk0una23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_sk0una23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_ouoj6i25,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_ouoj6i25: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_xqhovj23,
            onChange: (value) => setAttributes({ typography_xqhovj23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_3z99ek23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3z99ek23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Image Box"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Horizontal`),
            value: heading_t7w26y23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'baseline', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_jew2pz23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_jew2pz23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Vertical`),
            value: heading_tsks1123,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_thjhop23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_thjhop23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_tylq7223,
            noLock: false,
            unit: ['px','%',],
            separator:"before",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_tylq7223: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_xxnafn23,
            
            min: slider_xxnafn23 && slider_xxnafn23.unit ? slider_xxnafn23Function(slider_xxnafn23.unit, 'min') : 0,
            max: slider_xxnafn23 && slider_xxnafn23.unit ? slider_xxnafn23Function(slider_xxnafn23.unit, 'max') : 100,
            step: slider_xxnafn23 && slider_xxnafn23.unit ? slider_xxnafn23Function(slider_xxnafn23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_xxnafn23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_97e15a23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_97e15a23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_y8d75s23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_y8d75s23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_etwxh323,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_etwxh323: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_bnilhn23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_bnilhn23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_cij2ye23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_cij2ye23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_e2ep8223,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_e2ep8223: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-sj5wmm24", block_id, false, props.clientId);
                }
            }

            
let g_text_8wm4ah23 = text_8wm4ah23 && text_8wm4ah23 != undefined  ? text_8wm4ah23 : "";
let g_textarea_90fwfc23 = textarea_90fwfc23 && textarea_90fwfc23 != undefined  ? textarea_90fwfc23 : "";
let g_media_g1sl5u23 = media_g1sl5u23 && media_g1sl5u23.url && media_g1sl5u23.url != undefined  ? media_g1sl5u23.url : "";
let g_rawhtml_bq2hy625 = rawhtml_bq2hy625 && rawhtml_bq2hy625 != undefined  ? rawhtml_bq2hy625 : "";
let g_choose_dtkucc23 = choose_dtkucc23 && choose_dtkucc23 != undefined  ? choose_dtkucc23 : "";
let g_typography_05gw5v23 = typography_05gw5v23 && typography_05gw5v23 != undefined  ? typography_05gw5v23 : "";
let g_color_sk0una23 = color_sk0una23 && color_sk0una23 != undefined  ? color_sk0una23 : "";
let g_choose_ouoj6i25 = choose_ouoj6i25 && choose_ouoj6i25 != undefined  ? choose_ouoj6i25 : "";
let g_typography_xqhovj23 = typography_xqhovj23 && typography_xqhovj23 != undefined  ? typography_xqhovj23 : "";
let g_color_3z99ek23 = color_3z99ek23 && color_3z99ek23 != undefined  ? color_3z99ek23 : "";
let g_heading_t7w26y23 = heading_t7w26y23 && heading_t7w26y23 != undefined  ? heading_t7w26y23 : "";
let g_choose_jew2pz23 = choose_jew2pz23 && choose_jew2pz23 != undefined  ? choose_jew2pz23 : "";
let g_heading_tsks1123 = heading_tsks1123 && heading_tsks1123 != undefined  ? heading_tsks1123 : "";
let g_choose_thjhop23 = choose_thjhop23 && choose_thjhop23 != undefined  ? choose_thjhop23 : "";
let g_dimension_tylq7223 = dimension_tylq7223 && dimension_tylq7223 != undefined  ? dimension_tylq7223 : "";
let g_background_bnilhn23 = background_bnilhn23 && background_bnilhn23 != undefined  ? background_bnilhn23 : "";
let g_border_cij2ye23 = border_cij2ye23 && border_cij2ye23 != undefined  ? border_cij2ye23 : "";
let g_border_97e15a23 = border_97e15a23 && border_97e15a23 != undefined  ? border_97e15a23 : "";
let g_dimension_y8d75s23 = dimension_y8d75s23 && dimension_y8d75s23 != undefined  ? dimension_y8d75s23 : "";
let g_boxshadow_etwxh323 = boxshadow_etwxh323 && boxshadow_etwxh323 != undefined  ? boxshadow_etwxh323 : "";
let g_boxshadow_e2ep8223 = boxshadow_e2ep8223 && boxshadow_e2ep8223 != undefined  ? boxshadow_e2ep8223 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_sj5wmm24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-cta-image-box">
    <div class="cta-image-inner">
        <img class="cta-img" src="${g_media_g1sl5u23}">
        <div class="cta-content">
            <span class="cta-content-title">${g_text_8wm4ah23}</span>
            <p class="cta-content-desc">${g_textarea_90fwfc23}</p>
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
            text_8wm4ah23,
textarea_90fwfc23,
media_g1sl5u23,
rawhtml_bq2hy625,
choose_dtkucc23,
typography_05gw5v23,
color_sk0una23,
choose_ouoj6i25,
typography_xqhovj23,
color_3z99ek23,
heading_t7w26y23,
choose_jew2pz23,
heading_tsks1123,
choose_thjhop23,
dimension_tylq7223,
slider_xxnafn23,
background_bnilhn23,
border_cij2ye23,
border_97e15a23,
dimension_y8d75s23,
boxshadow_etwxh323,
boxshadow_e2ep8223,
normalhover_bawg9h23,

            block_id,
        } = attributes;

        

        

        

        
let g_text_8wm4ah23 = text_8wm4ah23 && text_8wm4ah23 != undefined  ? text_8wm4ah23 : "";
let g_textarea_90fwfc23 = textarea_90fwfc23 && textarea_90fwfc23 != undefined  ? textarea_90fwfc23 : "";
let g_media_g1sl5u23 = media_g1sl5u23 && media_g1sl5u23.url && media_g1sl5u23.url != undefined  ? media_g1sl5u23.url : "";
let g_rawhtml_bq2hy625 = rawhtml_bq2hy625 && rawhtml_bq2hy625 != undefined  ? rawhtml_bq2hy625 : "";
let g_choose_dtkucc23 = choose_dtkucc23 && choose_dtkucc23 != undefined  ? choose_dtkucc23 : "";
let g_typography_05gw5v23 = typography_05gw5v23 && typography_05gw5v23 != undefined  ? typography_05gw5v23 : "";
let g_color_sk0una23 = color_sk0una23 && color_sk0una23 != undefined  ? color_sk0una23 : "";
let g_choose_ouoj6i25 = choose_ouoj6i25 && choose_ouoj6i25 != undefined  ? choose_ouoj6i25 : "";
let g_typography_xqhovj23 = typography_xqhovj23 && typography_xqhovj23 != undefined  ? typography_xqhovj23 : "";
let g_color_3z99ek23 = color_3z99ek23 && color_3z99ek23 != undefined  ? color_3z99ek23 : "";
let g_heading_t7w26y23 = heading_t7w26y23 && heading_t7w26y23 != undefined  ? heading_t7w26y23 : "";
let g_choose_jew2pz23 = choose_jew2pz23 && choose_jew2pz23 != undefined  ? choose_jew2pz23 : "";
let g_heading_tsks1123 = heading_tsks1123 && heading_tsks1123 != undefined  ? heading_tsks1123 : "";
let g_choose_thjhop23 = choose_thjhop23 && choose_thjhop23 != undefined  ? choose_thjhop23 : "";
let g_dimension_tylq7223 = dimension_tylq7223 && dimension_tylq7223 != undefined  ? dimension_tylq7223 : "";
let g_background_bnilhn23 = background_bnilhn23 && background_bnilhn23 != undefined  ? background_bnilhn23 : "";
let g_border_cij2ye23 = border_cij2ye23 && border_cij2ye23 != undefined  ? border_cij2ye23 : "";
let g_border_97e15a23 = border_97e15a23 && border_97e15a23 != undefined  ? border_97e15a23 : "";
let g_dimension_y8d75s23 = dimension_y8d75s23 && dimension_y8d75s23 != undefined  ? dimension_y8d75s23 : "";
let g_boxshadow_etwxh323 = boxshadow_etwxh323 && boxshadow_etwxh323 != undefined  ? boxshadow_etwxh323 : "";
let g_boxshadow_e2ep8223 = boxshadow_e2ep8223 && boxshadow_e2ep8223 != undefined  ? boxshadow_e2ep8223 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-sj5wmm24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_sj5wmm24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-cta-image-box">
    <div class="cta-image-inner">
        <img class="cta-img" src="${g_media_g1sl5u23}">
        <div class="cta-content">
            <span class="cta-content-title">${g_text_8wm4ah23}</span>
            <p class="cta-content-desc">${g_textarea_90fwfc23}</p>
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
    new CTA_Image_Box_sj5wmm24();