<template>
    <header id="masthead" class="site-header" role="banner">
        <div class="row">
            <div class="columns large-2 medium-2 small-2">
                <router-link :to="{ name: 'home'}" class="site-name"> {{ site_name }} </router-link>
            </div>
        </div>
        <div class="row">
            <div class="columns large-8 medium-8 small-8">
                <span id="site-description">
                    {{ site_description }}
                </span>
            </div>
        </div>

        <div class="row">
            <div class="column large-12 medium-12 small-12">
                <nav id="site-navigation" v-if="loaded === 'true'">
                    <ul>
                        <li v-for="item in menus" v-if="item.type != 'custom'">
                            <router-link :to="{ name: 'page', params: { name: getUrlName( item.url ) }}"> {{ item.title }} </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
</template>

<script>
  export default {
    mounted: function () {
      // console.log( wp.api.collections );
      this.getMenu()
    },
    data () {
      return {

        menus: [],
        site_name: rtwp.site_name,
        site_description: rtwp.site_description,
        isActive: false,
        loaded: 'false'

      }
    },
    methods: {

      getMenu: function () {
        const vm = this
        vm.loaded = 'false'

        vm.$http.get('wp-api-menus/v2/menu-locations/primary-menu')
          .then((res) => {
            console.log('getMenu')
            console.log(vm)
            vm.menus = res.data
            vm.loaded = 'true'
          })
          .catch((res) => {
            // console.log( `Something went wrong : ${ res }` );
          })
      },
      getUrlName: function (url) {
        const array = url.split('/')
        return array[ array.length - 2 ]
      },
      toggleMenu: function () {
        // console.log("Clicked" + this.isActive);
        this.isActive = !this.isActive
      }
    }
  }
</script>

<style>


</style>
