
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state:{
        barColor: 'rgba(0, 0, 0, .8), rgba(0, 0, 0, .8)',
        barImage: 'https://demos.creative-tim.com/material-dashboard/assets/img/sidebar-1.jpg',
        drawer: true,
        loadingBtn : false,
        loadingBtn2 : false,
        sweetAlert:{
            title:'Éxito',
            html:'',
            icon:'success',
            toast:true,
            timer:5000,
            confirmButtonText :'<span class="mdi mdi-close-circle-outline"></span> Cerrar',
            timerProgressBar: true,
            position:'top',
            confirmButtonColor: '#a5dc86',
        }
    },
    mutations: {
        setDrawer (state) {
            state.drawer = !state.drawer
        },
        setLoadingBtn(state) {
            state.loadingBtn = !state.loadingBtn
        },
        setLoadingBtn2(state) {
            state.loadingBtn2 = !state.loadingBtn2
        },
        setSeewtAlert(state,payload){

            if(typeof payload.timer != "undefined")
                state.sweetAlert.timer = payload.timer

            if(typeof payload.title != "undefined")
                state.sweetAlert.title = payload.title

            if(typeof payload.icon != "undefined")
                state.sweetAlert.icon = payload.icon

            if(typeof payload.toast != "undefined")
                state.sweetAlert.toast = payload.toast

            if(typeof payload.confirmButtonColor != "undefined")
                state.sweetAlert.confirmButtonColor = payload.confirmButtonColor

            state.sweetAlert.html = payload.html

        }
      },
    actions:{
        errorRequest({commit,state,dispatch},payload){

            state.alertas='';
            let errorValidacion=false;

            if(typeof payload.data != 'undefined'){
                errorValidacion = payload.data.status === 422
            }else if(typeof payload.status != 'undefined'){
                errorValidacion = payload.status === 422
            }

            if(errorValidacion){ // ERROR DE VALIDACION DEL REQUEST
                let datos=""

                typeof payload.data !="undefined"
                    ?   datos = payload.data.datos
                    :   datos = payload.datos;

                Object.keys(datos).forEach(item => {
                    let msg = datos[item];
                    for(let i = 0; i < msg.length; i++)
                        state.alertas += msg[i] + '<br />';
                });

            }else if(payload.data.status===500){ // MAYORMENTE ERROR DE PROGRAMACIÓN
                let i =1;
                Object.keys(payload.data.datos).forEach(item => {
                    let msg = payload.data.datos[item];
                    if(i<5)
                        state.alertas += item+' '+msg + '<br />';
                    i++;
                });
            }else{
                state.alertas = 'Ha ocurrido un error inesperado, intente nuevamente';
            }

            if(typeof payload.data.btn == "undefined")
                commit('setLoadingBtn')
            else if(payload.data.btn===2)
                commit('setLoadingBtn2')

            let error500 = !(typeof payload.data != "undefined" && payload.data.status===500)

            let data ={
                param:{
                    title: 'Error!',
                    html: state.alertas,
                    icon: 'error',
                    toast: error500,
                    timer: !error500 ? 25000 : state.sweetAlert.timer,
                    position: state.sweetAlert.position,
                    confirmButtonColor : '#d60400'
                }
            }
            dispatch('alertNotification',data)

        },

        alertNotification({commit,state},payload){
            commit('setSeewtAlert',{
                html : payload.param.html,
                timer : payload.param.timer,
                title : payload.param.title,
                icon : payload.param.icon,
                toast : payload.param.toast,
                confirmButtonColor: payload.param.confirmButtonColor
            });

            Vue.swal({
                title: state.sweetAlert.title,
                html: state.sweetAlert.html,
                icon: state.sweetAlert.icon,
                toast: state.sweetAlert.toast,
                timerProgressBar : state.sweetAlert.timerProgressBar,
                timer: state.sweetAlert.timer,
                position: state.sweetAlert.position,
                confirmButtonText : state.sweetAlert.confirmButtonText,
                grow:'row',
                confirmButtonColor: state.sweetAlert.confirmButtonColor,
            });

            commit('setSeewtAlert',{
                title:'Éxito',
                icon:'success',
                toast:true,
                timer:5000,
                confirmButtonColor: '#a5dc86',
            });
        }
    }
})
