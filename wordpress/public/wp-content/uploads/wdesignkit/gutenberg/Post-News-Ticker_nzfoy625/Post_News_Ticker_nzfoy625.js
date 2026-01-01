
    class Post_News_Ticker_nzfoy625 {
        constructor() {
            this.Post_News_Ticker_nzfoy625_ea0rkt25();
        }
    
        Post_News_Ticker_nzfoy625_ea0rkt25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
        const {
            select,
            resolveSelect,
        } = wp.data;
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Select,Pmgc_Text,Pmgc_Note,Pmgc_Url,Pmgc_IconList,Pmgc_Typography,Pmgc_Color,Pmgc_Tabs,Pmgc_Dimension,Pmgc_Range,Pmgc_Background,Pmgc_Border,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-nzfoy625', {
        title: __('Post News Ticker'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "fas fa-bullhorn  tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'WDesignKit',
        keywords: [__('Post News Ticker'),__('Blog Post Slider'),__('News Display Widget'),__('Article'),__('Gutenberg Block'),__('Dynamic Content'),__('Content Ticker'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        const [cpt_wblbl324_st, setcpt_wblbl324_st] = useState([]);

        
            useEffect(() => {
                cpt_wblbl324_fun(cpt_wblbl324);
            }, [])

        

        const CPT_array = async (f_data) => {

                var query = {
                    per_page: f_data.max_post,
                    order: f_data.order,
                    offset: 0,
                    status: 'publish',
                    _embed: true,
                };

                if (f_data?.order_by && f_data?.order_by != 'none') {
                    query = Object.assign({}, query, { 'orderby': f_data.order_by })
                }

                if (f_data.include) {
                    let in_array = f_data.include.split(',');
                    query = Object.assign({}, query, { 'include': in_array })
                }

                if (f_data.exclude) {
                    let in_array = f_data.exclude.split(',');
                    query = Object.assign({}, query, { 'exclude': in_array })
                }

                const newdata = await resolveSelect('core').getEntityRecords('postType', f_data.type, query) || [];

                return await newdata;
            }
                
            const GetCPT = (data) => {
                let cpt_obj = {
                    'title' : data?.title?.rendered ? data.title.rendered : '',
                    'description' : data?.excerpt?.rendered ? data.excerpt.rendered : '',
                    'thumbnail' : data._embedded?.['wp:featuredmedia']?.[0]?.source_url ? data._embedded['wp:featuredmedia'][0].source_url : 'https://j-j-labs.com/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                    'post_link' : data?.link ? data.link : '',
                    'post_date' : data?.date ? data.date : '',
                    'auth_name' : data?._embedded?.author?.[0]?.name ? data._embedded.author[0].name : '',
                    'auth_id' : data?._embedded?.author?.[0]?.id ? data._embedded.author[0].id : '',
                    'auth_profile' : data?._embedded?.author?.[0]?.avatar_urls?.['48'] ? data._embedded.author[0].avatar_urls['48'] : 'https://j-j-labs.com/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                    'auth_url' : data?._embedded?.author?.[0]?.link ? data?._embedded.author[0].link : '',
                    'catrgory_list' : data?._embedded?.['wp:term']?.[0] ? data._embedded['wp:term'][0] : [],
                    'tag_list' : data?._embedded?.['wp:term']?.[1] ? data._embedded['wp:term'][1] : [],
                };

                return cpt_obj;
            }

        const cpt_wblbl324_fun = async (value, type) => {

                    let filters = {
                        type: cpt_wblbl324,
                        include: include_wblbl324,
                        exclude: exclude_wblbl324,
                        max_post: max_post_wblbl324,
                        order: order_wblbl324,
                        order_by: order_by_wblbl324,
                        max_cat: max_cat_wblbl324,
                        max_tag: max_tag_wblbl324,
                    }

                    if (value != undefined && type != undefined) {
                        filters = Object.assign({}, filters, { [type]: value })
                    }

                    let data = await CPT_array(filters);

                    await setcpt_wblbl324_st(data);
                }
const slider_vsvkp425Function = (unit, type) => {
                var g_slider_vsvkp425_list = [];
                g_slider_vsvkp425_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_vsvkp425_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_vsvkp425_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_vsvkp425_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_vsvkp425_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_vsvkp425_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_vsvkp425_list[unit][type];
            };
const slider_0di8a525Function = (unit, type) => {
                var g_slider_0di8a525_list = [];
                g_slider_0di8a525_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_0di8a525_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_0di8a525_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_0di8a525_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_0di8a525_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_0di8a525_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_0di8a525_list[unit][type];
            };
const slider_8okhk024Function = (unit, type) => {
                var g_slider_8okhk024_list = [];
                g_slider_8okhk024_list['px'] = { "type": "px", "min": 0, "max": 1000, "step": 1 };
g_slider_8okhk024_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_8okhk024_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_8okhk024_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_8okhk024_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_8okhk024_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_8okhk024_list[unit][type];
            };
const slider_bsk1hb24Function = (unit, type) => {
                var g_slider_bsk1hb24_list = [];
                g_slider_bsk1hb24_list['px'] = { "type": "px", "min": 0, "max": 100, "step": 1 };
g_slider_bsk1hb24_list['%'] = { "type": "%", "min": 0, "max": 10, "step": 1 };
g_slider_bsk1hb24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_bsk1hb24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_bsk1hb24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_bsk1hb24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_bsk1hb24_list[unit][type];
            };
const slider_yj60wm24Function = (unit, type) => {
                var g_slider_yj60wm24_list = [];
                g_slider_yj60wm24_list['px'] = { "type": "px", "min": 0, "max": 150, "step": 1 };
g_slider_yj60wm24_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_yj60wm24_list['em'] = { "type": "em", "min": 0, "max": 10, "step": 1 };
g_slider_yj60wm24_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_yj60wm24_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_yj60wm24_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_yj60wm24_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               select_zf67du24,
include_wblbl324,
exclude_wblbl324,
max_post_wblbl324,
order_wblbl324,
order_by_wblbl324,
max_cat_wblbl324,
max_tag_wblbl324,
cpt_wblbl324,
rawhtml_l32l8b25,
text_lx4byh24,
url_2ncmwl24,
iconscontrol_41jkqf24,
iconscontrol_91o61124,
rawhtml_xig5cz25,
typography_gp54l824,
color_wrjl2324,
color_wiclqb24,
normalhover_yai7q224,
dimension_ai6uxe24,
dimension_5owrw424,
slider_vsvkp425,
slider_0di8a525,
typography_dtc79d24,
color_dul1x924,
background_m90yb124,
color_1yrvb424,
background_ez26kn24,
border_c2a5zz25,
dimension_zpk2vl24,
border_ifpsy925,
boxshadow_590ulz25,
boxshadow_plgzuf25,
normalhover_8k8h5c24,
dimension_53il9m24,
slider_8okhk024,
slider_bsk1hb24,
color_74gcj224,
background_92d34g24,
color_5aau1a24,
background_5itug524,
normalhover_1heid124,
dimension_j7md5125,
slider_yj60wm24,
background_u4h1kb24,
border_57fid325,
dimension_fjv6nk24,
border_772iem25,
boxshadow_04x5yu24,
boxshadow_10n2cg24,
normalhover_679qm124,

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
                        main_function_nzfoy625(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_nzfoy625 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                let postnews = $scope[0].querySelector('.wkit-pnt-main-container');
var unid = Math.random().toString(32).slice(2);
if(postnews.getAttribute('data-unique')){
    return;
}else{
    postnews.setAttribute('data-unique', unid)
}
let currentIndex = 0;
let isAnimating = false; // Flag to track animation state
let autoSlideInterval;

function getNewsContents() {
    return postnews.querySelectorAll('.w-pntslider-content'); // Fetch the content only within this widget
}

function updateContent(direction) {
    if (isAnimating) return; // Prevent overlapping animations
    isAnimating = true; // Set animation flag

    const newsContents = getNewsContents();
    if (newsContents.length === 0) {
        isAnimating = false; // Reset animation flag if no content
        return;
    }

    const currentItem = newsContents[currentIndex];
    currentItem.classList.remove('active');
    currentItem.classList.add('outgoing');

    if (direction === 'next') {
        currentIndex = (currentIndex + 1) % newsContents.length;
    } else if (direction === 'prev') {
        currentIndex = (currentIndex - 1 + newsContents.length) % newsContents.length;
    }

    const newItem = newsContents[currentIndex];
    const selectValue = postnews.getAttribute('data-select');

    // Apply animations
    if (selectValue === 'style-1' || selectValue === 'style-2' || selectValue === 'style-4' || selectValue === 'style-5') {
        currentItem.style.transform = direction === 'next' ? 'translateX(-100%)' : 'translateX(100%)';
        newItem.style.transform = direction === 'next' ? 'translateX(100%)' : 'translateX(-100%)';
    } else if (selectValue === 'style-3') {
        currentItem.style.transform = direction === 'next' ? 'translateY(-100%)' : 'translateY(100%)';
        newItem.style.transform = direction === 'next' ? 'translateY(100%)' : 'translateY(-100%)';
    }

    setTimeout(function () {
        if (selectValue === 'style-3') {
            newItem.classList.add('active');
            newItem.style.transform = 'translateY(0%)';
        } else {
            newItem.classList.add('active');
            newItem.style.transform = 'translateX(0%)';
        }
        currentItem.classList.remove('outgoing');
        currentItem.style.transform = '';
        isAnimating = false; // Reset animation flag
    }, 300); // Ensure this matches your CSS transition duration
}

function autoSlide() {
    autoSlideInterval = setInterval(function () {
        updateContent('next');
    }, 3000);
}

// Set the first item as active on load
const newsContents = getNewsContents();
if (newsContents.length > 0) {
    newsContents[0].classList.add('active'); // Ensure the first item is active
}

if (postnews.classList.contains('w-pntslider-style-1') || 
    postnews.classList.contains('w-pntslider-style-3') || 
    postnews.classList.contains('w-pntslider-style-4')) {
    
    postnews.querySelector('.w-pnt-nextBtn').addEventListener('click', function () {
        updateContent('next');
    });
    postnews.querySelector('.w-pnt-prevBtn').addEventListener('click', function () {
        updateContent('prev');
    });
}

if (postnews.classList.contains('w-pntslider-style-2') || 
    postnews.classList.contains('w-pntslider-style-5')) {
    autoSlide();
}
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Layout"), initialOpen: true },
 React.createElement(Pmgc_Select, {
                label: __(`Style`),
                options:[['style-1',__('Style 1')],['style-2',__('Style 2')],['style-3',__('Style 3')],['style-4',__('Style 4')],['style-5',__('Style 5')],],
                separator:"default",
                
                
                value: select_zf67du24,
                onChange: (value) => {setAttributes({ select_zf67du24: value }) },
            }),
), React.createElement(PanelBody, { title: __("Content"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Post Type`),
                options: wdkit_post_type?.post_list ? wdkit_post_type.post_list : [],
                separator:"default",
                help: `Author details aren't displayed for the "Product" post type.`,
                
                value: cpt_wblbl324,
                onChange: (value) => {setAttributes({ cpt_wblbl324: value }), cpt_wblbl324_fun(value, 'type')},
            }),

            
             React.createElement(Pmgc_Text, {
                label: __(`Include Product(s)`),
                type: "text",
                value: include_wblbl324,
                placeholder:`product_id`,
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ include_wblbl324: value }) , cpt_wblbl324_fun(value, 'include')},
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Exclude Product(s)`),
                type: "text",
                value: exclude_wblbl324,
                placeholder:`product_id`,
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ exclude_wblbl324: value }) , cpt_wblbl324_fun(value, 'exclude')},
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Max Post`),
                type: "number",
                value: max_post_wblbl324,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ max_post_wblbl324: value }) , cpt_wblbl324_fun(value, 'max_post')},
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Order`),
                options:[['desc',__('Descending')],['asc',__('Ascending')],],
                separator:"default",
                
                
                value: order_wblbl324,
                onChange: (value) => {setAttributes({ order_wblbl324: value }) , cpt_wblbl324_fun(value, 'order')},
            }),
 React.createElement(Pmgc_Select, {
                label: __(`Order By`),
                options:wdkit_post_type?.order_by ? wdkit_post_type.order_by : [],
                separator:"default",
                
                
                value: order_by_wblbl324,
                onChange: (value) => {setAttributes({ order_by_wblbl324: value }) , cpt_wblbl324_fun(value, 'order_by')},
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Max Category`),
                type: "number",
                value: max_cat_wblbl324,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ max_cat_wblbl324: value }) , cpt_wblbl324_fun(value, 'max_cat')},
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Max Tag`),
                type: "number",
                value: max_tag_wblbl324,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ max_tag_wblbl324: value }) , cpt_wblbl324_fun(value, 'max_tag')},
            }),

 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_l32l8b25,
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
After making any changes, please refresh the page once.
</div>`,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ rawhtml_l32l8b25: value }),
            }), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Text, {
                label: __(`Button Text`),
                type: "text",
                value: text_lx4byh24,
                
                dynamic: true,
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ text_lx4byh24: value }) },
            }),
 React.createElement(Pmgc_Url, {
                label: __(`URL`),
                type: "url",
                value: url_2ncmwl24,
                dynamic: [true, 'url_2ncmwl24'],
                
                
                separator:"default",
                inlineblock:false,
                onChange: (value) => setAttributes({ url_2ncmwl24: value }),
            }),
),( select_zf67du24 == "style-1" || select_zf67du24 == "style-3" || select_zf67du24 == "style-4" ) && React.createElement(PanelBody, { title: __("Navigation Icon"), initialOpen: false },
( select_zf67du24 == "style-1" || select_zf67du24 == "style-3" || select_zf67du24 == "style-4" ) && React.createElement(Pmgc_IconList, {
            label: __(`Previous Icon`),
            
            value: iconscontrol_41jkqf24,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_41jkqf24: value }),
            }), 
