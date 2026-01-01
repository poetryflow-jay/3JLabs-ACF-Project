
    class Tabbed_Images_Carousel_tedwgp24 {
        constructor() {
            this.Tabbed_Images_Carousel_tedwgp24_r0b7o325();
        }
    
        Tabbed_Images_Carousel_tedwgp24_r0b7o325() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_TextArea,Pmgc_Media,Pmgc_Repeater,Pmgc_Toggle,Pmgc_Note,Pmgc_Dimension,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,
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
   
    registerBlockType('wdkit/wb-tedwgp24', {
        title: __('Tabbed Images Carousel'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-tasks tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Tabs and Sliders'),__('Image Carousel'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_d5cslh23Function = (unit, type) => {
                var g_slider_d5cslh23_list = [];
                g_slider_d5cslh23_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_d5cslh23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_d5cslh23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_d5cslh23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_d5cslh23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_d5cslh23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_d5cslh23_list[unit][type];
            };
const slider_mhy6ql24Function = (unit, type) => {
                var g_slider_mhy6ql24_list = [];
                g_slider_mhy6ql24_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_mhy6ql24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_mhy6ql24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mhy6ql24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mhy6ql24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mhy6ql24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mhy6ql24_list[unit][type];
            };
const slider_i1ailp25Function = (unit, type) => {
                var g_slider_i1ailp25_list = [];
                g_slider_i1ailp25_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_i1ailp25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_i1ailp25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_i1ailp25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_i1ailp25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_i1ailp25_list['vh'] = { "type": "vh", "min": 0, "max": 100, "step": 1 };

                return g_slider_i1ailp25_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_gkmzpc23,
switcher_kt3fu723,
number_vfakqm23,
switcher_g8zjta23,
rawhtml_ags7w025,
dimension_cq5cog23,
typography_w5fjs223,
color_onttor23,
background_1ugzf623,
border_edxw8r23,
dimension_o2mdr723,
boxshadow_gfx23523,
color_gjbogd23,
background_m3aw1d23,
border_0semwo23,
boxshadow_j97m6y23,
color_u581v123,
background_qpms8o23,
border_fnx71323,
dimension_7kl3kf23,
boxshadow_nz483q23,
normalhover_wmxee423,
dimension_y4svm623,
typography_wl8gha23,
color_hjz2df23,
background_qsdslh23,
border_lffub123,
dimension_x6nwev23,
boxshadow_a3an8v23,
color_v2r1rq23,
background_c1206523,
border_lly75x23,
boxshadow_9li4zh23,
normalhover_vvjeoa23,
dimension_19snqt23,
dimension_mwp4jp23,
slider_d5cslh23,
color_0muwx223,
background_o7jfsv25,
border_er162525,
dimension_ib87jp25,
boxshadow_9w3omk25,
background_0p3b1025,
border_g6qu7h25,
boxshadow_93rky525,
background_1mv9a325,
border_iioife25,
dimension_y2wbtt25,
boxshadow_hyw5vm25,
normalhover_xmd9n625,
rawhtml_mldvc425,
dimension_sjgysx24,
slider_mhy6ql24,
slider_i1ailp25,
border_usissh23,
dimension_lj4gns23,
boxshadow_usizhc25,

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
                        main_function_tedwgp24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_tedwgp24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let mainTabC = $scope[0].querySelector('.wkit-tabbed-images-carousel');
if (!mainTabC) return;

let tabbed = mainTabC.querySelector(".tabbed-splide");

let splidewv;
if (typeof Splide !== 'undefined' && tabbed) {
    splidewv = new Splide(tabbed, {
        drag: false,
        arrows: false,
        autoplay: false,
        pagination: false,
    }).mount();
}

$scope[0].style.width = '100%';

let setIntVal;

// Switch active tab and Splide slide
function wkittabschange(crt, leftItem) {
    if (!crt.classList.contains('active')) {
        let getItemD = Number(crt.getAttribute('data-item'));
        leftItem.forEach((lia) => {
            lia.classList.remove('active');
            let getDesc = lia.querySelector('.car-content-desc');
            if (getDesc) getDesc.style.display = 'none';
        });

        crt.classList.add('active');
        let gecCDesc = crt.querySelector('.car-content-desc');
        if (gecCDesc) gecCDesc.style.display = 'block';

        // Sync with Splide
        if (splidewv && splidewv.go && !isNaN(getItemD)) {
            splidewv.go(getItemD);
        }
    }
}

// Auto advance logic
function autoIntervalAcc(active, leftItem) {
    if (active) {
        let next = active.nextElementSibling;
        if (!next) {
            next = leftItem[0]; // fallback to first
        }
        if (next) wkittabschange(next, leftItem);
    }
}

function initializeTabCarousel() {
    let leftItem = mainTabC.querySelectorAll('.carousel-left-content');
    let gtDuration = Number(mainTabC.getAttribute('data-duration') || 5);
mainTabC.style.setProperty('--border-duration', `${gtDuration}s`);

    if (leftItem.length) {
        // Initialize: mark first as active
        leftItem.forEach((li, index) => {
            let desc = li.querySelector('.car-content-desc');
            li.setAttribute('data-item', index); // 💡 Ensure this is added
            if (index === 0) {
                li.classList.add('active');
                if (desc) desc.style.display = 'block';
                if (splidewv && splidewv.go) {
                    splidewv.go(index); // force Splide to sync
                }
            } else {
                if (desc) desc.style.display = 'none';
            }

            // Click listener
            li.addEventListener('click', () => {
                wkittabschange(li, leftItem);
                if (mainTabC.classList.contains('tabbed-auto-play-yes')) {
                    clearInterval(setIntVal);
                    setIntVal = setInterval(() => {
                        let active = mainTabC.querySelector('.carousel-left-content.active');
                        autoIntervalAcc(active, leftItem);
                    }, gtDuration * 1000);
                }
            });
        });

        // Start autoplay
        if (mainTabC.classList.contains('tabbed-auto-play-yes')) {
            setIntVal = setInterval(() => {
                let active = mainTabC.querySelector('.carousel-left-content.active');
                autoIntervalAcc(active, leftItem);
            }, gtDuration * 1000);
        }
    }
}

// Ensure everything is ready
setTimeout(initializeTabCarousel, 10); // Or requestAnimationFrame if needed

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Tabs`),
            value: repeater_gkmzpc23,
            attributeName: 'repeater_gkmzpc23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_gkmzpc23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_ds10f723,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_ds10f723 = v; onChange(value); },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: value.textarea_c3jywx23,
                dynamic: true,
                onChange: v => { value.textarea_c3jywx23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_gtnxy423,
                
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_gtnxy423 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Auto Play`),
            
            value: switcher_kt3fu723,
            
            onChange: (value) => setAttributes({ switcher_kt3fu723: value }),
            }), 
( switcher_kt3fu723 ) && React.createElement(Pmgc_Text, {
                label: __(`Duration`),
                type: "number",
                value: number_vfakqm23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_vfakqm23: value }) },
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Reverse`),
            
            value: switcher_g8zjta23,
            
            onChange: (value) => setAttributes({ switcher_g8zjta23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_ags7w025,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/tabbed-images-carousel-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_ags7w025: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Title"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_cq5cog23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_cq5cog23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_w5fjs223,
            onChange: (value) => setAttributes({ typography_w5fjs223: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_onttor23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_onttor23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_1ugzf623,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_1ugzf623: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_edxw8r23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_edxw8r23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_o2mdr723,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_o2mdr723: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_gfx23523,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_gfx23523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_gjbogd23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_gjbogd23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_m3aw1d23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_m3aw1d23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_0semwo23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_0semwo23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_j97m6y23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_j97m6y23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_u581v123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_u581v123: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_qpms8o23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qpms8o23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_fnx71323,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_fnx71323: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_7kl3kf23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_7kl3kf23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_nz483q23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_nz483q23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_y4svm623,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_y4svm623: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_wl8gha23,
            onChange: (value) => setAttributes({ typography_wl8gha23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_hjz2df23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_hjz2df23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_qsdslh23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_qsdslh23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_lffub123,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_lffub123: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_x6nwev23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_x6nwev23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_a3an8v23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_a3an8v23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_v2r1rq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_v2r1rq23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_c1206523,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_c1206523: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_lly75x23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_lly75x23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_9li4zh23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_9li4zh23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Accordion"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_19snqt23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_19snqt23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_mwp4jp23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_mwp4jp23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
( switcher_kt3fu723 ) && React.createElement(Pmgc_Range, {
            label: __(`Bottom Border Width`),
            separator:'default',
            value: slider_d5cslh23,
            
            min: slider_d5cslh23 && slider_d5cslh23.unit ? slider_d5cslh23Function(slider_d5cslh23.unit, 'min') : 0,
            max: slider_d5cslh23 && slider_d5cslh23.unit ? slider_d5cslh23Function(slider_d5cslh23.unit, 'max') : 100,
            step: slider_d5cslh23 && slider_d5cslh23.unit ? slider_d5cslh23Function(slider_d5cslh23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_d5cslh23: value }),
            }), 
( switcher_kt3fu723 ) && React.createElement(Pmgc_Color, {
            label: __(`Bottom Border Color`),
            value: color_0muwx223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0muwx223: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_o7jfsv25,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_o7jfsv25: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_er162525,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_er162525: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ib87jp25,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ib87jp25: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_9w3omk25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_9w3omk25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_0p3b1025,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_0p3b1025: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_g6qu7h25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_g6qu7h25: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_93rky525,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_93rky525: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_1mv9a325,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_1mv9a325: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_iioife25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_iioife25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_y2wbtt25,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_y2wbtt25: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_hyw5vm25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_hyw5vm25: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_mldvc425,
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
If color properties are applied to the title and description, the color property from the accordion will not be applied.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_mldvc425: value }),
            }), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_sjgysx24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_sjgysx24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_mhy6ql24,
            
            min: slider_mhy6ql24 && slider_mhy6ql24.unit ? slider_mhy6ql24Function(slider_mhy6ql24.unit, 'min') : 0,
            max: slider_mhy6ql24 && slider_mhy6ql24.unit ? slider_mhy6ql24Function(slider_mhy6ql24.unit, 'max') : 100,
            step: slider_mhy6ql24 && slider_mhy6ql24.unit ? slider_mhy6ql24Function(slider_mhy6ql24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mhy6ql24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_i1ailp25,
            
            min: slider_i1ailp25 && slider_i1ailp25.unit ? slider_i1ailp25Function(slider_i1ailp25.unit, 'min') : 0,
            max: slider_i1ailp25 && slider_i1ailp25.unit ? slider_i1ailp25Function(slider_i1ailp25.unit, 'max') : 100,
            step: slider_i1ailp25 && slider_i1ailp25.unit ? slider_i1ailp25Function(slider_i1ailp25.unit, 'step') : 1,
            
                unit: ['px', '%', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_i1ailp25: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_usissh23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_usissh23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_lj4gns23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_lj4gns23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_usizhc25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_usizhc25: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-tedwgp24", block_id, false, props.clientId);
                }
            }

            
let g_switcher_kt3fu723 = switcher_kt3fu723 && switcher_kt3fu723 != undefined  ? 'yes' : "";
let g_number_vfakqm23 = number_vfakqm23 && number_vfakqm23 != undefined && ( switcher_kt3fu723 ) ? number_vfakqm23 : "";
let g_switcher_g8zjta23 = switcher_g8zjta23 && switcher_g8zjta23 != undefined  ? 'yes' : "";
let g_rawhtml_ags7w025 = rawhtml_ags7w025 && rawhtml_ags7w025 != undefined  ? rawhtml_ags7w025 : "";
let g_dimension_cq5cog23 = dimension_cq5cog23 && dimension_cq5cog23 != undefined  ? dimension_cq5cog23 : "";
let g_typography_w5fjs223 = typography_w5fjs223 && typography_w5fjs223 != undefined  ? typography_w5fjs223 : "";
let g_color_onttor23 = color_onttor23 && color_onttor23 != undefined  ? color_onttor23 : "";
let g_background_1ugzf623 = background_1ugzf623 && background_1ugzf623 != undefined  ? background_1ugzf623 : "";
let g_border_edxw8r23 = border_edxw8r23 && border_edxw8r23 != undefined  ? border_edxw8r23 : "";
let g_dimension_o2mdr723 = dimension_o2mdr723 && dimension_o2mdr723 != undefined  ? dimension_o2mdr723 : "";
let g_boxshadow_gfx23523 = boxshadow_gfx23523 && boxshadow_gfx23523 != undefined  ? boxshadow_gfx23523 : "";
let g_color_gjbogd23 = color_gjbogd23 && color_gjbogd23 != undefined  ? color_gjbogd23 : "";
let g_background_m3aw1d23 = background_m3aw1d23 && background_m3aw1d23 != undefined  ? background_m3aw1d23 : "";
let g_border_0semwo23 = border_0semwo23 && border_0semwo23 != undefined  ? border_0semwo23 : "";
let g_boxshadow_j97m6y23 = boxshadow_j97m6y23 && boxshadow_j97m6y23 != undefined  ? boxshadow_j97m6y23 : "";
let g_color_u581v123 = color_u581v123 && color_u581v123 != undefined  ? color_u581v123 : "";
let g_background_qpms8o23 = background_qpms8o23 && background_qpms8o23 != undefined  ? background_qpms8o23 : "";
let g_border_fnx71323 = border_fnx71323 && border_fnx71323 != undefined  ? border_fnx71323 : "";
let g_dimension_7kl3kf23 = dimension_7kl3kf23 && dimension_7kl3kf23 != undefined  ? dimension_7kl3kf23 : "";
let g_boxshadow_nz483q23 = boxshadow_nz483q23 && boxshadow_nz483q23 != undefined  ? boxshadow_nz483q23 : "";
let g_dimension_y4svm623 = dimension_y4svm623 && dimension_y4svm623 != undefined  ? dimension_y4svm623 : "";
let g_typography_wl8gha23 = typography_wl8gha23 && typography_wl8gha23 != undefined  ? typography_wl8gha23 : "";
let g_color_hjz2df23 = color_hjz2df23 && color_hjz2df23 != undefined  ? color_hjz2df23 : "";
let g_background_qsdslh23 = background_qsdslh23 && background_qsdslh23 != undefined  ? background_qsdslh23 : "";
let g_border_lffub123 = border_lffub123 && border_lffub123 != undefined  ? border_lffub123 : "";
let g_dimension_x6nwev23 = dimension_x6nwev23 && dimension_x6nwev23 != undefined  ? dimension_x6nwev23 : "";
let g_boxshadow_a3an8v23 = boxshadow_a3an8v23 && boxshadow_a3an8v23 != undefined  ? boxshadow_a3an8v23 : "";
let g_color_v2r1rq23 = color_v2r1rq23 && color_v2r1rq23 != undefined  ? color_v2r1rq23 : "";
let g_background_c1206523 = background_c1206523 && background_c1206523 != undefined  ? background_c1206523 : "";
let g_border_lly75x23 = border_lly75x23 && border_lly75x23 != undefined  ? border_lly75x23 : "";
let g_boxshadow_9li4zh23 = boxshadow_9li4zh23 && boxshadow_9li4zh23 != undefined  ? boxshadow_9li4zh23 : "";
let g_dimension_19snqt23 = dimension_19snqt23 && dimension_19snqt23 != undefined  ? dimension_19snqt23 : "";
let g_dimension_mwp4jp23 = dimension_mwp4jp23 && dimension_mwp4jp23 != undefined  ? dimension_mwp4jp23 : "";
let g_color_0muwx223 = color_0muwx223 && color_0muwx223 != undefined && ( switcher_kt3fu723 ) ? color_0muwx223 : "";
let g_background_o7jfsv25 = background_o7jfsv25 && background_o7jfsv25 != undefined  ? background_o7jfsv25 : "";
let g_border_er162525 = border_er162525 && border_er162525 != undefined  ? border_er162525 : "";
let g_dimension_ib87jp25 = dimension_ib87jp25 && dimension_ib87jp25 != undefined  ? dimension_ib87jp25 : "";
let g_boxshadow_9w3omk25 = boxshadow_9w3omk25 && boxshadow_9w3omk25 != undefined  ? boxshadow_9w3omk25 : "";
let g_background_0p3b1025 = background_0p3b1025 && background_0p3b1025 != undefined  ? background_0p3b1025 : "";
let g_border_g6qu7h25 = border_g6qu7h25 && border_g6qu7h25 != undefined  ? border_g6qu7h25 : "";
let g_boxshadow_93rky525 = boxshadow_93rky525 && boxshadow_93rky525 != undefined  ? boxshadow_93rky525 : "";
let g_background_1mv9a325 = background_1mv9a325 && background_1mv9a325 != undefined  ? background_1mv9a325 : "";
let g_border_iioife25 = border_iioife25 && border_iioife25 != undefined  ? border_iioife25 : "";
let g_dimension_y2wbtt25 = dimension_y2wbtt25 && dimension_y2wbtt25 != undefined  ? dimension_y2wbtt25 : "";
let g_boxshadow_hyw5vm25 = boxshadow_hyw5vm25 && boxshadow_hyw5vm25 != undefined  ? boxshadow_hyw5vm25 : "";
let g_rawhtml_mldvc425 = rawhtml_mldvc425 && rawhtml_mldvc425 != undefined  ? rawhtml_mldvc425 : "";
let g_dimension_sjgysx24 = dimension_sjgysx24 && dimension_sjgysx24 != undefined  ? dimension_sjgysx24 : "";
let g_border_usissh23 = border_usissh23 && border_usissh23 != undefined  ? border_usissh23 : "";
let g_dimension_lj4gns23 = dimension_lj4gns23 && dimension_lj4gns23 != undefined  ? dimension_lj4gns23 : "";
let g_boxshadow_usizhc25 = boxshadow_usizhc25 && boxshadow_usizhc25 != undefined  ? boxshadow_usizhc25 : "";
            
let repeater_gkmzpc23_wv25 = "";
                            
repeater_gkmzpc23  && repeater_gkmzpc23.map((r_item, index) => {
                                
let grnp_text_ds10f723 = r_item.text_ds10f723  ? r_item.text_ds10f723 : "";
let grnp_textarea_c3jywx23 = r_item.textarea_c3jywx23  ? r_item.textarea_c3jywx23 : "";
let grnp_media_gtnxy423 = r_item?.media_gtnxy423?.url != undefined  ? r_item?.media_gtnxy423.url : "";
                                repeater_gkmzpc23_wv25 += `<div class="{loop-class} carousel-left-content" data-item="${index}" data-repeater_gkmzpc23="{repeater_gkmzpc23}">
               <span class="car-content-title">${grnp_text_ds10f723}</span>
               <p class="car-content-desc">${grnp_textarea_c3jywx23}</p>
            </div>`;
                            })
let repeater_gkmzpc23_8d25 = "";
                            
repeater_gkmzpc23  && repeater_gkmzpc23.map((r_item, index) => {
                                
let grnp_text_ds10f723 = r_item.text_ds10f723  ? r_item.text_ds10f723 : "";
let grnp_textarea_c3jywx23 = r_item.textarea_c3jywx23  ? r_item.textarea_c3jywx23 : "";
let grnp_media_gtnxy423 = r_item?.media_gtnxy423?.url != undefined  ? r_item?.media_gtnxy423.url : "";
                                repeater_gkmzpc23_8d25 += `<li class="{loop-class} carousel-right-image splide__slide" data-image="${index}" data-repeater_gkmzpc23="{repeater_gkmzpc23}">
                     <img src="${grnp_media_gtnxy423}">
                    </li>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_tedwgp24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-tabbed-images-carousel tabbed-auto-play-${g_switcher_kt3fu723} layout-reverse-${g_switcher_g8zjta23}" data-duration="${g_number_vfakqm23}">
    <div class="wkit-tabbed-wrapper">
        <div class="wkit-tabbed-left">
            ${repeater_gkmzpc23_wv25}
        </div>
        <div class="tabbed-splide splide">
            <div class="splide__track">
               <ul class="wkit-tabbed-right splide__list">
                    ${repeater_gkmzpc23_8d25}
                </ul>

            </div>
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
            repeater_gkmzpc23,
