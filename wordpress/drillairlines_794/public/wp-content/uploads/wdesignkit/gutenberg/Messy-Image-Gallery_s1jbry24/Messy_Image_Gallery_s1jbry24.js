
    class Messy_Image_Gallery_s1jbry24 {
        constructor() {
            this.Messy_Image_Gallery_s1jbry24_ns813b25();
        }
    
        Messy_Image_Gallery_s1jbry24_ns813b25() {
    const { useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
   
    const {
        PanelBody,
        
    } = wp.components;

    
   
    const {
       Pmgc_PanelTabs,
       Pmgc_Tab,
       Pmgc_Media,Pmgc_Text,Pmgc_Select,Pmgc_Dimension,Pmgc_Range,Pmgc_Border,Pmgc_BoxShadow,
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
   
    registerBlockType('wdkit/wb-s1jbry24', {
        title: __('Messy Image Gallery'), // Block title.
        description: __(''),
        icon: React.createElement("i", {
                class: "far fa-images tpae-wdkit-logo",
                style: { fontSize: "20px" }
        }),
        category: 'Wkit Builder',
        keywords: [__('Image Gallery'),__('Ecommerce'),__('Photography'),],
   
        edit: (props) => {
        const [device, setDevice] = useState('');
        
        

        

        

        

        const slider_yby6w923Function = (unit, type) => {
                var g_slider_yby6w923_list = [];
                g_slider_yby6w923_list['px'] = { "type": "px", "min": 0, "max": 500, "step": 1 };
g_slider_yby6w923_list['%'] = { "type": "%", "min": 0, "max": 100, "step": 1 };
g_slider_yby6w923_list['em'] = { "type": "em", "min": 0, "max": 100, "step": 1 };
g_slider_yby6w923_list['rem'] = { "type": "rem", "min": 0, "max": 10, "step": 1 };
g_slider_yby6w923_list['deg'] = { "type": "deg", "min": 0, "max": 10, "step": 1 };
g_slider_yby6w923_list['vh'] = { "type": "vh", "min": 0, "max": 10, "step": 1 };

                return g_slider_yby6w923_list[unit][type];
            };

   
            const {
               isSelected,
               attributes,
               setAttributes,
            } = props;
   
            const {
               gallery_akrzzp23,
number_vyg7w323,
number_aavqr223,
number_6lz7rh23,
select_qw8bw523,
number_fzoo2323,
number_bbcfm423,
number_d461vm23,
number_1u0iw823,
number_zl0c0t23,
number_xfs7ml23,
dimension_bytsh423,
slider_yby6w923,
border_so1gec23,
dimension_i8dqlc23,
boxshadow_cg04l323,

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
                        main_function_s1jbry24(jQuery('.wdkit-block-' + block_id))
                    }
                }, 1500);
            }, [attributes])

            const main_function_s1jbry24 = ($scope) => {
                let is_editable = wp?.blocks ? true : false;
                const s = (t,e,i,r,n)=>(t - e) * (n - r) / (i - e) + r
  , a = (t,e,i=80,r=500)=>{
    const n = t.offsetLeft + t.offsetWidth / 2
      , a = t.offsetTop + t.offsetHeight / 2
      , h = e.offsetLeft + e.offsetWidth / 2
      , u = e.offsetTop + e.offsetHeight / 2;
    i = Math.max(s(o(t, e), 0, r, i, 0), 0);
    const l = Math.atan2(Math.abs(u - a), Math.abs(h - n));
    let f = Math.abs(Math.cos(l) * i)
      , p = Math.abs(Math.sin(l) * i);
    return {
        x: n < h ? -1 * f : f,
        y: a < u ? -1 * p : p
    } 
}
  , o = (t,e)=>{
    const i = t.offsetLeft + t.offsetWidth / 2
      , r = t.offsetTop + t.offsetHeight / 2
      , n = e.offsetLeft + e.offsetWidth / 2
      , s = e.offsetTop + e.offsetHeight / 2;
    return Math.hypot(i - n, r - s)
}
;
function h(t, e, i) {
    return e in t ? Object.defineProperty(t, e, {
        value: i,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[e] = i,
    t
}

class Nr {
    constructor(t) {
        h(this, "DOM", {
            el: null,
            image: null
        }),
        this.DOM.el = t,
        this.DOM.image = this.DOM.el.querySelector(".wkit-widget-item-img")
    }
}
class Yr {
    initEvents() {
        for (const t of this.items)
            t.DOM.el.addEventListener("click", (()=>this.expand(t)))
    }
    expand(t) {
        this.tl && this.tl.kill();
        const e = this.items.indexOf(t);
        if (this.previousExpanded = -1 !== this.expanded && this.expanded !== e ? this.expanded : -1,
        this.expanded = this.expanded === e ? -1 : e,
        this.tl = gsap.timeline({
            defaults: {
                duration: this.options.duration,
                ease: this.options.ease
            }
        }).addLabel("start", 0).addLabel("end", this.options.duration).set(t.DOM.el, {
            zIndex: -1 === this.expanded ? 1 : 999
        }, -1 === this.expanded ? "end" : "start"),
        this.options.skew ? this.tl.to(t.DOM.el, {
            duration: .4 * this.options.duration,
            ease: "sine.in",
            scale: 1 + (this.options.scale - 1) / 2,
            skewX: -1 === this.expanded ? -1 * this.options.skew : this.options.skew,
            skewY: -1 === this.expanded ? -1 * this.options.skew : this.options.skew,
            x: 0,
            y: 0,
            rotation: 0
        }, "start").to(t.DOM.el, {
            duration: .6 * this.options.duration,
            ease: "power4",
            scale: -1 === this.expanded ? 1 : this.options.scale,
            skewX: 0,
            skewY: 0
        }, "start+=" + .4 * this.options.duration) : this.tl.to(t.DOM.el, {
            scale: -1 === this.expanded ? 1 : this.options.scale,
            x: 0,
            y: 0,
            rotation: 0
        }, "start"),
        -1 !== this.previousExpanded) {
            const t = this.items[this.previousExpanded]
              , e = 0;
            this.tl.set(t.DOM.el, {
                zIndex: 1,
                delay: e
            }, "start").to(t.DOM.el, {
                scale: 1,
                x: 0,
                y: 0,
                rotation: 0,
                delay: e
            }, "start")
        }
        const i = this.items.filter((e=>e != t));
        for (let e of i) {
            const {x: i, y: r} = -1 === this.expanded ? {
                x: 0,
                y: 0
            } : a(e.DOM.el, t.DOM.el, this.options.spread, this.options.maxDistance)
              , n = 0
              , h = Math.round(s(o(e.DOM.el, t.DOM.el), 0, 1e5, 998, 1))
              , u = this.options.maxRotation ? Math.max(Math.round(s(o(e.DOM.el, t.DOM.el), 0, 500, this.options.maxRotation, 0)), 0) : 0;
            this.tl.set(e.DOM.el, {
                zIndex: -1 === this.expanded ? 1 : h,
                delay: n
            }, -1 === this.expanded ? "end" : "start").to(e.DOM.el, {
                x: i,
                y: r,
                rotation: -1 === this.expanded ? 0 : gsap.utils.random(-1 * u, u),
                delay: n
            }, "start")
        }
    }
    constructor(t) {
        h(this, "DOM", {
            el: null,
            items: null
        }),
        h(this, "expanded", -1),
        h(this, "previousExpanded", -1),
        this.DOM.el = t,
        this.DOM.items = [...this.DOM.el.querySelectorAll(".wkit-widget-gallery-item")],
        this.items = [],
        this.DOM.items.forEach((t=>this.items.push(new Nr(t)))),
        this.options = {},
        this.options.duration = Number(this.DOM.el.dataset.duration) || .8,
        this.options.ease = this.DOM.el.dataset.ease || "power4",
        this.options.scale = Number(this.DOM.el.dataset.scale) || 2,
        this.options.skew = Number(this.DOM.el.dataset.skew) || 0,
        this.options.maxRotation = Number(this.DOM.el.dataset.maxRotation) || 0,
        this.options.spread = Number(this.DOM.el.dataset.spread) || 80,
        this.options.maxDistance = Number(this.DOM.el.dataset.maxDistance) || 500,
        this.initEvents()
    }
}
let allML = document.querySelectorAll(".wkit-image-grid-wrap"); 
if(allML){
    allML.forEach((ml)=>{
            ml.parentElement.style.width = '100%';
        let deskCol = (ml.getAttribute('data-deskcol')) ? Number(ml.getAttribute('data-deskcol')) : 10,
            tabCol = (ml.getAttribute('data-tabcol')) ? Number(ml.getAttribute('data-tabcol')) : 6,
            mobCol = (ml.getAttribute('data-mobcol')) ? Number(ml.getAttribute('data-mobcol')) : 3;
        let screenWidth = screen.width;
        if(screenWidth >= 1024){
            ml.style.cssText = "grid-template-columns: repeat("+deskCol+",1fr)";
        }else if(screenWidth < 1024 && screenWidth >= 768){
            ml.style.cssText = "grid-template-columns: repeat("+tabCol+",1fr)";
        }else if(screenWidth < 768){
            setTimeout(()=>{
                let offHeight = ml.offsetHeight;
                ml.style.height = offHeight+"px";
            }, 1000);
            ml.style.cssText = "grid-template-columns: repeat("+mobCol+",1fr)";
        }
        new Yr(ml)
    })
}
            }
   
            const inspectorControls = (isSelected && (React.createElement(InspectorControls, null,
                React.createElement(Fragment, null,
                    React.createElement(Pmgc_PanelTabs, null,
                        React.createElement(Pmgc_Tab, { tabTitle: __("Layout") },
                            React.createElement(PanelBody, { title: __("Image"), initialOpen: true },
 React.createElement(Pmgc_Media, {
            label: __(`Gallery`),
            
            
            multiple: true,
            type: ['image'],
            separator:'default',
            value: gallery_akrzzp23,
            panel: true,
            inlineblock:false,
            onChange: (value) => setAttributes({ gallery_akrzzp23: value }),
            }), 
 React.createElement(Pmgc_Text, {
                label: __(`Desktop Column`),
                type: "number",
                value: number_vyg7w323,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_vyg7w323: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Tablet Column`),
                type: "number",
                value: number_aavqr223,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_aavqr223: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Mobile Column`),
                type: "number",
                value: number_6lz7rh23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_6lz7rh23: value }) },
            }),
), React.createElement(PanelBody, { title: __("Animation"), initialOpen: false },
 React.createElement(Pmgc_Select, {
                label: __(`Transition`),
                options:[['none',__('Default')],['power1',__('power1')],['power2',__('power2')],['power3',__('power3')],['back',__('back')],['elastic',__('elastic')],['bounce',__('bounce')],['rough',__('rough')],['slow',__('slow')],['steps',__('steps')],['circ',__('circ')],['expo',__('expo')],['sine',__('sine')],],
                separator:"default",
                
                
                value: select_qw8bw523,
                onChange: (value) => {setAttributes({ select_qw8bw523: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Duration`),
                type: "number",
                value: number_fzoo2323,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_fzoo2323: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Skew`),
                type: "number",
                value: number_bbcfm423,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_bbcfm423: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Scale`),
                type: "number",
                value: number_d461vm23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_d461vm23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Rotation`),
                type: "number",
                value: number_1u0iw823,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_1u0iw823: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Spread`),
                type: "number",
                value: number_zl0c0t23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_zl0c0t23: value }) },
            }),
 React.createElement(Pmgc_Text, {
                label: __(`Distance`),
                type: "number",
                value: number_xfs7ml23,
                
                
                
                separator:"default",
                
                onChange: (value) => {setAttributes({ number_xfs7ml23: value }) },
            }),
),
                        ),
                        React.createElement(Pmgc_Tab, { tabTitle: __("Style") },
                            React.createElement(PanelBody, { title: __("Image"), initialOpen: true },
 React.createElement(Pmgc_Dimension, {
            label: __(`Margin`),
            value: dimension_bytsh423,
            noLock: false,
            unit: ['px','%','em',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_bytsh423: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_Range, {
            label: __(`Gap`),
            separator:'default',
            value: slider_yby6w923,
            
            min: slider_yby6w923 && slider_yby6w923.unit ? slider_yby6w923Function(slider_yby6w923.unit, 'min') : 0,
            max: slider_yby6w923 && slider_yby6w923.unit ? slider_yby6w923Function(slider_yby6w923.unit, 'max') : 100,
            step: slider_yby6w923 && slider_yby6w923.unit ? slider_yby6w923Function(slider_yby6w923.unit, 'step') : 1,
            
                unit: ['px', '%', 'em', 'rem', 'deg', 'vh', ],
                responsive: true,
                device: device,
                onDeviceChange: (value) => this.setState({ device: value }),
            onChange: (value) => setAttributes({ slider_yby6w923: value }),
            }), 
 React.createElement(Pmgc_Border, {
            
            value: border_so1gec23,
            separator:"default",
            
            
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
            onChange: (value) => setAttributes({ border_so1gec23: value }),
            }), 
 React.createElement(Pmgc_Dimension, {
            label: __(`Border Radius`),
            value: dimension_i8dqlc23,
            noLock: false,
            unit: ['px','%',],
            separator:"default",
            
            inlineblock:false,
            onChange: (value) => setAttributes({ dimension_i8dqlc23: value }),
            responsive: true,
            device: device,
            onDeviceChange: (value) => setDevice( value ),
        }),
 React.createElement(Pmgc_BoxShadow, {
            
            value: boxshadow_cg04l323,
            separator:"default",
            
            
            onChange: (value) => setAttributes({ boxshadow_cg04l323: value }),
            }), 
),
                        ),
                    )
                )
            )));

            if (props.attributes.block_id) {
                var element = document.getElementsByClassName("tpgb-block-" + block_id)
                if (null != element && "undefined" != typeof element) {
                    Pmgc_CssGenerator(props.attributes, 'wdkit', "wb-s1jbry24", block_id, false, props.clientId);
                }
            }

            
let g_number_vyg7w323 = number_vyg7w323 && number_vyg7w323 != undefined  ? number_vyg7w323 : "";
let g_number_aavqr223 = number_aavqr223 && number_aavqr223 != undefined  ? number_aavqr223 : "";
let g_number_6lz7rh23 = number_6lz7rh23 && number_6lz7rh23 != undefined  ? number_6lz7rh23 : "";
let g_select_qw8bw523 = select_qw8bw523 && select_qw8bw523 != undefined  ? select_qw8bw523 : "";
let g_number_fzoo2323 = number_fzoo2323 && number_fzoo2323 != undefined  ? number_fzoo2323 : "";
let g_number_bbcfm423 = number_bbcfm423 && number_bbcfm423 != undefined  ? number_bbcfm423 : "";
let g_number_d461vm23 = number_d461vm23 && number_d461vm23 != undefined  ? number_d461vm23 : "";
let g_number_1u0iw823 = number_1u0iw823 && number_1u0iw823 != undefined  ? number_1u0iw823 : "";
let g_number_zl0c0t23 = number_zl0c0t23 && number_zl0c0t23 != undefined  ? number_zl0c0t23 : "";
let g_number_xfs7ml23 = number_xfs7ml23 && number_xfs7ml23 != undefined  ? number_xfs7ml23 : "";
let g_dimension_bytsh423 = dimension_bytsh423 && dimension_bytsh423 != undefined  ? dimension_bytsh423 : "";
let g_border_so1gec23 = border_so1gec23 && border_so1gec23 != undefined  ? border_so1gec23 : "";
let g_dimension_i8dqlc23 = dimension_i8dqlc23 && dimension_i8dqlc23 != undefined  ? dimension_i8dqlc23 : "";
let g_boxshadow_cg04l323 = boxshadow_cg04l323 && boxshadow_cg04l323 != undefined  ? boxshadow_cg04l323 : "";
let gallery_akrzzp23_7925 = '';
                                gallery_akrzzp23  && gallery_akrzzp23.map((g_image, index) => {
                                    gallery_akrzzp23_7925 += `<div class="wkit-widget-gallery-item {loop-class}" data-gallery_akrzzp23="{gallery_akrzzp23}">
        <div class="wkit-widget-item-img" style="background-image:url(${g_image.url})"></div>
    </div>`;
                                })
            
   
            return (
                React.createElement(Fragment, null, inspectorControls,
                    wp.element.createElement("div", {
                    class: "wkit-wb-Widget_s1jbry24 wdkit-block-"+block_id+"",
                        dangerouslySetInnerHTML: {
                            __html: `<div class="wkit-image-grid-wrap" data-deskcol="${g_number_vyg7w323}" data-tabcol="${g_number_aavqr223}" data-mobcol="${g_number_6lz7rh23}" data-duration="${g_number_fzoo2323}" data-ease="${g_select_qw8bw523}" data-skew="${g_number_bbcfm423}" data-scale="${g_number_d461vm23}" data-max-rotation="${g_number_1u0iw823}" data-spread="${g_number_zl0c0t23}" data-max-distance="${g_number_xfs7ml23}">
    ${gallery_akrzzp23_7925} 
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
            gallery_akrzzp23,
number_vyg7w323,
number_aavqr223,
number_6lz7rh23,
select_qw8bw523,
number_fzoo2323,
number_bbcfm423,
number_d461vm23,
number_1u0iw823,
number_zl0c0t23,
number_xfs7ml23,
dimension_bytsh423,
slider_yby6w923,
border_so1gec23,
dimension_i8dqlc23,
boxshadow_cg04l323,

            block_id,
        } = attributes;

        

        

        

        
let g_number_vyg7w323 = number_vyg7w323 && number_vyg7w323 != undefined  ? number_vyg7w323 : "";
let g_number_aavqr223 = number_aavqr223 && number_aavqr223 != undefined  ? number_aavqr223 : "";
let g_number_6lz7rh23 = number_6lz7rh23 && number_6lz7rh23 != undefined  ? number_6lz7rh23 : "";
let g_select_qw8bw523 = select_qw8bw523 && select_qw8bw523 != undefined  ? select_qw8bw523 : "";
let g_number_fzoo2323 = number_fzoo2323 && number_fzoo2323 != undefined  ? number_fzoo2323 : "";
let g_number_bbcfm423 = number_bbcfm423 && number_bbcfm423 != undefined  ? number_bbcfm423 : "";
let g_number_d461vm23 = number_d461vm23 && number_d461vm23 != undefined  ? number_d461vm23 : "";
let g_number_1u0iw823 = number_1u0iw823 && number_1u0iw823 != undefined  ? number_1u0iw823 : "";
let g_number_zl0c0t23 = number_zl0c0t23 && number_zl0c0t23 != undefined  ? number_zl0c0t23 : "";
let g_number_xfs7ml23 = number_xfs7ml23 && number_xfs7ml23 != undefined  ? number_xfs7ml23 : "";
let g_dimension_bytsh423 = dimension_bytsh423 && dimension_bytsh423 != undefined  ? dimension_bytsh423 : "";
let g_border_so1gec23 = border_so1gec23 && border_so1gec23 != undefined  ? border_so1gec23 : "";
let g_dimension_i8dqlc23 = dimension_i8dqlc23 && dimension_i8dqlc23 != undefined  ? dimension_i8dqlc23 : "";
let g_boxshadow_cg04l323 = boxshadow_cg04l323 && boxshadow_cg04l323 != undefined  ? boxshadow_cg04l323 : "";
let gallery_akrzzp23_7925 = '';
                                gallery_akrzzp23  && gallery_akrzzp23.map((g_image, index) => {
                                    gallery_akrzzp23_7925 += `<div class="wkit-widget-gallery-item {loop-class}" data-gallery_akrzzp23="{gallery_akrzzp23}">
        <div class="wkit-widget-item-img" style="background-image:url(${g_image.url})"></div>
    </div>`;
                                })
        

        let styleCss = Pmgc_CssGenerator(attributes, 'wdkit', "wb-s1jbry24", block_id, true);

        return (
            React.createElement(Fragment, null,
                wp.element.createElement("div", {
                    class: "wkit-wb-Widget_s1jbry24 wdkit-block-" + block_id + "",
                    dangerouslySetInnerHTML: {
                        __html: `<div class="wkit-image-grid-wrap" data-deskcol="${g_number_vyg7w323}" data-tabcol="${g_number_aavqr223}" data-mobcol="${g_number_6lz7rh23}" data-duration="${g_number_fzoo2323}" data-ease="${g_select_qw8bw523}" data-skew="${g_number_bbcfm423}" data-scale="${g_number_d461vm23}" data-max-rotation="${g_number_1u0iw823}" data-spread="${g_number_zl0c0t23}" data-max-distance="${g_number_xfs7ml23}">
    ${gallery_akrzzp23_7925} 
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
    new Messy_Image_Gallery_s1jbry24();