<template>
    <v-form ref="form">
        <v-alert
                color="primary"
                dark
                icon="mdi-package-variant"
                border="left"
                dense

        >
            En esta sección puede realizar acciones con su inventario, agregar nuevos artículos, editar y eliminar
        </v-alert>
        <v-data-table
                :headers="headers"
                :items="dataTable"
                sort-by="estado"
                update: sort-desc
                class="elevation-1"
                :items-per-page="10"
                dense
                :loading=loadTable
                loading-text="Cargando datos"
                :search="search"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>Artículos</v-toolbar-title>
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
                            max-width="800px"
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
                            <v-card-title class="pb-0">
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text class="pb-0">
                                <v-container fluid>
                                    <v-dialog
                                            v-model="dialogCatg"
                                            max-width="500px"
                                            :persistent=true
                                    >
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">Categorías</span>
                                            </v-card-title>
                                            <v-card-text >
                                                <v-container class="py-0">
                                                    <v-row>
                                                        <v-data-table
                                                                :headers="headersCatg"
                                                                :items="dataTableCatg"
                                                                sort-by="estado"
                                                                update: sort-desc
                                                                class="elevation-1"
                                                                :items-per-page="10"
                                                                dense
                                                                :loading=loadTable
                                                                loading-text="Cargando datos"
                                                                :search="searchCatg"
                                                        >
                                                            <template v-slot:top>
                                                                <v-toolbar flat color="white">
                                                                    <v-text-field
                                                                            v-model="searchCatg"
                                                                            append-icon="mdi-magnify"
                                                                            label="Buscar"
                                                                            hide-details
                                                                            class="mr-5"
                                                                    ></v-text-field>
                                                                    <v-spacer></v-spacer>
                                                                    <v-btn
                                                                            color="primary"
                                                                            small
                                                                            type="button"
                                                                            :loading=loadingBtnCatg
                                                                            @click="addCategoria"
                                                                    >
                                                                        <v-icon>mdi-plus</v-icon> Nueva
                                                                    </v-btn>
                                                                </v-toolbar>
                                                            </template>
                                                            <template v-slot:item.categoria="props">
                                                                <v-edit-dialog
                                                                        :return-value.sync="props.item.categoria"
                                                                        @save="saveCatg(props.item)"
                                                                >
                                                                    {{ props.item.categoria }}
                                                                    <template v-slot:input>
                                                                        <v-text-field
                                                                                v-model="props.item.categoria"
                                                                                label="Nombre"
                                                                                single-line
                                                                        ></v-text-field>
                                                                    </template>
                                                                </v-edit-dialog>
                                                            </template>
                                                            <template v-slot:item.estado="{ item }">
                                                                <span v-text="item.estado ? 'Activo': 'Inactivo' "></span>
                                                            </template>
                                                            <template v-slot:item.actions="{ item }">
                                                                <v-btn
                                                                        icon
                                                                        small
                                                                        v-if="item.id_categoria_inventario"
                                                                        :x-small=true
                                                                        @click="estadoCatg(item)"
                                                                        :color="item.estado ? 'red' : 'success'"
                                                                >
                                                                    <v-icon :title="item.estado ? 'Descativar usuario' : 'Activar usuario'">
                                                                        {{item.estado ? 'mdi-account-remove' : 'mdi-account-check'}}
                                                                    </v-icon>

                                                                </v-btn>
                                                                <v-btn
                                                                        icon
                                                                        small
                                                                        v-if="!item.id_categoria_inventario"
                                                                        :x-small=true
                                                                        @click="elminarCatg(item)"
                                                                        color="warning"
                                                                >
                                                                    <v-icon title="Eliminar categoria">
                                                                        mdi-delete
                                                                    </v-icon>

                                                                </v-btn >
                                                            </template>
                                                            <template v-slot:no-results>
                                                                <v-alert type="warning" dense icon="mdi-cancel">
                                                                    No se encontraron categorías
                                                                </v-alert>
                                                            </template>
                                                            <template v-slot:no-data>
                                                                <v-alert type="error" dense>
                                                                    No se encontraron categorias
                                                                </v-alert>
                                                            </template>
                                                            <template v-slot:loading>
                                                                       <span class="grey--text">
                                                                            Cargando categorias <v-icon>mdi-dots-horizontal</v-icon>
                                                                        </span>
                                                            </template>
                                                        </v-data-table>
                                                    </v-row>
                                                    <v-row class="text-center">
                                                        <v-col>
                                                            <v-btn
                                                                    class="mt-2"
                                                                    color="secondary"
                                                                    @click="dialogCatg = !dialogCatg"
                                                            >
                                                                <v-icon>mdi-cancel</v-icon> Cerrar
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                    </v-dialog>
                                    <v-form ref="form">
                                        <v-row>
                                            <v-col class="pb-0" cols="12" sm="4">
                                                <v-select
                                                        :items="catgArticulos"
                                                        label="Categoría"
                                                        :rules="requiredRules"
                                                        item-text="categoria"
                                                        item-value="id_categoria_inventario"
                                                        v-model="editedItem.id_categoria_inventario"
                                                        dense
                                                >
                                                    <template v-slot:prepend>
                                                        <v-icon @click="dialogCatg = true">
                                                            mdi-plus-circle-outline</v-icon>
                                                    </template>
                                                </v-select>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.articulo"
                                                        label="Nombre"
                                                        :rules="nombreRules"
                                                        dense
                                                        counter="300"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="4">
                                                <v-text-field
                                                        v-model="editedItem.codigo_p"
                                                        label="Código principal"
                                                        dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="2">
                                                <v-text-field
                                                        v-model="editedItem.codigo_a"
                                                        label="Código auxiliar"
                                                        dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="3">
                                                <v-select
                                                        :items="stockeable"
                                                        label="Disminuye stock?"
                                                        item-text="name"
                                                        item-value="id"
                                                        v-model="editedItem.stockeable"
                                                        dense
                                                >
                                                </v-select>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="2">
                                                <v-text-field
                                                        v-model.number="editedItem.stock"
                                                        label="Stock"
                                                        :rules="stockRules"
                                                        type="number"
                                                        min="0"
                                                        dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="2">
                                                <v-select
                                                        :items=um
                                                        label="Und med."
                                                        v-model="editedItem.und"
                                                        :rules="requiredRules"
                                                        dense
                                                >
                                                </v-select>
                                            </v-col>
                                            <v-col class="pb-0" cols="12" sm="3">
                                                <v-text-field
                                                        v-model.number="editedItem.neto"
                                                        label="Valor neto"
                                                        prepend-icon="mdi-cash-100"
                                                        min="0"
                                                        :rules="netoRules"
                                                        type="number"
                                                        dense
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-form>
                                </v-container>
                                </v-card-text>
                                <v-card-title class="pt-0">
                                    <v-row>
                                        <v-col class="headline py-0" cols="12">
                                            Impuestos
                                        </v-col>
                                    </v-row>
                                </v-card-title>
                                <v-card-text class="pb-0">
                                    <v-col cols="12" sm="12" class="py-0">
                                        <v-row
                                                v-if="impuestos.length>0"
                                                v-for="(imp,index) in imps"
                                                :key=index
                                        >
                                            <v-col cols="12" sm="4" class="pb-0" >
                                                <v-select
                                                        :items=imp.impuestos
                                                        label="Impuesto"
                                                        v-model="imp.id_impuesto"
                                                        item-value="id_impuesto"
                                                        item-text="nombre"
                                                        dense
                                                        clearable
                                                >
                                                </v-select>
                                            </v-col>
                                            <v-col cols="12" sm="8" class="pb-0" >
                                                <v-autocomplete
                                                        v-model="imp.id_tipo_impuesto"
                                                        :items="imp.tipo_impuestos"
                                                        label="Tipo de impuesto"
                                                        item-text="descripcion"
                                                        item-value="id_tipo_impuesto"
                                                        dense
                                                        clearable
                                                >
                                                    <template  v-slot:no-data >
                                                        <div class="pl-2">Seleccione un impuesto</div>
                                                    </template>
                                                </v-autocomplete>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-card-text>
                        </v-card>
                        <v-footer >
                            <v-row class="text-center">
                                <v-col>
                                    <v-btn
                                            class="ma-2"
                                            color="primary"
                                            @click="storeInventario"
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
                    No se encontraron registros
                </v-alert>
            </template>
            <template v-slot:loading>
               <span class="grey--text">
                    Cargando datos <v-icon>mdi-dots-horizontal</v-icon>
                </span>
            </template>
        </v-data-table>
    </v-form>


