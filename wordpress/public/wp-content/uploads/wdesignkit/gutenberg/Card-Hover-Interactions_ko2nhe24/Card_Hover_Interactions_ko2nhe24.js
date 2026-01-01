
    class Card_Hover_Interactions_ko2nhe24 {
        constructor() {
            this.Card_Hover_Interactions_ko2nhe24_dtana625();
        }
    
        Card_Hover_Interactions_ko2nhe24_dtana625() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_TextArea,Pmgc_Url,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-ko2nhe24', {
        title: __('Card Hover Interactions'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-life-ring tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Card Hover'),__('Card Interaction'),__('Web Design'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_usi3t524Function = (unit, type) => {
                var g_slider_usi3t524_list = [];
                g_slider_usi3t524_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_usi3t524_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_usi3t524_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_usi3t524_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_usi3t524_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_usi3t524_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_usi3t524_list[unit][type];
            };
const slider_10ou3x25Function = (unit, type) => {
                var g_slider_10ou3x25_list = [];
                g_slider_10ou3x25_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_10ou3x25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_10ou3x25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_10ou3x25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_10ou3x25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_10ou3x25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_10ou3x25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_8vc69r23,
wysiwyg_i7nxdv23,
text_he3eqm23,
url_l53fwh24,
rawhtml_ykf4g025,
choose_tele1x23,
dimension_uvg3ob23,
typography_muk4h623,
color_6igtei23,
color_k95kgo23,
background_lq5qj723,
background_0lmfm923,
border_nmwo8l23,
border_38egoy23,
dimension_pc4t3l23,
boxshadow_fk8d1723,
boxshadow_39lk3l23,
normalhover_kexxal23,
choose_zt2u9s23,
dimension_kvmy8s23,
typography_vshpia23,
color_66m0bp23,
background_4rh4lg23,
border_vilwcc23,
dimension_1uwf4x23,
boxshadow_lrlqu523,
color_0vq1mc23,
background_iqgerx23,
border_c78qak23,
boxshadow_2nh5ri23,
normalhover_v8um5423,
choose_ch268d23,
dimension_o71r2i23,
dimension_zo7n4r23,
typography_xcu08723,
color_dqol0c23,
background_tcl91s23,
border_wmbfg123,
dimension_b65lva23,
color_p9gr2o23,
background_pwg8rf23,
border_zu9iqp23,
boxshadow_p123fm23,
boxshadow_az5c7723,
normalhover_9qyqrh23,
dimension_3k9nvu23,
dimension_m9lynd23,
slider_usi3t524,
slider_10ou3x25,
color_72ry3t25,
background_9rrp6o23,
border_mz61js23,
dimension_afs8c723,
boxshadow_lszi4523,
background_mkuc3u23,
border_th1hpv23,
boxshadow_5jooob23,
normalhover_1zo4f823,

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
                        main_function_ko2nhe24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_ko2nhe24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let mainCard = $scope[0].querySelector('.wkit-card-hvr-main-interact');
let getContent = mainCard.querySelector('.wkit-card-hvr-inn-content');
let getTitle = mainCard.querySelector('.wkit-card-hvr-title');

let titleHeight = getTitle.offsetHeight;
getContent.style.transform = 'translateY(calc(100% - ' + titleHeight + 'px))';


mainCard.addEventListener('mouseenter', function() {
    getContent.style.transform = 'translateY(0)';
});

mainCard.addEventListener('mouseleave', function() {
    getContent.style.transform = 'translateY(calc(100% - ' + titleHeight + 'px))';
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
                value: text_8vc69r23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_8vc69r23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"2",
                value: wysiwyg_i7nxdv23,
                dynamic: true,
                onChange: (value) => setAttributes({ wysiwyg_i7nxdv23: value }),
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_he3eqm23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_he3eqm23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_l53fwh24,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_l53fwh24: value }),
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ykf4g025,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/card-hover-interactions-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_ykf4g025: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                           ( text_8vc69r23 != "" ) && React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
( text_8vc69r23 != "" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_tele1x23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_tele1x23: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_uvg3ob23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_uvg3ob23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_muk4h623,
            onChange: (value) => setAttributes({ typography_muk4h623: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( text_8vc69r23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_6igtei23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_6igtei23: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_lq5qj723,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lq5qj723: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_nmwo8l23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_nmwo8l23: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_pc4t3l23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_pc4t3l23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_8vc69r23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_fk8d1723,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_fk8d1723: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( text_8vc69r23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_k95kgo23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_k95kgo23: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_0lmfm923,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_0lmfm923: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_38egoy23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_38egoy23: value }),
            }), 
( text_8vc69r23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_39lk3l23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_39lk3l23: value }),
            }), 
), 
), 
),( wysiwyg_i7nxdv23 != "" ) && React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_zt2u9s23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_zt2u9s23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_kvmy8s23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_kvmy8s23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_vshpia23,
            onChange: (value) => setAttributes({ typography_vshpia23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_66m0bp23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_66m0bp23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_4rh4lg23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_4rh4lg23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_vilwcc23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_vilwcc23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_1uwf4x23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_1uwf4x23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lrlqu523,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lrlqu523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_0vq1mc23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0vq1mc23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_iqgerx23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_iqgerx23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_c78qak23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_c78qak23: value }),
            }), 
