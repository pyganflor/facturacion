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
                                            v-model.number="ruc"
                                            :counter="13"
                                            :rules=rucRules
                                            required
                                            v-mask="'#############'"
                                    />
                                </v-col>

                                <v-col cols="12" sm="4">
                                    <v-file-input
                                            accept="image/png, image/jpeg, image/jpg"
                                            placeholder="Logo empresa"
                                            :rules=logoEmpresaRules
                                            prepend-icon="mdi-image-area"
                                            label="Logo de las facturas"
                                            @change="getLogoEmpresa"
                                    ></v-file-input>
                                </v-col>

                                <v-col
                                        cols="12"
                                        sm="8"
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
                                    <v-dialog
                                            v-model="dialogInfoAdicional"
                                            persistent
                                            max-width="900px"
                                            v-if="fileP12Save"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                    color="primary"
                                                    dark
                                                    v-bind="attrs"
                                                    v-on="on"
                                            >
                                                <v-icon>mdi-information-outline</v-icon> Info adicional
                                            </v-btn>
                                        </template>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">Datos adicionales para la facturación</span>
                                            </v-card-title>
                                            <v-container class="py-0">
                                                <v-col class="py-0" >
                                                    <v-form ref="form_datos_adicionales">
                                                        <v-row>
                                                            <v-col
                                                                    class="12"
                                                                    md="2"
                                                                    v-if="modulos.facturacion"
                                                            >
                                                                <v-text-field
                                                                        label="Nº de factura"
                                                                        v-model="secuencial.n_factura"
                                                                        required
                                                                        :rules="secuencialRule"
                                                                        type="number"
                                                                        min="0"
                                                                />
                                                            </v-col>
                                                            <v-col
                                                                    md="2"
                                                                    v-if="modulos.guiaRemision"
                                                            >
                                                                <v-text-field

                                                                        label="Nº de guía"
                                                                        v-model="secuencial.n_guia_remision"
                                                                        required
                                                                        type="number"
                                                                        min="0"
                                                                        :rules="secuencialRule"
                                                                />
                                                            </v-col>
                                                            <v-col
                                                                    class="12"
                                                                    md="3"
                                                                    v-if="modulos.notaDebito"
                                                            >
                                                                <v-text-field
                                                                        label="Nº de nota de débito"
                                                                        v-model="secuencial.n_nota_debito"
                                                                        required
                                                                        type="number"
                                                                        :rules="secuencialRule"
                                                                        min="0"
                                                                />
                                                            </v-col>
                                                            <v-col
                                                                    class="12"
                                                                    md="3"
                                                                    v-if="modulos.notaCredito"
                                                            >
                                                                <v-text-field

                                                                        label="Nº de nota de crédito"
                                                                        v-model="secuencial.n_nota_credito"
                                                                        required
                                                                        type="number"
                                                                        :rules="secuencialRule"
                                                                        min="0"
                                                                />
                                                            </v-col>
                                                            <v-col
                                                                    class="12"
                                                                    md="2"
                                                                    v-if="modulos.retencion"
                                                            >
                                                                <v-text-field

                                                                        label="Nº de retención"
                                                                        v-model="secuencial.n_retencion"
                                                                        required
                                                                        type="number"
                                                                        min="0"
                                                                        :rules="secuencialRule"
                                                                />
                                                            </v-col>
                                                        </v-row>
                                                        <v-row>
                                                            <v-col cols="12" class="py-0">
                                                                <v-switch
                                                                        class="py-0"
                                                                        v-model="entorno"
                                                                        inset
                                                                        :label="`Entorno para autorizaciones con el SRI: ${entornos[Number(entorno)].texto}`"
                                                                ></v-switch>
                                                            </v-col>
                                                        </v-row>
                                                        <v-row>
                                                            <v-col cols="12" sm="6">
                                                                <v-col cols="12">
                                                                    <v-row>
                                                                        <div class="headline col-sm-9 col-md-10">Puntos de emisión</div>
                                                                        <div class="col-sm-3 col-md-2">
                                                                            <v-btn
                                                                                    color="primary"
                                                                                    fab
                                                                                    x-small
                                                                                    @click="addNumeroEsp('pe')"
                                                                            >
                                                                                <v-icon>mdi-plus</v-icon>
                                                                            </v-btn>
                                                                        </div>
                                                                    </v-row>
                                                                </v-col>
                                                                <v-row>
                                                                    <v-col
                                                                            cols="12"
                                                                            md="4"
                                                                            sm="6"
                                                                            v-for="(pe, x) in ptoEmision"
                                                                            :key="`pto_${x}`"
                                                                            :id="`pto_${x}`"
                                                                    >
                                                                        <v-text-field
                                                                                label="Número"
                                                                                v-model="pe.numero"
                                                                                :rules="requiredRule"
                                                                                type="number"
                                                                                required
                                                                                append-icon="mdi-delete-forever"
                                                                                @click:append="removeInput(pe)"
                                                                        />
                                                                    </v-col>
                                                                </v-row>
                                                            </v-col>
                                                            <v-divider

                                                                    inset
                                                                    vertical
                                                            ></v-divider>
                                                            <v-col
                                                            >
                                                                <v-col cols="12">
                                                                    <v-row>
                                                                        <div class="headline col-sm-9 col-md-10">Números de factureros</div>
                                                                        <div class="col-sm-3 col-md-2">
                                                                            <v-btn
                                                                                    color="primary"
                                                                                    fab
                                                                                    x-small
                                                                                    @click="addNumeroEsp('fact')"
                                                                            >
                                                                                <v-icon>mdi-plus</v-icon>
                                                                            </v-btn>
                                                                        </div>
                                                                    </v-row>
                                                                </v-col>
                                                                <v-row>
                                                                    <v-col
                                                                            cols="12"
                                                                            md="4"
                                                                            sm="6"
                                                                            v-for="(fac, y) in facturero"
                                                                            :key="`fac_${y}`"
                                                                            :id="`fac_${y}`"
                                                                    >
                                                                        <v-text-field
                                                                                label="Número"
                                                                                v-model="fac.numero"
                                                                                :rules="requiredRule"
                                                                                type="number"
                                                                                required
                                                                                append-icon="mdi-delete-forever"
                                                                                @click:append="removeInput(fac)"
                                                                        />
                                                                    </v-col>
                                                                </v-row>
                                                            </v-col>
                                                        </v-row>
                                                    </v-form>
                                                </v-col>
                                            </v-container>
                                            <v-card-actions >
                                                <v-row class="text-center">
                                                    <v-col cols="12">
                                                        <v-btn
                                                                color="primary"
                                                                class="ma-2"
                                                                @click="storeInfoAdicional"
                                                                storeIn=loadingBtn3
                                                        >
                                                            <v-icon>mdi-content-save</v-icon> Actualizar
                                                        </v-btn>
                                                        <v-btn
                                                                color="secondary"
                                                                class="ma-2"
                                                                @click="dialogInfoAdicional=!dialogInfoAdicional"
                                                                :loading=loadingBtn3
                                                        >
                                                            <v-icon>mdi-cancel</v-icon> Cancelar
                                                        </v-btn>
                                                    </v-col>
                                                </v-row>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
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
                        :avatar="avatar"
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

                                <v-col cols="12" class="py-0 mt-0"

                                >
                                    <v-text-field
                                            type="email"
                                            v-model="correo"
                                            label="Correo"
                                            :rules="correoRules"
                                            required
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" class="py-0 mt-0"

                                >
                                    <v-text-field
                                            v-model="tlf"
                                            label="Teléfono"
                                            :rules="tlfRules"
                                            min="0"
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

                                <v-col cols="12" sm="12" >
                                    <v-card-actions>
                                        <v-btn
                                                color="primary"
                                                :block=true
                                                class="px-0"
                                                @click="updateAccesos"
                                                :loading=loadingBtn2
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

    import {mapState,mapMutations,mapActions} from 'vuex'

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
            secuencial:{
                n_factura:'',
                n_guia_remison:'',
                n_nota_debito:'',
                n_nota_credito:'',
                n_retencion:''
            },
            ptoEmision:[{numero:''}],
            facturero:[{numero:''}],
            entornos:[{texto:'Pruebas (Los comprobantes generados no tendrán validez)'},{texto:'Producción'}],
            entorno:false,
            contriEsp: '',
            oblContablidad : ['SI','NO'],
            obligadoContabilidad:'',
            show1:false,
            show2:false,
            show3:false,
            dialogInfoAdicional:false,
            passFileP12:'',
            pass:'',
            correo:'',
            tlf:'',
            avatar :'',
            userPerfil:'',
            loadingBtn3:false,
            logoEmpresa: '',
            fileP12: '',
            actualPass:'',
            imagen: '',
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
                v => (v && v.length == 13) || 'El ruc debe ser de 13 dígitos',
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
            requiredRule:[
                v => !!v && v.length == 3 || 'Hasta 3 dígitos',
                v => /^\d*$/.test(v) || 'El número no puede tener caracteres especiales',
            ],
            firmElectRules:[
                v => !!v || 'Debe cargar su firma electrónica para poder facturar'
            ],
            imagenRules:[
                v => !v || v.size < 100000 || 'La imagen debe pesar menos de 100kb',
            ],
            logoEmpresaRules:[
                v => !v || v.size < 500000 || 'La imagen debe pesar menos de 500kb',
            ],
            modulos:{
                facturacion:false,
                notaDebito:false,
                notaCredito: false,
                guiaRemision:false,
                retencion:false
            },
            correoRules:[
                v => !!v || 'Debe escribir un correo electrónico personal',
                v => /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'Debe escribir un correo válido'
            ],
            tlfRules:[
                v => (!v || v.length <= 10) || 'El teléfono debe ser menor o igual a 10 dígitos ',
                v => /^\d*$/.test(v) || 'El número no puede tener caracteres especiales',
            ],
            secuencialRule:[
                v => /^\d*$/.test(v) || 'Sin caracteres especiales',
                v => (!v || v.length <= 9) || 'Hasta 9 dígitos'
            ]
        }),
        computed:{
            ...mapState(['loadingBtn2']),

        },
        methods:{
            ...mapMutations(['setLoadingBtn2','setLoadingBtn']),

            ...mapActions(['alertNotification','errorRequest','httpRequest']),

            addNumeroEsp(tipo){

                if(tipo==='pe'){
                    this.ptoEmision.push({numero:''})
                }else{
                    this.facturero.push({numero:''})
                }
            },

            removeInput(item){

                var index = this.facturero.indexOf(item)

                if(index > -1){
                    if(this.facturero.length>1)
                        this.facturero.splice(index,1)
                }else{
                    index = this.ptoEmision.indexOf(item)
                    if(this.ptoEmision.length>1)
                        this.ptoEmision.splice(index,1)
                }

            },

            getImagen(event){
                if(typeof event != "undefined"){
                    this.imagen = event

                    let reader = new FileReader();
                    reader.onload = (e)=> {
                        $('div#avatar div.v-image__image').css('background-image', 'url(' + e.target.result + ')');
                    }

                    reader.readAsDataURL(event);
                }else{
                    this.imagen = ''
                    $('div#avatar div.v-image__image').css('background-image', 'url('+this.storage+'/icono_usuario.png'+')');
                }
            },

            getFileP12(event){
                this.fileP12 = typeof event != "undefined" ? event : ''
            },

            getLogoEmpresa(event){
                this.logoEmpresa = typeof event != "undefined" ? event : ''
            },

            updatePerfil(){

                if(!this.$refs.form_perfil.validate())
                    return

                this.setLoadingBtn()

                let formPerfil = new FormData();
                formPerfil.append('razonSocial',this.razonSocial)
                formPerfil.append('nombreComercial',this.nombreComercial)
                formPerfil.append('ruc',this.ruc)
                formPerfil.append('dirMatriz',this.dirMatriz)
                formPerfil.append('dirEstablecimiento',this.dirEstablecimiento)
                formPerfil.append('contriEsp',this.contriEsp)
                formPerfil.append('obligadoContabilidad',this.obligadoContabilidad)
                formPerfil.append('fileP12',this.fileP12)
                if(this.logoEmpresa != "")
                    formPerfil.append('logoEmpresa',this.logoEmpresa)
                formPerfil.append('passFileP12',this.passFileP12)

                axios.post('/perfil/update_perfil',
                    formPerfil
                ).then(response => {

                    this.setLoadingBtn()

                    this.alertNotification({
                        param:{
                            html: response.data.msg
                        }
                    });

                    this.fileP12Save = true
                    this.perfil.empresa_firma = response.data.empresaFirma
                    this.perfil.nombre_firma = response.data.nombreFirma
                    this.perfil.firma_desde = response.data.firmaDesde
                    this.perfil.firma_hasta = response.data.firmaHasta

                }).catch(error => {

                    let response = error.response;
                    this.errorRequest({
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

                this.setLoadingBtn2()

                let formAccesos = new FormData();
                formAccesos.append('usuario',this.user)
                formAccesos.append('correo',this.correo)

                if(this.actualPass !=='')
                    formAccesos.append('actualPass',this.actualPass)
                if(this.pass !=='')
                    formAccesos.append('pass',this.pass)
                if(this.imagen !=='')
                    formAccesos.append('imagen',this.imagen)
                if(this.tlf !=='')
                    formAccesos.append('tlf',this.tlf)


                axios.post('/perfil/update_accesos',
                   formAccesos
                ).then(response => {

                    console.log(response.data);
                    this.setLoadingBtn2()
                    this.alertNotification({
                        param:{
                            html: response.data.msg
                        }
                    });

                }).catch(error => {

                    let response = error.response;


                    this.errorRequest({
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
            },

            storeInfoAdicional(){

                if(!this.$refs.form_datos_adicionales.validate())
                    return

                let infoAdicional={
                    id_usuario : this.usuario.id_usuario,
                    entorno: this.entorno
                }

                if(this.secuencial.n_factura.length>0)
                    infoAdicional.n_factura = this.secuencial.n_factura
                if(this.secuencial.n_guia_remision.length>0)
                    infoAdicional.n_guia_remision = this.secuencial.n_guia_remision
                if(this.secuencial.n_nota_credito.length>0)
                    infoAdicional.n_nota_credito = this.secuencial.n_nota_credito
                if(this.secuencial.n_nota_debito.length>0)
                    infoAdicional.n_nota_debito = this.secuencial.n_nota_debito
                if(this.secuencial.n_retencion.length>0)
                    infoAdicional.n_retencion = this.secuencial.n_retencion

                infoAdicional.factureros = this.facturero
                infoAdicional.ptoEmision = this.ptoEmision

                this.httpRequest({

                    method:'post',
                    url:'perfil/store_info_adicional',
                    data:infoAdicional

                }).then((res)=>{

                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })

                    this.usuario.perfil = res.data.perfil

                    this.dialogInfoAdicional=false

                })

            }
        },
        mounted() {

            this.user= this.usuario.nombre

            this.avatar=this.storage+'/icono_usuario.png'

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
            if(typeof this.perfil.oblig_cont !=="undefined" && this.perfil.oblig_cont !=null)
                this.obligadoContabilidad = this.perfil.oblig_cont
            if(typeof this.perfil.pass_firma_elec !=="undefined")
                this.fileP12Save = true
            if(typeof this.perfil.firma_elec !=="undefined")
                this.passFileP12Save = this.perfil.firma_elec
            if(typeof this.usuario.correo !== "undefined")
                this.correo = this.usuario.correo
            if(typeof this.usuario.tlf !=="undefined" && this.usuario.tlf!=null)
                this.tlf = this.usuario.tlf
            if(typeof this.usuario.imagen !="undefined")
                this.avatar = this.storage+'/'+this.usuario.imagen

            for(let modulo of this.usuario.modulos){
                this.modulos.facturacion = this.modulos.facturacion || modulo.id_modulo === 1
                this.modulos.guiaRemision = this.modulos.guiaRemision || modulo.id_modulo === 2
                this.modulos.notaDebito = this.modulos.notaDebito || modulo.id_modulo === 3
                this.modulos.notaCredito = this.modulos.notaCredito || modulo.id_modulo === 4
                this.modulos.retencion = this.modulos.retencion || modulo.id_modulo === 5
            }

            this.secuencial.n_factura = this.usuario.perfil.n_factura
            this.secuencial.n_guia_remision = this.usuario.perfil.n_guia_remision
            this.secuencial.n_nota_credito = this.usuario.perfil.n_nota_credito
            this.secuencial.n_nota_debito = this.usuario.perfil.n_nota_debito
            this.secuencial.n_retencion = this.usuario.perfil.n_retencion
            this.entorno= this.usuario.perfil.entorno === 2

            if(this.usuario.factureros.length>0)
                this.facturero = this.usuario.factureros

            if(this.usuario.pto_emision.length>0)
                this.ptoEmision = this.usuario.pto_emision

            if(!!this.usuario.perfil)
                this.existPerfil = true
        }

    }
</script>
