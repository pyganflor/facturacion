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
                                style="margin-top: 2.3px;"
                                class="py-0"
                                cols="12"
                                md="2"
                                sm="6"
                        >
                            <v-select
                                    class="mt-5"
                                    :items="modulos"
                                    label="Tipo de comprobante"
                                    item-text="modulo.nombre"
                                    item-value="modulo.id_modulo"
                                    v-model="idModulo"
                                    dense
                            ></v-select>
                        </v-col>
                        <v-col class="py-0" cols="12" md="3" sm="6">
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
                                    v-model="idCliente"
                                    clearable
                                    dense

                            >
                            </v-select>
                        </v-col>
                        <v-col class="py-0" cols="12" md="3" sm="6">
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
                                            title="Nuevo comprobante"
                                    >
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </template>
                                <v-card>
                                    <v-toolbar style="height: 46px" dark color="primary">
                                        <v-btn  class="pb-4" icon dark @click="dialog = false">
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                        <v-toolbar-title class="pb-4" >Nuevo comprabante</v-toolbar-title>
                                        <v-spacer></v-spacer>
                                        <v-toolbar-items class="pb-4" >
                                            <v-btn dark text @click="dialog = false">
                                                <v-icon>mdi-check-all</v-icon> Generar
                                            </v-btn>
                                        </v-toolbar-items>
                                    </v-toolbar>
                                    <v-container fluid>
                                        <v-btn
                                                class="ma-2"
                                                tile color="indigo"
                                                dark
                                                v-if="factura"
                                        >
                                            Facturas</v-btn>
                                        <v-btn
                                                class="ma-2"
                                                tile
                                                color="indigo"
                                                dark
                                                v-if="retencion"
                                        >
                                            Retenciones</v-btn>
                                        <v-btn
                                                class="ma-2"
                                                tile
                                                color="indigo"
                                                dark
                                                v-if="g_remision"
                                        >
                                            Guías de remisión</v-btn>
                                        <v-btn
                                                class="ma-2"
                                                tile
                                                color="indigo"
                                                dark
                                                v-if="n_debito"
                                        >
                                            Notas de débito</v-btn>
                                        <v-btn
                                                class="ma-2"
                                                tile
                                                color="indigo"
                                                dark
                                                v-if="n_credito"
                                        >
                                            Notas de crédito</v-btn>
                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <v-row>
                                                    <v-col cols="12" sm="4">
                                                        <v-select
                                                                class="mt-5"
                                                                :items="modulos"
                                                                label="Tipo de comprobante"
                                                                item-text="modulo.nombre"
                                                                item-value="modulo.id_modulo"
                                                                v-model="idComprobante"
                                                                dense
                                                        ></v-select>
                                                    </v-col>
                                                    <v-col cols="12" sm="4">
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
                                                    <v-col cols="12" sm="4">
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
                                                    <v-col class="py-0" cols="12" sm="6">
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
                                                    <v-col class="py-0" cols="12" sm="6">
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
                                                    <v-col cols="12" sm="12">
                                                        <v-text-field
                                                                v-model="comentario"
                                                                label="Comentario"
                                                        ></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-col>
                                            <v-col cols="12" md="6"></v-col>
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
                required:true,
                type:Array,
                default:[]
            },
            modulos:{
                required:true,
                type:Array,
                default:[]
            },
            factureros:{
                required:true,
                type:Array,
                default:[]
            },
            sustento_tributario:{
                required:true,
                type:Array,
                default:[]
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
            g_remision:false,
            retencion:false,
            n_debito:false,
            n_credito:false,
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
            idModulo:1,
            idSustentoTributario:1,
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
            },
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

            activaBtnModulo(){
                for(let modulo of this.modulos){
                    this.factura = this.factura || modulo.id_modulo === 1
                    this.g_remision = this.g_remision || modulo.id_modulo === 2
                    this.retencion = this.retencion || modulo.id_modulo === 5
                    this.n_debito = this.n_debito || modulo.id_modulo === 3
                    this.n_credito = this.n_credito || modulo.id_modulo === 4
                }
            }
        },
        created () {
            this.initialize()

            this.activaBtnModulo()
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