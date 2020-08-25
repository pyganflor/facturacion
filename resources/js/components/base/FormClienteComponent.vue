<template>
    <v-form ref="form_cliente">
        <v-dialog
                v-model="dialog"
                max-width="900px"
                :persistent=true
        >
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
                                        :rules=requiredRule
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
                                        :rules=requiredRule
                                        @change="consumidorFinal(editedItem.id_tipo_identificacion)"
                                >
                                </v-select>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-text-field
                                        v-model="editedItem.identificacion"
                                        label="Identificación"
                                        :disabled=cm
                                        :rules=requiredRule
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
                                        :rules=requiredRule
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
                                        :rules=requiredRule
                                >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-text-field
                                        v-model="editedItem.correo"
                                        label="Correo"
                                        prepend-icon="mdi-email"
                                        :rules=correoRules
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
                                        counter="300"
                                        :rules=requiredRule
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
    </v-form>
</template>

<script>

    import {mapState,mapActions,mapMutations} from 'vuex'

    export default {
        name: "FormCliente",
        props:{
            paises:{
                type:Array,
                required:true
            },
            tipopago:{
                type:Array,
                required:true
            },
            tipoidentificacion:{
                type:Array,
                required:true
            },
            editedItem:{
                type:Object,
                required:true
            },
            tamanoBtn:{
                type:String,
                default: 'small'
            },
            icon:{
                type:Boolean,
                default: false
            },
            dialog:{
                type: Boolean,
                default: false
            },
            editedIndex: {
                type: Number,
                default: -1
            }
        },
        data: () => ({
            ut:['Días','Mes','Años'],
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
            requiredRule:[
                v => !!v || 'El campo es requerido'
            ],
            correoRules:[
                v => !!v || 'El correo electrónico del cliente es obligatorio',
                v => /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'Debe escribir un correo válido'
            ],
            cm:false, //consumidor final
            x_small:false,
            small :false
        }),
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'Nuevo cliente' : 'Editar cliente '+this.editedItem.nombre
            },

            ...mapState(['loadingBtn'])
        },
        methods:{

            ...mapActions(['httpRequest','alertNotification']),

            ...mapMutations(['setLoadingBtn','setLoadingTable']),

            consumidorFinal(idTipIdent){
                if(idTipIdent===4){ // 4 es el ID del consumidor final en la tabla tipo_identificacion
                    this.editedItem.identificacion = 9999999999999
                    this.cm=true
                }else{
                    this.editedItem.identificacion = ''
                    this.cm=false
                }
            },

            closeModal () {
                this.$emit('setDialog',false)
            },

            save () {

                if (!this.$refs.form_cliente.validate())
                    return;

                this.setLoadingBtn()
                this.setLoadingTable()

                this.httpRequest({
                    method:'post',
                    url:'/cliente/store',
                    data : this.editedItem
                }).then(response => {

                    this.$emit('setData', response.data)
                    this.closeModal()
                    this.setLoadingBtn()
                    this.setLoadingTable()
                    this.alertNotification({
                        param:{
                            html: response.data.msg
                        }
                    });

                })

            },

        },
        mounted(){
            this.small= this.tamanoBtn==='small'
            this.x_small= this.tamanoBtn==='x-small'
        }
    }
</script>

<style scoped>

</style>