switcher_kt3fu723,
number_vfakqm23,
switcher_g8zjta23,
rawhtml_ags7w025,
dimension_cq5cog23,
typography_w5fjs223,
color_onttor23,
background_1ugzf623,
border_edxw8r23,
dimension_o2mdr723,
boxshadow_gfx23523,
color_gjbogd23,
background_m3aw1d23,
border_0semwo23,
boxshadow_j97m6y23,
color_u581v123,
background_qpms8o23,
border_fnx71323,
dimension_7kl3kf23,
boxshadow_nz483q23,
normalhover_wmxee423,
dimension_y4svm623,
typography_wl8gha23,
color_hjz2df23,
background_qsdslh23,
border_lffub123,
dimension_x6nwev23,
boxshadow_a3an8v23,
color_v2r1rq23,
background_c1206523,
border_lly75x23,
boxshadow_9li4zh23,
normalhover_vvjeoa23,
dimension_19snqt23,
dimension_mwp4jp23,
slider_d5cslh23,
color_0muwx223,
background_o7jfsv25,
border_er162525,
dimension_ib87jp25,
boxshadow_9w3omk25,
background_0p3b1025,
border_g6qu7h25,
boxshadow_93rky525,
background_1mv9a325,
border_iioife25,
dimension_y2wbtt25,
boxshadow_hyw5vm25,
normalhover_xmd9n625,
rawhtml_mldvc425,
dimension_sjgysx24,
slider_mhy6ql24,
slider_i1ailp25,
border_usissh23,
dimension_lj4gns23,
boxshadow_usizhc25,

            block_id,
        } = attributes;

        

        

        

        
