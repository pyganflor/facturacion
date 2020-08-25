<template>
    <div class="col-md-12 ">
        <v-form ref="form">
            <descripcion-vista
                    descripcion="En esta sección puede gestionar sus clientes, agregar nuevos, editar y eliminar"
                    icon="mdi-account-convert"
            />
            <v-data-table
                    :headers="headers"
                    :items="dataTable"
                    sort-by="estado"
                    update: sort-desc
                    class="elevation-1"
                    :items-per-page="10"
                    dense
                    :loading=loadingTable
                    loading-text="Cargando datos"
                    :search="search"
            >
                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <v-toolbar-title>Clientes</v-toolbar-title>
                        <v-divider
                                class="mx-4"
                                inset
                                vertical
                        ></v-divider>
                        <v-spacer></v-spacer>
                        <v-text-field
                                v-model="search"
                                append-icon="mdi-magnify"
                                label="Buscar"
                                hide-details
                                class="mr-5"
                        ></v-text-field>
                        <v-btn
                                color="primary"
                                fab dark
                                small
                                class="mb-2"
                                @click="dialog = true"
                        >
                            <v-icon>mdi-account-plus</v-icon>
                        </v-btn>
                        <form-cliente
                            :tipoidentificacion=tipoidentificacion
                            :paises=paises
                            :dialog=dialog
                            :editedItem=editedItem
                            :editedIndex=editedIndex
                            :tipopago=tipopago
                            @setDialog=setDialog
                            @setData=setDataTable
                        ></form-cliente>
                        <!--<v-dialog
                                v-model="dialog"
                                max-width="900px"
                                :persistent=true
                        >
                            <template v-slot:activator="{ on }">
                                <v-btn
                                        color="primary"
                                        fab small dark
                                        class="mb-2"
                                        v-on="on">
                                    <v-icon>mdi-account-plus</v-icon>
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.nombre"
                                                        label="Nombre o razón social"
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12" sm="4">
                                                <v-select
                                                        :items="tipoidentificacion"
                                                        attach
                                                        v-model="editedItem.id_tipo_identificacion"
                                                        label="Tipo de identificacion"
                                                        item-text="nombre"
                                                        item-value="id_tipo_identificacion"
                                                        @change="consumidorFinal(editedItem.id_tipo_identificacion)"
                                                >
                                                </v-select>
                                            </v-col>

                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.identificacion"
                                                        label="Identificación"
                                                        :disabled=cm
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-autocomplete
                                                        v-model="editedItem.codigo_pais"
                                                        :items="paises"
                                                        label="País"
                                                        persistent-hint
                                                        prepend-icon="mdi-google-maps"
                                                        item-text="nombre"
                                                        item-value="codigo"
                                                >
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-autocomplete
                                                        v-model="editedItem.id_tipo_pago"
                                                        :items="tipopago"
                                                        label="Tipo de pago"
                                                        prepend-icon="mdi-cash-multiple"
                                                        item-text="nombre"
                                                        item-value="id_tipo_pago"
                                                >
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.correo"
                                                        label="Correo"
                                                        prepend-icon="mdi-email"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="3">
                                                <v-text-field
                                                        v-model="editedItem.tlf"
                                                        label="Teléfono"
                                                        prepend-icon="mdi-cellphone-android"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="2">
                                                <v-text-field
                                                        v-model.number="editedItem.plazo_pago"
                                                        label="Plazo de pago"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="2">
                                                <v-select
                                                        :items=ut
                                                        attach
                                                        v-model="editedItem.ut_plazo_pago"
                                                        label="Und. tiempo"
                                                >
                                                </v-select>
                                            </v-col>

                                            <v-col cols="12" sm="5">
                                                <v-text-field
                                                        v-model="editedItem.direccion"
                                                        label="Dirección"
                                                        prepend-icon="mdi-home-map-marker"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                            <v-footer >
                                <v-row class="text-center">
                                    <v-col>
                                        <v-btn
                                                class="ma-2"
                                                color="primary"
                                                @click="save"
                                                :loading=$store.state.loadingBtn
                                        >
                                            <v-icon>mdi-floppy</v-icon> Guardar
                                        </v-btn>
                                        <v-btn
                                                class="ma-2"
                                                color="secondary"
                                                @click="closeModal"
                                        >
                                            <v-icon>mdi-cancel</v-icon> Cancelar
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-footer>
                        </v-dialog>-->
                    </v-toolbar>
                </template>
                <template v-slot:item.razon_social="{ item }">
                    <span class="text-capitalize" v-text="item.razon_social"></span>
                </template>
                <template v-slot:item.correo="{ item }">
                    <a :href="'mailto:'+item.correo" v-text="item.correo"></a>
                </template>
                <template v-slot:item.tlf="{ item }">
                    <a :href="'tel:'+item.tlf" v-text="item.tlf"></a>
                </template>
                <template v-slot:item.estado="{ item }">
                    <span v-text="item.estado ? 'Activo': 'Inactivo' "></span>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-btn
                            icon
                            small
                            class="mr-2"
                            :x-small=true
                            @click="editItem(item)"
                    >
                        <v-icon title="Editar usuario">mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                            icon
                            small
                            :x-small=true
                            @click="estadoItem(item)"
                            :color="item.estado ? 'red' : 'success'"
                    >
                        <v-icon :title="item.estado ? 'Descativar usuario' : 'Activar usuario'">
                            {{item.estado ? 'mdi-account-remove' : 'mdi-account-check'}}
                        </v-icon>

                    </v-btn >
                </template>
                <template v-slot:no-results>
                    <v-alert type="warning" dense icon="mdi-cancel">
                        No se encontraron registros
                    </v-alert>
                </template>
                <template v-slot:no-data>
                    <v-alert type="error" dense>
                        {{textAlert}}
                    </v-alert>
                </template>
            </v-data-table>
        </v-form>
    </div>
