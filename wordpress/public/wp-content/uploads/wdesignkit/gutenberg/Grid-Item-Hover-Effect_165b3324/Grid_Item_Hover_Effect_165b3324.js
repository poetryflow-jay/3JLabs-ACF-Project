
    class Grid_Item_Hover_Effect_165b3324 {
        constructor() {
            this.Grid_Item_Hover_Effect_165b3324_e2wqq525();
        }
    
        Grid_Item_Hover_Effect_165b3324_e2wqq525() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Media,Pmgc_Text,Pmgc_TextArea,Pmgc_Background,Pmgc_Label_Heading,Pmgc_Typography,Pmgc_RadioAdvanced,Pmgc_Color,Pmgc_Dimension,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-165b3324', {
        title: __('Grid Item Hover Effect'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-th tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('icon box'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        
   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_yz2a6g23,
media_b9lx0423,
select_osz0gl23,
text_mqfxni23,
textarea_xkdmvy23,
select_r31rb323,
text_nb262d23,
textarea_ffw1jh23,
background_tmv7dc23,
heading_xmxjp923,
typography_s6vwsy23,
choose_4tnkwo23,
color_l18c0823,
heading_o89l0623,
typography_ist8gn23,
choose_c1w0o323,
color_rtgj1n23,
background_lcpy8923,
heading_vlnfoc23,
typography_z8y6rq23,
choose_f7s9n123,
color_h146hi23,
heading_sjxht823,
typography_rq9coe23,
choose_oco3ax23,
color_pza92p23,
background_gws5h423,
color_pnz90023,
dimension_jimc3h23,
border_e2di3m23,
boxshadow_zah1aw23,
background_84w2cm23,
color_nuqv1e23,
dimension_l80vh023,
border_znmnp323,
boxshadow_14o8qb23,
normalhover_q9o21823,

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
                        main_function_165b3324(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_165b3324 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getCrt = $scope[0].querySelector('.wkit-grid-item-hover-effect');
let getEffect = getCrt.getAttribute('data-effect');

let getInner = getCrt.querySelector('.grid-inner');

let getImage = getInner.querySelector('.grid-inner-image');

let getPos = $scope[0].querySelectorAll('.inner__box');


let getTitle = $scope[0].querySelector('.pos-top-title');
var charArray2 = getTitle.textContent.split('');
getTitle.textContent = "";

for (var i = 0; i < charArray2.length; i++) {
    var charSpan = document.createElement("span");
    charSpan.textContent = charArray2[i];
    charSpan.className = "char";
    getTitle.appendChild(charSpan);
  }
  
  
  
let chargetTitle = getTitle.querySelectorAll(".char");



let desc = $scope[0].querySelector(".pos-bottom-desc");
var charArray = desc.textContent.split('');
desc.textContent = "";

for (var i = 0; i < charArray.length; i++) {
    var charSpan = document.createElement("span");
    charSpan.textContent = charArray[i];
    charSpan.className = "char";
    desc.appendChild(charSpan);
  }
let charDesc = desc.querySelectorAll(".char");

if (getEffect === 'style-1') {
    getInner.addEventListener('mouseenter', enter);
    getInner.addEventListener('mouseleave', leave);
}else if (getEffect === 'style-2') {
    getInner.addEventListener('mouseenter', function(event) {
        enter2(event);
    });
    getInner.addEventListener('mouseleave', function(event) {
        leave2(event);
    });
}else{
    getInner.addEventListener('mouseenter', enter3);
    getInner.addEventListener('mouseleave', leave3);
}

function enter() {
    if ( this.leaveTimeline) {
        this.leaveTimeline.kill();
    }
    
    this.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.5,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'saturate(100%) brightness(100%)',
    }, {
        scale: 0.85,
        filter: 'saturate(200%) brightness(70%)'
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        xPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        },
        yPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        }
    }, {
        opacity: 1,
        xPercent: 0,
        yPercent: 0,
    }, 'start')
      .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}


