<template>
  <v-card
    class="mx-auto"
    max-width="350"
    :loagin=loading
  >
    <v-img
        :contain=true
        height="200px"
        src="./imagenes/login.jpg"
        class='primary'
    >
    </v-img>

    <v-card-subtitle class="pb-0">Inicia sesión</v-card-subtitle>

    <div class="px-4">
        <v-text-field
            v-model="usuario"
            :counter="6"
            :rules="usuarioRules"
            label="Usuario"
            required
        />
    </div>

    <v-card-actions class=text-center>
        <v-btn
            color="white"
            icon='mdi-floppy'
        >
            Guardar
        </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>

    export default{

        data: () =>({
            loading:false,
            usuario: '',
            usuarioRules:[
                v => !!v || 'El usuario es obligatorio',
                v => (v && v.length <= 6) || 'Debe escribir almenos 6',
            ]
        })


    }

</script>
