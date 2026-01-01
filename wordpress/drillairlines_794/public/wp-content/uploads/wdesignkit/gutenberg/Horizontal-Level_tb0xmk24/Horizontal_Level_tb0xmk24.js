
    class Horizontal_Level_tb0xmk24 {
        constructor() {
            this.Horizontal_Level_tb0xmk24_mdfy0925();
        }
    
        Horizontal_Level_tb0xmk24_mdfy0925() {
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
   
    registerBlockType('wdkit/wb-tb0xmk24', {
        title: __('Horizontal Level'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-ellipsis-h tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Procedural Information'),__('Step by Step Guide'),__('Visual Communication'),__('User Guidance'),__('Website Processes'),__('Clarity in Presentation'),__('Process steps'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_l41s3h24Function = (unit, type) => {
                var g_slider_l41s3h24_list = [];
                g_slider_l41s3h24_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_l41s3h24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_l41s3h24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_l41s3h24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_l41s3h24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_l41s3h24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_l41s3h24_list[unit][type];
            };
const slider_x9qrcx24Function = (unit, type) => {
                var g_slider_x9qrcx24_list = [];
                g_slider_x9qrcx24_list['px'] = { "type": "px", "min": 0, "max": 10, "step": 1 };
g_slider_x9qrcx24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_x9qrcx24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_x9qrcx24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_x9qrcx24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_x9qrcx24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_x9qrcx24_list[unit][type];
            };
const slider_k0pgbq25Function = (unit, type) => {
                var g_slider_k0pgbq25_list = [];
                g_slider_k0pgbq25_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_k0pgbq25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_k0pgbq25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_k0pgbq25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_k0pgbq25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_k0pgbq25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_k0pgbq25_list[unit][type];
            };
const slider_szqvmo25Function = (unit, type) => {
                var g_slider_szqvmo25_list = [];
                g_slider_szqvmo25_list['px'] = { "type": "px", "min": 0, "max": 200, "step": 1 };
g_slider_szqvmo25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_szqvmo25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_szqvmo25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_szqvmo25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_szqvmo25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_szqvmo25_list[unit][type];
            };
const slider_ijas5623Function = (unit, type) => {
                var g_slider_ijas5623_list = [];
                g_slider_ijas5623_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ijas5623_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ijas5623_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ijas5623_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ijas5623_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ijas5623_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ijas5623_list[unit][type];
            };
const slider_2lvnux25Function = (unit, type) => {
                var g_slider_2lvnux25_list = [];
                g_slider_2lvnux25_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_2lvnux25_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_2lvnux25_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2lvnux25_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2lvnux25_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2lvnux25_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2lvnux25_list[unit][type];
            };
const slider_ig6lr025Function = (unit, type) => {
                var g_slider_ig6lr025_list = [];
                g_slider_ig6lr025_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ig6lr025_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ig6lr025_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ig6lr025_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ig6lr025_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ig6lr025_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ig6lr025_list[unit][type];
            };
const slider_12dfcq23Function = (unit, type) => {
                var g_slider_12dfcq23_list = [];
                g_slider_12dfcq23_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_12dfcq23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_12dfcq23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_12dfcq23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_12dfcq23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_12dfcq23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_12dfcq23_list[unit][type];
            };
const slider_qmpnwd23Function = (unit, type) => {
                var g_slider_qmpnwd23_list = [];
                g_slider_qmpnwd23_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_qmpnwd23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_qmpnwd23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_qmpnwd23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_qmpnwd23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_qmpnwd23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_qmpnwd23_list[unit][type];
            };
const slider_55eqx325Function = (unit, type) => {
                var g_slider_55eqx325_list = [];
                g_slider_55eqx325_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_55eqx325_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_55eqx325_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_55eqx325_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_55eqx325_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_55eqx325_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_55eqx325_list[unit][type];
            };
const slider_ja6oq825Function = (unit, type) => {
                var g_slider_ja6oq825_list = [];
                g_slider_ja6oq825_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_ja6oq825_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_ja6oq825_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_ja6oq825_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_ja6oq825_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_ja6oq825_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_ja6oq825_list[unit][type];
            };
const slider_msm9fl24Function = (unit, type) => {
                var g_slider_msm9fl24_list = [];
                g_slider_msm9fl24_list['px'] = { "type": "px", "min": 0, "max": 58, "step": 1 };
g_slider_msm9fl24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_msm9fl24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_msm9fl24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_msm9fl24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_msm9fl24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_msm9fl24_list[unit][type];
            };
const slider_tto3tx24Function = (unit, type) => {
                var g_slider_tto3tx24_list = [];
                g_slider_tto3tx24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_tto3tx24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_tto3tx24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_tto3tx24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_tto3tx24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_tto3tx24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_tto3tx24_list[unit][type];
            };
const slider_iunpeg24Function = (unit, type) => {
                var g_slider_iunpeg24_list = [];
                g_slider_iunpeg24_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_iunpeg24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_iunpeg24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_iunpeg24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_iunpeg24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_iunpeg24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_iunpeg24_list[unit][type];
            };
const slider_3gaoy324Function = (unit, type) => {
                var g_slider_3gaoy324_list = [];
                g_slider_3gaoy324_list['px'] = { "type": "px", "min": 0, "max": 60, "step": 1 };
g_slider_3gaoy324_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_3gaoy324_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_3gaoy324_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_3gaoy324_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_3gaoy324_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_3gaoy324_list[unit][type];
            };
const slider_2mh27d24Function = (unit, type) => {
                var g_slider_2mh27d24_list = [];
                g_slider_2mh27d24_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_2mh27d24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_2mh27d24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_2mh27d24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_2mh27d24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_2mh27d24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_2mh27d24_list[unit][type];
            };
const slider_00pqga24Function = (unit, type) => {
                var g_slider_00pqga24_list = [];
                g_slider_00pqga24_list['px'] = { "type": "px", "min": 0, "max": 50, "step": 1 };
g_slider_00pqga24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_00pqga24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_00pqga24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_00pqga24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_00pqga24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_00pqga24_list[unit][type];
            };
const slider_n1w3iv23Function = (unit, type) => {
                var g_slider_n1w3iv23_list = [];
                g_slider_n1w3iv23_list['px'] = { "type": "px", "min": 0, "max": 40, "step": 1 };
g_slider_n1w3iv23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_n1w3iv23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_n1w3iv23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_n1w3iv23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_n1w3iv23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_n1w3iv23_list[unit][type];
            };
const slider_y7vfau23Function = (unit, type) => {
                var g_slider_y7vfau23_list = [];
                g_slider_y7vfau23_list['px'] = { "type": "px", "min": 0, "max": 20, "step": 1 };
g_slider_y7vfau23_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_y7vfau23_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_y7vfau23_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_y7vfau23_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_y7vfau23_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_y7vfau23_list[unit][type];
            };
const slider_k1755025Function = (unit, type) => {
                var g_slider_k1755025_list = [];
                g_slider_k1755025_list['px'] = { "type": "px", "min": 0, "max": 20, "step": 1 };
g_slider_k1755025_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_k1755025_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_k1755025_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_k1755025_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_k1755025_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_k1755025_list[unit][type];
            };
const slider_xz0rw923Function = (unit, type) => {
                var g_slider_xz0rw923_list = [];
                g_slider_xz0rw923_list['px'] = { "type": "px", "min": 0, "max": 14, "step": 1 };
g_slider_xz0rw923_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_xz0rw923_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_xz0rw923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_xz0rw923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_xz0rw923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_xz0rw923_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_abg0cy23,
rawhtml_mooe0925,
repeater_9qo8go23,
select_9cj4zf23,
select_595scc23,
select_rnc7lt23,
rawhtml_1wxxna25,
slider_l41s3h24,
slider_x9qrcx24,
normalhover_za08o723,
rawhtml_jpfy1r25,
dimension_ppyvov23,
dimension_yiohpe23,
slider_k0pgbq25,
slider_szqvmo25,
typography_j2tc9o23,
color_csrfiz23,
color_sjl3vk23,
color_0ifsza23,
normalhover_wpf6f923,
rawhtml_5r36wa25,
dimension_f3d64j23,
dimension_hw0owm23,
typography_colu3g23,
color_07s30123,
color_4wstaj23,
color_pjmfoh23,
normalhover_w8njhx23,
rawhtml_3nntll25,
slider_ijas5623,
slider_2lvnux25,
slider_ig6lr025,
color_1y7tk223,
color_cgtr8b23,
normalhover_i3sd5u23,
rawhtml_zfkoco25,
slider_12dfcq23,
slider_qmpnwd23,
slider_55eqx325,
slider_ja6oq825,
slider_msm9fl24,
slider_tto3tx24,
slider_iunpeg24,
slider_3gaoy324,
slider_2mh27d24,
slider_00pqga24,
heading_l6skcp23,
color_phbrh123,
heading_coxi2v23,
color_cb9phj23,
heading_8t3wco23,
color_dgdb5r23,
heading_u4z29t23,
color_u6uwgr23,
normalhover_5051p023,
rawhtml_idsoj925,
slider_n1w3iv23,
color_qel2yq25,
color_mknsfl25,
normalhover_obgr1v25,
slider_y7vfau23,
slider_k1755025,
color_e953u023,
color_frajh523,
color_7yc8dx23,
normalhover_ap8zux23,
slider_xz0rw923,
color_8sg7hj23,
color_o8klr423,
color_y4257t25,
normalhover_8c2xt925,
rawhtml_v39p1b25,
dimension_y4lx1023,
dimension_oyasnz23,
background_27rlp723,
border_yp8c5723,
dimension_4cxerm23,
boxshadow_4z5n1c23,
background_n6jf3h23,
border_e5gwsa23,
boxshadow_9wzyqt23,
normalhover_b19a0b23,

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
                        main_function_tb0xmk24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_tb0xmk24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let hzMain = $scope.find('.wkit-multi-step-hz-wrapper');
let hzStep = hzMain[0].querySelectorAll('.wkit-hz-pix-levels-step');

hzStep.forEach(function(el) {
    if (!el.classList.contains('wkit-hz-active')) {
        el.classList.add('wkit-hz-active');
    }else{
        el.classList.remove('wkit-hz-active');
        el.classList.add('wkit-hz-complete');
    }
});

var hzNumber = hzMain[0].querySelectorAll('.wkit-hz-inner-style-2 .wkit-prg-two');

hzNumber.forEach(function(element) {
    if (!element.hasAttribute('data-updated')) {
        var dataIndex = parseInt(element.getAttribute('data-index')) || 0;
        element.setAttribute('data-index', dataIndex + 1);
        element.setAttribute('data-updated', 'true');
    }
});

var hzCheckIcon = hzMain[0].querySelectorAll('.wkit-hz-inner-style-3 .wkit-hz-active-yes .wkit-hz-pix-levels-dot-inner');
hzCheckIcon.forEach(function(ele) {
    ele.classList.add('wkit-hz-check-mark');
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
                help: `You must enable the ACTIVE field from Repeater when choosing Check Mark to show an active icon.`,
                
                value: select_abg0cy23,
                onChange: (value) => {setAttributes({ select_abg0cy23: value }) },
            }),
( select_abg0cy23 == "style-3" ) && React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_mooe0925,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_mooe0925: value }),
            }), 
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Repeater, {
            // max: 10,
            labelText:__(`Levels`),
            value: repeater_9qo8go23,
            attributeName: 'repeater_9qo8go23',
            addText: 'Add Item',
            
            
            onChange: value => setAttributes({ repeater_9qo8go23: value }),
            },
            (value, onChange) => {
                return [
                    React.createElement(Fragment, null,
                         React.createElement(Pmgc_Select, {
                label: __(`Select`),
                options:[['text',__('Text')],['icon',__('Icon')],['image',__('Image')],],
                separator:"default",
                
                
                value: value.select_fstai223,
                onChange: v => { value.select_fstai223 = v; onChange(value); },
            }),
( value.select_fstai223 == "text" ) && React.createElement(Pmgc_Text, {
                label: __(`Title`),
                type: "text",
                value: value.text_83fo9323,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: v => { value.text_83fo9323 = v; onChange(value); },
            }),
( value.select_fstai223 == "icon" ) && React.createElement(Pmgc_IconList, {
            label: __(`Select Icon`),
            
            value: value.iconscontrol_391bl823,
            separator:'default',
            onChange: v => { value.iconscontrol_391bl823 = v; onChange(value); },
            }), 
( value.select_fstai223 == "image" ) && React.createElement(Pmgc_Media, {
                label: __(`Select Image`),
                multiple: false,
                separator:'default',
                value: value.media_7czjjy23,
                
                
                type: ["image","svg"],
                panel: true,
                inlineblock:false,
                onChange: v => { value.media_7czjjy23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_TextArea, {
                label: __(`Description`),
                separator:"default",
                inlineblock:false,
                
                rows:"2",
                value: value.wysiwyg_y0zki523,
                
                onChange: v => { value.wysiwyg_y0zki523 = v; onChange(value); },
            }),
 React.createElement(Pmgc_Toggle, {
            label: __(`Active`),
            
            value: value.switcher_vdtbtv23,
            
            onChange: v => { value.switcher_vdtbtv23 = (v == true ? 'yes' : v); onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Normal`),
            value: value.heading_ih8txk23,
            separator:"default",
            inlineblock: true,
            }), 
( value.select_fstai223 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: value.color_3gtq5223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_3gtq5223 = v; onChange(value); },
            }), 
( value.select_fstai223 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_kvwfob23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_kvwfob23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: value.color_cpt8q623,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_cpt8q623 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Hover`),
            value: value.heading_zp6dmt23,
            separator:"before",
            inlineblock: true,
            }), 
( value.select_fstai223 == "text" ) && React.createElement(Pmgc_Color, {
            label: __(`Title Color`),
            value: value.color_fdwdr923,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_fdwdr923 = v; onChange(value); },
            }), 
( value.select_fstai223 == "icon" ) && React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: value.color_id73mt23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_id73mt23 = v; onChange(value); },
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Description Color`),
            value: value.color_czo5ce23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_czo5ce23 = v; onChange(value); },
            }), 
( !value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Dot Color`),
            value: value.color_5iswbp23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"before",
            
            
            onChange: v => { value.color_5iswbp23 = v; onChange(value); },
            }), 
( value.select_fstai223 == "icon" && value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Icon Color`),
            value: value.color_2f4wn225,
            disableAlpha: false,
            disableGlobal: false,
            separator:"before",
            
            
            onChange: v => { value.color_2f4wn225 = v; onChange(value); },
            }), 
( value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Dot Color`),
            value: value.color_xazzfl23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_xazzfl23 = v; onChange(value); },
            }), 
