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

                                <v-col cols="12" md="6">
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
                                            :items="oblContablidad"
                                            label="Obligado a llevar contabilidad"
                                            :rules=oblContRules
                                            v-model="obligadoContabilidad"
                                            requied
                                    ></v-select>
                                </v-col>

                                <v-col cols="12" md="6" class="py-0 mt-0">
                                    <v-file-input
                                            accept=".P12"
                                            placeholder="Seleccione un archivo .p12"
                                            prepend-icon="mdi-file-lock"
                                            label="Firma electrónica"
                                            @change="getFileP12"
                                            :append-icon="fileP12Save != null ? 'mdi-eye' : ''"
                                            required
                                            @click:append="dataP12"
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12" md="6" class="py-0 mt-0">
                                    <v-text-field
                                            :append-icon="show3 ? 'mdi-eye' : 'mdi-eye-off'"
                                            :type="show3 ? 'text' : 'password'"
                                            label="Constraseña de la firma electrónica"
                                            v-model="passFileP12"
                                            class="input-group--focused"
                                            @click:append="show3 = !show3"
                                            required
                                    ></v-text-field>
                                </v-col>

                                <v-col
                                        cols="12"
                                        class="text-center"
                                >
                                    <v-btn
                                            color="primary"
                                            class="mr-0"
                                            @click="updatePerfil"
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
                    class="mt-5 mt-md-0"
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
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12" class="py-0 mt-0">
                                    <v-file-input
                                            accept="image/png, image/jpeg, image/jpg"
                                            placeholder="Imagen de empresa"
                                            prepend-icon="mdi-image-area"
                                            label="Logo de las facturas"
                                            @change="getImagenEmpresa"
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12" sm="12" >
                                    <v-card-actions>
                                        <v-btn
                                                color="primary"
                                                :block=true
                                                class="px-0"
                                                @click="updateAccesos"
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
                required:true,
                type: Object
            },
            perfil:{
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
            oblContablidad : ['SI','NO'],
            obligadoContabilidad:'',
            show1:false,
            show2:false,
            show3:false,
            passFileP12:'',
            pass:'',
            userPerfil:'',
            actualPass:'',
            imagen: '',
            fileP12: '',
            user:'',
            fileP12Save:null,
            passFileP12Save:null,
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
                v => !!v || 'Debe cargar su firma electrónica para poder facturar'
            ],
            imagenRules:[
                v => !v || v.size < 100000 || 'La imagen debe pesar menos de 100kb',
            ],
        }),
        methods:{
            getImagen(event){
                this.imagen = typeof event != "undefined" ? event : ''
            },

            getFileP12(event){
                this.fileP12 = typeof event != "undefined" ? event : ''
            },

            getImagenEmpresa(event){

            },

            updatePerfil(){

                if(!this.$refs.form_perfil.validate())
                    return

                this.$store.commit('setLoadingBtn')

                let formPerfil = new FormData();
                formPerfil.append('razonSocial',this.razonSocial)
                formPerfil.append('nombreComercial',this.nombreComercial)
                formPerfil.append('ruc',this.ruc)
                formPerfil.append('dirMatriz',this.dirMatriz)
                formPerfil.append('dirEstablecimiento',this.dirEstablecimiento)
                formPerfil.append('contriEsp',this.contriEsp)
                formPerfil.append('obligadoContabilidad',this.obligadoContabilidad)
                formPerfil.append('fileP12',this.fileP12)
                formPerfil.append('passFileP12',this.passFileP12)

                axios.post('/perfil/update_perfil',
                    formPerfil
                ).then(response => {
                    console.log(response.data);
                    this.$store.commit('setLoadingBtn')
                    this.$store.dispatch({
                        type: 'alertNotification',
                        param:{
                            html: response.data.msg
                        }
                    });

                }).catch(error => {

                    let response = error.response;
                    console.log(response,error);

                    this.$store.dispatch({
                        type: 'errorRequest',
                        data : {
                            datos: response.data.errors,
                            status : response.status
                        }
                    });
                });


            },

            updateAccesos(){

                if(!this.$refs.form_accesos.validate())
                    return

                this.$store.commit('setLoadingBtn2')

                let formAccesos = new FormData();
                formAccesos.append('usuario',this.user)

                if(this.actualPass !=='')
                    formAccesos.append('actualPass',this.actualPass)
                if(this.pass !=='')
                    formAccesos.append('pass',this.pass)
                if(this.imagen !=='')
                    formAccesos.append('imagen',this.imagen)

                axios.post('/perfil/update_accesos',
                   formAccesos
                ).then(response => {
                    console.log(response.data);
                    this.$store.commit('setLoadingBtn2')
                    this.$store.dispatch({
                        type: 'alertNotification',
                        param:{
                            html: response.data.msg
                        }
                    });

                }).catch(error => {

                    let response = error.response;
                    console.log(response,error);

                    this.$store.dispatch({
                        type: 'errorRequest',
                        data : {
                            datos: response.data.errors,
                            status : response.status,
                            btn:2
                        }
                    });
                });
            },

            dataP12(){

                let html ="<div>" +
                            "<p><b>EXPIDIDO POR:</b> "+this.perfil.empresa_firma+"</p>" +
                            "<p><b>PROPIETARIO:</b> "+this.perfil.nombre_firma+"</p>" +
                            "<p><b>VÁLIDO DESDE:</b> "+this.perfil.firma_desde+"</p>" +
                            "<p><b>VÁLIDO HASTA:</b> "+this.perfil.firma_hasta+"</p>" +
                         "</div>";

                Vue.swal({
                    title:'Firma electrónica',
                    html: html,
                    iconHtml:'<img src="/imagenes/llave_electronica3.png" style="width:50px">',
                    icon: 'info',
                    timerProgressBar : true,
                    timer: 20000,
                    position: 'top',
                    showConfirmButton:false,
                    showCloseButton: true,
                    closeButtonHtml:'<span class="mdi mdi-close"></span>',
                });
            }
        },
        mounted() {
            this.user= this.usuario.nombre
            console.log(this.perfil);
            if(typeof this.perfil.razon_social !=="undefined")
                this.razonSocial = this.perfil.razon_social
            if(typeof this.perfil.nombre_comercial !=="undefined")
                this.nombreComercial = this.perfil.nombre_comercial
            if(typeof this.perfil.ruc !=="undefined")
                this.ruc = this.perfil.ruc
            if(typeof this.perfil.direc_matriz !=="undefined")
                this.dirMatriz = this.perfil.direc_matriz
            if(typeof this.perfil.direc_establecimiento !=="undefined")
                this.dirEstablecimiento = this.perfil.direc_establecimiento
            if(typeof this.perfil.contri_esp !=="undefined")
                this.contriEsp = this.perfil.contri_esp
            if(typeof this.perfil.oblig_cont !=="undefined")
                this.obligadoContabilidad = this.perfil.oblig_cont
            if(typeof this.perfil.pass_firma_elec !=="undefined")
                this.fileP12Save = this.perfil.pass_firma_elec
            if(typeof this.perfil.firma_elec !=="undefined")
                this.passFileP12Save = this.perfil.firma_elec
        }

    }
</script>
