
    class Info_Card_3D_dtikgw24 {
        constructor() {
            this.Info_Card_3D_dtikgw24_1ls4xs25();
        }
    
        Info_Card_3D_dtikgw24_1ls4xs25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Select,Pmgc_Text,Pmgc_Toggle,Pmgc_Url,Pmgc_Note,Pmgc_RadioAdvanced,Pmgc_Range,Pmgc_Background,Pmgc_Dimension,Pmgc_BoxShadow,Pmgc_CssFilter,Pmgc_Tabs,Pmgc_Typography,Pmgc_Color,
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
   
    registerBlockType('wdkit/wb-dtikgw24', {
        title: __('Info Card 3D'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-id-card tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('hover'),__('3D card'),__('image galleries'),__('menus'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_348lea23Function = (unit, type) => {
                var g_slider_348lea23_list = [];
                g_slider_348lea23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_348lea23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_348lea23_list['em'] = { "type": "em", "min": 0, "max": 100, "step": 1 };
g_slider_348lea23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_348lea23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_348lea23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_348lea23_list[unit][type];
            };
const slider_6pyzzk23Function = (unit, type) => {
                var g_slider_6pyzzk23_list = [];
                g_slider_6pyzzk23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_6pyzzk23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_6pyzzk23_list['em'] = { "type": "em", "min": 0, "max": 100, "step": 1 };
g_slider_6pyzzk23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6pyzzk23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6pyzzk23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6pyzzk23_list[unit][type];
            };
const slider_t3kvf623Function = (unit, type) => {
                var g_slider_t3kvf623_list = [];
                g_slider_t3kvf623_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_t3kvf623_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_t3kvf623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_t3kvf623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_t3kvf623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_t3kvf623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_t3kvf623_list[unit][type];
            };
const slider_w4hvrl23Function = (unit, type) => {
                var g_slider_w4hvrl23_list = [];
                g_slider_w4hvrl23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_w4hvrl23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_w4hvrl23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_w4hvrl23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_w4hvrl23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_w4hvrl23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_w4hvrl23_list[unit][type];
            };
const slider_a58gx923Function = (unit, type) => {
                var g_slider_a58gx923_list = [];
                g_slider_a58gx923_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_a58gx923_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_a58gx923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_a58gx923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_a58gx923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_a58gx923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_a58gx923_list[unit][type];
            };
const slider_mynzrh23Function = (unit, type) => {
                var g_slider_mynzrh23_list = [];
                g_slider_mynzrh23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_mynzrh23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_mynzrh23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mynzrh23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mynzrh23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mynzrh23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mynzrh23_list[unit][type];
            };
const slider_c2nbag25Function = (unit, type) => {
                var g_slider_c2nbag25_list = [];
                g_slider_c2nbag25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_c2nbag25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_c2nbag25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_c2nbag25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_c2nbag25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_c2nbag25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_c2nbag25_list[unit][type];
            };
const slider_wb555l25Function = (unit, type) => {
                var g_slider_wb555l25_list = [];
                g_slider_wb555l25_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_wb555l25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_wb555l25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_wb555l25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_wb555l25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_wb555l25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_wb555l25_list[unit][type];
            };
const slider_wagk7j23Function = (unit, type) => {
                var g_slider_wagk7j23_list = [];
                g_slider_wagk7j23_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_wagk7j23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_wagk7j23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_wagk7j23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_wagk7j23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_wagk7j23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_wagk7j23_list[unit][type];
            };
const slider_6ra31623Function = (unit, type) => {
                var g_slider_6ra31623_list = [];
                g_slider_6ra31623_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_6ra31623_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_6ra31623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_6ra31623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6ra31623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6ra31623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6ra31623_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               media_rgrifu23,
media_eryzmq23,
select_zt4n4023,
media_i0al3x23,
text_o3cj5e23,
text_hz6zzq23,
text_92s25e23,
switcher_h874oh23,
switcher_1gw9zi23,
switcher_1fne9g23,
url_kfr8f423,
switcher_2l3uz025,
switcher_xl8f9r25,
rawhtml_lj6lfq25,
choose_bksbr123,
slider_348lea23,
slider_6pyzzk23,
background_3tpr0j23,
background_3gi7wc23,
dimension_jhyrrj23,
boxshadow_hqqx1u23,
boxshadow_b1tdot23,
cssfilter_76xl1g23,
cssfilter_iv0j2w23,
normalhover_a3pp5h23,
slider_t3kvf623,
slider_w4hvrl23,
typography_ok2cb823,
color_h9ep6723,
color_1xe9kh23,
color_iqe69k23,
textshadow_0mzkjk23,
slider_a58gx923,
slider_mynzrh23,
typography_vufd0e23,
color_gaetcw23,
color_wporwk23,
color_jfkyzl23,
textshadow_we0ow423,
slider_c2nbag25,
slider_wb555l25,
typography_06n3ap23,
color_1r5x6223,
color_keowse25,
color_zjtrfb25,
textshadow_owj6gw23,
slider_wagk7j23,
slider_6ra31623,

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
                        main_function_dtikgw24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_dtikgw24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let infoCard = $scope[0].querySelector('.wkit-hover-3d-card-main');
let cardItem = infoCard.querySelector('.wkit-hover-3d-card-item');
let link = cardItem.querySelector('.info-link');
const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
const isMobile = window.innerWidth <= 767;

link.addEventListener('click', (e) => {
    if (
        (isMobile && !cardItem.classList.contains('disable-mobile-link')) ||
        (isTablet && !cardItem.classList.contains('disable-tablet-link'))
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Media, {
                label: __(`Main Banner Image`),
                multiple: false,
                separator:'default',
                value: media_rgrifu23,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_rgrifu23: value }),
            }), 
 React.createElement(Pmgc_Media, {
                label: __(`Select Character Image`),
                multiple: false,
                separator:'default',
                value: media_eryzmq23,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_eryzmq23: value }),
            }), 
 React.createElement(Pmgc_Select, {
                label: __(`Select Type`),
                options:[['text',__('Text')],['image',__('Image')],],
                separator:"default",
                
                
                value: select_zt4n4023,
                onChange: (value) => {setAttributes({ select_zt4n4023: value }) },
            }),
( select_zt4n4023 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Select Title Image `),
                multiple: false,
                separator:'default',
                value: media_i0al3x23,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_i0al3x23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Text, {
                label: __(`Title 1`),
                type: "text",
                value: text_o3cj5e23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_o3cj5e23: value }) },
            }),
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Text, {
                label: __(`Title 2`),
                type: "text",
                value: text_hz6zzq23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_hz6zzq23: value }) },
            }),
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Text, {
                label: __(`Title 3`),
                type: "text",
                value: text_92s25e23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_92s25e23: value }) },
            }),
),( select_zt4n4023 == "text" ) && React.createElement(PanelBody, { title: __("Special Options"), initialOpen: false },
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Toggle, {
            label: __(`Title 1 Stroke`),
            
            value: switcher_h874oh23,
            
            onChange: (value) => setAttributes({ switcher_h874oh23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Toggle, {
            label: __(`Title 2 Stroke`),
            
            value: switcher_1gw9zi23,
            
            onChange: (value) => setAttributes({ switcher_1gw9zi23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Toggle, {
            label: __(`Title 3 Stroke`),
            
            value: switcher_1fne9g23,
            
            onChange: (value) => setAttributes({ switcher_1fne9g23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_kfr8f423,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_kfr8f423: value }),
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Tablet Link`),
            
            value: switcher_2l3uz025,
            
            onChange: (value) => setAttributes({ switcher_2l3uz025: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Mobile Link`),
            
            value: switcher_xl8f9r25,
            
            onChange: (value) => setAttributes({ switcher_xl8f9r25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_lj6lfq25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/info-card-3d-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_lj6lfq25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Card"), initialOpen: true },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_bksbr123,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_bksbr123: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_348lea23,
            
            min: slider_348lea23 && slider_348lea23.unit ? slider_348lea23Function(slider_348lea23.unit, 'min') : 0,
            max: slider_348lea23 && slider_348lea23.unit ? slider_348lea23Function(slider_348lea23.unit, 'max') : 100,
            step: slider_348lea23 && slider_348lea23.unit ? slider_348lea23Function(slider_348lea23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_348lea23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_6pyzzk23,
            
            min: slider_6pyzzk23 && slider_6pyzzk23.unit ? slider_6pyzzk23Function(slider_6pyzzk23.unit, 'min') : 0,
            max: slider_6pyzzk23 && slider_6pyzzk23.unit ? slider_6pyzzk23Function(slider_6pyzzk23.unit, 'max') : 100,
            step: slider_6pyzzk23 && slider_6pyzzk23.unit ? slider_6pyzzk23Function(slider_6pyzzk23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6pyzzk23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_3tpr0j23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_3tpr0j23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_jhyrrj23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jhyrrj23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_hqqx1u23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_hqqx1u23: value }),
            }), 
 React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_76xl1g23,
            onChange: (value) => setAttributes({ cssfilter_76xl1g23: value }),
            separator:'default',
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_3gi7wc23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_3gi7wc23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_b1tdot23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_b1tdot23: value }),
            }), 
 React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_iv0j2w23,
            onChange: (value) => setAttributes({ cssfilter_iv0j2w23: value }),
            separator:'default',
            }), 
), 
), 
),( select_zt4n4023 == "text" ) && React.createElement(PanelBody, { title: __("Title 1"), initialOpen: false },
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_t3kvf623,
            
            min: slider_t3kvf623 && slider_t3kvf623.unit ? slider_t3kvf623Function(slider_t3kvf623.unit, 'min') : 0,
            max: slider_t3kvf623 && slider_t3kvf623.unit ? slider_t3kvf623Function(slider_t3kvf623.unit, 'max') : 100,
            step: slider_t3kvf623 && slider_t3kvf623.unit ? slider_t3kvf623Function(slider_t3kvf623.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_t3kvf623: value }),
            }), 
( switcher_h874oh23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_w4hvrl23,
            
            min: slider_w4hvrl23 && slider_w4hvrl23.unit ? slider_w4hvrl23Function(slider_w4hvrl23.unit, 'min') : 0,
            max: slider_w4hvrl23 && slider_w4hvrl23.unit ? slider_w4hvrl23Function(slider_w4hvrl23.unit, 'max') : 100,
            step: slider_w4hvrl23 && slider_w4hvrl23.unit ? slider_w4hvrl23Function(slider_w4hvrl23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_w4hvrl23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_ok2cb823,
            onChange: (value) => setAttributes({ typography_ok2cb823: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_zt4n4023 == "text" && !switcher_h874oh23 ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_h9ep6723,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_h9ep6723: value }),
            }), 
( switcher_h874oh23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_1xe9kh23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1xe9kh23: value }),
            }), 
( switcher_h874oh23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Fill Color`),
            value: color_iqe69k23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_iqe69k23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_0mzkjk23,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_0mzkjk23: value }),
            }), 
),( select_zt4n4023 == "text" ) && React.createElement(PanelBody, { title: __("Title 2"), initialOpen: false },
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_a58gx923,
            
            min: slider_a58gx923 && slider_a58gx923.unit ? slider_a58gx923Function(slider_a58gx923.unit, 'min') : 0,
            max: slider_a58gx923 && slider_a58gx923.unit ? slider_a58gx923Function(slider_a58gx923.unit, 'max') : 100,
            step: slider_a58gx923 && slider_a58gx923.unit ? slider_a58gx923Function(slider_a58gx923.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_a58gx923: value }),
            }), 
