<template>
    <v-card
            flat
            tile
            color="grey lighten-4"
    >
        <v-container fluid>
            <v-row>
                <v-col
                        cols="12"
                        md="6"
                >
                    <v-alert
                            color="primary"
                            border="left"
                            elevation="2"
                            colored-border
                            icon="mdi-file-document"
                            dense
                    >
                        Datos de la retención
                    </v-alert>
                    <v-col cols="12" class="py-0" >
                        <v-text-field
                                v-model="claveAcceso"
                                label="Clave de acceso"
                                append-icon="mdi-numeric"
                                :rules="requiredRule"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" class="py-0" >
                        <v-text-field
                                v-model="nAutorizacion"
                                label="Número de autorización"
                                append-icon="mdi-numeric"
                                :rules="requiredRule"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" class="py-0">
                        <v-row>
                            <v-col
                                    class="py-0"
                                    cols="12"
                                    sm="6"
                            >
                                <v-menu>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                                v-model="fechaDoc"
                                                label="Fecha del documento"
                                                persistent-hint
                                                readonly
                                                append-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"

                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                            v-model="fechaDoc"
                                            no-title
                                            @input="menu2 = false"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col
                                    class="py-0"
                                    cols="12"
                                    sm="6"
                            >
                                <v-menu>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                                v-model="fechaCont"
                                                label="Fecha de contabilidad"
                                                persistent-hint
                                                readonly
                                                append-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"

                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                            v-model="fechaCont"
                                            no-title
                                            @input="menu2 = false"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-col>
                <v-col
                        cols="12"
                        md="6"
                >
                    <v-alert
                            color="primary"
                            border="left"
                            elevation="2"
                            colored-border
                            icon="mdi-account-convert"
                            dense
                    >
                        Datos del cliente
                    </v-alert>
                    <v-col cols="12" class="py-0" >
                        <v-row>
                            <v-col cols="12" class="py-0">
                                <v-autocomplete
                                        class="mt-5"
                                        :items="clientes"
                                        label="Cliente"
                                        item-text="nombre"
                                        item-value="id_cliente"
                                        :rules="requiredRule"
                                        v-model="idCliente"
                                        dense
                                >
                                    <template slot='selection' slot-scope='{ item }'>
                                        {{ item.identificacion }} - {{ item.nombre }}
                                    </template>
                                    <template slot='item' slot-scope='{ item }'>
                                        {{ item.identificacion }} - {{ item.nombre }}
                                    </template>
                                    <template v-slot:no-data>
                                        <div class="ml-2">
                                            <v-icon>mdi-alert-circle-outline</v-icon> Sin clientes
                                        </div>
                                    </template>
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" class="py-0">
                                <v-autocomplete
                                        class="mt-5"
                                        :items="facturas"
                                        label="Factura"
                                        item-text="secuencial"
                                        item-value="id_factura"
                                        :rules="requiredRule"
                                        v-model="idFactura"
                                        dense
                                >
                                    <template v-slot:no-data>
                                        <div class="ml-2">
                                            <v-icon>mdi-alert-circle-outline</v-icon> Sin facturas
                                        </div>
                                    </template>
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" class="py-0" >
                        <v-text-field
                                v-model="comentario"
                                label="Comentario"
                                counter="200"
                                :rules=comentarioRule
                                append-icon="mdi-comment-text-outline"
                        ></v-text-field>
                    </v-col>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-alert
                            color="primary"
                            border="left"
                            elevation="2"
                            colored-border
                            icon="mdi-package-variant-closed"
                            dense
                    >
                        <span style="position: relative;top:4px">Items retenidos</span>
                        <div class="float-right">
                            <v-btn
                                    fab
                                    color="secondary"
                                    title="Agregar retención"
                                    @click="addItemRetencion"
                                    x-small
                            >
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </div>
                    </v-alert>
                </v-col>
                <v-col>
                    <v-simple-table
                            dense
                            class="table-bordered"
                    >
                        <template v-slot:default>
                            <thead>
                            <tr class="primary">
                                <th class="white--text" style="width: 20px;">N°</th>
                                <th class="white--text" style="width: 600px;">Concepto</th>
                                <th class="white--text">Base imponible</th>
                                <th class="white--text" >Porcentaje</th>
                                <th class="white--text" >Valor retenido</th>
                                <th class="text-center white--text" style="width: 50px;">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                    v-for="(item ,x) in itemsRetencion"
                                    :key="x"
                            >
                                <td>{{(x+1)}})</td>
                                <td style="width: 200px;">
                                    <v-autocomplete
                                            class="py-0"
                                            :items="conceptos"
                                            item-text="nombre"
                                            item-value="id_detalle_impuesto_retencion"
                                            v-model="item.id_concepto_retencion"
                                            :multiple=false
                                            @change="setTotal()"
                                            :single-line=true
                                            dense
                                    >
                                    </v-autocomplete>
                                </td>
                                <td style="width: 100px;">
                                    <v-text-field
                                            class="py-0 text-center"
                                            v-model.number="item.base_imponible"
                                            type="number"
                                            min="0"
                                            :rules="requiredRule"
                                            @keyup="setTotal()"
                                            @change="setTotal()"
                                    ></v-text-field>
                                </td>
                                <td style="width: 100px;">
                                    <v-text-field
                                            class="py-0 text-center"
                                            v-model.number="item.porcentaje"
                                            type="number"
                                            min="0"
                                            :readonly=true
                                            :rules="requiredRule"
                                            @keyup="setTotal()"
                                            @change="setTotal()"
                                    ></v-text-field>
                                </td>
                                <td style="width: 100px;">
                                    <v-text-field
                                            class="py-0 text-center"
                                            v-model="item.valor_retenido"
                                            type="number"
                                            min="0"
                                            :readonly=true
                                            @keyup="setTotal()"
                                            @change="setTotal()"
                                            :rules="requiredRule"
                                    ></v-text-field>
                                </td>
                                <td class="text-center" style="width: 100px;">
                                    <v-btn
                                            fab
                                            x-small
                                            color="warning"
                                            title="Eliminar item"
                                            @click="removeItemRetencion(item)"
                                    >
                                        <v-icon>mdi-delete-forever</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right"><b>Total:</b></td>
                                <td class="text-center"><b>${{totalBi.toFixed(2)}}</b></td>
                                <td></td>
                                <td class="text-center"><b>${{totalVr.toFixed(2)}}</b></td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <v-col
                            cols="12"
                            class="text-center"
                    >
                        <v-btn
                                dark
                                @click="storeRetencionManual"
                                color="primary"
                        >
                            <v-icon>mdi-content-save</v-icon>
                            <span class="d-none d-md-block">Procesar</span>
                        </v-btn>
                    </v-col>
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>

