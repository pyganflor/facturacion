// COMPONENTES REGISTRADOS

import Vue from 'vue'

// LOGIN
Vue.component('login-component', require('./components/auth/LoginComponent.vue').default);

// LAYOUT
Vue.component('nav-component', require('./components/partials_layout/NavComponent.vue').default);
Vue.component('aside-component', require('components/partials_layout/AsideComponent.vue').default);

// DASHBOARD
Vue.component('administrador-component', require('./components/dashboard/AdministradorComponent.vue').default);

//PERFIL
Vue.component('perfil-component', require('./components/perfil/PerfilComponent.vue').default);


//USUARIOS
Vue.component('gestion-usuario', require('./components/usuarios/UsuariosComponent.vue').default);

//CLIENTES
Vue.component('gestion-cliente', require('./components/clientes/ClientesComponent.vue').default);

//INVENTARIO
Vue.component('gestion-inventario', require('./components/inventario/InventarioComponent.vue').default);

//MODULOS
Vue.component('gestion-modulos', require('./components/modulos/ModuloComponent.vue').default);

//PROVEEDORES
Vue.component('gestion-proveedores', require('./components/proveedores/ProveedoresComponent.vue').default);


//COMPROBANTES
Vue.component('gestion-factura', require('./components/comprobantes/FacturaComponent').default);
Vue.component('gestion-retencion-cliente', require('./components/comprobantes/RetencionClienteComponent').default);


//COMPONENTES TEMPLATE BASE
Vue.component('base-material-card', require('./components/base/MaterialCard.vue').default);
Vue.component('base-breadcumbs', require('./components/base/BreadCumbsComponent.vue').default);
