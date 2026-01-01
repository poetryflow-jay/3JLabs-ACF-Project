
    class Vertical_Level_7kq3uw24 {
        constructor() {
            this.Vertical_Level_7kq3uw24_fdbimy25();
        }
    
        Vertical_Level_7kq3uw24_fdbimy25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Note,Pmgc_Text,Pmgc_IconList,Pmgc_Media,Pmgc_TextArea,Pmgc_Toggle,Pmgc_Label_Heading,Pmgc_Color,Pmgc_Repeater,Pmgc_Range,Pmgc_Tabs,Pmgc_Dimension,Pmgc_Typography,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-7kq3uw24', {
        title: __('Vertical Level'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-ellipsis-v tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Vertical Level'),__('Step by Step Guide'),__('Creative Representation'),__('Procedural Information'),__('Website Processes'),__('Visual Clarity'),__('Vertical Steps'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_b5wt1p24Function = (unit, type) => {
                var g_slider_b5wt1p24_list = [];
                g_slider_b5wt1p24_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_b5wt1p24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_b5wt1p24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_b5wt1p24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_b5wt1p24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_b5wt1p24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_b5wt1p24_list[unit][type];
            };
const slider_spv9a524Function = (unit, type) => {
                var g_slider_spv9a524_list = [];
                g_slider_spv9a524_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_spv9a524_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_spv9a524_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_spv9a524_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_spv9a524_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_spv9a524_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_spv9a524_list[unit][type];
            };
const slider_4f9ksm23Function = (unit, type) => {
                var g_slider_4f9ksm23_list = [];
                g_slider_4f9ksm23_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_4f9ksm23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_4f9ksm23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_4f9ksm23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_4f9ksm23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_4f9ksm23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_4f9ksm23_list[unit][type];
            };
const slider_17rycx23Function = (unit, type) => {
                var g_slider_17rycx23_list = [];
                g_slider_17rycx23_list['px'] = { "type": "px", "min": 0, "max": 2000, "step": 3 };
g_slider_17rycx23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_17rycx23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_17rycx23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_17rycx23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_17rycx23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_17rycx23_list[unit][type];
            };
const slider_tdgput25Function = (unit, type) => {
                var g_slider_tdgput25_list = [];
                g_slider_tdgput25_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_tdgput25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_tdgput25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_tdgput25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_tdgput25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_tdgput25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_tdgput25_list[unit][type];
            };
const slider_bsl6pe23Function = (unit, type) => {
                var g_slider_bsl6pe23_list = [];
                g_slider_bsl6pe23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_bsl6pe23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_bsl6pe23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_bsl6pe23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_bsl6pe23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_bsl6pe23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_bsl6pe23_list[unit][type];
            };
const slider_2mmjo823Function = (unit, type) => {
                var g_slider_2mmjo823_list = [];
                g_slider_2mmjo823_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_2mmjo823_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_2mmjo823_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2mmjo823_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2mmjo823_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2mmjo823_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2mmjo823_list[unit][type];
            };
const slider_humxqr24Function = (unit, type) => {
                var g_slider_humxqr24_list = [];
                g_slider_humxqr24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_humxqr24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_humxqr24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_humxqr24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_humxqr24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_humxqr24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_humxqr24_list[unit][type];
            };
const slider_44n3jp24Function = (unit, type) => {
                var g_slider_44n3jp24_list = [];
                g_slider_44n3jp24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_44n3jp24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_44n3jp24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_44n3jp24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_44n3jp24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_44n3jp24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_44n3jp24_list[unit][type];
            };
const slider_cl6adz24Function = (unit, type) => {
                var g_slider_cl6adz24_list = [];
                g_slider_cl6adz24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_cl6adz24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_cl6adz24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_cl6adz24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_cl6adz24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_cl6adz24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_cl6adz24_list[unit][type];
            };
const slider_ngxjcy24Function = (unit, type) => {
                var g_slider_ngxjcy24_list = [];
                g_slider_ngxjcy24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_ngxjcy24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_ngxjcy24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ngxjcy24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ngxjcy24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ngxjcy24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ngxjcy24_list[unit][type];
            };
const slider_5l0r9k24Function = (unit, type) => {
                var g_slider_5l0r9k24_list = [];
                g_slider_5l0r9k24_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_5l0r9k24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_5l0r9k24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_5l0r9k24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_5l0r9k24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_5l0r9k24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_5l0r9k24_list[unit][type];
            };
const slider_hiw30v24Function = (unit, type) => {
                var g_slider_hiw30v24_list = [];
                g_slider_hiw30v24_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_hiw30v24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_hiw30v24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_hiw30v24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_hiw30v24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_hiw30v24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_hiw30v24_list[unit][type];
            };
const slider_j8anjs23Function = (unit, type) => {
                var g_slider_j8anjs23_list = [];
                g_slider_j8anjs23_list['px'] = { "type": "px", "min": 0, "max": 40, "step": 1 };
g_slider_j8anjs23_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_j8anjs23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_j8anjs23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_j8anjs23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_j8anjs23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_j8anjs23_list[unit][type];
            };
const slider_24zrck25Function = (unit, type) => {
                var g_slider_24zrck25_list = [];
                g_slider_24zrck25_list['px'] = { "type": "px", "min": -100, "max": 100, "step": 1 };
g_slider_24zrck25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_24zrck25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_24zrck25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_24zrck25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_24zrck25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_24zrck25_list[unit][type];
            };
const slider_zasg0323Function = (unit, type) => {
                var g_slider_zasg0323_list = [];
                g_slider_zasg0323_list['px'] = { "type": "px", "min": 0, "max": 40, "step": 1 };
g_slider_zasg0323_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_zasg0323_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_zasg0323_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_zasg0323_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_zasg0323_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_zasg0323_list[unit][type];
            };
const slider_jy7s4h24Function = (unit, type) => {
                var g_slider_jy7s4h24_list = [];
                g_slider_jy7s4h24_list['px'] = { "type": "px", "min": 0, "max": 20, "step": 1 };
g_slider_jy7s4h24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_jy7s4h24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_jy7s4h24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_jy7s4h24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_jy7s4h24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_jy7s4h24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_nhr9qd23,
rawhtml_tndgki25,
repeater_8rn0w223,
rawhtml_bxa27a25,
slider_b5wt1p24,
slider_spv9a524,
normalhover_qscp2824,
rawhtml_s3p36o25,
dimension_5chiij23,
dimension_ev1anw23,
typography_o0oh3t23,
color_9y0up723,
color_chjm5523,
color_ulpqga23,
normalhover_82bgd023,
rawhtml_bi3ap525,
dimension_vtvtbr23,
dimension_51azh123,
typography_s8eoip23,
color_1eyj9i23,
color_ffmmbb23,
color_67p52023,
normalhover_nul8mm23,
rawhtml_fpq3le25,
slider_4f9ksm23,
color_l9hrrk23,
color_wwnfak23,
color_4mfaaw23,
normalhover_8rtkwq23,
rawhtml_9wq12425,
slider_17rycx23,
slider_tdgput25,
slider_bsl6pe23,
slider_2mmjo823,
slider_humxqr24,
slider_44n3jp24,
slider_cl6adz24,
slider_ngxjcy24,
slider_5l0r9k24,
slider_hiw30v24,
heading_80ntvk23,
color_1q7id323,
heading_z2n3ui23,
color_av4gsa23,
heading_tx6tse23,
heading_dspala23,
heading_flidce23,
heading_d0oggu23,
color_xqa3oq23,
heading_huci9g23,
color_tag0qi23,
normalhover_sjel8l23,
rawhtml_sle38i25,
slider_j8anjs23,
slider_24zrck25,
color_p0839w23,
color_0fsle923,
color_qgd3pi23,
normalhover_hi79cb23,
slider_zasg0323,
color_yn7qqw23,
color_vkzjwf23,
normalhover_810qo123,
slider_jy7s4h24,
color_3vcprb23,
color_n94v6o23,
color_7hl08c25,
normalhover_zeajyo25,
rawhtml_mpweuw25,
dimension_h90zss23,
dimension_z0q74o23,
background_z81eny23,
border_asyv9l23,
dimension_ql5iwo23,
boxshadow_waz3yx23,
background_tna86r23,
border_idqltp23,
boxshadow_8vghll23,
normalhover_cgv0nk23,

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
                        main_function_7kq3uw24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_7kq3uw24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let vlMain = $scope.find('.wkit-multi-step-vl-wrapper');
let vlStep = vlMain[0].querySelectorAll('.wkit-vl-pix-levels-step');

vlStep.forEach(function(el) {
    if (!el.classList.contains('wkit-vl-active')) {
        el.classList.add('wkit-vl-active');
    }else{
        el.classList.remove('wkit-vl-active');
        el.classList.add('wkit-vl-complete');
    }
});

let vlNumber = vlMain[0].querySelectorAll('.wkit-vl-inner-style-2 .wkit-prg-two');
vlNumber.forEach(function(element, index) {
    let inner = element.querySelector('.wkit-vl-pix-levels-dot-inner');

    // Remove existing number-only nodes
    element.childNodes.forEach(node => {
        if (node.nodeType === 3) element.removeChild(node); // remove text nodes
    });

    // Insert number before the inner span
    if (inner) {
        element.insertBefore(document.createTextNode(index + 1), inner);
    } else {
        element.textContent = index + 1;
    }
});

var vlCheckIcon = vlMain[0].querySelectorAll('.wkit-vl-inner-style-3 .wkit-vl-active-yes .wkit-vl-pix-levels-dot-inner');
vlCheckIcon.forEach(function(ele) {
    ele.classList.add('wkit-vl-check-mark');
});

            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['style-1',__('Dot')],['style-2',__('Number')],['style-3',__('Check Mark')],],
                separator:"default",
                
                
                value: select_nhr9qd23,
                onChange: (value) => {setAttributes({ select_nhr9qd23: value }) },
            }),
