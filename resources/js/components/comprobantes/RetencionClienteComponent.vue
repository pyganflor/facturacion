<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-alert
                color="primary"
                dark
                icon="mdi-percent"
                border="left"
                dense
        >
            En esta sección puede realizar las diferentes acciones con las retenciones recibidas de sus clientes
        </v-alert>
        <v-data-table
                :headers="headers"
                :items="dataTable"
                sort-by="calories"
                class="elevation-1"
                :loading=loadingTable
                :search=search
                loading-text="Buscando retenciones de clientes..."
                dense
        >
            <template v-slot:top>
                <v-col class="py-0" cols="12">
                    <v-form ref="form_factura">
                        <v-row>
                            <v-col
                                    class="py-0"
                                    cols="12"
                                    md="3"
                                    sm="6"
                            >
                                <v-menu>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                                v-model="dateRangeText"
                                                label="Fechas"
                                                persistent-hint
                                                readonly
                                                append-icon="mdi-calendar-multiple"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"

                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                            range
                                            v-model="dates"
                                            no-title
                                            @input="menu2 = false"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col
                                    style="margin-top: 2.3px;"
                                    class="py-0"
                                    cols="12"
                                    md="3"
                                    sm="6">
                                <v-select
                                        class="mt-5"
                                        :items="clientes"
                                        label="Clientes"
                                        item-text="nombre"
                                        item-value="id_cliente"
                                        v-model="idClienteSearch"
                                        clearable
                                        dense
                                        @change="getDataComponent"
                                >
                                </v-select>
                            </v-col>
                            <v-col
                                    class="py-0"
                                    cols="12"
                                    md="3"
                                    sm="6">
                                <v-text-field
                                        v-model="search"
                                        append-icon="mdi-magnify"
                                        label="Buscar"
                                        single-line
                                        hide-details
                                ></v-text-field>
                            </v-col>
                            <v-col
                                    class="py-0"
                                    cols="12"
                                    md="2"
                                    sm="6">
                                <v-select
                                        style="padding-top: 2.4px"
                                        class="mt-5"
                                        :items="estados"
                                        label="Estados"
                                        item-text="nombre"
                                        item-value="id"
                                        v-model="estado"
                                        dense
                                        @change="getDataComponent"
                                >
                                </v-select>
                            </v-col>
                            <v-col
                                    class="py-0 btn-comprobante text-md-center pt-md-3"
                                    cols="12"
                                    md="1"
                                    sm="12">
                                <v-dialog
                                        v-model="dialog"
                                        fullscreen
                                        hide-overlay
                                        transition="dialog-bottom-transition"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                                color="primary"
                                                fab small
                                                v-bind="attrs"
                                                v-on="on"
                                                title="Registrar retención"
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-card>
                                        <v-toolbar style="height: 46px" dark color="primary">
                                            <v-btn  class="pb-4" icon dark @click="dialog = false">
                                                <v-icon>mdi-close</v-icon>
                                            </v-btn>
                                            <v-toolbar-title class="pb-4" >
                                                <v-icon>mdi-percent</v-icon> Registrar retención de cliente
                                            </v-toolbar-title>
                                            <v-spacer></v-spacer>
                                        </v-toolbar>
                                        <v-container
                                                fluid
                                        >
                                            <v-tabs
                                                    v-model="tab"
                                                    background-color="primary"
                                                    class="elevation-2"
                                                    dark
                                                    :centered=true
                                                    :grow=true
                                            >
                                                <v-tab href="#tab-1">
                                                    <v-icon >mdi-pencil</v-icon>
                                                    Manual
                                                </v-tab>
                                                <v-tab href="#tab-2">
                                                    <v-icon >mdi-package-up</v-icon>
                                                     Asistente
                                                </v-tab>
                                                <v-tab-item value="tab-1">
                                                    <form-retencion-cliente
                                                        :facturas=facturas
                                                        :clientes=clientes
                                                        :conceptos=conceptos
                                                    />
                                                </v-tab-item>
                                                <v-tab-item value="tab-2">
                                                    <v-card
                                                            flat
                                                            tile
                                                           color="grey lighten-4"
                                                    >
                                                        <v-container fluid>
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-alert
                                                                            color="primary"
                                                                            border="left"
                                                                            elevation="2"
                                                                            colored-border
                                                                            icon="mdi-file-check"
                                                                            dense
                                                                    >
                                                                        Puede cargar el archivo .xml de la retención del cliente o cargar el archivo .txt descargado desde la web del SRI
                                                                    </v-alert>
                                                                </v-col>
                                                                <v-col cols="12" class="py-0">
                                                                    <v-row>
                                                                        <v-col
                                                                                cols="12"
                                                                                md="6"
                                                                                class="py-0"
                                                                        >
                                                                            <v-file-input
                                                                                    show-size
                                                                                    counter
                                                                                    label="Carga el archivo .xml"
                                                                                    accept=".xml"
                                                                                    append-icon="mdi-send"
                                                                                    :rules="xmlRules"
                                                                                    @change="readXml"
                                                                                    @click:append="procesarXml"
                                                                            ></v-file-input>
                                                                        </v-col>
                                                                        <v-col
                                                                                cols="12"
                                                                                md="6"
                                                                                class="py-0"
                                                                        >
                                                                            <v-file-input
                                                                                    show-size
                                                                                    counter
                                                                                    label="Carga el archivo .txt"
                                                                                    accept=".txt"
                                                                                    append-icon="mdi-send"
                                                                                    :rules="txtRules"
                                                                                    @change="readTxt"
                                                                                    @click:append="procesarTxt"
                                                                            ></v-file-input>
                                                                        </v-col>
                                                                    </v-row>
                                                                </v-col>
                                                            </v-row>
                                                        </v-container>
                                                    </v-card>
                                                </v-tab-item>
                                            </v-tabs>
                                        </v-container>
                                    </v-card>
                                </v-dialog>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-col>
            </template>
            <template v-slot:no-results="{ item }">
                No se encontraron retenciones registradas
            </template>
            <template v-slot:no-data>
                Sin retenciones registradas
            </template>
  <!--          <template v-slot:item.total="{ item }">
                <b>${{parseFloat(item.total).toFixed(2)}}</b>
            </template>
            &lt;!&ndash;<template v-slot:item.entorno="{ item }">
                {{item.entorno === 1 ? 'Pruebas' : 'Producción'}}
            </template>&ndash;&gt;
            <template v-slot:item.estado="{ item }">
                {{estados.find(e => e.id === item.estado).nombre}}
            </template>
            <template v-slot:item.causa="{ item }">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs, item }">
                        <v-btn
                                color="secondary"
                                dark
                                text
                                x-small
                                v-bind="attrs"
                                v-on="on"
                        >
                            <v-icon>mdi-message-text-outline</v-icon>
                        </v-btn>
                    </template>
                    <div style="background: black" class="px-2">{{item.causa}}</div>
                </v-tooltip>
            </template>
            <template v-slot:item.clave_acceso="{ item }">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                                color="secondary"
                                dark
                                text
                                x-small
                                v-bind="attrs"
                                v-on="on"
                                @click="copyClaveAcceso(item.clave_acceso)"
                        >
                            <v-icon>mdi-eye</v-icon>
                        </v-btn>
                    </template>
                    <span :id="`clave_acceso_${item.clave_acceso}`">{{item.clave_acceso}}</span>
                </v-tooltip>
            </template>-->
            <template v-slot:item.actions="{ item }">
                <v-menu
                        transition="slide-y-transition"
                        bottom
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                                color="secondary"
                                v-bind="attrs"
                                v-on="on"
                        >
                            mdi-menu
                        </v-icon>
                    </template>
                    <v-list>
                        <!--<v-list-item class="list-actions" v-if="item.estado === 1">
                            <v-list-item-title>
                                <v-btn text small >
                                    <a :href="`/factura/comprobante/${item.id_factura}`" target="_blank" class="link">
                                        <v-icon color="success">mdi-file-pdf</v-icon> Visualizar
                                    </a>
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>-->
                    </v-list>
                </v-menu>
            </template>
        </v-data-table>
    </div>
