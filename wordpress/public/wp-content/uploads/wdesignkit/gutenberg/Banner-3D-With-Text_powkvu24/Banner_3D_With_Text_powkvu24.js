
    class Banner_3D_With_Text_powkvu24 {
        constructor() {
            this.Banner_3D_With_Text_powkvu24_6gvyvq25();
        }
    
        Banner_3D_With_Text_powkvu24_6gvyvq25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Media,Pmgc_IconList,Pmgc_Url,Pmgc_Note,Pmgc_Range,Pmgc_Typography,Pmgc_Color,Pmgc_BoxShadow,Pmgc_Dimension,Pmgc_Background,Pmgc_Border,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-powkvu24', {
        title: __('Banner 3D With Text'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-money-bill-wave-alt tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('3D Banner'),__('Immersive Design'),__('Interactive Text'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_shzvad23Function = (unit, type) => {
                var g_slider_shzvad23_list = [];
                g_slider_shzvad23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_shzvad23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_shzvad23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_shzvad23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_shzvad23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_shzvad23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_shzvad23_list[unit][type];
            };
const slider_a2x1hz23Function = (unit, type) => {
                var g_slider_a2x1hz23_list = [];
                g_slider_a2x1hz23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_a2x1hz23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_a2x1hz23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_a2x1hz23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_a2x1hz23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_a2x1hz23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_a2x1hz23_list[unit][type];
            };
const slider_x2xu0w23Function = (unit, type) => {
                var g_slider_x2xu0w23_list = [];
                g_slider_x2xu0w23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_x2xu0w23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_x2xu0w23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_x2xu0w23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_x2xu0w23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_x2xu0w23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_x2xu0w23_list[unit][type];
            };
const slider_i2ibqa23Function = (unit, type) => {
                var g_slider_i2ibqa23_list = [];
                g_slider_i2ibqa23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_i2ibqa23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_i2ibqa23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_i2ibqa23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_i2ibqa23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_i2ibqa23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_i2ibqa23_list[unit][type];
            };
const slider_x0klcn24Function = (unit, type) => {
                var g_slider_x0klcn24_list = [];
                g_slider_x0klcn24_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_x0klcn24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_x0klcn24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_x0klcn24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_x0klcn24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_x0klcn24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_x0klcn24_list[unit][type];
            };
const slider_dxt92y25Function = (unit, type) => {
                var g_slider_dxt92y25_list = [];
                g_slider_dxt92y25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_dxt92y25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_dxt92y25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_dxt92y25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_dxt92y25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_dxt92y25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_dxt92y25_list[unit][type];
            };
const slider_l1oaun23Function = (unit, type) => {
                var g_slider_l1oaun23_list = [];
                g_slider_l1oaun23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_l1oaun23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_l1oaun23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_l1oaun23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_l1oaun23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_l1oaun23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_l1oaun23_list[unit][type];
            };
const slider_i87qmm23Function = (unit, type) => {
                var g_slider_i87qmm23_list = [];
                g_slider_i87qmm23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_i87qmm23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_i87qmm23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_i87qmm23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_i87qmm23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_i87qmm23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_i87qmm23_list[unit][type];
            };
const slider_jokpyw23Function = (unit, type) => {
                var g_slider_jokpyw23_list = [];
                g_slider_jokpyw23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_jokpyw23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_jokpyw23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_jokpyw23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_jokpyw23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_jokpyw23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_jokpyw23_list[unit][type];
            };
const slider_724x6123Function = (unit, type) => {
                var g_slider_724x6123_list = [];
                g_slider_724x6123_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_724x6123_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_724x6123_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_724x6123_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_724x6123_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_724x6123_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_724x6123_list[unit][type];
            };
const slider_pkwmqc23Function = (unit, type) => {
                var g_slider_pkwmqc23_list = [];
                g_slider_pkwmqc23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_pkwmqc23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_pkwmqc23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_pkwmqc23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_pkwmqc23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_pkwmqc23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_pkwmqc23_list[unit][type];
            };
const slider_taxx7t23Function = (unit, type) => {
                var g_slider_taxx7t23_list = [];
                g_slider_taxx7t23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_taxx7t23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_taxx7t23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_taxx7t23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_taxx7t23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_taxx7t23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_taxx7t23_list[unit][type];
            };
const slider_6kca7623Function = (unit, type) => {
                var g_slider_6kca7623_list = [];
                g_slider_6kca7623_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_6kca7623_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_6kca7623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_6kca7623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6kca7623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6kca7623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6kca7623_list[unit][type];
            };
const slider_g6v4ta23Function = (unit, type) => {
                var g_slider_g6v4ta23_list = [];
                g_slider_g6v4ta23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_g6v4ta23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_g6v4ta23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_g6v4ta23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_g6v4ta23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_g6v4ta23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_g6v4ta23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_2lohav23,
media_d8fqyk23,
text_owngik23,
iconscontrol_7p9n2e23,
text_rf6gqh23,
iconscontrol_a76ucn23,
text_ibsykc23,
iconscontrol_hgr83i23,
url_34bkrv23,
rawhtml_xf3ewd25,
slider_shzvad23,
slider_a2x1hz23,
typography_0kwrj523,
color_vrwl3y23,
textshadow_28i3rq25,
dimension_a9s3lw23,
slider_x2xu0w23,
slider_i2ibqa23,
slider_x0klcn24,
typography_u90bcr23,
color_zi1nod23,
background_0scwvp23,
border_owgmr523,
dimension_jdjlld23,
boxshadow_py2c4123,
slider_dxt92y25,
slider_l1oaun23,
color_2lsa3223,
slider_i87qmm23,
slider_jokpyw23,
typography_u7mycy23,
color_qfshax23,
color_en7opi23,
background_ah7cuv23,
border_u0i0l323,
background_ht728f23,
border_l3fepp23,
boxshadow_09uoe123,
boxshadow_6gqgma23,
normalhover_o6xqhi23,
slider_724x6123,
slider_pkwmqc23,
slider_taxx7t23,
color_9f0ijv23,
color_4zpfye23,
normalhover_sir57825,
slider_6kca7623,
slider_g6v4ta23,
background_4q0qow23,
border_qepes223,
dimension_qyjrez23,

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
                        main_function_powkvu24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_powkvu24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                 VanillaTilt.init($scope[0].querySelectorAll(".banner-3d-inner"));

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_2lohav23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_2lohav23: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_d8fqyk23,
                dynamic: [true, 'media_d8fqyk23'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_d8fqyk23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Subtitle"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Subtitle Text 1`),
                type: "text",
                value: text_owngik23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_owngik23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Subtitle Icon 1 `),
            
            value: iconscontrol_7p9n2e23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_7p9n2e23: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Subtitle Text 2`),
                type: "text",
                value: text_rf6gqh23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_rf6gqh23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Subtitle Icon 2 `),
            
            value: iconscontrol_a76ucn23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_a76ucn23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_ibsykc23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_ibsykc23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_hgr83i23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_hgr83i23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_34bkrv23,
                dynamic: [true, 'url_34bkrv23'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_34bkrv23: value }),
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_xf3ewd25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/banner-3d-with-text-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_xf3ewd25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                           ( text_2lohav23 != "" ) && React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
( text_2lohav23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_shzvad23,
            
            min: slider_shzvad23 && slider_shzvad23.unit ? slider_shzvad23Function(slider_shzvad23.unit, 'min') : 0,
            max: slider_shzvad23 && slider_shzvad23.unit ? slider_shzvad23Function(slider_shzvad23.unit, 'max') : 100,
            step: slider_shzvad23 && slider_shzvad23.unit ? slider_shzvad23Function(slider_shzvad23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_shzvad23: value }),
            }), 
( text_2lohav23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_a2x1hz23,
            
            min: slider_a2x1hz23 && slider_a2x1hz23.unit ? slider_a2x1hz23Function(slider_a2x1hz23.unit, 'min') : 0,
            max: slider_a2x1hz23 && slider_a2x1hz23.unit ? slider_a2x1hz23Function(slider_a2x1hz23.unit, 'max') : 100,
            step: slider_a2x1hz23 && slider_a2x1hz23.unit ? slider_a2x1hz23Function(slider_a2x1hz23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_a2x1hz23: value }),
            }), 
( text_2lohav23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_0kwrj523,
            onChange: (value) => setAttributes({ typography_0kwrj523: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( text_2lohav23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_vrwl3y23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vrwl3y23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_28i3rq25,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_28i3rq25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Subtitle"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_a9s3lw23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_a9s3lw23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( text_owngik23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_x2xu0w23,
            
            min: slider_x2xu0w23 && slider_x2xu0w23.unit ? slider_x2xu0w23Function(slider_x2xu0w23.unit, 'min') : 0,
            max: slider_x2xu0w23 && slider_x2xu0w23.unit ? slider_x2xu0w23Function(slider_x2xu0w23.unit, 'max') : 100,
            step: slider_x2xu0w23 && slider_x2xu0w23.unit ? slider_x2xu0w23Function(slider_x2xu0w23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_x2xu0w23: value }),
            }), 
( text_owngik23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_i2ibqa23,
            
            min: slider_i2ibqa23 && slider_i2ibqa23.unit ? slider_i2ibqa23Function(slider_i2ibqa23.unit, 'min') : 0,
            max: slider_i2ibqa23 && slider_i2ibqa23.unit ? slider_i2ibqa23Function(slider_i2ibqa23.unit, 'max') : 100,
            step: slider_i2ibqa23 && slider_i2ibqa23.unit ? slider_i2ibqa23Function(slider_i2ibqa23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_i2ibqa23: value }),
            }), 
( text_owngik23 != "" ) && React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_x0klcn24,
            
            min: slider_x0klcn24 && slider_x0klcn24.unit ? slider_x0klcn24Function(slider_x0klcn24.unit, 'min') : 0,
            max: slider_x0klcn24 && slider_x0klcn24.unit ? slider_x0klcn24Function(slider_x0klcn24.unit, 'max') : 100,
            step: slider_x0klcn24 && slider_x0klcn24.unit ? slider_x0klcn24Function(slider_x0klcn24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_x0klcn24: value }),
            }), 
( text_owngik23 != "" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_u90bcr23,
            onChange: (value) => setAttributes({ typography_u90bcr23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( text_owngik23 != "" ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_zi1nod23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_zi1nod23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_0scwvp23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_0scwvp23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_owgmr523,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_owgmr523: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_jdjlld23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jdjlld23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_py2c4123,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_py2c4123: value }),
            }), 
), React.createElement(PanelBody, { title: __("Subtitle Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_dxt92y25,
            
            min: slider_dxt92y25 && slider_dxt92y25.unit ? slider_dxt92y25Function(slider_dxt92y25.unit, 'min') : 0,
            max: slider_dxt92y25 && slider_dxt92y25.unit ? slider_dxt92y25Function(slider_dxt92y25.unit, 'max') : 100,
            step: slider_dxt92y25 && slider_dxt92y25.unit ? slider_dxt92y25Function(slider_dxt92y25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_dxt92y25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_l1oaun23,
            
            min: slider_l1oaun23 && slider_l1oaun23.unit ? slider_l1oaun23Function(slider_l1oaun23.unit, 'min') : 0,
            max: slider_l1oaun23 && slider_l1oaun23.unit ? slider_l1oaun23Function(slider_l1oaun23.unit, 'max') : 100,
            step: slider_l1oaun23 && slider_l1oaun23.unit ? slider_l1oaun23Function(slider_l1oaun23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_l1oaun23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_2lsa3223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_2lsa3223: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_i87qmm23,
            
            min: slider_i87qmm23 && slider_i87qmm23.unit ? slider_i87qmm23Function(slider_i87qmm23.unit, 'min') : 0,
            max: slider_i87qmm23 && slider_i87qmm23.unit ? slider_i87qmm23Function(slider_i87qmm23.unit, 'max') : 100,
            step: slider_i87qmm23 && slider_i87qmm23.unit ? slider_i87qmm23Function(slider_i87qmm23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_i87qmm23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_jokpyw23,
            
            min: slider_jokpyw23 && slider_jokpyw23.unit ? slider_jokpyw23Function(slider_jokpyw23.unit, 'min') : 0,
            max: slider_jokpyw23 && slider_jokpyw23.unit ? slider_jokpyw23Function(slider_jokpyw23.unit, 'max') : 100,
            step: slider_jokpyw23 && slider_jokpyw23.unit ? slider_jokpyw23Function(slider_jokpyw23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_jokpyw23: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_u7mycy23,
            onChange: (value) => setAttributes({ typography_u7mycy23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_qfshax23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qfshax23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ht728f23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ht728f23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_l3fepp23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_l3fepp23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_6gqgma23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_6gqgma23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_en7opi23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_en7opi23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ah7cuv23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ah7cuv23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_u0i0l323,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_u0i0l323: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_09uoe123,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_09uoe123: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Button Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Width`),
            separator:'default',
            value: slider_724x6123,
            
            min: slider_724x6123 && slider_724x6123.unit ? slider_724x6123Function(slider_724x6123.unit, 'min') : 0,
            max: slider_724x6123 && slider_724x6123.unit ? slider_724x6123Function(slider_724x6123.unit, 'max') : 100,
            step: slider_724x6123 && slider_724x6123.unit ? slider_724x6123Function(slider_724x6123.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_724x6123: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Height`),
            separator:'default',
            value: slider_pkwmqc23,
            
            min: slider_pkwmqc23 && slider_pkwmqc23.unit ? slider_pkwmqc23Function(slider_pkwmqc23.unit, 'min') : 0,
            max: slider_pkwmqc23 && slider_pkwmqc23.unit ? slider_pkwmqc23Function(slider_pkwmqc23.unit, 'max') : 100,
            step: slider_pkwmqc23 && slider_pkwmqc23.unit ? slider_pkwmqc23Function(slider_pkwmqc23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_pkwmqc23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_taxx7t23,
            
            min: slider_taxx7t23 && slider_taxx7t23.unit ? slider_taxx7t23Function(slider_taxx7t23.unit, 'min') : 0,
            max: slider_taxx7t23 && slider_taxx7t23.unit ? slider_taxx7t23Function(slider_taxx7t23.unit, 'max') : 100,
            step: slider_taxx7t23 && slider_taxx7t23.unit ? slider_taxx7t23Function(slider_taxx7t23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_taxx7t23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_9f0ijv23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9f0ijv23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_4zpfye23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_4zpfye23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_6kca7623,
            
            min: slider_6kca7623 && slider_6kca7623.unit ? slider_6kca7623Function(slider_6kca7623.unit, 'min') : 0,
            max: slider_6kca7623 && slider_6kca7623.unit ? slider_6kca7623Function(slider_6kca7623.unit, 'max') : 100,
            step: slider_6kca7623 && slider_6kca7623.unit ? slider_6kca7623Function(slider_6kca7623.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6kca7623: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_g6v4ta23,
            
            min: slider_g6v4ta23 && slider_g6v4ta23.unit ? slider_g6v4ta23Function(slider_g6v4ta23.unit, 'min') : 0,
            max: slider_g6v4ta23 && slider_g6v4ta23.unit ? slider_g6v4ta23Function(slider_g6v4ta23.unit, 'max') : 100,
            step: slider_g6v4ta23 && slider_g6v4ta23.unit ? slider_g6v4ta23Function(slider_g6v4ta23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_g6v4ta23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_4q0qow23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_4q0qow23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_qepes223,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_qepes223: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_qyjrez23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_qyjrez23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-powkvu24", block_id, false, props.clientId);
                }
            }

            
let g_text_2lohav23 = text_2lohav23 && text_2lohav23 != undefined  ? text_2lohav23 : "";
let g_media_d8fqyk23 = media_d8fqyk23 && media_d8fqyk23.url && media_d8fqyk23.url != undefined  ? media_d8fqyk23.url : "";
let g_text_owngik23 = text_owngik23 && text_owngik23 != undefined  ? text_owngik23 : "";
let g_iconscontrol_7p9n2e23 = iconscontrol_7p9n2e23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_7p9n2e23+'"></i></span>' : '';

let g_text_rf6gqh23 = text_rf6gqh23 && text_rf6gqh23 != undefined  ? text_rf6gqh23 : "";
let g_iconscontrol_a76ucn23 = iconscontrol_a76ucn23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_a76ucn23+'"></i></span>' : '';

let g_text_ibsykc23 = text_ibsykc23 && text_ibsykc23 != undefined  ? text_ibsykc23 : "";
let g_iconscontrol_hgr83i23 = iconscontrol_hgr83i23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_hgr83i23+'"></i></span>' : '';

let g_url_34bkrv23_url = url_34bkrv23?.url && url_34bkrv23?.url != undefined ? url_34bkrv23.url : "";
let g_url_34bkrv23_target = url_34bkrv23?.target && url_34bkrv23?.target != undefined ? url_34bkrv23.target : "";
let g_url_34bkrv23_nofollow = url_34bkrv23?.nofollow && url_34bkrv23?.nofollow != undefined ? url_34bkrv23.nofollow : "";
let g_url_34bkrv23_ctmArt = url_34bkrv23?.attr != undefined ? url_34bkrv23.attr : "";
                    let g_url_34bkrv23_attr = ''

                    if (g_url_34bkrv23_ctmArt) {
                        let main_array = g_url_34bkrv23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_34bkrv23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_xf3ewd25 = rawhtml_xf3ewd25 && rawhtml_xf3ewd25 != undefined  ? rawhtml_xf3ewd25 : "";
let g_typography_0kwrj523 = typography_0kwrj523 && typography_0kwrj523 != undefined && ( (text_2lohav23 != "") ) ? typography_0kwrj523 : "";
let g_color_vrwl3y23 = color_vrwl3y23 && color_vrwl3y23 != undefined && ( (text_2lohav23 != "") ) ? color_vrwl3y23 : "";
let g_textshadow_28i3rq25 = textshadow_28i3rq25 && textshadow_28i3rq25 != undefined  ? textshadow_28i3rq25 : "";
let g_dimension_a9s3lw23 = dimension_a9s3lw23 && dimension_a9s3lw23 != undefined  ? dimension_a9s3lw23 : "";
let g_typography_u90bcr23 = typography_u90bcr23 && typography_u90bcr23 != undefined && ( (text_owngik23 != "") ) ? typography_u90bcr23 : "";
let g_color_zi1nod23 = color_zi1nod23 && color_zi1nod23 != undefined && ( (text_owngik23 != "") ) ? color_zi1nod23 : "";
let g_background_0scwvp23 = background_0scwvp23 && background_0scwvp23 != undefined  ? background_0scwvp23 : "";
let g_border_owgmr523 = border_owgmr523 && border_owgmr523 != undefined  ? border_owgmr523 : "";
let g_dimension_jdjlld23 = dimension_jdjlld23 && dimension_jdjlld23 != undefined  ? dimension_jdjlld23 : "";
let g_boxshadow_py2c4123 = boxshadow_py2c4123 && boxshadow_py2c4123 != undefined  ? boxshadow_py2c4123 : "";
let g_color_2lsa3223 = color_2lsa3223 && color_2lsa3223 != undefined  ? color_2lsa3223 : "";
let g_typography_u7mycy23 = typography_u7mycy23 && typography_u7mycy23 != undefined  ? typography_u7mycy23 : "";
let g_color_qfshax23 = color_qfshax23 && color_qfshax23 != undefined  ? color_qfshax23 : "";
let g_color_en7opi23 = color_en7opi23 && color_en7opi23 != undefined  ? color_en7opi23 : "";
let g_background_ah7cuv23 = background_ah7cuv23 && background_ah7cuv23 != undefined  ? background_ah7cuv23 : "";
let g_border_u0i0l323 = border_u0i0l323 && border_u0i0l323 != undefined  ? border_u0i0l323 : "";
let g_background_ht728f23 = background_ht728f23 && background_ht728f23 != undefined  ? background_ht728f23 : "";
let g_border_l3fepp23 = border_l3fepp23 && border_l3fepp23 != undefined  ? border_l3fepp23 : "";
let g_boxshadow_09uoe123 = boxshadow_09uoe123 && boxshadow_09uoe123 != undefined  ? boxshadow_09uoe123 : "";
let g_boxshadow_6gqgma23 = boxshadow_6gqgma23 && boxshadow_6gqgma23 != undefined  ? boxshadow_6gqgma23 : "";
let g_color_9f0ijv23 = color_9f0ijv23 && color_9f0ijv23 != undefined  ? color_9f0ijv23 : "";
let g_color_4zpfye23 = color_4zpfye23 && color_4zpfye23 != undefined  ? color_4zpfye23 : "";
let g_background_4q0qow23 = background_4q0qow23 && background_4q0qow23 != undefined  ? background_4q0qow23 : "";
let g_border_qepes223 = border_qepes223 && border_qepes223 != undefined  ? border_qepes223 : "";
let g_dimension_qyjrez23 = dimension_qyjrez23 && dimension_qyjrez23 != undefined  ? dimension_qyjrez23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_powkvu24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-banner-3d-with-text">
    <div class="banner-3d-inner">
        <div class="banner-3d-inner-heading">
            <div class="banner-3d-inner-heading-inner">
                <p class="banner-3d-inner-heading-inner-text">${g_text_2lohav23}</p>
                <div class="banner-3d-inner-heading-content">
                    ${g_iconscontrol_7p9n2e23}
                    <span class="banner-3d-inner-heading-subtitle">${g_text_owngik23}</span>
                    ${g_iconscontrol_a76ucn23}
                    <span class="banner-3d-inner-heading-subtitle">${g_text_rf6gqh23}</span>
                </div>
            </div>
        </div>  
         <div class="banner-3d-inner-media">
                <div class="banner-3d-inner-media-inner">
                    <div class="banner-3d-inner-media-inner-image" style="background-image:url(${g_media_d8fqyk23})"></div>
                </div>
         </div>
         <div class="banner-3d-inner-btn">
            <a href="${g_url_34bkrv23_url}" class="btn-banner" target="${g_url_34bkrv23_target}" rel="${g_url_34bkrv23_nofollow} noopener">
                ${g_iconscontrol_hgr83i23}
                <span class="banner-3d-inner-btn-text">${g_text_ibsykc23}</span>
            </a>
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
            text_2lohav23,
media_d8fqyk23,
text_owngik23,
iconscontrol_7p9n2e23,
text_rf6gqh23,
iconscontrol_a76ucn23,
text_ibsykc23,
iconscontrol_hgr83i23,
url_34bkrv23,
rawhtml_xf3ewd25,
slider_shzvad23,
slider_a2x1hz23,
typography_0kwrj523,
color_vrwl3y23,
textshadow_28i3rq25,
dimension_a9s3lw23,
slider_x2xu0w23,
slider_i2ibqa23,
slider_x0klcn24,
typography_u90bcr23,
color_zi1nod23,
background_0scwvp23,
border_owgmr523,
dimension_jdjlld23,
boxshadow_py2c4123,
slider_dxt92y25,
slider_l1oaun23,
color_2lsa3223,
slider_i87qmm23,
slider_jokpyw23,
typography_u7mycy23,
color_qfshax23,
color_en7opi23,
background_ah7cuv23,
border_u0i0l323,
background_ht728f23,
border_l3fepp23,
boxshadow_09uoe123,
boxshadow_6gqgma23,
normalhover_o6xqhi23,
slider_724x6123,
slider_pkwmqc23,
slider_taxx7t23,
color_9f0ijv23,
color_4zpfye23,
normalhover_sir57825,
slider_6kca7623,
slider_g6v4ta23,
background_4q0qow23,
border_qepes223,
dimension_qyjrez23,

            block_id,
        } = attributes;

        

        

        

        
let g_text_2lohav23 = text_2lohav23 && text_2lohav23 != undefined  ? text_2lohav23 : "";
let g_media_d8fqyk23 = media_d8fqyk23 && media_d8fqyk23.url && media_d8fqyk23.url != undefined  ? media_d8fqyk23.url : "";
let g_text_owngik23 = text_owngik23 && text_owngik23 != undefined  ? text_owngik23 : "";
let g_iconscontrol_7p9n2e23 = iconscontrol_7p9n2e23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_7p9n2e23+'"></i></span>' : '';

let g_text_rf6gqh23 = text_rf6gqh23 && text_rf6gqh23 != undefined  ? text_rf6gqh23 : "";
let g_iconscontrol_a76ucn23 = iconscontrol_a76ucn23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_a76ucn23+'"></i></span>' : '';

let g_text_ibsykc23 = text_ibsykc23 && text_ibsykc23 != undefined  ? text_ibsykc23 : "";
let g_iconscontrol_hgr83i23 = iconscontrol_hgr83i23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_hgr83i23+'"></i></span>' : '';

let g_url_34bkrv23_url = url_34bkrv23?.url && url_34bkrv23?.url != undefined ? url_34bkrv23.url : "";
let g_url_34bkrv23_target = url_34bkrv23?.target && url_34bkrv23?.target != undefined ? url_34bkrv23.target : "";
let g_url_34bkrv23_nofollow = url_34bkrv23?.nofollow && url_34bkrv23?.nofollow != undefined ? url_34bkrv23.nofollow : "";
let g_url_34bkrv23_ctmArt = url_34bkrv23?.attr != undefined ? url_34bkrv23.attr : "";
                    let g_url_34bkrv23_attr = ''

                    if (g_url_34bkrv23_ctmArt) {
                        let main_array = g_url_34bkrv23_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_34bkrv23_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_rawhtml_xf3ewd25 = rawhtml_xf3ewd25 && rawhtml_xf3ewd25 != undefined  ? rawhtml_xf3ewd25 : "";
let g_typography_0kwrj523 = typography_0kwrj523 && typography_0kwrj523 != undefined && ( (text_2lohav23 != "") ) ? typography_0kwrj523 : "";
let g_color_vrwl3y23 = color_vrwl3y23 && color_vrwl3y23 != undefined && ( (text_2lohav23 != "") ) ? color_vrwl3y23 : "";
let g_textshadow_28i3rq25 = textshadow_28i3rq25 && textshadow_28i3rq25 != undefined  ? textshadow_28i3rq25 : "";
let g_dimension_a9s3lw23 = dimension_a9s3lw23 && dimension_a9s3lw23 != undefined  ? dimension_a9s3lw23 : "";
let g_typography_u90bcr23 = typography_u90bcr23 && typography_u90bcr23 != undefined && ( (text_owngik23 != "") ) ? typography_u90bcr23 : "";
let g_color_zi1nod23 = color_zi1nod23 && color_zi1nod23 != undefined && ( (text_owngik23 != "") ) ? color_zi1nod23 : "";
let g_background_0scwvp23 = background_0scwvp23 && background_0scwvp23 != undefined  ? background_0scwvp23 : "";
let g_border_owgmr523 = border_owgmr523 && border_owgmr523 != undefined  ? border_owgmr523 : "";
let g_dimension_jdjlld23 = dimension_jdjlld23 && dimension_jdjlld23 != undefined  ? dimension_jdjlld23 : "";
let g_boxshadow_py2c4123 = boxshadow_py2c4123 && boxshadow_py2c4123 != undefined  ? boxshadow_py2c4123 : "";
let g_color_2lsa3223 = color_2lsa3223 && color_2lsa3223 != undefined  ? color_2lsa3223 : "";
let g_typography_u7mycy23 = typography_u7mycy23 && typography_u7mycy23 != undefined  ? typography_u7mycy23 : "";
let g_color_qfshax23 = color_qfshax23 && color_qfshax23 != undefined  ? color_qfshax23 : "";
let g_color_en7opi23 = color_en7opi23 && color_en7opi23 != undefined  ? color_en7opi23 : "";
let g_background_ah7cuv23 = background_ah7cuv23 && background_ah7cuv23 != undefined  ? background_ah7cuv23 : "";
let g_border_u0i0l323 = border_u0i0l323 && border_u0i0l323 != undefined  ? border_u0i0l323 : "";
let g_background_ht728f23 = background_ht728f23 && background_ht728f23 != undefined  ? background_ht728f23 : "";
let g_border_l3fepp23 = border_l3fepp23 && border_l3fepp23 != undefined  ? border_l3fepp23 : "";
let g_boxshadow_09uoe123 = boxshadow_09uoe123 && boxshadow_09uoe123 != undefined  ? boxshadow_09uoe123 : "";
let g_boxshadow_6gqgma23 = boxshadow_6gqgma23 && boxshadow_6gqgma23 != undefined  ? boxshadow_6gqgma23 : "";
let g_color_9f0ijv23 = color_9f0ijv23 && color_9f0ijv23 != undefined  ? color_9f0ijv23 : "";
let g_color_4zpfye23 = color_4zpfye23 && color_4zpfye23 != undefined  ? color_4zpfye23 : "";
let g_background_4q0qow23 = background_4q0qow23 && background_4q0qow23 != undefined  ? background_4q0qow23 : "";
let g_border_qepes223 = border_qepes223 && border_qepes223 != undefined  ? border_qepes223 : "";
let g_dimension_qyjrez23 = dimension_qyjrez23 && dimension_qyjrez23 != undefined  ? dimension_qyjrez23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-powkvu24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_powkvu24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-banner-3d-with-text">
    <div class="banner-3d-inner">
        <div class="banner-3d-inner-heading">
            <div class="banner-3d-inner-heading-inner">
                <p class="banner-3d-inner-heading-inner-text">${g_text_2lohav23}</p>
                <div class="banner-3d-inner-heading-content">
                    ${g_iconscontrol_7p9n2e23}
                    <span class="banner-3d-inner-heading-subtitle">${g_text_owngik23}</span>
                    ${g_iconscontrol_a76ucn23}
                    <span class="banner-3d-inner-heading-subtitle">${g_text_rf6gqh23}</span>
                </div>
            </div>
        </div>  
         <div class="banner-3d-inner-media">
                <div class="banner-3d-inner-media-inner">
                    <div class="banner-3d-inner-media-inner-image" style="background-image:url(${g_media_d8fqyk23})"></div>
                </div>
         </div>
         <div class="banner-3d-inner-btn">
            <a href="${g_url_34bkrv23_url}" class="btn-banner" target="${g_url_34bkrv23_target}" rel="${g_url_34bkrv23_nofollow} noopener">
                ${g_iconscontrol_hgr83i23}
                <span class="banner-3d-inner-btn-text">${g_text_ibsykc23}</span>
            </a>
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
    new Banner_3D_With_Text_powkvu24();