</template>

<script>

    import {mapActions,mapMutations,mapState} from 'vuex'

    export default {
        name: "InvenarioComponent",
        props:{
            inventario:{
                required:true,
                type: Object
            },
            impuestos:{
                required:true,
                type: Array
            },
        },
        data:() =>({
            headers: [
                { text: 'Nombre',value: 'articulo' },
                { text: 'Código principal',value: 'codigo_p' },
                { text: 'Código aux',value: 'codigo_a' },
                { text: 'Categoría', value: 'categoria' },
                { text: 'Und. medida', value: 'u_medida', sortable: false},
                { text: 'Valor', value: 'neto' },
                { text: 'Stock', value: 'stock' },
                { text:'Estado', value:'estado'},
                { text: 'Acciones', value:'actions', sotable:false }
            ],
            headersCatg: [
                { text: 'Nombre',value: 'categoria' },
                { text:'Estado', value:'estado', align:'center'},
                { text: 'Acciones', value:'actions', align:'center', sotable:false }
            ],
            dialog: false,
            dialogCatg:false,
            loadTable:true,
            um:['und','kg','l'],
            correo :'',
            search : '',
            searchCatg:'',
            loadingBtnCatg:false,
            tipoImpuestos:[],
            imps:[],
            editedIndex: -1,
            editedIndexCat: -1,
            dataTable: [],
            dataTableCatg : [],
            catgArticulos: [],
            impsArticulo:[],
            stockeable:[{id:0, name:'No'},{id:1, name:'Si'}],
            editedItem: {
                articulo:'',
                id_articulo_categoria_inventario:'',
                codigo_a :'',
                codigo_p: '',
                id_categoria_inventario: '',
                und:'',
                neto:'',
                stock:'',
                stockeable:''
            },
            defaultItem: {
                articulo:'',
                id_articulo_categoria_inventario:'',
                codigo_a :'',
                codigo_p: '',
                id_categoria_inventario: '',
                und:'',
                neto:'',
                stock:'',
                stockeable:''
            },
            nombreRules:[
                v => !!v || 'La nombre del árticulo es obligatorio',
                v => (v && v.length <= 300) || 'El campo debe ser menor o igual 300 caracteres'
            ],
            requiredRules:[
                v => !!v || 'Campo obligatorio',
            ],
            netoRules:[
                v => !!v || 'El valor neto es obligatorio',
                v => (v && v > 0) || 'El valor de venta debe ser mayor o igual a 0'
            ],
            stockRules:[
                v => !!v || 'El stock es obligatorio',
                v => (v && v > 0) || 'El stock debe ser mayor o igual a 0'
            ]
        }),
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'Nuevo árticulo' : 'Editar árticulo'
            },
            ...mapState(['loadingBtn','paramsAlertQuestion']),
        },
        watch: {
            dialog (val) {
                val || this.closeModal()
            },

        },
        methods:{

            ...mapActions(['httpRequest','alertNotification']),

            ...mapMutations(['setLoadingBtn']),

            editItem (item) {
                this.show= false
                this.editedIndex = this.dataTable.indexOf(item)
                this.editedItem = Object.assign({}, item)
                for(let x=0; x<this.imps.length; x++){
                    if(typeof item.impuestos[x] != "undefined"){
                        this.imps[x].id_impuesto=  item.impuestos[x].id_impuesto
                        this.imps[x].id_tipo_impuesto = item.impuestos[x].id_tipo_impuesto
                    }else{
                        this.imps[x].id_impuesto= ""
                        this.imps[x].id_tipo_impuesto = ""
                    }
                }
                this.contrasena=''
                this.dialog = true
            },

            estadoItem (item) {

                Vue.swal({
                    text: "¿Esta seguro de "+(item.estado ? 'desactivar': 'activar')+" el artículo "+item.articulo.toUpperCase()+".?",
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {
                        this.httpRequest({
                            method:'post',
                            url:'inventario/estado',
                            data:{
                                id_articulo_categoria_inventario: item.id_articulo_categoria_inventario,
                                estado: item.estado
                            }
                        }).then((res)=>{

                            this.alertNotification({
                                param: {
                                    html: res.data.msg
                                }
                            })

                            let index = this.dataTable.indexOf(item)

                            item.estado = !item.estado

                            Object.assign(this.dataTable[index],item)

                        })
                    }
                })
            },

            estadoCatg(item){

                this.httpRequest({
                    method:'post',
                    url:'inventario/estado_categoria',
                    data:item
                }).then((res)=>{

                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })

                    let indexSelect = this.catgArticulos.indexOf(item)
                    let indexTable = this.dataTableCatg.indexOf(item)

                    if(item.estado){
                        this.catgArticulos.splice(indexSelect, 1)
                    }else{
                        this.catgArticulos.push(res.data.categoria)
                    }
                    Object.assign(this.dataTableCatg[indexTable],res.data.categoria)
                })
            },

            closeModal () {
                this.dialog = false;
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem)
                    this.editedIndex = -1
                });
                for(let x=0; x<this.imps.length; x++){
                    this.imps[x].id_impuesto= null
                    this.imps[x].id_tipo_impuesto = null
                }
            },

            storeInventario () {

                if (!this.$refs.form.validate())
                    return;

                let impuestos=[]

                for(let imp of this.imps){
                    if(typeof imp.id_impuesto!="undefined"
                        && typeof imp.id_tipo_impuesto!="undefined"
                        && imp.id_impuesto !=""
                        && imp.id_tipo_impuesto!=""
                        && imp.id_impuesto !=null
                        && imp.id_tipo_impuesto!=null)
                    {
                        impuestos.push({
                            id_impuesto: imp.id_impuesto,
                            id_tipo_impuesto : imp.id_tipo_impuesto
                        })
                    }
                }

                this.setLoadingBtn()

                this.httpRequest({

                    method:'post',
                    url:'inventario/store_inventario',
                    data:{
                        articulo:this.editedItem.articulo,
                        stock:this.editedItem.stock,
                        id_articulo_categoria_inventario:this.editedItem.id_articulo_categoria_inventario,
                        codigo_a :this.editedItem.codigo_a,
                        codigo_p: this.editedItem.codigo_p,
                        id_categoria_inventario: this.editedItem.id_categoria_inventario,
                        und:this.editedItem.und,
                        neto:this.editedItem.neto,
                        impuestos: impuestos
                    }

                }).then((res)=>{

                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })

                    this.setLoadingBtn()

                    let data = {
                        id_categoria_inventario: res.data.articulo.id_categoria_inventario,
                        id_articulo_categoria_inventario:res.data.articulo.id_articulo_categoria_inventario,
                        articulo: res.data.articulo.articulo,
                        codigo_a : res.data.articulo.codigo_a,
                        codigo_p : res.data.articulo.codigo_p,
                        categoria: res.data.articulo.categoria.categoria,
                        und : res.data.articulo.und,
                        neto: res.data.articulo.neto,
                        estado : res.data.articulo.estado,
                        valor : res.data.articulo.neto,
                        stock: res.data.articulo.stock,
                        impuestos : res.data.articulo.impuestos
                    }

                    if (this.editedIndex > -1) { // ACTUALIZA

                        Object.assign(this.dataTable[this.editedIndex],data)

                    } else { // GUARDA

                        this.dataTable.push(data)

                    }
                    this.closeModal()

                })

            },

            saveCatg(item){

                this.httpRequest({
                    method:'post',
                    url:'inventario/store_categoria',
                    data:item
                }).then((res)=>{

                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })

                    let data= {
                        id_categoria_inventario: res.data.idCategoria,
                        categoria: res.data.categoria.categoria,
                        estado: res.data.categoria.estado
                    }

                    let indexSelect = this.catgArticulos.indexOf(item)
                    let indexTable = this.dataTableCatg.indexOf(item)

                    if(item.id_categoria_inventario == ""){ //GUARDA
                        this.catgArticulos.push(data)
                    }else{
                        Object.assign(this.catgArticulos[indexSelect],data)
                    }
                    Object.assign(this.dataTableCatg[indexTable],data)
                })

            },

            addCategoria(){
                this.loadingBtnCatg=true
                this.dataTableCatg.unshift({
                    id_categoria_inventario: '',
                    categoria : '',
                    estado : true
                });
                this.loadingBtnCatg=false
            },

            elminarCatg(item){
                let index = this.dataTableCatg.indexOf(item)
                this.dataTableCatg.splice(index, 1)
            },

        },
        mounted() {

            for(let categoria of this.inventario.categorias){

                let data= {
                    id_categoria_inventario: categoria.id_categoria_inventario,
                    categoria : categoria.categoria,
                    estado : categoria.estado
                }

                this.dataTableCatg.push(data)

                if(categoria.estado)
                    this.catgArticulos.push(data);

                for(let articulo of categoria.articulos){

                    let impuestos =[];

                    for(let impuesto of articulo.impuestos) {
                        impuestos.push({
                            id_impuesto: impuesto.id_impuesto,
                            nombre_imp: impuesto.impuesto.nombre,
                            id_tipo_impuesto: impuesto.tipo_impuesto.id_tipo_impuesto,
                            descrpicon_tipo_imp : impuesto.tipo_impuesto.descripcion,
                        })
                    }

                    this.dataTable.push({
                        id_categoria_inventario: articulo.id_categoria_inventario,
                        id_articulo_categoria_inventario:articulo.id_articulo_categoria_inventario,
                        articulo: articulo.articulo,
                        codigo_a : articulo.codigo_a,
                        codigo_p : articulo.codigo_p,
                        categoria: categoria.categoria,
                        und : articulo.und,
                        neto: articulo.neto,
                        estado : articulo.estado,
                        stock: articulo.stock,
                        impuestos : impuestos
                    })
                }
            }

            for(let impuesto of this.impuestos){
                this.imps.push({
                    impuestos: [{id_impuesto:impuesto.id_impuesto, nombre:impuesto.nombre}],
                    tipo_impuestos : impuesto.tipo_impuesto,
                    id_impuesto:'',
                    id_tipo_impuesto:''
                })
            }

            this.loadTable=false

        }
    }
</script>

<style scoped>

</style>