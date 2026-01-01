
    class WhatsApp_Chat_opjpzt24 {
        constructor() {
            this.WhatsApp_Chat_opjpzt24_2u753o25();
        }
    
        WhatsApp_Chat_opjpzt24_2u753o25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Text,Pmgc_Select,Pmgc_IconList,Pmgc_Media,Pmgc_TextArea,Pmgc_Note,Pmgc_Toggle,Pmgc_Dimension,Pmgc_Range,Pmgc_Typography,Pmgc_Color,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,Pmgc_Tabs,Pmgc_Label_Heading,Pmgc_RadioAdvanced,
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
   
    registerBlockType('wdkit/wb-opjpzt24', {
        title: __('WhatsApp Chat'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fab fa-whatsapp tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('WhatsApp'),__('Chat'),__('Social'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_lydd7p24Function = (unit, type) => {
                var g_slider_lydd7p24_list = [];
                g_slider_lydd7p24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_lydd7p24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_lydd7p24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_lydd7p24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_lydd7p24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_lydd7p24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_lydd7p24_list[unit][type];
            };
const slider_veb44t23Function = (unit, type) => {
                var g_slider_veb44t23_list = [];
                g_slider_veb44t23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_veb44t23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_veb44t23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_veb44t23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_veb44t23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_veb44t23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_veb44t23_list[unit][type];
            };
const slider_mw4nur23Function = (unit, type) => {
                var g_slider_mw4nur23_list = [];
                g_slider_mw4nur23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_mw4nur23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_mw4nur23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mw4nur23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mw4nur23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mw4nur23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mw4nur23_list[unit][type];
            };
const slider_mxphtu23Function = (unit, type) => {
                var g_slider_mxphtu23_list = [];
                g_slider_mxphtu23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_mxphtu23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_mxphtu23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_mxphtu23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_mxphtu23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_mxphtu23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_mxphtu23_list[unit][type];
            };
const slider_2h080623Function = (unit, type) => {
                var g_slider_2h080623_list = [];
                g_slider_2h080623_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_2h080623_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_2h080623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2h080623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2h080623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2h080623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2h080623_list[unit][type];
            };
const slider_qyqx1225Function = (unit, type) => {
                var g_slider_qyqx1225_list = [];
                g_slider_qyqx1225_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_qyqx1225_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_qyqx1225_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_qyqx1225_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_qyqx1225_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_qyqx1225_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_qyqx1225_list[unit][type];
            };
const slider_17yuoc25Function = (unit, type) => {
                var g_slider_17yuoc25_list = [];
                g_slider_17yuoc25_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_17yuoc25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_17yuoc25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_17yuoc25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_17yuoc25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_17yuoc25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_17yuoc25_list[unit][type];
            };
const slider_ak69m625Function = (unit, type) => {
                var g_slider_ak69m625_list = [];
                g_slider_ak69m625_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_ak69m625_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ak69m625_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ak69m625_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ak69m625_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ak69m625_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ak69m625_list[unit][type];
            };
const slider_yzufd925Function = (unit, type) => {
                var g_slider_yzufd925_list = [];
                g_slider_yzufd925_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_yzufd925_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_yzufd925_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_yzufd925_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_yzufd925_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_yzufd925_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_yzufd925_list[unit][type];
            };
const slider_32v3hk23Function = (unit, type) => {
                var g_slider_32v3hk23_list = [];
                g_slider_32v3hk23_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_32v3hk23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_32v3hk23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_32v3hk23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_32v3hk23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_32v3hk23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_32v3hk23_list[unit][type];
            };
const slider_uh8ghe23Function = (unit, type) => {
                var g_slider_uh8ghe23_list = [];
                g_slider_uh8ghe23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_uh8ghe23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_uh8ghe23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_uh8ghe23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_uh8ghe23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_uh8ghe23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_uh8ghe23_list[unit][type];
            };
const slider_cl7ey923Function = (unit, type) => {
                var g_slider_cl7ey923_list = [];
                g_slider_cl7ey923_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_cl7ey923_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_cl7ey923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_cl7ey923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_cl7ey923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_cl7ey923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_cl7ey923_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               text_vyi5pp23,
select_l6nskb23,
select_7ixk5r25,
iconscontrol_0p5x3523,
text_wiqdor23,
media_vq0g6123,
iconscontrol_2n1gbw24,
text_obeg2k23,
iconscontrol_m6x3ve24,
textarea_q2tlj223,
text_by1gvg23,
select_egyy2t23,
iconscontrol_4r22vh23,
text_wge8i023,
rawhtml_072sti25,
textarea_riravg23,
switcher_xuo26v23,
switcher_wvlbhb23,
rawhtml_aovgsr25,
dimension_in5gfk24,
slider_lydd7p24,
typography_htd7qf23,
color_sy57cu23,
color_dk27qn23,
background_ecwnl223,
border_5esc5f23,
dimension_dqp9qp23,
boxshadow_cxjrq723,
color_5fw9ji23,
color_ku18rf23,
background_9ob81j23,
border_47tsxy23,
boxshadow_t7be2123,
color_yr45co23,
color_d4iumi23,
background_7crk3423,
border_3vpgwj23,
dimension_ny2hx423,
boxshadow_qawmmw23,
normalhover_fve45k23,
slider_veb44t23,
slider_mw4nur23,
color_28idto23,
color_7gudm823,
color_c3w3xm23,
normalhover_yfwzv825,
background_fya9if23,
slider_mxphtu23,
slider_2h080623,
color_u45lua23,
border_k7nhoe23,
boxshadow_uni09u23,
slider_qyqx1225,
slider_17yuoc25,
typography_xia4h723,
color_oraozg23,
color_i0o17225,
slider_ak69m625,
slider_yzufd925,
typography_mcrrug23,
color_kawax723,
color_cbsu8v25,
heading_n4q47y23,
typography_8np72823,
color_0wn6u123,
heading_oc81cg23,
typography_oim9na23,
color_u1dm9m23,
heading_9cq99223,
typography_jw21k123,
color_lmmbec23,
background_qjrdgj23,
color_9odl9323,
color_anbrrw23,
heading_ygznk023,
dimension_ofe1s523,
background_lpaz0623,
heading_tl42m423,
dimension_kfsp3j24,
typography_2uyqzo23,
color_of6pef23,
background_j8ky0523,
border_um2xc923,
dimension_nsvcp223,
boxshadow_lwnprh23,
color_s55e8823,
background_lsqnsv23,
border_v893xn23,
boxshadow_pjwdj623,
normalhover_vijh8423,
slider_32v3hk23,
slider_uh8ghe23,
color_oag24j23,
color_egk1he23,
normalhover_4xdyrx25,
choose_w125ax23,
slider_cl7ey923,
background_hqy35f24,
border_bkdazs24,
dimension_n36rc224,
boxshadow_b3o1jt24,

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
                        main_function_opjpzt24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_opjpzt24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let getWA = $scope[0].querySelector('.wkit-whatsapp-chat');
var unid = Math.random().toString(32).slice(2);
if(getWA.getAttribute('data-unique')){
    return;
}else{
    getWA.setAttribute('data-unique', unid)
}

let closeCross = getWA.querySelector('.wkit-wa-popup-close');

let getLoader = getWA.querySelector('.whatsapp-typing-loader');
let getBody = getWA.querySelector('.body-main-content');
setTimeout(()=>{
    getLoader.style.opacity = '1';
}, 2000);

setTimeout(()=>{
    let timeEl = getBody.querySelector('.content-time');
    let nowDT = new Date();
    let hours = nowDT.getHours();
    let minutes = nowDT.getMinutes();
    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    let formattedTime = `${hours}:${minutes}`;
    timeEl.textContent = formattedTime;
    
    getLoader.style.opacity = '0';
    getBody.style.opacity = '1';
}, 4000);

let waBtn = getWA.querySelector('.wkit-wa-btn-wrap');

let gtNumber = waBtn.getAttribute('wa-number');
let gtMesaage = waBtn.getAttribute('wa-message');
if(gtNumber){
    
    let cLink = 'https://api.whatsapp.com/send/?phone='+gtNumber;
    if(gtMesaage){
        cLink += '&text='+gtMesaage;
    }
    let gtAtag = waBtn.querySelector('.wkit-whatsapp-btn');
    
    gtAtag.href = cLink;
}


let tglBtn = getWA.querySelector('.wkit-toggle-btn');

let chatBox = getWA.querySelector('.wkit-whatsapp-chat-inner');

tglBtn.addEventListener('click', (e)=>{
    let getCrt = e.currentTarget;
    if(getCrt.classList.contains('active')){
        getCrt.classList.remove('active');
        chatBox.classList.remove('active');
    }else{
        getCrt.classList.add('active');
        chatBox.classList.add('active');
    }
});

closeCross.addEventListener('click', (e)=>{
    tglBtn.classList.remove('active');
    chatBox.classList.remove('active');
});
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Toggle Button"), initialOpen: true },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_vyi5pp23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_vyi5pp23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Icon Type`),
                options:[['none',__('None')],['icon',__('Icon')],],
                separator:"default",
                
                
                value: select_l6nskb23,
                onChange: (value) => {setAttributes({ select_l6nskb23: value }) },
            }),
( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_Select, {
                label: __(`Icon Position`),
                options:[['before',__('Before')],['after',__('After')],],
                separator:"default",
                
                
                value: select_7ixk5r25,
                onChange: (value) => {setAttributes({ select_7ixk5r25: value }) },
            }),
( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_0p5x3523,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_0p5x3523: value }),
            }), 
), React.createElement(PanelBody, { title: __("Profile Title"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: text_wiqdor23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_wiqdor23: value }) },
            }),
 React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: media_vq0g6123,
                
                
                type: ["image"],
                panel: true,
                inlineblock:false,
                onChange: (value) => setAttributes({ media_vq0g6123: value }),
            }), 
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_2n1gbw24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_2n1gbw24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Profile Subtitle"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Text`),
                type: "text",
                value: text_obeg2k23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_obeg2k23: value }) },
            }),
 React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_m6x3ve24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_m6x3ve24: value }),
            }), 
), React.createElement(PanelBody, { title: __("Popup Content"), initialOpen: false },
 React.createElement(Pmgc_TextArea, {
                label: __(`Text`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_q2tlj223,
                
                onChange: (value) => setAttributes({ textarea_q2tlj223: value }),
            }),
), React.createElement(PanelBody, { title: __("Popup Button"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Text`),
                type: "text",
                value: text_by1gvg23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_by1gvg23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Icon Type`),
                options:[['none',__('None')],['icon',__('Icon')],],
                separator:"default",
                
                
                value: select_egyy2t23,
                onChange: (value) => {setAttributes({ select_egyy2t23: value }) },
            }),
( select_egyy2t23 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: iconscontrol_4r22vh23,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_4r22vh23: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`WhatsApp Number`),
                type: "text",
                value: text_wge8i023,
                
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => {setAttributes({ text_wge8i023: value }) },
            }),
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_072sti25,
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
Add WhatsApp Number with County Code. Ex: 911234567890 here 91 is county code of India.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_072sti25: value }),
            }), 
 React.createElement(Pmgc_TextArea, {
                label: __(`Message`),
                separator:"default",
                inlineblock:false,
                
                rows:"5",
                value: textarea_riravg23,
                
                onChange: (value) => setAttributes({ textarea_riravg23: value }),
            }),
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Toggle, {
            label: __(`Show Dot`),
            
            value: switcher_xuo26v23,
            
            onChange: (value) => setAttributes({ switcher_xuo26v23: value }),
            }), 
 React.createElement(Pmgc_Toggle, {
            label: __(`Default Enable`),
            
            value: switcher_wvlbhb23,
            
            onChange: (value) => setAttributes({ switcher_wvlbhb23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_aovgsr25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/whatsapp-chat-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_aovgsr25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Toggle Button"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_in5gfk24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_in5gfk24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Top Space`),
            separator:'default',
            value: slider_lydd7p24,
            
            min: slider_lydd7p24 && slider_lydd7p24.unit ? slider_lydd7p24Function(slider_lydd7p24.unit, 'min') : 0,
            max: slider_lydd7p24 && slider_lydd7p24.unit ? slider_lydd7p24Function(slider_lydd7p24.unit, 'max') : 100,
            step: slider_lydd7p24 && slider_lydd7p24.unit ? slider_lydd7p24Function(slider_lydd7p24.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_lydd7p24: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_htd7qf23,
            onChange: (value) => setAttributes({ typography_htd7qf23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_sy57cu23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_sy57cu23: value }),
            }), 
( switcher_xuo26v23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: color_dk27qn23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_dk27qn23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ecwnl223,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ecwnl223: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_5esc5f23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_5esc5f23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_dqp9qp23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_dqp9qp23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cxjrq723,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cxjrq723: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_5fw9ji23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_5fw9ji23: value }),
            }), 
