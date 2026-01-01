
    class Flip_Card_Menu_mc5tke24 {
        constructor() {
            this.Flip_Card_Menu_mc5tke24_bu8jcm25();
        }
    
        Flip_Card_Menu_mc5tke24_bu8jcm25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_IconList,Pmgc_TextArea,Pmgc_Url,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Typography,Pmgc_Color,Pmgc_Range,Pmgc_Background,Pmgc_Border,Pmgc_Dimension,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-mc5tke24', {
        title: __('Flip Card Menu'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-sync-alt tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Create Flip Boxes'),__('Grid Design'),__('Visual Appeal'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_gp3j3123Function = (unit, type) => {
                var g_slider_gp3j3123_list = [];
                g_slider_gp3j3123_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_gp3j3123_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_gp3j3123_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_gp3j3123_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_gp3j3123_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_gp3j3123_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_gp3j3123_list[unit][type];
            };
const slider_zbk6he23Function = (unit, type) => {
                var g_slider_zbk6he23_list = [];
                g_slider_zbk6he23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_zbk6he23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_zbk6he23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_zbk6he23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_zbk6he23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_zbk6he23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_zbk6he23_list[unit][type];
            };
const slider_5ze7ti23Function = (unit, type) => {
                var g_slider_5ze7ti23_list = [];
                g_slider_5ze7ti23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_5ze7ti23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_5ze7ti23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5ze7ti23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5ze7ti23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5ze7ti23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5ze7ti23_list[unit][type];
            };
const slider_5iskqd25Function = (unit, type) => {
                var g_slider_5iskqd25_list = [];
                g_slider_5iskqd25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_5iskqd25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_5iskqd25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5iskqd25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5iskqd25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5iskqd25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5iskqd25_list[unit][type];
            };
const slider_ug3ebm23Function = (unit, type) => {
                var g_slider_ug3ebm23_list = [];
                g_slider_ug3ebm23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ug3ebm23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ug3ebm23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ug3ebm23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ug3ebm23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ug3ebm23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ug3ebm23_list[unit][type];
            };
const slider_h2w3p124Function = (unit, type) => {
                var g_slider_h2w3p124_list = [];
                g_slider_h2w3p124_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_h2w3p124_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_h2w3p124_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_h2w3p124_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_h2w3p124_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_h2w3p124_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_h2w3p124_list[unit][type];
            };
const slider_l8y85s24Function = (unit, type) => {
                var g_slider_l8y85s24_list = [];
                g_slider_l8y85s24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_l8y85s24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_l8y85s24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_l8y85s24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_l8y85s24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_l8y85s24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_l8y85s24_list[unit][type];
            };
const slider_rq269q23Function = (unit, type) => {
                var g_slider_rq269q23_list = [];
                g_slider_rq269q23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_rq269q23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_rq269q23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_rq269q23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_rq269q23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_rq269q23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_rq269q23_list[unit][type];
            };
const slider_i7fvqo24Function = (unit, type) => {
                var g_slider_i7fvqo24_list = [];
                g_slider_i7fvqo24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_i7fvqo24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_i7fvqo24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_i7fvqo24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_i7fvqo24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_i7fvqo24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_i7fvqo24_list[unit][type];
            };
const slider_7y0dec24Function = (unit, type) => {
                var g_slider_7y0dec24_list = [];
                g_slider_7y0dec24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_7y0dec24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_7y0dec24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_7y0dec24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_7y0dec24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_7y0dec24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_7y0dec24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_wp35xx23,
text_i272ou23,
iconscontrol_uopbcl23,
text_ci2kgb23,
textarea_ju37rl23,
text_5tof3823,
iconscontrol_gbrrck23,
url_obff7223,
rawhtml_2wcvxs25,
choose_z7c8ju23,
choose_md0hnd23,
choose_xd331p23,
rawhtml_ssh3jq25,
typography_6zh6rx23,
color_551wnq23,
slider_gp3j3123,
slider_zbk6he23,
slider_5ze7ti23,
color_6i4ih223,
background_um1wcq23,
border_wgzsoc23,
dimension_hnez1623,
boxshadow_9trrak23,
typography_p0cpjw23,
color_do5i2s23,
typography_sq8pc023,
color_ux2d6123,
choose_pms8oi23,
slider_5iskqd25,
slider_ug3ebm23,
color_ttr60d23,
background_ed0z0x23,
border_caib4i23,
dimension_w1w3fv23,
boxshadow_0gcift23,
dimension_ady14f23,
typography_3vfb1423,
color_yjpoy323,
background_kyfa4223,
background_x3t07b23,
border_kyvzfb23,
border_h5dhn123,
dimension_2we1dt23,
boxshadow_w75r6r23,
boxshadow_po2phf23,
normalhover_yrk2d723,
slider_h2w3p124,
slider_l8y85s24,
slider_rq269q23,
slider_i7fvqo24,
slider_7y0dec24,
border_g7e9ws23,
dimension_bmuok723,
boxshadow_2gtthl23,
dimension_gswmr923,
background_0ew14c23,
dimension_3079qb23,
background_2kcf5723,

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
                        main_function_mc5tke24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_mc5tke24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                
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
                
                
                value: select_wp35xx23,
                onChange: (value) => {setAttributes({ select_wp35xx23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Front Side"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_i272ou23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_i272ou23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_uopbcl23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_uopbcl23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Side"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_ci2kgb23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_ci2kgb23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_ju37rl23,
                dynamic: true,
                onChange: (value) => setAttributes({ textarea_ju37rl23: value }),
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_5tof3823,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_5tof3823: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_gbrrck23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_gbrrck23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_obff7223,
                dynamic: [true, 'url_obff7223'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_obff7223: value }),
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_2wcvxs25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/flip-card-menu-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_2wcvxs25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Front Title"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Direction`),
            separator:'default',
            
            options : [{ label: __('Column'), value: 'normal', title: __('Column'), icon: 'fas fa-arrow-down', svg: '' }, 
{ label: __('Column-Reverse'), value: 'column-reverse', title: __('Column-Reverse'), icon: 'fas fa-arrow-up', svg: '' }, 
],
            value: choose_z7c8ju23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_z7c8ju23: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Horizontal`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'flex-start', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'flex-end', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_md0hnd23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_md0hnd23: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Vertical`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_xd331p23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_xd331p23: value }),
            }), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ssh3jq25,
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
For Direction: Column-Reverse vertical alignment also will work in reverse format.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_ssh3jq25: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_6zh6rx23,
            onChange: (value) => setAttributes({ typography_6zh6rx23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_551wnq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_551wnq23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Front Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Margin Top`),
            separator:'default',
            value: slider_gp3j3123,
            
            min: slider_gp3j3123 && slider_gp3j3123.unit ? slider_gp3j3123Function(slider_gp3j3123.unit, 'min') : 0,
            max: slider_gp3j3123 && slider_gp3j3123.unit ? slider_gp3j3123Function(slider_gp3j3123.unit, 'max') : 100,
            step: slider_gp3j3123 && slider_gp3j3123.unit ? slider_gp3j3123Function(slider_gp3j3123.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_gp3j3123: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Margin Bottom`),
            separator:'default',
            value: slider_zbk6he23,
            
            min: slider_zbk6he23 && slider_zbk6he23.unit ? slider_zbk6he23Function(slider_zbk6he23.unit, 'min') : 0,
            max: slider_zbk6he23 && slider_zbk6he23.unit ? slider_zbk6he23Function(slider_zbk6he23.unit, 'max') : 100,
            step: slider_zbk6he23 && slider_zbk6he23.unit ? slider_zbk6he23Function(slider_zbk6he23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_zbk6he23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_5ze7ti23,
            
            min: slider_5ze7ti23 && slider_5ze7ti23.unit ? slider_5ze7ti23Function(slider_5ze7ti23.unit, 'min') : 0,
            max: slider_5ze7ti23 && slider_5ze7ti23.unit ? slider_5ze7ti23Function(slider_5ze7ti23.unit, 'max') : 100,
            step: slider_5ze7ti23 && slider_5ze7ti23.unit ? slider_5ze7ti23Function(slider_5ze7ti23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5ze7ti23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_6i4ih223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_6i4ih223: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_um1wcq23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_um1wcq23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_wgzsoc23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_wgzsoc23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_hnez1623,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_hnez1623: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_9trrak23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_9trrak23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Title"), initialOpen: false },
 React.createElement(Pmgc_Typography, {
            
            value: typography_p0cpjw23,
            onChange: (value) => setAttributes({ typography_p0cpjw23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_do5i2s23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_do5i2s23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Description"), initialOpen: false },
 React.createElement(Pmgc_Typography, {
            
            value: typography_sq8pc023,
            onChange: (value) => setAttributes({ typography_sq8pc023: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ux2d6123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ux2d6123: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Icon"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_pms8oi23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_pms8oi23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_5iskqd25,
            
            min: slider_5iskqd25 && slider_5iskqd25.unit ? slider_5iskqd25Function(slider_5iskqd25.unit, 'min') : 0,
            max: slider_5iskqd25 && slider_5iskqd25.unit ? slider_5iskqd25Function(slider_5iskqd25.unit, 'max') : 100,
            step: slider_5iskqd25 && slider_5iskqd25.unit ? slider_5iskqd25Function(slider_5iskqd25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5iskqd25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_ug3ebm23,
            
            min: slider_ug3ebm23 && slider_ug3ebm23.unit ? slider_ug3ebm23Function(slider_ug3ebm23.unit, 'min') : 0,
            max: slider_ug3ebm23 && slider_ug3ebm23.unit ? slider_ug3ebm23Function(slider_ug3ebm23.unit, 'max') : 100,
            step: slider_ug3ebm23 && slider_ug3ebm23.unit ? slider_ug3ebm23Function(slider_ug3ebm23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ug3ebm23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_ttr60d23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ttr60d23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ed0z0x23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ed0z0x23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_caib4i23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_caib4i23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_w1w3fv23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_w1w3fv23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_0gcift23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_0gcift23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ady14f23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ady14f23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_3vfb1423,
            onChange: (value) => setAttributes({ typography_3vfb1423: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_yjpoy323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yjpoy323: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_kyfa4223,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_kyfa4223: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_kyvzfb23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_kyvzfb23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_2we1dt23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_2we1dt23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_po2phf23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_po2phf23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_x3t07b23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_x3t07b23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_h5dhn123,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_h5dhn123: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_w75r6r23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_w75r6r23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Back Content"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_h2w3p124,
            
            min: slider_h2w3p124 && slider_h2w3p124.unit ? slider_h2w3p124Function(slider_h2w3p124.unit, 'min') : 0,
            max: slider_h2w3p124 && slider_h2w3p124.unit ? slider_h2w3p124Function(slider_h2w3p124.unit, 'max') : 100,
            step: slider_h2w3p124 && slider_h2w3p124.unit ? slider_h2w3p124Function(slider_h2w3p124.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_h2w3p124: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Row Gap`),
            separator:'default',
            value: slider_l8y85s24,
            
            min: slider_l8y85s24 && slider_l8y85s24.unit ? slider_l8y85s24Function(slider_l8y85s24.unit, 'min') : 0,
            max: slider_l8y85s24 && slider_l8y85s24.unit ? slider_l8y85s24Function(slider_l8y85s24.unit, 'max') : 100,
            step: slider_l8y85s24 && slider_l8y85s24.unit ? slider_l8y85s24Function(slider_l8y85s24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_l8y85s24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Card"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Margin`),
            separator:'default',
            value: slider_rq269q23,
            
            min: slider_rq269q23 && slider_rq269q23.unit ? slider_rq269q23Function(slider_rq269q23.unit, 'min') : 0,
            max: slider_rq269q23 && slider_rq269q23.unit ? slider_rq269q23Function(slider_rq269q23.unit, 'max') : 100,
            step: slider_rq269q23 && slider_rq269q23.unit ? slider_rq269q23Function(slider_rq269q23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_rq269q23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_i7fvqo24,
            
            min: slider_i7fvqo24 && slider_i7fvqo24.unit ? slider_i7fvqo24Function(slider_i7fvqo24.unit, 'min') : 0,
            max: slider_i7fvqo24 && slider_i7fvqo24.unit ? slider_i7fvqo24Function(slider_i7fvqo24.unit, 'max') : 100,
            step: slider_i7fvqo24 && slider_i7fvqo24.unit ? slider_i7fvqo24Function(slider_i7fvqo24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_i7fvqo24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_7y0dec24,
            
            min: slider_7y0dec24 && slider_7y0dec24.unit ? slider_7y0dec24Function(slider_7y0dec24.unit, 'min') : 0,
            max: slider_7y0dec24 && slider_7y0dec24.unit ? slider_7y0dec24Function(slider_7y0dec24.unit, 'max') : 100,
            step: slider_7y0dec24 && slider_7y0dec24.unit ? slider_7y0dec24Function(slider_7y0dec24.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_7y0dec24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_g7e9ws23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_g7e9ws23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_bmuok723,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_bmuok723: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_2gtthl23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_2gtthl23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Front Side"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_gswmr923,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_gswmr923: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_0ew14c23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_0ew14c23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Back Side"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_3079qb23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_3079qb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_2kcf5723,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_2kcf5723: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-mc5tke24", block_id, false, props.clientId);
                }
            }

            
let g_select_wp35xx23 = select_wp35xx23 && select_wp35xx23 != undefined  ? select_wp35xx23 : "";
let g_text_i272ou23 = text_i272ou23 && text_i272ou23 != undefined  ? text_i272ou23 : "";
let g_iconscontrol_uopbcl23 = iconscontrol_uopbcl23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_uopbcl23+'"></i></span>' : '';

let g_text_ci2kgb23 = text_ci2kgb23 && text_ci2kgb23 != undefined  ? text_ci2kgb23 : "";
let g_textarea_ju37rl23 = textarea_ju37rl23 && textarea_ju37rl23 != undefined  ? textarea_ju37rl23 : "";
let g_text_5tof3823 = text_5tof3823 && text_5tof3823 != undefined  ? text_5tof3823 : "";
let g_iconscontrol_gbrrck23 = iconscontrol_gbrrck23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_gbrrck23+'"></i></span>' : '';

let g_url_obff7223_url = url_obff7223?.url && url_obff7223?.url != undefined ? url_obff7223.url : "";
let g_url_obff7223_target = url_obff7223?.target && url_obff7223?.target != undefined ? url_obff7223.target : "";
let g_url_obff7223_nofollow = url_obff7223?.nofollow && url_obff7223?.nofollow != undefined ? url_obff7223.nofollow : "";
let g_url_obff7223_ctmArt = url_obff7223?.attr != undefined ? url_obff7223.attr : "";
                    let g_url_obff7223_attr = ''

                    if (g_url_obff7223_ctmArt) {
                        let main_array = g_url_obff7223_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_obff7223_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_2wcvxs25 = rawhtml_2wcvxs25 && rawhtml_2wcvxs25 != undefined  ? rawhtml_2wcvxs25 : "";
let g_choose_z7c8ju23 = choose_z7c8ju23 && choose_z7c8ju23 != undefined  ? choose_z7c8ju23 : "";
let g_choose_md0hnd23 = choose_md0hnd23 && choose_md0hnd23 != undefined  ? choose_md0hnd23 : "";
let g_choose_xd331p23 = choose_xd331p23 && choose_xd331p23 != undefined  ? choose_xd331p23 : "";
let g_rawhtml_ssh3jq25 = rawhtml_ssh3jq25 && rawhtml_ssh3jq25 != undefined  ? rawhtml_ssh3jq25 : "";
let g_typography_6zh6rx23 = typography_6zh6rx23 && typography_6zh6rx23 != undefined  ? typography_6zh6rx23 : "";
let g_color_551wnq23 = color_551wnq23 && color_551wnq23 != undefined  ? color_551wnq23 : "";
let g_color_6i4ih223 = color_6i4ih223 && color_6i4ih223 != undefined  ? color_6i4ih223 : "";
let g_background_um1wcq23 = background_um1wcq23 && background_um1wcq23 != undefined  ? background_um1wcq23 : "";
let g_border_wgzsoc23 = border_wgzsoc23 && border_wgzsoc23 != undefined  ? border_wgzsoc23 : "";
let g_dimension_hnez1623 = dimension_hnez1623 && dimension_hnez1623 != undefined  ? dimension_hnez1623 : "";
let g_boxshadow_9trrak23 = boxshadow_9trrak23 && boxshadow_9trrak23 != undefined  ? boxshadow_9trrak23 : "";
let g_typography_p0cpjw23 = typography_p0cpjw23 && typography_p0cpjw23 != undefined  ? typography_p0cpjw23 : "";
let g_color_do5i2s23 = color_do5i2s23 && color_do5i2s23 != undefined  ? color_do5i2s23 : "";
let g_typography_sq8pc023 = typography_sq8pc023 && typography_sq8pc023 != undefined  ? typography_sq8pc023 : "";
let g_color_ux2d6123 = color_ux2d6123 && color_ux2d6123 != undefined  ? color_ux2d6123 : "";
let g_choose_pms8oi23 = choose_pms8oi23 && choose_pms8oi23 != undefined  ? choose_pms8oi23 : "";
let g_color_ttr60d23 = color_ttr60d23 && color_ttr60d23 != undefined  ? color_ttr60d23 : "";
let g_background_ed0z0x23 = background_ed0z0x23 && background_ed0z0x23 != undefined  ? background_ed0z0x23 : "";
let g_border_caib4i23 = border_caib4i23 && border_caib4i23 != undefined  ? border_caib4i23 : "";
let g_dimension_w1w3fv23 = dimension_w1w3fv23 && dimension_w1w3fv23 != undefined  ? dimension_w1w3fv23 : "";
let g_boxshadow_0gcift23 = boxshadow_0gcift23 && boxshadow_0gcift23 != undefined  ? boxshadow_0gcift23 : "";
let g_dimension_ady14f23 = dimension_ady14f23 && dimension_ady14f23 != undefined  ? dimension_ady14f23 : "";
let g_typography_3vfb1423 = typography_3vfb1423 && typography_3vfb1423 != undefined  ? typography_3vfb1423 : "";
let g_color_yjpoy323 = color_yjpoy323 && color_yjpoy323 != undefined  ? color_yjpoy323 : "";
let g_background_kyfa4223 = background_kyfa4223 && background_kyfa4223 != undefined  ? background_kyfa4223 : "";
let g_background_x3t07b23 = background_x3t07b23 && background_x3t07b23 != undefined  ? background_x3t07b23 : "";
let g_border_kyvzfb23 = border_kyvzfb23 && border_kyvzfb23 != undefined  ? border_kyvzfb23 : "";
let g_border_h5dhn123 = border_h5dhn123 && border_h5dhn123 != undefined  ? border_h5dhn123 : "";
let g_dimension_2we1dt23 = dimension_2we1dt23 && dimension_2we1dt23 != undefined  ? dimension_2we1dt23 : "";
let g_boxshadow_w75r6r23 = boxshadow_w75r6r23 && boxshadow_w75r6r23 != undefined  ? boxshadow_w75r6r23 : "";
let g_boxshadow_po2phf23 = boxshadow_po2phf23 && boxshadow_po2phf23 != undefined  ? boxshadow_po2phf23 : "";
let g_border_g7e9ws23 = border_g7e9ws23 && border_g7e9ws23 != undefined  ? border_g7e9ws23 : "";
let g_dimension_bmuok723 = dimension_bmuok723 && dimension_bmuok723 != undefined  ? dimension_bmuok723 : "";
let g_boxshadow_2gtthl23 = boxshadow_2gtthl23 && boxshadow_2gtthl23 != undefined  ? boxshadow_2gtthl23 : "";
let g_dimension_gswmr923 = dimension_gswmr923 && dimension_gswmr923 != undefined  ? dimension_gswmr923 : "";
let g_background_0ew14c23 = background_0ew14c23 && background_0ew14c23 != undefined  ? background_0ew14c23 : "";
let g_dimension_3079qb23 = dimension_3079qb23 && dimension_3079qb23 != undefined  ? dimension_3079qb23 : "";
let g_background_2kcf5723 = background_2kcf5723 && background_2kcf5723 != undefined  ? background_2kcf5723 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_mc5tke24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-flip-card">
    <div class="wkit-flip-card-inner">
        <div class="wkit-flip-card-main type-${g_select_wp35xx23}">
            <div class="flip-card-frontside wkit-card-1">
                <div class="flip-card-frontside-title">${g_text_i272ou23}</div>
                ${g_iconscontrol_uopbcl23}
                <img class="flip-card-frontside-image" src="">
            </div>
            <div class="flip-card-backside wkit-card-1">
                ${g_iconscontrol_gbrrck23}
                <div class="flip-card-backside-content">
                    <span class="flip-card-title">${g_text_ci2kgb23}</span>
                    <span class="flip-card-desk">${g_textarea_ju37rl23}</span>
                    <a href="${g_url_obff7223_url}" class="flip-card-link" target="${g_url_obff7223_target}" rel="${g_url_obff7223_nofollow} noopener">
                        <span class="flip-card-text">${g_text_5tof3823}</span>
                    </a>
                </div>
            </div>
         </div>  
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
            select_wp35xx23,
text_i272ou23,
iconscontrol_uopbcl23,
text_ci2kgb23,
textarea_ju37rl23,
text_5tof3823,
iconscontrol_gbrrck23,
url_obff7223,
rawhtml_2wcvxs25,
choose_z7c8ju23,
choose_md0hnd23,
choose_xd331p23,
rawhtml_ssh3jq25,
typography_6zh6rx23,
color_551wnq23,
slider_gp3j3123,
slider_zbk6he23,
slider_5ze7ti23,
color_6i4ih223,
background_um1wcq23,
border_wgzsoc23,
dimension_hnez1623,
boxshadow_9trrak23,
typography_p0cpjw23,
color_do5i2s23,
typography_sq8pc023,
color_ux2d6123,
choose_pms8oi23,
slider_5iskqd25,
slider_ug3ebm23,
color_ttr60d23,
background_ed0z0x23,
border_caib4i23,
dimension_w1w3fv23,
boxshadow_0gcift23,
dimension_ady14f23,
typography_3vfb1423,
color_yjpoy323,
background_kyfa4223,
background_x3t07b23,
border_kyvzfb23,
border_h5dhn123,
dimension_2we1dt23,
boxshadow_w75r6r23,
boxshadow_po2phf23,
normalhover_yrk2d723,
slider_h2w3p124,
slider_l8y85s24,
slider_rq269q23,
slider_i7fvqo24,
slider_7y0dec24,
border_g7e9ws23,
dimension_bmuok723,
boxshadow_2gtthl23,
dimension_gswmr923,
background_0ew14c23,
dimension_3079qb23,
background_2kcf5723,

            block_id,
        } = attributes;

        

        

        

        
let g_select_wp35xx23 = select_wp35xx23 && select_wp35xx23 != undefined  ? select_wp35xx23 : "";
let g_text_i272ou23 = text_i272ou23 && text_i272ou23 != undefined  ? text_i272ou23 : "";
let g_iconscontrol_uopbcl23 = iconscontrol_uopbcl23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_uopbcl23+'"></i></span>' : '';

let g_text_ci2kgb23 = text_ci2kgb23 && text_ci2kgb23 != undefined  ? text_ci2kgb23 : "";
let g_textarea_ju37rl23 = textarea_ju37rl23 && textarea_ju37rl23 != undefined  ? textarea_ju37rl23 : "";
let g_text_5tof3823 = text_5tof3823 && text_5tof3823 != undefined  ? text_5tof3823 : "";
let g_iconscontrol_gbrrck23 = iconscontrol_gbrrck23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_gbrrck23+'"></i></span>' : '';

let g_url_obff7223_url = url_obff7223?.url && url_obff7223?.url != undefined ? url_obff7223.url : "";
let g_url_obff7223_target = url_obff7223?.target && url_obff7223?.target != undefined ? url_obff7223.target : "";
let g_url_obff7223_nofollow = url_obff7223?.nofollow && url_obff7223?.nofollow != undefined ? url_obff7223.nofollow : "";
let g_url_obff7223_ctmArt = url_obff7223?.attr != undefined ? url_obff7223.attr : "";
                    let g_url_obff7223_attr = ''

                    if (g_url_obff7223_ctmArt) {
                        let main_array = g_url_obff7223_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_obff7223_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_2wcvxs25 = rawhtml_2wcvxs25 && rawhtml_2wcvxs25 != undefined  ? rawhtml_2wcvxs25 : "";
let g_choose_z7c8ju23 = choose_z7c8ju23 && choose_z7c8ju23 != undefined  ? choose_z7c8ju23 : "";
let g_choose_md0hnd23 = choose_md0hnd23 && choose_md0hnd23 != undefined  ? choose_md0hnd23 : "";
let g_choose_xd331p23 = choose_xd331p23 && choose_xd331p23 != undefined  ? choose_xd331p23 : "";
let g_rawhtml_ssh3jq25 = rawhtml_ssh3jq25 && rawhtml_ssh3jq25 != undefined  ? rawhtml_ssh3jq25 : "";
let g_typography_6zh6rx23 = typography_6zh6rx23 && typography_6zh6rx23 != undefined  ? typography_6zh6rx23 : "";
let g_color_551wnq23 = color_551wnq23 && color_551wnq23 != undefined  ? color_551wnq23 : "";
let g_color_6i4ih223 = color_6i4ih223 && color_6i4ih223 != undefined  ? color_6i4ih223 : "";
let g_background_um1wcq23 = background_um1wcq23 && background_um1wcq23 != undefined  ? background_um1wcq23 : "";
let g_border_wgzsoc23 = border_wgzsoc23 && border_wgzsoc23 != undefined  ? border_wgzsoc23 : "";
let g_dimension_hnez1623 = dimension_hnez1623 && dimension_hnez1623 != undefined  ? dimension_hnez1623 : "";
let g_boxshadow_9trrak23 = boxshadow_9trrak23 && boxshadow_9trrak23 != undefined  ? boxshadow_9trrak23 : "";
let g_typography_p0cpjw23 = typography_p0cpjw23 && typography_p0cpjw23 != undefined  ? typography_p0cpjw23 : "";
let g_color_do5i2s23 = color_do5i2s23 && color_do5i2s23 != undefined  ? color_do5i2s23 : "";
let g_typography_sq8pc023 = typography_sq8pc023 && typography_sq8pc023 != undefined  ? typography_sq8pc023 : "";
let g_color_ux2d6123 = color_ux2d6123 && color_ux2d6123 != undefined  ? color_ux2d6123 : "";
let g_choose_pms8oi23 = choose_pms8oi23 && choose_pms8oi23 != undefined  ? choose_pms8oi23 : "";
let g_color_ttr60d23 = color_ttr60d23 && color_ttr60d23 != undefined  ? color_ttr60d23 : "";
let g_background_ed0z0x23 = background_ed0z0x23 && background_ed0z0x23 != undefined  ? background_ed0z0x23 : "";
let g_border_caib4i23 = border_caib4i23 && border_caib4i23 != undefined  ? border_caib4i23 : "";
let g_dimension_w1w3fv23 = dimension_w1w3fv23 && dimension_w1w3fv23 != undefined  ? dimension_w1w3fv23 : "";
let g_boxshadow_0gcift23 = boxshadow_0gcift23 && boxshadow_0gcift23 != undefined  ? boxshadow_0gcift23 : "";
let g_dimension_ady14f23 = dimension_ady14f23 && dimension_ady14f23 != undefined  ? dimension_ady14f23 : "";
let g_typography_3vfb1423 = typography_3vfb1423 && typography_3vfb1423 != undefined  ? typography_3vfb1423 : "";
let g_color_yjpoy323 = color_yjpoy323 && color_yjpoy323 != undefined  ? color_yjpoy323 : "";
let g_background_kyfa4223 = background_kyfa4223 && background_kyfa4223 != undefined  ? background_kyfa4223 : "";
let g_background_x3t07b23 = background_x3t07b23 && background_x3t07b23 != undefined  ? background_x3t07b23 : "";
let g_border_kyvzfb23 = border_kyvzfb23 && border_kyvzfb23 != undefined  ? border_kyvzfb23 : "";
let g_border_h5dhn123 = border_h5dhn123 && border_h5dhn123 != undefined  ? border_h5dhn123 : "";
let g_dimension_2we1dt23 = dimension_2we1dt23 && dimension_2we1dt23 != undefined  ? dimension_2we1dt23 : "";
let g_boxshadow_w75r6r23 = boxshadow_w75r6r23 && boxshadow_w75r6r23 != undefined  ? boxshadow_w75r6r23 : "";
let g_boxshadow_po2phf23 = boxshadow_po2phf23 && boxshadow_po2phf23 != undefined  ? boxshadow_po2phf23 : "";
let g_border_g7e9ws23 = border_g7e9ws23 && border_g7e9ws23 != undefined  ? border_g7e9ws23 : "";
let g_dimension_bmuok723 = dimension_bmuok723 && dimension_bmuok723 != undefined  ? dimension_bmuok723 : "";
let g_boxshadow_2gtthl23 = boxshadow_2gtthl23 && boxshadow_2gtthl23 != undefined  ? boxshadow_2gtthl23 : "";
let g_dimension_gswmr923 = dimension_gswmr923 && dimension_gswmr923 != undefined  ? dimension_gswmr923 : "";
let g_background_0ew14c23 = background_0ew14c23 && background_0ew14c23 != undefined  ? background_0ew14c23 : "";
let g_dimension_3079qb23 = dimension_3079qb23 && dimension_3079qb23 != undefined  ? dimension_3079qb23 : "";
let g_background_2kcf5723 = background_2kcf5723 && background_2kcf5723 != undefined  ? background_2kcf5723 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-mc5tke24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_mc5tke24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-flip-card">
    <div class="wkit-flip-card-inner">
        <div class="wkit-flip-card-main type-${g_select_wp35xx23}">
            <div class="flip-card-frontside wkit-card-1">
                <div class="flip-card-frontside-title">${g_text_i272ou23}</div>
                ${g_iconscontrol_uopbcl23}
                <img class="flip-card-frontside-image" src="">
            </div>
            <div class="flip-card-backside wkit-card-1">
                ${g_iconscontrol_gbrrck23}
                <div class="flip-card-backside-content">
                    <span class="flip-card-title">${g_text_ci2kgb23}</span>
                    <span class="flip-card-desk">${g_textarea_ju37rl23}</span>
                    <a href="${g_url_obff7223_url}" class="flip-card-link" target="${g_url_obff7223_target}" rel="${g_url_obff7223_nofollow} noopener">
                        <span class="flip-card-text">${g_text_5tof3823}</span>
                    </a>
                </div>
            </div>
         </div>  
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
    new Flip_Card_Menu_mc5tke24();