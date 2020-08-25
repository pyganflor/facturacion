<template>
    <v-alert
            color="primary"
            dark
            :icon=icon
            border="left"
            dense
    >
        {{descripcion}}
    </v-alert>
</template>

<script>
    export default {
        name: "DescripcionVistaComponent",
        props:{
            descripcion:{
                type: String,
                required:true
            },
            icon:{
                type:String,
                required:true
            }
        }
    }

</script>

<style scoped>

</style>