require('./bootstrap');

window.Vue = require('vue');

require('./route_component')

import store from './store'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { VueMaskDirective } from 'v-mask'

Vue.use(VueSweetalert2);
Vue.use(Vuetify)
Vue.directive('mask', VueMaskDirective)

const app = new Vue({
    el: '#app',
    store,
    vuetify : new Vuetify({
        theme:{
            themes:{
                light:{
                    primary : '#00b388',
                    secondary : '#4d4d4f'
                }
            }
        }
    })
});
