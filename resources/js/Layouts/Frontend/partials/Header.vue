<template>
    <!-- Header -->
    <header id="page-header">
        <!-- Header Content -->
        <div class="content-header">
            <!-- Left Section -->
            <div class="content-header-section">
                <!-- Logo -->
                <div class="content-header-item height-50">
                    <a :href="route('home')">
                        <img :src="asset($page.props.settings.app_logo)" class="img-fluid h-100">
                    </a>
                </div>
                <!-- END Logo -->
            </div>
            <!-- END Left Section -->

            <!-- Right Section -->
            <div class="content-header-section d-flex">
                
                <CartHeader/>

                <template v-if="$page.props.auth.user">
                    <b-dropdown variant="dual link" class="d-inline-block ml-2" menu-class="p-0 border-0 dropdown-menu-md" right no-caret ref="oneDropdownDefaultUser">
                    <template #button-content>
                    <div class="d-flex align-items-center">
                        <span class="d-none d-sm-inline-block ml-2">{{ $page.props.auth.user.name }}</span>
                        <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                    </div>
                    </template>
                    <div class="p-2">
                        <div role="separator" class="dropdown-divider"></div>
                        <Link :href="route('logout')" method="post" as="button" class="dropdown-item d-flex align-items-center justify-content-between">
                            <span class="font-size-sm font-w500">Log Out</span>
                        </Link >
                    </div>
                </b-dropdown>
                </template>
                <template v-else>
                    <div class="float-right my-auto">
                        <Link type="button" class="btn btn-outline-primary mx-1 btn-login" :href="route('login')">
                            Masuk
                        </Link>
                        <Link type="button" class="btn btn-primary btn-noborder mx-1 btn-login" :href="route('register')">
                            Daftar
                        </Link>
                    </div>
                </template>
            </div>
            <!-- END Right Section -->
        </div>
        <!-- END Header Content -->
    </header>
    <!-- END Header -->
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue';
    import CartHeader from '@/components/Cart/cartHeader.vue';
    export default {
        name: 'BaseHeader',
        components: {
            Link,
            CartHeader,
        },
        props: {
            user: Object
        },
        data () {
    return {
      baseSearchTerm: '',
      notifications: [
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-check-circle text-success',
          title: 'You have a new follower',
          time: '15 min ago'
        },
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-plus-circle text-primary',
          title: '1 new sale, keep it up',
          time: '22 min ago'
        },
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-times-circle text-danger',
          title: 'Update failed, restart server',
          time: '26 min ago'
        },
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-plus-circle text-primary',
          title: '2 new sales, keep it up',
          time: '33 min ago'
        },
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-user-plus text-success',
          title: 'You have a new subscriber',
          time: '41 min ago'
        },
        {
          href: 'javascript:void(0)',
          icon: 'fa fa-fw fa-check-circle text-success',
          title: 'You have a new follower',
          time: '42 min ago'
        }
      ]
    }
  },
    }

</script>
