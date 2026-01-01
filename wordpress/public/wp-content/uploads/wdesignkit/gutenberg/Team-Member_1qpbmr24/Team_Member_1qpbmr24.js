
    class Team_Member_1qpbmr24 {
        constructor() {
            this.Team_Member_1qpbmr24_0b4q7425();
        }
    
        Team_Member_1qpbmr24_0b4q7425() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Toggle,Pmgc_Url,Pmgc_Media,Pmgc_Repeater,Pmgc_IconList,Pmgc_Label_Heading,Pmgc_Typography,Pmgc_Color,Pmgc_Border,Pmgc_RadioAdvanced,Pmgc_Range,Pmgc_Dimension,Pmgc_CssFilter,Pmgc_Background,Pmgc_BoxShadow,Pmgc_Tabs,
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
   
    registerBlockType('wdkit/wb-1qpbmr24', {
        title: __('Team Member'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-users tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Team Member'),__('Team Showcase'),__('Employee Introduction'),__('Staff Display'),__('Team Bios'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_2gsaq423Function = (unit, type) => {
                var g_slider_2gsaq423_list = [];
                g_slider_2gsaq423_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_2gsaq423_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_2gsaq423_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2gsaq423_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2gsaq423_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2gsaq423_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2gsaq423_list[unit][type];
            };
const slider_sytywj24Function = (unit, type) => {
                var g_slider_sytywj24_list = [];
                g_slider_sytywj24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_sytywj24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_sytywj24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_sytywj24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_sytywj24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_sytywj24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_sytywj24_list[unit][type];
            };
const slider_1jfk7k24Function = (unit, type) => {
                var g_slider_1jfk7k24_list = [];
                g_slider_1jfk7k24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_1jfk7k24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_1jfk7k24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_1jfk7k24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_1jfk7k24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_1jfk7k24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_1jfk7k24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_l1jn6223,
switcher_ojei9t24,
url_ceyz7y24,
text_zmblu923,
media_32xtty23,
repeater_9eeglb23,
iconscontrol_pe6nkm23,
number_k7clr924,
heading_tdn66z23,
typography_8ltefq23,
color_kyc5g323,
heading_n9z4ln23,
typography_cwat2k23,
color_1tl0m823,
heading_98fbik23,
typography_txa9ln23,
color_xxy5e023,
heading_fkmicb23,
typography_wpm3b723,
color_pvhcpj23,
border_qsgvnb24,
color_rm0flj24,
color_ilkqhl24,
color_rfy9t924,
choose_oqjasf24,
slider_2gsaq423,
heading_90s51623,
dimension_omxavm23,
heading_khgtni23,
slider_sytywj24,
color_tyn54v24,
dimension_nne6w723,
cssfilter_lbjzf423,
cssfilter_v0pz0r23,
border_blx8sh23,
dimension_17qn4523,
background_n59vra23,
border_mezyq423,
dimension_udi1yb23,
background_ljmsu923,
boxshadow_g8h72p23,
boxshadow_kj13ph23,
normalhover_5qrydq23,
heading_u4aj9m23,
choose_e4wkhj24,
dimension_39xrvj23,
heading_ddahaj24,
slider_1jfk7k24,
color_38m0go24,

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
                        main_function_1qpbmr24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_1qpbmr24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let teamMember = $scope[0].querySelector('.wkit-team-member');
let memberSecond = teamMember.querySelector('.team-member-second');
let memberSecondInner = memberSecond.querySelector('.team-member-second-info-open-close');
let memberThird = teamMember.querySelector('.team-member-third');


  memberSecondInner.addEventListener("click", function(e){
      
      memberThird.classList.toggle('active');
      
      e.currentTarget.classList.toggle('active');
      
      if (memberThird.style.display == "flex"){
          memberThird.style.display = "none";
        } 
        else {
          memberThird.style.display = "flex";
        }
    });
   
  let memberItem = teamMember.querySelectorAll('.team-member-content-item');
  
  let totalItem = 0;
  memberItem.forEach((el,index)=>{
      if(index==0){
          el.classList.add('active');
      }
      totalItem = index;
  });
  
  let clickNextArrow = teamMember.querySelector('.team-member-third-info-arrow');
  let nextArrow = Number(clickNextArrow.getAttribute('data-counter'))*1000
  
  if(totalItem >= 1){
    clickNextArrow.addEventListener('click', changeItem);
     setTimeout(()=>{
        setInterval(changeItem,nextArrow)
    }, 100);   
  }
 
 function changeItem(){
     let itemActive = teamMember.querySelector('.team-member-content-item.active');
      if(itemActive.nextElementSibling){
            itemActive.nextElementSibling.classList.add('active');
            itemActive.classList.remove('active');
            
        }else{
            let closeInn = itemActive.closest('.team-member-third-interest'),
            firstItem = closeInn.firstElementChild;
            if(firstItem){
                firstItem.classList.add('active');
            }
            itemActive.classList.remove('active');
        }
 }
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Name`),
                type: "text",
                value: text_l1jn6223,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_l1jn6223: value }) },
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Link Enable`),
            
            value: switcher_ojei9t24,
            
            onChange: (value) => setAttributes({ switcher_ojei9t24: value }),
            }), 
( switcher_ojei9t24 ) && React.createElement(Pmgc_Url, {
                label: __(`Url`),
                type: "url",
                value: url_ceyz7y24,
                dynamic: [true, 'url_ceyz7y24'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_ceyz7y24: value }),
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Designation`),
                type: "text",
                value: text_zmblu923,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_zmblu923: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_32xtty23,
                dynamic: [true, 'media_32xtty23'],
                
                type: [],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_32xtty23: value }),
            }), 
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Select Interest`),
            value: repeater_9eeglb23,
            attributeName: 'repeater_9eeglb23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_9eeglb23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Text, {
                label: __(`Question`),
                type: "text",
                value: value.text_0lehe723,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_0lehe723 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Answer`),
                type: "text",
                value: value.text_b4fs5123,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_b4fs5123 = v; onChange(value); },
            }),

                    )]
            }), 
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_pe6nkm23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_pe6nkm23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Extra"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Animation Duration`),
                type: "number",
                value: number_k7clr924,
                dynamic: true,
                help: `Set a timer for when content should be changed.






`,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_k7clr924: value }) },
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Name Text/Link`),
            value: heading_tdn66z23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_8ltefq23,
            onChange: (value) => setAttributes({ typography_8ltefq23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_kyc5g323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kyc5g323: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Designation`),
            value: heading_n9z4ln23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_cwat2k23,
            onChange: (value) => setAttributes({ typography_cwat2k23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_1tl0m823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1tl0m823: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Question`),
            value: heading_98fbik23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_txa9ln23,
            onChange: (value) => setAttributes({ typography_txa9ln23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_xxy5e023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_xxy5e023: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Answer`),
            value: heading_fkmicb23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_wpm3b723,
            onChange: (value) => setAttributes({ typography_wpm3b723: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_pvhcpj23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_pvhcpj23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Banner Dot"), initialOpen: false },
 React.createElement(Pmgc_Border, {
            
            value: border_qsgvnb24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_qsgvnb24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Dot1 Color`),
            value: color_rm0flj24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rm0flj24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Dot2 Color`),
            value: color_ilkqhl24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ilkqhl24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Dot3 Color`),
            value: color_rfy9t924,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_rfy9t924: value }),
            }), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_oqjasf24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_oqjasf24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_2gsaq423,
            
            min: slider_2gsaq423 && slider_2gsaq423.unit ? slider_2gsaq423Function(slider_2gsaq423.unit, 'min') : 0,
            max: slider_2gsaq423 && slider_2gsaq423.unit ? slider_2gsaq423Function(slider_2gsaq423.unit, 'max') : 100,
            step: slider_2gsaq423 && slider_2gsaq423.unit ? slider_2gsaq423Function(slider_2gsaq423.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2gsaq423: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Banner`),
            value: heading_90s51623,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_omxavm23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_omxavm23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Description`),
            value: heading_khgtni23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Toggle Icon Size`),
            separator:'default',
            value: slider_sytywj24,
            
            min: slider_sytywj24 && slider_sytywj24.unit ? slider_sytywj24Function(slider_sytywj24.unit, 'min') : 0,
            max: slider_sytywj24 && slider_sytywj24.unit ? slider_sytywj24Function(slider_sytywj24.unit, 'max') : 100,
            step: slider_sytywj24 && slider_sytywj24.unit ? slider_sytywj24Function(slider_sytywj24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_sytywj24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Toggle Color`),
            value: color_tyn54v24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_tyn54v24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_nne6w723,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_nne6w723: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_lbjzf423,
            onChange: (value) => setAttributes({ cssfilter_lbjzf423: value }),
            separator:'default',
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_blx8sh23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_blx8sh23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_17qn4523,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_17qn4523: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_n59vra23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_n59vra23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_g8h72p23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_g8h72p23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_CssFilter, {
            
            
            value: cssfilter_v0pz0r23,
            onChange: (value) => setAttributes({ cssfilter_v0pz0r23: value }),
            separator:'default',
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_mezyq423,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_mezyq423: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_udi1yb23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_udi1yb23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_ljmsu923,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ljmsu923: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_kj13ph23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_kj13ph23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Question & Answer`),
            value: heading_u4aj9m23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_e4wkhj24,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_e4wkhj24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_39xrvj23,
            noLock: false,
            unit: ['px',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_39xrvj23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Arrow`),
            value: heading_ddahaj24,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Size`),
            separator:'default',
            value: slider_1jfk7k24,
            
            min: slider_1jfk7k24 && slider_1jfk7k24.unit ? slider_1jfk7k24Function(slider_1jfk7k24.unit, 'min') : 0,
            max: slider_1jfk7k24 && slider_1jfk7k24.unit ? slider_1jfk7k24Function(slider_1jfk7k24.unit, 'max') : 100,
            step: slider_1jfk7k24 && slider_1jfk7k24.unit ? slider_1jfk7k24Function(slider_1jfk7k24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_1jfk7k24: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color`),
            value: color_38m0go24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_38m0go24: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-1qpbmr24", block_id, false, props.clientId);
                }
            }

            
let g_text_l1jn6223 = text_l1jn6223 && text_l1jn6223 != undefined  ? text_l1jn6223 : "";
let g_switcher_ojei9t24 = switcher_ojei9t24 && switcher_ojei9t24 != undefined  ? 'yes' : "";
let g_url_ceyz7y24_url = url_ceyz7y24?.url && url_ceyz7y24?.url != undefined ? url_ceyz7y24.url : "";
let g_url_ceyz7y24_target = url_ceyz7y24?.target && url_ceyz7y24?.target != undefined ? url_ceyz7y24.target : "";
let g_url_ceyz7y24_nofollow = url_ceyz7y24?.nofollow && url_ceyz7y24?.nofollow != undefined ? url_ceyz7y24.nofollow : "";
let g_url_ceyz7y24_ctmArt = url_ceyz7y24?.attr != undefined ? url_ceyz7y24.attr : "";
                    let g_url_ceyz7y24_attr = ''

                    if (g_url_ceyz7y24_ctmArt) {
                        let main_array = g_url_ceyz7y24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_ceyz7y24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_text_zmblu923 = text_zmblu923 && text_zmblu923 != undefined  ? text_zmblu923 : "";
let g_media_32xtty23 = media_32xtty23 && media_32xtty23.url && media_32xtty23.url != undefined  ? media_32xtty23.url : "";
let g_iconscontrol_pe6nkm23 = iconscontrol_pe6nkm23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_pe6nkm23+'"></i></span>' : '';

let g_number_k7clr924 = number_k7clr924 && number_k7clr924 != undefined  ? number_k7clr924 : "";
let g_heading_tdn66z23 = heading_tdn66z23 && heading_tdn66z23 != undefined  ? heading_tdn66z23 : "";
let g_typography_8ltefq23 = typography_8ltefq23 && typography_8ltefq23 != undefined  ? typography_8ltefq23 : "";
let g_color_kyc5g323 = color_kyc5g323 && color_kyc5g323 != undefined  ? color_kyc5g323 : "";
let g_heading_n9z4ln23 = heading_n9z4ln23 && heading_n9z4ln23 != undefined  ? heading_n9z4ln23 : "";
let g_typography_cwat2k23 = typography_cwat2k23 && typography_cwat2k23 != undefined  ? typography_cwat2k23 : "";
let g_color_1tl0m823 = color_1tl0m823 && color_1tl0m823 != undefined  ? color_1tl0m823 : "";
let g_heading_98fbik23 = heading_98fbik23 && heading_98fbik23 != undefined  ? heading_98fbik23 : "";
let g_typography_txa9ln23 = typography_txa9ln23 && typography_txa9ln23 != undefined  ? typography_txa9ln23 : "";
let g_color_xxy5e023 = color_xxy5e023 && color_xxy5e023 != undefined  ? color_xxy5e023 : "";
let g_heading_fkmicb23 = heading_fkmicb23 && heading_fkmicb23 != undefined  ? heading_fkmicb23 : "";
let g_typography_wpm3b723 = typography_wpm3b723 && typography_wpm3b723 != undefined  ? typography_wpm3b723 : "";
let g_color_pvhcpj23 = color_pvhcpj23 && color_pvhcpj23 != undefined  ? color_pvhcpj23 : "";
let g_border_qsgvnb24 = border_qsgvnb24 && border_qsgvnb24 != undefined  ? border_qsgvnb24 : "";
let g_color_rm0flj24 = color_rm0flj24 && color_rm0flj24 != undefined  ? color_rm0flj24 : "";
let g_color_ilkqhl24 = color_ilkqhl24 && color_ilkqhl24 != undefined  ? color_ilkqhl24 : "";
let g_color_rfy9t924 = color_rfy9t924 && color_rfy9t924 != undefined  ? color_rfy9t924 : "";
let g_choose_oqjasf24 = choose_oqjasf24 && choose_oqjasf24 != undefined  ? choose_oqjasf24 : "";
let g_heading_90s51623 = heading_90s51623 && heading_90s51623 != undefined  ? heading_90s51623 : "";
let g_dimension_omxavm23 = dimension_omxavm23 && dimension_omxavm23 != undefined  ? dimension_omxavm23 : "";
let g_heading_khgtni23 = heading_khgtni23 && heading_khgtni23 != undefined  ? heading_khgtni23 : "";
let g_color_tyn54v24 = color_tyn54v24 && color_tyn54v24 != undefined  ? color_tyn54v24 : "";
let g_dimension_nne6w723 = dimension_nne6w723 && dimension_nne6w723 != undefined  ? dimension_nne6w723 : "";
let g_cssfilter_lbjzf423 = cssfilter_lbjzf423 && cssfilter_lbjzf423 != undefined  ? cssfilter_lbjzf423 : "";
let g_cssfilter_v0pz0r23 = cssfilter_v0pz0r23 && cssfilter_v0pz0r23 != undefined  ? cssfilter_v0pz0r23 : "";
let g_border_blx8sh23 = border_blx8sh23 && border_blx8sh23 != undefined  ? border_blx8sh23 : "";
let g_dimension_17qn4523 = dimension_17qn4523 && dimension_17qn4523 != undefined  ? dimension_17qn4523 : "";
let g_background_n59vra23 = background_n59vra23 && background_n59vra23 != undefined  ? background_n59vra23 : "";
let g_border_mezyq423 = border_mezyq423 && border_mezyq423 != undefined  ? border_mezyq423 : "";
let g_dimension_udi1yb23 = dimension_udi1yb23 && dimension_udi1yb23 != undefined  ? dimension_udi1yb23 : "";
let g_background_ljmsu923 = background_ljmsu923 && background_ljmsu923 != undefined  ? background_ljmsu923 : "";
let g_boxshadow_g8h72p23 = boxshadow_g8h72p23 && boxshadow_g8h72p23 != undefined  ? boxshadow_g8h72p23 : "";
let g_boxshadow_kj13ph23 = boxshadow_kj13ph23 && boxshadow_kj13ph23 != undefined  ? boxshadow_kj13ph23 : "";
let g_heading_u4aj9m23 = heading_u4aj9m23 && heading_u4aj9m23 != undefined  ? heading_u4aj9m23 : "";
let g_choose_e4wkhj24 = choose_e4wkhj24 && choose_e4wkhj24 != undefined  ? choose_e4wkhj24 : "";
let g_dimension_39xrvj23 = dimension_39xrvj23 && dimension_39xrvj23 != undefined  ? dimension_39xrvj23 : "";
let g_heading_ddahaj24 = heading_ddahaj24 && heading_ddahaj24 != undefined  ? heading_ddahaj24 : "";
let g_color_38m0go24 = color_38m0go24 && color_38m0go24 != undefined  ? color_38m0go24 : "";
            
let repeater_9eeglb23_0s25 = "";
                            
repeater_9eeglb23  && repeater_9eeglb23.map((r_item, index) => {
                                
let grnp_text_0lehe723 = r_item.text_0lehe723  ? r_item.text_0lehe723 : "";
let grnp_text_b4fs5123 = r_item.text_b4fs5123  ? r_item.text_b4fs5123 : "";
                                repeater_9eeglb23_0s25 += `<div class="tp-repeater-item-${r_item._key} team-member-content-item" data-repeater_9eeglb23="{repeater_9eeglb23}">
                     <div class="team-member-third-question">${grnp_text_0lehe723}</div>
                     <div class="team-member-third-answer">${grnp_text_b4fs5123}</div>
                </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_1qpbmr24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-team-member">
    <div class="wkit-team-member-inner">
        <div class="team-memeber-first">
            <span class="team-member-first-circle circle-one"></span>
            <span class="team-member-first-circle circle-two"></span>
            <span class="team-member-first-circle circle-three"></span>
        </div>
        <div class="team-member-second">
            <div class="team-memeber-second-inner">
                <img class="team-member-img" src="${g_media_32xtty23}">
            </div>
            <div class="team-member-second-info">
                <a href="${g_url_ceyz7y24_url}" class="team-member-second-info-title Link-on-${g_switcher_ojei9t24}" rel="noopener">
                    ${g_text_l1jn6223}
                </a>
                <div class="team-member-second-info-open-close">+</div>
            </div>
        </div>
        <div class="team-member-third">
             <div class="team-member-third-info">
                 <div class="team-member-third-info-designation">${g_text_zmblu923}</div>
                 <div class="team-member-third-info-arrow" data-counter="${g_number_k7clr924}">
                     ${g_iconscontrol_pe6nkm23}
                 </div>
             </div>
             <div class="team-member-third-interest">
                 ${repeater_9eeglb23_0s25}
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
            text_l1jn6223,
switcher_ojei9t24,
url_ceyz7y24,
text_zmblu923,
media_32xtty23,
repeater_9eeglb23,
iconscontrol_pe6nkm23,
number_k7clr924,
heading_tdn66z23,
typography_8ltefq23,
color_kyc5g323,
heading_n9z4ln23,
typography_cwat2k23,
color_1tl0m823,
heading_98fbik23,
typography_txa9ln23,
color_xxy5e023,
heading_fkmicb23,
typography_wpm3b723,
color_pvhcpj23,
border_qsgvnb24,
color_rm0flj24,
color_ilkqhl24,
color_rfy9t924,
choose_oqjasf24,
slider_2gsaq423,
heading_90s51623,
dimension_omxavm23,
heading_khgtni23,
slider_sytywj24,
color_tyn54v24,
dimension_nne6w723,
cssfilter_lbjzf423,
cssfilter_v0pz0r23,
border_blx8sh23,
dimension_17qn4523,
background_n59vra23,
border_mezyq423,
dimension_udi1yb23,
background_ljmsu923,
boxshadow_g8h72p23,
boxshadow_kj13ph23,
normalhover_5qrydq23,
heading_u4aj9m23,
choose_e4wkhj24,
dimension_39xrvj23,
heading_ddahaj24,
slider_1jfk7k24,
color_38m0go24,

            block_id,
        } = attributes;

        

        

        

        
let g_text_l1jn6223 = text_l1jn6223 && text_l1jn6223 != undefined  ? text_l1jn6223 : "";
let g_switcher_ojei9t24 = switcher_ojei9t24 && switcher_ojei9t24 != undefined  ? 'yes' : "";
let g_url_ceyz7y24_url = url_ceyz7y24?.url && url_ceyz7y24?.url != undefined ? url_ceyz7y24.url : "";
let g_url_ceyz7y24_target = url_ceyz7y24?.target && url_ceyz7y24?.target != undefined ? url_ceyz7y24.target : "";
let g_url_ceyz7y24_nofollow = url_ceyz7y24?.nofollow && url_ceyz7y24?.nofollow != undefined ? url_ceyz7y24.nofollow : "";
let g_url_ceyz7y24_ctmArt = url_ceyz7y24?.attr != undefined ? url_ceyz7y24.attr : "";
                    let g_url_ceyz7y24_attr = ''

                    if (g_url_ceyz7y24_ctmArt) {
                        let main_array = g_url_ceyz7y24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_ceyz7y24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_text_zmblu923 = text_zmblu923 && text_zmblu923 != undefined  ? text_zmblu923 : "";
let g_media_32xtty23 = media_32xtty23 && media_32xtty23.url && media_32xtty23.url != undefined  ? media_32xtty23.url : "";
let g_iconscontrol_pe6nkm23 = iconscontrol_pe6nkm23 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_pe6nkm23+'"></i></span>' : '';

let g_number_k7clr924 = number_k7clr924 && number_k7clr924 != undefined  ? number_k7clr924 : "";
let g_heading_tdn66z23 = heading_tdn66z23 && heading_tdn66z23 != undefined  ? heading_tdn66z23 : "";
let g_typography_8ltefq23 = typography_8ltefq23 && typography_8ltefq23 != undefined  ? typography_8ltefq23 : "";
let g_color_kyc5g323 = color_kyc5g323 && color_kyc5g323 != undefined  ? color_kyc5g323 : "";
let g_heading_n9z4ln23 = heading_n9z4ln23 && heading_n9z4ln23 != undefined  ? heading_n9z4ln23 : "";
let g_typography_cwat2k23 = typography_cwat2k23 && typography_cwat2k23 != undefined  ? typography_cwat2k23 : "";
let g_color_1tl0m823 = color_1tl0m823 && color_1tl0m823 != undefined  ? color_1tl0m823 : "";
let g_heading_98fbik23 = heading_98fbik23 && heading_98fbik23 != undefined  ? heading_98fbik23 : "";
let g_typography_txa9ln23 = typography_txa9ln23 && typography_txa9ln23 != undefined  ? typography_txa9ln23 : "";
let g_color_xxy5e023 = color_xxy5e023 && color_xxy5e023 != undefined  ? color_xxy5e023 : "";
let g_heading_fkmicb23 = heading_fkmicb23 && heading_fkmicb23 != undefined  ? heading_fkmicb23 : "";
let g_typography_wpm3b723 = typography_wpm3b723 && typography_wpm3b723 != undefined  ? typography_wpm3b723 : "";
let g_color_pvhcpj23 = color_pvhcpj23 && color_pvhcpj23 != undefined  ? color_pvhcpj23 : "";
let g_border_qsgvnb24 = border_qsgvnb24 && border_qsgvnb24 != undefined  ? border_qsgvnb24 : "";
let g_color_rm0flj24 = color_rm0flj24 && color_rm0flj24 != undefined  ? color_rm0flj24 : "";
let g_color_ilkqhl24 = color_ilkqhl24 && color_ilkqhl24 != undefined  ? color_ilkqhl24 : "";
let g_color_rfy9t924 = color_rfy9t924 && color_rfy9t924 != undefined  ? color_rfy9t924 : "";
let g_choose_oqjasf24 = choose_oqjasf24 && choose_oqjasf24 != undefined  ? choose_oqjasf24 : "";
let g_heading_90s51623 = heading_90s51623 && heading_90s51623 != undefined  ? heading_90s51623 : "";
let g_dimension_omxavm23 = dimension_omxavm23 && dimension_omxavm23 != undefined  ? dimension_omxavm23 : "";
let g_heading_khgtni23 = heading_khgtni23 && heading_khgtni23 != undefined  ? heading_khgtni23 : "";
let g_color_tyn54v24 = color_tyn54v24 && color_tyn54v24 != undefined  ? color_tyn54v24 : "";
let g_dimension_nne6w723 = dimension_nne6w723 && dimension_nne6w723 != undefined  ? dimension_nne6w723 : "";
let g_cssfilter_lbjzf423 = cssfilter_lbjzf423 && cssfilter_lbjzf423 != undefined  ? cssfilter_lbjzf423 : "";
let g_cssfilter_v0pz0r23 = cssfilter_v0pz0r23 && cssfilter_v0pz0r23 != undefined  ? cssfilter_v0pz0r23 : "";
let g_border_blx8sh23 = border_blx8sh23 && border_blx8sh23 != undefined  ? border_blx8sh23 : "";
let g_dimension_17qn4523 = dimension_17qn4523 && dimension_17qn4523 != undefined  ? dimension_17qn4523 : "";
let g_background_n59vra23 = background_n59vra23 && background_n59vra23 != undefined  ? background_n59vra23 : "";
let g_border_mezyq423 = border_mezyq423 && border_mezyq423 != undefined  ? border_mezyq423 : "";
let g_dimension_udi1yb23 = dimension_udi1yb23 && dimension_udi1yb23 != undefined  ? dimension_udi1yb23 : "";
let g_background_ljmsu923 = background_ljmsu923 && background_ljmsu923 != undefined  ? background_ljmsu923 : "";
let g_boxshadow_g8h72p23 = boxshadow_g8h72p23 && boxshadow_g8h72p23 != undefined  ? boxshadow_g8h72p23 : "";
let g_boxshadow_kj13ph23 = boxshadow_kj13ph23 && boxshadow_kj13ph23 != undefined  ? boxshadow_kj13ph23 : "";
let g_heading_u4aj9m23 = heading_u4aj9m23 && heading_u4aj9m23 != undefined  ? heading_u4aj9m23 : "";
let g_choose_e4wkhj24 = choose_e4wkhj24 && choose_e4wkhj24 != undefined  ? choose_e4wkhj24 : "";
let g_dimension_39xrvj23 = dimension_39xrvj23 && dimension_39xrvj23 != undefined  ? dimension_39xrvj23 : "";
let g_heading_ddahaj24 = heading_ddahaj24 && heading_ddahaj24 != undefined  ? heading_ddahaj24 : "";
let g_color_38m0go24 = color_38m0go24 && color_38m0go24 != undefined  ? color_38m0go24 : "";
        
let repeater_9eeglb23_0s25 = "";
                            
repeater_9eeglb23  && repeater_9eeglb23.map((r_item, index) => {
                                
let grnp_text_0lehe723 = r_item.text_0lehe723  ? r_item.text_0lehe723 : "";
let grnp_text_b4fs5123 = r_item.text_b4fs5123  ? r_item.text_b4fs5123 : "";
                                repeater_9eeglb23_0s25 += `<div class="tp-repeater-item-${r_item._key} team-member-content-item" data-repeater_9eeglb23="{repeater_9eeglb23}">
                     <div class="team-member-third-question">${grnp_text_0lehe723}</div>
                     <div class="team-member-third-answer">${grnp_text_b4fs5123}</div>
                </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-1qpbmr24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_1qpbmr24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-team-member">
    <div class="wkit-team-member-inner">
        <div class="team-memeber-first">
            <span class="team-member-first-circle circle-one"></span>
            <span class="team-member-first-circle circle-two"></span>
            <span class="team-member-first-circle circle-three"></span>
        </div>
        <div class="team-member-second">
            <div class="team-memeber-second-inner">
                <img class="team-member-img" src="${g_media_32xtty23}">
            </div>
            <div class="team-member-second-info">
                <a href="${g_url_ceyz7y24_url}" class="team-member-second-info-title Link-on-${g_switcher_ojei9t24}" rel="noopener">
                    ${g_text_l1jn6223}
                </a>
                <div class="team-member-second-info-open-close">+</div>
            </div>
        </div>
        <div class="team-member-third">
             <div class="team-member-third-info">
                 <div class="team-member-third-info-designation">${g_text_zmblu923}</div>
                 <div class="team-member-third-info-arrow" data-counter="${g_number_k7clr924}">
                     ${g_iconscontrol_pe6nkm23}
                 </div>
             </div>
             <div class="team-member-third-interest">
                 ${repeater_9eeglb23_0s25}
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
    new Team_Member_1qpbmr24();