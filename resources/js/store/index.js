
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state:{
        barColor: 'rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)',
        barImage: 'https://demos.creative-tim.com/material-dashboard/assets/img/sidebar-1.jpg',
        drawer: true,
        loadingBtn : false
    },
    mutations: {
        setDrawer (state) {
            state.drawer = !state.drawer
        },
        setLoadingBtn(state) {
            state.loadingBtn = !state.loadingBtn
        }
      },
    actions:{
        errorRequest({commit,state},datos,status){
            console.log(datos,status);
            state.alertas='';

            let errorValidacion = datos.status === 422 || (typeof status !== 'undefined' && typeof datos.status==='undefined')

            if(errorValidacion){ // ERROR DE VALIDACION DEL REQUEST
                Object.keys(datos.datos).forEach(item => {
                    let msg = datos.datos[item];
                    for(let i = 0; i < msg.length; i++)
                        state.alertas += msg[i] + '<br />';
                });

            }else if(datos.status===500){ // MAYORMENTE ERROR DE PROGRAMACIÓN
                let i =1;
                Object.keys(datos.datos).forEach(item => {
                    let msg = datos.datos[item];
                    if(i<5)
                        state.alertas += item+' '+msg + '<br />';
                    i++;
                });
            }else{
                state.alertas = 'Ha ocurrido un error inesperado, intente nuevamente';
            }
            commit('setLoadingBtn')
            alert(state.alertas);
        }
    },
})
