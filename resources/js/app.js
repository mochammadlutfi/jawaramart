import { App, plugin } from '@inertiajs/inertia-vue'
import Vue from 'vue'
// import { InertiaProgress } from '@inertiajs/progress'


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
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

Vue.use(plugin)
Vue.use(BootstrapVue)
Vue.use(VueSweetalert2);
const el = document.getElementById('app');
Vue.mixin({
    methods: {
        route,
        asset(path) {
            var base_path = window._asset || '';
            return base_path + path;
        },
        currency(value){
            if (typeof value !== "number") {
                return value;
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

new Vue({
    store,
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
                    return require(`./Pages/${name}`).default
                }
            },
        },
    }),
}).$mount(el)