( select_nhr9qd23 == "style-3" ) && React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_tndgki25,
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
You must enable the ACTIVE field from Repeater when choosing Check Mark to show an active icon.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_tndgki25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Levels`),
            value: repeater_8rn0w223,
            attributeName: 'repeater_8rn0w223',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_8rn0w223: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Select, {
                label: __(`Select`),
                options:[['text',__('Text')],['icon',__('Icon')],['image',__('Image')],],
                separator:"default",
                
                
                value: value.select_ej7gme23,
                onChange: v => { value.select_ej7gme23 = v; onChange(value); },
            }),
( value.select_ej7gme23 == "text" ) && React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_vx4ivy23,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_vx4ivy23 = v; onChange(value); },
            }),
( value.select_ej7gme23 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_e0tko023,
            separator:'default',
            onChange: v => { value.iconscontrol_e0tko023 = v; onChange(value); },
            }), 
( value.select_ej7gme23 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_8061n023,
                
                
                type: ["image","svg"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_8061n023 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"2",
                value: value.wysiwyg_4wbmqg23,
                
                onChange: v => { value.wysiwyg_4wbmqg23 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Active`),
            
            value: value.switcher_lryedf23,
            
            onChange: v => { value.switcher_lryedf23 = (v == true ? 'yes' : v); onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_ur4f3t23,
            separator:"default",
            inlineblock: true,
            }), 
( value.select_ej7gme23 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: value.color_2ynyfv23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_2ynyfv23 = v; onChange(value); },
            }), 
( value.select_ej7gme23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_lrsetc23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_lrsetc23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: value.color_xp4j5c23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_xp4j5c23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_iypfma23,
            separator:"default",
            inlineblock: true,
            }), 
( value.select_ej7gme23 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: value.color_jhnu2423,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_jhnu2423 = v; onChange(value); },
            }), 
( value.select_ej7gme23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_c38r5j23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_c38r5j23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: value.color_a1phqu23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"after",
            
            
            onChange: v => { value.color_a1phqu23 = v; onChange(value); },
            }), 
( !value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: value.color_iyhbs023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_iyhbs023 = v; onChange(value); },
            }), 
( value.switcher_lryedf23 && value.select_ej7gme23 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Active Icon Color`),
            value: value.color_sxy5ra25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_sxy5ra25 = v; onChange(value); },
            }), 
( value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Dot Color`),
            value: value.color_r50bao23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_r50bao23 = v; onChange(value); },
            }), 
( !value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Inner Dot Color`),
            value: value.color_0hfwjr23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_0hfwjr23 = v; onChange(value); },
            }), 
( value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Inner Dot Color`),
            value: value.color_pobie923,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_pobie923 = v; onChange(value); },
            }), 