<script>
    import {mapActions} from 'vuex'

    export default {
        name: "FormRetencionCliente",
        props:{
            conceptos:{
                type: Array,
                required: true
            },
            facturas:{
                type: Array,
                required: true
            },
            clientes:{
                type: Array,
                required: true
            },
        },
        data : () => ({
            claveAcceso:null,
            nAutorizacion:null,
            fechaDoc: new Date().toISOString().substr(0, 10),
            fechaCont: new Date().toISOString().substr(0, 10),
            comentario:null,
            idFactura:null,
            idCliente:null,
            totalBi:0,
            totalVr:0,
            itemsRetencion:[],
            comentarioRule:[
                v=> !v || (!!v && v.length<=200) || "Sólo hasta 200 caracteres"
            ],
            requiredRule:[
                v => !!v || 'Campo obligatorio'
            ],
        }),
        methods:{

            ...mapActions(['httpRequest']),

            addItemRetencion(){
                this.itemsRetencion.push({
                    codigo_retencion:'',
                    id_concepto_retencion:'',
                    base_imponible:'0',
                    porcentaje:'0',
                    valor_retenido:'0',
                })
            },

            removeItemRetencion(item){
                let index = this.itemsRetencion.indexOf(item)
                this.itemsRetencion.splice(index ,1)
                this.setTotal()
            },

            setTotal(){
                let i=0
                this.totalBi=0
                this.totalVr=0
                for(let retencion of this.itemsRetencion){
                    let impuesto = this.conceptos.find(e => e.id_detalle_impuesto_retencion == retencion.id_concepto_retencion);
                    let porcentaje = parseFloat(impuesto.porcentaje)
                    let baseImponible = isNaN(parseFloat(retencion.base_imponible)) ? 0 : parseFloat(retencion.base_imponible)
                    this.itemsRetencion[i].porcentaje = porcentaje
                    this.itemsRetencion[i].codigo_retencion = impuesto.codigo
                    let valorRetenido = baseImponible*(porcentaje/100)
                    this.itemsRetencion[i].valor_retenido = valorRetenido.toFixed(2)
                    this.totalBi += baseImponible
                    this.totalVr += valorRetenido
                    i++
                }
            },

            storeRetencionManual(){
                this.loadingTable = true
                this.httpRequest({
                    method: 'post',
                    url: 'retencion_cliente/store_retencion_manual',
                    data: {
                        nAutorizacion : this.nAutorizacion,
                        claveAcceso: this.claveAcceso,
                        fechaDoc : this.fechaDoc,
                        fechaCont : this.fechaCont,
                        idCliente:  this.idCliente,
                        idFactura : this.idFactura,
                        comentario : this.comentario,
                        retenciones : this.itemsRetencion
                    }
                }).then((res) => {
                    /*this.dataTable=res.data
                    this.loadingTable = false*/
                })
            },
        },
        created(){
            this.addItemRetencion()
        }
    }
</script>

<style scoped>

</style>