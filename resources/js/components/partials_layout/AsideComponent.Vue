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
            src="https://demos.creative-tim.com/vuetify-material-dashboard/favicon.ico"
            max-height="30"
          />
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title
            class="display-1 py-2"
            v-text="profile.title"
          />
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
          v-for="(item, i) in items"
          :key="i"
          link
          dense
        >
          <v-list-item-icon>
            <v-icon v-text="item.icon"></v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="item.title"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
      <div />
    </v-list>

  </v-navigation-drawer>
</template>

<script>
  // Utilities
  import { mapState } from 'vuex'

  export default {
    props: {
      expandOnHover: {
        type: Boolean,
        default: false,
      },
    },

    data: () => ({
        item: 1,
        items: [
            {
            icon: 'mdi-view-dashboard',
            title: 'dashboard'
            },
            {
            icon: 'mdi-account',
            title: 'user'
            },
            {
            title: 'rtables',
            icon: 'mdi-clipboard-outline'
            },
            {
            title: 'typography',
            icon: 'mdi-format-font'
            },
            {
            title: 'icons',
            icon: 'mdi-chart-bubble'
            },
            {
            title: 'google',
            icon: 'mdi-map-marker'
            },
            {
            title: 'notifications',
            icon: 'mdi-bell'
            },
        ],
    }),

    computed: {
      ...mapState(['barColor', 'barImage']),
      profile () {
        return {
          avatar: true,
          title: 'Avatar',
        }
      },
    },

    methods: {


    },
  }
</script>
