<template>
    <v-container
            id="user-profile"
            fluid
            tag="section"
            color="primary"
    >
        <v-row justify="center">
            <v-col
                    cols="12"
                    md="8"
            >
                <base-material-card
                        color="primary"
                >
                    <template v-slot:heading>
                        <div class="display-1 font-weight-light">
                            Editar perfil
                        </div>
                        <div class="subtitle-1 font-weight-light">
                            Completa o edita tu perfil para la facturación electrónica y de sesión
                        </div>
                    </template>

                    <v-form novalidate="true" ref="form_perfil">
                        <v-container class="py-0">
                            <v-row>
                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            :rules=razonSocialRule
                                            label="Razon social"
                                            v-model=razonSocial
                                            required
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="Nombre comercial"
                                            v-model=nombreComercial
                                            :rules=nombreComercialRule
                                            required
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="Ruc"
                                            type="number"
                                            v-model="ruc"
                                            :counter="13"
                                            :rules=rucRules
                                            required
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        sm="12"
                                >
                                    <v-text-field
                                            label="Dirección matriz"
                                            v-model="dirMatriz"
                                            :rules=dirMatrizRule
                                            required
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="12"
                                >
                                    <v-text-field
                                            label="Dirección de establecimiento"
                                            v-model="dirEstablecimiento"
                                            :rules="dirEstablecimientoRule"
                                            required
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field
                                            label="N° Contribuyente especial"
                                            v-model="contriEsp"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="6"
                                >
                                    <v-select
                                            :items="OblContablidad"
                                            label="Obligado a llevar contabilidad"
                                            :rules=oblContRules
                                            requied
                                    ></v-select>
                                </v-col>

                                <v-col cols="12" class="py-0 mt-0">
                                    <v-file-input
                                            :rules="firmElectRules"
                                            accept=".P12"
                                            placeholder="Seleccione un archivo .p12"
                                            prepend-icon="mdi-file-lock"
                                            label="Firma electrónica"
                                            required
                                    ></v-file-input>
                                </v-col>

                                <v-col
                                        cols="12"
                                        class="text-center"
                                >
                                    <v-btn
                                            color="primary"
                                            class="mr-0"
                                            @click="savePerfil"
                                            :loading=this.$store.state.loadingBtn
                                    >
                                        <v-icon>mdi-content-save</v-icon> Actualizar
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </base-material-card>
            </v-col>

            <v-col
                    cols="12"
                    md="4"
            >
                <base-material-card
                        class="v-card-profile"
                        :avatar="usuario.imagen === null  ? '/imagenes/icono_usuario.png' : storage+'/'+usuario.imagen"
                >

                    <v-form ref="form_accesos">
                        <v-container>
                            <v-row>
                                <v-col cols="12" class="py-0 mt-0"

                                >
                                    <v-text-field
                                            v-model="user"
                                            label="Usuario (De inicio de sesión)"
                                            :rules="usuarioRules"
                                            required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" class="py-0 mt-0">
                                    <v-text-field
                                            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                                            :type="show1 ? 'text' : 'password'"
                                            label="Contraseña actual"
                                            v-model="actualPass"
                                            class="input-group--focused"
                                            @click:append="show1 = !show1"
                                            required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" class="py-0 mt-0">
                                    <v-text-field
                                            :append-icon="show2 ? 'mdi-eye' : 'mdi-eye-off'"
                                            :type="show2 ? 'text' : 'password'"
                                            label="Nueva contraseña"
                                            v-model="pass"
                                            class="input-group--focused"
                                            @click:append="show2 = !show2"
                                            required
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" class="py-0 mt-0">
                                    <v-file-input
                                            name="img"
                                            id="img"
                                            :rules="imagenRules"
                                            accept="image/png, image/jpeg, image/jpg"
                                            placeholder="Seleccione una imagen"
                                            prepend-icon="mdi-image-area"
                                            label="Avatar"
                                            @change="getImagen"
                                            ref="file"
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12" sm="12" >
                                    <v-card-actions>
                                        <v-btn
                                                color="primary"
                                                :block=true
                                                class="px-0"
                                                @click="saveAccesos"
                                                :loading=this.$store.state.loadingBtn2
                                        >
                                            <v-icon>mdi-content-save</v-icon> Actualizar

                                        </v-btn>
                                    </v-card-actions>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </base-material-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>

    export default {
        props :{
            usuario:{
                required:true
            },
            storage:{
                required:true,
                type:String
            }
        },
        data:() =>({
            razonSocial: '',
            nombreComercial :'',
            ruc: '',
            dirMatriz:'',
            dirEstablecimiento: '',
            contriEsp: '',
            OblContablidad : ['SI','NO'],
            show1:false,
            show2:false,
            pass:'',
            actualPass:'',
            imagen: '',
            user:'',
            razonSocialRule:[
                v => !!v || 'La razón social es obligatoria',
                v => (v && v.length <= 300) || 'El campo debe ser menor o igual 300 caracteres'
            ],
            nombreComercialRule: [
                v => !!v || 'El nombre comercial es obligatorio',
                v => (v && v.length <= 300) || 'El campo debe ser menor o igual 300 caracteres'
            ],
            rucRules:[
                v => !!v || 'El ruc es obligatorio ',
                v => (v && v.length === 13) || 'El ruc debe ser de 13 digitos',
            ],
            usuarioRules:[
                v => (v && v.length >= 6) || 'Debe escribir por lo menos 6 caracteres'
            ],
            dirMatrizRule: [
                v => !!v || 'La dirección matriz es obligatoria',
                v => (v && v.length <= 300) || 'El campo debe ser menor o igual 300 caracteres'
            ],
            dirEstablecimientoRule:[
                v => !!v || 'La dirección del establecimiento es obligatoria',
                v => (v && v.length <= 300) || 'El campo debe ser menor o igual 300 caracteres'
            ],
            oblContRules:[
                v => !!v || 'Defina si es obligado a llevar contabilidad',
            ],
            firmElectRules:[
                v => !!v || 'Debe cargar su firma electrónica para poder facturar',
            ],
            imagenRules:[
                v => !v || v.size < 100000 || 'La imagen debe pesar menos de 100kb',
            ],

        }),
        methods:{
            getImagen(event){
                console.log(event);
                this.imagen = typeof event != "undefined" ? event : ''
            },
            savePerfil(){



                if(this.$refs.form_perfil.validate())
                    return


                //this.$store.commit('setLoadingBtn')


            },

            saveAccesos(){

                if(!this.$refs.form_accesos.validate())
                    return


                this.$store.commit('setLoadingBtn2')

                var formAccesos = new FormData();
                formAccesos.append('usuario',this.user)

                if(this.actualPass !=='')
                    formAccesos.append('actualPass',this.actualPass)
                if(this.pass !=='')
                    formAccesos.append('pass',this.pass)
                if(this.imagen !=='')
                    formAccesos.append('imagen',this.imagen)

                axios.post('/perfil/guardar_accesos',
                   formAccesos
                ).then(response => {
                    console.log(response.data);
                    this.$store.commit('setLoadingBtn2')
                    this.$store.dispatch({
                        type: 'alertNotification',
                        param:{
                            html: response.data.msg,
                        }
                    });

                    /*this.$store.commit('alertSnackBar',{
                        text: 'Los accesos se han actualizado exitosamente'
                    });*/

                }).catch(error => {

                    let response = error.response;
                    console.log(response);
                    this.$store.dispatch({
                        type: 'errorRequest',
                        data : {
                            datos: response.data.errors,
                            status : response.status,
                            btn: 2
                        }
                    });
                });
            }
        },
        mounted() {
            this.user= this.usuario.nombre
        }

    }
</script>