( !value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Inner Dot Color`),
            value: value.color_rpbmum23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_rpbmum23 = v; onChange(value); },
            }), 
( value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Inner Dot Color`),
            value: value.color_ybqtct23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_ybqtct23 = v; onChange(value); },
            }), 
( !value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Progress Bar Color`),
            value: value.color_73oeht23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_73oeht23 = v; onChange(value); },
            }), 
( value.switcher_vdtbtv23 ) && React.createElement(Pmgc_Color, {
            label: __(`Active Progress Bar Color`),
            value: value.color_2gfe2b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: v => { value.color_2gfe2b23 = v; onChange(value); },
            }), 

                    )]
            }), 
), React.createElement(PanelBody, { title: __("Extra Options"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Desktop`),
                options:[['dcl-1',__('1')],['dcl-2',__('2')],['dcl-3',__('3')],['dcl-4',__('4')],['dcl-5',__('5')],['dcl-6',__('6')],['dcl-7',__('7')],['dcl-8',__('8')],],
                separator:"default",
                
                
                value: select_9cj4zf23,
                onChange: (value) => {setAttributes({ select_9cj4zf23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Tablet`),
                options:[['tcl-1',__('1')],['tcl-2',__('2')],['tcl-3',__('3')],['tcl-4',__('4')],['tcl-5',__('5')],['tcl-6',__('6')],],
                separator:"default",
                
                
                value: select_595scc23,
                onChange: (value) => {setAttributes({ select_595scc23: value }) },
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Mobile`),
                options:[['mcl-1',__('1')],['mcl-2',__('2')],['mcl-3',__('3')],['mcl-4',__('4')],],
                separator:"default",
                
                
                value: select_rnc7lt23,
                onChange: (value) => {setAttributes({ select_rnc7lt23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_1wxxna25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/horizontal-level-block/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_1wxxna25: value }),
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
            value: slider_l41s3h24,
            
            min: slider_l41s3h24 && slider_l41s3h24.unit ? slider_l41s3h24Function(slider_l41s3h24.unit, 'min') : 0,
            max: slider_l41s3h24 && slider_l41s3h24.unit ? slider_l41s3h24Function(slider_l41s3h24.unit, 'max') : 100,
            step: slider_l41s3h24 && slider_l41s3h24.unit ? slider_l41s3h24Function(slider_l41s3h24.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_l41s3h24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Range, {
            label: __(`Opacity`),
            separator:'default',
            value: slider_x9qrcx24,
            
            min: slider_x9qrcx24 && slider_x9qrcx24.unit ? slider_x9qrcx24Function(slider_x9qrcx24.unit, 'min') : 0,
            max: slider_x9qrcx24 && slider_x9qrcx24.unit ? slider_x9qrcx24Function(slider_x9qrcx24.unit, 'max') : 100,
            step: slider_x9qrcx24 && slider_x9qrcx24.unit ? slider_x9qrcx24Function(slider_x9qrcx24.unit, 'step') : 1,
            
                unit: ['%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_x9qrcx24: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_jpfy1r25,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_jpfy1r25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Title"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ppyvov23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ppyvov23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_yiohpe23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_yiohpe23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_k0pgbq25,
            
            min: slider_k0pgbq25 && slider_k0pgbq25.unit ? slider_k0pgbq25Function(slider_k0pgbq25.unit, 'min') : 0,
            max: slider_k0pgbq25 && slider_k0pgbq25.unit ? slider_k0pgbq25Function(slider_k0pgbq25.unit, 'max') : 100,
            step: slider_k0pgbq25 && slider_k0pgbq25.unit ? slider_k0pgbq25Function(slider_k0pgbq25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_k0pgbq25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Offset`),
            separator:'default',
            value: slider_szqvmo25,
            
            min: slider_szqvmo25 && slider_szqvmo25.unit ? slider_szqvmo25Function(slider_szqvmo25.unit, 'min') : 0,
            max: slider_szqvmo25 && slider_szqvmo25.unit ? slider_szqvmo25Function(slider_szqvmo25.unit, 'max') : 100,
            step: slider_szqvmo25 && slider_szqvmo25.unit ? slider_szqvmo25Function(slider_szqvmo25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_szqvmo25: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_j2tc9o23,
            onChange: (value) => setAttributes({ typography_j2tc9o23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_csrfiz23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_csrfiz23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_sjl3vk23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_sjl3vk23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_0ifsza23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_0ifsza23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_5r36wa25,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_5r36wa25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Description"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_f3d64j23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_f3d64j23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_hw0owm23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_hw0owm23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Typography, {
            
            value: typography_colu3g23,
            onChange: (value) => setAttributes({ typography_colu3g23: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_07s30123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_07s30123: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_4wstaj23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_4wstaj23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_pjmfoh23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_pjmfoh23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_3nntll25,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_3nntll25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Icon"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_ijas5623,
            
            min: slider_ijas5623 && slider_ijas5623.unit ? slider_ijas5623Function(slider_ijas5623.unit, 'min') : 0,
            max: slider_ijas5623 && slider_ijas5623.unit ? slider_ijas5623Function(slider_ijas5623.unit, 'max') : 100,
            step: slider_ijas5623 && slider_ijas5623.unit ? slider_ijas5623Function(slider_ijas5623.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ijas5623: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_2lvnux25,
            
            min: slider_2lvnux25 && slider_2lvnux25.unit ? slider_2lvnux25Function(slider_2lvnux25.unit, 'min') : 0,
            max: slider_2lvnux25 && slider_2lvnux25.unit ? slider_2lvnux25Function(slider_2lvnux25.unit, 'max') : 100,
            step: slider_2lvnux25 && slider_2lvnux25.unit ? slider_2lvnux25Function(slider_2lvnux25.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2lvnux25: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Offset`),
            separator:'default',
            value: slider_ig6lr025,
            
            min: slider_ig6lr025 && slider_ig6lr025.unit ? slider_ig6lr025Function(slider_ig6lr025.unit, 'min') : 0,
            max: slider_ig6lr025 && slider_ig6lr025.unit ? slider_ig6lr025Function(slider_ig6lr025.unit, 'max') : 100,
            step: slider_ig6lr025 && slider_ig6lr025.unit ? slider_ig6lr025Function(slider_ig6lr025.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ig6lr025: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_1y7tk223,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1y7tk223: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_cgtr8b23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cgtr8b23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_zfkoco25,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_zfkoco25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Image"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Left Offset`),
            separator:'default',
            value: slider_12dfcq23,
            
            min: slider_12dfcq23 && slider_12dfcq23.unit ? slider_12dfcq23Function(slider_12dfcq23.unit, 'min') : 0,
            max: slider_12dfcq23 && slider_12dfcq23.unit ? slider_12dfcq23Function(slider_12dfcq23.unit, 'max') : 100,
            step: slider_12dfcq23 && slider_12dfcq23.unit ? slider_12dfcq23Function(slider_12dfcq23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_12dfcq23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_qmpnwd23,
            
            min: slider_qmpnwd23 && slider_qmpnwd23.unit ? slider_qmpnwd23Function(slider_qmpnwd23.unit, 'min') : 0,
            max: slider_qmpnwd23 && slider_qmpnwd23.unit ? slider_qmpnwd23Function(slider_qmpnwd23.unit, 'max') : 100,
            step: slider_qmpnwd23 && slider_qmpnwd23.unit ? slider_qmpnwd23Function(slider_qmpnwd23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_qmpnwd23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_55eqx325,
            
            min: slider_55eqx325 && slider_55eqx325.unit ? slider_55eqx325Function(slider_55eqx325.unit, 'min') : 0,
            max: slider_55eqx325 && slider_55eqx325.unit ? slider_55eqx325Function(slider_55eqx325.unit, 'max') : 100,
            step: slider_55eqx325 && slider_55eqx325.unit ? slider_55eqx325Function(slider_55eqx325.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_55eqx325: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Bottom Offset`),
            separator:'default',
            value: slider_ja6oq825,
            
            min: slider_ja6oq825 && slider_ja6oq825.unit ? slider_ja6oq825Function(slider_ja6oq825.unit, 'min') : 0,
            max: slider_ja6oq825 && slider_ja6oq825.unit ? slider_ja6oq825Function(slider_ja6oq825.unit, 'max') : 100,
            step: slider_ja6oq825 && slider_ja6oq825.unit ? slider_ja6oq825Function(slider_ja6oq825.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_ja6oq825: value }),
            }), 
), React.createElement(PanelBody, { title: __("Dots"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Dots Width`),
            separator:'default',
            value: slider_msm9fl24,
            
            min: slider_msm9fl24 && slider_msm9fl24.unit ? slider_msm9fl24Function(slider_msm9fl24.unit, 'min') : 0,
            max: slider_msm9fl24 && slider_msm9fl24.unit ? slider_msm9fl24Function(slider_msm9fl24.unit, 'max') : 100,
            step: slider_msm9fl24 && slider_msm9fl24.unit ? slider_msm9fl24Function(slider_msm9fl24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_msm9fl24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Dots Height`),
            separator:'default',
            value: slider_tto3tx24,
            
            min: slider_tto3tx24 && slider_tto3tx24.unit ? slider_tto3tx24Function(slider_tto3tx24.unit, 'min') : 0,
            max: slider_tto3tx24 && slider_tto3tx24.unit ? slider_tto3tx24Function(slider_tto3tx24.unit, 'max') : 100,
            step: slider_tto3tx24 && slider_tto3tx24.unit ? slider_tto3tx24Function(slider_tto3tx24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_tto3tx24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Width`),
            separator:'before',
            value: slider_iunpeg24,
            
            min: slider_iunpeg24 && slider_iunpeg24.unit ? slider_iunpeg24Function(slider_iunpeg24.unit, 'min') : 0,
            max: slider_iunpeg24 && slider_iunpeg24.unit ? slider_iunpeg24Function(slider_iunpeg24.unit, 'max') : 100,
            step: slider_iunpeg24 && slider_iunpeg24.unit ? slider_iunpeg24Function(slider_iunpeg24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_iunpeg24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Height`),
            separator:'default',
            value: slider_3gaoy324,
            
            min: slider_3gaoy324 && slider_3gaoy324.unit ? slider_3gaoy324Function(slider_3gaoy324.unit, 'min') : 0,
            max: slider_3gaoy324 && slider_3gaoy324.unit ? slider_3gaoy324Function(slider_3gaoy324.unit, 'max') : 100,
            step: slider_3gaoy324 && slider_3gaoy324.unit ? slider_3gaoy324Function(slider_3gaoy324.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_3gaoy324: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Top offset`),
            separator:'before',
            value: slider_2mh27d24,
            
            min: slider_2mh27d24 && slider_2mh27d24.unit ? slider_2mh27d24Function(slider_2mh27d24.unit, 'min') : 0,
            max: slider_2mh27d24 && slider_2mh27d24.unit ? slider_2mh27d24Function(slider_2mh27d24.unit, 'max') : 100,
            step: slider_2mh27d24 && slider_2mh27d24.unit ? slider_2mh27d24Function(slider_2mh27d24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_2mh27d24: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Inner Dots Left offset`),
            separator:'default',
            value: slider_00pqga24,
            
            min: slider_00pqga24 && slider_00pqga24.unit ? slider_00pqga24Function(slider_00pqga24.unit, 'min') : 0,
            max: slider_00pqga24 && slider_00pqga24.unit ? slider_00pqga24Function(slider_00pqga24.unit, 'max') : 100,
            step: slider_00pqga24 && slider_00pqga24.unit ? slider_00pqga24Function(slider_00pqga24.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_00pqga24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_l6skcp23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_phbrh123,
            disableAlpha: false,
            disableGlobal: false,
            separator:"after",
            
            
            onChange: (value) => setAttributes({ color_phbrh123: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Inner Dots`),
            value: heading_coxi2v23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_cb9phj23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_cb9phj23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Label_Heading, {
            label: __(`Dots`),
            value: heading_8t3wco23,
            separator:"default",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_dgdb5r23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_dgdb5r23: value }),
            }), 
 React.createElement(Pmgc_Label_Heading, {
            label: __(`Inner Dots`),
            value: heading_u4z29t23,
            separator:"before",
            inlineblock: true,
            }), 
 React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_u6uwgr23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_u6uwgr23: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_idsoj925,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_idsoj925: value }),
            }), 
),( select_abg0cy23 == "style-3" ) && React.createElement(PanelBody, { title: __("Checked Icon"), initialOpen: false },
( select_abg0cy23 == "style-3" ) && React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_n1w3iv23,
            
            min: slider_n1w3iv23 && slider_n1w3iv23.unit ? slider_n1w3iv23Function(slider_n1w3iv23.unit, 'min') : 0,
            max: slider_n1w3iv23 && slider_n1w3iv23.unit ? slider_n1w3iv23Function(slider_n1w3iv23.unit, 'max') : 100,
            step: slider_n1w3iv23 && slider_n1w3iv23.unit ? slider_n1w3iv23Function(slider_n1w3iv23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_n1w3iv23: value }),
            }), 
