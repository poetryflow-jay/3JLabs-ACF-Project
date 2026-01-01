
    class Open_Door_CTA_0iaprh24 {
        constructor() {
            this.Open_Door_CTA_0iaprh24_lif3y825();
        }
    
        Open_Door_CTA_0iaprh24_lif3y825() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Media,Pmgc_Url,Pmgc_Toggle,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Tabs,Pmgc_Range,Pmgc_Border,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-0iaprh24', {
        title: __('Open Door CTA'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-door-open tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Open Door'),__('Call to Action'),__('Hover Effect'),__('Opening Door Animation'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_an5fbo24Function = (unit, type) => {
                var g_slider_an5fbo24_list = [];
                g_slider_an5fbo24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_an5fbo24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_an5fbo24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_an5fbo24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_an5fbo24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_an5fbo24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_an5fbo24_list[unit][type];
            };
const slider_3rx4x624Function = (unit, type) => {
                var g_slider_3rx4x624_list = [];
                g_slider_3rx4x624_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_3rx4x624_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_3rx4x624_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_3rx4x624_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_3rx4x624_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_3rx4x624_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_3rx4x624_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_g2dr7v24,
text_axcmuo24,
media_sxu09l24,
url_xtmgq224,
switcher_ffps9u25,
switcher_92zvfd25,
rawhtml_djtqed25,
choose_bwfhr024,
dimension_h7dr0f25,
typography_olwwb124,
color_oiycwp24,
color_1n84rf24,
background_bwna2u24,
background_xc22jj24,
normalhover_cam2wv24,
slider_an5fbo24,
slider_3rx4x624,
border_urwr9h24,
dimension_za7zv224,
boxshadow_lbb3sm24,

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
                        main_function_0iaprh24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_0iaprh24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getCTA = $scope[0].querySelector('.wkit-open-door-cta');
let ctaAtag = getCTA.querySelector('.open-door-cta');
let linkContainer = getCTA.querySelector('.open-door-cta-inner');
const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;
ctaAtag.addEventListener('click', (e) => {
    if (
        (isMobile && !linkContainer.classList.contains('disable-mobile-link')) ||
        (isTablet && !linkContainer.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});
ctaAtag.addEventListener('mouseenter', (e)=>{
    getCTA.classList.add('active');
})
ctaAtag.addEventListener('mouseleave', (e)=>{
    getCTA.classList.remove('active');
})


            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Prefix Text`),
                type: "text",
                value: text_g2dr7v24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_g2dr7v24: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Postfix Text`),
                type: "text",
                value: text_axcmuo24,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_axcmuo24: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_sxu09l24,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_sxu09l24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_xtmgq224,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_xtmgq224: value }),
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_ffps9u25,
            
            onChange: (value) => setAttributes({ switcher_ffps9u25: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_92zvfd25,
            
            onChange: (value) => setAttributes({ switcher_92zvfd25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_djtqed25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/open-door-cta-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_djtqed25: value }),
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
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_bwfhr024,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_bwfhr024: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_h7dr0f25,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_h7dr0f25: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_olwwb124,
            onChange: (value) => setAttributes({ typography_olwwb124: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_oiycwp24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_oiycwp24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_bwna2u24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_bwna2u24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1n84rf24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1n84rf24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_xc22jj24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_xc22jj24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_an5fbo24,
            
            min: slider_an5fbo24 && slider_an5fbo24.unit ? slider_an5fbo24Function(slider_an5fbo24.unit, 'min') : 0,
            max: slider_an5fbo24 && slider_an5fbo24.unit ? slider_an5fbo24Function(slider_an5fbo24.unit, 'max') : 100,
            step: slider_an5fbo24 && slider_an5fbo24.unit ? slider_an5fbo24Function(slider_an5fbo24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_an5fbo24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_3rx4x624,
            
            min: slider_3rx4x624 && slider_3rx4x624.unit ? slider_3rx4x624Function(slider_3rx4x624.unit, 'min') : 0,
            max: slider_3rx4x624 && slider_3rx4x624.unit ? slider_3rx4x624Function(slider_3rx4x624.unit, 'max') : 100,
            step: slider_3rx4x624 && slider_3rx4x624.unit ? slider_3rx4x624Function(slider_3rx4x624.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_3rx4x624: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_urwr9h24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_urwr9h24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_za7zv224,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_za7zv224: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lbb3sm24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lbb3sm24: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-0iaprh24", block_id, false, props.clientId);
                }
            }

            
let g_text_g2dr7v24 = text_g2dr7v24 && text_g2dr7v24 != undefined  ? text_g2dr7v24 : "";
let g_text_axcmuo24 = text_axcmuo24 && text_axcmuo24 != undefined  ? text_axcmuo24 : "";
let g_media_sxu09l24 = media_sxu09l24 && media_sxu09l24.url && media_sxu09l24.url != undefined  ? media_sxu09l24.url : "";
let g_url_xtmgq224_url = url_xtmgq224?.url && url_xtmgq224?.url != undefined ? url_xtmgq224.url : "";
let g_url_xtmgq224_target = url_xtmgq224?.target && url_xtmgq224?.target != undefined ? url_xtmgq224.target : "";
let g_url_xtmgq224_nofollow = url_xtmgq224?.nofollow && url_xtmgq224?.nofollow != undefined ? url_xtmgq224.nofollow : "";
let g_url_xtmgq224_ctmArt = url_xtmgq224?.attr != undefined ? url_xtmgq224.attr : "";
                    let g_url_xtmgq224_attr = ''

                    if (g_url_xtmgq224_ctmArt) {
                        let main_array = g_url_xtmgq224_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_xtmgq224_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_ffps9u25 = switcher_ffps9u25 && switcher_ffps9u25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_92zvfd25 = switcher_92zvfd25 && switcher_92zvfd25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_djtqed25 = rawhtml_djtqed25 && rawhtml_djtqed25 != undefined  ? rawhtml_djtqed25 : "";
let g_choose_bwfhr024 = choose_bwfhr024 && choose_bwfhr024 != undefined  ? choose_bwfhr024 : "";
let g_dimension_h7dr0f25 = dimension_h7dr0f25 && dimension_h7dr0f25 != undefined  ? dimension_h7dr0f25 : "";
let g_typography_olwwb124 = typography_olwwb124 && typography_olwwb124 != undefined  ? typography_olwwb124 : "";
let g_color_oiycwp24 = color_oiycwp24 && color_oiycwp24 != undefined  ? color_oiycwp24 : "";
let g_color_1n84rf24 = color_1n84rf24 && color_1n84rf24 != undefined  ? color_1n84rf24 : "";
let g_background_bwna2u24 = background_bwna2u24 && background_bwna2u24 != undefined  ? background_bwna2u24 : "";
let g_background_xc22jj24 = background_xc22jj24 && background_xc22jj24 != undefined  ? background_xc22jj24 : "";
let g_border_urwr9h24 = border_urwr9h24 && border_urwr9h24 != undefined  ? border_urwr9h24 : "";
let g_dimension_za7zv224 = dimension_za7zv224 && dimension_za7zv224 != undefined  ? dimension_za7zv224 : "";
let g_boxshadow_lbb3sm24 = boxshadow_lbb3sm24 && boxshadow_lbb3sm24 != undefined  ? boxshadow_lbb3sm24 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_0iaprh24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-open-door-cta">
    <div class="open-door-cta-inner ${g_switcher_ffps9u25} ${g_switcher_92zvfd25}">
        <a class="open-door-cta" href="${g_url_xtmgq224_url}" target="${g_url_xtmgq224_target}" rel="${g_url_xtmgq224_nofollow} noopener">
            <span class="door-prefix-text" data-pretext="${g_text_g2dr7v24}">${g_text_g2dr7v24}</span>
            <div class="main-cta-door" style="background-image: url(${g_media_sxu09l24})"></div>
            <span class="door-postfix-text" data-posttext="${g_text_axcmuo24}">${g_text_axcmuo24}</span>
        </a>
    </div>
</div> `
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
            text_g2dr7v24,
text_axcmuo24,
media_sxu09l24,
url_xtmgq224,
switcher_ffps9u25,
switcher_92zvfd25,
rawhtml_djtqed25,
choose_bwfhr024,
dimension_h7dr0f25,
typography_olwwb124,
color_oiycwp24,
color_1n84rf24,
background_bwna2u24,
background_xc22jj24,
normalhover_cam2wv24,
slider_an5fbo24,
slider_3rx4x624,
border_urwr9h24,
dimension_za7zv224,
boxshadow_lbb3sm24,

            block_id,
        } = attributes;

        

        

        

        
let g_text_g2dr7v24 = text_g2dr7v24 && text_g2dr7v24 != undefined  ? text_g2dr7v24 : "";
let g_text_axcmuo24 = text_axcmuo24 && text_axcmuo24 != undefined  ? text_axcmuo24 : "";
let g_media_sxu09l24 = media_sxu09l24 && media_sxu09l24.url && media_sxu09l24.url != undefined  ? media_sxu09l24.url : "";
let g_url_xtmgq224_url = url_xtmgq224?.url && url_xtmgq224?.url != undefined ? url_xtmgq224.url : "";
let g_url_xtmgq224_target = url_xtmgq224?.target && url_xtmgq224?.target != undefined ? url_xtmgq224.target : "";
let g_url_xtmgq224_nofollow = url_xtmgq224?.nofollow && url_xtmgq224?.nofollow != undefined ? url_xtmgq224.nofollow : "";
let g_url_xtmgq224_ctmArt = url_xtmgq224?.attr != undefined ? url_xtmgq224.attr : "";
                    let g_url_xtmgq224_attr = ''

                    if (g_url_xtmgq224_ctmArt) {
                        let main_array = g_url_xtmgq224_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_xtmgq224_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_ffps9u25 = switcher_ffps9u25 && switcher_ffps9u25 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_92zvfd25 = switcher_92zvfd25 && switcher_92zvfd25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_djtqed25 = rawhtml_djtqed25 && rawhtml_djtqed25 != undefined  ? rawhtml_djtqed25 : "";
let g_choose_bwfhr024 = choose_bwfhr024 && choose_bwfhr024 != undefined  ? choose_bwfhr024 : "";
let g_dimension_h7dr0f25 = dimension_h7dr0f25 && dimension_h7dr0f25 != undefined  ? dimension_h7dr0f25 : "";
let g_typography_olwwb124 = typography_olwwb124 && typography_olwwb124 != undefined  ? typography_olwwb124 : "";
let g_color_oiycwp24 = color_oiycwp24 && color_oiycwp24 != undefined  ? color_oiycwp24 : "";
let g_color_1n84rf24 = color_1n84rf24 && color_1n84rf24 != undefined  ? color_1n84rf24 : "";
let g_background_bwna2u24 = background_bwna2u24 && background_bwna2u24 != undefined  ? background_bwna2u24 : "";
let g_background_xc22jj24 = background_xc22jj24 && background_xc22jj24 != undefined  ? background_xc22jj24 : "";
let g_border_urwr9h24 = border_urwr9h24 && border_urwr9h24 != undefined  ? border_urwr9h24 : "";
let g_dimension_za7zv224 = dimension_za7zv224 && dimension_za7zv224 != undefined  ? dimension_za7zv224 : "";
let g_boxshadow_lbb3sm24 = boxshadow_lbb3sm24 && boxshadow_lbb3sm24 != undefined  ? boxshadow_lbb3sm24 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-0iaprh24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_0iaprh24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-open-door-cta">
    <div class="open-door-cta-inner ${g_switcher_ffps9u25} ${g_switcher_92zvfd25}">
        <a class="open-door-cta" href="${g_url_xtmgq224_url}" target="${g_url_xtmgq224_target}" rel="${g_url_xtmgq224_nofollow} noopener">
            <span class="door-prefix-text" data-pretext="${g_text_g2dr7v24}">${g_text_g2dr7v24}</span>
            <div class="main-cta-door" style="background-image: url(${g_media_sxu09l24})"></div>
            <span class="door-postfix-text" data-posttext="${g_text_axcmuo24}">${g_text_axcmuo24}</span>
        </a>
    </div>
</div> `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Open_Door_CTA_0iaprh24();