( switcher_1gw9zi23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_mynzrh23,
            
            min: slider_mynzrh23 && slider_mynzrh23.unit ? slider_mynzrh23Function(slider_mynzrh23.unit, 'min') : 0,
            max: slider_mynzrh23 && slider_mynzrh23.unit ? slider_mynzrh23Function(slider_mynzrh23.unit, 'max') : 100,
            step: slider_mynzrh23 && slider_mynzrh23.unit ? slider_mynzrh23Function(slider_mynzrh23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mynzrh23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_vufd0e23,
            onChange: (value) => setAttributes({ typography_vufd0e23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_zt4n4023 == "text" && !switcher_1gw9zi23 ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_gaetcw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_gaetcw23: value }),
            }), 
( switcher_1gw9zi23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_wporwk23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_wporwk23: value }),
            }), 
( switcher_1gw9zi23 && select_zt4n4023 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Fill Color`),
            value: color_jfkyzl23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_jfkyzl23: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_we0ow423,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_we0ow423: value }),
            }), 
),( select_zt4n4023 == "text" ) && React.createElement(PanelBody, { title: __("Title 3"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Gap`),
            separator:'default',
            value: slider_c2nbag25,
            
            min: slider_c2nbag25 && slider_c2nbag25.unit ? slider_c2nbag25Function(slider_c2nbag25.unit, 'min') : 0,
            max: slider_c2nbag25 && slider_c2nbag25.unit ? slider_c2nbag25Function(slider_c2nbag25.unit, 'max') : 100,
            step: slider_c2nbag25 && slider_c2nbag25.unit ? slider_c2nbag25Function(slider_c2nbag25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_c2nbag25: value }),
            }), 