( wysiwyg_i7nxdv23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_2nh5ri23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_2nh5ri23: value }),
            }), 
), 
), 
),( text_he3eqm23 != "" ) && React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
( text_he3eqm23 != "" ) && React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_ch268d23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_ch268d23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_o71r2i23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_o71r2i23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_zo7n4r23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_zo7n4r23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_xcu08723,
            onChange: (value) => setAttributes({ typography_xcu08723: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( text_he3eqm23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_dqol0c23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_dqol0c23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_tcl91s23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_tcl91s23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_wmbfg123,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_wmbfg123: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_b65lva23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_b65lva23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_he3eqm23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_p123fm23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_p123fm23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( text_he3eqm23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_p9gr2o23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_p9gr2o23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Background, {
            
            value: background_pwg8rf23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_pwg8rf23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_Border, {
            
            value: border_zu9iqp23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_zu9iqp23: value }),
            }), 
( text_he3eqm23 != "" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_az5c7723,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_az5c7723: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Box Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_3k9nvu23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_3k9nvu23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_m9lynd23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_m9lynd23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_usi3t524,
            
            min: slider_usi3t524 && slider_usi3t524.unit ? slider_usi3t524Function(slider_usi3t524.unit, 'min') : 0,
            max: slider_usi3t524 && slider_usi3t524.unit ? slider_usi3t524Function(slider_usi3t524.unit, 'max') : 100,
            step: slider_usi3t524 && slider_usi3t524.unit ? slider_usi3t524Function(slider_usi3t524.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_usi3t524: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_10ou3x25,
            
            min: slider_10ou3x25 && slider_10ou3x25.unit ? slider_10ou3x25Function(slider_10ou3x25.unit, 'min') : 0,
            max: slider_10ou3x25 && slider_10ou3x25.unit ? slider_10ou3x25Function(slider_10ou3x25.unit, 'max') : 100,
            step: slider_10ou3x25 && slider_10ou3x25.unit ? slider_10ou3x25Function(slider_10ou3x25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_10ou3x25: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Overlay Color`),
            value: color_72ry3t25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_72ry3t25: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_9rrp6o23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_9rrp6o23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_mz61js23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_mz61js23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_afs8c723,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_afs8c723: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lszi4523,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lszi4523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_mkuc3u23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_mkuc3u23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_th1hpv23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_th1hpv23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_5jooob23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_5jooob23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-ko2nhe24", block_id, false, props.clientId);
                }
            }

            
let g_text_8vc69r23 = text_8vc69r23 && text_8vc69r23 != undefined  ? text_8vc69r23 : "";
let g_wysiwyg_i7nxdv23 = wysiwyg_i7nxdv23 && wysiwyg_i7nxdv23 != undefined  ? wysiwyg_i7nxdv23 : "";
let g_text_he3eqm23 = text_he3eqm23 && text_he3eqm23 != undefined  ? text_he3eqm23 : "";
let g_url_l53fwh24_url = url_l53fwh24?.url && url_l53fwh24?.url != undefined ? url_l53fwh24.url : "";
let g_url_l53fwh24_target = url_l53fwh24?.target && url_l53fwh24?.target != undefined ? url_l53fwh24.target : "";
let g_url_l53fwh24_nofollow = url_l53fwh24?.nofollow && url_l53fwh24?.nofollow != undefined ? url_l53fwh24.nofollow : "";
let g_url_l53fwh24_ctmArt = url_l53fwh24?.attr != undefined ? url_l53fwh24.attr : "";
                    let g_url_l53fwh24_attr = ''

                    if (g_url_l53fwh24_ctmArt) {
                        let main_array = g_url_l53fwh24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_l53fwh24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_ykf4g025 = rawhtml_ykf4g025 && rawhtml_ykf4g025 != undefined  ? rawhtml_ykf4g025 : "";
let g_choose_tele1x23 = choose_tele1x23 && choose_tele1x23 != undefined && ( (text_8vc69r23 != "") ) ? choose_tele1x23 : "";
let g_dimension_uvg3ob23 = dimension_uvg3ob23 && dimension_uvg3ob23 != undefined && ( (text_8vc69r23 != "") ) ? dimension_uvg3ob23 : "";
let g_typography_muk4h623 = typography_muk4h623 && typography_muk4h623 != undefined && ( (text_8vc69r23 != "") ) ? typography_muk4h623 : "";
let g_color_6igtei23 = color_6igtei23 && color_6igtei23 != undefined && ( (text_8vc69r23 != "") ) ? color_6igtei23 : "";
let g_color_k95kgo23 = color_k95kgo23 && color_k95kgo23 != undefined && ( (text_8vc69r23 != "") ) ? color_k95kgo23 : "";
let g_background_lq5qj723 = background_lq5qj723 && background_lq5qj723 != undefined && ( (text_8vc69r23 != "") ) ? background_lq5qj723 : "";
let g_background_0lmfm923 = background_0lmfm923 && background_0lmfm923 != undefined && ( (text_8vc69r23 != "") ) ? background_0lmfm923 : "";
let g_border_nmwo8l23 = border_nmwo8l23 && border_nmwo8l23 != undefined && ( (text_8vc69r23 != "") ) ? border_nmwo8l23 : "";
let g_border_38egoy23 = border_38egoy23 && border_38egoy23 != undefined && ( (text_8vc69r23 != "") ) ? border_38egoy23 : "";
let g_dimension_pc4t3l23 = dimension_pc4t3l23 && dimension_pc4t3l23 != undefined && ( (text_8vc69r23 != "") ) ? dimension_pc4t3l23 : "";
let g_boxshadow_fk8d1723 = boxshadow_fk8d1723 && boxshadow_fk8d1723 != undefined && ( (text_8vc69r23 != "") ) ? boxshadow_fk8d1723 : "";
let g_boxshadow_39lk3l23 = boxshadow_39lk3l23 && boxshadow_39lk3l23 != undefined && ( (text_8vc69r23 != "") ) ? boxshadow_39lk3l23 : "";
let g_choose_zt2u9s23 = choose_zt2u9s23 && choose_zt2u9s23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? choose_zt2u9s23 : "";
let g_dimension_kvmy8s23 = dimension_kvmy8s23 && dimension_kvmy8s23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? dimension_kvmy8s23 : "";
let g_typography_vshpia23 = typography_vshpia23 && typography_vshpia23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? typography_vshpia23 : "";
let g_color_66m0bp23 = color_66m0bp23 && color_66m0bp23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? color_66m0bp23 : "";
let g_background_4rh4lg23 = background_4rh4lg23 && background_4rh4lg23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? background_4rh4lg23 : "";
let g_border_vilwcc23 = border_vilwcc23 && border_vilwcc23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? border_vilwcc23 : "";
let g_dimension_1uwf4x23 = dimension_1uwf4x23 && dimension_1uwf4x23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? dimension_1uwf4x23 : "";
let g_boxshadow_lrlqu523 = boxshadow_lrlqu523 && boxshadow_lrlqu523 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? boxshadow_lrlqu523 : "";
let g_color_0vq1mc23 = color_0vq1mc23 && color_0vq1mc23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? color_0vq1mc23 : "";
let g_background_iqgerx23 = background_iqgerx23 && background_iqgerx23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? background_iqgerx23 : "";
let g_border_c78qak23 = border_c78qak23 && border_c78qak23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? border_c78qak23 : "";
let g_boxshadow_2nh5ri23 = boxshadow_2nh5ri23 && boxshadow_2nh5ri23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? boxshadow_2nh5ri23 : "";
let g_choose_ch268d23 = choose_ch268d23 && choose_ch268d23 != undefined && ( (text_he3eqm23 != "") ) ? choose_ch268d23 : "";
let g_dimension_o71r2i23 = dimension_o71r2i23 && dimension_o71r2i23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_o71r2i23 : "";
let g_dimension_zo7n4r23 = dimension_zo7n4r23 && dimension_zo7n4r23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_zo7n4r23 : "";
let g_typography_xcu08723 = typography_xcu08723 && typography_xcu08723 != undefined && ( (text_he3eqm23 != "") ) ? typography_xcu08723 : "";
let g_color_dqol0c23 = color_dqol0c23 && color_dqol0c23 != undefined && ( (text_he3eqm23 != "") ) ? color_dqol0c23 : "";
let g_background_tcl91s23 = background_tcl91s23 && background_tcl91s23 != undefined && ( (text_he3eqm23 != "") ) ? background_tcl91s23 : "";
let g_border_wmbfg123 = border_wmbfg123 && border_wmbfg123 != undefined && ( (text_he3eqm23 != "") ) ? border_wmbfg123 : "";
let g_dimension_b65lva23 = dimension_b65lva23 && dimension_b65lva23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_b65lva23 : "";
let g_color_p9gr2o23 = color_p9gr2o23 && color_p9gr2o23 != undefined && ( (text_he3eqm23 != "") ) ? color_p9gr2o23 : "";
let g_background_pwg8rf23 = background_pwg8rf23 && background_pwg8rf23 != undefined && ( (text_he3eqm23 != "") ) ? background_pwg8rf23 : "";
let g_border_zu9iqp23 = border_zu9iqp23 && border_zu9iqp23 != undefined && ( (text_he3eqm23 != "") ) ? border_zu9iqp23 : "";
let g_boxshadow_p123fm23 = boxshadow_p123fm23 && boxshadow_p123fm23 != undefined && ( (text_he3eqm23 != "") ) ? boxshadow_p123fm23 : "";
let g_boxshadow_az5c7723 = boxshadow_az5c7723 && boxshadow_az5c7723 != undefined && ( (text_he3eqm23 != "") ) ? boxshadow_az5c7723 : "";
let g_dimension_3k9nvu23 = dimension_3k9nvu23 && dimension_3k9nvu23 != undefined  ? dimension_3k9nvu23 : "";
let g_dimension_m9lynd23 = dimension_m9lynd23 && dimension_m9lynd23 != undefined  ? dimension_m9lynd23 : "";
let g_color_72ry3t25 = color_72ry3t25 && color_72ry3t25 != undefined  ? color_72ry3t25 : "";
let g_background_9rrp6o23 = background_9rrp6o23 && background_9rrp6o23 != undefined  ? background_9rrp6o23 : "";
let g_border_mz61js23 = border_mz61js23 && border_mz61js23 != undefined  ? border_mz61js23 : "";
let g_dimension_afs8c723 = dimension_afs8c723 && dimension_afs8c723 != undefined  ? dimension_afs8c723 : "";
let g_boxshadow_lszi4523 = boxshadow_lszi4523 && boxshadow_lszi4523 != undefined  ? boxshadow_lszi4523 : "";
let g_background_mkuc3u23 = background_mkuc3u23 && background_mkuc3u23 != undefined  ? background_mkuc3u23 : "";
let g_border_th1hpv23 = border_th1hpv23 && border_th1hpv23 != undefined  ? border_th1hpv23 : "";
let g_boxshadow_5jooob23 = boxshadow_5jooob23 && boxshadow_5jooob23 != undefined  ? boxshadow_5jooob23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ko2nhe24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-card-hvr-main-interact">
    <div class="wkit-card-hvr-inn-content">
        <h2 class="wkit-card-hvr-title" data-ttl="${g_text_8vc69r23}">
            ${g_text_8vc69r23}
        </h2>
         <span class="wkit-card-hvr-desc" data-desc="${g_wysiwyg_i7nxdv23}">
            ${g_wysiwyg_i7nxdv23}
        </span>
        <a class="wkit-card-hvr-main-btn" data-btn="${g_text_he3eqm23}" href="${g_url_l53fwh24_url}" target="${g_url_l53fwh24_target}" rel="${g_url_l53fwh24_nofollow} noopener">
            <button class="wkit-card-hvr-btn">
                ${g_text_he3eqm23}
            </button>
        </a>
    </div>
</div>     `
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
            text_8vc69r23,
wysiwyg_i7nxdv23,
text_he3eqm23,
url_l53fwh24,
rawhtml_ykf4g025,
choose_tele1x23,
dimension_uvg3ob23,
typography_muk4h623,
color_6igtei23,
color_k95kgo23,
background_lq5qj723,
background_0lmfm923,
border_nmwo8l23,
border_38egoy23,
dimension_pc4t3l23,
boxshadow_fk8d1723,
boxshadow_39lk3l23,
normalhover_kexxal23,
choose_zt2u9s23,
dimension_kvmy8s23,
typography_vshpia23,
color_66m0bp23,
background_4rh4lg23,
border_vilwcc23,
dimension_1uwf4x23,
boxshadow_lrlqu523,
color_0vq1mc23,
background_iqgerx23,
border_c78qak23,
boxshadow_2nh5ri23,
normalhover_v8um5423,
choose_ch268d23,
dimension_o71r2i23,
dimension_zo7n4r23,
typography_xcu08723,
color_dqol0c23,
background_tcl91s23,
border_wmbfg123,
dimension_b65lva23,
color_p9gr2o23,
background_pwg8rf23,
border_zu9iqp23,
boxshadow_p123fm23,
boxshadow_az5c7723,
normalhover_9qyqrh23,
dimension_3k9nvu23,
dimension_m9lynd23,
slider_usi3t524,
slider_10ou3x25,
color_72ry3t25,
background_9rrp6o23,
border_mz61js23,
dimension_afs8c723,
boxshadow_lszi4523,
background_mkuc3u23,
border_th1hpv23,
boxshadow_5jooob23,
normalhover_1zo4f823,

            block_id,
        } = attributes;

        

        

        

        
let g_text_8vc69r23 = text_8vc69r23 && text_8vc69r23 != undefined  ? text_8vc69r23 : "";
let g_wysiwyg_i7nxdv23 = wysiwyg_i7nxdv23 && wysiwyg_i7nxdv23 != undefined  ? wysiwyg_i7nxdv23 : "";
let g_text_he3eqm23 = text_he3eqm23 && text_he3eqm23 != undefined  ? text_he3eqm23 : "";
let g_url_l53fwh24_url = url_l53fwh24?.url && url_l53fwh24?.url != undefined ? url_l53fwh24.url : "";
let g_url_l53fwh24_target = url_l53fwh24?.target && url_l53fwh24?.target != undefined ? url_l53fwh24.target : "";
let g_url_l53fwh24_nofollow = url_l53fwh24?.nofollow && url_l53fwh24?.nofollow != undefined ? url_l53fwh24.nofollow : "";
let g_url_l53fwh24_ctmArt = url_l53fwh24?.attr != undefined ? url_l53fwh24.attr : "";
                    let g_url_l53fwh24_attr = ''

                    if (g_url_l53fwh24_ctmArt) {
                        let main_array = g_url_l53fwh24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_l53fwh24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_ykf4g025 = rawhtml_ykf4g025 && rawhtml_ykf4g025 != undefined  ? rawhtml_ykf4g025 : "";
let g_choose_tele1x23 = choose_tele1x23 && choose_tele1x23 != undefined && ( (text_8vc69r23 != "") ) ? choose_tele1x23 : "";
let g_dimension_uvg3ob23 = dimension_uvg3ob23 && dimension_uvg3ob23 != undefined && ( (text_8vc69r23 != "") ) ? dimension_uvg3ob23 : "";
let g_typography_muk4h623 = typography_muk4h623 && typography_muk4h623 != undefined && ( (text_8vc69r23 != "") ) ? typography_muk4h623 : "";
let g_color_6igtei23 = color_6igtei23 && color_6igtei23 != undefined && ( (text_8vc69r23 != "") ) ? color_6igtei23 : "";
let g_color_k95kgo23 = color_k95kgo23 && color_k95kgo23 != undefined && ( (text_8vc69r23 != "") ) ? color_k95kgo23 : "";
let g_background_lq5qj723 = background_lq5qj723 && background_lq5qj723 != undefined && ( (text_8vc69r23 != "") ) ? background_lq5qj723 : "";
let g_background_0lmfm923 = background_0lmfm923 && background_0lmfm923 != undefined && ( (text_8vc69r23 != "") ) ? background_0lmfm923 : "";
let g_border_nmwo8l23 = border_nmwo8l23 && border_nmwo8l23 != undefined && ( (text_8vc69r23 != "") ) ? border_nmwo8l23 : "";
let g_border_38egoy23 = border_38egoy23 && border_38egoy23 != undefined && ( (text_8vc69r23 != "") ) ? border_38egoy23 : "";
let g_dimension_pc4t3l23 = dimension_pc4t3l23 && dimension_pc4t3l23 != undefined && ( (text_8vc69r23 != "") ) ? dimension_pc4t3l23 : "";
let g_boxshadow_fk8d1723 = boxshadow_fk8d1723 && boxshadow_fk8d1723 != undefined && ( (text_8vc69r23 != "") ) ? boxshadow_fk8d1723 : "";
let g_boxshadow_39lk3l23 = boxshadow_39lk3l23 && boxshadow_39lk3l23 != undefined && ( (text_8vc69r23 != "") ) ? boxshadow_39lk3l23 : "";
let g_choose_zt2u9s23 = choose_zt2u9s23 && choose_zt2u9s23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? choose_zt2u9s23 : "";
let g_dimension_kvmy8s23 = dimension_kvmy8s23 && dimension_kvmy8s23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? dimension_kvmy8s23 : "";
let g_typography_vshpia23 = typography_vshpia23 && typography_vshpia23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? typography_vshpia23 : "";
let g_color_66m0bp23 = color_66m0bp23 && color_66m0bp23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? color_66m0bp23 : "";
let g_background_4rh4lg23 = background_4rh4lg23 && background_4rh4lg23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? background_4rh4lg23 : "";
let g_border_vilwcc23 = border_vilwcc23 && border_vilwcc23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? border_vilwcc23 : "";
let g_dimension_1uwf4x23 = dimension_1uwf4x23 && dimension_1uwf4x23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? dimension_1uwf4x23 : "";
let g_boxshadow_lrlqu523 = boxshadow_lrlqu523 && boxshadow_lrlqu523 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? boxshadow_lrlqu523 : "";
let g_color_0vq1mc23 = color_0vq1mc23 && color_0vq1mc23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? color_0vq1mc23 : "";
let g_background_iqgerx23 = background_iqgerx23 && background_iqgerx23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? background_iqgerx23 : "";
let g_border_c78qak23 = border_c78qak23 && border_c78qak23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? border_c78qak23 : "";
let g_boxshadow_2nh5ri23 = boxshadow_2nh5ri23 && boxshadow_2nh5ri23 != undefined && ( (wysiwyg_i7nxdv23 != "") ) ? boxshadow_2nh5ri23 : "";
let g_choose_ch268d23 = choose_ch268d23 && choose_ch268d23 != undefined && ( (text_he3eqm23 != "") ) ? choose_ch268d23 : "";
let g_dimension_o71r2i23 = dimension_o71r2i23 && dimension_o71r2i23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_o71r2i23 : "";
let g_dimension_zo7n4r23 = dimension_zo7n4r23 && dimension_zo7n4r23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_zo7n4r23 : "";
let g_typography_xcu08723 = typography_xcu08723 && typography_xcu08723 != undefined && ( (text_he3eqm23 != "") ) ? typography_xcu08723 : "";
let g_color_dqol0c23 = color_dqol0c23 && color_dqol0c23 != undefined && ( (text_he3eqm23 != "") ) ? color_dqol0c23 : "";
let g_background_tcl91s23 = background_tcl91s23 && background_tcl91s23 != undefined && ( (text_he3eqm23 != "") ) ? background_tcl91s23 : "";
let g_border_wmbfg123 = border_wmbfg123 && border_wmbfg123 != undefined && ( (text_he3eqm23 != "") ) ? border_wmbfg123 : "";
let g_dimension_b65lva23 = dimension_b65lva23 && dimension_b65lva23 != undefined && ( (text_he3eqm23 != "") ) ? dimension_b65lva23 : "";
let g_color_p9gr2o23 = color_p9gr2o23 && color_p9gr2o23 != undefined && ( (text_he3eqm23 != "") ) ? color_p9gr2o23 : "";
let g_background_pwg8rf23 = background_pwg8rf23 && background_pwg8rf23 != undefined && ( (text_he3eqm23 != "") ) ? background_pwg8rf23 : "";
let g_border_zu9iqp23 = border_zu9iqp23 && border_zu9iqp23 != undefined && ( (text_he3eqm23 != "") ) ? border_zu9iqp23 : "";
let g_boxshadow_p123fm23 = boxshadow_p123fm23 && boxshadow_p123fm23 != undefined && ( (text_he3eqm23 != "") ) ? boxshadow_p123fm23 : "";
let g_boxshadow_az5c7723 = boxshadow_az5c7723 && boxshadow_az5c7723 != undefined && ( (text_he3eqm23 != "") ) ? boxshadow_az5c7723 : "";
let g_dimension_3k9nvu23 = dimension_3k9nvu23 && dimension_3k9nvu23 != undefined  ? dimension_3k9nvu23 : "";
let g_dimension_m9lynd23 = dimension_m9lynd23 && dimension_m9lynd23 != undefined  ? dimension_m9lynd23 : "";
let g_color_72ry3t25 = color_72ry3t25 && color_72ry3t25 != undefined  ? color_72ry3t25 : "";
let g_background_9rrp6o23 = background_9rrp6o23 && background_9rrp6o23 != undefined  ? background_9rrp6o23 : "";
let g_border_mz61js23 = border_mz61js23 && border_mz61js23 != undefined  ? border_mz61js23 : "";
let g_dimension_afs8c723 = dimension_afs8c723 && dimension_afs8c723 != undefined  ? dimension_afs8c723 : "";
let g_boxshadow_lszi4523 = boxshadow_lszi4523 && boxshadow_lszi4523 != undefined  ? boxshadow_lszi4523 : "";
let g_background_mkuc3u23 = background_mkuc3u23 && background_mkuc3u23 != undefined  ? background_mkuc3u23 : "";
let g_border_th1hpv23 = border_th1hpv23 && border_th1hpv23 != undefined  ? border_th1hpv23 : "";
let g_boxshadow_5jooob23 = boxshadow_5jooob23 && boxshadow_5jooob23 != undefined  ? boxshadow_5jooob23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-ko2nhe24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_ko2nhe24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-card-hvr-main-interact">
    <div class="wkit-card-hvr-inn-content">
        <h2 class="wkit-card-hvr-title" data-ttl="${g_text_8vc69r23}">
            ${g_text_8vc69r23}
        </h2>
         <span class="wkit-card-hvr-desc" data-desc="${g_wysiwyg_i7nxdv23}">
            ${g_wysiwyg_i7nxdv23}
        </span>
        <a class="wkit-card-hvr-main-btn" data-btn="${g_text_he3eqm23}" href="${g_url_l53fwh24_url}" target="${g_url_l53fwh24_target}" rel="${g_url_l53fwh24_nofollow} noopener">
            <button class="wkit-card-hvr-btn">
                ${g_text_he3eqm23}
            </button>
        </a>
    </div>
</div>     `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Card_Hover_Interactions_ko2nhe24();