let g_switcher_kt3fu723 = switcher_kt3fu723 && switcher_kt3fu723 != undefined  ? 'yes' : "";
let g_number_vfakqm23 = number_vfakqm23 && number_vfakqm23 != undefined && ( switcher_kt3fu723 ) ? number_vfakqm23 : "";
let g_switcher_g8zjta23 = switcher_g8zjta23 && switcher_g8zjta23 != undefined  ? 'yes' : "";
let g_rawhtml_ags7w025 = rawhtml_ags7w025 && rawhtml_ags7w025 != undefined  ? rawhtml_ags7w025 : "";
let g_dimension_cq5cog23 = dimension_cq5cog23 && dimension_cq5cog23 != undefined  ? dimension_cq5cog23 : "";
let g_typography_w5fjs223 = typography_w5fjs223 && typography_w5fjs223 != undefined  ? typography_w5fjs223 : "";
let g_color_onttor23 = color_onttor23 && color_onttor23 != undefined  ? color_onttor23 : "";
let g_background_1ugzf623 = background_1ugzf623 && background_1ugzf623 != undefined  ? background_1ugzf623 : "";
let g_border_edxw8r23 = border_edxw8r23 && border_edxw8r23 != undefined  ? border_edxw8r23 : "";
let g_dimension_o2mdr723 = dimension_o2mdr723 && dimension_o2mdr723 != undefined  ? dimension_o2mdr723 : "";
let g_boxshadow_gfx23523 = boxshadow_gfx23523 && boxshadow_gfx23523 != undefined  ? boxshadow_gfx23523 : "";
let g_color_gjbogd23 = color_gjbogd23 && color_gjbogd23 != undefined  ? color_gjbogd23 : "";
let g_background_m3aw1d23 = background_m3aw1d23 && background_m3aw1d23 != undefined  ? background_m3aw1d23 : "";
let g_border_0semwo23 = border_0semwo23 && border_0semwo23 != undefined  ? border_0semwo23 : "";
let g_boxshadow_j97m6y23 = boxshadow_j97m6y23 && boxshadow_j97m6y23 != undefined  ? boxshadow_j97m6y23 : "";
let g_color_u581v123 = color_u581v123 && color_u581v123 != undefined  ? color_u581v123 : "";
let g_background_qpms8o23 = background_qpms8o23 && background_qpms8o23 != undefined  ? background_qpms8o23 : "";
let g_border_fnx71323 = border_fnx71323 && border_fnx71323 != undefined  ? border_fnx71323 : "";
let g_dimension_7kl3kf23 = dimension_7kl3kf23 && dimension_7kl3kf23 != undefined  ? dimension_7kl3kf23 : "";
let g_boxshadow_nz483q23 = boxshadow_nz483q23 && boxshadow_nz483q23 != undefined  ? boxshadow_nz483q23 : "";
let g_dimension_y4svm623 = dimension_y4svm623 && dimension_y4svm623 != undefined  ? dimension_y4svm623 : "";
let g_typography_wl8gha23 = typography_wl8gha23 && typography_wl8gha23 != undefined  ? typography_wl8gha23 : "";
let g_color_hjz2df23 = color_hjz2df23 && color_hjz2df23 != undefined  ? color_hjz2df23 : "";
let g_background_qsdslh23 = background_qsdslh23 && background_qsdslh23 != undefined  ? background_qsdslh23 : "";
let g_border_lffub123 = border_lffub123 && border_lffub123 != undefined  ? border_lffub123 : "";
let g_dimension_x6nwev23 = dimension_x6nwev23 && dimension_x6nwev23 != undefined  ? dimension_x6nwev23 : "";
let g_boxshadow_a3an8v23 = boxshadow_a3an8v23 && boxshadow_a3an8v23 != undefined  ? boxshadow_a3an8v23 : "";
let g_color_v2r1rq23 = color_v2r1rq23 && color_v2r1rq23 != undefined  ? color_v2r1rq23 : "";
let g_background_c1206523 = background_c1206523 && background_c1206523 != undefined  ? background_c1206523 : "";
let g_border_lly75x23 = border_lly75x23 && border_lly75x23 != undefined  ? border_lly75x23 : "";
let g_boxshadow_9li4zh23 = boxshadow_9li4zh23 && boxshadow_9li4zh23 != undefined  ? boxshadow_9li4zh23 : "";
let g_dimension_19snqt23 = dimension_19snqt23 && dimension_19snqt23 != undefined  ? dimension_19snqt23 : "";
let g_dimension_mwp4jp23 = dimension_mwp4jp23 && dimension_mwp4jp23 != undefined  ? dimension_mwp4jp23 : "";
let g_color_0muwx223 = color_0muwx223 && color_0muwx223 != undefined && ( switcher_kt3fu723 ) ? color_0muwx223 : "";
let g_background_o7jfsv25 = background_o7jfsv25 && background_o7jfsv25 != undefined  ? background_o7jfsv25 : "";
let g_border_er162525 = border_er162525 && border_er162525 != undefined  ? border_er162525 : "";
let g_dimension_ib87jp25 = dimension_ib87jp25 && dimension_ib87jp25 != undefined  ? dimension_ib87jp25 : "";
let g_boxshadow_9w3omk25 = boxshadow_9w3omk25 && boxshadow_9w3omk25 != undefined  ? boxshadow_9w3omk25 : "";
let g_background_0p3b1025 = background_0p3b1025 && background_0p3b1025 != undefined  ? background_0p3b1025 : "";
let g_border_g6qu7h25 = border_g6qu7h25 && border_g6qu7h25 != undefined  ? border_g6qu7h25 : "";
let g_boxshadow_93rky525 = boxshadow_93rky525 && boxshadow_93rky525 != undefined  ? boxshadow_93rky525 : "";
let g_background_1mv9a325 = background_1mv9a325 && background_1mv9a325 != undefined  ? background_1mv9a325 : "";
let g_border_iioife25 = border_iioife25 && border_iioife25 != undefined  ? border_iioife25 : "";
let g_dimension_y2wbtt25 = dimension_y2wbtt25 && dimension_y2wbtt25 != undefined  ? dimension_y2wbtt25 : "";
let g_boxshadow_hyw5vm25 = boxshadow_hyw5vm25 && boxshadow_hyw5vm25 != undefined  ? boxshadow_hyw5vm25 : "";
let g_rawhtml_mldvc425 = rawhtml_mldvc425 && rawhtml_mldvc425 != undefined  ? rawhtml_mldvc425 : "";
let g_dimension_sjgysx24 = dimension_sjgysx24 && dimension_sjgysx24 != undefined  ? dimension_sjgysx24 : "";
let g_border_usissh23 = border_usissh23 && border_usissh23 != undefined  ? border_usissh23 : "";
let g_dimension_lj4gns23 = dimension_lj4gns23 && dimension_lj4gns23 != undefined  ? dimension_lj4gns23 : "";
let g_boxshadow_usizhc25 = boxshadow_usizhc25 && boxshadow_usizhc25 != undefined  ? boxshadow_usizhc25 : "";
        
