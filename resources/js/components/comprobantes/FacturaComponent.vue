<template>
    <v-data-table
            :headers="headers"
            :items="desserts"
            sort-by="calories"
            class="elevation-1"
            dense
    >
        <template v-slot:top>
            <v-col class="py-0" cols="12">
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
                            md="4"
                            sm="6">
                        <v-select
                                class="mt-5"
                                :items="clientes"
                                label="Clientes"
                                item-text="nombre"
                                item-value="id_cliente"
                                v-model="idCliente"
                                clearable
                                dense
                        >
                        </v-select>
                    </v-col>
                    <v-col
                            class="py-0"
                            cols="12"
                            md="4"
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
                                        title="Nueva factura"
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
                                        <v-icon>mdi-file-document</v-icon> Nueva factura
                                    </v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-toolbar-items class="pb-4" >
                                        <v-btn dark text @click="dialog = false">
                                            <v-icon>mdi-check-all</v-icon> Generar
                                        </v-btn>
                                    </v-toolbar-items>
                                </v-toolbar>
                                <v-container fluid>
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-row class="py-0">
                                                <v-col cols="12" class="py-0">
                                                    <v-alert
                                                            color="primary"
                                                            border="left"
                                                            elevation="2"
                                                            colored-border
                                                            icon="mdi-file-document"
                                                            dense
                                                    >
                                                        Datos de la factura
                                                    </v-alert>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6" md="3">
                                                    <v-select
                                                            class="mt-5"
                                                            :items="punto_emision"
                                                            label="Punto de emisión"
                                                            item-text="numero"
                                                            item-value="id_pto_emision"
                                                            v-model="puntoEmision"
                                                            dense
                                                    ></v-select>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6" md="3">
                                                    <v-select
                                                            class="mt-5"
                                                            :items="factureros"
                                                            label="Facturero"
                                                            item-text="numero"
                                                            item-value="numero"
                                                            v-model="facturero"
                                                            dense
                                                    ></v-select>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6" md="3">
                                                    <v-menu>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                    v-model="fechaDocumento"
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
                                                                range
                                                                v-model="dates"
                                                                no-title
                                                                @input="menu = false"
                                                        ></v-date-picker>
                                                    </v-menu>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6" md="3">
                                                    <v-menu>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                    v-model="fechaVencimiento"
                                                                    label="Fecha de vencimiento"
                                                                    persistent-hint
                                                                    readonly
                                                                    append-icon="mdi-calendar"
                                                                    readonly
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                            ></v-text-field>
                                                        </template>
                                                        <v-date-picker
                                                                range
                                                                v-model="dates"
                                                                no-title
                                                                @input="menu3 = false"
                                                        ></v-date-picker>
                                                    </v-menu>
                                                </v-col>
                                                <v-col cols="12" class="py-0" sm="4">
                                                    <v-select
                                                            class="mt-5"
                                                            :items="sustento_tributario"
                                                            label="Sustento tributario"
                                                            item-text="nombre"
                                                            item-value="id_sustento_tributario"
                                                            v-model="idSustentoTributario"
                                                            dense
                                                    ></v-select>
                                                </v-col>
                                                <v-col cols="12" class="py-0" sm="8">
                                                    <v-text-field
                                                            v-model="comentario"
                                                            label="Comentario"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-row class="py-0">
                                                <v-col cols="12" class="py-0">
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
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6">
                                                    <v-select
                                                            class="mt-5"
                                                            :items="clientes"
                                                            label="Cliente"
                                                            item-text="nombre"
                                                            item-value="id_cliente"
                                                            v-model="idCliente"
                                                            @change="setInfo"
                                                            dense
                                                    ></v-select>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6">
                                                    <v-select
                                                            class="mt-5"
                                                            :items="tipos_pago"
                                                            label="Tipo de pago"
                                                            item-text="nombre"
                                                            item-value="id_tipo_pago"
                                                            v-model="idTipoPago"
                                                            dense
                                                    ></v-select>
                                                </v-col>
                                                <v-col cols="12" class="pb-0" sm="6">
                                                    <v-text-field
                                                            style="margin-top: -2px"
                                                            v-model="correos"
                                                            label="Correos (separados por coma)"
                                                            append-icon="mdi-email-outline"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-alert
                                                    color="primary"
                                                    border="left"
                                                    elevation="2"
                                                    colored-border
                                                    icon="mdi-package-variant-closed"
                                                    dense
                                            >
                                                Productos de venta
                                                <div class="float-right">
                                                    <v-btn
                                                            fab
                                                            color="secondary"
                                                            title="Agregar artículo a la factura"
                                                            @click="addArticulo"
                                                            x-small
                                                    >
                                                        <v-icon>mdi-plus</v-icon>
                                                    </v-btn>
                                                    <v-btn
                                                        fab
                                                        color="primary"
                                                        title="Crear nuevo producto"
                                                        x-small
                                                    >
                                                        <v-icon>mdi-package-up</v-icon>
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
                                                    <tr>
                                                        <th style="width: 20px;">N°</th>
                                                        <th style="width: 400px;">Categoría</th>
                                                        <th style="width: 400px;">Artículo</th>
                                                        <th >Cantidad</th>
                                                        <th >Precio U.</th>
                                                        <th >Precio T.</th>
                                                        <th class="text-center" style="width: 50px;">Acción</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(art ,x) in articulosFactura" :key="x">
                                                        <td>{{(x+1)}})</td>
                                                        <td style="width: 400px;">
                                                            <v-select
                                                                    class="py-0"
                                                                    :items="categorias"
                                                                    item-text="nombre"
                                                                    item-value="id_categoria"
                                                                    v-model="art.id_categoria"
                                                                    @change="setArticulos(art)"
                                                                    dense
                                                            >
                                                                <template v-slot:no-data>
                                                                    <div class="ml-3">
                                                                        <v-icon>mdi-alert-circle-outline</v-icon> Sin categorías
                                                                    </div>
                                                                </template>
                                                            </v-select>
                                                        </td>
                                                        <td style="width: 400px;">
                                                            <v-autocomplete
                                                                    class="py-0"
                                                                    :items="art.articulos"
                                                                    item-text="articulo"
                                                                    item-value="id_articulo_categoria_inventario"
                                                                    v-model="art.id_articulo"
                                                                    @change="setDataArticulo(art)"
                                                                    dense
                                                            >
                                                                <template v-slot:no-data>
                                                                    <div class="ml-3">
                                                                        <v-icon>mdi-alert-circle-outline</v-icon> Sin artículos
                                                                    </div>
                                                                </template>
                                                            </v-autocomplete>
                                                        </td>
                                                        <td style="width: 100px;">
                                                            <v-text-field
                                                                    class="py-0"
                                                                    v-model="art.cantidad"
                                                                    type="number"
                                                                    min="0"
                                                                    @keyup="setTotal(art)"
                                                                    @click="setTotal(art)"
                                                            ></v-text-field>
                                                        </td>
                                                        <td style="width: 100px;">
                                                            <v-text-field
                                                                    class="py-0"
                                                                    v-model="art.monto"
                                                                    type="number"
                                                                    min="0"
                                                                    @keyup="setTotal(art)"
                                                                    @click="setTotal(art)"
                                                            ></v-text-field>
                                                        </td>
                                                        <td style="width: 100px;">
                                                            <v-text-field
                                                                    class="py-0"
                                                                    v-model="art.total"
                                                                    type="number"
                                                                    min="0"
                                                            ></v-text-field>
                                                        </td>
                                                        <td class="text-center" style="width: 100px;">
                                                            <v-btn
                                                                    fab
                                                                    x-small
                                                                    color="warning"
                                                                    title="Eliminar artículo"
                                                                    @click="removeArticulo(art)"
                                                            ><v-icon>mdi-delete-forever</v-icon></v-btn>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </template>
                                            </v-simple-table>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card>
                        </v-dialog>
                    </v-col>
                </v-row>
            </v-col>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
            >
                mdi-pencil
            </v-icon>
            <v-icon
                    small
                    @click="deleteItem(item)"
            >
                mdi-delete
            </v-icon>
        </template>
    </v-data-table>
