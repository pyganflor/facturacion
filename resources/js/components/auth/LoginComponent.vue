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

    <v-col cols="12" sm="12" class="py-0  px-4">
        <v-text-field
            v-model="usuario"
            :counter="6"
            :rules="usuarioRules"
            label="Usuario"
            required
        />
    </v-col>

    <v-col cols="12" sm="12" class="px-4 py-0">
        <v-text-field
            :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="passRules"
            :type="show ? 'text' : 'password'"
            label="Contraseña"
            :counter="5"
            v-model="pass"
            class="input-group--focused"
            @click:append="show = !show"
            required
        ></v-text-field>
    </v-col>

    <v-col cols="12" sm="12" class="px-4 py-0 mt-0">
        <v-checkbox
            v-model="recordar"
            label="Recordarme"
        ></v-checkbox>
    </v-col>

    <v-col cols="12" sm="12" >
        <v-card-actions>
            <v-btn
                color="primary"
                :block=true
                class="px-0"
            >
                <v-icon>mdi mdi-login-variant</v-icon> Ingresar
            </v-btn>
        </v-card-actions>
        <v-btn
                text
                link
                :block=true
            >
                Terminos y condiciones
        </v-btn>
    </v-col>
  </v-card>
</template>

<script>

    export default{

        data: () =>({
            loading:false,
            usuario: '',
            pass:'',
            show : false,
            recordar : false,
            usuarioRules:[
                v => !!v || 'El usuario es obligatorio',
                v => (v && v.length >= 6) || 'Debe escribir almenos 6'
            ],
            passRules:[
                v => !!v || 'La contraseña es obligatorio',
                v => (v && v.length >= 5) || 'Debe escribir almenos 5',
            ]
        }),
        methods:{

            login(){
                this.loading=true
            }
        }


    }

</script>