</template>

<script>

    import {mapActions,mapMutations,mapState} from 'vuex'

    export default {
        props: {
            clientes: {
                type: Array,
                required:true
            },
            facturas: {
                type: Array,
                required:true
            },
            conceptos:{
                type: Array,
                required:true
            }
        },
        name: "RetencionCliente",
        data: ()=> ({
            dates: [
                new Date().toISOString().substr(0, 10),
                new Date().toISOString().substr(0, 10)
            ],
            headers:[
                { text: 'F. retención', value: 'f_retencion' },
                { text: 'Secuencial', value: 'secuencial', align: 'center' },
                { text: 'Cliente', value: 'cliente' },
                { text: 'N° factura', value: 'fecha_aut'},
                { text: 'T. iva', value: 't_iva' },
                { text: 'T. renta', value: 't_renta', align: 'center'  },
                { text: 'Total', value: 'total', align: 'center'  },
                { text: 'Estado', value: 'estado' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            dataTable:[],
            estados:[
                {id:0, nombre: 'Anulada'},
                {id:1, nombre: 'Recibida'}
            ],
            txtRules:[
                v => !v || (!!v && v.name.split(".")[v.name.split(".").length-1]== "txt") || 'Debe cargar un archivo .txt'
            ],
            xmlRules:[
                v => !v || (!!v && v.name.split(".")[v.name.split(".").length-1]== "xml") || 'Debe cargar un archivo .xml'
            ],
            overlay:false,
            tab:null,
            txt:null,
            xml:null,
            idCliente:null,
            loadingTable:false,
            search:'',
            dialog:false,
            idClienteSearch: '',
            estado: 1
        }),
        watch:{
            dates : function () {
                if(this.dateRangeText.split('~').length===2)
                    this.getDataComponent()
            },
        },
        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
            ...mapState(['loadingBtn','paramsAlertQuestion']),
        },
        methods:{
            ...mapActions(['httpRequest','alertNotification','errorRequest']),

            ...mapMutations(['setLoadingBtn']),

            getDataComponent(){
                this.loadingTable = true
                this.dataTable=[]
                this.httpRequest({
                    method: 'get',
                    url: 'retencion_cliente/list',
                    data: {
                        estado : this.estado,
                        fechas: this.dateRangeText,
                        id_cliente : this.idClienteSearch
                    }
                }).then((res) => {
                    this.dataTable=res.data
                    this.loadingTable = false
                })
            },

            readTxt(event){
                this.txt = typeof event != "undefined" ? event : null
            },

            readXml(event){
                this.xml = typeof event != "undefined" ? event : null
            },

            procesarTxt(){

                let formTxt = new FormData();
                formTxt.append('txt',this.txt)

                axios.post('/retencion_cliente/procesar_txt',
                    formTxt
                ).then(response => {

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
                            status : response.status
                        }
                    });

                });
            },

            procesarXml(){
                let formXml = new FormData();
                formXml.append('xml',this.xml)

                axios.post('/retencion_cliente/procesar_xml',
                    formXml
                ).then(response => {

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
                            status : response.status
                        }
                    });

                });
            },



            storeRetencionAsistido(){

            }
        },
        created(){
            this.getDataComponent()
        }
    }
</script>

<style scoped>

</style>