function leave() {
    if ( this.enterTimeline ) {
        this.enterTimeline.kill();
    }

    this.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        scale: 1,
        filter: 'saturate(100%) brightness(100%)'
    }, 'start')
    .to(getPos, {
        opacity: 0,
        xPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        },
        yPercent: (_, target) => {
            if (target.classList.contains('pos-top-left')) {
                return -100;
            }
            else if (target.classList.contains('pos-top-right')) {
                return -100;
            }
            else if (target.classList.contains('pos-bottom-left')) {
                return 100;
            }
            else if (target.classList.contains('pos-bottom-right')) {
                return 100;
            }
            return 0;
        }
    }, 'start');
}

let lastMouseX = null;
let lastMouseY = null;

document.addEventListener('mousemove', (e) => {
    lastMouseX = e.clientX;
    lastMouseY = e.clientY;
});

const getMouseEnterDirection = (element, lastX, lastY) => {
    const { top, right, bottom, left } = element.getBoundingClientRect();
    
    if (lastY <= Math.floor(top)) return "top";
    if (lastY >= Math.floor(bottom)) return "bottom";
    if (lastX <= Math.floor(left)) return "left";
    if (lastX >= Math.floor(right)) return "right";
    
    return "unknown";
}

function enter2(event){
    const direction = getMouseEnterDirection(event.target, lastMouseX, lastMouseY);
    event.target.dataset.direction = direction;

    if ( event.leaveTimeline ) {
        event.leaveTimeline.kill();
    }
    
    event.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.7,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'grayscale(0%)',
    }, {
        //scale: 1,
        xPercent: () => {
            if ( direction === 'left' ) {
                return -10;
            }
            else if ( direction === 'right' ) {
                return 10;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -10;
            }
            else if ( direction === 'bottom' ) {
                return 10;
            }
            else return 0;
        },
        filter: 'grayscale(40%)'
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        xPercent: () => {
            if ( direction === 'left' ) {
                return -20;
            }
            else if ( direction === 'right' ) {
                return 20;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -20;
            }
            else if ( direction === 'bottom' ) {
                return 20;
            }
            else return 0;
        },
        rotation: -10
    }, {
        opacity: 1,
        xPercent: 0,
        yPercent: 0,
        rotation: 0,
        stagger: 0.08
    }, 'start')
    .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}
function leave2(event) {
   const direction = event.target.dataset.direction;
    
    if ( event.enterTimeline ) {
        event.enterTimeline.kill();
    }
    event.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        //scale: 1.3,
        xPercent: 0,
        yPercent: 0,
        filter: 'grayscale(0%)'
    }, 'start')
    .to(getPos, {
        //scale: 0,
        opacity: 0,
        
        xPercent: () => {
            if ( direction === 'left' ) {
                return -20;
            }
            else if ( direction === 'right' ) {
                return 20;
            }
            else return 0;
        },
        yPercent: () => {
            if ( direction === 'top' ) {
                return -20;
            }
            else if ( direction === 'bottom' ) {
                return 20;
            }
            else return 0;
        },
        rotation: -10
    }, 'start');
    
}

