
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state:{
        barColor: 'rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)',
        barImage: 'https://demos.creative-tim.com/material-dashboard/assets/img/sidebar-1.jpg',
        drawer: true,
    },
    mutations: {
        setDrawer (state) {
            state.drawer = !state.drawer
        },
        iconDrawer (state){
            state.drawer
        }
      },
    actions:{


    }
})