let repeater_gkmzpc23_wv25 = "";
                            
repeater_gkmzpc23  && repeater_gkmzpc23.map((r_item, index) => {
                                
let grnp_text_ds10f723 = r_item.text_ds10f723  ? r_item.text_ds10f723 : "";
let grnp_textarea_c3jywx23 = r_item.textarea_c3jywx23  ? r_item.textarea_c3jywx23 : "";
let grnp_media_gtnxy423 = r_item?.media_gtnxy423?.url != undefined  ? r_item?.media_gtnxy423.url : "";
                                repeater_gkmzpc23_wv25 += `<div class="{loop-class} carousel-left-content" data-item="${index}" data-repeater_gkmzpc23="{repeater_gkmzpc23}">
               <span class="car-content-title">${grnp_text_ds10f723}</span>
               <p class="car-content-desc">${grnp_textarea_c3jywx23}</p>
            </div>`;
                            })
let repeater_gkmzpc23_8d25 = "";
                            
repeater_gkmzpc23  && repeater_gkmzpc23.map((r_item, index) => {
                                
let grnp_text_ds10f723 = r_item.text_ds10f723  ? r_item.text_ds10f723 : "";
let grnp_textarea_c3jywx23 = r_item.textarea_c3jywx23  ? r_item.textarea_c3jywx23 : "";
let grnp_media_gtnxy423 = r_item?.media_gtnxy423?.url != undefined  ? r_item?.media_gtnxy423.url : "";
                                repeater_gkmzpc23_8d25 += `<li class="{loop-class} carousel-right-image splide__slide" data-image="${index}" data-repeater_gkmzpc23="{repeater_gkmzpc23}">
                     <img src="${grnp_media_gtnxy423}">
                    </li>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-tedwgp24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_tedwgp24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-tabbed-images-carousel tabbed-auto-play-${g_switcher_kt3fu723} layout-reverse-${g_switcher_g8zjta23}" data-duration="${g_number_vfakqm23}">
    <div class="wkit-tabbed-wrapper">
        <div class="wkit-tabbed-left">
            ${repeater_gkmzpc23_wv25}
        </div>
        <div class="tabbed-splide splide">
            <div class="splide__track">
               <ul class="wkit-tabbed-right splide__list">
                    ${repeater_gkmzpc23_8d25}
                </ul>

            </div>
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
    new Tabbed_Images_Carousel_tedwgp24();