</template>

<script>

    import {mapState,mapMutations} from 'vuex'

    export default {
        props:{
            clientes:{
                required:true,
                type: Array
            },
            tipopago:{
                required:true,
                type: Array
            },
            tipoidentificacion:{
                required:true,
                type: Array
            },
            paises:{
                required:true,
                type: Array
            }
        },
        data:() =>({
            headers: [
                { text: 'Nombre / Razón social',value: 'nombre' },
                { text: 'Identificación', value: 'identificacion' },
                { text: 'Correo', value: 'correo' },
                { text: 'Teléfono', value: 'tlf', sortable: false },
                { text:'Estado', value:'estado'},
                { text: 'Acciones', value:'actions', sotable:false }
            ],
            dialog: false,
            cm:false, //consumidor final
            search : '',
            textAlert: 'No se encontraron registros',
            editedIndex: -1,
            dataTable: [],
            ut:['Días','Mes','Años'],
            editedItem: {
                id_cliente:'',
                id_tipo_identificacion:'',
                correo: '',
                nombre: '',
                id_tipo_pago:'',
                codigo_pais:'',
                tlf:'',
                identificacion:'',
                direccion:'',
                plazo_pago:'',
                ut_plazo_pago:'',
                estado:true
            },
            defaultItem: {
                id_cliente:'',
                id_tipo_identificacion:'',
                correo: '',
                nombre: '',
                id_tipo_pago:'',
                codigo_pais:'',
                tlf:'',
                identificacion:'',
                direccion:'',
                plazo_pago:'',
                ut_plazo_pago:'',
                estado:true
            },
        }),

        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'Nuevo cliente' : 'Editar cliente '+this.editedItem.nombre
            },

            ...mapState(['paramsAlertQuestion','loadingTable'])
        },

        methods:{

            ...mapMutations(['setLoadingTable','setLoadingBtn']),

            /*consumidorFinal(idTipIdent){
                if(idTipIdent===4){ // 4 es el ID del consumidor final en la tabla tipo_identificacion
                    this.editedItem.identificacion = 9999999999999
                    this.cm=true
                }else{
                    this.editedItem.identificacion = ''
                    this.cm=false
                }
            },*/

            setDataTable(item){
                console.log(item)

                if (this.editedIndex > -1) { // ACTUALIZA
                    Object.assign(this.dataTable[this.editedIndex],this.editedItem)
                } else { // GUARDA
                    this.editedItem.id_cliente = item.idCliente;
                    this.dataTable.push(this.editedItem)
                }
            },

            setDialog(estado){
                this.dialog=estado
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                });
            },

            editItem (item) {
                this.dialog = true
                this.editedIndex = this.dataTable.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.dialog = true
            },

            estadoItem (item) {
                Vue.swal({
                    text: "¿Esta seguro de "+(item.estado ? 'desactivar': 'activar')+" al cliente "+item.nombre.toUpperCase()+".?",
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {
                        axios.post('/cliente/estado',{
                            idCliente : item.id_cliente,
                            estado : item.estado
                        }).then(response =>{
                            Vue.swal(
                                'Éxito!',
                                response.data.msg,
                                'success'
                            );
                            item.estado =  !item.estado

                        }).catch(error =>{

                            let response = error.response;
                            Vue.swal(
                                'Error!',
                                response.data.message+', '+response.data.file+' en la línea '+response.data.line,
                                'error'
                            );

                        });
                    }
                })
            },

            /*save () {

                if (!this.$refs.form.validate())
                    return;

                this.setLoadingBtn()
                this.setLoadingTable()

                axios.post('/cliente/store',{
                    data : this.editedItem
                }).then(response => {

                    if (this.editedIndex > -1) { // ACTUALIZA
                        Object.assign(this.dataTable[this.editedIndex],this.editedItem)
                    } else { // GUARDA
                        this.editedItem.id_cliente = response.data.idCliente;
                        this.dataTable.push(this.editedItem)
                    }

                    this.closeModal();
                    this.setLoadingBtn()
                    this.setLoadingTable()
                    this.$store.dispatch({
                        type: 'alertNotification',
                        param:{
                            html: response.data.msg
                        }
                    });

                }).catch(error => {
                    let response = error.response;

                    this.$store.dispatch({
                        type: 'errorRequest',
                        data : {
                            datos: response.data.errors,
                            status : response.status,
                        }
                    });
                });

            }*/
        },
        mounted() {
            this.dataTable = this.clientes
        }
    }
</script>