( !value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Progress Bar Color`),
            value: value.color_fzwbk523,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_fzwbk523 = v; onChange(value); },
            }), 
( value.switcher_lryedf23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Progress Bar Color`),
            value: value.color_jl0z0m23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_jl0z0m23 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_bxa27a25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/vertical-level-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_bxa27a25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Range, {
            label: __(`Opacity`),
            separator:'default',
            value: slider_b5wt1p24,
            
            min: slider_b5wt1p24 && slider_b5wt1p24.unit ? slider_b5wt1p24Function(slider_b5wt1p24.unit, 'min') : 0,
            max: slider_b5wt1p24 && slider_b5wt1p24.unit ? slider_b5wt1p24Function(slider_b5wt1p24.unit, 'max') : 100,
            step: slider_b5wt1p24 && slider_b5wt1p24.unit ? slider_b5wt1p24Function(slider_b5wt1p24.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_b5wt1p24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Range, {
            label: __(`Opacity`),
            separator:'default',
            value: slider_spv9a524,
            
            min: slider_spv9a524 && slider_spv9a524.unit ? slider_spv9a524Function(slider_spv9a524.unit, 'min') : 0,
            max: slider_spv9a524 && slider_spv9a524.unit ? slider_spv9a524Function(slider_spv9a524.unit, 'max') : 100,
            step: slider_spv9a524 && slider_spv9a524.unit ? slider_spv9a524Function(slider_spv9a524.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_spv9a524: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_s3p36o25,
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
You can apply here different opacity for Normal level & Active level.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_s3p36o25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Title"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_5chiij23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5chiij23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_ev1anw23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ev1anw23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_o0oh3t23,
            onChange: (value) => setAttributes({ typography_o0oh3t23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_9y0up723,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_9y0up723: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_chjm5523,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_chjm5523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ulpqga23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ulpqga23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_bi3ap525,
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
If a property is already applied to the main card, then the property applied here will not take effect.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_bi3ap525: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_vtvtbr23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_vtvtbr23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_51azh123,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_51azh123: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_s8eoip23,
            onChange: (value) => setAttributes({ typography_s8eoip23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1eyj9i23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1eyj9i23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_ffmmbb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_ffmmbb23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_67p52023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_67p52023: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_fpq3le25,
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
If a property is already applied to the main card, then the property applied here will not take effect.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_fpq3le25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_4f9ksm23,
            
            min: slider_4f9ksm23 && slider_4f9ksm23.unit ? slider_4f9ksm23Function(slider_4f9ksm23.unit, 'min') : 0,
            max: slider_4f9ksm23 && slider_4f9ksm23.unit ? slider_4f9ksm23Function(slider_4f9ksm23.unit, 'max') : 100,
            step: slider_4f9ksm23 && slider_4f9ksm23.unit ? slider_4f9ksm23Function(slider_4f9ksm23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_4f9ksm23: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_l9hrrk23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_l9hrrk23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_wwnfak23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_wwnfak23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_9wq12425,
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
If a property is already applied to the main card, then the property applied here will not take effect.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_9wq12425: value }),
            }), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_17rycx23,
            
            min: slider_17rycx23 && slider_17rycx23.unit ? slider_17rycx23Function(slider_17rycx23.unit, 'min') : 0,
            max: slider_17rycx23 && slider_17rycx23.unit ? slider_17rycx23Function(slider_17rycx23.unit, 'max') : 100,
            step: slider_17rycx23 && slider_17rycx23.unit ? slider_17rycx23Function(slider_17rycx23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_17rycx23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Image Width`),
            separator:'default',
            value: slider_tdgput25,
            
            min: slider_tdgput25 && slider_tdgput25.unit ? slider_tdgput25Function(slider_tdgput25.unit, 'min') : 0,
            max: slider_tdgput25 && slider_tdgput25.unit ? slider_tdgput25Function(slider_tdgput25.unit, 'max') : 100,
            step: slider_tdgput25 && slider_tdgput25.unit ? slider_tdgput25Function(slider_tdgput25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_tdgput25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Image Height`),
            separator:'default',
            value: slider_bsl6pe23,
            
            min: slider_bsl6pe23 && slider_bsl6pe23.unit ? slider_bsl6pe23Function(slider_bsl6pe23.unit, 'min') : 0,
            max: slider_bsl6pe23 && slider_bsl6pe23.unit ? slider_bsl6pe23Function(slider_bsl6pe23.unit, 'max') : 100,
            step: slider_bsl6pe23 && slider_bsl6pe23.unit ? slider_bsl6pe23Function(slider_bsl6pe23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_bsl6pe23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Image Bottom Gap`),
            separator:'default',
            value: slider_2mmjo823,
            
            min: slider_2mmjo823 && slider_2mmjo823.unit ? slider_2mmjo823Function(slider_2mmjo823.unit, 'min') : 0,
            max: slider_2mmjo823 && slider_2mmjo823.unit ? slider_2mmjo823Function(slider_2mmjo823.unit, 'max') : 100,
            step: slider_2mmjo823 && slider_2mmjo823.unit ? slider_2mmjo823Function(slider_2mmjo823.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2mmjo823: value }),
            }), 
), React.createElement(PanelBody, { title: __("Dots"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Dots Width`),
            separator:'default',
            value: slider_humxqr24,
            
            min: slider_humxqr24 && slider_humxqr24.unit ? slider_humxqr24Function(slider_humxqr24.unit, 'min') : 0,
            max: slider_humxqr24 && slider_humxqr24.unit ? slider_humxqr24Function(slider_humxqr24.unit, 'max') : 100,
            step: slider_humxqr24 && slider_humxqr24.unit ? slider_humxqr24Function(slider_humxqr24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_humxqr24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Dots Height`),
            separator:'default',
            value: slider_44n3jp24,
            
            min: slider_44n3jp24 && slider_44n3jp24.unit ? slider_44n3jp24Function(slider_44n3jp24.unit, 'min') : 0,
            max: slider_44n3jp24 && slider_44n3jp24.unit ? slider_44n3jp24Function(slider_44n3jp24.unit, 'max') : 100,
            step: slider_44n3jp24 && slider_44n3jp24.unit ? slider_44n3jp24Function(slider_44n3jp24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_44n3jp24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Width`),
            separator:'before',
            value: slider_cl6adz24,
            
            min: slider_cl6adz24 && slider_cl6adz24.unit ? slider_cl6adz24Function(slider_cl6adz24.unit, 'min') : 0,
            max: slider_cl6adz24 && slider_cl6adz24.unit ? slider_cl6adz24Function(slider_cl6adz24.unit, 'max') : 100,
            step: slider_cl6adz24 && slider_cl6adz24.unit ? slider_cl6adz24Function(slider_cl6adz24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_cl6adz24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Height`),
            separator:'default',
            value: slider_ngxjcy24,
            
            min: slider_ngxjcy24 && slider_ngxjcy24.unit ? slider_ngxjcy24Function(slider_ngxjcy24.unit, 'min') : 0,
            max: slider_ngxjcy24 && slider_ngxjcy24.unit ? slider_ngxjcy24Function(slider_ngxjcy24.unit, 'max') : 100,
            step: slider_ngxjcy24 && slider_ngxjcy24.unit ? slider_ngxjcy24Function(slider_ngxjcy24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ngxjcy24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Top offset`),
            separator:'before',
            value: slider_5l0r9k24,
            
            min: slider_5l0r9k24 && slider_5l0r9k24.unit ? slider_5l0r9k24Function(slider_5l0r9k24.unit, 'min') : 0,
            max: slider_5l0r9k24 && slider_5l0r9k24.unit ? slider_5l0r9k24Function(slider_5l0r9k24.unit, 'max') : 100,
            step: slider_5l0r9k24 && slider_5l0r9k24.unit ? slider_5l0r9k24Function(slider_5l0r9k24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_5l0r9k24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Left offset`),
            separator:'default',
            value: slider_hiw30v24,
            
            min: slider_hiw30v24 && slider_hiw30v24.unit ? slider_hiw30v24Function(slider_hiw30v24.unit, 'min') : 0,
            max: slider_hiw30v24 && slider_hiw30v24.unit ? slider_hiw30v24Function(slider_hiw30v24.unit, 'max') : 100,
            step: slider_hiw30v24 && slider_hiw30v24.unit ? slider_hiw30v24Function(slider_hiw30v24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_hiw30v24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_80ntvk23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_1q7id323,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1q7id323: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Inner Dots`),
            value: heading_z2n3ui23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_av4gsa23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_av4gsa23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_d0oggu23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_xqa3oq23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_xqa3oq23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Inner Dots`),
            value: heading_huci9g23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_tag0qi23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_tag0qi23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_sle38i25,
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
If a property is already applied to the main card, then the property applied here will not take effect.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_sle38i25: value }),
            }), 
),( select_nhr9qd23 == "style-2" ) && React.createElement(PanelBody, { title: __("Number"), initialOpen: false },
( select_nhr9qd23 == "style-2" ) && React.createElement(Pmgc_Range, {
            label: __(`Size`),
            separator:'default',
            value: slider_j8anjs23,
            
            min: slider_j8anjs23 && slider_j8anjs23.unit ? slider_j8anjs23Function(slider_j8anjs23.unit, 'min') : 0,
            max: slider_j8anjs23 && slider_j8anjs23.unit ? slider_j8anjs23Function(slider_j8anjs23.unit, 'max') : 100,
            step: slider_j8anjs23 && slider_j8anjs23.unit ? slider_j8anjs23Function(slider_j8anjs23.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_j8anjs23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_24zrck25,
            
            min: slider_24zrck25 && slider_24zrck25.unit ? slider_24zrck25Function(slider_24zrck25.unit, 'min') : 0,
            max: slider_24zrck25 && slider_24zrck25.unit ? slider_24zrck25Function(slider_24zrck25.unit, 'max') : 100,
            step: slider_24zrck25 && slider_24zrck25.unit ? slider_24zrck25Function(slider_24zrck25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_24zrck25: value }),
            }), 
( select_nhr9qd23 == "style-2" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_p0839w23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_p0839w23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_0fsle923,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0fsle923: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_qgd3pi23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qgd3pi23: value }),
            }), 
), 
), 
),( select_nhr9qd23 == "style-3" ) && React.createElement(PanelBody, { title: __("Checked Icon"), initialOpen: false },
( select_nhr9qd23 == "style-3" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size `),
            separator:'default',
            value: slider_zasg0323,
            
            min: slider_zasg0323 && slider_zasg0323.unit ? slider_zasg0323Function(slider_zasg0323.unit, 'min') : 0,
            max: slider_zasg0323 && slider_zasg0323.unit ? slider_zasg0323Function(slider_zasg0323.unit, 'max') : 100,
            step: slider_zasg0323 && slider_zasg0323.unit ? slider_zasg0323Function(slider_zasg0323.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_zasg0323: value }),
            }), 
( select_nhr9qd23 == "style-3" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_yn7qqw23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_yn7qqw23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_vkzjwf23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_vkzjwf23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Progress Bar"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Progress Bar Height`),
            separator:'default',
            value: slider_jy7s4h24,
            
            min: slider_jy7s4h24 && slider_jy7s4h24.unit ? slider_jy7s4h24Function(slider_jy7s4h24.unit, 'min') : 0,
            max: slider_jy7s4h24 && slider_jy7s4h24.unit ? slider_jy7s4h24Function(slider_jy7s4h24.unit, 'max') : 100,
            step: slider_jy7s4h24 && slider_jy7s4h24.unit ? slider_jy7s4h24Function(slider_jy7s4h24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_jy7s4h24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_3vcprb23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_3vcprb23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_7hl08c25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7hl08c25: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_mpweuw25,
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
If a property is already applied to the main card, then the property applied here will not take effect.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_mpweuw25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Box Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_h90zss23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_h90zss23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_z0q74o23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_z0q74o23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_z81eny23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_z81eny23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_asyv9l23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_asyv9l23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_ql5iwo23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ql5iwo23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_waz3yx23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_waz3yx23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_tna86r23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_tna86r23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_idqltp23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_idqltp23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_8vghll23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_8vghll23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-7kq3uw24", block_id, false, props.clientId);
                }
            }

            
let g_select_nhr9qd23 = select_nhr9qd23 && select_nhr9qd23 != undefined  ? select_nhr9qd23 : "";
let g_rawhtml_tndgki25 = rawhtml_tndgki25 && rawhtml_tndgki25 != undefined && ( (select_nhr9qd23 == "style-3") ) ? rawhtml_tndgki25 : "";
let g_rawhtml_bxa27a25 = rawhtml_bxa27a25 && rawhtml_bxa27a25 != undefined  ? rawhtml_bxa27a25 : "";
let g_rawhtml_s3p36o25 = rawhtml_s3p36o25 && rawhtml_s3p36o25 != undefined  ? rawhtml_s3p36o25 : "";
let g_dimension_5chiij23 = dimension_5chiij23 && dimension_5chiij23 != undefined  ? dimension_5chiij23 : "";
let g_dimension_ev1anw23 = dimension_ev1anw23 && dimension_ev1anw23 != undefined  ? dimension_ev1anw23 : "";
let g_typography_o0oh3t23 = typography_o0oh3t23 && typography_o0oh3t23 != undefined  ? typography_o0oh3t23 : "";
let g_color_9y0up723 = color_9y0up723 && color_9y0up723 != undefined  ? color_9y0up723 : "";
let g_color_chjm5523 = color_chjm5523 && color_chjm5523 != undefined  ? color_chjm5523 : "";
let g_color_ulpqga23 = color_ulpqga23 && color_ulpqga23 != undefined  ? color_ulpqga23 : "";
let g_rawhtml_bi3ap525 = rawhtml_bi3ap525 && rawhtml_bi3ap525 != undefined  ? rawhtml_bi3ap525 : "";
let g_dimension_vtvtbr23 = dimension_vtvtbr23 && dimension_vtvtbr23 != undefined  ? dimension_vtvtbr23 : "";
let g_dimension_51azh123 = dimension_51azh123 && dimension_51azh123 != undefined  ? dimension_51azh123 : "";
let g_typography_s8eoip23 = typography_s8eoip23 && typography_s8eoip23 != undefined  ? typography_s8eoip23 : "";
let g_color_1eyj9i23 = color_1eyj9i23 && color_1eyj9i23 != undefined  ? color_1eyj9i23 : "";
let g_color_ffmmbb23 = color_ffmmbb23 && color_ffmmbb23 != undefined  ? color_ffmmbb23 : "";
let g_color_67p52023 = color_67p52023 && color_67p52023 != undefined  ? color_67p52023 : "";
let g_rawhtml_fpq3le25 = rawhtml_fpq3le25 && rawhtml_fpq3le25 != undefined  ? rawhtml_fpq3le25 : "";
let g_color_l9hrrk23 = color_l9hrrk23 && color_l9hrrk23 != undefined  ? color_l9hrrk23 : "";
let g_color_wwnfak23 = color_wwnfak23 && color_wwnfak23 != undefined  ? color_wwnfak23 : "";
let g_color_4mfaaw23 = color_4mfaaw23 && color_4mfaaw23 != undefined  ? color_4mfaaw23 : "";
let g_rawhtml_9wq12425 = rawhtml_9wq12425 && rawhtml_9wq12425 != undefined  ? rawhtml_9wq12425 : "";
let g_heading_80ntvk23 = heading_80ntvk23 && heading_80ntvk23 != undefined  ? heading_80ntvk23 : "";
let g_color_1q7id323 = color_1q7id323 && color_1q7id323 != undefined  ? color_1q7id323 : "";
let g_heading_z2n3ui23 = heading_z2n3ui23 && heading_z2n3ui23 != undefined  ? heading_z2n3ui23 : "";
let g_color_av4gsa23 = color_av4gsa23 && color_av4gsa23 != undefined  ? color_av4gsa23 : "";
let g_heading_tx6tse23 = heading_tx6tse23 && heading_tx6tse23 != undefined  ? heading_tx6tse23 : "";
let g_heading_dspala23 = heading_dspala23 && heading_dspala23 != undefined  ? heading_dspala23 : "";
let g_heading_flidce23 = heading_flidce23 && heading_flidce23 != undefined  ? heading_flidce23 : "";
let g_heading_d0oggu23 = heading_d0oggu23 && heading_d0oggu23 != undefined  ? heading_d0oggu23 : "";
let g_color_xqa3oq23 = color_xqa3oq23 && color_xqa3oq23 != undefined  ? color_xqa3oq23 : "";
let g_heading_huci9g23 = heading_huci9g23 && heading_huci9g23 != undefined  ? heading_huci9g23 : "";
let g_color_tag0qi23 = color_tag0qi23 && color_tag0qi23 != undefined  ? color_tag0qi23 : "";
let g_rawhtml_sle38i25 = rawhtml_sle38i25 && rawhtml_sle38i25 != undefined  ? rawhtml_sle38i25 : "";
let g_color_p0839w23 = color_p0839w23 && color_p0839w23 != undefined  ? color_p0839w23 : "";
let g_color_0fsle923 = color_0fsle923 && color_0fsle923 != undefined  ? color_0fsle923 : "";
let g_color_qgd3pi23 = color_qgd3pi23 && color_qgd3pi23 != undefined  ? color_qgd3pi23 : "";
let g_color_yn7qqw23 = color_yn7qqw23 && color_yn7qqw23 != undefined  ? color_yn7qqw23 : "";
let g_color_vkzjwf23 = color_vkzjwf23 && color_vkzjwf23 != undefined  ? color_vkzjwf23 : "";
let g_color_3vcprb23 = color_3vcprb23 && color_3vcprb23 != undefined  ? color_3vcprb23 : "";
let g_color_n94v6o23 = color_n94v6o23 && color_n94v6o23 != undefined  ? color_n94v6o23 : "";
let g_color_7hl08c25 = color_7hl08c25 && color_7hl08c25 != undefined  ? color_7hl08c25 : "";
let g_rawhtml_mpweuw25 = rawhtml_mpweuw25 && rawhtml_mpweuw25 != undefined  ? rawhtml_mpweuw25 : "";
let g_dimension_h90zss23 = dimension_h90zss23 && dimension_h90zss23 != undefined  ? dimension_h90zss23 : "";
let g_dimension_z0q74o23 = dimension_z0q74o23 && dimension_z0q74o23 != undefined  ? dimension_z0q74o23 : "";
let g_background_z81eny23 = background_z81eny23 && background_z81eny23 != undefined  ? background_z81eny23 : "";
let g_border_asyv9l23 = border_asyv9l23 && border_asyv9l23 != undefined  ? border_asyv9l23 : "";
let g_dimension_ql5iwo23 = dimension_ql5iwo23 && dimension_ql5iwo23 != undefined  ? dimension_ql5iwo23 : "";
let g_boxshadow_waz3yx23 = boxshadow_waz3yx23 && boxshadow_waz3yx23 != undefined  ? boxshadow_waz3yx23 : "";
let g_background_tna86r23 = background_tna86r23 && background_tna86r23 != undefined  ? background_tna86r23 : "";
let g_border_idqltp23 = border_idqltp23 && border_idqltp23 != undefined  ? border_idqltp23 : "";
let g_boxshadow_8vghll23 = boxshadow_8vghll23 && boxshadow_8vghll23 != undefined  ? boxshadow_8vghll23 : "";
            
let repeater_8rn0w223_q825 = "";
                            
repeater_8rn0w223  && repeater_8rn0w223.map((r_item, index) => {
                                
let grnp_select_ej7gme23 = r_item.select_ej7gme23  ? r_item.select_ej7gme23 : "";
let grnp_text_vx4ivy23 = r_item.text_vx4ivy23 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.text_vx4ivy23 : "";
let grnp_iconscontrol_e0tko023 = r_item?.iconscontrol_e0tko023 != undefined && ( (r_item?.select_ej7gme23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_e0tko023+'"></i></span>' : '';

let grnp_media_8061n023 = r_item?.media_8061n023?.url != undefined && ( (r_item?.select_ej7gme23 == "image") ) ? r_item?.media_8061n023.url : "";
let grnp_wysiwyg_4wbmqg23 = r_item.wysiwyg_4wbmqg23  ? r_item.wysiwyg_4wbmqg23 : "";
let grnp_switcher_lryedf23 = r_item.switcher_lryedf23  ? 'yes' : "";
let grnp_heading_ur4f3t23 = r_item.heading_ur4f3t23  ? r_item.heading_ur4f3t23 : "";
let grnp_color_2ynyfv23 = r_item.color_2ynyfv23 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.color_2ynyfv23 : "";
let grnp_color_lrsetc23 = r_item.color_lrsetc23 && ( (r_item?.select_ej7gme23 == "icon") ) ? r_item.color_lrsetc23 : "";
let grnp_color_xp4j5c23 = r_item.color_xp4j5c23  ? r_item.color_xp4j5c23 : "";
let grnp_heading_iypfma23 = r_item.heading_iypfma23  ? r_item.heading_iypfma23 : "";
let grnp_color_jhnu2423 = r_item.color_jhnu2423 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.color_jhnu2423 : "";
let grnp_color_c38r5j23 = r_item.color_c38r5j23 && ( (r_item?.select_ej7gme23 == "icon") ) ? r_item.color_c38r5j23 : "";
let grnp_color_a1phqu23 = r_item.color_a1phqu23  ? r_item.color_a1phqu23 : "";
let grnp_color_iyhbs023 = r_item.color_iyhbs023 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_iyhbs023 : "";
let grnp_color_sxy5ra25 = r_item.color_sxy5ra25 && ( r_item?.switcher_lryedf23&&(r_item?.select_ej7gme23 == "icon") ) ? r_item.color_sxy5ra25 : "";
let grnp_color_r50bao23 = r_item.color_r50bao23 && ( r_item?.switcher_lryedf23 ) ? r_item.color_r50bao23 : "";
let grnp_color_0hfwjr23 = r_item.color_0hfwjr23 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_0hfwjr23 : "";
let grnp_color_pobie923 = r_item.color_pobie923 && ( r_item?.switcher_lryedf23 ) ? r_item.color_pobie923 : "";
let grnp_color_fzwbk523 = r_item.color_fzwbk523 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_fzwbk523 : "";
let grnp_color_jl0z0m23 = r_item.color_jl0z0m23 && ( r_item?.switcher_lryedf23 ) ? r_item.color_jl0z0m23 : "";
                                repeater_8rn0w223_q825 += `<div class="tp-repeater-item-${r_item._key} wkit-vl-step-content wkit-vl-${grnp_select_ej7gme23} wkit-vl-pix-levels-step wkit-vl-active-${grnp_switcher_lryedf23} col-xs-12 col-md-3" data-repeater_8rn0w223="{repeater_8rn0w223}">
                <h6 class="wkit-vl-step-title text-center" data-ttl="${grnp_text_vx4ivy23}">${grnp_text_vx4ivy23}</h6>
                <span class="wkit-vl-progress-icon">${grnp_iconscontrol_e0tko023 }</span>
                <div class="wkit-vl-progress-img">
                     <img src="${grnp_media_8061n023}" class="tp-vl-title-image">
                </div>
                <div class="wkit-progress-area text-center">
                    <div class="progress wkit-bg-gray-2">
                        <div class="wkit-prg-one progress-bar wkit-vl-bg-gradient-primary">
                        </div>
                    </div>
                    <div class="wkit-pix-leveles-dot-div">
                        <span class="wkit-prg-two wkit-vl-pix-levels-dot wkit-vl-bg-gradient-primary" data-index="${index}">
                            <span class="wkit-vl-pix-levels-dot-inner wkit-vl-bg-dark-opacity-3"></span>
                        </span>
                    </div>
                </div>
                <span class="wkit-vl-step-desc text-center" data-desc="${grnp_wysiwyg_4wbmqg23}">${grnp_wysiwyg_4wbmqg23}</span>
            </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7kq3uw24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-multi-step-vl-wrapper text-center">
    <div class="wkit-vl-card wkit-vl-inner-${g_select_nhr9qd23}">
        <div class="wkit-vl-content-bg row">
            ${repeater_8rn0w223_q825}
        </div>
    </div>
</div>    `
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
            select_nhr9qd23,
rawhtml_tndgki25,
repeater_8rn0w223,
rawhtml_bxa27a25,
slider_b5wt1p24,
slider_spv9a524,
normalhover_qscp2824,
rawhtml_s3p36o25,
dimension_5chiij23,
dimension_ev1anw23,
typography_o0oh3t23,
color_9y0up723,
color_chjm5523,
color_ulpqga23,
normalhover_82bgd023,
rawhtml_bi3ap525,
dimension_vtvtbr23,
dimension_51azh123,
typography_s8eoip23,
color_1eyj9i23,
color_ffmmbb23,
color_67p52023,
normalhover_nul8mm23,
rawhtml_fpq3le25,
slider_4f9ksm23,
color_l9hrrk23,
color_wwnfak23,
color_4mfaaw23,
normalhover_8rtkwq23,
rawhtml_9wq12425,
slider_17rycx23,
slider_tdgput25,
slider_bsl6pe23,
slider_2mmjo823,
slider_humxqr24,
slider_44n3jp24,
slider_cl6adz24,
slider_ngxjcy24,
slider_5l0r9k24,
slider_hiw30v24,
heading_80ntvk23,
color_1q7id323,
heading_z2n3ui23,
color_av4gsa23,
heading_tx6tse23,
heading_dspala23,
heading_flidce23,
heading_d0oggu23,
color_xqa3oq23,
heading_huci9g23,
color_tag0qi23,
normalhover_sjel8l23,
rawhtml_sle38i25,
slider_j8anjs23,
slider_24zrck25,
color_p0839w23,
color_0fsle923,
color_qgd3pi23,
normalhover_hi79cb23,
slider_zasg0323,
color_yn7qqw23,
color_vkzjwf23,
normalhover_810qo123,
slider_jy7s4h24,
color_3vcprb23,
color_n94v6o23,
color_7hl08c25,
normalhover_zeajyo25,
rawhtml_mpweuw25,
dimension_h90zss23,
dimension_z0q74o23,
background_z81eny23,
border_asyv9l23,
dimension_ql5iwo23,
boxshadow_waz3yx23,
background_tna86r23,
border_idqltp23,
boxshadow_8vghll23,
normalhover_cgv0nk23,

            block_id,
        } = attributes;

        

        

        

        
let g_select_nhr9qd23 = select_nhr9qd23 && select_nhr9qd23 != undefined  ? select_nhr9qd23 : "";
let g_rawhtml_tndgki25 = rawhtml_tndgki25 && rawhtml_tndgki25 != undefined && ( (select_nhr9qd23 == "style-3") ) ? rawhtml_tndgki25 : "";
let g_rawhtml_bxa27a25 = rawhtml_bxa27a25 && rawhtml_bxa27a25 != undefined  ? rawhtml_bxa27a25 : "";
let g_rawhtml_s3p36o25 = rawhtml_s3p36o25 && rawhtml_s3p36o25 != undefined  ? rawhtml_s3p36o25 : "";
let g_dimension_5chiij23 = dimension_5chiij23 && dimension_5chiij23 != undefined  ? dimension_5chiij23 : "";
let g_dimension_ev1anw23 = dimension_ev1anw23 && dimension_ev1anw23 != undefined  ? dimension_ev1anw23 : "";
let g_typography_o0oh3t23 = typography_o0oh3t23 && typography_o0oh3t23 != undefined  ? typography_o0oh3t23 : "";
let g_color_9y0up723 = color_9y0up723 && color_9y0up723 != undefined  ? color_9y0up723 : "";
let g_color_chjm5523 = color_chjm5523 && color_chjm5523 != undefined  ? color_chjm5523 : "";
let g_color_ulpqga23 = color_ulpqga23 && color_ulpqga23 != undefined  ? color_ulpqga23 : "";
let g_rawhtml_bi3ap525 = rawhtml_bi3ap525 && rawhtml_bi3ap525 != undefined  ? rawhtml_bi3ap525 : "";
let g_dimension_vtvtbr23 = dimension_vtvtbr23 && dimension_vtvtbr23 != undefined  ? dimension_vtvtbr23 : "";
let g_dimension_51azh123 = dimension_51azh123 && dimension_51azh123 != undefined  ? dimension_51azh123 : "";
let g_typography_s8eoip23 = typography_s8eoip23 && typography_s8eoip23 != undefined  ? typography_s8eoip23 : "";
let g_color_1eyj9i23 = color_1eyj9i23 && color_1eyj9i23 != undefined  ? color_1eyj9i23 : "";
let g_color_ffmmbb23 = color_ffmmbb23 && color_ffmmbb23 != undefined  ? color_ffmmbb23 : "";
let g_color_67p52023 = color_67p52023 && color_67p52023 != undefined  ? color_67p52023 : "";
let g_rawhtml_fpq3le25 = rawhtml_fpq3le25 && rawhtml_fpq3le25 != undefined  ? rawhtml_fpq3le25 : "";
let g_color_l9hrrk23 = color_l9hrrk23 && color_l9hrrk23 != undefined  ? color_l9hrrk23 : "";
let g_color_wwnfak23 = color_wwnfak23 && color_wwnfak23 != undefined  ? color_wwnfak23 : "";
let g_color_4mfaaw23 = color_4mfaaw23 && color_4mfaaw23 != undefined  ? color_4mfaaw23 : "";
let g_rawhtml_9wq12425 = rawhtml_9wq12425 && rawhtml_9wq12425 != undefined  ? rawhtml_9wq12425 : "";
let g_heading_80ntvk23 = heading_80ntvk23 && heading_80ntvk23 != undefined  ? heading_80ntvk23 : "";
let g_color_1q7id323 = color_1q7id323 && color_1q7id323 != undefined  ? color_1q7id323 : "";
let g_heading_z2n3ui23 = heading_z2n3ui23 && heading_z2n3ui23 != undefined  ? heading_z2n3ui23 : "";
let g_color_av4gsa23 = color_av4gsa23 && color_av4gsa23 != undefined  ? color_av4gsa23 : "";
let g_heading_tx6tse23 = heading_tx6tse23 && heading_tx6tse23 != undefined  ? heading_tx6tse23 : "";
let g_heading_dspala23 = heading_dspala23 && heading_dspala23 != undefined  ? heading_dspala23 : "";
let g_heading_flidce23 = heading_flidce23 && heading_flidce23 != undefined  ? heading_flidce23 : "";
let g_heading_d0oggu23 = heading_d0oggu23 && heading_d0oggu23 != undefined  ? heading_d0oggu23 : "";
let g_color_xqa3oq23 = color_xqa3oq23 && color_xqa3oq23 != undefined  ? color_xqa3oq23 : "";
let g_heading_huci9g23 = heading_huci9g23 && heading_huci9g23 != undefined  ? heading_huci9g23 : "";
let g_color_tag0qi23 = color_tag0qi23 && color_tag0qi23 != undefined  ? color_tag0qi23 : "";
let g_rawhtml_sle38i25 = rawhtml_sle38i25 && rawhtml_sle38i25 != undefined  ? rawhtml_sle38i25 : "";
let g_color_p0839w23 = color_p0839w23 && color_p0839w23 != undefined  ? color_p0839w23 : "";
let g_color_0fsle923 = color_0fsle923 && color_0fsle923 != undefined  ? color_0fsle923 : "";
let g_color_qgd3pi23 = color_qgd3pi23 && color_qgd3pi23 != undefined  ? color_qgd3pi23 : "";
let g_color_yn7qqw23 = color_yn7qqw23 && color_yn7qqw23 != undefined  ? color_yn7qqw23 : "";
let g_color_vkzjwf23 = color_vkzjwf23 && color_vkzjwf23 != undefined  ? color_vkzjwf23 : "";
let g_color_3vcprb23 = color_3vcprb23 && color_3vcprb23 != undefined  ? color_3vcprb23 : "";
let g_color_n94v6o23 = color_n94v6o23 && color_n94v6o23 != undefined  ? color_n94v6o23 : "";
let g_color_7hl08c25 = color_7hl08c25 && color_7hl08c25 != undefined  ? color_7hl08c25 : "";
let g_rawhtml_mpweuw25 = rawhtml_mpweuw25 && rawhtml_mpweuw25 != undefined  ? rawhtml_mpweuw25 : "";
let g_dimension_h90zss23 = dimension_h90zss23 && dimension_h90zss23 != undefined  ? dimension_h90zss23 : "";
let g_dimension_z0q74o23 = dimension_z0q74o23 && dimension_z0q74o23 != undefined  ? dimension_z0q74o23 : "";
let g_background_z81eny23 = background_z81eny23 && background_z81eny23 != undefined  ? background_z81eny23 : "";
let g_border_asyv9l23 = border_asyv9l23 && border_asyv9l23 != undefined  ? border_asyv9l23 : "";
let g_dimension_ql5iwo23 = dimension_ql5iwo23 && dimension_ql5iwo23 != undefined  ? dimension_ql5iwo23 : "";
let g_boxshadow_waz3yx23 = boxshadow_waz3yx23 && boxshadow_waz3yx23 != undefined  ? boxshadow_waz3yx23 : "";
let g_background_tna86r23 = background_tna86r23 && background_tna86r23 != undefined  ? background_tna86r23 : "";
let g_border_idqltp23 = border_idqltp23 && border_idqltp23 != undefined  ? border_idqltp23 : "";
let g_boxshadow_8vghll23 = boxshadow_8vghll23 && boxshadow_8vghll23 != undefined  ? boxshadow_8vghll23 : "";
        
let repeater_8rn0w223_q825 = "";
                            
repeater_8rn0w223  && repeater_8rn0w223.map((r_item, index) => {
                                
let grnp_select_ej7gme23 = r_item.select_ej7gme23  ? r_item.select_ej7gme23 : "";
let grnp_text_vx4ivy23 = r_item.text_vx4ivy23 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.text_vx4ivy23 : "";
let grnp_iconscontrol_e0tko023 = r_item?.iconscontrol_e0tko023 != undefined && ( (r_item?.select_ej7gme23 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_e0tko023+'"></i></span>' : '';

let grnp_media_8061n023 = r_item?.media_8061n023?.url != undefined && ( (r_item?.select_ej7gme23 == "image") ) ? r_item?.media_8061n023.url : "";
let grnp_wysiwyg_4wbmqg23 = r_item.wysiwyg_4wbmqg23  ? r_item.wysiwyg_4wbmqg23 : "";
let grnp_switcher_lryedf23 = r_item.switcher_lryedf23  ? 'yes' : "";
let grnp_heading_ur4f3t23 = r_item.heading_ur4f3t23  ? r_item.heading_ur4f3t23 : "";
let grnp_color_2ynyfv23 = r_item.color_2ynyfv23 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.color_2ynyfv23 : "";
let grnp_color_lrsetc23 = r_item.color_lrsetc23 && ( (r_item?.select_ej7gme23 == "icon") ) ? r_item.color_lrsetc23 : "";
let grnp_color_xp4j5c23 = r_item.color_xp4j5c23  ? r_item.color_xp4j5c23 : "";
let grnp_heading_iypfma23 = r_item.heading_iypfma23  ? r_item.heading_iypfma23 : "";
let grnp_color_jhnu2423 = r_item.color_jhnu2423 && ( (r_item?.select_ej7gme23 == "text") ) ? r_item.color_jhnu2423 : "";
let grnp_color_c38r5j23 = r_item.color_c38r5j23 && ( (r_item?.select_ej7gme23 == "icon") ) ? r_item.color_c38r5j23 : "";
let grnp_color_a1phqu23 = r_item.color_a1phqu23  ? r_item.color_a1phqu23 : "";
let grnp_color_iyhbs023 = r_item.color_iyhbs023 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_iyhbs023 : "";
let grnp_color_sxy5ra25 = r_item.color_sxy5ra25 && ( r_item?.switcher_lryedf23&&(r_item?.select_ej7gme23 == "icon") ) ? r_item.color_sxy5ra25 : "";
let grnp_color_r50bao23 = r_item.color_r50bao23 && ( r_item?.switcher_lryedf23 ) ? r_item.color_r50bao23 : "";
let grnp_color_0hfwjr23 = r_item.color_0hfwjr23 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_0hfwjr23 : "";
let grnp_color_pobie923 = r_item.color_pobie923 && ( r_item?.switcher_lryedf23 ) ? r_item.color_pobie923 : "";
let grnp_color_fzwbk523 = r_item.color_fzwbk523 && ( !r_item?.switcher_lryedf23 ) ? r_item.color_fzwbk523 : "";
let grnp_color_jl0z0m23 = r_item.color_jl0z0m23 && ( r_item?.switcher_lryedf23 ) ? r_item.color_jl0z0m23 : "";
                                repeater_8rn0w223_q825 += `<div class="tp-repeater-item-${r_item._key} wkit-vl-step-content wkit-vl-${grnp_select_ej7gme23} wkit-vl-pix-levels-step wkit-vl-active-${grnp_switcher_lryedf23} col-xs-12 col-md-3" data-repeater_8rn0w223="{repeater_8rn0w223}">
                <h6 class="wkit-vl-step-title text-center" data-ttl="${grnp_text_vx4ivy23}">${grnp_text_vx4ivy23}</h6>
                <span class="wkit-vl-progress-icon">${grnp_iconscontrol_e0tko023 }</span>
                <div class="wkit-vl-progress-img">
                     <img src="${grnp_media_8061n023}" class="tp-vl-title-image">
                </div>
                <div class="wkit-progress-area text-center">
                    <div class="progress wkit-bg-gray-2">
                        <div class="wkit-prg-one progress-bar wkit-vl-bg-gradient-primary">
                        </div>
                    </div>
                    <div class="wkit-pix-leveles-dot-div">
                        <span class="wkit-prg-two wkit-vl-pix-levels-dot wkit-vl-bg-gradient-primary" data-index="${index}">
                            <span class="wkit-vl-pix-levels-dot-inner wkit-vl-bg-dark-opacity-3"></span>
                        </span>
                    </div>
                </div>
                <span class="wkit-vl-step-desc text-center" data-desc="${grnp_wysiwyg_4wbmqg23}">${grnp_wysiwyg_4wbmqg23}</span>
            </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-7kq3uw24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_7kq3uw24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-multi-step-vl-wrapper text-center">
    <div class="wkit-vl-card wkit-vl-inner-${g_select_nhr9qd23}">
        <div class="wkit-vl-content-bg row">
            ${repeater_8rn0w223_q825}
        </div>
    </div>
</div>    `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Vertical_Level_7kq3uw24();