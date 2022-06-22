import { App, plugin } from '@inertiajs/inertia-vue'
import Vue from 'vue'


import store from './store'
import BootstrapVue from 'bootstrap-vue';
import VueSweetalert2 from 'vue-sweetalert2';

// Custom components
import BaseLayoutModifier from '@/components/BaseLayoutModifier'
import BaseBlock from '@/components/BaseBlock'
import BaseBackground from '@/components/BaseBackground'
import BasePageHeading from '@/components/BasePageHeading'
import BaseNavigation from '@/components/BaseNavigation'

// Register global components
Vue.component(BaseLayoutModifier.name, BaseLayoutModifier)
Vue.component(BaseBlock.name, BaseBlock)
Vue.component(BaseBackground.name, BaseBackground)
Vue.component(BasePageHeading.name, BasePageHeading)
Vue.component(BaseNavigation.name, BaseNavigation)

Vue.use(plugin)
Vue.use(BootstrapVue)
Vue.use(VueSweetalert2);
const el = document.getElementById('app');

import permission from '@/Utils/permission';
Vue.mixin({
    methods: {
        route,
        permission,
        asset(path) {
            var base_path = window._asset || '';
            return base_path + path;
        },
        currency(value){
            if (typeof value !== "number") {
                // return value;
                value = parseFloat(value);
             }
             var formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
             });
             return formatter.format(value);
        },
        toUpperCase(value){
            var str = value.toString();
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        },
    },
});

Vue.config.productionTip = false;

new Vue({
    store,
    data: {
        window: {
            width: 0,
            height: 0,
            mobile : false,
        }
    },
    created() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
    },
    destroyed() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() {
            this.window.width = window.innerWidth;
            this.window.height = window.innerHeight;
            if(this.window.width > 768){
                this.window.mobile = false;
            }else{
                this.window.mobile = true;
            }
        }
    },
    render: h => h(App, {
        props: {
            title: title => `${title} - My App`,
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => {
                let parts = name.split('::')
                if(parts.length > 1){
                    let nameVue = parts[1].split('.')[0]
                    return require(`../../Modules/${ parts[0] }/Resources/Pages/${ nameVue }.vue`).default
                }else{
                    return require(`./Pages/Frontend/${name}`).default
                }
            },
        },
    }),
}).$mount(el)
