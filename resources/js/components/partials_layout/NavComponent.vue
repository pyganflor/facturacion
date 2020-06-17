<template>
        <v-app-bar
                id="app-bar"
                absolute
                app
                color="primary"
                flat
                height="50"
        >
            <v-btn
                    class="mr-3 white--text"
                    elevation="1"
                    fab
                    small
                    @click="setDrawer"
                    :depressed=true
                    color="transparent"
            >
                <v-icon v-if="drawer"> mdi-close </v-icon>
                <v-icon v-else> mdi-dots-vertical </v-icon>
            </v-btn>

            <v-toolbar-title
                    class="hidden-sm-and-down font-weight-light"
            />

            <v-spacer />

            <div class="mx-3" />

            <v-menu
                    bottom
                    left
                    offset-y
                    origin="top right"
                    transition="scale-transition"
            >
                <template v-slot:activator="{ on }">
                    <v-btn
                            class="ml-2 white--text"
                            min-width="0"
                            text
                            fab
                            small
                            v-on="on"
                    >
                        <v-badge
                                color="red"
                                overlap
                                bordered
                        >
                            <template v-slot:badge>
                                <span>0</span>
                            </template>

                            <v-icon>mdi-bell</v-icon>
                        </v-badge>
                    </v-btn>
                </template>

                <v-list
                        :tile="false"
                        nav
                >
                    <div >
                        <app-bar-item
                                v-for="(n, i) in notifications"
                                :key="i"
                        >
                            <v-list-item-title class="py-1">
                                <v-icon color="warning">mdi mdi-exclamation</v-icon> {{n}}
                            </v-list-item-title>
                        </app-bar-item>
                    </div>
                </v-list>
            </v-menu>

            <v-btn
                    class="ml-2 white--text"
                    min-width="0"
                    text
                    link
                    href="/logout"
            >
                <v-icon>mdi-logout-variant</v-icon> Salir
            </v-btn>
        </v-app-bar>

</template>

<script>
    import { mapState, mapMutations } from 'vuex'

    export default {
        props: {
            usuario: {
                required : true
            }
        },

        data: () => ({
            notifications: [
                'Mike John Responded to your email',
                'You have 5 new tasks',
                'You\'re now friends with Andrew',
                'Another Notification',
                'Another one',
            ],
            items:[
                {
                    text: 'Dashboard',
                    disabled: false,
                    href: 'breadcrumbs_dashboard',
                },
                {
                    text: 'Link 1',
                    disabled: false,
                    href: 'breadcrumbs_link_1',
                },
                {
                    text: 'Link 2',
                    disabled: true,
                    href: 'breadcrumbs_link_2',
                },
            ]
        }),

        computed: {
            ...mapState(['drawer']),
        },

        methods: {
            ...mapMutations({
                setDrawer: 'setDrawer',
            }),
        },
    }
</script>