</template>

<script>
    export default {
        props:{
            clientes:{
                type:Array,
                default:[]
            },
            factureros:{
                type:Array,
                default:[]
            },
            sustento_tributario:{
                type:Array,
                default:[]
            },
            punto_emision:{
                type:Array,
                default:[]
            },
            tipos_pago:{
                type:Array,
                default:[]
            },
            inventario:{
                type:Object,
                default:{}
            }
        },
        data: () => ({
            dates: [
                new Date().toISOString().substr(0, 10),
                new Date().toISOString().substr(0, 10)
            ],
            menu3:true,
            menu: true,
            menu2: true,
            factura:false,
            comentario:'',
            correos:'',
            tlfs:[],
            g_remision:false,
            retencion:false,
            n_debito:false,
            n_credito:false,
            puntoEmision:'',
            facturero:'',
            f_documento:'',
            f_vencimiento:'',
            idComprobante:1,
            dialog: false,
            fechaDocumento:new Date().toISOString().substr(0, 10),
            fechaVencimiento:new Date().toISOString().substr(0, 10),
            idCliente:'',
            notifications:false,
            sound:false,
            widgets:false,
            search:'',
            idSustentoTributario:1,
            idTipoPago:'',
            headers: [
                {
                    text: 'Dessert (100g serving)',
                    align: 'start',
                    sortable: false,
                    value: 'name',
                },
                { text: 'Calories', value: 'calories' },
                { text: 'Fat (g)', value: 'fat' },
                { text: 'Carbs (g)', value: 'carbs' },
                { text: 'Protein (g)', value: 'protein' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            desserts: [],
            editedIndex: -1,
            articulos:[],
            categorias:[],
            idCategoria:'',
            articulosFactura:[
                {
                    articulos:[],
                    id_categoria:'',
                    id_articulo:'',
                    cantidad:1,
                    monto:'',
                    total:0
                }
            ],
            editedItem: {
                name: '',
                calories: 0,
                fat: 0,
                carbs: 0,
                protein: 0,
            },
            defaultItem: {
                name: '',
                calories: 0,
                fat: 0,
                carbs: 0,
                protein: 0,
            }
        }),

        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
        },

        watch: {
            dialog (val) {
                val || this.close()
            },
        },

        methods: {

            initialize () {
                this.desserts = [
                    {
                        name: 'Frozen Yogurt',
                        calories: 159,
                        fat: 6.0,
                        carbs: 24,
                        protein: 4.0,
                    },
                    {
                        name: 'Ice cream sandwich',
                        calories: 237,
                        fat: 9.0,
                        carbs: 37,
                        protein: 4.3,
                    },
                    {
                        name: 'Eclair',
                        calories: 262,
                        fat: 16.0,
                        carbs: 23,
                        protein: 6.0,
                    },
                    {
                        name: 'Cupcake',
                        calories: 305,
                        fat: 3.7,
                        carbs: 67,
                        protein: 4.3,
                    },
                    {
                        name: 'Gingerbread',
                        calories: 356,
                        fat: 16.0,
                        carbs: 49,
                        protein: 3.9,
                    },
                    {
                        name: 'Jelly bean',
                        calories: 375,
                        fat: 0.0,
                        carbs: 94,
                        protein: 0.0,
                    },
                    {
                        name: 'Lollipop',
                        calories: 392,
                        fat: 0.2,
                        carbs: 98,
                        protein: 0,
                    },
                    {
                        name: 'Honeycomb',
                        calories: 408,
                        fat: 3.2,
                        carbs: 87,
                        protein: 6.5,
                    },
                    {
                        name: 'Donut',
                        calories: 452,
                        fat: 25.0,
                        carbs: 51,
                        protein: 4.9,
                    },
                    {
                        name: 'KitKat',
                        calories: 518,
                        fat: 26.0,
                        carbs: 65,
                        protein: 7,
                    },
                ]
            },

            editItem (item) {
                this.editedIndex = this.desserts.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.dialog = true
            },

            deleteItem (item) {
                const index = this.desserts.indexOf(item)
                confirm('Are you sure you want to delete this item?') && this.desserts.splice(index, 1)
            },

            close () {
                this.dialog = false
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                })
            },

            save () {
                if (this.editedIndex > -1) {
                    Object.assign(this.desserts[this.editedIndex], this.editedItem)
                } else {
                    this.desserts.push(this.editedItem)
                }
                this.close()
            },

            setInfo(){
                this.correos=''
                this.tlfs=[]
                let cliente =this.clientes.find(ele => ele.id_cliente === this.idCliente)
                this.tlfs.push({tlf: cliente.tlf})
                this.idTipoPago = cliente.id_tipo_pago
                this.correos=cliente.correo+' , '
            },

            setArticulos(obj){
                let art =this.inventario.categorias_activadas.find(ele => ele.id_categoria_inventario === obj.id_categoria)
                obj.articulos = art.articulos
            },

            addArticulo(){
                this.articulosFactura.push({
                    id_categoria:'',
                    id_articulo:'',
                    cantidad:1,
                    monto:'',
                    total:0
                })
            },

            removeArticulo(item){
                let index = this.articulosFactura.indexOf(item)
                this.articulosFactura.splice(index,1)
            },

            setDataArticulo(item){
                let art = item.articulos.find(ele => ele.id_articulo_categoria_inventario === item.id_articulo)
                item.monto = art.neto
                this.setTotal(item)
                /*for (let imp of art.impuestos){
                    let tarifa =  imp.tipo_impuesto.tarifa/100
                    let totalImp = item.monto*tarifa
                    total += parseFloat(item.monto)+parseFloat(totalImp)
                }*/
            },

            setTotal(item){
                let art = item.articulos.find(ele => ele.id_articulo_categoria_inventario === item.id_articulo)
                console.log(item)
                item.total = item.monto*item.cantidad
            }

        },
        created () {
            this.initialize()

            for(let categoria of this.inventario.categorias_activadas){
                this.categorias.push({
                    id_categoria:categoria.id_categoria_inventario,
                    nombre:categoria.categoria
                })
            }

        },


    }
</script>

<style scoped>

    header.v-toolbar{
        height:46px!important
    }

    @media(max-width: 959px){
        .btn-comprobante{
            position: absolute;
            text-align: right;
            top: 3px;
            right: 3px;
        }
    }

</style>