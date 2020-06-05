require('./bootstrap');

window.Vue = require('vue');

require('./route_component')

import store from './store'
import Vuetify from 'vuetify'

Vue.use(Vuetify)

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
    }),
});