( select_abg0cy23 == "style-3" ) && React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_qel2yq25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_qel2yq25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_mknsfl25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_mknsfl25: value }),
            }), 
), 
), 
),( select_abg0cy23 == "style-2" ) && React.createElement(PanelBody, { title: __("Number"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Size`),
            separator:'default',
            value: slider_y7vfau23,
            
            min: slider_y7vfau23 && slider_y7vfau23.unit ? slider_y7vfau23Function(slider_y7vfau23.unit, 'min') : 0,
            max: slider_y7vfau23 && slider_y7vfau23.unit ? slider_y7vfau23Function(slider_y7vfau23.unit, 'max') : 100,
            step: slider_y7vfau23 && slider_y7vfau23.unit ? slider_y7vfau23Function(slider_y7vfau23.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_y7vfau23: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Top Offset`),
            separator:'default',
            value: slider_k1755025,
            
            min: slider_k1755025 && slider_k1755025.unit ? slider_k1755025Function(slider_k1755025.unit, 'min') : 0,
            max: slider_k1755025 && slider_k1755025.unit ? slider_k1755025Function(slider_k1755025.unit, 'max') : 100,
            step: slider_k1755025 && slider_k1755025.unit ? slider_k1755025Function(slider_k1755025.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_k1755025: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_e953u023,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_e953u023: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_frajh523,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_frajh523: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Number Color`),
            value: color_7yc8dx23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_7yc8dx23: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Progress Bar"), initialOpen: false },
 React.createElement(Pmgc_Range, {
            label: __(`Progress Bar Height`),
            separator:'default',
            value: slider_xz0rw923,
            
            min: slider_xz0rw923 && slider_xz0rw923.unit ? slider_xz0rw923Function(slider_xz0rw923.unit, 'min') : 0,
            max: slider_xz0rw923 && slider_xz0rw923.unit ? slider_xz0rw923Function(slider_xz0rw923.unit, 'max') : 100,
            step: slider_xz0rw923 && slider_xz0rw923.unit ? slider_xz0rw923Function(slider_xz0rw923.unit, 'step') : 1,
            
                unit: ['px', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_xz0rw923: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_8sg7hj23,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_8sg7hj23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Active')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Background Color`),
            value: color_y4257t25,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_y4257t25: value }),
            }), 
), 
), 
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_v39p1b25,
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
            
            
            onChange: (value) => setAttributes({ rawhtml_v39p1b25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Box Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_y4lx1023,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_y4lx1023: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_oyasnz23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_oyasnz23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_27rlp723,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_27rlp723: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_yp8c5723,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_yp8c5723: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_4cxerm23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_4cxerm23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_4z5n1c23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_4z5n1c23: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Background, {
            
            value: background_n6jf3h23,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_n6jf3h23: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_e5gwsa23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_e5gwsa23: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_9wzyqt23,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_9wzyqt23: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-tb0xmk24", block_id, false, props.clientId);
                }
            }

            
let g_select_abg0cy23 = select_abg0cy23 && select_abg0cy23 != undefined  ? select_abg0cy23 : "";
let g_rawhtml_mooe0925 = rawhtml_mooe0925 && rawhtml_mooe0925 != undefined && ( (select_abg0cy23 == "style-3") ) ? rawhtml_mooe0925 : "";
let g_select_9cj4zf23 = select_9cj4zf23 && select_9cj4zf23 != undefined  ? select_9cj4zf23 : "";
let g_select_595scc23 = select_595scc23 && select_595scc23 != undefined  ? select_595scc23 : "";
let g_select_rnc7lt23 = select_rnc7lt23 && select_rnc7lt23 != undefined  ? select_rnc7lt23 : "";
let g_rawhtml_1wxxna25 = rawhtml_1wxxna25 && rawhtml_1wxxna25 != undefined  ? rawhtml_1wxxna25 : "";
let g_rawhtml_jpfy1r25 = rawhtml_jpfy1r25 && rawhtml_jpfy1r25 != undefined  ? rawhtml_jpfy1r25 : "";
let g_dimension_ppyvov23 = dimension_ppyvov23 && dimension_ppyvov23 != undefined  ? dimension_ppyvov23 : "";
let g_dimension_yiohpe23 = dimension_yiohpe23 && dimension_yiohpe23 != undefined  ? dimension_yiohpe23 : "";
let g_typography_j2tc9o23 = typography_j2tc9o23 && typography_j2tc9o23 != undefined  ? typography_j2tc9o23 : "";
let g_color_csrfiz23 = color_csrfiz23 && color_csrfiz23 != undefined  ? color_csrfiz23 : "";
let g_color_sjl3vk23 = color_sjl3vk23 && color_sjl3vk23 != undefined  ? color_sjl3vk23 : "";
let g_color_0ifsza23 = color_0ifsza23 && color_0ifsza23 != undefined  ? color_0ifsza23 : "";
let g_rawhtml_5r36wa25 = rawhtml_5r36wa25 && rawhtml_5r36wa25 != undefined  ? rawhtml_5r36wa25 : "";
let g_dimension_f3d64j23 = dimension_f3d64j23 && dimension_f3d64j23 != undefined  ? dimension_f3d64j23 : "";
let g_dimension_hw0owm23 = dimension_hw0owm23 && dimension_hw0owm23 != undefined  ? dimension_hw0owm23 : "";
let g_typography_colu3g23 = typography_colu3g23 && typography_colu3g23 != undefined  ? typography_colu3g23 : "";
let g_color_07s30123 = color_07s30123 && color_07s30123 != undefined  ? color_07s30123 : "";
let g_color_4wstaj23 = color_4wstaj23 && color_4wstaj23 != undefined  ? color_4wstaj23 : "";
let g_color_pjmfoh23 = color_pjmfoh23 && color_pjmfoh23 != undefined  ? color_pjmfoh23 : "";
let g_rawhtml_3nntll25 = rawhtml_3nntll25 && rawhtml_3nntll25 != undefined  ? rawhtml_3nntll25 : "";
let g_color_1y7tk223 = color_1y7tk223 && color_1y7tk223 != undefined  ? color_1y7tk223 : "";
let g_color_cgtr8b23 = color_cgtr8b23 && color_cgtr8b23 != undefined  ? color_cgtr8b23 : "";
let g_rawhtml_zfkoco25 = rawhtml_zfkoco25 && rawhtml_zfkoco25 != undefined  ? rawhtml_zfkoco25 : "";
let g_heading_l6skcp23 = heading_l6skcp23 && heading_l6skcp23 != undefined  ? heading_l6skcp23 : "";
let g_color_phbrh123 = color_phbrh123 && color_phbrh123 != undefined  ? color_phbrh123 : "";
let g_heading_coxi2v23 = heading_coxi2v23 && heading_coxi2v23 != undefined  ? heading_coxi2v23 : "";
let g_color_cb9phj23 = color_cb9phj23 && color_cb9phj23 != undefined  ? color_cb9phj23 : "";
let g_heading_8t3wco23 = heading_8t3wco23 && heading_8t3wco23 != undefined  ? heading_8t3wco23 : "";
let g_color_dgdb5r23 = color_dgdb5r23 && color_dgdb5r23 != undefined  ? color_dgdb5r23 : "";
let g_heading_u4z29t23 = heading_u4z29t23 && heading_u4z29t23 != undefined  ? heading_u4z29t23 : "";
let g_color_u6uwgr23 = color_u6uwgr23 && color_u6uwgr23 != undefined  ? color_u6uwgr23 : "";
let g_rawhtml_idsoj925 = rawhtml_idsoj925 && rawhtml_idsoj925 != undefined  ? rawhtml_idsoj925 : "";
let g_color_qel2yq25 = color_qel2yq25 && color_qel2yq25 != undefined  ? color_qel2yq25 : "";
let g_color_mknsfl25 = color_mknsfl25 && color_mknsfl25 != undefined  ? color_mknsfl25 : "";
let g_color_e953u023 = color_e953u023 && color_e953u023 != undefined  ? color_e953u023 : "";
let g_color_frajh523 = color_frajh523 && color_frajh523 != undefined  ? color_frajh523 : "";
let g_color_7yc8dx23 = color_7yc8dx23 && color_7yc8dx23 != undefined  ? color_7yc8dx23 : "";
let g_color_8sg7hj23 = color_8sg7hj23 && color_8sg7hj23 != undefined  ? color_8sg7hj23 : "";
let g_color_o8klr423 = color_o8klr423 && color_o8klr423 != undefined  ? color_o8klr423 : "";
let g_color_y4257t25 = color_y4257t25 && color_y4257t25 != undefined  ? color_y4257t25 : "";
let g_rawhtml_v39p1b25 = rawhtml_v39p1b25 && rawhtml_v39p1b25 != undefined  ? rawhtml_v39p1b25 : "";
let g_dimension_y4lx1023 = dimension_y4lx1023 && dimension_y4lx1023 != undefined  ? dimension_y4lx1023 : "";
let g_dimension_oyasnz23 = dimension_oyasnz23 && dimension_oyasnz23 != undefined  ? dimension_oyasnz23 : "";
let g_background_27rlp723 = background_27rlp723 && background_27rlp723 != undefined  ? background_27rlp723 : "";
let g_border_yp8c5723 = border_yp8c5723 && border_yp8c5723 != undefined  ? border_yp8c5723 : "";
let g_dimension_4cxerm23 = dimension_4cxerm23 && dimension_4cxerm23 != undefined  ? dimension_4cxerm23 : "";
let g_boxshadow_4z5n1c23 = boxshadow_4z5n1c23 && boxshadow_4z5n1c23 != undefined  ? boxshadow_4z5n1c23 : "";
let g_background_n6jf3h23 = background_n6jf3h23 && background_n6jf3h23 != undefined  ? background_n6jf3h23 : "";
let g_border_e5gwsa23 = border_e5gwsa23 && border_e5gwsa23 != undefined  ? border_e5gwsa23 : "";
let g_boxshadow_9wzyqt23 = boxshadow_9wzyqt23 && boxshadow_9wzyqt23 != undefined  ? boxshadow_9wzyqt23 : "";
            
let repeater_9qo8go23_ek25 = "";
                            
repeater_9qo8go23  && repeater_9qo8go23.map((r_item, index) => {
                                
let grnp_select_fstai223 = r_item.select_fstai223  ? r_item.select_fstai223 : "";
let grnp_text_83fo9323 = r_item.text_83fo9323 && ( (r_item?.select_fstai223 == "text") ) ? r_item.text_83fo9323 : "";
let grnp_iconscontrol_391bl823 = r_item?.iconscontrol_391bl823 != undefined && ( (r_item?.select_fstai223 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_391bl823+'"></i></span>' : '';

let grnp_media_7czjjy23 = r_item?.media_7czjjy23?.url != undefined && ( (r_item?.select_fstai223 == "image") ) ? r_item?.media_7czjjy23.url : "";
let grnp_wysiwyg_y0zki523 = r_item.wysiwyg_y0zki523  ? r_item.wysiwyg_y0zki523 : "";
let grnp_switcher_vdtbtv23 = r_item.switcher_vdtbtv23  ? 'yes' : "";
let grnp_heading_ih8txk23 = r_item.heading_ih8txk23  ? r_item.heading_ih8txk23 : "";
let grnp_color_3gtq5223 = r_item.color_3gtq5223 && ( (r_item?.select_fstai223 == "text") ) ? r_item.color_3gtq5223 : "";
let grnp_color_kvwfob23 = r_item.color_kvwfob23 && ( (r_item?.select_fstai223 == "icon") ) ? r_item.color_kvwfob23 : "";
let grnp_color_cpt8q623 = r_item.color_cpt8q623  ? r_item.color_cpt8q623 : "";
let grnp_heading_zp6dmt23 = r_item.heading_zp6dmt23  ? r_item.heading_zp6dmt23 : "";
let grnp_color_fdwdr923 = r_item.color_fdwdr923 && ( (r_item?.select_fstai223 == "text") ) ? r_item.color_fdwdr923 : "";
let grnp_color_id73mt23 = r_item.color_id73mt23 && ( (r_item?.select_fstai223 == "icon") ) ? r_item.color_id73mt23 : "";
let grnp_color_czo5ce23 = r_item.color_czo5ce23  ? r_item.color_czo5ce23 : "";
let grnp_color_5iswbp23 = r_item.color_5iswbp23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_5iswbp23 : "";
let grnp_color_2f4wn225 = r_item.color_2f4wn225 && ( (r_item?.select_fstai223 == "icon")&&r_item?.switcher_vdtbtv23 ) ? r_item.color_2f4wn225 : "";
let grnp_color_xazzfl23 = r_item.color_xazzfl23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_xazzfl23 : "";
let grnp_color_rpbmum23 = r_item.color_rpbmum23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_rpbmum23 : "";
let grnp_color_ybqtct23 = r_item.color_ybqtct23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_ybqtct23 : "";
let grnp_color_73oeht23 = r_item.color_73oeht23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_73oeht23 : "";
let grnp_color_2gfe2b23 = r_item.color_2gfe2b23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_2gfe2b23 : "";
                                repeater_9qo8go23_ek25 += `<div id="" class="tp-repeater-item-${r_item._key} wkit-hz-step-content wkit-hz-${grnp_select_fstai223} wkit-hz-pix-levels-step wkit-hz-active-${grnp_switcher_vdtbtv23} col-xs-12 hz-${g_select_9cj4zf23} hz-${g_select_595scc23} hz-${g_select_rnc7lt23}" data-repeater_9qo8go23="{repeater_9qo8go23}">
                <h6 class="wkit-hz-step-title text-center" data-ttl="${grnp_text_83fo9323}">${grnp_text_83fo9323}</h6>
                <span class="wkit-hz-progress-icon">${grnp_iconscontrol_391bl823 }</span>
                <div class="wkit-hz-progress-img">
                    <img src="${grnp_media_7czjjy23}" class="tp-hz-title-image">
                </div>
                <div class="wkit-progress-area text-center">
                    <div class="progress wkit-bg-gray-2">
                        <div class="wkit-prg-one progress-bar wkit-hz-bg-gradient-primary">
                        </div>
                    </div>
                    <div class="wkit-pix-leveles-dot-div">
                        <span class="wkit-prg-two wkit-hz-pix-levels-dot wkit-hz-bg-gradient-primary" data-index="${index}">
                            <span class="wkit-hz-pix-levels-dot-inner wkit-hz-bg-dark-opacity-3"></span>
                        </span>
                    </div>
                </div>
                <span class="wkit-hz-step-desc text-center" data-desc="${grnp_wysiwyg_y0zki523}">${grnp_wysiwyg_y0zki523}</span>
            </div>`;
                            })
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_tb0xmk24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-multi-step-hz-wrapper text-center">
    <div class="wkit-hz-card wkit-hz-inner-${g_select_abg0cy23}">
        <div class="wkit-hz-content-bg row">
            ${repeater_9qo8go23_ek25}
        </div>
    </div>
</div>       `
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
            select_abg0cy23,
rawhtml_mooe0925,
repeater_9qo8go23,
select_9cj4zf23,
select_595scc23,
select_rnc7lt23,
rawhtml_1wxxna25,
slider_l41s3h24,
slider_x9qrcx24,
normalhover_za08o723,
rawhtml_jpfy1r25,
dimension_ppyvov23,
dimension_yiohpe23,
slider_k0pgbq25,
slider_szqvmo25,
typography_j2tc9o23,
color_csrfiz23,
color_sjl3vk23,
color_0ifsza23,
normalhover_wpf6f923,
rawhtml_5r36wa25,
dimension_f3d64j23,
dimension_hw0owm23,
typography_colu3g23,
color_07s30123,
color_4wstaj23,
color_pjmfoh23,
normalhover_w8njhx23,
rawhtml_3nntll25,
slider_ijas5623,
slider_2lvnux25,
slider_ig6lr025,
color_1y7tk223,
color_cgtr8b23,
normalhover_i3sd5u23,
rawhtml_zfkoco25,
slider_12dfcq23,
slider_qmpnwd23,
slider_55eqx325,
slider_ja6oq825,
slider_msm9fl24,
slider_tto3tx24,
slider_iunpeg24,
slider_3gaoy324,
slider_2mh27d24,
slider_00pqga24,
heading_l6skcp23,
color_phbrh123,
heading_coxi2v23,
color_cb9phj23,
heading_8t3wco23,
color_dgdb5r23,
heading_u4z29t23,
color_u6uwgr23,
normalhover_5051p023,
rawhtml_idsoj925,
slider_n1w3iv23,
color_qel2yq25,
color_mknsfl25,
normalhover_obgr1v25,
slider_y7vfau23,
slider_k1755025,
color_e953u023,
color_frajh523,
color_7yc8dx23,
normalhover_ap8zux23,
slider_xz0rw923,
color_8sg7hj23,
color_o8klr423,
color_y4257t25,
normalhover_8c2xt925,
rawhtml_v39p1b25,
dimension_y4lx1023,
dimension_oyasnz23,
background_27rlp723,
border_yp8c5723,
dimension_4cxerm23,
boxshadow_4z5n1c23,
background_n6jf3h23,
border_e5gwsa23,
boxshadow_9wzyqt23,
normalhover_b19a0b23,

            block_id,
        } = attributes;

        

        

        

        
let g_select_abg0cy23 = select_abg0cy23 && select_abg0cy23 != undefined  ? select_abg0cy23 : "";
let g_rawhtml_mooe0925 = rawhtml_mooe0925 && rawhtml_mooe0925 != undefined && ( (select_abg0cy23 == "style-3") ) ? rawhtml_mooe0925 : "";
let g_select_9cj4zf23 = select_9cj4zf23 && select_9cj4zf23 != undefined  ? select_9cj4zf23 : "";
let g_select_595scc23 = select_595scc23 && select_595scc23 != undefined  ? select_595scc23 : "";
let g_select_rnc7lt23 = select_rnc7lt23 && select_rnc7lt23 != undefined  ? select_rnc7lt23 : "";
let g_rawhtml_1wxxna25 = rawhtml_1wxxna25 && rawhtml_1wxxna25 != undefined  ? rawhtml_1wxxna25 : "";
let g_rawhtml_jpfy1r25 = rawhtml_jpfy1r25 && rawhtml_jpfy1r25 != undefined  ? rawhtml_jpfy1r25 : "";
let g_dimension_ppyvov23 = dimension_ppyvov23 && dimension_ppyvov23 != undefined  ? dimension_ppyvov23 : "";
let g_dimension_yiohpe23 = dimension_yiohpe23 && dimension_yiohpe23 != undefined  ? dimension_yiohpe23 : "";
let g_typography_j2tc9o23 = typography_j2tc9o23 && typography_j2tc9o23 != undefined  ? typography_j2tc9o23 : "";
let g_color_csrfiz23 = color_csrfiz23 && color_csrfiz23 != undefined  ? color_csrfiz23 : "";
let g_color_sjl3vk23 = color_sjl3vk23 && color_sjl3vk23 != undefined  ? color_sjl3vk23 : "";
let g_color_0ifsza23 = color_0ifsza23 && color_0ifsza23 != undefined  ? color_0ifsza23 : "";
let g_rawhtml_5r36wa25 = rawhtml_5r36wa25 && rawhtml_5r36wa25 != undefined  ? rawhtml_5r36wa25 : "";
let g_dimension_f3d64j23 = dimension_f3d64j23 && dimension_f3d64j23 != undefined  ? dimension_f3d64j23 : "";
let g_dimension_hw0owm23 = dimension_hw0owm23 && dimension_hw0owm23 != undefined  ? dimension_hw0owm23 : "";
let g_typography_colu3g23 = typography_colu3g23 && typography_colu3g23 != undefined  ? typography_colu3g23 : "";
let g_color_07s30123 = color_07s30123 && color_07s30123 != undefined  ? color_07s30123 : "";
let g_color_4wstaj23 = color_4wstaj23 && color_4wstaj23 != undefined  ? color_4wstaj23 : "";
let g_color_pjmfoh23 = color_pjmfoh23 && color_pjmfoh23 != undefined  ? color_pjmfoh23 : "";
let g_rawhtml_3nntll25 = rawhtml_3nntll25 && rawhtml_3nntll25 != undefined  ? rawhtml_3nntll25 : "";
let g_color_1y7tk223 = color_1y7tk223 && color_1y7tk223 != undefined  ? color_1y7tk223 : "";
let g_color_cgtr8b23 = color_cgtr8b23 && color_cgtr8b23 != undefined  ? color_cgtr8b23 : "";
let g_rawhtml_zfkoco25 = rawhtml_zfkoco25 && rawhtml_zfkoco25 != undefined  ? rawhtml_zfkoco25 : "";
let g_heading_l6skcp23 = heading_l6skcp23 && heading_l6skcp23 != undefined  ? heading_l6skcp23 : "";
let g_color_phbrh123 = color_phbrh123 && color_phbrh123 != undefined  ? color_phbrh123 : "";
let g_heading_coxi2v23 = heading_coxi2v23 && heading_coxi2v23 != undefined  ? heading_coxi2v23 : "";
let g_color_cb9phj23 = color_cb9phj23 && color_cb9phj23 != undefined  ? color_cb9phj23 : "";
let g_heading_8t3wco23 = heading_8t3wco23 && heading_8t3wco23 != undefined  ? heading_8t3wco23 : "";
let g_color_dgdb5r23 = color_dgdb5r23 && color_dgdb5r23 != undefined  ? color_dgdb5r23 : "";
let g_heading_u4z29t23 = heading_u4z29t23 && heading_u4z29t23 != undefined  ? heading_u4z29t23 : "";
let g_color_u6uwgr23 = color_u6uwgr23 && color_u6uwgr23 != undefined  ? color_u6uwgr23 : "";
let g_rawhtml_idsoj925 = rawhtml_idsoj925 && rawhtml_idsoj925 != undefined  ? rawhtml_idsoj925 : "";
let g_color_qel2yq25 = color_qel2yq25 && color_qel2yq25 != undefined  ? color_qel2yq25 : "";
let g_color_mknsfl25 = color_mknsfl25 && color_mknsfl25 != undefined  ? color_mknsfl25 : "";
let g_color_e953u023 = color_e953u023 && color_e953u023 != undefined  ? color_e953u023 : "";
let g_color_frajh523 = color_frajh523 && color_frajh523 != undefined  ? color_frajh523 : "";
let g_color_7yc8dx23 = color_7yc8dx23 && color_7yc8dx23 != undefined  ? color_7yc8dx23 : "";
let g_color_8sg7hj23 = color_8sg7hj23 && color_8sg7hj23 != undefined  ? color_8sg7hj23 : "";
let g_color_o8klr423 = color_o8klr423 && color_o8klr423 != undefined  ? color_o8klr423 : "";
let g_color_y4257t25 = color_y4257t25 && color_y4257t25 != undefined  ? color_y4257t25 : "";
let g_rawhtml_v39p1b25 = rawhtml_v39p1b25 && rawhtml_v39p1b25 != undefined  ? rawhtml_v39p1b25 : "";
let g_dimension_y4lx1023 = dimension_y4lx1023 && dimension_y4lx1023 != undefined  ? dimension_y4lx1023 : "";
let g_dimension_oyasnz23 = dimension_oyasnz23 && dimension_oyasnz23 != undefined  ? dimension_oyasnz23 : "";
let g_background_27rlp723 = background_27rlp723 && background_27rlp723 != undefined  ? background_27rlp723 : "";
let g_border_yp8c5723 = border_yp8c5723 && border_yp8c5723 != undefined  ? border_yp8c5723 : "";
let g_dimension_4cxerm23 = dimension_4cxerm23 && dimension_4cxerm23 != undefined  ? dimension_4cxerm23 : "";
let g_boxshadow_4z5n1c23 = boxshadow_4z5n1c23 && boxshadow_4z5n1c23 != undefined  ? boxshadow_4z5n1c23 : "";
let g_background_n6jf3h23 = background_n6jf3h23 && background_n6jf3h23 != undefined  ? background_n6jf3h23 : "";
let g_border_e5gwsa23 = border_e5gwsa23 && border_e5gwsa23 != undefined  ? border_e5gwsa23 : "";
let g_boxshadow_9wzyqt23 = boxshadow_9wzyqt23 && boxshadow_9wzyqt23 != undefined  ? boxshadow_9wzyqt23 : "";
        
let repeater_9qo8go23_ek25 = "";
                            
repeater_9qo8go23  && repeater_9qo8go23.map((r_item, index) => {
                                
let grnp_select_fstai223 = r_item.select_fstai223  ? r_item.select_fstai223 : "";
let grnp_text_83fo9323 = r_item.text_83fo9323 && ( (r_item?.select_fstai223 == "text") ) ? r_item.text_83fo9323 : "";
let grnp_iconscontrol_391bl823 = r_item?.iconscontrol_391bl823 != undefined && ( (r_item?.select_fstai223 == "icon") ) ? '<span class="tp-title-icon"><i class="'+r_item?.iconscontrol_391bl823+'"></i></span>' : '';

let grnp_media_7czjjy23 = r_item?.media_7czjjy23?.url != undefined && ( (r_item?.select_fstai223 == "image") ) ? r_item?.media_7czjjy23.url : "";
let grnp_wysiwyg_y0zki523 = r_item.wysiwyg_y0zki523  ? r_item.wysiwyg_y0zki523 : "";
let grnp_switcher_vdtbtv23 = r_item.switcher_vdtbtv23  ? 'yes' : "";
let grnp_heading_ih8txk23 = r_item.heading_ih8txk23  ? r_item.heading_ih8txk23 : "";
let grnp_color_3gtq5223 = r_item.color_3gtq5223 && ( (r_item?.select_fstai223 == "text") ) ? r_item.color_3gtq5223 : "";
let grnp_color_kvwfob23 = r_item.color_kvwfob23 && ( (r_item?.select_fstai223 == "icon") ) ? r_item.color_kvwfob23 : "";
let grnp_color_cpt8q623 = r_item.color_cpt8q623  ? r_item.color_cpt8q623 : "";
let grnp_heading_zp6dmt23 = r_item.heading_zp6dmt23  ? r_item.heading_zp6dmt23 : "";
let grnp_color_fdwdr923 = r_item.color_fdwdr923 && ( (r_item?.select_fstai223 == "text") ) ? r_item.color_fdwdr923 : "";
let grnp_color_id73mt23 = r_item.color_id73mt23 && ( (r_item?.select_fstai223 == "icon") ) ? r_item.color_id73mt23 : "";
let grnp_color_czo5ce23 = r_item.color_czo5ce23  ? r_item.color_czo5ce23 : "";
let grnp_color_5iswbp23 = r_item.color_5iswbp23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_5iswbp23 : "";
let grnp_color_2f4wn225 = r_item.color_2f4wn225 && ( (r_item?.select_fstai223 == "icon")&&r_item?.switcher_vdtbtv23 ) ? r_item.color_2f4wn225 : "";
let grnp_color_xazzfl23 = r_item.color_xazzfl23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_xazzfl23 : "";
let grnp_color_rpbmum23 = r_item.color_rpbmum23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_rpbmum23 : "";
let grnp_color_ybqtct23 = r_item.color_ybqtct23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_ybqtct23 : "";
let grnp_color_73oeht23 = r_item.color_73oeht23 && ( !r_item?.switcher_vdtbtv23 ) ? r_item.color_73oeht23 : "";
let grnp_color_2gfe2b23 = r_item.color_2gfe2b23 && ( r_item?.switcher_vdtbtv23 ) ? r_item.color_2gfe2b23 : "";
                                repeater_9qo8go23_ek25 += `<div id="" class="tp-repeater-item-${r_item._key} wkit-hz-step-content wkit-hz-${grnp_select_fstai223} wkit-hz-pix-levels-step wkit-hz-active-${grnp_switcher_vdtbtv23} col-xs-12 hz-${g_select_9cj4zf23} hz-${g_select_595scc23} hz-${g_select_rnc7lt23}" data-repeater_9qo8go23="{repeater_9qo8go23}">
                <h6 class="wkit-hz-step-title text-center" data-ttl="${grnp_text_83fo9323}">${grnp_text_83fo9323}</h6>
                <span class="wkit-hz-progress-icon">${grnp_iconscontrol_391bl823 }</span>
                <div class="wkit-hz-progress-img">
                    <img src="${grnp_media_7czjjy23}" class="tp-hz-title-image">
                </div>
                <div class="wkit-progress-area text-center">
                    <div class="progress wkit-bg-gray-2">
                        <div class="wkit-prg-one progress-bar wkit-hz-bg-gradient-primary">
                        </div>
                    </div>
                    <div class="wkit-pix-leveles-dot-div">
                        <span class="wkit-prg-two wkit-hz-pix-levels-dot wkit-hz-bg-gradient-primary" data-index="${index}">
                            <span class="wkit-hz-pix-levels-dot-inner wkit-hz-bg-dark-opacity-3"></span>
                        </span>
                    </div>
                </div>
                <span class="wkit-hz-step-desc text-center" data-desc="${grnp_wysiwyg_y0zki523}">${grnp_wysiwyg_y0zki523}</span>
            </div>`;
                            })

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-tb0xmk24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_tb0xmk24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-multi-step-hz-wrapper text-center">
    <div class="wkit-hz-card wkit-hz-inner-${g_select_abg0cy23}">
        <div class="wkit-hz-content-bg row">
            ${repeater_9qo8go23_ek25}
        </div>
    </div>
</div>       `
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Horizontal_Level_tb0xmk24();