
    class Horizontal_Parallax_Slider_aockms24 {
        constructor() {
            this.Horizontal_Parallax_Slider_aockms24_bp81ig25();
        }
    
        Horizontal_Parallax_Slider_aockms24_bp81ig25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Text,Pmgc_TextArea,Pmgc_Repeater,Pmgc_Label_Heading,Pmgc_Select,Pmgc_Typography,Pmgc_Dimension,Pmgc_RadioAdvanced,Pmgc_Color,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Range,Pmgc_Background,
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
   
    registerBlockType('wdkit/wb-aockms24', {
        title: __('Horizontal Parallax Slider'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-sliders-h tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Horizontal Slider'),__('Navigation Slider'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_3t3vkg23Function = (unit, type) => {
                var g_slider_3t3vkg23_list = [];
                g_slider_3t3vkg23_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_3t3vkg23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_3t3vkg23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_3t3vkg23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_3t3vkg23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_3t3vkg23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_3t3vkg23_list[unit][type];
            };
const slider_dj2v0123Function = (unit, type) => {
                var g_slider_dj2v0123_list = [];
                g_slider_dj2v0123_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_dj2v0123_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_dj2v0123_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_dj2v0123_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_dj2v0123_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_dj2v0123_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_dj2v0123_list[unit][type];
            };
const slider_t29ndc23Function = (unit, type) => {
                var g_slider_t29ndc23_list = [];
                g_slider_t29ndc23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_t29ndc23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_t29ndc23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_t29ndc23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_t29ndc23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_t29ndc23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_t29ndc23_list[unit][type];
            };
const slider_6cmrwg23Function = (unit, type) => {
                var g_slider_6cmrwg23_list = [];
                g_slider_6cmrwg23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_6cmrwg23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_6cmrwg23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_6cmrwg23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_6cmrwg23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_6cmrwg23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_6cmrwg23_list[unit][type];
            };
const slider_m2j52i23Function = (unit, type) => {
                var g_slider_m2j52i23_list = [];
                g_slider_m2j52i23_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_m2j52i23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_m2j52i23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_m2j52i23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_m2j52i23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_m2j52i23_list['vh'] = { "type": "vh", "min": 0, "max": 100, "step": 1 };

                return g_slider_m2j52i23_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               repeater_xusa8q23,
heading_lvxens23,
number_762mii23,
number_nqdoo823,
number_ytr5xm23,
number_7xampd23,
number_cjvw9323,
number_jrqiju23,
select_yvs4bc23,
typography_lbldfg23,
dimension_s73szz23,
choose_5cm6x223,
choose_6s0es223,
color_j0mtyo23,
color_vtucw123,
border_y1akov23,
dimension_8ds8jt23,
boxshadow_ua51a623,
color_08gz6m23,
color_kdsel523,
border_tqk44623,
dimension_e6d2pz23,
boxshadow_72dhqb23,
color_la9z9k23,
color_q12b3b23,
border_hdlhhk23,
dimension_uugqj923,
boxshadow_2729f823,
normalhover_v6rllg23,
heading_vl8jm123,
typography_4up2m223,
slider_3t3vkg23,
heading_69y7v423,
typography_duilkw23,
dimension_ty1a7123,
choose_z52kxr23,
choose_d8pdmx23,
heading_lijhqz23,
background_825k2m23,
color_qghma023,
color_b3gw1x23,
border_y8b9pb23,
dimension_l8p22p23,
boxshadow_w4xd7o23,
color_0beo7w23,
color_9do1xc23,
border_hudn6z23,
dimension_1fhooe23,
boxshadow_hfpgih23,
normalhover_hxhhpa23,
slider_dj2v0123,
slider_t29ndc23,
slider_6cmrwg23,
color_yas8wd23,
background_nts6e223,
border_fxvtfw23,
dimension_47uh7p23,
boxshadow_649gyd23,
color_g2oqdt23,
background_f2y8be23,
border_9dwo0k23,
dimension_evf5lq23,
boxshadow_d91slq23,
normalhover_8oda6723,
slider_m2j52i23,

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
                        main_function_aockms24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_aockms24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let crtSlider = $scope[0].querySelector('.wkit-hor-par-slider');

var unid = Math.random().toString(32).slice(2);

let mainSliderC = crtSlider.querySelector('.wkit-main-slider');
let navSliderC = crtSlider.querySelector('.wkit-nav-slider');

let slideNextC = crtSlider.querySelector('.swiper-button-next');
let slidePrevC = crtSlider.querySelector('.swiper-button-prev');

mainSliderC.classList.add('main-slider-'+unid);
navSliderC.classList.add('nav-slider-'+unid);
slideNextC.classList.add('slide-next-'+unid);
slidePrevC.classList.add('slide-prev-'+unid);

let mainSliderSelector = '.main-slider-'+unid,
    navSliderSelector = '.nav-slider-'+unid,
    interleaveOffset = 0.5;
    
let deskSPV = (crtSlider.getAttribute('data-spv-desk')) ? Number(crtSlider.getAttribute('data-spv-desk')) : 6,
    tabSPV = (crtSlider.getAttribute('data-spv-tab')) ? Number(crtSlider.getAttribute('data-spv-tab')) : 6,
    mobSPV = (crtSlider.getAttribute('data-spv-mob')) ? Number(crtSlider.getAttribute('data-spv-mob')) : 6,
    deskSSB = (crtSlider.getAttribute('data-ssb-desk')) ? Number(crtSlider.getAttribute('data-ssb-desk')) : 6,
    tabSSB = (crtSlider.getAttribute('data-ssb-tab')) ? Number(crtSlider.getAttribute('data-ssb-tab')) : 6,
    mobSSB = (crtSlider.getAttribute('data-ssb-mob')) ? Number(crtSlider.getAttribute('data-ssb-mob')) : 6;
    
    let screenWidth = screen.width;
    
    let spaceBet = 6, slidePerV = 6, loopAdiS = 12;
    if (screenWidth >= 1024) {
        spaceBet = deskSSB;
        slidePerV = deskSPV;
        loopAdiS = deskSPV * 2;
    } else if (screenWidth < 1024 && screenWidth >= 768) {
        spaceBet = tabSSB;
        slidePerV = tabSPV;
        loopAdiS = tabSPV * 2;
    } else if (screenWidth < 768) {
        spaceBet = mobSSB;
        slidePerV = mobSPV;
        loopAdiS = mobSPV * 2;
    }

// Main Slider

    setTimeout(()=>{
        let mainSliderOptions = {
          loop: true,
          speed:1000,
          autoplay:{
            delay:3000
          },
          loopAdditionalSlides: 10,
          grabCursor: true,
          watchSlidesProgress: true,
          navigation: {
            nextEl: '.slide-next-'+unid,
            prevEl: '.slide-prev-'+unid,
          },
          on: {
            slideChangeTransitionEnd: function(){
              let swiper = this,
                  captions = swiper.el.querySelectorAll('.wkit-slide-desc');
              for (let i = 0; i < captions.length; ++i) {
                captions[i].classList.remove('show');
              }
              let getCurr = swiper.el.querySelectorAll('[data-swiper-slide-index="'+swiper.realIndex+'"]');
              getCurr.forEach((gc)=>{
                  gc.querySelector('.wkit-slide-desc').classList.add('show');
              })
            },
            progress: function(){
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                let slideProgress = swiper.slides[i].progress,
                    innerOffset = swiper.width * interleaveOffset,
                    innerTranslate = slideProgress * innerOffset;
               
                swiper.slides[i].querySelector(".slide-bg-img").style.transform =
                  "translateX(" + innerTranslate + "px)";
              }
            },
            touchStart: function() {
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                swiper.slides[i].style.transition = "";
              }
            },
            setTransition: function(speed) {
              let swiper = this;
              for (let i = 0; i < swiper.slides.length; i++) {
                swiper.slides[i].style.transition = speed + "ms";
                swiper.slides[i].querySelector(".slide-bg-img").style.transition =
                  speed + "ms";
              }
            }
          }
        };
        let mainSlider = new Swiper(mainSliderSelector, mainSliderOptions);
          
        let navSliderOptions = {
            loop: true,
            loopAdditionalSlides: loopAdiS,
            speed:1000,
            spaceBetween: spaceBet,
            slidesPerView: slidePerV,
            centeredSlides : true,
            touchRatio: 1,
            slideToClickedSlide: true,
            direction: 'vertical',
            on: {
                click: function(){
                    mainSlider.autoplay.stop();
                }
            }
        };
        
        let navSlider = new Swiper(navSliderSelector, navSliderOptions);
        mainSlider.controller.control = navSlider;
        navSlider.controller.control = mainSlider;
    }, 100);
    
// Navigation Slider


            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Slides`),
            value: repeater_xusa8q23,
            attributeName: 'repeater_xusa8q23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_xusa8q23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_nd5c1223,
                
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_nd5c1223 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_wwxxt823,
                
                
                
                separator:"default",
                
                onChange: v => { value.text_wwxxt823 = v; onChange(value); },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: value.textarea_2d6fdp23,
                
                onChange: v => { value.textarea_2d6fdp23 = v; onChange(value); },
            }),

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Slide Per View"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Slide Per View value should be adjusted as per the given repeaters`),
            value: heading_lvxens23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Desktop`),
                type: "number",
                value: number_762mii23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_762mii23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tablet`),
                type: "number",
                value: number_nqdoo823,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_nqdoo823: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Mobile`),
                type: "number",
                value: number_ytr5xm23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_ytr5xm23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Slide Space Between"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Desktop`),
                type: "number",
                value: number_7xampd23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_7xampd23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tablet`),
                type: "number",
                value: number_cjvw9323,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_cjvw9323: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Mobile`),
                type: "number",
                value: number_jrqiju23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_jrqiju23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Side Bar Position`),
                options:[['left',__('Left')],['right',__('Right')],],
                separator:"default",
                
                
                value: select_yvs4bc23,
                onChange: (value) => {setAttributes({ select_yvs4bc23: value }) },
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Side Bar"), initialOpen: true },
 React.createElement(Pmgc_Typography, {
            
            value: typography_lbldfg23,
            onChange: (value) => setAttributes({ typography_lbldfg23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_s73szz23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_s73szz23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Horizontal Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left ', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_5cm6x223,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_5cm6x223: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Vertical Alignment`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_6s0es223,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_6s0es223: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_j0mtyo23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_j0mtyo23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Overlay Color`),
            value: color_vtucw123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vtucw123: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_y1akov23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_y1akov23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_8ds8jt23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_8ds8jt23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_ua51a623,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_ua51a623: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_08gz6m23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_08gz6m23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Overlay Color`),
            value: color_kdsel523,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kdsel523: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_tqk44623,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_tqk44623: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_e6d2pz23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_e6d2pz23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_72dhqb23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_72dhqb23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_la9z9k23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_la9z9k23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Overlay Color`),
            value: color_q12b3b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_q12b3b23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_hdlhhk23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_hdlhhk23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_uugqj923,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_uugqj923: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_2729f823,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_2729f823: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Content Slider"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_vl8jm123,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_4up2m223,
            onChange: (value) => setAttributes({ typography_4up2m223: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Space`),
            separator:'default',
            value: slider_3t3vkg23,
            
            min: slider_3t3vkg23 && slider_3t3vkg23.unit ? slider_3t3vkg23Function(slider_3t3vkg23.unit, 'min') : 0,
            max: slider_3t3vkg23 && slider_3t3vkg23.unit ? slider_3t3vkg23Function(slider_3t3vkg23.unit, 'max') : 100,
            step: slider_3t3vkg23 && slider_3t3vkg23.unit ? slider_3t3vkg23Function(slider_3t3vkg23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_3t3vkg23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_69y7v423,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_duilkw23,
            onChange: (value) => setAttributes({ typography_duilkw23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ty1a7123,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ty1a7123: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Horizontal Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left ', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_z52kxr23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_z52kxr23: value }),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Vertical Alignment`),
            separator:'default',
            
            options : [{ label: __('Top'), value: 'flex-start', title: __('Top'), icon: 'fas fa-arrow-up', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Bottom'), value: 'flex-end', title: __('Bottom'), icon: 'fas fa-arrow-down', svg: '' }, 
],
            value: choose_d8pdmx23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_d8pdmx23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Overlay`),
            value: heading_lijhqz23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_825k2m23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_825k2m23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_qghma023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qghma023: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: color_b3gw1x23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_b3gw1x23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_y8b9pb23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_y8b9pb23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_l8p22p23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_l8p22p23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_w4xd7o23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_w4xd7o23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: color_0beo7w23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0beo7w23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: color_9do1xc23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9do1xc23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_hudn6z23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_hudn6z23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_1fhooe23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_1fhooe23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_hfpgih23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_hfpgih23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Arrow"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_dj2v0123,
            
            min: slider_dj2v0123 && slider_dj2v0123.unit ? slider_dj2v0123Function(slider_dj2v0123.unit, 'min') : 0,
            max: slider_dj2v0123 && slider_dj2v0123.unit ? slider_dj2v0123Function(slider_dj2v0123.unit, 'max') : 100,
            step: slider_dj2v0123 && slider_dj2v0123.unit ? slider_dj2v0123Function(slider_dj2v0123.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_dj2v0123: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_t29ndc23,
            
            min: slider_t29ndc23 && slider_t29ndc23.unit ? slider_t29ndc23Function(slider_t29ndc23.unit, 'min') : 0,
            max: slider_t29ndc23 && slider_t29ndc23.unit ? slider_t29ndc23Function(slider_t29ndc23.unit, 'max') : 100,
            step: slider_t29ndc23 && slider_t29ndc23.unit ? slider_t29ndc23Function(slider_t29ndc23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_t29ndc23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_6cmrwg23,
            
            min: slider_6cmrwg23 && slider_6cmrwg23.unit ? slider_6cmrwg23Function(slider_6cmrwg23.unit, 'min') : 0,
            max: slider_6cmrwg23 && slider_6cmrwg23.unit ? slider_6cmrwg23Function(slider_6cmrwg23.unit, 'max') : 100,
            step: slider_6cmrwg23 && slider_6cmrwg23.unit ? slider_6cmrwg23Function(slider_6cmrwg23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_6cmrwg23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_yas8wd23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yas8wd23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_nts6e223,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_nts6e223: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_fxvtfw23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_fxvtfw23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_47uh7p23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_47uh7p23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_649gyd23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_649gyd23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_g2oqdt23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_g2oqdt23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_f2y8be23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_f2y8be23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_9dwo0k23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_9dwo0k23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_evf5lq23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_evf5lq23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_d91slq23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_d91slq23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Extra Option"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_m2j52i23,
            
            min: slider_m2j52i23 && slider_m2j52i23.unit ? slider_m2j52i23Function(slider_m2j52i23.unit, 'min') : 0,
            max: slider_m2j52i23 && slider_m2j52i23.unit ? slider_m2j52i23Function(slider_m2j52i23.unit, 'max') : 100,
            step: slider_m2j52i23 && slider_m2j52i23.unit ? slider_m2j52i23Function(slider_m2j52i23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_m2j52i23: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-aockms24", block_id, false, props.clientId);
                }
            }

            
let g_heading_lvxens23 = heading_lvxens23 && heading_lvxens23 != undefined  ? heading_lvxens23 : "";
let g_number_762mii23 = number_762mii23 && number_762mii23 != undefined  ? number_762mii23 : "";
let g_number_nqdoo823 = number_nqdoo823 && number_nqdoo823 != undefined  ? number_nqdoo823 : "";
let g_number_ytr5xm23 = number_ytr5xm23 && number_ytr5xm23 != undefined  ? number_ytr5xm23 : "";
let g_number_7xampd23 = number_7xampd23 && number_7xampd23 != undefined  ? number_7xampd23 : "";
let g_number_cjvw9323 = number_cjvw9323 && number_cjvw9323 != undefined  ? number_cjvw9323 : "";
let g_number_jrqiju23 = number_jrqiju23 && number_jrqiju23 != undefined  ? number_jrqiju23 : "";
let g_select_yvs4bc23 = select_yvs4bc23 && select_yvs4bc23 != undefined  ? select_yvs4bc23 : "";
let g_typography_lbldfg23 = typography_lbldfg23 && typography_lbldfg23 != undefined  ? typography_lbldfg23 : "";
let g_dimension_s73szz23 = dimension_s73szz23 && dimension_s73szz23 != undefined  ? dimension_s73szz23 : "";
let g_choose_5cm6x223 = choose_5cm6x223 && choose_5cm6x223 != undefined  ? choose_5cm6x223 : "";
let g_choose_6s0es223 = choose_6s0es223 && choose_6s0es223 != undefined  ? choose_6s0es223 : "";
let g_color_j0mtyo23 = color_j0mtyo23 && color_j0mtyo23 != undefined  ? color_j0mtyo23 : "";
let g_color_vtucw123 = color_vtucw123 && color_vtucw123 != undefined  ? color_vtucw123 : "";
let g_border_y1akov23 = border_y1akov23 && border_y1akov23 != undefined  ? border_y1akov23 : "";
let g_dimension_8ds8jt23 = dimension_8ds8jt23 && dimension_8ds8jt23 != undefined  ? dimension_8ds8jt23 : "";
let g_boxshadow_ua51a623 = boxshadow_ua51a623 && boxshadow_ua51a623 != undefined  ? boxshadow_ua51a623 : "";
let g_color_08gz6m23 = color_08gz6m23 && color_08gz6m23 != undefined  ? color_08gz6m23 : "";
let g_color_kdsel523 = color_kdsel523 && color_kdsel523 != undefined  ? color_kdsel523 : "";
let g_border_tqk44623 = border_tqk44623 && border_tqk44623 != undefined  ? border_tqk44623 : "";
let g_dimension_e6d2pz23 = dimension_e6d2pz23 && dimension_e6d2pz23 != undefined  ? dimension_e6d2pz23 : "";
let g_boxshadow_72dhqb23 = boxshadow_72dhqb23 && boxshadow_72dhqb23 != undefined  ? boxshadow_72dhqb23 : "";
let g_color_la9z9k23 = color_la9z9k23 && color_la9z9k23 != undefined  ? color_la9z9k23 : "";
let g_color_q12b3b23 = color_q12b3b23 && color_q12b3b23 != undefined  ? color_q12b3b23 : "";
let g_border_hdlhhk23 = border_hdlhhk23 && border_hdlhhk23 != undefined  ? border_hdlhhk23 : "";
let g_dimension_uugqj923 = dimension_uugqj923 && dimension_uugqj923 != undefined  ? dimension_uugqj923 : "";
let g_boxshadow_2729f823 = boxshadow_2729f823 && boxshadow_2729f823 != undefined  ? boxshadow_2729f823 : "";
let g_heading_vl8jm123 = heading_vl8jm123 && heading_vl8jm123 != undefined  ? heading_vl8jm123 : "";
let g_typography_4up2m223 = typography_4up2m223 && typography_4up2m223 != undefined  ? typography_4up2m223 : "";
let g_heading_69y7v423 = heading_69y7v423 && heading_69y7v423 != undefined  ? heading_69y7v423 : "";
let g_typography_duilkw23 = typography_duilkw23 && typography_duilkw23 != undefined  ? typography_duilkw23 : "";
let g_dimension_ty1a7123 = dimension_ty1a7123 && dimension_ty1a7123 != undefined  ? dimension_ty1a7123 : "";
let g_choose_z52kxr23 = choose_z52kxr23 && choose_z52kxr23 != undefined  ? choose_z52kxr23 : "";
let g_choose_d8pdmx23 = choose_d8pdmx23 && choose_d8pdmx23 != undefined  ? choose_d8pdmx23 : "";
let g_heading_lijhqz23 = heading_lijhqz23 && heading_lijhqz23 != undefined  ? heading_lijhqz23 : "";
let g_background_825k2m23 = background_825k2m23 && background_825k2m23 != undefined  ? background_825k2m23 : "";
let g_color_qghma023 = color_qghma023 && color_qghma023 != undefined  ? color_qghma023 : "";
let g_color_b3gw1x23 = color_b3gw1x23 && color_b3gw1x23 != undefined  ? color_b3gw1x23 : "";
let g_border_y8b9pb23 = border_y8b9pb23 && border_y8b9pb23 != undefined  ? border_y8b9pb23 : "";
let g_dimension_l8p22p23 = dimension_l8p22p23 && dimension_l8p22p23 != undefined  ? dimension_l8p22p23 : "";
let g_boxshadow_w4xd7o23 = boxshadow_w4xd7o23 && boxshadow_w4xd7o23 != undefined  ? boxshadow_w4xd7o23 : "";
let g_color_0beo7w23 = color_0beo7w23 && color_0beo7w23 != undefined  ? color_0beo7w23 : "";
let g_color_9do1xc23 = color_9do1xc23 && color_9do1xc23 != undefined  ? color_9do1xc23 : "";
let g_border_hudn6z23 = border_hudn6z23 && border_hudn6z23 != undefined  ? border_hudn6z23 : "";
let g_dimension_1fhooe23 = dimension_1fhooe23 && dimension_1fhooe23 != undefined  ? dimension_1fhooe23 : "";
let g_boxshadow_hfpgih23 = boxshadow_hfpgih23 && boxshadow_hfpgih23 != undefined  ? boxshadow_hfpgih23 : "";
let g_color_yas8wd23 = color_yas8wd23 && color_yas8wd23 != undefined  ? color_yas8wd23 : "";
let g_background_nts6e223 = background_nts6e223 && background_nts6e223 != undefined  ? background_nts6e223 : "";
let g_border_fxvtfw23 = border_fxvtfw23 && border_fxvtfw23 != undefined  ? border_fxvtfw23 : "";
let g_dimension_47uh7p23 = dimension_47uh7p23 && dimension_47uh7p23 != undefined  ? dimension_47uh7p23 : "";
let g_boxshadow_649gyd23 = boxshadow_649gyd23 && boxshadow_649gyd23 != undefined  ? boxshadow_649gyd23 : "";
let g_color_g2oqdt23 = color_g2oqdt23 && color_g2oqdt23 != undefined  ? color_g2oqdt23 : "";
let g_background_f2y8be23 = background_f2y8be23 && background_f2y8be23 != undefined  ? background_f2y8be23 : "";
let g_border_9dwo0k23 = border_9dwo0k23 && border_9dwo0k23 != undefined  ? border_9dwo0k23 : "";
let g_dimension_evf5lq23 = dimension_evf5lq23 && dimension_evf5lq23 != undefined  ? dimension_evf5lq23 : "";
let g_boxshadow_d91slq23 = boxshadow_d91slq23 && boxshadow_d91slq23 != undefined  ? boxshadow_d91slq23 : "";
            
let repeater_xusa8q23_7f25 = "";
                            
repeater_xusa8q23  && repeater_xusa8q23.map((r_item, index) => {
                                
let grnp_media_nd5c1223 = r_item?.media_nd5c1223?.url != undefined  ? r_item?.media_nd5c1223.url : "";
let grnp_text_wwxxt823 = r_item.text_wwxxt823  ? r_item.text_wwxxt823 : "";
let grnp_textarea_2d6fdp23 = r_item.textarea_2d6fdp23  ? r_item.textarea_2d6fdp23 : "";
                                repeater_xusa8q23_7f25 += `<div class="tp-repeater-item-${r_item._key} swiper-slide" data-repeater_xusa8q23="{repeater_xusa8q23}">
                <div class="slide-bg-img" style="background-image: url(${grnp_media_nd5c1223})"></div>
                <div class="main-slide-overlay"></div>
                <div class="wkit-slider-content">
                    <span class="wkit-slide-title" data-title="${grnp_text_wwxxt823}">${grnp_text_wwxxt823}</span>
                    <p class="wkit-slide-desc" data-desc="${grnp_textarea_2d6fdp23}">${grnp_textarea_2d6fdp23}</p>
                </div>
            </div>`;
                            })
let repeater_xusa8q23_ln25 = "";
                            
repeater_xusa8q23  && repeater_xusa8q23.map((r_item, index) => {
                                
let grnp_media_nd5c1223 = r_item?.media_nd5c1223?.url != undefined  ? r_item?.media_nd5c1223.url : "";
let grnp_text_wwxxt823 = r_item.text_wwxxt823  ? r_item.text_wwxxt823 : "";
let grnp_textarea_2d6fdp23 = r_item.textarea_2d6fdp23  ? r_item.textarea_2d6fdp23 : "";
                                repeater_xusa8q23_ln25 += `<div class="tp-repeater-item-${r_item._key} swiper-slide" data-repeater_xusa8q23="{repeater_xusa8q23}">
                <div class="slide-bg-img" style="background-image:url(${grnp_media_nd5c1223})"></div>
                <div class="nav-slide-overlay"></div>
                <div class="wkit-slider-content">
                    <span class="wkit-slide-title" data-title="${grnp_text_wwxxt823}">${grnp_text_wwxxt823}</span>
                </div>
            </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_aockms24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-hor-par-slider side-bar-pos-${g_select_yvs4bc23}" data-spv-desk="${g_number_762mii23}" data-spv-tab="${g_number_nqdoo823}" data-spv-mob="${g_number_ytr5xm23}" data-ssb-desk="${g_number_7xampd23}" data-ssb-tab="${g_number_cjvw9323}" data-ssb-mob="${g_number_jrqiju23}">
    <div class="wkit-slider-wrap wkit-main-slider">
        <div class="swiper-wrapper">
            ${repeater_xusa8q23_7f25}
        </div>
        <div class="swiper-button-prev swiper-button-white"></div>
        <div class="swiper-button-next swiper-button-white"></div>
    </div>
    <div class="wkit-slider-wrap wkit-nav-slider">
        <div class="swiper-wrapper" role="navigation">
            ${repeater_xusa8q23_ln25}
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
            repeater_xusa8q23,
heading_lvxens23,
number_762mii23,
number_nqdoo823,
number_ytr5xm23,
number_7xampd23,
number_cjvw9323,
number_jrqiju23,
select_yvs4bc23,
typography_lbldfg23,
dimension_s73szz23,
choose_5cm6x223,
choose_6s0es223,
color_j0mtyo23,
color_vtucw123,
border_y1akov23,
dimension_8ds8jt23,
boxshadow_ua51a623,
color_08gz6m23,
color_kdsel523,
border_tqk44623,
dimension_e6d2pz23,
boxshadow_72dhqb23,
color_la9z9k23,
color_q12b3b23,
border_hdlhhk23,
dimension_uugqj923,
boxshadow_2729f823,
normalhover_v6rllg23,
heading_vl8jm123,
typography_4up2m223,
slider_3t3vkg23,
heading_69y7v423,
typography_duilkw23,
dimension_ty1a7123,
choose_z52kxr23,
choose_d8pdmx23,
heading_lijhqz23,
background_825k2m23,
color_qghma023,
color_b3gw1x23,
border_y8b9pb23,
dimension_l8p22p23,
boxshadow_w4xd7o23,
color_0beo7w23,
color_9do1xc23,
border_hudn6z23,
dimension_1fhooe23,
boxshadow_hfpgih23,
normalhover_hxhhpa23,
slider_dj2v0123,
slider_t29ndc23,
slider_6cmrwg23,
color_yas8wd23,
background_nts6e223,
border_fxvtfw23,
dimension_47uh7p23,
boxshadow_649gyd23,
color_g2oqdt23,
background_f2y8be23,
border_9dwo0k23,
dimension_evf5lq23,
boxshadow_d91slq23,
normalhover_8oda6723,
slider_m2j52i23,

            block_id,
        } = attributes;

        

        

        

        
let g_heading_lvxens23 = heading_lvxens23 && heading_lvxens23 != undefined  ? heading_lvxens23 : "";
let g_number_762mii23 = number_762mii23 && number_762mii23 != undefined  ? number_762mii23 : "";
let g_number_nqdoo823 = number_nqdoo823 && number_nqdoo823 != undefined  ? number_nqdoo823 : "";
let g_number_ytr5xm23 = number_ytr5xm23 && number_ytr5xm23 != undefined  ? number_ytr5xm23 : "";
let g_number_7xampd23 = number_7xampd23 && number_7xampd23 != undefined  ? number_7xampd23 : "";
let g_number_cjvw9323 = number_cjvw9323 && number_cjvw9323 != undefined  ? number_cjvw9323 : "";
let g_number_jrqiju23 = number_jrqiju23 && number_jrqiju23 != undefined  ? number_jrqiju23 : "";
let g_select_yvs4bc23 = select_yvs4bc23 && select_yvs4bc23 != undefined  ? select_yvs4bc23 : "";
let g_typography_lbldfg23 = typography_lbldfg23 && typography_lbldfg23 != undefined  ? typography_lbldfg23 : "";
let g_dimension_s73szz23 = dimension_s73szz23 && dimension_s73szz23 != undefined  ? dimension_s73szz23 : "";
let g_choose_5cm6x223 = choose_5cm6x223 && choose_5cm6x223 != undefined  ? choose_5cm6x223 : "";
let g_choose_6s0es223 = choose_6s0es223 && choose_6s0es223 != undefined  ? choose_6s0es223 : "";
let g_color_j0mtyo23 = color_j0mtyo23 && color_j0mtyo23 != undefined  ? color_j0mtyo23 : "";
let g_color_vtucw123 = color_vtucw123 && color_vtucw123 != undefined  ? color_vtucw123 : "";
let g_border_y1akov23 = border_y1akov23 && border_y1akov23 != undefined  ? border_y1akov23 : "";
let g_dimension_8ds8jt23 = dimension_8ds8jt23 && dimension_8ds8jt23 != undefined  ? dimension_8ds8jt23 : "";
let g_boxshadow_ua51a623 = boxshadow_ua51a623 && boxshadow_ua51a623 != undefined  ? boxshadow_ua51a623 : "";
let g_color_08gz6m23 = color_08gz6m23 && color_08gz6m23 != undefined  ? color_08gz6m23 : "";
let g_color_kdsel523 = color_kdsel523 && color_kdsel523 != undefined  ? color_kdsel523 : "";
let g_border_tqk44623 = border_tqk44623 && border_tqk44623 != undefined  ? border_tqk44623 : "";
let g_dimension_e6d2pz23 = dimension_e6d2pz23 && dimension_e6d2pz23 != undefined  ? dimension_e6d2pz23 : "";
let g_boxshadow_72dhqb23 = boxshadow_72dhqb23 && boxshadow_72dhqb23 != undefined  ? boxshadow_72dhqb23 : "";
let g_color_la9z9k23 = color_la9z9k23 && color_la9z9k23 != undefined  ? color_la9z9k23 : "";
let g_color_q12b3b23 = color_q12b3b23 && color_q12b3b23 != undefined  ? color_q12b3b23 : "";
let g_border_hdlhhk23 = border_hdlhhk23 && border_hdlhhk23 != undefined  ? border_hdlhhk23 : "";
let g_dimension_uugqj923 = dimension_uugqj923 && dimension_uugqj923 != undefined  ? dimension_uugqj923 : "";
let g_boxshadow_2729f823 = boxshadow_2729f823 && boxshadow_2729f823 != undefined  ? boxshadow_2729f823 : "";
let g_heading_vl8jm123 = heading_vl8jm123 && heading_vl8jm123 != undefined  ? heading_vl8jm123 : "";
let g_typography_4up2m223 = typography_4up2m223 && typography_4up2m223 != undefined  ? typography_4up2m223 : "";
let g_heading_69y7v423 = heading_69y7v423 && heading_69y7v423 != undefined  ? heading_69y7v423 : "";
let g_typography_duilkw23 = typography_duilkw23 && typography_duilkw23 != undefined  ? typography_duilkw23 : "";
let g_dimension_ty1a7123 = dimension_ty1a7123 && dimension_ty1a7123 != undefined  ? dimension_ty1a7123 : "";
let g_choose_z52kxr23 = choose_z52kxr23 && choose_z52kxr23 != undefined  ? choose_z52kxr23 : "";
let g_choose_d8pdmx23 = choose_d8pdmx23 && choose_d8pdmx23 != undefined  ? choose_d8pdmx23 : "";
let g_heading_lijhqz23 = heading_lijhqz23 && heading_lijhqz23 != undefined  ? heading_lijhqz23 : "";
let g_background_825k2m23 = background_825k2m23 && background_825k2m23 != undefined  ? background_825k2m23 : "";
let g_color_qghma023 = color_qghma023 && color_qghma023 != undefined  ? color_qghma023 : "";
let g_color_b3gw1x23 = color_b3gw1x23 && color_b3gw1x23 != undefined  ? color_b3gw1x23 : "";
let g_border_y8b9pb23 = border_y8b9pb23 && border_y8b9pb23 != undefined  ? border_y8b9pb23 : "";
let g_dimension_l8p22p23 = dimension_l8p22p23 && dimension_l8p22p23 != undefined  ? dimension_l8p22p23 : "";
let g_boxshadow_w4xd7o23 = boxshadow_w4xd7o23 && boxshadow_w4xd7o23 != undefined  ? boxshadow_w4xd7o23 : "";
let g_color_0beo7w23 = color_0beo7w23 && color_0beo7w23 != undefined  ? color_0beo7w23 : "";
let g_color_9do1xc23 = color_9do1xc23 && color_9do1xc23 != undefined  ? color_9do1xc23 : "";
let g_border_hudn6z23 = border_hudn6z23 && border_hudn6z23 != undefined  ? border_hudn6z23 : "";
let g_dimension_1fhooe23 = dimension_1fhooe23 && dimension_1fhooe23 != undefined  ? dimension_1fhooe23 : "";
let g_boxshadow_hfpgih23 = boxshadow_hfpgih23 && boxshadow_hfpgih23 != undefined  ? boxshadow_hfpgih23 : "";
let g_color_yas8wd23 = color_yas8wd23 && color_yas8wd23 != undefined  ? color_yas8wd23 : "";
let g_background_nts6e223 = background_nts6e223 && background_nts6e223 != undefined  ? background_nts6e223 : "";
let g_border_fxvtfw23 = border_fxvtfw23 && border_fxvtfw23 != undefined  ? border_fxvtfw23 : "";
let g_dimension_47uh7p23 = dimension_47uh7p23 && dimension_47uh7p23 != undefined  ? dimension_47uh7p23 : "";
let g_boxshadow_649gyd23 = boxshadow_649gyd23 && boxshadow_649gyd23 != undefined  ? boxshadow_649gyd23 : "";
let g_color_g2oqdt23 = color_g2oqdt23 && color_g2oqdt23 != undefined  ? color_g2oqdt23 : "";
let g_background_f2y8be23 = background_f2y8be23 && background_f2y8be23 != undefined  ? background_f2y8be23 : "";
let g_border_9dwo0k23 = border_9dwo0k23 && border_9dwo0k23 != undefined  ? border_9dwo0k23 : "";
let g_dimension_evf5lq23 = dimension_evf5lq23 && dimension_evf5lq23 != undefined  ? dimension_evf5lq23 : "";
let g_boxshadow_d91slq23 = boxshadow_d91slq23 && boxshadow_d91slq23 != undefined  ? boxshadow_d91slq23 : "";
        
let repeater_xusa8q23_7f25 = "";
                            
repeater_xusa8q23  && repeater_xusa8q23.map((r_item, index) => {
                                
let grnp_media_nd5c1223 = r_item?.media_nd5c1223?.url != undefined  ? r_item?.media_nd5c1223.url : "";
let grnp_text_wwxxt823 = r_item.text_wwxxt823  ? r_item.text_wwxxt823 : "";
let grnp_textarea_2d6fdp23 = r_item.textarea_2d6fdp23  ? r_item.textarea_2d6fdp23 : "";
                                repeater_xusa8q23_7f25 += `<div class="tp-repeater-item-${r_item._key} swiper-slide" data-repeater_xusa8q23="{repeater_xusa8q23}">
                <div class="slide-bg-img" style="background-image: url(${grnp_media_nd5c1223})"></div>
                <div class="main-slide-overlay"></div>
                <div class="wkit-slider-content">
                    <span class="wkit-slide-title" data-title="${grnp_text_wwxxt823}">${grnp_text_wwxxt823}</span>
                    <p class="wkit-slide-desc" data-desc="${grnp_textarea_2d6fdp23}">${grnp_textarea_2d6fdp23}</p>
                </div>
            </div>`;
                            })
let repeater_xusa8q23_ln25 = "";
                            
repeater_xusa8q23  && repeater_xusa8q23.map((r_item, index) => {
                                
let grnp_media_nd5c1223 = r_item?.media_nd5c1223?.url != undefined  ? r_item?.media_nd5c1223.url : "";
let grnp_text_wwxxt823 = r_item.text_wwxxt823  ? r_item.text_wwxxt823 : "";
let grnp_textarea_2d6fdp23 = r_item.textarea_2d6fdp23  ? r_item.textarea_2d6fdp23 : "";
                                repeater_xusa8q23_ln25 += `<div class="tp-repeater-item-${r_item._key} swiper-slide" data-repeater_xusa8q23="{repeater_xusa8q23}">
                <div class="slide-bg-img" style="background-image:url(${grnp_media_nd5c1223})"></div>
                <div class="nav-slide-overlay"></div>
                <div class="wkit-slider-content">
                    <span class="wkit-slide-title" data-title="${grnp_text_wwxxt823}">${grnp_text_wwxxt823}</span>
                </div>
            </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-aockms24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_aockms24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-hor-par-slider side-bar-pos-${g_select_yvs4bc23}" data-spv-desk="${g_number_762mii23}" data-spv-tab="${g_number_nqdoo823}" data-spv-mob="${g_number_ytr5xm23}" data-ssb-desk="${g_number_7xampd23}" data-ssb-tab="${g_number_cjvw9323}" data-ssb-mob="${g_number_jrqiju23}">
    <div class="wkit-slider-wrap wkit-main-slider">
        <div class="swiper-wrapper">
            ${repeater_xusa8q23_7f25}
        </div>
        <div class="swiper-button-prev swiper-button-white"></div>
        <div class="swiper-button-next swiper-button-white"></div>
    </div>
    <div class="wkit-slider-wrap wkit-nav-slider">
        <div class="swiper-wrapper" role="navigation">
            ${repeater_xusa8q23_ln25}
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
    new Horizontal_Parallax_Slider_aockms24();