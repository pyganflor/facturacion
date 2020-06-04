// COMPONENTES REGISTRADOS

import * as Vue from 'vue'

// LOGIN
Vue.component('login-component', require('./components/auth/loginComponent.vue').default);
Vue.component('nav-component', require('./components/partials_layout/NavComponent.vue').default);
Vue.component('aside-component', require('./components/partials_layout/AsideComponent.vue').default);