function enter3(){
    if ( this.leaveTimeline ) {
        this.leaveTimeline.kill();
    }
    
    this.enterTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.7,
            ease: 'expo'
        }
    })
    .addLabel('start', 0)
    .fromTo(getImage, {
        filter: 'grayscale(0%)',
    }, {
        filter: 'grayscale(90%)'
    }, 'start')
    .to(getImage, {
        ease: 'power4',
        duration: 0.6,
        scaleY: 1
    }, 'start')
    .to(getImage, {
        duration: 1.5,
        scaleX: 1
    }, 'start')
    .fromTo(getPos, {
        opacity: 0,
        scale: 0,
        rotation: -10
    }, {
        opacity: 1,
        scale: 1,
        rotation: 0,
        stagger: 0.08
    }, 'start')
    .fromTo(chargetTitle, {
        opacity: 0,
    }, {
        duration: 0.3,
        opacity: 1,
        stagger: 0.1,
    }, 'start+=.2')
    .fromTo(charDesc, {
        opacity: 0
    }, {
        duration: 0.1,
        opacity: 1,
        stagger: {
            from: 'random',
            each: 0.05
        },
    }, 'start+=.2')
}
function leave3(){
    if ( this.enterTimeline ) {
        this.enterTimeline.kill();
    }
    this.leaveTimeline = gsap
    .timeline({
        defaults: {
            duration: 0.8,
            ease: 'expo'
        } 
    })
    .addLabel('start', 0)
    .to(getImage, {
        scale: 1.3,
        filter: 'grayscale(0%)'
    }, 'start')
    .to(getPos, {
        scale: 0,
        opacity: 0,
        rotation: -10
    }, 'start');
}
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Effect Type`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],['style-3',__('Style 3')],],
                separator:"default",
                
                
                value: select_yz2a6g23,
                onChange: (value) => {setAttributes({ select_yz2a6g23: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Media`),
                multiple: false,
                separator:'default',
                value: media_b9lx0423,
                dynamic: [true, 'media_b9lx0423'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_b9lx0423: value }),
            }), 
), React.createElement(PanelBody, { title: __("Top Box"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Position`),
                options:[['left',__('Left')],['right',__('Right')],],
                separator:"default",
                
                
                value: select_osz0gl23,
                onChange: (value) => {setAttributes({ select_osz0gl23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_mqfxni23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_mqfxni23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_xkdmvy23,
                dynamic: true,
                onChange: (value) => setAttributes({ textarea_xkdmvy23: value }),
            }),
), React.createElement(PanelBody, { title: __("Bottom Box"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Position`),
                options:[['left',__('Left')],['right',__('Right')],],
                separator:"default",
                
                
                value: select_r31rb323,
                onChange: (value) => {setAttributes({ select_r31rb323: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_nb262d23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_nb262d23: value }) },
            }),
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_ffw1jh23,
                dynamic: true,
                onChange: (value) => setAttributes({ textarea_ffw1jh23: value }),
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Top Box"), initialOpen: true },
 React.createElement(Pmgc_Background, {
            
            value: background_tmv7dc23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_tmv7dc23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_xmxjp923,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_s6vwsy23,
            onChange: (value) => setAttributes({ typography_s6vwsy23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_4tnkwo23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_4tnkwo23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_l18c0823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_l18c0823: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_o89l0623,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_ist8gn23,
            onChange: (value) => setAttributes({ typography_ist8gn23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_c1w0o323,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_c1w0o323: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_rtgj1n23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rtgj1n23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Bottom Box"), initialOpen: false },
 React.createElement(Pmgc_Background, {
            
            value: background_lcpy8923,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lcpy8923: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_vlnfoc23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_z8y6rq23,
            onChange: (value) => setAttributes({ typography_z8y6rq23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_f7s9n123,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_f7s9n123: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_h146hi23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_h146hi23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_sjxht823,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_rq9coe23,
            onChange: (value) => setAttributes({ typography_rq9coe23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_oco3ax23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_oco3ax23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_pza92p23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_pza92p23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_gws5h423,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_gws5h423: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_pnz90023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_pnz90023: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_jimc3h23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_jimc3h23: value }),
            
        }),
 React.createElement(Pmgc_Border, {
            
            value: border_e2di3m23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_e2di3m23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_zah1aw23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_zah1aw23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_84w2cm23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_84w2cm23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_nuqv1e23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_nuqv1e23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_l80vh023,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_l80vh023: value }),
            
        }),
 React.createElement(Pmgc_Border, {
            
            value: border_znmnp323,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_znmnp323: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_14o8qb23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_14o8qb23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-165b3324", block_id, false, props.clientId);
                }
            }

            
let g_select_yz2a6g23 = select_yz2a6g23 && select_yz2a6g23 != undefined  ? select_yz2a6g23 : "";
let g_media_b9lx0423 = media_b9lx0423 && media_b9lx0423.url && media_b9lx0423.url != undefined  ? media_b9lx0423.url : "";
let g_select_osz0gl23 = select_osz0gl23 && select_osz0gl23 != undefined  ? select_osz0gl23 : "";
let g_text_mqfxni23 = text_mqfxni23 && text_mqfxni23 != undefined  ? text_mqfxni23 : "";
let g_textarea_xkdmvy23 = textarea_xkdmvy23 && textarea_xkdmvy23 != undefined  ? textarea_xkdmvy23 : "";
let g_select_r31rb323 = select_r31rb323 && select_r31rb323 != undefined  ? select_r31rb323 : "";
let g_text_nb262d23 = text_nb262d23 && text_nb262d23 != undefined  ? text_nb262d23 : "";
let g_textarea_ffw1jh23 = textarea_ffw1jh23 && textarea_ffw1jh23 != undefined  ? textarea_ffw1jh23 : "";
let g_background_tmv7dc23 = background_tmv7dc23 && background_tmv7dc23 != undefined  ? background_tmv7dc23 : "";
let g_heading_xmxjp923 = heading_xmxjp923 && heading_xmxjp923 != undefined  ? heading_xmxjp923 : "";
let g_typography_s6vwsy23 = typography_s6vwsy23 && typography_s6vwsy23 != undefined  ? typography_s6vwsy23 : "";
let g_choose_4tnkwo23 = choose_4tnkwo23 && choose_4tnkwo23 != undefined  ? choose_4tnkwo23 : "";
let g_color_l18c0823 = color_l18c0823 && color_l18c0823 != undefined  ? color_l18c0823 : "";
let g_heading_o89l0623 = heading_o89l0623 && heading_o89l0623 != undefined  ? heading_o89l0623 : "";
let g_typography_ist8gn23 = typography_ist8gn23 && typography_ist8gn23 != undefined  ? typography_ist8gn23 : "";
let g_choose_c1w0o323 = choose_c1w0o323 && choose_c1w0o323 != undefined  ? choose_c1w0o323 : "";
let g_color_rtgj1n23 = color_rtgj1n23 && color_rtgj1n23 != undefined  ? color_rtgj1n23 : "";
let g_background_lcpy8923 = background_lcpy8923 && background_lcpy8923 != undefined  ? background_lcpy8923 : "";
let g_heading_vlnfoc23 = heading_vlnfoc23 && heading_vlnfoc23 != undefined  ? heading_vlnfoc23 : "";
let g_typography_z8y6rq23 = typography_z8y6rq23 && typography_z8y6rq23 != undefined  ? typography_z8y6rq23 : "";
let g_choose_f7s9n123 = choose_f7s9n123 && choose_f7s9n123 != undefined  ? choose_f7s9n123 : "";
let g_color_h146hi23 = color_h146hi23 && color_h146hi23 != undefined  ? color_h146hi23 : "";
let g_heading_sjxht823 = heading_sjxht823 && heading_sjxht823 != undefined  ? heading_sjxht823 : "";
let g_typography_rq9coe23 = typography_rq9coe23 && typography_rq9coe23 != undefined  ? typography_rq9coe23 : "";
let g_choose_oco3ax23 = choose_oco3ax23 && choose_oco3ax23 != undefined  ? choose_oco3ax23 : "";
let g_color_pza92p23 = color_pza92p23 && color_pza92p23 != undefined  ? color_pza92p23 : "";
let g_background_gws5h423 = background_gws5h423 && background_gws5h423 != undefined  ? background_gws5h423 : "";
let g_color_pnz90023 = color_pnz90023 && color_pnz90023 != undefined  ? color_pnz90023 : "";
let g_dimension_jimc3h23 = dimension_jimc3h23 && dimension_jimc3h23 != undefined  ? dimension_jimc3h23 : "";
let g_border_e2di3m23 = border_e2di3m23 && border_e2di3m23 != undefined  ? border_e2di3m23 : "";
let g_boxshadow_zah1aw23 = boxshadow_zah1aw23 && boxshadow_zah1aw23 != undefined  ? boxshadow_zah1aw23 : "";
let g_background_84w2cm23 = background_84w2cm23 && background_84w2cm23 != undefined  ? background_84w2cm23 : "";
let g_color_nuqv1e23 = color_nuqv1e23 && color_nuqv1e23 != undefined  ? color_nuqv1e23 : "";
let g_dimension_l80vh023 = dimension_l80vh023 && dimension_l80vh023 != undefined  ? dimension_l80vh023 : "";
let g_border_znmnp323 = border_znmnp323 && border_znmnp323 != undefined  ? border_znmnp323 : "";
let g_boxshadow_14o8qb23 = boxshadow_14o8qb23 && boxshadow_14o8qb23 != undefined  ? boxshadow_14o8qb23 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_165b3324 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-grid-item-hover-effect" data-effect="${g_select_yz2a6g23}">
    <div class="grid-inner">
        <div class="grid-inner-image" style="background-image:url(${g_media_b9lx0423})"></div>
        <div class="inner__box top-inner-box pos-top-${g_select_osz0gl23}">
            <span data-title="${g_text_mqfxni23}" class="pos-top-title">${g_text_mqfxni23}</span>
            <p data-desc="${g_textarea_xkdmvy23}" class="pos-top-desc">${g_textarea_xkdmvy23}</p>
        </div>
        <div class="inner__box bottom-inner-box pos-bottom-${g_select_r31rb323}">
            <span data-title="${g_text_nb262d23}" class="pos-bottom-title">${g_text_nb262d23}</span>
            <p data-desc="${g_textarea_ffw1jh23}" class="pos-bottom-desc">${g_textarea_ffw1jh23}</p>
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
            select_yz2a6g23,
media_b9lx0423,
select_osz0gl23,
text_mqfxni23,
textarea_xkdmvy23,
select_r31rb323,
text_nb262d23,
textarea_ffw1jh23,
background_tmv7dc23,
heading_xmxjp923,
typography_s6vwsy23,
choose_4tnkwo23,
color_l18c0823,
heading_o89l0623,
typography_ist8gn23,
choose_c1w0o323,
color_rtgj1n23,
background_lcpy8923,
heading_vlnfoc23,
typography_z8y6rq23,
choose_f7s9n123,
color_h146hi23,
heading_sjxht823,
typography_rq9coe23,
choose_oco3ax23,
color_pza92p23,
background_gws5h423,
color_pnz90023,
dimension_jimc3h23,
border_e2di3m23,
boxshadow_zah1aw23,
background_84w2cm23,
color_nuqv1e23,
dimension_l80vh023,
border_znmnp323,
boxshadow_14o8qb23,
normalhover_q9o21823,

            block_id,
        } = attributes;

        

        

        

        
let g_select_yz2a6g23 = select_yz2a6g23 && select_yz2a6g23 != undefined  ? select_yz2a6g23 : "";
let g_media_b9lx0423 = media_b9lx0423 && media_b9lx0423.url && media_b9lx0423.url != undefined  ? media_b9lx0423.url : "";
let g_select_osz0gl23 = select_osz0gl23 && select_osz0gl23 != undefined  ? select_osz0gl23 : "";
let g_text_mqfxni23 = text_mqfxni23 && text_mqfxni23 != undefined  ? text_mqfxni23 : "";
let g_textarea_xkdmvy23 = textarea_xkdmvy23 && textarea_xkdmvy23 != undefined  ? textarea_xkdmvy23 : "";
let g_select_r31rb323 = select_r31rb323 && select_r31rb323 != undefined  ? select_r31rb323 : "";
let g_text_nb262d23 = text_nb262d23 && text_nb262d23 != undefined  ? text_nb262d23 : "";
let g_textarea_ffw1jh23 = textarea_ffw1jh23 && textarea_ffw1jh23 != undefined  ? textarea_ffw1jh23 : "";
let g_background_tmv7dc23 = background_tmv7dc23 && background_tmv7dc23 != undefined  ? background_tmv7dc23 : "";
let g_heading_xmxjp923 = heading_xmxjp923 && heading_xmxjp923 != undefined  ? heading_xmxjp923 : "";
let g_typography_s6vwsy23 = typography_s6vwsy23 && typography_s6vwsy23 != undefined  ? typography_s6vwsy23 : "";
let g_choose_4tnkwo23 = choose_4tnkwo23 && choose_4tnkwo23 != undefined  ? choose_4tnkwo23 : "";
let g_color_l18c0823 = color_l18c0823 && color_l18c0823 != undefined  ? color_l18c0823 : "";
let g_heading_o89l0623 = heading_o89l0623 && heading_o89l0623 != undefined  ? heading_o89l0623 : "";
let g_typography_ist8gn23 = typography_ist8gn23 && typography_ist8gn23 != undefined  ? typography_ist8gn23 : "";
let g_choose_c1w0o323 = choose_c1w0o323 && choose_c1w0o323 != undefined  ? choose_c1w0o323 : "";
let g_color_rtgj1n23 = color_rtgj1n23 && color_rtgj1n23 != undefined  ? color_rtgj1n23 : "";
let g_background_lcpy8923 = background_lcpy8923 && background_lcpy8923 != undefined  ? background_lcpy8923 : "";
let g_heading_vlnfoc23 = heading_vlnfoc23 && heading_vlnfoc23 != undefined  ? heading_vlnfoc23 : "";
let g_typography_z8y6rq23 = typography_z8y6rq23 && typography_z8y6rq23 != undefined  ? typography_z8y6rq23 : "";
let g_choose_f7s9n123 = choose_f7s9n123 && choose_f7s9n123 != undefined  ? choose_f7s9n123 : "";
let g_color_h146hi23 = color_h146hi23 && color_h146hi23 != undefined  ? color_h146hi23 : "";
let g_heading_sjxht823 = heading_sjxht823 && heading_sjxht823 != undefined  ? heading_sjxht823 : "";
let g_typography_rq9coe23 = typography_rq9coe23 && typography_rq9coe23 != undefined  ? typography_rq9coe23 : "";
let g_choose_oco3ax23 = choose_oco3ax23 && choose_oco3ax23 != undefined  ? choose_oco3ax23 : "";
let g_color_pza92p23 = color_pza92p23 && color_pza92p23 != undefined  ? color_pza92p23 : "";
let g_background_gws5h423 = background_gws5h423 && background_gws5h423 != undefined  ? background_gws5h423 : "";
let g_color_pnz90023 = color_pnz90023 && color_pnz90023 != undefined  ? color_pnz90023 : "";
let g_dimension_jimc3h23 = dimension_jimc3h23 && dimension_jimc3h23 != undefined  ? dimension_jimc3h23 : "";
let g_border_e2di3m23 = border_e2di3m23 && border_e2di3m23 != undefined  ? border_e2di3m23 : "";
let g_boxshadow_zah1aw23 = boxshadow_zah1aw23 && boxshadow_zah1aw23 != undefined  ? boxshadow_zah1aw23 : "";
let g_background_84w2cm23 = background_84w2cm23 && background_84w2cm23 != undefined  ? background_84w2cm23 : "";
let g_color_nuqv1e23 = color_nuqv1e23 && color_nuqv1e23 != undefined  ? color_nuqv1e23 : "";
let g_dimension_l80vh023 = dimension_l80vh023 && dimension_l80vh023 != undefined  ? dimension_l80vh023 : "";
let g_border_znmnp323 = border_znmnp323 && border_znmnp323 != undefined  ? border_znmnp323 : "";
let g_boxshadow_14o8qb23 = boxshadow_14o8qb23 && boxshadow_14o8qb23 != undefined  ? boxshadow_14o8qb23 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-165b3324", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_165b3324 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-grid-item-hover-effect" data-effect="${g_select_yz2a6g23}">
    <div class="grid-inner">
        <div class="grid-inner-image" style="background-image:url(${g_media_b9lx0423})"></div>
        <div class="inner__box top-inner-box pos-top-${g_select_osz0gl23}">
            <span data-title="${g_text_mqfxni23}" class="pos-top-title">${g_text_mqfxni23}</span>
            <p data-desc="${g_textarea_xkdmvy23}" class="pos-top-desc">${g_textarea_xkdmvy23}</p>
        </div>
        <div class="inner__box bottom-inner-box pos-bottom-${g_select_r31rb323}">
            <span data-title="${g_text_nb262d23}" class="pos-bottom-title">${g_text_nb262d23}</span>
            <p data-desc="${g_textarea_ffw1jh23}" class="pos-bottom-desc">${g_textarea_ffw1jh23}</p>
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
    new Grid_Item_Hover_Effect_165b3324();