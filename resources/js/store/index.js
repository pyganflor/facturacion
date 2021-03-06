
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
        loadingTable:false,
        paramsAlertQuestion:{
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00b388',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        },
        formaPago:[
            {id_forma_pago:1,nombre:'Contado'},
            {id_forma_pago:2,nombre:'Crédito'}
        ],
        sweetAlert:{
            title:'Éxito',
            html:'',
            icon:'success',
            toast:true,
            timer:8000,
            grow:'row',
            confirmButtonText :'<span class="mdi mdi-close-circle-outline"></span> Cerrar',
            timerProgressBar: true,
            position:'top',
            confirmButtonColor: '#a5dc86',
        },
        estados:[
            {id: 1, nombre: 'Autorizado'},
            {id: 0, nombre: 'Rechazado' },
            {id: 2, nombre: 'No recibido'},
            {id: 3, nombre: 'Anulado'},
            {id: 4, nombre: 'No consultado'},
        ],
    },
    mutations: {
        setLoadingTable (state) {
            state.loadingTable = !state.loadingTable
        },

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

            if(typeof payload.grow != "undefined")
                state.sweetAlert.grow = payload.grow

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
            }else {
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
                grow: payload.param.grow,
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
                grow : state.sweetAlert.grow,
                confirmButtonColor: state.sweetAlert.confirmButtonColor,
            });

            commit('setSeewtAlert',{
                title:'Éxito',
                icon:'success',
                grow:'row',
                toast:true,
                timer:5000,
                confirmButtonColor: '#a5dc86',
            });
        },

        httpRequest({commit,state,dispatch},payload){

            return new Promise((resolve, reject)=>{
                let body =  {params: payload.data}
                if(payload.method === 'post' || payload.method === 'POST')
                    body = {data: payload.data}

                axios({
                        method:payload.method,
                        url: payload.url,
                        ...body
                    }).then(res => {

                    resolve(res)

                }).catch(err => {

                    reject(err)

                    console.log(err);
                    let response = err.response;
                    dispatch('errorRequest',{
                        data : {
                            datos: response.data.errors,
                            status : response.status,
                        }
                    });

                    if(state.loadingTable)
                        commit('setLoadingTable')

                });

            })
        }
    }
})
