<template>
    <div class="col-md-12 ">
        <v-form ref="form">
            <v-alert
                    color="primary"
                    dark
                    icon="mdi-account-switch"
                    border="left"
                    dense

            >
                En esta sección puede gestionar sus proveedores, agregar nuevos, editar y eliminar
            </v-alert>
            <v-data-table
                    :headers="headers"
                    :items="desserts"
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
                        <v-toolbar-title>Proveedores</v-toolbar-title>
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
                        <v-dialog
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
                                                        v-model="editedItem.nombre_comercial"
                                                        label="Nombre comercial"
                                                        :rules="requierdRules"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.razon_social"
                                                        label="Razón social"
                                                        :rules="requierdRules"
                                                >
                                                </v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.identificacion"
                                                        label="Identificación"
                                                        :rules="requierdRules"
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.correo"
                                                        label="Correo"
                                                        type="email"
                                                        prepend-icon="mdi-email"
                                                        :rules="correoRules"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.tlf"
                                                        label="Teléfono"
                                                        prepend-icon="mdi-cellphone-android"
                                                        :rules="tlfRules"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-select
                                                        :items=tc
                                                        attach
                                                        v-model="editedItem.tipo_cta"
                                                        label="Tipo de cuenta"
                                                >
                                                </v-select>
                                            </v-col>
                                            <v-col cols="12" sm="3">
                                                <v-text-field
                                                        v-model.number="editedItem.banco"
                                                        label="Banco"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="3">
                                                <v-text-field
                                                        v-model.number="editedItem.numero_cta"
                                                        label="Número de cta"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6">
                                                <v-text-field
                                                        v-model="editedItem.direccion"
                                                        label="Dirección"
                                                        prepend-icon="mdi-home-map-marker"
                                                        :rules="requierdRules"
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
                                                :loading=loadingBtn
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
                        </v-dialog>
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
    import {mapState,mapMutations,mapActions} from 'vuex'

    export default {
        props:{
            proveedores:{
                required:true,
                type: Array,
                default:[]
            }
        },
        data:() =>({
            headers: [
                { text: 'Nombre',value: 'nombre_comercial' },
                { text: 'Identificación', value: 'identificacion' },
                { text: 'Correo', value: 'correo' },
                { text: 'Teléfono', value: 'tlf', sortable: false },
                { text:'Estado', value:'estado'},
                { text: 'Acciones', value:'actions', sotable:false }
            ],
            dialog: false,
            search : '',
            textAlert: 'No se encontraron registros',
            editedIndex: -1,
            desserts: [],
            tc:['Ahorro','Corriente'], // TIPOS DE CUENTAS
            editedItem: {
                id_proveedor:'',
                identificacion:'',
                nombre_comercial: '',
                razon_social:'',
                tlf:'',
                correo: '',
                direccion:'',
                banco:'',
                estado:'',
                tipo_cta:'',
                numero_cta:''
            },
            defaultItem: {
                id_proveedor:'',
                correo: '',
                nombre_comercial: '',
                razon_social:'',
                tlf:'',
                identificacion:'',
                direccion:'',
                banco:'',
                estado:'',
                tipo_cta:'',
                numero_cta:''
            },
            requierdRules:[
                v => !!v || 'El campo es obligatorio'
            ],
            correoRules:[
                v => !!v || 'Debe escribir el correo electrónico del proveedor',
                v => /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'Debe escribir un correo válido'
            ],
            tlfRules:[
                v => !!v || 'Debe escribir el  teléfono del proveedor',
                v => (!v || v.length <= 10) || 'El teléfono debe ser menor o igual a 10 digitos ',
                v => /^\d*$/.test(v) || 'El número no puede tener caracteres especiales',
            ]
        }),
        computed: {
            ...mapState(['loadingBtn','loadingTable']),

            formTitle () {
                return this.editedIndex === -1 ? 'Nuevo proveedor' : 'Editar proveedor '+this.editedItem.nombre_comercial
            },
        },
        watch: {
            dialog (val) {
                val || this.closeModal()
            },
        },
        methods:{

            ...mapMutations(['setLoadingBtn','setLoadingTable']),

            ...mapActions(['errorRequest','alertNotification','httpRequest']),

            editItem (item) {
                this.editedIndex = this.desserts.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.dialog = true
            },

            estadoItem (item) {
                Vue.swal({
                    text: "¿Esta seguro de "+(item.estado ? 'desactivar': 'activar')+" al proveedor "+item.nombre_comercial.toUpperCase()+".?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#00b388',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {

                        this.httpRequest({
                            method:'post',
                            url:'proveedor/estado',
                            data : {
                                idProveedor : item.id_proveedor,
                                estado : item.estado
                            }
                        }).then(res => {

                            this.alertNotification({
                                param: {
                                    html: res.data.msg
                                }
                            })

                            item.estado =  !item.estado

                            this.closeModal()

                        })

                    }
                })
            },

            closeModal () {
                this.dialog = false;
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                });
            },

            save () {

                if (!this.$refs.form.validate())
                    return;

                this.setLoadingBtn()
                this.setLoadingTable()
                this.httpRequest({
                    method:'post',
                    url:'proveedor/store',
                    data : this.editedItem
                }).then(res => {

                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })

                    if (this.editedIndex > -1) { // ACTUALIZA
                        Object.assign(this.desserts[this.editedIndex],res.data.proveedor)
                    } else { // GUARDA
                        this.desserts.push(res.data.proveedor)
                    }

                    this.closeModal()
                    this.setLoadingBtn()
                    this.setLoadingTable()
                })

            }
        },
        mounted() {
            this.desserts = this.proveedores
        }
    }
</script>
