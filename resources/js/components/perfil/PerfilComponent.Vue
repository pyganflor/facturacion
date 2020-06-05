<template>
    <v-container
            id="user-profile"
            fluid
            tag="section"
            color="primary"
    >
        <v-row justify="center">
            <v-col
                    cols="12"
                    md="8"
            >
                <base-material-card
                        color="primary"
                >
                    <template v-slot:heading>
                        <div class="display-1 font-weight-light">
                            Editar perfil
                        </div>
                        <div class="subtitle-1 font-weight-light">
                            Completa o edita tu perfil para la facturación electrónica y de sesión
                        </div>
                    </template>

                    <v-form>
                        <v-container class="py-0">
                            <v-row>
                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="Company (disabled)"
                                            disabled
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            class="purple-input"
                                            label="User Name"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="Email Address"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="6"
                                >
                                    <v-text-field
                                            label="First Name"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="6"
                                >
                                    <v-text-field
                                            label="Last Name"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field
                                            label="Adress"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="City"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            label="Country"
                                            class="purple-input"
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        md="4"
                                >
                                    <v-text-field
                                            class="purple-input"
                                            label="Postal Code"
                                            type="number"
                                    />
                                </v-col>

                                <v-col cols="12">
                                    <v-textarea
                                            class="purple-input"
                                            label="About Me"
                                            value="Lorem ipsum dolor sit amet, consectetur adipiscing elit."
                                    />
                                </v-col>

                                <v-col
                                        cols="12"
                                        class="text-right"
                                >
                                    <v-btn
                                            color="success"
                                            class="mr-0"
                                    >
                                        Update Profile
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </base-material-card>
            </v-col>

            <v-col
                    cols="12"
                    md="4"
            >
                <base-material-card
                        class="v-card-profile"
                        :avatar="usuario.imagen === null  ? '/imagenes/icono_usuario.png' : usuario.imagen"
                >

                    <v-form v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12"

                                >
                                    <v-text-field
                                            v-model="usuario.nombre"
                                            label="Usuario (De inicio de sesión)"
                                            required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" >
                                    <v-text-field
                                            :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                            :rules="passRules"
                                            :type="show1 ? 'text' : 'password'"
                                            label="Antigua contraseña"
                                            v-model="olPass"
                                            class="input-group--focused"
                                            @click:append="show = !show"
                                            required
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" >
                                    <v-text-field
                                            :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                            :rules="passRules"
                                            :type="show2 ? 'text' : 'password'"
                                            label="Nueva contraseña"
                                            v-model="pass"
                                            class="input-group--focused"
                                            @click:append="show = !show"
                                            required
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </base-material-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>

    export default {
        props :{
            usuario:{
                required:true
            }
        },
        data:() =>({
            valid:false,
            show1:false,
            show2:false,
            usuarioRules:[
                v => !!v || 'El usuario es obligatorio',
                v => (v && v.length >= 6) || 'Debe escribir almenos 6'
            ],
            passRules:[
                v => !!v || 'La contraseña es obligatorio',
                v => (v && v.length >= 5) || 'Debe escribir almenos 5',
            ]

        })

    }
</script>
