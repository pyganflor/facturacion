<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-alert
                color="primary"
                dark
                icon="mdi-file-document"
                border="left"
                dense
        >
            En esta sección puede realizar las diferentes acciones con sus facturas
        </v-alert>
        <v-data-table
                :headers="headers"
                :items="dataTable"
                sort-by="calories"
                class="elevation-1"
                :loading=loadingTable
                :search=search
                loading-text="Buscando facturas"
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
                                            @change="searchDataTable"

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
                                        @change="getDataComponent()"
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
                                                <v-btn
                                                        dark
                                                        text
                                                        @click="generarComprobante"
                                                >
                                                    <v-icon>mdi-check-all</v-icon> <span class="d-none d-md-block">Generar</span>
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
                                                                    :rules="requiredRule"
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
                                                                    :rules="requiredRule"
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
                                                                            :rules="requiredRule"
                                                                            v-bind="attrs"
                                                                            v-on="on"
                                                                    ></v-text-field>
                                                                </template>
                                                                <v-date-picker
                                                                        v-model="fechaDocumento"
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
                                                                            :rules="requiredRule"
                                                                            v-on="on"
                                                                    ></v-text-field>
                                                                </template>
                                                                <v-date-picker
                                                                        v-model="fechaVencimiento"
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
                                                                    :rules="requiredRule"
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
                                                                    class="py-1"
                                                            >
                                                                Datos del cliente
                                                                <div class="float-right">
                                                                    <v-btn
                                                                            fab
                                                                            color="primary"
                                                                            title="Crear nuevo cliente"
                                                                            x-small
                                                                    >
                                                                        <v-icon>mdi-account-multiple-plus</v-icon>
                                                                    </v-btn>
                                                                </div>
                                                            </v-alert>
                                                        </v-col>
                                                        <v-col
                                                                cols="12"
                                                                class="pb-0"
                                                                sm="6"
                                                                :md="`${idFormaPago === 1 ? 5 : 6 }`"
                                                        >
                                                            <v-select
                                                                    class="mt-5"
                                                                    :items="clientes"
                                                                    label="Cliente"
                                                                    item-text="nombre"
                                                                    item-value="id_cliente"
                                                                    :rules="requiredRule"
                                                                    v-model="idCliente"
                                                                    @change="setInfo"
                                                                    dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-col
                                                                cols="12"
                                                                sm="12"
                                                                :md="`${idFormaPago === 1 ? 3 : 6 }`"
                                                        >
                                                            <v-select
                                                                    class="mt-5"
                                                                    :items="formaPago"
                                                                    label="Formas de pago"
                                                                    :rules="requiredRule"
                                                                    item-text="nombre"
                                                                    item-value="id_forma_pago"
                                                                    v-model="idFormaPago"
                                                                    dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-col
                                                                class="mt-4"
                                                                cols="12"
                                                                sm="12"
                                                                :md="`${idFormaPago === 1 ? '4' : '' }`"
                                                                :class="`${idFormaPago === 2 ? 'd-none' : '' }`"
                                                        >
                                                            <v-select

                                                                    :items="tipos_pago"
                                                                    label="Tipo de pago"
                                                                    :rules="requiredRule"
                                                                    item-text="nombre"
                                                                    item-value="id_tipo_pago"
                                                                    v-model="idTipoPago"
                                                                    dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-col
                                                                cols="12"
                                                                class="pb-0"
                                                                sm="6"

                                                        >
                                                            <v-text-field
                                                                    class="py-0"
                                                                    style="margin-top: -2px"
                                                                    v-model="correos"
                                                                    :rules="requiredRule"
                                                                    label="Correos (separados por coma)"
                                                                    append-icon="mdi-email-outline"
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col
                                                                cols="12"
                                                                class="py-0"
                                                                sm="6"
                                                        >
                                                            <v-row>
                                                                <v-col
                                                                        cols="12"
                                                                        sm="6"
                                                                        class="py-0"
                                                                >
                                                                    <v-text-field
                                                                            class="pb-0"
                                                                            style="padding-top: 6px;"
                                                                            v-model="plazo"
                                                                            :rules="requiredRule"
                                                                            label="Tiempo pago plazo"
                                                                    ></v-text-field>
                                                                </v-col>
                                                                <v-col
                                                                        cols="12"
                                                                        sm="6"
                                                                        class="pb-0"
                                                                >
                                                                    <v-select
                                                                            style="padding-bottom: 1px;"
                                                                            :items=undTiempo
                                                                            label="Und. tiempo"
                                                                            v-model="undTiempoPlazo"
                                                                            :rules="requiredRule"
                                                                            dense
                                                                    >
                                                                    </v-select>
                                                                </v-col>
                                                            </v-row>
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
                                                        <span style="position: relative;top:4px">Árticulos</span>
                                                        <div class="float-right">
                                                            <v-btn
                                                                    fab
                                                                    color="secondary"
                                                                    title="Agregar retención"
                                                                    @click="addArticulo"
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
                                                                <th class="white--text" style="width: 400px;">Categoría</th>
                                                                <th class="white--text" style="width: 400px;">Artículo</th>
                                                                <th class="white--text" >Cantidad</th>
                                                                <th class="white--text" >Precio U.</th>
                                                                <th class="white--text" >Descuento.</th>
                                                                <th class="white--text text-center" >Precio T.</th>
                                                                <th class="text-center white--text" style="width: 50px;">Acción</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr
                                                                    v-for="(art ,x) in articulosFactura"
                                                                    :key="x"
                                                            >
                                                                <td>{{(x+1)}})</td>
                                                                <td style="width: 200px;">
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
                                                                            :rules="requiredRule"
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
                                                                            :rules="requiredRule"
                                                                            @keyup="setTotal(art)"
                                                                            @click="setTotal(art)"
                                                                    ></v-text-field>
                                                                </td>
                                                                <td style="width: 100px;">
                                                                    <v-text-field
                                                                            class="py-0"
                                                                            v-model="art.descuento"
                                                                            type="number"
                                                                            min="0"
                                                                            @keyup="setTotal(art)"
                                                                            :rules="requiredRule"
                                                                            @click="setTotal(art)"
                                                                    ></v-text-field>
                                                                </td>
                                                                <td class="text-center" style="width: 100px;">
                                                                    <h3 class="mb-2">${{art.total}}</h3>
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
                                            <v-row>
                                                <v-col cols="12" md="8"></v-col>
                                                <v-col cols="12" sm="12" md="4">
                                                    <v-simple-table dense>
                                                        <template v-slot:default>
                                                            <tbody>
                                                            <tr class="primary white--text">
                                                                <td>Descripción</td>
                                                                <td>Monto</td>
                                                            </tr>
                                                            <tr >
                                                                <td>Sub total</td>
                                                                <td>${{subTotal}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sub total IVA 12%</td>
                                                                <td>${{iva12}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sub total IVA 14%</td>
                                                                <td>${{iva14}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sub total IVA 0%</td>
                                                                <td id="iva_0">${{iva0}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sub total No objeto</td>
                                                                <td>${{noObjeto}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sub total Excento</td>
                                                                <td >${{excento}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>ICE:</td>
                                                                <td id="ice">0.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td>IRBPNR:</td>
                                                                <td id="irbpnr">0.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Descuento</td>
                                                                <td>${{descuento}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Total</b></td>
                                                                <td><b>${{total}}</b></td>
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
                    </v-form>
                </v-col>
            </template>
            <template v-slot:no-results="{ item }">
                No se encontraron facturas
            </template>
            <template v-slot:no-data>
                Sin registros
            </template>
            <template v-slot:item.total="{ item }">
                <b>${{parseFloat(item.total).toFixed(2)}}</b>
            </template>
            <!--<template v-slot:item.entorno="{ item }">
                {{item.entorno === 1 ? 'Pruebas' : 'Producción'}}
            </template>-->
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
            </template>
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
                        <v-list-item class="list-actions" v-if="item.estado === 1">
                            <v-list-item-title>
                                <v-btn text small >
                                    <a :href="`/factura/comprobante/${item.id_factura}`" target="_blank" class="link">
                                        <v-icon color="success">mdi-file-pdf</v-icon> Visualizar
                                    </a>
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item
                                class="list-actions"
                                v-if="item.estado === 1"
                        >
                            <v-list-item-title >
                                <v-btn text small @click="reenviarCorreo(item)">
                                    <v-icon color="success">mdi-email-outline</v-icon> Enviar correo
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item
                                v-if="item.estado!==1 && item.estado!==3 && item.estado!==4"
                                class="list-actions"
                        >
                            <v-list-item-title>
                                <v-btn text small @click="editarFactura(item.id_factura)">
                                    <v-icon color="warning">mdi-pencil</v-icon> Editar
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                        <!--<v-list-item
                                v-if="(item.estado===0 || item.estado===2) && item.estado!==4"
                                class="list-actions"
                        >
                            <v-list-item-title>
                                <v-btn text small >
                                    <v-icon color="success">mdi-auto-upload</v-icon> Reenviar al sri
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>-->
                        <v-list-item class="list-actions">
                            <v-list-item-title>
                                <v-btn text small @click="consultarFactura(item)">
                                    <v-icon color="success">mdi-comment-question-outline</v-icon> Consultar factura
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item v-if="item.estado===1" class="list-actions">
                            <v-list-item-title>
                                <v-btn text small @click="anularFactura(item)">
                                    <v-icon color="red">mdi-cancel</v-icon> Anular factura
                                </v-btn>
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    import {mapActions,mapMutations,mapState} from 'vuex'

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
            menu: true,
            menu2: true,
            menu3:true,
            factura:false,
            comentario:'',
            correos:'',
            puntoEmision:'',
            facturero:'',
            dialog: false,
            fechaDocumento:new Date().toISOString().substr(0, 10),
            fechaVencimiento:new Date().toISOString().substr(0, 10),
            idCliente:'',
            idClienteSearch:'',
            search:'',
            idSustentoTributario:1,
            idTipoPago:'',
            undTiempoPlazo:'Dias',
            plazo:'0',
            iva0:0,
            iva12:0,
            iva14:0,
            subTotal:0,
            noObjeto:0,
            excento:0,
            descuento:0,
            total:0,
            overlay:false,
            editar :false,
            secuencialEdit :'',
            idFactura:'',
            undTiempo: ['Dias','Semanas','Meses'],
            formaPago:[
                {id_forma_pago:1,nombre:'Contado'},
                {id_forma_pago:2,nombre:'Crédito'}
            ],
            headers: [
                { text: 'Secuencial', value: 'secuencial' },
                { text: 'Clave acceso', value: 'clave_acceso', align: 'center' },
                { text: 'Fecha documento', value: 'fecha_doc' },
                { text: 'Fecha autorización', value: 'fecha_aut'},
                { text: 'Cliente', value: 'cliente' },
                { text: 'Total', value: 'total', align: 'center'  },
                { text: 'Estado', value: 'estado', align: 'center'  },
                { text: 'Mensajes', value: 'causa' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            dataTable: [],
            estado:1,
            editedIndex: -1,
            articulos:[],
            categorias:[],
            idCategoria:'',
            idFormaPago:1,
            articulosFactura:[
                {
                    articulos:[],
                    id_categoria:'',
                    id_articulo:'',
                    cantidad:1,
                    descuento:'0',
                    monto:'',
                    total:0
                }
            ],
            requiredRule:[
                v => !!v || 'Campo obligatorio'
            ]
        }),

        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },

            ...mapState(['loadingBtn','paramsAlertQuestion','estados','loadingTable']),
        },

        methods: {

            ...mapActions(['httpRequest','alertNotification']),

            ...mapMutations(['setLoadingBtn','setLoadingTable']),

            copyClaveAcceso(id){
                var codigoACopiar = document.getElementById('clave_acceso_'+id);
                var seleccion = document.createRange();
                seleccion.selectNodeContents(codigoACopiar);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(seleccion);
                let res = document.execCommand('copy');
                window.getSelection().removeRange(seleccion);
            },

            getDataComponent(){
                this.setLoadingTable()
                this.dataTable=[]
                this.httpRequest({
                    method: 'get',
                    url: 'factura/list',
                    data: {
                        estado : this.estado,
                        fechas: this.dateRangeText,
                        id_cliente : this.idClienteSearch
                    }
                }).then((res) => {

                    this.dataTable=res.data
                    this.setLoadingTable()
                })

            },

            setInfo(){
                this.correos=''
                let cliente =this.clientes.find(ele => ele.id_cliente === this.idCliente)
                this.idTipoPago = cliente.id_tipo_pago
                this.correos=cliente.correo+' , '
            },

            setArticulos(obj){
                let art = this.inventario.categorias_activadas.find(ele => ele.id_categoria_inventario === obj.id_categoria)
                obj.articulos = art.articulos
            },

            addArticulo(){
                this.articulosFactura.push({
                    id_categoria:'',
                    id_articulo:'',
                    cantidad:1,
                    monto:'',
                    descuento:'0',
                    total:0
                })
            },

            removeArticulo(item){
                let index = this.articulosFactura.indexOf(item)
                this.articulosFactura.splice(index,1)
                this.calculaMontos()
            },

            setDataArticulo(item){
                let art = item.articulos.find(ele => ele.id_articulo_categoria_inventario === item.id_articulo)
                item.monto = art.neto
                this.setTotal(item)
            },

            setTotal(item){
                item.total = (item.monto*item.cantidad)-item.descuento
                this.calculaMontos()
            },

            calculaMontos(){
                let iva14 = 0
                let iva12 = 0
                let iva0 = 0
                let noObeto = 0
                let excento = 0
                let ice = 0
                let irbpnr = 0
                let descuento = 0
                this.descuento=0.00
                this.subTotal=0.00
                this.total=0
                let calculoTotal = 0

                for(let arr of this.articulosFactura){
                    console.log(arr)
                    let art = arr.articulos.find(ele => ele.id_articulo_categoria_inventario === arr.id_articulo)
                    let subTotalArticulo = parseFloat(arr.total)
                    let totalMontoImpuesto = 0
                    let montoImp =0

                    for(let impuesto of art.impuestos){

                        let codigo = impuesto.impuesto.codigo
                        let idTipoImpuesto = impuesto.tipo_impuesto.id_tipo_impuesto
                        let tarifa = impuesto.tipo_impuesto.tarifa

                        if(codigo === '2'){ //IVA

                            montoImp = subTotalArticulo*(parseFloat(tarifa)/100)
                            totalMontoImpuesto += parseFloat(montoImp)

                            if(idTipoImpuesto === 3){
                                iva14 += parseFloat(arr.total)
                            }else if(idTipoImpuesto === 2){
                                iva12 += parseFloat(arr.total)
                            }else if(idTipoImpuesto === 10){
                                iva0 += parseFloat(arr.total)
                            }else if(idTipoImpuesto === 4){
                                noObeto += parseFloat(arr.total)
                            }else if(idTipoImpuesto === 5){
                                excento += parseFloat(arr.total)
                            }

                        }else if(codigo === '3'){ //ICE

                        }else if(codigo === '5'){ //IRBPNR

                        }

                        this.iva0=iva0
                        this.iva12=iva12
                        this.iva14=iva14
                        this.noObjeto=noObeto
                        this.excento=excento
                    }

                    this.descuento+= isNaN(parseFloat(arr.descuento)) ? 0 : parseFloat(arr.descuento)
                    this.subTotal+= isNaN(parseFloat(arr.total)) ? 0 : parseFloat(arr.total)
                    let montoTotal = parseFloat(arr.total)+parseFloat(totalMontoImpuesto)
                    calculoTotal +=montoTotal

                }

                this.total= calculoTotal.toFixed(2)
            },

            generarComprobante(){

                if(!this.$refs.form_factura.validate())
                    return

                let html='<div class="text-left">'
                    html+= '<p><input type="checkbox" checked id="genera_xml" name="genera_xml" disabled> <label for="genera_xml">Se generará el xml</label></p>'
                    html+= '<p><input type="checkbox" checked id="guarda_factura" name="guarda_factura" disabled> <label for="guarda_factura">Se guardará el registro de la factura</label></p>'
                    html+= '<p><input type="checkbox" checked id="firmar" name="firmar"> <label for="firmar">Firmar documento electrónico</label></p>'
                    html+= '<p><input type="checkbox" checked id="autorizar" name="autorizar"> <label for="autorizar">Autorizar documento electrónico</label></p>'
                    html+= '<p><input type="checkbox" checked id="correo" name="correo"> <label for="correo">Envío de correo electrónico</label></p>'
                    html+= '</div>'

                Vue.swal({
                    title: "Acciones a realizar",
                    html:html,
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {

                        this.overlay = true
                        this.dialog = false
                        let articulos = []

                        for (let articulosCatg of this.articulosFactura) {

                            let articulo = articulosCatg.articulos.find(ele => ele.id_articulo_categoria_inventario === articulosCatg.id_articulo)

                            if (typeof articulo != "undefined") {

                                let impuestos = []
                                let valorConImpuesto = 0

                                for (let impuesto of articulo.impuestos) {
                                    let valorImpuesto = 0

                                    if (impuesto.tipo_impuesto.tipo_tarifa === "%") {
                                        valorImpuesto = parseFloat((articulo.neto * (impuesto.tipo_impuesto.tarifa / 100)))
                                        valorConImpuesto = parseFloat(articulo.neto) + valorImpuesto
                                    } else if (impuesto.tipo_impuesto.tipo_tarifa === "t") {

                                    } else if (impuesto.tipo_impuesto.tipo_tarifa === "n") {

                                    }

                                    impuestos.push({
                                        id_impuesto: impuesto.id_impuesto,
                                        nombre_imp: impuesto.nombre,
                                        id_tipo_impuesto: impuesto.id_tipo_impuesto,
                                        tarifa: impuesto.tipo_impuesto.tarifa,
                                        total: valorConImpuesto,
                                        valor_imp: valorImpuesto,
                                        base_imponible: articulosCatg.total,
                                        codigo_imp: impuesto.impuesto.codigo,
                                        codigo_tipo_imp: impuesto.tipo_impuesto.codigo,
                                        nombre_tipo_impuesto: impuesto.tipo_impuesto.descripcion
                                    })
                                }

                                articulos.push({
                                    id_articulo: articulo.id_articulo_categoria_inventario,
                                    id_categoria_inventario: '',
                                    nombre: articulo.articulo,
                                    neto: articulo.neto,
                                    stock: articulo.stock,
                                    codigo_p: articulo.codigo_p,
                                    codigo_a: articulo.codigo_a,
                                    cantidad: articulosCatg.cantidad,
                                    descuento: articulosCatg.descuento,
                                    total: articulosCatg.total,
                                    impuestos: impuestos
                                });
                            }

                        }

                        this.httpRequest({
                            method: 'post',
                            url: 'factura/store',
                            data: {
                                ptoEmision: this.puntoEmision,
                                facturero: this.facturero,
                                fechaDoc: this.fechaDocumento,
                                fechaVenc: this.fechaVencimiento,
                                sustTributario: this.idSustentoTributario,
                                comentario: this.comentario,
                                idCliente: this.idCliente,
                                formaPago: this.idFormaPago,
                                idTipoPago: this.idTipoPago,
                                correos: this.correos,
                                subTotal: this.subTotal,
                                descuento: this.descuento,
                                total: this.total,
                                plazo: this.plazo,
                                undTiempoPlazo: this.undTiempoPlazo,
                                articulos: articulos,
                                firmar: $("#firmar").is(":checked"),
                                autorizar: $("#autorizar").is(":checked"),
                                correo: $("#correo").is(":checked"),
                                editar : this.editar,
                                secuencialEdit : this.secuencialEdit,
                                id_factura : this.idFactura
                            }
                        }).then((res) => {

                            this.overlay = false
                            this.alertNotification({
                                param: {
                                    title: res.data.success ? 'Exito' : 'Error',
                                    icon: res.data.success ? 'success' : 'error',
                                    confirmButtonColor: res.data.success ? '#a5dc86' : '#00b388',
                                    html: res.data.msg,
                                    toast: false,
                                    grow: false,
                                    timer: 45000
                                }
                            })
                            if (res.data.success) {

                                if (this.estado === res.data.factura.estado)
                                    this.dataTable.unshift(res.data.factura);

                                this.puntoEmision = this.punto_emision.length===1 ? this.punto_emision[0].numero : ''
                                this.facturero = this.factureros.length===1 ? this.factureros[0].numero : ''
                                this.fechaDocumento = new Date().toISOString().substr(0, 10)
                                this.fechaVencimiento = new Date().toISOString().substr(0, 10)
                                this.idSustentoTributario = 2
                                this.comentario = ''
                                this.idCliente = ''
                                this.idFormaPago = 1
                                this.idTipoPago = ''
                                this.correos = ''
                                this.subTotal = ''
                                this.descuento = ''
                                this.total = ''
                                this.plazo = '0'
                                this.undTiempoPlazo = 'Dias'
                                Object.assign(this.articulosFactura, {
                                    id_categoria: '',
                                    id_articulo: '',
                                    cantidad: 1,
                                    descuento: '0',
                                    monto: '',
                                    total: 0,
                                    articulos: []
                                })

                            }else if(!res.data.success && this.editar){
                                this.getDataComponent()
                            }

                            this.editar=false
                            this.secuencialEdit=''
                            this.idFactura=''

                        }).catch((err) => {
                            this.overlay = false
                            this.dialog = true
                        })
                    }
                });
            },

            anularFactura(item){

                Vue.swal({
                    text: "Esta seguro de anular la factura "+item.secuencial+"?",
                    ...this.paramsAlertQuestion,
                    timerProgressBar:false,
                    icon:'question'
                }).then((result) => {
                    if (result.value) {
                        this.httpRequest({
                            method: 'post',
                            url: 'factura/anular',
                            data: {
                                id_factura : item.id_factura
                            }
                        }).then((res) => {

                            Vue.swal({
                                title: 'Recordatorio',
                                icon: 'info',
                                timerProgressBar:false,
                                html: res.data.msg+'<br/> Recuerde que debe ingresar al portal del SRI <a target="_blank" href="https://srienlinea.sri.gob.ec/sri-en-linea/inicio/NAT">https://srienlinea.sri.gob.ec</a> para realizar las acciones pertinentes y anular la factura ',
                                ...this.paramsAlertQuestion
                            })

                            let obj = this.dataTable.find(e => e.id_factura === item.id_factura)
                            let index = this.dataTable.indexOf(obj)
                            this.dataTable.splice(index,1)

                        })

                    }
                })

            },

            reenviarCorreo(item){

                Vue.swal({
                    ...this.paramsAlertQuestion,
                    timerProgressBar:false,
                    icon:'question',
                    iconHtml: '<span class="mdi mdi-email-outline"></span>',
                    html: '<div>' +
                                '<div style="margin-bottom: 15px">Para agregar un correo adicional ingreselo separado por coma (,)?</div>'+
                                '<input type="text" style="border: 1px solid gainsboro;padding: 5px;width: 95%;margin: 0 auto;border-radius: 3px"' +
                                ' value="'+item.correos+'" id="correos" placeholder="Correos extra">' +
                                '<div style="color:red" id="error"></div>'+
                          '</div>',
                    preConfirm:()=>{

                        let correos = $("#correos").val().split(',')

                        if(correos.length===0){
                            $("#error").html('');
                            return true
                        }else{
                            let i=false
                            let correoInvalido = ''
                            for(let correo of correos){
                                if(correo.trim() !== ""){
                                    let test = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(correo.trim())
                                    if(!test){
                                        i=true;
                                        correoInvalido=correo
                                        break
                                    }
                                }
                            }

                            if(i){
                                $("#error").html('El correo '+correoInvalido+' es inválido');
                                return false
                            }else{
                                $("#error").html('');
                                return true
                            }
                        }
                    },
                }).then((result) => {
                    if (result.value) {
                        this.httpRequest({
                            method: 'post',
                            url: 'factura/reenviar_correo',
                            data: {
                                id_factura : item.id_factura,
                                correos : $("#correos").val(),
                                carpeta : item.carpeta,
                                secuencial : item.secuencial,
                                razon_social : item.razon_social
                            }
                        }).then((res) => {

                            this.alertNotification({
                                param:{
                                    html: res.data.msg,
                                    timer:20000,
                                    grow:false,
                                    toast:false
                                }
                            })


                        })
                    }
                })
            },

            editarFactura(idFactura){

                this.editar=true
                this.httpRequest({
                    method: 'get',
                    url: 'factura/editar',
                    data: {
                        id_comprobante : idFactura,
                        'comprobante' : 'factura',
                    }
                }).then((res) => {
                    this.secuencialEdit = res.data.secuencial
                    this.idFactura = idFactura
                    this.puntoEmision = res.data.ptoEmi
                    this.facturero = res.data.facturero
                    this.fechaDocumento = res.data.fechaDoc
                    this.fechaVencimiento = res.data.fechaVenc
                    this.idSustentoTributario = res.data.sustTributario
                    this.comentario = res.data.comentario
                    this.idCliente = res.data.idCliente
                    this.idFormaPago = res.data.formaPago
                    this.idTipoPago = res.data.idTipoPago
                    this.correos = res.data.correos
                    this.plazo = res.data.plazo
                    this.undTiempoPlazo = res.data.unTiempoPlazo
                    this.dialog = true

                    this.articulosFactura=[]
                    for(let articulo  of res.data.articulos){
                        this.articulosFactura.push({
                            articulos: this.inventario.categorias_activadas.find(e => e.id_categoria_inventario === articulo.id_categoria_inventario).articulos,
                            id_categoria: articulo.id_categoria_inventario,
                            id_articulo: articulo.id_articulo_categoria_inventario,
                            cantidad: articulo.cantidad,
                            descuento: articulo.descuento,
                            total: articulo.precio_total_sin_imp,
                            monto: articulo.precio_unitario,
                        })
                    }

                    this.calculaMontos()

                })



            },

            searchDataTable(){
                if(this.dateRangeText.split('~').length===2)
                    this.getDataComponent()
            },

            consultarFactura(item){

                this.overlay=true
                this.httpRequest({
                    method: 'post',
                    url: 'factura/consultar',
                    data: {
                        comprobante:'factura',
                        id_usuario: item.id_usuario,
                        id_comprobante : item.id_factura,
                        clave_acceso :item.clave_acceso,
                        carpeta_personal : item.carpeta
                    }
                }).then((res) => {
                    this.overlay=false
                    this.alertNotification({
                        param: {
                            title: res.data.success ? 'Exito' : 'Error',
                            icon: res.data.success ? 'success' : 'error',
                            confirmButtonColor: res.data.success ? '#a5dc86' : '#ff7674',
                            html: res.data.msg,
                            toast: false,
                            grow: false,
                            timer: 45000
                        }
                    })
                    this.getDataComponent()
                }).catch((err) => {
                    this.overlay = false
                    this.dialog = true
                })
            }

        },

        created () {
            this.getDataComponent()

            if(this.factureros.length===1)
                this.facturero = this.factureros[0].numero

            if(this.punto_emision.length===1)
                this.puntoEmision = this.punto_emision[0].numero

            this.undTiempoPlazo='Días'

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

    .list-actions{
        min-height: 35px!important;
    }

    .link{
        text-decoration:none!important
    }


</style>