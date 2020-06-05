<template>
    <v-container
            id="dashboard"
            fluid
            tag="section"
    >
        <v-row>
            <v-col
                    cols="12"
                    sm="4"
            >
                <v-card
                        class="mx-auto"
                        max-width="300"

                >
                    <v-list-item>
                        <v-list-item-avatar
                                tile
                                size="80"
                                color="primary"
                                class="item_avatar_dashboard"
                        >   <v-icon large class="white--text">mdi-file-chart</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content class="text-bold text-right">
                            <v-list-item-subtitle>Monto</v-list-item-subtitle>
                            <div class="mb-4 headline">$123.33</div>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider class="mx-4"></v-divider>
                    <v-card-actions class="py-0 px-4">
                        <v-list-item-title class="mb-1">
                            <v-icon>mdi-cash-multiple</v-icon> Facturado
                        </v-list-item-title>
                    </v-card-actions>
                </v-card>

            </v-col>
            <v-col
                    cols="12"
                    sm="4"
            >
                <v-card
                        class="mx-auto"
                        max-width="300"
                >
                    <v-list-item>
                        <v-list-item-avatar
                                tile
                                size="80"
                                color="info"
                                class="item_avatar_dashboard"
                        >   <v-icon large class="white--text">mdi-account-multiple</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content class="text-bold text-right">
                            <v-list-item-subtitle>Cantidad</v-list-item-subtitle>
                            <div class="mb-4 headline">60</div>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider class="mx-4"></v-divider>
                    <v-card-actions class="py-0 px-4">
                        <v-list-item-title class="mb-1">
                            <v-icon>mdi-account-settings</v-icon> Usuarios
                        </v-list-item-title>
                    </v-card-actions>
                </v-card>

            </v-col>
            <v-col
                    cols="12"
                    sm="4"
            >
                <v-card
                        class="mx-auto"
                        max-width="300"
                >
                    <v-list-item>
                        <v-list-item-avatar
                                tile
                                size="80"
                                color="warning"
                                class="item_avatar_dashboard"
                        >
                            <v-icon large class="white--text">mdi-view-dashboard</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content class="text-bold text-right">
                            <v-list-item-subtitle>Cantidad</v-list-item-subtitle>
                            <div class="mb-4 headline">3</div>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider class="mx-4"></v-divider>
                    <v-card-actions class="py-0 px-4">
                        <v-list-item-title class="mb-1">
                            <v-icon>mdi-shape-circle-plus</v-icon> Módulos
                        </v-list-item-title>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    export default {

        data: () =>({

        }),

        methods: {

        },
    }
</script>