( switcher_1fne9g23 ) && React.createElement(Pmgc_Range, {
            label: __(`Stroke Width`),
            separator:'default',
            value: slider_wb555l25,
            
            min: slider_wb555l25 && slider_wb555l25.unit ? slider_wb555l25Function(slider_wb555l25.unit, 'min') : 0,
            max: slider_wb555l25 && slider_wb555l25.unit ? slider_wb555l25Function(slider_wb555l25.unit, 'max') : 100,
            step: slider_wb555l25 && slider_wb555l25.unit ? slider_wb555l25Function(slider_wb555l25.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_wb555l25: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_Typography, {
            
            value: typography_06n3ap23,
            onChange: (value) => setAttributes({ typography_06n3ap23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
( select_zt4n4023 == "text" && !switcher_1fne9g23 ) && React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1r5x6223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1r5x6223: value }),
            }), 
( switcher_1fne9g23 ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Color`),
            value: color_keowse25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_keowse25: value }),
            }), 
( switcher_1fne9g23 ) && React.createElement(Pmgc_Color, {
            label: __(`Stroke Fill Color`),
            value: color_zjtrfb25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_zjtrfb25: value }),
            }), 
( select_zt4n4023 == "text" ) && React.createElement(Pmgc_BoxShadow, {
            
            value: textshadow_owj6gw23,
            istextShadow: true,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ textshadow_owj6gw23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Character Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_wagk7j23,
            
            min: slider_wagk7j23 && slider_wagk7j23.unit ? slider_wagk7j23Function(slider_wagk7j23.unit, 'min') : 0,
            max: slider_wagk7j23 && slider_wagk7j23.unit ? slider_wagk7j23Function(slider_wagk7j23.unit, 'max') : 100,
            step: slider_wagk7j23 && slider_wagk7j23.unit ? slider_wagk7j23Function(slider_wagk7j23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_wagk7j23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_6ra31623,
            
            min: slider_6ra31623 && slider_6ra31623.unit ? slider_6ra31623Function(slider_6ra31623.unit, 'min') : 0,
            max: slider_6ra31623 && slider_6ra31623.unit ? slider_6ra31623Function(slider_6ra31623.unit, 'max') : 100,
            step: slider_6ra31623 && slider_6ra31623.unit ? slider_6ra31623Function(slider_6ra31623.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6ra31623: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-dtikgw24", block_id, false, props.clientId);
                }
            }

            
let g_media_rgrifu23 = media_rgrifu23 && media_rgrifu23.url && media_rgrifu23.url != undefined  ? media_rgrifu23.url : "";
let g_media_eryzmq23 = media_eryzmq23 && media_eryzmq23.url && media_eryzmq23.url != undefined  ? media_eryzmq23.url : "";
let g_select_zt4n4023 = select_zt4n4023 && select_zt4n4023 != undefined  ? select_zt4n4023 : "";
let g_media_i0al3x23 = media_i0al3x23 && media_i0al3x23.url && media_i0al3x23.url != undefined && ( (select_zt4n4023 == "image") ) ? media_i0al3x23.url : "";
let g_text_o3cj5e23 = text_o3cj5e23 && text_o3cj5e23 != undefined && ( (select_zt4n4023 == "text") ) ? text_o3cj5e23 : "";
let g_text_hz6zzq23 = text_hz6zzq23 && text_hz6zzq23 != undefined && ( (select_zt4n4023 == "text") ) ? text_hz6zzq23 : "";
let g_text_92s25e23 = text_92s25e23 && text_92s25e23 != undefined && ( (select_zt4n4023 == "text") ) ? text_92s25e23 : "";
let g_switcher_h874oh23 = switcher_h874oh23 && switcher_h874oh23 != undefined && ( (select_zt4n4023 == "text") ) ? 'yes' : "";
let g_switcher_1gw9zi23 = switcher_1gw9zi23 && switcher_1gw9zi23 != undefined && ( (select_zt4n4023 == "text") ) ? 'title2-active' : "";
let g_switcher_1fne9g23 = switcher_1fne9g23 && switcher_1fne9g23 != undefined && ( (select_zt4n4023 == "text") ) ? 'title3-active' : "";
let g_url_kfr8f423_url = url_kfr8f423?.url && url_kfr8f423?.url != undefined ? url_kfr8f423.url : "";
let g_url_kfr8f423_target = url_kfr8f423?.target && url_kfr8f423?.target != undefined ? url_kfr8f423.target : "";
let g_url_kfr8f423_nofollow = url_kfr8f423?.nofollow && url_kfr8f423?.nofollow != undefined ? url_kfr8f423.nofollow : "";
let g_url_kfr8f423_ctmArt = url_kfr8f423?.attr != undefined ? url_kfr8f423.attr : "";
                    let g_url_kfr8f423_attr = ''

                    if (g_url_kfr8f423_ctmArt) {
                        let main_array = g_url_kfr8f423_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_kfr8f423_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_2l3uz025 = switcher_2l3uz025 && switcher_2l3uz025 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_xl8f9r25 = switcher_xl8f9r25 && switcher_xl8f9r25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_lj6lfq25 = rawhtml_lj6lfq25 && rawhtml_lj6lfq25 != undefined  ? rawhtml_lj6lfq25 : "";
let g_choose_bksbr123 = choose_bksbr123 && choose_bksbr123 != undefined  ? choose_bksbr123 : "";
let g_background_3tpr0j23 = background_3tpr0j23 && background_3tpr0j23 != undefined  ? background_3tpr0j23 : "";
let g_background_3gi7wc23 = background_3gi7wc23 && background_3gi7wc23 != undefined  ? background_3gi7wc23 : "";
let g_dimension_jhyrrj23 = dimension_jhyrrj23 && dimension_jhyrrj23 != undefined  ? dimension_jhyrrj23 : "";
let g_boxshadow_hqqx1u23 = boxshadow_hqqx1u23 && boxshadow_hqqx1u23 != undefined  ? boxshadow_hqqx1u23 : "";
let g_boxshadow_b1tdot23 = boxshadow_b1tdot23 && boxshadow_b1tdot23 != undefined  ? boxshadow_b1tdot23 : "";
let g_cssfilter_76xl1g23 = cssfilter_76xl1g23 && cssfilter_76xl1g23 != undefined  ? cssfilter_76xl1g23 : "";
let g_cssfilter_iv0j2w23 = cssfilter_iv0j2w23 && cssfilter_iv0j2w23 != undefined  ? cssfilter_iv0j2w23 : "";
let g_typography_ok2cb823 = typography_ok2cb823 && typography_ok2cb823 != undefined && ( (select_zt4n4023 == "text") ) ? typography_ok2cb823 : "";
let g_color_h9ep6723 = color_h9ep6723 && color_h9ep6723 != undefined && ( (select_zt4n4023 == "text")&&!switcher_h874oh23 ) ? color_h9ep6723 : "";
let g_color_1xe9kh23 = color_1xe9kh23 && color_1xe9kh23 != undefined && ( switcher_h874oh23&&(select_zt4n4023 == "text") ) ? color_1xe9kh23 : "";
let g_color_iqe69k23 = color_iqe69k23 && color_iqe69k23 != undefined && ( switcher_h874oh23&&(select_zt4n4023 == "text") ) ? color_iqe69k23 : "";
let g_textshadow_0mzkjk23 = textshadow_0mzkjk23 && textshadow_0mzkjk23 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_0mzkjk23 : "";
let g_typography_vufd0e23 = typography_vufd0e23 && typography_vufd0e23 != undefined && ( (select_zt4n4023 == "text") ) ? typography_vufd0e23 : "";
let g_color_gaetcw23 = color_gaetcw23 && color_gaetcw23 != undefined && ( (select_zt4n4023 == "text")&&!switcher_1gw9zi23 ) ? color_gaetcw23 : "";
let g_color_wporwk23 = color_wporwk23 && color_wporwk23 != undefined && ( switcher_1gw9zi23&&(select_zt4n4023 == "text") ) ? color_wporwk23 : "";
let g_color_jfkyzl23 = color_jfkyzl23 && color_jfkyzl23 != undefined && ( switcher_1gw9zi23&&(select_zt4n4023 == "text") ) ? color_jfkyzl23 : "";
let g_textshadow_we0ow423 = textshadow_we0ow423 && textshadow_we0ow423 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_we0ow423 : "";
let g_typography_06n3ap23 = typography_06n3ap23 && typography_06n3ap23 != undefined && ( (select_zt4n4023 == "text") ) ? typography_06n3ap23 : "";
let g_color_1r5x6223 = color_1r5x6223 && color_1r5x6223 != undefined && ( (select_zt4n4023 == "text")&&!switcher_1fne9g23 ) ? color_1r5x6223 : "";
let g_color_keowse25 = color_keowse25 && color_keowse25 != undefined && ( switcher_1fne9g23 ) ? color_keowse25 : "";
let g_color_zjtrfb25 = color_zjtrfb25 && color_zjtrfb25 != undefined && ( switcher_1fne9g23 ) ? color_zjtrfb25 : "";
let g_textshadow_owj6gw23 = textshadow_owj6gw23 && textshadow_owj6gw23 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_owj6gw23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_dtikgw24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-hover-3d-card-main">
    <div class="wkit-hover-3d-card-item ${g_switcher_2l3uz025} ${g_switcher_xl8f9r25}">
         <a href="${g_url_kfr8f423_url}" target="${g_url_kfr8f423_target}" rel="${g_url_kfr8f423_nofollow} noopener" class="info-link">
          <div class="wkit-hover-3d-card">
            <div class="wkit-hover-3d-card-wrap">
              <img src="${g_media_rgrifu23}" class="wkit-hover-3d-card-cover-img">
            </div>
            <div class="wkit-content wkit-hover-card-title">
                <div class="wkit-hover-3d-card-title-1 wkit-stroke-${g_switcher_h874oh23}">${g_text_o3cj5e23}</div>
                <div class="wkit-hover-3d-card-title-2 wkit-stroke-${g_switcher_1gw9zi23}">${g_text_hz6zzq23}</div>
                <div class="wkit-hover-3d-card-title-3 wkit-stroke-${g_switcher_1fne9g23}">${g_text_92s25e23}</div>
            </div>
            <img src="${g_media_i0al3x23}" class="wkit-hover-card-title">
            <img src="${g_media_eryzmq23}" class="wkit-hover-card-character">
          </div> 
        </a>
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
            media_rgrifu23,
media_eryzmq23,
select_zt4n4023,
media_i0al3x23,
text_o3cj5e23,
text_hz6zzq23,
text_92s25e23,
switcher_h874oh23,
switcher_1gw9zi23,
switcher_1fne9g23,
url_kfr8f423,
switcher_2l3uz025,
switcher_xl8f9r25,
rawhtml_lj6lfq25,
choose_bksbr123,
slider_348lea23,
slider_6pyzzk23,
background_3tpr0j23,
background_3gi7wc23,
dimension_jhyrrj23,
boxshadow_hqqx1u23,
boxshadow_b1tdot23,
cssfilter_76xl1g23,
cssfilter_iv0j2w23,
normalhover_a3pp5h23,
slider_t3kvf623,
slider_w4hvrl23,
typography_ok2cb823,
color_h9ep6723,
color_1xe9kh23,
color_iqe69k23,
textshadow_0mzkjk23,
slider_a58gx923,
slider_mynzrh23,
typography_vufd0e23,
color_gaetcw23,
color_wporwk23,
color_jfkyzl23,
textshadow_we0ow423,
slider_c2nbag25,
slider_wb555l25,
typography_06n3ap23,
color_1r5x6223,
color_keowse25,
color_zjtrfb25,
textshadow_owj6gw23,
slider_wagk7j23,
slider_6ra31623,

            block_id,
        } = attributes;

        

        

        

        
let g_media_rgrifu23 = media_rgrifu23 && media_rgrifu23.url && media_rgrifu23.url != undefined  ? media_rgrifu23.url : "";
let g_media_eryzmq23 = media_eryzmq23 && media_eryzmq23.url && media_eryzmq23.url != undefined  ? media_eryzmq23.url : "";
let g_select_zt4n4023 = select_zt4n4023 && select_zt4n4023 != undefined  ? select_zt4n4023 : "";
let g_media_i0al3x23 = media_i0al3x23 && media_i0al3x23.url && media_i0al3x23.url != undefined && ( (select_zt4n4023 == "image") ) ? media_i0al3x23.url : "";
let g_text_o3cj5e23 = text_o3cj5e23 && text_o3cj5e23 != undefined && ( (select_zt4n4023 == "text") ) ? text_o3cj5e23 : "";
let g_text_hz6zzq23 = text_hz6zzq23 && text_hz6zzq23 != undefined && ( (select_zt4n4023 == "text") ) ? text_hz6zzq23 : "";
let g_text_92s25e23 = text_92s25e23 && text_92s25e23 != undefined && ( (select_zt4n4023 == "text") ) ? text_92s25e23 : "";
let g_switcher_h874oh23 = switcher_h874oh23 && switcher_h874oh23 != undefined && ( (select_zt4n4023 == "text") ) ? 'yes' : "";
let g_switcher_1gw9zi23 = switcher_1gw9zi23 && switcher_1gw9zi23 != undefined && ( (select_zt4n4023 == "text") ) ? 'title2-active' : "";
let g_switcher_1fne9g23 = switcher_1fne9g23 && switcher_1fne9g23 != undefined && ( (select_zt4n4023 == "text") ) ? 'title3-active' : "";
let g_url_kfr8f423_url = url_kfr8f423?.url && url_kfr8f423?.url != undefined ? url_kfr8f423.url : "";
let g_url_kfr8f423_target = url_kfr8f423?.target && url_kfr8f423?.target != undefined ? url_kfr8f423.target : "";
let g_url_kfr8f423_nofollow = url_kfr8f423?.nofollow && url_kfr8f423?.nofollow != undefined ? url_kfr8f423.nofollow : "";
let g_url_kfr8f423_ctmArt = url_kfr8f423?.attr != undefined ? url_kfr8f423.attr : "";
                    let g_url_kfr8f423_attr = ''

                    if (g_url_kfr8f423_ctmArt) {
                        let main_array = g_url_kfr8f423_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_kfr8f423_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_switcher_2l3uz025 = switcher_2l3uz025 && switcher_2l3uz025 != undefined  ? 'disable-tablet-link' : "";
let g_switcher_xl8f9r25 = switcher_xl8f9r25 && switcher_xl8f9r25 != undefined  ? 'disable-mobile-link' : "";
let g_rawhtml_lj6lfq25 = rawhtml_lj6lfq25 && rawhtml_lj6lfq25 != undefined  ? rawhtml_lj6lfq25 : "";
let g_choose_bksbr123 = choose_bksbr123 && choose_bksbr123 != undefined  ? choose_bksbr123 : "";
let g_background_3tpr0j23 = background_3tpr0j23 && background_3tpr0j23 != undefined  ? background_3tpr0j23 : "";
let g_background_3gi7wc23 = background_3gi7wc23 && background_3gi7wc23 != undefined  ? background_3gi7wc23 : "";
let g_dimension_jhyrrj23 = dimension_jhyrrj23 && dimension_jhyrrj23 != undefined  ? dimension_jhyrrj23 : "";
let g_boxshadow_hqqx1u23 = boxshadow_hqqx1u23 && boxshadow_hqqx1u23 != undefined  ? boxshadow_hqqx1u23 : "";
let g_boxshadow_b1tdot23 = boxshadow_b1tdot23 && boxshadow_b1tdot23 != undefined  ? boxshadow_b1tdot23 : "";
let g_cssfilter_76xl1g23 = cssfilter_76xl1g23 && cssfilter_76xl1g23 != undefined  ? cssfilter_76xl1g23 : "";
let g_cssfilter_iv0j2w23 = cssfilter_iv0j2w23 && cssfilter_iv0j2w23 != undefined  ? cssfilter_iv0j2w23 : "";
let g_typography_ok2cb823 = typography_ok2cb823 && typography_ok2cb823 != undefined && ( (select_zt4n4023 == "text") ) ? typography_ok2cb823 : "";
let g_color_h9ep6723 = color_h9ep6723 && color_h9ep6723 != undefined && ( (select_zt4n4023 == "text")&&!switcher_h874oh23 ) ? color_h9ep6723 : "";
let g_color_1xe9kh23 = color_1xe9kh23 && color_1xe9kh23 != undefined && ( switcher_h874oh23&&(select_zt4n4023 == "text") ) ? color_1xe9kh23 : "";
let g_color_iqe69k23 = color_iqe69k23 && color_iqe69k23 != undefined && ( switcher_h874oh23&&(select_zt4n4023 == "text") ) ? color_iqe69k23 : "";
let g_textshadow_0mzkjk23 = textshadow_0mzkjk23 && textshadow_0mzkjk23 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_0mzkjk23 : "";
let g_typography_vufd0e23 = typography_vufd0e23 && typography_vufd0e23 != undefined && ( (select_zt4n4023 == "text") ) ? typography_vufd0e23 : "";
let g_color_gaetcw23 = color_gaetcw23 && color_gaetcw23 != undefined && ( (select_zt4n4023 == "text")&&!switcher_1gw9zi23 ) ? color_gaetcw23 : "";
let g_color_wporwk23 = color_wporwk23 && color_wporwk23 != undefined && ( switcher_1gw9zi23&&(select_zt4n4023 == "text") ) ? color_wporwk23 : "";
let g_color_jfkyzl23 = color_jfkyzl23 && color_jfkyzl23 != undefined && ( switcher_1gw9zi23&&(select_zt4n4023 == "text") ) ? color_jfkyzl23 : "";
let g_textshadow_we0ow423 = textshadow_we0ow423 && textshadow_we0ow423 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_we0ow423 : "";
let g_typography_06n3ap23 = typography_06n3ap23 && typography_06n3ap23 != undefined && ( (select_zt4n4023 == "text") ) ? typography_06n3ap23 : "";
let g_color_1r5x6223 = color_1r5x6223 && color_1r5x6223 != undefined && ( (select_zt4n4023 == "text")&&!switcher_1fne9g23 ) ? color_1r5x6223 : "";
let g_color_keowse25 = color_keowse25 && color_keowse25 != undefined && ( switcher_1fne9g23 ) ? color_keowse25 : "";
let g_color_zjtrfb25 = color_zjtrfb25 && color_zjtrfb25 != undefined && ( switcher_1fne9g23 ) ? color_zjtrfb25 : "";
let g_textshadow_owj6gw23 = textshadow_owj6gw23 && textshadow_owj6gw23 != undefined && ( (select_zt4n4023 == "text") ) ? textshadow_owj6gw23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-dtikgw24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_dtikgw24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-hover-3d-card-main">
    <div class="wkit-hover-3d-card-item ${g_switcher_2l3uz025} ${g_switcher_xl8f9r25}">
         <a href="${g_url_kfr8f423_url}" target="${g_url_kfr8f423_target}" rel="${g_url_kfr8f423_nofollow} noopener" class="info-link">
          <div class="wkit-hover-3d-card">
            <div class="wkit-hover-3d-card-wrap">
              <img src="${g_media_rgrifu23}" class="wkit-hover-3d-card-cover-img">
            </div>
            <div class="wkit-content wkit-hover-card-title">
                <div class="wkit-hover-3d-card-title-1 wkit-stroke-${g_switcher_h874oh23}">${g_text_o3cj5e23}</div>
                <div class="wkit-hover-3d-card-title-2 wkit-stroke-${g_switcher_1gw9zi23}">${g_text_hz6zzq23}</div>
                <div class="wkit-hover-3d-card-title-3 wkit-stroke-${g_switcher_1fne9g23}">${g_text_92s25e23}</div>
            </div>
            <img src="${g_media_i0al3x23}" class="wkit-hover-card-title">
            <img src="${g_media_eryzmq23}" class="wkit-hover-card-character">
          </div> 
        </a>
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
    new Info_Card_3D_dtikgw24();