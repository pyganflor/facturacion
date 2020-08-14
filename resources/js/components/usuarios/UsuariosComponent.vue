<template>
    <div class="col-md-12 ">
        <v-form ref="form">
            <v-alert
                    color="primary"
                    dark
                    icon="mdi-account-multiple"
                    border="left"
                    dense

            >
                En esta sección puede realizar acciones con los usuario, agregar nuevos, editar y eliminar
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
                        <v-toolbar-title>Usuarios</v-toolbar-title>
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
                                max-width="700px"
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
                                                        label="Nombre"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.correo"
                                                        label="Correo"
                                                        type="email"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="4">
                                                <v-text-field
                                                        v-model="contrasena"
                                                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                                        :type="show ? 'text' : 'password'"
                                                        name="input-10-1"
                                                        label="Contraseña"
                                                        @click:append="show = !show"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-select
                                                :items="rolesactivos"
                                                attach
                                                v-model="editedItem.roles"
                                                chips
                                                label=Roles
                                                multiple
                                                item-text="nombre"
                                                item-value="id_rol"
                                        >
                                            <template v-slot:selection="{ item }">
                                                <v-chip>
                                                        <span>{{ item.nombre }}</span>
                                                </v-chip>
                                            </template>
                                            <template v-slot:item="{ item }">
                                                {{ item.nombre }}
                                            </template>
                                        </v-select>
                                        <v-select
                                                :items="modulosactivos"
                                                attach
                                                v-model="editedItem.modulos"
                                                chips
                                                label=Módulos
                                                multiple
                                                item-text="nombre"
                                                item-value="id_modulo"
                                        >
                                            <template v-slot:selection="{ item }">
                                                <v-chip>
                                                    <span>{{ item.nombre }}</span>
                                                </v-chip>
                                            </template>
                                            <template v-slot:item="{ item }">
                                                {{ item.nombre }}
                                            </template>
                                        </v-select>
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
            usuarios:{
                required:true,
                type: Array
            },
            modulosactivos:{
                required:true,
                type: Array
            },
            rolesactivos:{
                required:true,
                type: Array
            }
        },
        data:() =>({
            headers: [
                { text: 'Nombre',value: 'nombre' },
                { text: 'Razón social',value: 'razon_social' },
                { text: 'Ruc', value: 'ruc' },
                { text: 'Correo', value: 'correo' },
                { text:'Estado', value:'estado'},
                { text: 'Teléfono', value: 'tlf', sortable: false },
                { text: 'Acciones', value:'actions', sotable:false }
            ],
            dialog: false,
            correo :'',
            show: false,
            chip:true,
            contrasena:'',
            modulos:'',
            roles:'',
            search : '',
            textAlert: 'No se encontraron registros',
            editedIndex: -1,
            rolesActivos : [],
            desserts: [],
            editedItem: {
                idUsuario:'',
                correo: '',
                nombre: '',
                contrasena:'',
                roles:[],
                modulos:[]
            },
            defaultItem: {
                idUsuario:'',
                correo: '',
                nombre: '',
                roles:[],
                constrasena:'',
                modulos:[]
            },
        }),
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'Nuevo usuario' : 'Editar usuario'
            },
            ...mapState(['loadingTable','paramsAlertQuestion'])
        },
        watch: {
            dialog (val) {
                val || this.closeModal()
            },
        },
        methods:{

            ...mapMutations(['setLoadingTable','setLoadingBtn']),

            ...mapActions(['alertNotification']),

            editItem (item) {
                this.show= false
                this.editedIndex = this.desserts.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.contrasena=''
                this.dialog = true
            },

            estadoItem (item) {
                Vue.swal({
                    text: "¿Esta seguro de "+(item.estado ? 'desactivar': 'activar')+" al usuario "+item.nombre.toUpperCase()+".?",
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {
                        axios.post('/usuario/estado',{
                            idUsuario : item.id_usuario,
                            estado : item.estado
                        }).then(response =>{
                            Vue.swal(
                                'Éxito!',
                                response.data.msg,
                                'success'
                            );
                            item.estado =  !item.estado
                            /*this.$store.dispatch({
                                type: 'alertNotification',
                                param:{
                                    html: response.data.msg
                                }
                            });*/
                        }).catch(error =>{

                            let response = error.response;
                            Vue.swal(
                                'Error!',
                                response.data.message+', '+response.data.file+' en la línea '+response.data.line,
                                'error'
                            );

                            /*this.$store.dispatch({
                                type: 'errorRequest',
                                data : {
                                    datos: response.data.errors,
                                    status : response.status,
                                }
                            });*/
                        });
                    }
                })
            },

            closeModal () {
                this.dialog = false;
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                    this.modulos=''
                    this.roles=''
                });
            },

            save () {

                if (!this.$refs.form.validate())
                    return;

                this.$store.commit('setLoadingBtn')
                this.setLoadingTable()
                axios.post('/usuario/store',{
                    idUsuario : this.editedItem.id_usuario,
                    nombre: this.editedItem.nombre,
                    correo: this.editedItem.correo,
                    contrasena: this.contrasena,
                    modulos : this.editedItem.modulos,
                    roles : this.editedItem.roles
                }).then(response => {

                    let data= {
                        id_usuario : response.data.usuario.id_usuario,
                        nombre : response.data.usuario.nombre,
                        correo : response.data.usuario.correo,
                        estado : response.data.usuario.estado,
                        modulos : response.data.usuario.modulos,
                        roles : response.data.usuario.roles
                    };

                    if (this.editedIndex > -1) { // ACTUALIZA
                        Object.assign(this.desserts[this.editedIndex],data)
                    } else { // GUARDA
                        this.desserts.push(data)
                    }

                    this.closeModal();
                    this.setLoadingBtn()
                    this.alertNotification({
                        param:{
                            html: response.data.msg
                        }
                    });
                    this.setLoadingTable()

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

            }
        },
        mounted() {
            this.desserts = this.usuarios
        }
    }
</script>

<style scoped>

</style>