( switcher_xuo26v23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: color_ku18rf23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ku18rf23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_9ob81j23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_9ob81j23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_47tsxy23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_47tsxy23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_t7be2123,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_t7be2123: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_yr45co23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yr45co23: value }),
            }), 
( switcher_xuo26v23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: color_d4iumi23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_d4iumi23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_7crk3423,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_7crk3423: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_3vpgwj23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_3vpgwj23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ny2hx423,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ny2hx423: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_qawmmw23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_qawmmw23: value }),
            }), 
), 
), 
),( select_l6nskb23 == "icon" ) && React.createElement(PanelBody, { title: __("Toggle Button Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_veb44t23,
            
            min: slider_veb44t23 && slider_veb44t23.unit ? slider_veb44t23Function(slider_veb44t23.unit, 'min') : 0,
            max: slider_veb44t23 && slider_veb44t23.unit ? slider_veb44t23Function(slider_veb44t23.unit, 'max') : 100,
            step: slider_veb44t23 && slider_veb44t23.unit ? slider_veb44t23Function(slider_veb44t23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_veb44t23: value }),
            }), 
( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_mw4nur23,
            
            min: slider_mw4nur23 && slider_mw4nur23.unit ? slider_mw4nur23Function(slider_mw4nur23.unit, 'min') : 0,
            max: slider_mw4nur23 && slider_mw4nur23.unit ? slider_mw4nur23Function(slider_mw4nur23.unit, 'max') : 100,
            step: slider_mw4nur23 && slider_mw4nur23.unit ? slider_mw4nur23Function(slider_mw4nur23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mw4nur23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            }, ( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_28idto23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_28idto23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            }, ( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_c3w3xm23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_c3w3xm23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            }, ( select_l6nskb23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_7gudm823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7gudm823: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Content Header"), initialOpen: false },
 React.createElement(Pmgc_Background, {
            
            value: background_fya9if23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_fya9if23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Avatar Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_mxphtu23,
            
            min: slider_mxphtu23 && slider_mxphtu23.unit ? slider_mxphtu23Function(slider_mxphtu23.unit, 'min') : 0,
            max: slider_mxphtu23 && slider_mxphtu23.unit ? slider_mxphtu23Function(slider_mxphtu23.unit, 'max') : 100,
            step: slider_mxphtu23 && slider_mxphtu23.unit ? slider_mxphtu23Function(slider_mxphtu23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_mxphtu23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_2h080623,
            
            min: slider_2h080623 && slider_2h080623.unit ? slider_2h080623Function(slider_2h080623.unit, 'min') : 0,
            max: slider_2h080623 && slider_2h080623.unit ? slider_2h080623Function(slider_2h080623.unit, 'max') : 100,
            step: slider_2h080623 && slider_2h080623.unit ? slider_2h080623Function(slider_2h080623.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2h080623: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: color_u45lua23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_u45lua23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_k7nhoe23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_k7nhoe23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_uni09u23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_uni09u23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Profile Title"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_qyqx1225,
            
            min: slider_qyqx1225 && slider_qyqx1225.unit ? slider_qyqx1225Function(slider_qyqx1225.unit, 'min') : 0,
            max: slider_qyqx1225 && slider_qyqx1225.unit ? slider_qyqx1225Function(slider_qyqx1225.unit, 'max') : 100,
            step: slider_qyqx1225 && slider_qyqx1225.unit ? slider_qyqx1225Function(slider_qyqx1225.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_qyqx1225: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_17yuoc25,
            
            min: slider_17yuoc25 && slider_17yuoc25.unit ? slider_17yuoc25Function(slider_17yuoc25.unit, 'min') : 0,
            max: slider_17yuoc25 && slider_17yuoc25.unit ? slider_17yuoc25Function(slider_17yuoc25.unit, 'max') : 100,
            step: slider_17yuoc25 && slider_17yuoc25.unit ? slider_17yuoc25Function(slider_17yuoc25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_17yuoc25: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_xia4h723,
            onChange: (value) => setAttributes({ typography_xia4h723: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_oraozg23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_oraozg23: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_i0o17225,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_i0o17225: value }),
            }), 
), React.createElement(PanelBody, { title: __("Profile Subtitle"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_ak69m625,
            
            min: slider_ak69m625 && slider_ak69m625.unit ? slider_ak69m625Function(slider_ak69m625.unit, 'min') : 0,
            max: slider_ak69m625 && slider_ak69m625.unit ? slider_ak69m625Function(slider_ak69m625.unit, 'max') : 100,
            step: slider_ak69m625 && slider_ak69m625.unit ? slider_ak69m625Function(slider_ak69m625.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ak69m625: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_yzufd925,
            
            min: slider_yzufd925 && slider_yzufd925.unit ? slider_yzufd925Function(slider_yzufd925.unit, 'min') : 0,
            max: slider_yzufd925 && slider_yzufd925.unit ? slider_yzufd925Function(slider_yzufd925.unit, 'max') : 100,
            step: slider_yzufd925 && slider_yzufd925.unit ? slider_yzufd925Function(slider_yzufd925.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_yzufd925: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_mcrrug23,
            onChange: (value) => setAttributes({ typography_mcrrug23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_kawax723,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_kawax723: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_cbsu8v25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cbsu8v25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Popup Body"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Title`),
            value: heading_n4q47y23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_8np72823,
            onChange: (value) => setAttributes({ typography_8np72823: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_0wn6u123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0wn6u123: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Message`),
            value: heading_oc81cg23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_oim9na23,
            onChange: (value) => setAttributes({ typography_oim9na23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_u1dm9m23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_u1dm9m23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Time`),
            value: heading_9cq99223,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_jw21k123,
            onChange: (value) => setAttributes({ typography_jw21k123: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_lmmbec23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_lmmbec23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_qjrdgj23,
            sources: ["color","image","gradient"],
            separator:'before',
            
            onChange: (value) => setAttributes({ background_qjrdgj23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Loader"), initialOpen: false },
 React.createElement(Pmgc_Color, {
            label: __(`Color 1`),
            value: color_9odl9323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9odl9323: value }),
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Color 2`),
            value: color_anbrrw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_anbrrw23: value }),
            }), 
), React.createElement(PanelBody, { title: __("Popup Button"), initialOpen: false },
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Outer`),
            value: heading_ygznk023,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ofe1s523,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ofe1s523: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Background, {
            
            value: background_lpaz0623,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lpaz0623: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Button`),
            value: heading_tl42m423,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_kfsp3j24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_kfsp3j24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_2uyqzo23,
            onChange: (value) => setAttributes({ typography_2uyqzo23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_of6pef23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_of6pef23: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_j8ky0523,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_j8ky0523: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_um2xc923,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_um2xc923: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_nsvcp223,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_nsvcp223: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_lwnprh23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_lwnprh23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_s55e8823,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_s55e8823: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_lsqnsv23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_lsqnsv23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_v893xn23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_v893xn23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_pjwdj623,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_pjwdj623: value }),
            }), 
), 
), 
),( select_egyy2t23 == "icon" ) && React.createElement(PanelBody, { title: __("Popup Button Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_32v3hk23,
            
            min: slider_32v3hk23 && slider_32v3hk23.unit ? slider_32v3hk23Function(slider_32v3hk23.unit, 'min') : 0,
            max: slider_32v3hk23 && slider_32v3hk23.unit ? slider_32v3hk23Function(slider_32v3hk23.unit, 'max') : 100,
            step: slider_32v3hk23 && slider_32v3hk23.unit ? slider_32v3hk23Function(slider_32v3hk23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_32v3hk23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_uh8ghe23,
            
            min: slider_uh8ghe23 && slider_uh8ghe23.unit ? slider_uh8ghe23Function(slider_uh8ghe23.unit, 'min') : 0,
            max: slider_uh8ghe23 && slider_uh8ghe23.unit ? slider_uh8ghe23Function(slider_uh8ghe23.unit, 'max') : 100,
            step: slider_uh8ghe23 && slider_uh8ghe23.unit ? slider_uh8ghe23Function(slider_uh8ghe23.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_uh8ghe23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_oag24j23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_oag24j23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_egk1he23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_egk1he23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Popup Background"), initialOpen: false },
 React.createElement(Pmgc_RadioAdvanced, {
            label: __(`Alignment`),
            separator:'default',
            
            options : [{ label: __('Left'), value: 'left', title: __('Left'), icon: 'fas fa-align-left', svg: '' }, 
{ label: __('Center'), value: 'center', title: __('Center'), icon: 'fas fa-align-center', svg: '' }, 
{ label: __('Right'), value: 'right', title: __('Right'), icon: 'fas fa-align-right', svg: '' }, 
],
            value: choose_w125ax23,
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ choose_w125ax23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_cl7ey923,
            
            min: slider_cl7ey923 && slider_cl7ey923.unit ? slider_cl7ey923Function(slider_cl7ey923.unit, 'min') : 0,
            max: slider_cl7ey923 && slider_cl7ey923.unit ? slider_cl7ey923Function(slider_cl7ey923.unit, 'max') : 100,
            step: slider_cl7ey923 && slider_cl7ey923.unit ? slider_cl7ey923Function(slider_cl7ey923.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_cl7ey923: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_hqy35f24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_hqy35f24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_bkdazs24,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_bkdazs24: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_n36rc224,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_n36rc224: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_b3o1jt24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_b3o1jt24: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-opjpzt24", block_id, false, props.clientId);
                }
            }

            
let g_text_vyi5pp23 = text_vyi5pp23 && text_vyi5pp23 != undefined  ? text_vyi5pp23 : "";
let g_select_l6nskb23 = select_l6nskb23 && select_l6nskb23 != undefined  ? select_l6nskb23 : "";
let g_select_7ixk5r25 = select_7ixk5r25 && select_7ixk5r25 != undefined && ( (select_l6nskb23 == "icon") ) ? select_7ixk5r25 : "";
let g_iconscontrol_0p5x3523 = iconscontrol_0p5x3523 != undefined && ( (select_l6nskb23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_0p5x3523+'"></i></span>' : '';

let g_text_wiqdor23 = text_wiqdor23 && text_wiqdor23 != undefined  ? text_wiqdor23 : "";
let g_media_vq0g6123 = media_vq0g6123 && media_vq0g6123.url && media_vq0g6123.url != undefined  ? media_vq0g6123.url : "";
let g_iconscontrol_2n1gbw24 = iconscontrol_2n1gbw24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_2n1gbw24+'"></i></span>' : '';

let g_text_obeg2k23 = text_obeg2k23 && text_obeg2k23 != undefined  ? text_obeg2k23 : "";
let g_iconscontrol_m6x3ve24 = iconscontrol_m6x3ve24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_m6x3ve24+'"></i></span>' : '';

let g_textarea_q2tlj223 = textarea_q2tlj223 && textarea_q2tlj223 != undefined  ? textarea_q2tlj223 : "";
let g_text_by1gvg23 = text_by1gvg23 && text_by1gvg23 != undefined  ? text_by1gvg23 : "";
let g_select_egyy2t23 = select_egyy2t23 && select_egyy2t23 != undefined  ? select_egyy2t23 : "";
let g_iconscontrol_4r22vh23 = iconscontrol_4r22vh23 != undefined && ( (select_egyy2t23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_4r22vh23+'"></i></span>' : '';

let g_text_wge8i023 = text_wge8i023 && text_wge8i023 != undefined  ? text_wge8i023 : "";
let g_rawhtml_072sti25 = rawhtml_072sti25 && rawhtml_072sti25 != undefined  ? rawhtml_072sti25 : "";
let g_textarea_riravg23 = textarea_riravg23 && textarea_riravg23 != undefined  ? textarea_riravg23 : "";
let g_switcher_xuo26v23 = switcher_xuo26v23 && switcher_xuo26v23 != undefined  ? 'yes' : "";
let g_switcher_wvlbhb23 = switcher_wvlbhb23 && switcher_wvlbhb23 != undefined  ? 'active' : "";
let g_rawhtml_aovgsr25 = rawhtml_aovgsr25 && rawhtml_aovgsr25 != undefined  ? rawhtml_aovgsr25 : "";
let g_dimension_in5gfk24 = dimension_in5gfk24 && dimension_in5gfk24 != undefined  ? dimension_in5gfk24 : "";
let g_typography_htd7qf23 = typography_htd7qf23 && typography_htd7qf23 != undefined  ? typography_htd7qf23 : "";
let g_color_sy57cu23 = color_sy57cu23 && color_sy57cu23 != undefined  ? color_sy57cu23 : "";
let g_color_dk27qn23 = color_dk27qn23 && color_dk27qn23 != undefined && ( switcher_xuo26v23 ) ? color_dk27qn23 : "";
let g_background_ecwnl223 = background_ecwnl223 && background_ecwnl223 != undefined  ? background_ecwnl223 : "";
let g_border_5esc5f23 = border_5esc5f23 && border_5esc5f23 != undefined  ? border_5esc5f23 : "";
let g_dimension_dqp9qp23 = dimension_dqp9qp23 && dimension_dqp9qp23 != undefined  ? dimension_dqp9qp23 : "";
let g_boxshadow_cxjrq723 = boxshadow_cxjrq723 && boxshadow_cxjrq723 != undefined  ? boxshadow_cxjrq723 : "";
let g_color_5fw9ji23 = color_5fw9ji23 && color_5fw9ji23 != undefined  ? color_5fw9ji23 : "";
let g_color_ku18rf23 = color_ku18rf23 && color_ku18rf23 != undefined && ( switcher_xuo26v23 ) ? color_ku18rf23 : "";
let g_background_9ob81j23 = background_9ob81j23 && background_9ob81j23 != undefined  ? background_9ob81j23 : "";
let g_border_47tsxy23 = border_47tsxy23 && border_47tsxy23 != undefined  ? border_47tsxy23 : "";
let g_boxshadow_t7be2123 = boxshadow_t7be2123 && boxshadow_t7be2123 != undefined  ? boxshadow_t7be2123 : "";
let g_color_yr45co23 = color_yr45co23 && color_yr45co23 != undefined  ? color_yr45co23 : "";
let g_color_d4iumi23 = color_d4iumi23 && color_d4iumi23 != undefined && ( switcher_xuo26v23 ) ? color_d4iumi23 : "";
let g_background_7crk3423 = background_7crk3423 && background_7crk3423 != undefined  ? background_7crk3423 : "";
let g_border_3vpgwj23 = border_3vpgwj23 && border_3vpgwj23 != undefined  ? border_3vpgwj23 : "";
let g_dimension_ny2hx423 = dimension_ny2hx423 && dimension_ny2hx423 != undefined  ? dimension_ny2hx423 : "";
let g_boxshadow_qawmmw23 = boxshadow_qawmmw23 && boxshadow_qawmmw23 != undefined  ? boxshadow_qawmmw23 : "";
let g_color_28idto23 = color_28idto23 && color_28idto23 != undefined && ( (select_l6nskb23 == "icon") ) ? color_28idto23 : "";
let g_color_7gudm823 = color_7gudm823 && color_7gudm823 != undefined && ( (select_l6nskb23 == "icon") ) ? color_7gudm823 : "";
let g_color_c3w3xm23 = color_c3w3xm23 && color_c3w3xm23 != undefined && ( (select_l6nskb23 == "icon") ) ? color_c3w3xm23 : "";
let g_background_fya9if23 = background_fya9if23 && background_fya9if23 != undefined  ? background_fya9if23 : "";
let g_color_u45lua23 = color_u45lua23 && color_u45lua23 != undefined  ? color_u45lua23 : "";
let g_border_k7nhoe23 = border_k7nhoe23 && border_k7nhoe23 != undefined  ? border_k7nhoe23 : "";
let g_boxshadow_uni09u23 = boxshadow_uni09u23 && boxshadow_uni09u23 != undefined  ? boxshadow_uni09u23 : "";
let g_typography_xia4h723 = typography_xia4h723 && typography_xia4h723 != undefined  ? typography_xia4h723 : "";
let g_color_oraozg23 = color_oraozg23 && color_oraozg23 != undefined  ? color_oraozg23 : "";
let g_color_i0o17225 = color_i0o17225 && color_i0o17225 != undefined  ? color_i0o17225 : "";
let g_typography_mcrrug23 = typography_mcrrug23 && typography_mcrrug23 != undefined  ? typography_mcrrug23 : "";
let g_color_kawax723 = color_kawax723 && color_kawax723 != undefined  ? color_kawax723 : "";
let g_color_cbsu8v25 = color_cbsu8v25 && color_cbsu8v25 != undefined  ? color_cbsu8v25 : "";
let g_heading_n4q47y23 = heading_n4q47y23 && heading_n4q47y23 != undefined  ? heading_n4q47y23 : "";
let g_typography_8np72823 = typography_8np72823 && typography_8np72823 != undefined  ? typography_8np72823 : "";
let g_color_0wn6u123 = color_0wn6u123 && color_0wn6u123 != undefined  ? color_0wn6u123 : "";
let g_heading_oc81cg23 = heading_oc81cg23 && heading_oc81cg23 != undefined  ? heading_oc81cg23 : "";
let g_typography_oim9na23 = typography_oim9na23 && typography_oim9na23 != undefined  ? typography_oim9na23 : "";
let g_color_u1dm9m23 = color_u1dm9m23 && color_u1dm9m23 != undefined  ? color_u1dm9m23 : "";
let g_heading_9cq99223 = heading_9cq99223 && heading_9cq99223 != undefined  ? heading_9cq99223 : "";
let g_typography_jw21k123 = typography_jw21k123 && typography_jw21k123 != undefined  ? typography_jw21k123 : "";
let g_color_lmmbec23 = color_lmmbec23 && color_lmmbec23 != undefined  ? color_lmmbec23 : "";
let g_background_qjrdgj23 = background_qjrdgj23 && background_qjrdgj23 != undefined  ? background_qjrdgj23 : "";
let g_color_9odl9323 = color_9odl9323 && color_9odl9323 != undefined  ? color_9odl9323 : "";
let g_color_anbrrw23 = color_anbrrw23 && color_anbrrw23 != undefined  ? color_anbrrw23 : "";
let g_heading_ygznk023 = heading_ygznk023 && heading_ygznk023 != undefined  ? heading_ygznk023 : "";
let g_dimension_ofe1s523 = dimension_ofe1s523 && dimension_ofe1s523 != undefined  ? dimension_ofe1s523 : "";
let g_background_lpaz0623 = background_lpaz0623 && background_lpaz0623 != undefined  ? background_lpaz0623 : "";
let g_heading_tl42m423 = heading_tl42m423 && heading_tl42m423 != undefined  ? heading_tl42m423 : "";
let g_dimension_kfsp3j24 = dimension_kfsp3j24 && dimension_kfsp3j24 != undefined  ? dimension_kfsp3j24 : "";
let g_typography_2uyqzo23 = typography_2uyqzo23 && typography_2uyqzo23 != undefined  ? typography_2uyqzo23 : "";
let g_color_of6pef23 = color_of6pef23 && color_of6pef23 != undefined  ? color_of6pef23 : "";
let g_background_j8ky0523 = background_j8ky0523 && background_j8ky0523 != undefined  ? background_j8ky0523 : "";
let g_border_um2xc923 = border_um2xc923 && border_um2xc923 != undefined  ? border_um2xc923 : "";
let g_dimension_nsvcp223 = dimension_nsvcp223 && dimension_nsvcp223 != undefined  ? dimension_nsvcp223 : "";
let g_boxshadow_lwnprh23 = boxshadow_lwnprh23 && boxshadow_lwnprh23 != undefined  ? boxshadow_lwnprh23 : "";
let g_color_s55e8823 = color_s55e8823 && color_s55e8823 != undefined  ? color_s55e8823 : "";
let g_background_lsqnsv23 = background_lsqnsv23 && background_lsqnsv23 != undefined  ? background_lsqnsv23 : "";
let g_border_v893xn23 = border_v893xn23 && border_v893xn23 != undefined  ? border_v893xn23 : "";
let g_boxshadow_pjwdj623 = boxshadow_pjwdj623 && boxshadow_pjwdj623 != undefined  ? boxshadow_pjwdj623 : "";
let g_color_oag24j23 = color_oag24j23 && color_oag24j23 != undefined  ? color_oag24j23 : "";
let g_color_egk1he23 = color_egk1he23 && color_egk1he23 != undefined  ? color_egk1he23 : "";
let g_choose_w125ax23 = choose_w125ax23 && choose_w125ax23 != undefined  ? choose_w125ax23 : "";
let g_background_hqy35f24 = background_hqy35f24 && background_hqy35f24 != undefined  ? background_hqy35f24 : "";
let g_border_bkdazs24 = border_bkdazs24 && border_bkdazs24 != undefined  ? border_bkdazs24 : "";
let g_dimension_n36rc224 = dimension_n36rc224 && dimension_n36rc224 != undefined  ? dimension_n36rc224 : "";
let g_boxshadow_b3o1jt24 = boxshadow_b3o1jt24 && boxshadow_b3o1jt24 != undefined  ? boxshadow_b3o1jt24 : "";
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_opjpzt24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-whatsapp-chat">
    <div class="wkit-wa-inner-wrap">
        <div class="wkit-whatsapp-chat-inner ${g_switcher_wvlbhb23}">
            <div class="wkit-whatsapp-header">
                <div class="header-avatar" data-src="${g_media_vq0g6123}">
                    <div class="avatar-img-wrap">
                        <img class="avatar-image" src="${g_media_vq0g6123}">
                    </div>
                </div>
                <div class="header-content">
                    <span class="header-name" data-text="${g_text_wiqdor23}">${g_text_wiqdor23}${g_iconscontrol_2n1gbw24}</span>
                    <span class="header-time" data-text="${g_text_obeg2k23}">${g_iconscontrol_m6x3ve24}${g_text_obeg2k23}</span>
                </div>
                <div class="wkit-wa-popup-close">+</div>
            </div>
            <div class="wkit-whatsapp-body">
                <div class="whatsapp-typing-loader">
                    <div class="what-dot dot-1"></div>
                    <div class="what-dot dot-2"></div>
                    <div class="what-dot dot-3"></div>
                </div>
                <div class="body-main-content">
                    <div class="author-name" data-text="${g_text_wiqdor23}">${g_text_wiqdor23}</div>
                    <div class="content-text" data-text="${g_textarea_q2tlj223}">${g_textarea_q2tlj223}</div>
                    <div class="content-time"></div>
                </div>
            </div>
            <div class="wkit-wa-btn-wrap type-${g_select_egyy2t23}" wa-number="${g_text_wge8i023}" wa-message="${g_textarea_riravg23}">
                <a class="wkit-whatsapp-btn" target="_blank" rel="noopener">
                    ${g_iconscontrol_4r22vh23}
                    <span class="wa-btn-text" data-text="${g_text_by1gvg23}">${g_text_by1gvg23}</span>
                </a>
            </div>
        </div>
        <div class="wkit-whatsapp-toggle type-${g_select_l6nskb23}">
            <div class="wkit-toggle-btn dot-show-${g_switcher_xuo26v23} ${g_switcher_wvlbhb23} ${g_select_7ixk5r25}">
                ${g_iconscontrol_0p5x3523}
                <span class="wkit-tgl-btn-text" data-text="${g_text_vyi5pp23}">${g_text_vyi5pp23}</span>
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
            text_vyi5pp23,
select_l6nskb23,
select_7ixk5r25,
iconscontrol_0p5x3523,
text_wiqdor23,
media_vq0g6123,
iconscontrol_2n1gbw24,
text_obeg2k23,
iconscontrol_m6x3ve24,
textarea_q2tlj223,
text_by1gvg23,
select_egyy2t23,
iconscontrol_4r22vh23,
text_wge8i023,
rawhtml_072sti25,
textarea_riravg23,
switcher_xuo26v23,
switcher_wvlbhb23,
rawhtml_aovgsr25,
dimension_in5gfk24,
slider_lydd7p24,
typography_htd7qf23,
color_sy57cu23,
color_dk27qn23,
background_ecwnl223,
border_5esc5f23,
dimension_dqp9qp23,
boxshadow_cxjrq723,
color_5fw9ji23,
color_ku18rf23,
background_9ob81j23,
border_47tsxy23,
boxshadow_t7be2123,
color_yr45co23,
color_d4iumi23,
background_7crk3423,
border_3vpgwj23,
dimension_ny2hx423,
boxshadow_qawmmw23,
normalhover_fve45k23,
slider_veb44t23,
slider_mw4nur23,
color_28idto23,
color_7gudm823,
color_c3w3xm23,
normalhover_yfwzv825,
background_fya9if23,
slider_mxphtu23,
slider_2h080623,
color_u45lua23,
border_k7nhoe23,
boxshadow_uni09u23,
slider_qyqx1225,
slider_17yuoc25,
typography_xia4h723,
color_oraozg23,
color_i0o17225,
slider_ak69m625,
slider_yzufd925,
typography_mcrrug23,
color_kawax723,
color_cbsu8v25,
heading_n4q47y23,
typography_8np72823,
color_0wn6u123,
heading_oc81cg23,
typography_oim9na23,
color_u1dm9m23,
heading_9cq99223,
typography_jw21k123,
color_lmmbec23,
background_qjrdgj23,
color_9odl9323,
color_anbrrw23,
heading_ygznk023,
dimension_ofe1s523,
background_lpaz0623,
heading_tl42m423,
dimension_kfsp3j24,
typography_2uyqzo23,
color_of6pef23,
background_j8ky0523,
border_um2xc923,
dimension_nsvcp223,
boxshadow_lwnprh23,
color_s55e8823,
background_lsqnsv23,
border_v893xn23,
boxshadow_pjwdj623,
normalhover_vijh8423,
slider_32v3hk23,
slider_uh8ghe23,
color_oag24j23,
color_egk1he23,
normalhover_4xdyrx25,
choose_w125ax23,
slider_cl7ey923,
background_hqy35f24,
border_bkdazs24,
dimension_n36rc224,
boxshadow_b3o1jt24,

            block_id,
        } = attributes;

        

        

        

        
let g_text_vyi5pp23 = text_vyi5pp23 && text_vyi5pp23 != undefined  ? text_vyi5pp23 : "";
let g_select_l6nskb23 = select_l6nskb23 && select_l6nskb23 != undefined  ? select_l6nskb23 : "";
let g_select_7ixk5r25 = select_7ixk5r25 && select_7ixk5r25 != undefined && ( (select_l6nskb23 == "icon") ) ? select_7ixk5r25 : "";
let g_iconscontrol_0p5x3523 = iconscontrol_0p5x3523 != undefined && ( (select_l6nskb23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_0p5x3523+'"></i></span>' : '';

let g_text_wiqdor23 = text_wiqdor23 && text_wiqdor23 != undefined  ? text_wiqdor23 : "";
let g_media_vq0g6123 = media_vq0g6123 && media_vq0g6123.url && media_vq0g6123.url != undefined  ? media_vq0g6123.url : "";
let g_iconscontrol_2n1gbw24 = iconscontrol_2n1gbw24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_2n1gbw24+'"></i></span>' : '';

let g_text_obeg2k23 = text_obeg2k23 && text_obeg2k23 != undefined  ? text_obeg2k23 : "";
let g_iconscontrol_m6x3ve24 = iconscontrol_m6x3ve24 != undefined  ? '<span class="tp-title-icon"><i class="'+iconscontrol_m6x3ve24+'"></i></span>' : '';

let g_textarea_q2tlj223 = textarea_q2tlj223 && textarea_q2tlj223 != undefined  ? textarea_q2tlj223 : "";
let g_text_by1gvg23 = text_by1gvg23 && text_by1gvg23 != undefined  ? text_by1gvg23 : "";
let g_select_egyy2t23 = select_egyy2t23 && select_egyy2t23 != undefined  ? select_egyy2t23 : "";
let g_iconscontrol_4r22vh23 = iconscontrol_4r22vh23 != undefined && ( (select_egyy2t23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_4r22vh23+'"></i></span>' : '';

let g_text_wge8i023 = text_wge8i023 && text_wge8i023 != undefined  ? text_wge8i023 : "";
let g_rawhtml_072sti25 = rawhtml_072sti25 && rawhtml_072sti25 != undefined  ? rawhtml_072sti25 : "";
let g_textarea_riravg23 = textarea_riravg23 && textarea_riravg23 != undefined  ? textarea_riravg23 : "";
let g_switcher_xuo26v23 = switcher_xuo26v23 && switcher_xuo26v23 != undefined  ? 'yes' : "";
let g_switcher_wvlbhb23 = switcher_wvlbhb23 && switcher_wvlbhb23 != undefined  ? 'active' : "";
let g_rawhtml_aovgsr25 = rawhtml_aovgsr25 && rawhtml_aovgsr25 != undefined  ? rawhtml_aovgsr25 : "";
let g_dimension_in5gfk24 = dimension_in5gfk24 && dimension_in5gfk24 != undefined  ? dimension_in5gfk24 : "";
let g_typography_htd7qf23 = typography_htd7qf23 && typography_htd7qf23 != undefined  ? typography_htd7qf23 : "";
let g_color_sy57cu23 = color_sy57cu23 && color_sy57cu23 != undefined  ? color_sy57cu23 : "";
let g_color_dk27qn23 = color_dk27qn23 && color_dk27qn23 != undefined && ( switcher_xuo26v23 ) ? color_dk27qn23 : "";
let g_background_ecwnl223 = background_ecwnl223 && background_ecwnl223 != undefined  ? background_ecwnl223 : "";
let g_border_5esc5f23 = border_5esc5f23 && border_5esc5f23 != undefined  ? border_5esc5f23 : "";
let g_dimension_dqp9qp23 = dimension_dqp9qp23 && dimension_dqp9qp23 != undefined  ? dimension_dqp9qp23 : "";
let g_boxshadow_cxjrq723 = boxshadow_cxjrq723 && boxshadow_cxjrq723 != undefined  ? boxshadow_cxjrq723 : "";
let g_color_5fw9ji23 = color_5fw9ji23 && color_5fw9ji23 != undefined  ? color_5fw9ji23 : "";
let g_color_ku18rf23 = color_ku18rf23 && color_ku18rf23 != undefined && ( switcher_xuo26v23 ) ? color_ku18rf23 : "";
let g_background_9ob81j23 = background_9ob81j23 && background_9ob81j23 != undefined  ? background_9ob81j23 : "";
let g_border_47tsxy23 = border_47tsxy23 && border_47tsxy23 != undefined  ? border_47tsxy23 : "";
let g_boxshadow_t7be2123 = boxshadow_t7be2123 && boxshadow_t7be2123 != undefined  ? boxshadow_t7be2123 : "";
let g_color_yr45co23 = color_yr45co23 && color_yr45co23 != undefined  ? color_yr45co23 : "";
let g_color_d4iumi23 = color_d4iumi23 && color_d4iumi23 != undefined && ( switcher_xuo26v23 ) ? color_d4iumi23 : "";
let g_background_7crk3423 = background_7crk3423 && background_7crk3423 != undefined  ? background_7crk3423 : "";
let g_border_3vpgwj23 = border_3vpgwj23 && border_3vpgwj23 != undefined  ? border_3vpgwj23 : "";
let g_dimension_ny2hx423 = dimension_ny2hx423 && dimension_ny2hx423 != undefined  ? dimension_ny2hx423 : "";
let g_boxshadow_qawmmw23 = boxshadow_qawmmw23 && boxshadow_qawmmw23 != undefined  ? boxshadow_qawmmw23 : "";
let g_color_28idto23 = color_28idto23 && color_28idto23 != undefined && ( (select_l6nskb23 == "icon") ) ? color_28idto23 : "";
let g_color_7gudm823 = color_7gudm823 && color_7gudm823 != undefined && ( (select_l6nskb23 == "icon") ) ? color_7gudm823 : "";
let g_color_c3w3xm23 = color_c3w3xm23 && color_c3w3xm23 != undefined && ( (select_l6nskb23 == "icon") ) ? color_c3w3xm23 : "";
let g_background_fya9if23 = background_fya9if23 && background_fya9if23 != undefined  ? background_fya9if23 : "";
let g_color_u45lua23 = color_u45lua23 && color_u45lua23 != undefined  ? color_u45lua23 : "";
let g_border_k7nhoe23 = border_k7nhoe23 && border_k7nhoe23 != undefined  ? border_k7nhoe23 : "";
let g_boxshadow_uni09u23 = boxshadow_uni09u23 && boxshadow_uni09u23 != undefined  ? boxshadow_uni09u23 : "";
let g_typography_xia4h723 = typography_xia4h723 && typography_xia4h723 != undefined  ? typography_xia4h723 : "";
let g_color_oraozg23 = color_oraozg23 && color_oraozg23 != undefined  ? color_oraozg23 : "";
let g_color_i0o17225 = color_i0o17225 && color_i0o17225 != undefined  ? color_i0o17225 : "";
let g_typography_mcrrug23 = typography_mcrrug23 && typography_mcrrug23 != undefined  ? typography_mcrrug23 : "";
let g_color_kawax723 = color_kawax723 && color_kawax723 != undefined  ? color_kawax723 : "";
let g_color_cbsu8v25 = color_cbsu8v25 && color_cbsu8v25 != undefined  ? color_cbsu8v25 : "";
let g_heading_n4q47y23 = heading_n4q47y23 && heading_n4q47y23 != undefined  ? heading_n4q47y23 : "";
let g_typography_8np72823 = typography_8np72823 && typography_8np72823 != undefined  ? typography_8np72823 : "";
let g_color_0wn6u123 = color_0wn6u123 && color_0wn6u123 != undefined  ? color_0wn6u123 : "";
let g_heading_oc81cg23 = heading_oc81cg23 && heading_oc81cg23 != undefined  ? heading_oc81cg23 : "";
let g_typography_oim9na23 = typography_oim9na23 && typography_oim9na23 != undefined  ? typography_oim9na23 : "";
let g_color_u1dm9m23 = color_u1dm9m23 && color_u1dm9m23 != undefined  ? color_u1dm9m23 : "";
let g_heading_9cq99223 = heading_9cq99223 && heading_9cq99223 != undefined  ? heading_9cq99223 : "";
let g_typography_jw21k123 = typography_jw21k123 && typography_jw21k123 != undefined  ? typography_jw21k123 : "";
let g_color_lmmbec23 = color_lmmbec23 && color_lmmbec23 != undefined  ? color_lmmbec23 : "";
let g_background_qjrdgj23 = background_qjrdgj23 && background_qjrdgj23 != undefined  ? background_qjrdgj23 : "";
let g_color_9odl9323 = color_9odl9323 && color_9odl9323 != undefined  ? color_9odl9323 : "";
let g_color_anbrrw23 = color_anbrrw23 && color_anbrrw23 != undefined  ? color_anbrrw23 : "";
let g_heading_ygznk023 = heading_ygznk023 && heading_ygznk023 != undefined  ? heading_ygznk023 : "";
let g_dimension_ofe1s523 = dimension_ofe1s523 && dimension_ofe1s523 != undefined  ? dimension_ofe1s523 : "";
let g_background_lpaz0623 = background_lpaz0623 && background_lpaz0623 != undefined  ? background_lpaz0623 : "";
let g_heading_tl42m423 = heading_tl42m423 && heading_tl42m423 != undefined  ? heading_tl42m423 : "";
let g_dimension_kfsp3j24 = dimension_kfsp3j24 && dimension_kfsp3j24 != undefined  ? dimension_kfsp3j24 : "";
let g_typography_2uyqzo23 = typography_2uyqzo23 && typography_2uyqzo23 != undefined  ? typography_2uyqzo23 : "";
let g_color_of6pef23 = color_of6pef23 && color_of6pef23 != undefined  ? color_of6pef23 : "";
let g_background_j8ky0523 = background_j8ky0523 && background_j8ky0523 != undefined  ? background_j8ky0523 : "";
let g_border_um2xc923 = border_um2xc923 && border_um2xc923 != undefined  ? border_um2xc923 : "";
let g_dimension_nsvcp223 = dimension_nsvcp223 && dimension_nsvcp223 != undefined  ? dimension_nsvcp223 : "";
let g_boxshadow_lwnprh23 = boxshadow_lwnprh23 && boxshadow_lwnprh23 != undefined  ? boxshadow_lwnprh23 : "";
let g_color_s55e8823 = color_s55e8823 && color_s55e8823 != undefined  ? color_s55e8823 : "";
let g_background_lsqnsv23 = background_lsqnsv23 && background_lsqnsv23 != undefined  ? background_lsqnsv23 : "";
let g_border_v893xn23 = border_v893xn23 && border_v893xn23 != undefined  ? border_v893xn23 : "";
let g_boxshadow_pjwdj623 = boxshadow_pjwdj623 && boxshadow_pjwdj623 != undefined  ? boxshadow_pjwdj623 : "";
let g_color_oag24j23 = color_oag24j23 && color_oag24j23 != undefined  ? color_oag24j23 : "";
let g_color_egk1he23 = color_egk1he23 && color_egk1he23 != undefined  ? color_egk1he23 : "";
let g_choose_w125ax23 = choose_w125ax23 && choose_w125ax23 != undefined  ? choose_w125ax23 : "";
let g_background_hqy35f24 = background_hqy35f24 && background_hqy35f24 != undefined  ? background_hqy35f24 : "";
let g_border_bkdazs24 = border_bkdazs24 && border_bkdazs24 != undefined  ? border_bkdazs24 : "";
let g_dimension_n36rc224 = dimension_n36rc224 && dimension_n36rc224 != undefined  ? dimension_n36rc224 : "";
let g_boxshadow_b3o1jt24 = boxshadow_b3o1jt24 && boxshadow_b3o1jt24 != undefined  ? boxshadow_b3o1jt24 : "";
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-opjpzt24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_opjpzt24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-whatsapp-chat">
    <div class="wkit-wa-inner-wrap">
        <div class="wkit-whatsapp-chat-inner ${g_switcher_wvlbhb23}">
            <div class="wkit-whatsapp-header">
                <div class="header-avatar" data-src="${g_media_vq0g6123}">
                    <div class="avatar-img-wrap">
                        <img class="avatar-image" src="${g_media_vq0g6123}">
                    </div>
                </div>
                <div class="header-content">
                    <span class="header-name" data-text="${g_text_wiqdor23}">${g_text_wiqdor23}${g_iconscontrol_2n1gbw24}</span>
                    <span class="header-time" data-text="${g_text_obeg2k23}">${g_iconscontrol_m6x3ve24}${g_text_obeg2k23}</span>
                </div>
                <div class="wkit-wa-popup-close">+</div>
            </div>
            <div class="wkit-whatsapp-body">
                <div class="whatsapp-typing-loader">
                    <div class="what-dot dot-1"></div>
                    <div class="what-dot dot-2"></div>
                    <div class="what-dot dot-3"></div>
                </div>
                <div class="body-main-content">
                    <div class="author-name" data-text="${g_text_wiqdor23}">${g_text_wiqdor23}</div>
                    <div class="content-text" data-text="${g_textarea_q2tlj223}">${g_textarea_q2tlj223}</div>
                    <div class="content-time"></div>
                </div>
            </div>
            <div class="wkit-wa-btn-wrap type-${g_select_egyy2t23}" wa-number="${g_text_wge8i023}" wa-message="${g_textarea_riravg23}">
                <a class="wkit-whatsapp-btn" target="_blank" rel="noopener">
                    ${g_iconscontrol_4r22vh23}
                    <span class="wa-btn-text" data-text="${g_text_by1gvg23}">${g_text_by1gvg23}</span>
                </a>
            </div>
        </div>
        <div class="wkit-whatsapp-toggle type-${g_select_l6nskb23}">
            <div class="wkit-toggle-btn dot-show-${g_switcher_xuo26v23} ${g_switcher_wvlbhb23} ${g_select_7ixk5r25}">
                ${g_iconscontrol_0p5x3523}
                <span class="wkit-tgl-btn-text" data-text="${g_text_vyi5pp23}">${g_text_vyi5pp23}</span>
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
    new WhatsApp_Chat_opjpzt24();