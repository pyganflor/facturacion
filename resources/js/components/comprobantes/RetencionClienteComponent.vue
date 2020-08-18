<template>
    <div>
        <v-overlay :value="overlay" z-index="999">
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
                item-key="id_retencion_cliente"
                class="elevation-1"
                :loading=loadingTable
                :search=search
                :expanded.sync="expanded"
                :single-expand=true
                :show-expand=true
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
                                                    <v-card
                                                            flat
                                                            tile
                                                            color="grey lighten-4"
                                                    >
                                                        <v-container fluid>
                                                            <v-form ref="form_retencion_cliente">
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
                                                                                    v-model="secuencial"
                                                                                    label="Número de documento"
                                                                                    append-icon="mdi-numeric"
                                                                                    :rules="nDocRule"
                                                                                    counter="17"
                                                                                    v-mask="'###-###-#########'"
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="12" class="py-0" >
                                                                            <v-text-field
                                                                                    v-model="nAutorizacion"
                                                                                    label="Número de autorización"
                                                                                    append-icon="mdi-numeric"
                                                                                    :rules="nAutDocRule"
                                                                                    counter="10"
                                                                                    v-mask="'##########'"
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
                                                                                                    :rules="requiredRule"
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
                                                                                                    :rules="requiredRule"
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
                                                                                            :items="facts"
                                                                                            label="Factura"
                                                                                            item-text="secuencial"
                                                                                            item-value="id_factura"
                                                                                            :rules="requiredRule"
                                                                                            v-model="idFactura"
                                                                                            @change="setCliente"
                                                                                            dense
                                                                                    >
                                                                                        <template v-slot:no-data>
                                                                                            <div class="ml-2">
                                                                                                <v-icon>mdi-alert-circle-outline</v-icon> Sin facturas
                                                                                            </div>
                                                                                        </template>
                                                                                    </v-autocomplete>
                                                                                </v-col>
                                                                                <v-col cols="12" class="py-0">
                                                                                    <v-autocomplete
                                                                                            class="mt-5"
                                                                                            :items="clientes"
                                                                                            label="Cliente"
                                                                                            item-text="nombre"
                                                                                            item-value="id_cliente"
                                                                                            :rules="requiredRule"
                                                                                            v-model="idClienteForm"
                                                                                            :readonly="readOnlyClientes"
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
                                                                                                :rules="requiredRule"
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
                                                                                    :loading=loadingBtn
                                                                            >
                                                                                <v-icon>mdi-content-save</v-icon>
                                                                                <span class="d-none d-md-block">Procesar</span>
                                                                            </v-btn>
                                                                        </v-col>
                                                                    </v-col>
                                                                </v-row>
                                                            </v-form>
                                                        </v-container>
                                                    </v-card>
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
                                                                    <v-row v-if="resultadoRetenciones">
                                                                        <v-col cols="12">
                                                                            <v-alert
                                                                                    color="primary"
                                                                                    border="left"
                                                                                    elevation="2"
                                                                                    colored-border
                                                                                    icon="mdi-check-circle-outline"
                                                                                    dense
                                                                            >
                                                                                Resultado de las retenciones de clientes cargadas
                                                                            </v-alert>
                                                                        </v-col>
                                                                        <v-col cols="12">
                                                                            <v-simple-table dense>
                                                                                <template v-slot:default>
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="text-left">N°</th>
                                                                                            <th class="text-left">Fecha</th>
                                                                                            <th class="text-left">Número</th>
                                                                                            <th class="text-left">Cliente</th>
                                                                                            <th class="text-left">Factura</th>
                                                                                            <th class="text-left">T. renta</th>
                                                                                            <th class="text-left">T. iva</th>
                                                                                            <th class="text-left">Total</th>
                                                                                            <th class="text-left">Acción</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr
                                                                                                v-for="(item, x) in dataTableRetencion"
                                                                                                :key="item.factura"
                                                                                        >
                                                                                            <td>{{ x+1 }}</td>
                                                                                            <td>{{ item.fecha }}</td>
                                                                                            <td>{{ item.numero }}</td>
                                                                                            <td>{{ item.cliente }}</td>
                                                                                            <td>{{ item.factura }}</td>
                                                                                            <td>${{ item.t_iva }}</td>
                                                                                            <td>${{ item.t_renta }}</td>
                                                                                            <td>${{ item.total }}</td>
                                                                                            <td>
                                                                                                <v-icon
                                                                                                    @click="removeRetencion(item)"
                                                                                                >
                                                                                                    mdi-delete-forever
                                                                                                </v-icon>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr v-if="dataTableRetencion.length===0">
                                                                                            <td class="text-center" colspan="9">
                                                                                                <v-icon>mdi-alert-circle-outline</v-icon>
                                                                                                Sin retenciones consultadas para registrar
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </template>
                                                                            </v-simple-table>
                                                                        </v-col>
                                                                        <v-col
                                                                                cols="12"
                                                                                class="text-center"
                                                                                v-if="dataTableRetencion.length>0"
                                                                        >
                                                                            <v-btn
                                                                                    class="primary"
                                                                                    @click="storeRetencionAsistido"
                                                                            >
                                                                                <v-icon>mdi-content-save</v-icon> Registrar
                                                                            </v-btn>
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
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    <div class="mt-2 font-weight-bold">
                        <v-icon>mdi-format-list-bulleted</v-icon> Detalles de la retención
                    </div>
                    <v-simple-table
                            dense
                            class="mb-5 mt-2"
                    >
                        <template v-slot:default>
                            <thead>
                            <tr class="grey lighten-2">
                                <th class="text-left w-50">Concepto</th>
                                <th class="text-left">Base imponible</th>
                                <th class="text-left">Porcentaje</th>
                                <th class="text-left">Valor retenido</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                    v-for="detalle in item.detalles"
                                    :key="detalle.id_detalle_retencion_cliente"
                            >
                                <td>{{ detalle.concepto }}</td>
                                <td>${{ detalle.base_imponile }}</td>
                                <td>{{ detalle.porcentaje }}%</td>
                                <td>${{ detalle.valor_retenido }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </td>
            </template>
            <template v-slot:no-results="{ item }">
                No se encontraron retenciones registradas
            </template>
            <template v-slot:no-data>
                Sin retenciones registradas
            </template>
            <template v-slot:item.total="{ item }">
                <b>${{item.total}}</b>
            </template>
            <template v-slot:item.t_iva="{ item }">
                <b>${{item.t_iva}}</b>
            </template>
            <template v-slot:item.t_renta="{ item }">
                <b>${{item.t_renta}}</b>
            </template>
            <template v-slot:item.estado="{ item }">
                {{estados.find(e => e.id === !!item.estado).nombre}}
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
                        <v-list-item class="list-actions" v-if="item.estado">
                            <v-list-item-title>
                                <v-btn
                                        text small
                                        @click="anularRetencion(item)"
                                >
                                    <v-icon color="red">mdi-delete-forever</v-icon> Anular retención
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
                { text: 'F. retención', value: 'fecha_emision' },
                { text: 'Número', value: 'secuencial', align: 'center' },
                { text: 'Cliente', value: 'cliente' },
                { text: 'N° factura', value: 'n_factura'},
                { text: 'T. iva', value: 't_iva' },
                { text: 'T. renta', value: 't_renta', align: 'center'  },
                { text: 'Total', value: 'total', align: 'center'  },
                { text: 'Estado', value: 'estado' },
                { text: 'Detalles', value: 'data-table-expand', sortable: false, align: 'center' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
            estados:[
                {id:false, nombre: 'Anulada'},
                {id:true, nombre: 'Recibida'}
            ],
            txtRules:[
                v => !v || (!!v && v.name.split(".")[v.name.split(".").length-1]== "txt") || 'Debe cargar un archivo .txt'
            ],
            xmlRules:[
                v => !v || (!!v && v.name.split(".")[v.name.split(".").length-1]== "xml") || 'Debe cargar un archivo .xml'
            ],
            comentarioRule:[
                v=> !v || (!!v && v.length<=200) || "Sólo hasta 200 caracteres"
            ],
            requiredRule:[
                v => !!v || 'Campo obligatorio'
            ],
            nDocRule:[
                v => !!v || 'Campo obligatorio',
                v => (!!v && v.length === 17) || 'El número de documento debe ser de 15 dígitos'
            ],
            nAutDocRule:[
                v => !!v || 'Campo obligatorio',
                v => (!!v && v.length === 10) || 'El número de autorización debe ser de 10 dígitos'
            ],
            facts:[],
            itemsRetencion:[],
            dataTable:[],
            expanded: [],
            dataTableRetencion:[],
            secuencial:null,
            nAutorizacion:null,
            fechaDoc: new Date().toISOString().substr(0, 10),
            fechaCont: new Date().toISOString().substr(0, 10),
            comentario:null,
            idFactura:null,
            idClienteForm:null,
            totalBi:0,
            totalVr:0,
            readOnlyClientes:false,
            overlay:false,
            resultadoRetenciones:false,
            tab:null,
            txt:null,
            xml:null,
            idCliente:null,
            search:'',
            dialog:false,
            idClienteSearch: '',
            estado: true
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

            ...mapState(['loadingBtn','paramsAlertQuestion','loadingTable',])
        },
        methods:{
            ...mapActions(['httpRequest','alertNotification','errorRequest']),

            ...mapMutations(['setLoadingBtn','setLoadingTable']),

            getDataComponent(){
                this.setLoadingTable()
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
                    this.setDataTable(res.data)
                    this.setLoadingTable()
                })
            },

            readTxt(event){
                this.txt = typeof event != "undefined" ? event : null
            },

            readXml(event){
                this.xml = typeof event != "undefined" ? event : null
            },

            precesaDataFile(data){
                this.dataTableRetencion=[]

                for(let retencion of data){

                    let t_iva=0
                    let t_renta=0
                    let total=0
                    for(let detalle of retencion.retenciones){

                        if(detalle.codigoTipoImpuesto === 2 || detalle.codigoTipoImpuesto === 4){ //IVA

                            t_iva += parseFloat(detalle.valorRetenido)

                        }else if(detalle.codigoTipoImpuesto === 1){ //RENTA

                            t_renta += parseFloat(detalle.valorRetenido)

                        }

                    }

                    total+= (t_iva+t_renta)

                    this.dataTableRetencion.push({
                        fecha: retencion.fechaEmision,
                        numero: retencion.secuencial,
                        cliente: retencion.razonSocial,
                        factura:retencion.retenciones[0].numDocSustento,
                        t_iva : t_iva,
                        t_renta : t_renta,
                        total: total
                    })

                }
            },

            procesarTxt(){

                let formTxt = new FormData();
                formTxt.append('txt',this.txt)
                this.overlay=true

                axios.post('/retencion_cliente/procesar_txt',
                    formTxt
                ).then(response => {

                    let observacion = (response.data.x > 0 ||response.data.y > 0 || response.data.z > 0)
                    let retencionesConsultadas = response.data.retencionesConsultadas
                    this.resultadoRetenciones = retencionesConsultadas.length > 0
                    this.precesaDataFile(retencionesConsultadas)
                    this.alertNotification({
                        param:{
                            html: response.data.msg,
                            timer: observacion ? 40000 : 5000,
                            toast : !observacion,
                            grow : observacion ? 'none' : 'row',
                            icon : observacion ? 'warning' : 'success',
                            confirmButtonColor: observacion ? '#87adbd' : '#a5dc86',
                            title : observacion ? 'Alerta' : 'Éxito',
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
                }).then(()=>{
                    this.overlay=false
                });
            },

            procesarXml(){
                this.overlay=true
                let formXml = new FormData();
                formXml.append('xml',this.xml)
                axios.post('/retencion_cliente/procesar_xml',
                    formXml
                ).then(response => {
                    let observacion = (response.data.x > 0 || response.data.y > 0)
                    let retencionesConsultadas = response.data.retencionesConsultadas
                    this.resultadoRetenciones = retencionesConsultadas.length > 0
                    this.precesaDataFile(retencionesConsultadas)
                    this.overlay=false
                    this.alertNotification({
                        param:{
                            html: response.data.msg,
                            timer: observacion ? 40000 : 5000,
                            toast : !observacion,
                            grow : observacion ? 'none' : 'row',
                            icon : observacion ? 'warning' : 'success',
                            confirmButtonColor: observacion ? '#87adbd' : '#a5dc86',
                            title : observacion ? 'Alerta' : 'Éxito',
                        }
                    });
                }).catch(error => {
                    this.overlay=false
                    let response = error.response;

                    this.errorRequest({
                        data : {
                            datos: response.data.errors,
                            status : response.status
                        }
                    });
                });
            },

            setDataTable (item) {

                let data = !Array.isArray(item) ? [item.retencionCliente] : item
                this.dialog = false

                for (let retencion of data) {

                    let filterFecha = retencion.fecha_emision >= this.dates[0] && retencion.fecha_emision <= this.dates[1]

                    if(retencion.estado === this.estado && filterFecha){
                        let totalIva = 0
                        let totalRenta = 0
                        let detalles =[]

                        for (let detalle of retencion.detalles) {
                            if (detalle.codigo_tipo_impuesto === 2 || detalle.codigo_tipo_impuesto === 4) { //IVA e IVA PRESUNTIVO
                                totalIva += parseFloat(detalle.valor_retenido)
                            } else if (detalle.codigo_tipo_impuesto === 1) { //RENTA
                                totalRenta += parseFloat(detalle.valor_retenido)
                            }

                            let concepto = this.conceptos.find(e => e.id_detalle_impuesto_retencion == detalle.codigo_retencion)
                            detalles.push({
                                id_detalle_retencion_cliente : detalle.id_detalle_retencion_cliente,
                                concepto : concepto.nombre,
                                base_imponile : detalle.base_imponible,
                                porcentaje : detalle.porcentaje_retenido,
                                valor_retenido : detalle.valor_retenido
                            })
                        }

                        this.dataTable.unshift({
                            id_retencion_cliente: retencion.id_retencion_cliente,
                            fecha_emision: retencion.fecha_emision,
                            secuencial: retencion.secuencial,
                            cliente: this.clientes.find(e => e.id_cliente === retencion.id_cliente).nombre,
                            n_factura: retencion.factura.secuencial,
                            t_iva: totalIva.toFixed(2),
                            t_renta: totalRenta.toFixed(2),
                            total: (totalIva + totalRenta).toFixed(2),
                            estado: retencion.estado,
                            detalles: detalles
                        })

                    }
                }

            },

            anularRetencion(item){
                Vue.swal({
                    text: "¿Esta seguro de anular la retención "+item.secuencial+"?, esta acción no se puede revertir",
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {
                        this.httpRequest({
                            method:'post',
                            url:'retencion_cliente/anular_retencion',
                            data:{
                                idRetencionCliente: item.id_retencion_cliente,
                            }
                        }).then((res)=>{

                            this.alertNotification({
                                param: {
                                    html: res.data.msg
                                }
                            })

                            let index = this.dataTable.indexOf(item)
                            this.dataTable.splice(index,1)

                        })
                    }
                })
            },

            setCliente(){
                let cliente = this.facts.find(e => e.id_factura === this.idFactura)
                if(typeof cliente != "undefined"){
                    this.idClienteForm = cliente.id_cliente
                    this.readOnlyClientes=true
                }
            },

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
                    if(typeof  impuesto!= "undefined"){
                        let porcentaje = parseFloat(impuesto.porcentaje)
                        let baseImponible = isNaN(parseFloat(retencion.base_imponible)) ? 0 : parseFloat(retencion.base_imponible)
                        this.itemsRetencion[i].porcentaje = porcentaje
                        this.itemsRetencion[i].codigo_retencion = impuesto.codigo
                        let valorRetenido = baseImponible*(porcentaje/100)
                        this.itemsRetencion[i].valor_retenido = valorRetenido.toFixed(2)
                        this.totalBi += baseImponible
                        this.totalVr += valorRetenido
                    }
                    i++
                }
            },

            storeRetencionManual(){

                if(!this.$refs.form_retencion_cliente.validate())
                    return false

                this.setLoadingTable()
                this.setLoadingBtn()
                this.httpRequest({
                    method: 'post',
                    url: 'retencion_cliente/store_retencion_manual',
                    data: {
                        nAutorizacion : this.nAutorizacion,
                        secuencial: this.secuencial.split("-").join(""),
                        fechaDoc : this.fechaDoc,
                        fechaCont : this.fechaCont,
                        idCliente:  this.idClienteForm,
                        idFactura : this.idFactura,
                        comentario : this.comentario,
                        retenciones : this.itemsRetencion
                    }
                }).then((res) => {
                    this.setLoadingBtn()
                    this.setDataTable(res.data)
                    this.nAutorizacion = null
                    this.fechaDoc = new Date().toISOString().substr(0, 10)
                    this.fechaCont = new Date().toISOString().substr(0, 10)
                    this.idClienteForm = null
                    this.secuencial = null
                    this.comentario = null
                    this.totalBi = 0
                    this.totalVr = 0
                    this.itemsRetencion=[]
                    this.addItemRetencion()
                    this.setLoadingTable()
                    const obj = this.facts.find(e => e.id_factura === this.idFactura)
                    let index = this.facts.indexOf(obj)
                    this.facts.splice(index,1)
                    this.idFactura = null
                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })
                })
            },

            removeRetencion(item){

                Vue.swal({
                    text: "¿Esta seguro de quitar la retención "+item.numero+" de la lista?, no se registrará la retención en el sistema",
                    ...this.paramsAlertQuestion
                }).then((result) => {
                    if (result.value) {
                        this.overlay=true
                        this.httpRequest({
                            method:'post',
                            url:'retencion_cliente/remove_retencion',
                            data:item
                        }).then((res)=>{
                            this.alertNotification({
                                param: {
                                    html: res.data.msg
                                }
                            })
                            let index = this.dataTableRetencion.indexOf(item)
                            this.dataTableRetencion.splice(index,1)
                        }).then(()=>{
                            this.overlay=false
                        })
                    }
                })
            },

            storeRetencionAsistido(){

                this.setLoadingBtn()
                this.overlay=true
                this.httpRequest({
                    method: 'post',
                    url: 'retencion_cliente/store_retencion_asistido',
                    data: null
                }).then((res) => {
                    this.setLoadingBtn()
                    this.txt=null
                    this.dataTableRetencion=[]
                    this.resultadoRetenciones=false
                    this.setDataTable(res.data.retencionCliente)
                    this.alertNotification({
                        param: {
                            html: res.data.msg
                        }
                    })
                }).then(()=>{
                    this.overlay=false
                })
            }
        },
        created(){
            this.getDataComponent()
            this.addItemRetencion()
            this.facts = this.facturas
        }
    }
</script>