( select_zf67du24 == "style-1" || select_zf67du24 == "style-3" || select_zf67du24 == "style-4" ) && React.createElement(Pmgc_IconList, {
            label: __(`Next Icon`),
            
            value: iconscontrol_91o61124,
            separator:'default',
            onChange: (value) => setAttributes({ iconscontrol_91o61124: value }),
            }), 
), React.createElement(PanelBody, { title: __("Need Help ?"), initialOpen: false },
 React.createElement(Pmgc_Note, {
            label: __(` `),
            value: rawhtml_xig5cz25,
            description: `<div class="wdk-help-main" style="display: flex; flex-direction: column; gap: 10px;">
    <a
      class="wdk-docs-link"
      style="color: #010101;
    font-size: 12px;
    font-weight: 400;
    line-height: 22px; text-decoration: none; border-color: transparent"
      href="https://learn.wdesignkit.com/docs/post-news-ticker-block/"
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
      href="https://store.posimyth.com/helpdesk/"
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
            
            
            onChange: (value) => setAttributes({ rawhtml_xig5cz25: value }),
            }), 
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Content"), initialOpen: true },
 React.createElement(Pmgc_Typography, {
            
            value: typography_gp54l824,
            onChange: (value) => setAttributes({ typography_gp54l824: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_wrjl2324,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_wrjl2324: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_wiclqb24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_wiclqb24: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Button"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_ai6uxe24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_ai6uxe24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_5owrw424,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_5owrw424: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Width`),
            separator:'default',
            value: slider_vsvkp425,
            
            min: slider_vsvkp425 && slider_vsvkp425.unit ? slider_vsvkp425Function(slider_vsvkp425.unit, 'min') : 0,
            max: slider_vsvkp425 && slider_vsvkp425.unit ? slider_vsvkp425Function(slider_vsvkp425.unit, 'max') : 100,
            step: slider_vsvkp425 && slider_vsvkp425.unit ? slider_vsvkp425Function(slider_vsvkp425.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_vsvkp425: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_0di8a525,
            
            min: slider_0di8a525 && slider_0di8a525.unit ? slider_0di8a525Function(slider_0di8a525.unit, 'min') : 0,
            max: slider_0di8a525 && slider_0di8a525.unit ? slider_0di8a525Function(slider_0di8a525.unit, 'max') : 100,
            step: slider_0di8a525 && slider_0di8a525.unit ? slider_0di8a525Function(slider_0di8a525.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_0di8a525: value }),
            }), 
 React.createElement(Pmgc_Typography, {
            
            value: typography_dtc79d24,
            onChange: (value) => setAttributes({ typography_dtc79d24: value }),
            separator:'default',
            
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_dul1x924,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_dul1x924: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_m90yb124,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_m90yb124: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_c2a5zz25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_c2a5zz25: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_zpk2vl24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_zpk2vl24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_590ulz25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_590ulz25: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Text Color`),
            value: color_1yrvb424,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_1yrvb424: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_ez26kn24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_ez26kn24: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_ifpsy925,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_ifpsy925: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_plgzuf25,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_plgzuf25: value }),
            }), 
), 
), 
),( select_zf67du24 == "style-1" || select_zf67du24 == "style-3" || select_zf67du24 == "style-4" ) && React.createElement(PanelBody, { title: __("Navigation Icon"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_53il9m24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_53il9m24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Icon Gap`),
            separator:'default',
            value: slider_8okhk024,
            
            min: slider_8okhk024 && slider_8okhk024.unit ? slider_8okhk024Function(slider_8okhk024.unit, 'min') : 0,
            max: slider_8okhk024 && slider_8okhk024.unit ? slider_8okhk024Function(slider_8okhk024.unit, 'max') : 100,
            step: slider_8okhk024 && slider_8okhk024.unit ? slider_8okhk024Function(slider_8okhk024.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_8okhk024: value }),
            }), 
 React.createElement(Pmgc_Range, {
            label: __(`Icon Size`),
            separator:'default',
            value: slider_bsk1hb24,
            
            min: slider_bsk1hb24 && slider_bsk1hb24.unit ? slider_bsk1hb24Function(slider_bsk1hb24.unit, 'min') : 0,
            max: slider_bsk1hb24 && slider_bsk1hb24.unit ? slider_bsk1hb24Function(slider_bsk1hb24.unit, 'max') : 100,
            step: slider_bsk1hb24 && slider_bsk1hb24.unit ? slider_bsk1hb24Function(slider_bsk1hb24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_bsk1hb24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_74gcj224,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_74gcj224: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_92d34g24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_92d34g24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Color, {
            label: __(`Icon Color`),
            value: color_5aau1a24,
            disableAlpha: false,
            disableGlobal: false,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ color_5aau1a24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_5itug524,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_5itug524: value }),
            }), 
), 
), 
), React.createElement(PanelBody, { title: __("Background"), initialOpen: false },
 React.createElement(Pmgc_Dimension, {
            label: __(`Padding`),
            value: dimension_j7md5125,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_j7md5125: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Height`),
            separator:'default',
            value: slider_yj60wm24,
            
            min: slider_yj60wm24 && slider_yj60wm24.unit ? slider_yj60wm24Function(slider_yj60wm24.unit, 'min') : 0,
            max: slider_yj60wm24 && slider_yj60wm24.unit ? slider_yj60wm24Function(slider_yj60wm24.unit, 'max') : 100,
            step: slider_yj60wm24 && slider_yj60wm24.unit ? slider_yj60wm24Function(slider_yj60wm24.unit, 'step') : 1,
            
                unit: ['px', '%', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_yj60wm24: value }),
            }), 
 React.createElement(Pmgc_Background, {
            
            value: background_u4h1kb24,
            sources: ["color","image","gradient"],
            separator:'default',
            
            onChange: (value) => setAttributes({ background_u4h1kb24: value }),
            }), 
 React.createElement(Pmgc_Tabs, null, React.createElement(Pmgc_Tab, {
                                tabTitle: __('Normal')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_57fid325,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_57fid325: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_fjv6nk24,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_fjv6nk24: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_04x5yu24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_04x5yu24: value }),
            }), 
), 
React.createElement(Pmgc_Tab, {
                                tabTitle: __('Hover')
                            },  React.createElement(Pmgc_Border, {
            
            value: border_772iem25,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_772iem25: value }),
            }), 
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_10n2cg24,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_10n2cg24: value }),
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
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-nzfoy625", block_id, false, props.clientId);
                }
            }

            
let g_select_zf67du24 = select_zf67du24 && select_zf67du24 != undefined  ? select_zf67du24 : "";
let g_rawhtml_l32l8b25 = rawhtml_l32l8b25 && rawhtml_l32l8b25 != undefined  ? rawhtml_l32l8b25 : "";
let g_text_lx4byh24 = text_lx4byh24 && text_lx4byh24 != undefined  ? text_lx4byh24 : "";
let g_url_2ncmwl24_url = url_2ncmwl24?.url && url_2ncmwl24?.url != undefined ? url_2ncmwl24.url : "";
let g_url_2ncmwl24_target = url_2ncmwl24?.target && url_2ncmwl24?.target != undefined ? url_2ncmwl24.target : "";
let g_url_2ncmwl24_nofollow = url_2ncmwl24?.nofollow && url_2ncmwl24?.nofollow != undefined ? url_2ncmwl24.nofollow : "";
let g_url_2ncmwl24_ctmArt = url_2ncmwl24?.attr != undefined ? url_2ncmwl24.attr : "";
                    let g_url_2ncmwl24_attr = ''

                    if (g_url_2ncmwl24_ctmArt) {
                        let main_array = g_url_2ncmwl24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_2ncmwl24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_41jkqf24 = iconscontrol_41jkqf24 != undefined && ( (select_zf67du24 == "style-1")||(select_zf67du24 == "style-3")||(select_zf67du24 == "style-4") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_41jkqf24+'"></i></span>' : '';

let g_iconscontrol_91o61124 = iconscontrol_91o61124 != undefined && ( (select_zf67du24 == "style-1")||(select_zf67du24 == "style-3")||(select_zf67du24 == "style-4") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_91o61124+'"></i></span>' : '';

let g_rawhtml_xig5cz25 = rawhtml_xig5cz25 && rawhtml_xig5cz25 != undefined  ? rawhtml_xig5cz25 : "";
let g_typography_gp54l824 = typography_gp54l824 && typography_gp54l824 != undefined  ? typography_gp54l824 : "";
let g_color_wrjl2324 = color_wrjl2324 && color_wrjl2324 != undefined  ? color_wrjl2324 : "";
let g_color_wiclqb24 = color_wiclqb24 && color_wiclqb24 != undefined  ? color_wiclqb24 : "";
let g_dimension_ai6uxe24 = dimension_ai6uxe24 && dimension_ai6uxe24 != undefined  ? dimension_ai6uxe24 : "";
let g_dimension_5owrw424 = dimension_5owrw424 && dimension_5owrw424 != undefined  ? dimension_5owrw424 : "";
let g_typography_dtc79d24 = typography_dtc79d24 && typography_dtc79d24 != undefined  ? typography_dtc79d24 : "";
let g_color_dul1x924 = color_dul1x924 && color_dul1x924 != undefined  ? color_dul1x924 : "";
let g_background_m90yb124 = background_m90yb124 && background_m90yb124 != undefined  ? background_m90yb124 : "";
let g_color_1yrvb424 = color_1yrvb424 && color_1yrvb424 != undefined  ? color_1yrvb424 : "";
let g_background_ez26kn24 = background_ez26kn24 && background_ez26kn24 != undefined  ? background_ez26kn24 : "";
let g_border_c2a5zz25 = border_c2a5zz25 && border_c2a5zz25 != undefined  ? border_c2a5zz25 : "";
let g_dimension_zpk2vl24 = dimension_zpk2vl24 && dimension_zpk2vl24 != undefined  ? dimension_zpk2vl24 : "";
let g_border_ifpsy925 = border_ifpsy925 && border_ifpsy925 != undefined  ? border_ifpsy925 : "";
let g_boxshadow_590ulz25 = boxshadow_590ulz25 && boxshadow_590ulz25 != undefined  ? boxshadow_590ulz25 : "";
let g_boxshadow_plgzuf25 = boxshadow_plgzuf25 && boxshadow_plgzuf25 != undefined  ? boxshadow_plgzuf25 : "";
let g_dimension_53il9m24 = dimension_53il9m24 && dimension_53il9m24 != undefined  ? dimension_53il9m24 : "";
let g_color_74gcj224 = color_74gcj224 && color_74gcj224 != undefined  ? color_74gcj224 : "";
let g_background_92d34g24 = background_92d34g24 && background_92d34g24 != undefined  ? background_92d34g24 : "";
let g_color_5aau1a24 = color_5aau1a24 && color_5aau1a24 != undefined  ? color_5aau1a24 : "";
let g_background_5itug524 = background_5itug524 && background_5itug524 != undefined  ? background_5itug524 : "";
let g_dimension_j7md5125 = dimension_j7md5125 && dimension_j7md5125 != undefined  ? dimension_j7md5125 : "";
let g_background_u4h1kb24 = background_u4h1kb24 && background_u4h1kb24 != undefined  ? background_u4h1kb24 : "";
let g_border_57fid325 = border_57fid325 && border_57fid325 != undefined  ? border_57fid325 : "";
let g_dimension_fjv6nk24 = dimension_fjv6nk24 && dimension_fjv6nk24 != undefined  ? dimension_fjv6nk24 : "";
let g_border_772iem25 = border_772iem25 && border_772iem25 != undefined  ? border_772iem25 : "";
let g_boxshadow_04x5yu24 = boxshadow_04x5yu24 && boxshadow_04x5yu24 != undefined  ? boxshadow_04x5yu24 : "";
let g_boxshadow_10n2cg24 = boxshadow_10n2cg24 && boxshadow_10n2cg24 != undefined  ? boxshadow_10n2cg24 : "";
            let cpt_wblbl324_al25 = '';
                                if(cpt_wblbl324){

                                    cpt_wblbl324_st?.length > 0 && cpt_wblbl324_st.map((cpt_item, index) => {
                                        let cpt_val = GetCPT(cpt_item);
                                        
                                        
                                        cpt_wblbl324_al25 += `<div class="{loop-class-cpt} w-pntslider-content" data-cpt_wblbl324="{cpt_wblbl324}">
                <a class="wkit-post-slider-name" href="${cpt_val?.post_link}" rel="noopener">${cpt_val?.title} </a>
            </div>`;
                                    });
                                }
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_nzfoy625 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-news-ticker-container" data-max="{{max_post}}">
    <div class="wkit-pnt-main-container w-pntslider-${g_select_zf67du24}" data-select="${g_select_zf67du24}">
        <div class="wkit-pnt-news">
            <a href="${g_url_2ncmwl24_url}" target="${g_url_2ncmwl24_target}" rel="noopener">${g_text_lx4byh24}</a>
        </div>
        <div class="w-slider-pnt">
            ${cpt_wblbl324_al25}

        </div>
        <div class="w-pnt-button">
            <div class="w-pnt-prevBtn">${g_iconscontrol_41jkqf24}</div>
            <div class="w-pnt-nextBtn">${g_iconscontrol_91o61124}</div>
        </div>
    </div>
</div>`
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
            select_zf67du24,
include_wblbl324,
exclude_wblbl324,
max_post_wblbl324,
order_wblbl324,
order_by_wblbl324,
max_cat_wblbl324,
max_tag_wblbl324,
cpt_wblbl324,
rawhtml_l32l8b25,
text_lx4byh24,
url_2ncmwl24,
iconscontrol_41jkqf24,
iconscontrol_91o61124,
rawhtml_xig5cz25,
typography_gp54l824,
color_wrjl2324,
color_wiclqb24,
normalhover_yai7q224,
dimension_ai6uxe24,
dimension_5owrw424,
slider_vsvkp425,
slider_0di8a525,
typography_dtc79d24,
color_dul1x924,
background_m90yb124,
color_1yrvb424,
background_ez26kn24,
border_c2a5zz25,
dimension_zpk2vl24,
border_ifpsy925,
boxshadow_590ulz25,
boxshadow_plgzuf25,
normalhover_8k8h5c24,
dimension_53il9m24,
slider_8okhk024,
slider_bsk1hb24,
color_74gcj224,
background_92d34g24,
color_5aau1a24,
background_5itug524,
normalhover_1heid124,
dimension_j7md5125,
slider_yj60wm24,
background_u4h1kb24,
border_57fid325,
dimension_fjv6nk24,
border_772iem25,
boxshadow_04x5yu24,
boxshadow_10n2cg24,
normalhover_679qm124,

            block_id,
        } = attributes;

        

        const CPT_array = (f_data) => {

                let query = {
                    per_page: f_data.max_post,
                    order: f_data.order,
                    offset: 0,
                    status: 'publish',
                    _embed: true,
                };

                if (f_data?.order_by && f_data?.order_by != 'none') {
                    query = Object.assign({}, query, { 'orderby': f_data.order_by })
                }

                if (f_data.include) {
                    let in_array = f_data.include.split(',');
                    query = Object.assign({}, query, { 'include': in_array })
                }

                if (f_data.exclude) {
                    let in_array = f_data.exclude.split(',');
                    query = Object.assign({}, query, { 'exclude': in_array })
                }

                let data = select('core').getEntityRecords('postType', f_data.type, query) || [];

                return data;
            }
                
            const GetCPT = (data) => {
                let cpt_obj = {
                    'title': data?.title?.rendered ? data.title.rendered : '',
                    'description': data?.excerpt?.rendered ? data.excerpt.rendered : '',
                    'thumbnail': data._embedded?.['wp:featuredmedia']?.[0]?.source_url ? data._embedded['wp:featuredmedia'][0].source_url : 'https://j-j-labs.com/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                    'post_link': data?.link ? data.link : '',
                    'post_date': data?.date ? data.date : '',
                    'auth_name': data?._embedded?.author?.[0]?.name ? data._embedded.author[0].name : '',
                    'auth_id': data?._embedded?.author?.[0]?.id ? data._embedded.author[0].id : '',
                    'auth_profile': data?._embedded?.author?.[0]?.avatar_urls?.['48'] ? data._embedded.author[0].avatar_urls['48'] : 'https://j-j-labs.com/wp-content/plugins/wdesignkit/assets/images/jpg/placeholder.png',
                    'auth_url': data?._embedded?.author?.[0]?.link ? data?._embedded.author[0].link : '',
                    'catrgory_list': data?._embedded?.['wp:term']?.[0] ? data._embedded['wp:term'][0] : [],
                    'tag_list': data?._embedded?.['wp:term']?.[1] ? data._embedded['wp:term'][1] : [],
                };

                return cpt_obj;
            }

        
                const cpt_wblbl324_flt = {
                    type: cpt_wblbl324,
                    include: include_wblbl324,
                    exclude: exclude_wblbl324,
                    max_post: max_post_wblbl324,
                    order: order_wblbl324,
                    order_by: order_by_wblbl324,
                    max_cat: max_cat_wblbl324,
                    max_tag: max_tag_wblbl324,
                };
            
            const cpt_wblbl324_st = CPT_array(cpt_wblbl324_flt);

        
let g_select_zf67du24 = select_zf67du24 && select_zf67du24 != undefined  ? select_zf67du24 : "";
let g_rawhtml_l32l8b25 = rawhtml_l32l8b25 && rawhtml_l32l8b25 != undefined  ? rawhtml_l32l8b25 : "";
let g_text_lx4byh24 = text_lx4byh24 && text_lx4byh24 != undefined  ? text_lx4byh24 : "";
let g_url_2ncmwl24_url = url_2ncmwl24?.url && url_2ncmwl24?.url != undefined ? url_2ncmwl24.url : "";
let g_url_2ncmwl24_target = url_2ncmwl24?.target && url_2ncmwl24?.target != undefined ? url_2ncmwl24.target : "";
let g_url_2ncmwl24_nofollow = url_2ncmwl24?.nofollow && url_2ncmwl24?.nofollow != undefined ? url_2ncmwl24.nofollow : "";
let g_url_2ncmwl24_ctmArt = url_2ncmwl24?.attr != undefined ? url_2ncmwl24.attr : "";
                    let g_url_2ncmwl24_attr = ''

                    if (g_url_2ncmwl24_ctmArt) {
                        let main_array = g_url_2ncmwl24_ctmArt.split(',');
                        main_array?.length > 0 && main_array.map((atr) => {
                            if(atr){
                                let sub_array = atr.split("|");
                                g_url_2ncmwl24_attr += (sub_array[0]?.trim() ? sub_array[0]?.trim() : '') + "='" + (sub_array[1]?.trim() ? sub_array[1]?.trim() : '') + "' ";
                            }
                        })
                    }
let g_iconscontrol_41jkqf24 = iconscontrol_41jkqf24 != undefined && ( (select_zf67du24 == "style-1")||(select_zf67du24 == "style-3")||(select_zf67du24 == "style-4") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_41jkqf24+'"></i></span>' : '';

let g_iconscontrol_91o61124 = iconscontrol_91o61124 != undefined && ( (select_zf67du24 == "style-1")||(select_zf67du24 == "style-3")||(select_zf67du24 == "style-4") ) ? '<span class="tp-title-icon"><i class="'+iconscontrol_91o61124+'"></i></span>' : '';

let g_rawhtml_xig5cz25 = rawhtml_xig5cz25 && rawhtml_xig5cz25 != undefined  ? rawhtml_xig5cz25 : "";
let g_typography_gp54l824 = typography_gp54l824 && typography_gp54l824 != undefined  ? typography_gp54l824 : "";
let g_color_wrjl2324 = color_wrjl2324 && color_wrjl2324 != undefined  ? color_wrjl2324 : "";
let g_color_wiclqb24 = color_wiclqb24 && color_wiclqb24 != undefined  ? color_wiclqb24 : "";
let g_dimension_ai6uxe24 = dimension_ai6uxe24 && dimension_ai6uxe24 != undefined  ? dimension_ai6uxe24 : "";
let g_dimension_5owrw424 = dimension_5owrw424 && dimension_5owrw424 != undefined  ? dimension_5owrw424 : "";
let g_typography_dtc79d24 = typography_dtc79d24 && typography_dtc79d24 != undefined  ? typography_dtc79d24 : "";
let g_color_dul1x924 = color_dul1x924 && color_dul1x924 != undefined  ? color_dul1x924 : "";
let g_background_m90yb124 = background_m90yb124 && background_m90yb124 != undefined  ? background_m90yb124 : "";
let g_color_1yrvb424 = color_1yrvb424 && color_1yrvb424 != undefined  ? color_1yrvb424 : "";
let g_background_ez26kn24 = background_ez26kn24 && background_ez26kn24 != undefined  ? background_ez26kn24 : "";
let g_border_c2a5zz25 = border_c2a5zz25 && border_c2a5zz25 != undefined  ? border_c2a5zz25 : "";
let g_dimension_zpk2vl24 = dimension_zpk2vl24 && dimension_zpk2vl24 != undefined  ? dimension_zpk2vl24 : "";
let g_border_ifpsy925 = border_ifpsy925 && border_ifpsy925 != undefined  ? border_ifpsy925 : "";
let g_boxshadow_590ulz25 = boxshadow_590ulz25 && boxshadow_590ulz25 != undefined  ? boxshadow_590ulz25 : "";
let g_boxshadow_plgzuf25 = boxshadow_plgzuf25 && boxshadow_plgzuf25 != undefined  ? boxshadow_plgzuf25 : "";
let g_dimension_53il9m24 = dimension_53il9m24 && dimension_53il9m24 != undefined  ? dimension_53il9m24 : "";
let g_color_74gcj224 = color_74gcj224 && color_74gcj224 != undefined  ? color_74gcj224 : "";
let g_background_92d34g24 = background_92d34g24 && background_92d34g24 != undefined  ? background_92d34g24 : "";
let g_color_5aau1a24 = color_5aau1a24 && color_5aau1a24 != undefined  ? color_5aau1a24 : "";
let g_background_5itug524 = background_5itug524 && background_5itug524 != undefined  ? background_5itug524 : "";
let g_dimension_j7md5125 = dimension_j7md5125 && dimension_j7md5125 != undefined  ? dimension_j7md5125 : "";
let g_background_u4h1kb24 = background_u4h1kb24 && background_u4h1kb24 != undefined  ? background_u4h1kb24 : "";
let g_border_57fid325 = border_57fid325 && border_57fid325 != undefined  ? border_57fid325 : "";
let g_dimension_fjv6nk24 = dimension_fjv6nk24 && dimension_fjv6nk24 != undefined  ? dimension_fjv6nk24 : "";
let g_border_772iem25 = border_772iem25 && border_772iem25 != undefined  ? border_772iem25 : "";
let g_boxshadow_04x5yu24 = boxshadow_04x5yu24 && boxshadow_04x5yu24 != undefined  ? boxshadow_04x5yu24 : "";
let g_boxshadow_10n2cg24 = boxshadow_10n2cg24 && boxshadow_10n2cg24 != undefined  ? boxshadow_10n2cg24 : "";
        let cpt_wblbl324_al25 = '';
                                if(cpt_wblbl324){

                                    cpt_wblbl324_st?.length > 0 && cpt_wblbl324_st.map((cpt_item, index) => {
                                        let cpt_val = GetCPT(cpt_item);
                                        
                                        
                                        cpt_wblbl324_al25 += `<div class="{loop-class-cpt} w-pntslider-content" data-cpt_wblbl324="{cpt_wblbl324}">
                <a class="wkit-post-slider-name" href="${cpt_val?.post_link}" rel="noopener">${cpt_val?.title} </a>
            </div>`;
                                    });
                                }

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-nzfoy625", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_nzfoy625 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-news-ticker-container" data-max="{{max_post}}">
    <div class="wkit-pnt-main-container w-pntslider-${g_select_zf67du24}" data-select="${g_select_zf67du24}">
        <div class="wkit-pnt-news">
            <a href="${g_url_2ncmwl24_url}" target="${g_url_2ncmwl24_target}" rel="noopener">${g_text_lx4byh24}</a>
        </div>
        <div class="w-slider-pnt">
            ${cpt_wblbl324_al25}

        </div>
        <div class="w-pnt-button">
            <div class="w-pnt-prevBtn">${g_iconscontrol_41jkqf24}</div>
            <div class="w-pnt-nextBtn">${g_iconscontrol_91o61124}</div>
        </div>
    </div>
</div>`
                    }

                }),
                /*#__PURE__*/React.createElement("style", null, styleCss)
            )
        );
       },
   });
}
}
    new Post_News_Ticker_nzfoy625();