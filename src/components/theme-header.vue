<template>

	<header id="masthead" class="site-header" role="banner">

		<div class="row">

			
			<div class="column large-10 medium-10 small-10">

				<router-link :to="{ name: 'home'}" class="site-name"> {{ site_name }} </router-link>
				<span>
					{{ site_description }}
				</span>

			</div>

			<div class="column large-2 medium-2 small-2 end">

				<!--<a id="nav-icon1" v-on:click="toggleMenu" v-bind:class="{open: isActive}">-->
					<!--<div></div>-->
					<!--<div></div>-->
					<!--<div></div>-->
				<!--</a>-->

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
