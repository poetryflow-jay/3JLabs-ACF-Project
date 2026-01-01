class MyClass_rebv2t25 {

        constructor() {
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
            let main_html = document.querySelectorAll(".wkit-wb-Widget_s1jbry24")    
                main_html.forEach(element => {
                    this.main_function_rebv2t25([element])
                });
        }, 800);
            });
        }

        main_function_rebv2t25($scope) {
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
    }

    new MyClass_rebv2t25();