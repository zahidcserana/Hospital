require('./bootstrap');
import 'vue-datetime/dist/vue-datetime.css'
import 'vue2-datepicker/index.css'
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';

import Vue from 'vue';
import moment from 'moment'

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueRouter from 'vue-router'
import VueSweetalert2 from 'vue-sweetalert2';
import VueSlimScroll from 'vue-slimscroll'
import VModal from 'vue-js-modal'
import { Datetime } from 'vue-datetime';
import DatePicker from 'vue2-datepicker'
import VueLoaders from 'vue-loaders';
import VueHtmlToPaper from 'vue-html-to-paper';
import Multiselect from 'vue-multiselect'

Vue.component('multiselect', Multiselect)
Vue.use(VueLoaders);
Vue.component('datetime', Datetime);
Vue.component('DatePicker', DatePicker);
Vue.component('VueCtkDateTimePicker', VueCtkDateTimePicker);

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueRouter)
Vue.use(VueSweetalert2);
Vue.use(VueSlimScroll);
Vue.use(VModal)

const options = {
    name: '_blank',
    specs: [
        'fullscreen=yes',
        'titlebar=yes',
        'scrollbars=yes'
    ],
    styles: [
        '/assets/css/bootstrap.min.css',
        '/assets/css/style.css',
    ]
}
Vue.use(VueHtmlToPaper, options);

Vue.filter('formatDateTime', function(value) {
    if (value) {
        return moment(String(value)).format('YYYY-MM-DD hh:mm A')
    }
});

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('YYYY-MM-DD')
    }
});

Vue.filter('myDateFormat', function(value) {
    if (value) {
        return moment(String(value)).format('MMMM Do, YYYY')
    }
});

Vue.filter('formatTime', function(value) {
    if (value) {
        return moment(String(value)).format('h:mm:ss a')
    }
});


const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
