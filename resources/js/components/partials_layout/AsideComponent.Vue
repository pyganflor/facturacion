<template>
    <v-navigation-drawer
            id="core-navigation-drawer"
            v-model="$store.state.drawer"
            :dark="barColor !== 'rgba(228, 226, 226, 1), rgba(255, 255, 255, 0.7)'"
            :expand-on-hover="expandOnHover"
            :right=false
            :src="barImage"
            mobile-break-point="640"
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
                            :src="usuario.imagen === null  ? '/imagenes/icono_usuario.png' : usuario.imagen"
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

            <v-list-item-group v-model="item" color="primary">
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

                <v-list-item
                        v-for="(item, i) in this.items"
                        :key="i"
                        link
                        dense
                        :href=item.url
                        v-if="item.title!=='Configuraciones'"
                >
                    <v-list-item-icon>
                        <v-icon v-text="item.icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title
                                v-text="item.title"
                        ></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-group
                        sub-group
                        v-else
                        :prepend-icon=item.icon
                        class="ml-n5"
                        dense

                >
                    <template  v-slot:activator >
                        <v-list-item-content >
                            <v-list-item-title
                                    style="font-size: .8125rem;font-weight: 500;line-height: 1rem;"
                                    class="ml-3" v-text="item.title"></v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                            v-for="(subMenu, x) in item.subMenu"
                            :key="x"
                            dense
                            :href=subMenu.url
                    >
                        <v-list-item-icon>
                            <v-icon v-text="subMenu.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-title
                                v-text="subMenu.title"
                                dense
                        ></v-list-item-title>
                    </v-list-item>
                </v-list-group>
            </v-list-item-group>
            <div />
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
                required : true,
                type: Array,
                default: [2]
            }
        },

        data: () => ({
            item: 0,
            items:[],
            itemsUsuario:[
                {
                    title: 'Facturar',
                    icon: 'mdi-cash-multiple',
                    url:'factura'
                },
                {
                    title: 'Documentos',
                    icon: 'mdi-file-pdf',
                    url:'documento'
                },
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
                    title: 'Productos',
                    icon: 'mdi-package-variant',
                    url:'producto'
                },
                {
                    title: 'Formularios',
                    icon: 'mdi-file-document',
                    url:'formulario'
                }
            ],
            itemsAdministrador:[
                {
                    title: 'Configuraciones',
                    icon: 'mdi-vector-circle',
                    subMenu:[
                        {
                            title: 'Usuarios',
                            icon: 'mdi-account-multiple',
                            url:'usuario'
                        },
                        {
                            title: 'Módulos',
                            icon: 'mdi-view-dashboard',
                            url:'modulo'
                        }
                        ,
                        {
                            title: 'Tienda',
                            icon: 'mdi-store',
                            url : 'tienda'
                        }
                    ]
                }
            ]
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
            itemsUsaurio(){
                for(let rol of this.roles){

                    if(rol===2)
                        for(let iu of this.itemsUsuario)
                            this.items.push(iu)

                    if(rol===1)
                        for(let ia of this.itemsAdministrador)
                            this.items.push(ia)
                }

            }
        },
        created(){
            this.itemsUsaurio();
        }

    }
</script>
