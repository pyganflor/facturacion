<template>
    <v-navigation-drawer
            id="core-navigation-drawer"
            v-model="$store.state.drawer"
            :dark="barColor !== 'rgba(228, 226, 226, 1), rgba(255, 255, 255, 0.7)'"
            :expand-on-hover="expandOnHover"
            :right=false
            :src="barImage"
            mobile-breakpoint="640"
            app
            width="230"
    >
        <template v-slot:img="props">
            <v-img
                    :gradient="`to bottom, ${barColor}`"
                    v-bind="props"
            />
        </template>

        <v-divider class="mb-1" />

        <v-list
                dense
                nav
        >
            <v-list-item>
                <v-list-item-avatar
                        class="align-self-center"
                        color="white"
                        contain
                >
                    <v-img
                            :src="usuario.imagen === null  ? '/imagenes/icono_usuario.png' : storage+'/'+usuario.imagen"
                            max-height="42"
                    />
                </v-list-item-avatar>


                <v-list-item-content>

                    <v-list-item-title
                            class="display-1 py-2"
                            v-text="profile.title"
                    ></v-list-item-title>

                </v-list-item-content>
            </v-list-item>
        </v-list>

        <v-divider class="mb-2" />

        <v-list
                expand
                nav
        >
            <v-subheader>Menú</v-subheader>

            <v-list-item-group
                    v-model="item"
                    color="primary"
            >

                <v-list-item
                        link
                        dense
                        href="/"
                >
                    <v-list-item-icon>
                        <v-icon >mdi-home-variant</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Inicio</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-group
                        sub-group
                        prepend-icon="mdi-vector-circle"
                        class="ml-n5"
                        dense
                        v-if=""

                >
                    <template  v-slot:activator >
                        <v-list-item-content >
                            <v-list-item-title
                                    style="font-size: .8125rem;font-weight: 500;line-height: 1rem;"
                                    class="ml-3">Configuración</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                            v-for="(ic, x) in itemsConfig"
                            :key="x"
                            dense
                            :href=ic.url
                    >
                        <v-list-item-icon>
                            <v-icon v-text="ic.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                                v-text="ic.title"
                                dense
                        ></v-list-item-title>
                    </v-list-item>
                </v-list-group>

                <v-list-group
                        sub-group
                        prepend-icon="mdi-cash-usd"
                        class="ml-n5"
                        dense
                        v-if="moduloVentas.length>0"
                >
                    <template  v-slot:activator >
                        <v-list-item-content >
                            <v-list-item-title
                                    style="font-size: .8125rem;font-weight: 500;line-height: 1rem;"
                                    class="ml-3">Módulo ventas</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                            v-for="(venta, x) in moduloVentas"
                            :key="x"
                            dense
                            :href=venta.modulo.url
                    >
                        <v-list-item-icon>
                            <v-icon v-text="venta.modulo.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                                v-text="venta.modulo.nombre"
                                dense
                        ></v-list-item-title>
                    </v-list-item>
                </v-list-group>

                <v-list-group
                        sub-group
                        prepend-icon="mdi-cash"
                        class="ml-n5"
                        dense
                        v-if="moduloCompras.length>0"
                >
                    <template  v-slot:activator >
                        <v-list-item-content >
                            <v-list-item-title
                                    style="font-size: .8125rem;font-weight: 500;line-height: 1rem;"
                                    class="ml-3">Módulo compras</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                            v-for="(venta, x) in moduloCompras"
                            :key="x"
                            dense
                            :href=venta.modulo.url
                    >
                        <v-list-item-icon>
                            <v-icon v-text="venta.modulo.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                                v-text="venta.modulo.nombre"
                                dense
                        ></v-list-item-title>
                    </v-list-item>
                </v-list-group>

                <v-list-item
                        v-for="(iu, i) in this.itemsUser"
                        :key="i"
                        link
                        dense
                        :href=iu.url
                >
                    <v-list-item-icon>
                        <v-icon v-text="iu.icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title v-text="iu.title"></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

            </v-list-item-group>

        </v-list>
    </v-navigation-drawer>
</template>

<script>

    import { mapState } from 'vuex'

    export default {
        props: {
            expandOnHover: {
                type: Boolean,
                default: false,
            },
            usuario: {
                required : true
            },
            roles:{
                type: Array,
                default: [2]
            },
            modulos:{
                type: Array,
                default: []
            },
            storage:{
                required:true,
                type:String
            }
        },
        data: () => ({
            item: 0,
            itemsConfig:[],
            itemsUser:[],
            moduloVentas:[],
            moduloCompras:[],
            menuConfig:[
                {
                    usuario:[
                        {
                            title: 'Perfil',
                            icon: 'mdi-account-check',
                            url:'perfil'
                        },
                        {
                            title: 'Clientes',
                            icon: 'mdi-account-circle',
                            url:'cliente'
                        },
                        {
                            title: 'Proveedores',
                            icon: 'mdi-account-star',
                            url:'proveedor'
                        },
                        {
                            title: 'Inventario',
                            icon: 'mdi-package-variant',
                            url:'inventario'
                        },
                    ],
                    adminsirtador:[
                        {
                            title: 'Usuarios',
                            icon: 'mdi-account-multiple',
                            url:'usuario'
                        },
                        {
                            title: 'Módulos',
                            icon: 'mdi-view-dashboard',
                            url:'modulo'
                        },
                        {
                            title: 'Tienda',
                            icon: 'mdi-store',
                            url : 'tienda'
                        }
                    ]
                }
            ],
            menuUsuario:[
                /*{
                    title: 'Documentos',
                    icon: 'mdi-file-pdf',
                    url:'documento'
                },*/
                {
                    title: 'Formularios',
                    icon: 'mdi-clipboard-text',
                    url:'formulario'
                }
            ],
        }),
        computed: {
            ...mapState(['barColor', 'barImage']),
            profile () {
                return {
                    avatar: true,
                    title: this.usuario.nombre,
                }
            },
        },
        methods: {
            itemsMenu(){
                for(let rol of this.roles){
                    for(let mc of this.menuConfig){
                        if(rol===2){ //USUARIO
                            for(let usuario of mc.usuario)
                                this.itemsConfig.push(usuario)
                        }else if(rol===1){ // ADMINISTRADOR
                            for(let adminsirtador of mc.adminsirtador)
                                this.itemsConfig.push(adminsirtador)
                        }
                    }

                    if(rol===2)
                        for(let mu of this.menuUsuario)
                            this.itemsUser.push(mu)

                }

                for(let moduldo of this.modulos){
                    if(moduldo.modulo.tipo === 1){ //VENTAS
                        this.moduloVentas.push(moduldo)
                    }else if(moduldo.modulo.tipo === 2){ //COMPRAS
                        this.moduloCompras.push(moduldo)
                    }
                }
            }
        },
        created(){
            this.itemsMenu();
        }

    }
</script>
