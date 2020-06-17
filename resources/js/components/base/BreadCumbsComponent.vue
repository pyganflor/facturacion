<template>
    <div>
    <v-breadcrumbs
            :items=items
            class="grey lighten-4 text-capitalize py-3"
    >
        <template v-slot:divider>
            <v-icon>mdi-chevron-double-right</v-icon>
        </template>
        <template v-slot:item="{ item }">
            <v-breadcrumbs-item
                    :href="item.href"
            >
                <v-icon color="primary" v-text=item.icon></v-icon> {{item.text }}
            </v-breadcrumbs-item>
        </template>
    </v-breadcrumbs>
    </div>
</template>

<script>
    export default {
        name: "BreadCumbsComponent",
        props: {
            path : {

            }
        },
        data: () => ({
            items:[
                {
                    text: 'Inicio',
                    disabled: false,
                    href: '/',
                    icon: 'mdi-home'
                },
                {
                    text: window.location.pathname.split("/")[1],
                    disabled: false,
                    href: window.location.pathname,
                    icon: 'mdi-link-variant'
                },
            ]
        })
    }
</script>

<style scoped>

</style>