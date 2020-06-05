<template>
    <v-card
            class="mx-auto"
            max-width="350"
            secondary
    >
        <v-img
            :contain=true
            height="200px"
            src="./imagenes/login.jpg"
            class='primary'
        >
        </v-img>

        <v-card-subtitle class="pb-0">Inicia sesión</v-card-subtitle>
        <v-form v-model="valid" ref="form">

            <v-col cols="12" sm="12" class="py-0  px-4">
                <v-text-field
                    v-model="usuario"
                    :rules="usuarioRules"
                    label="Usuario"
                    required
                ></v-text-field>
            </v-col>

            <v-col cols="12" sm="12" class="px-4 py-0">
                <v-text-field
                    :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="passRules"
                    :type="show ? 'text' : 'password'"
                    label="Contraseña"
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
                        @click="login"
                        :loading="$store.state.loadingBtn"
                    >
                        <v-icon>mdi mdi-login-variant</v-icon> Ingresar
                    </v-btn>
                </v-card-actions>
                <v-btn
                        text
                        link
                        :block=true
                        @click="terminosCondiciones"
                    >
                        Terminos y condiciones
                </v-btn>
            </v-col>
        </v-form>
    </v-card>
</template>

<script>

    export default{

        data: () =>({
            valid: false,
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

                if (!this.$refs.form.validate())
                    return;

                this.$store.commit('setLoadingBtn')
                axios.post('/login', {
                    remember :this.recordar,
                    usuario: this.usuario,
                    contrasena: this.pass
                }).then(response => {
                    alert('Has iniciado sesión');
                    window.location = '/';
                    this.$store.commit('setLoadingBtn')
                }).catch(error => {
                    console.log(error);
                    let response = error.response;

                    this.$store.dispatch({
                        type: 'errorRequest',
                        datos: response.data.errors,
                        status : response.status
                    });
                });
            },

            terminosCondiciones(){

            }
        }

    }

</script>
