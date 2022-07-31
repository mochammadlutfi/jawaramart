<template>
    <!-- Header -->
    <header id="page-header" class="bm-header shadow-sm">
        <!-- Header Content -->
        <div class="content-header">
            <!-- Left Section -->
            <div class="content-header-section" v-if="!$root.window.mobile">
                <!-- Logo -->
                <div class="content-header-item height-50">
                    <a :href="route('home')">
                        <img :src="asset($page.props.settings.app_logo)" class="img-fluid h-100">
                    </a>
                </div>
                <!-- END Logo -->
            </div>
            <!-- END Left Section -->


            <div class="content-header-section search-header">
                <a class="btn btn-circle btn-dual-secondary d-block d-lg-none my-auto" href="#" @click.prevent="routeBack" v-if="!route().current('home')">
                    <i class="fa fa-arrow-left"></i>
                </a>
                
                <div class="title-header" v-if="title && $root.window.mobile">
                    <h4>{{ title }}</h4>
                </div>
                <SearchBox v-else/>
            </div>

            <!-- Right Section -->
            <div class="content-header-section d-flex">
                <CartHeader/>
                <template v-if="$page.props.auth.user">
                    <b-dropdown variant="dual link" class="d-inline-block ml-2 d-none d-lg-block" menu-class="p-0 border-0 dropdown-menu-user" right no-caret ref="oneDropdownDefaultUser">
                        <template #button-content>
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle" :src="$page.props.auth.user.avatar_url" alt="Header Avatar" style="width: 30px;">
                                <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                            </div>
                        </template>
                        <div class="p-3 text-center bg-primary rounded-top">
                            <img class="img-avatar img-avatar48 img-avatar-thumb" :src="$page.props.auth.user.avatar_url" alt="">
                            <p class="mt-2 mb-0 text-white font-w500">{{ $page.props.auth.user.name }}</p>
                        </div>
                        <div class="p-2">
                            <a class="dropdown-item" :href="route('user.dashboard')">
                                <i class="fi fi-rs-home mr-5"></i> 
                                <span class="font-w600">Dashboard</span>
                            </a>
                            <a class="dropdown-item" :href="route('user.order.index')">
                                <i class="fi fi-rs-notebook"></i>
                                <span class="font-w600">Pesanan Saya</span>
                            </a>
                            <a class="dropdown-item" :href="route('user.wishlist.index')">
                                <i class="fi fi-rs-heart"></i>
                                <span class="font-w600">Wishlist</span>
                            </a>
                            <a class="dropdown-item" :href="route('user.address.index')">
                                <i class="si si-map"></i>
                                <span class="font-w600">Buku Alamat</span>
                            </a>
                            <a class="dropdown-item" :href="route('user.settings.index')">
                                <i class="fi fi-rs-settings"></i>
                                <span class="font-w600">Pengaturan</span>
                            </a>
                            <div role="separator" class="dropdown-divider"></div>
                            <Link :href="route('logout')" method="post" as="button" class="dropdown-item">
                                <i class="fi fi-rs-sign-out"></i>
                                <span class="font-w600">Keluar</span>
                            </Link>
                        </div>
                    </b-dropdown>
                </template>
                <template v-else>
                    <div class="float-right my-auto d-none d-lg-block">
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
<style>
.bm-header .content-header .search-header{
    width: 56%;
}

.bm-header .title-header {
    align-self: center;
    margin-left: 0px;
    margin-right: 0px;
}

.bm-header .title-header h4{
    display: block;
    position: relative;
    font-weight: 800;
    font-size: 16px;
    line-height: 22px;
    color: rgba(49, 53, 59, 0.96);
    letter-spacing: -0.1px;
    text-decoration: initial;
    margin: 0px;
}
@media only screen and (max-width: 768px) {
    .bm-header .content-header{
        display: flex;
        z-index: 999;
        height: 56px;
        padding: 16px 16px 16px 0 !important;
        position: sticky;
        align-items: center;
        justify-content: space-between;
        top: 0;
        width: 100%;
        box-shadow: 0 0 16px -4px rgb(0 0 0 / 12%);
    }

    .bm-header .content-header .search-header{
        display: flex;
        flex: 2;
    }
}

</style>
<script>
import { Link } from '@inertiajs/inertia-vue';
import CartHeader from '@/components/Cart/cartHeader.vue';
import SearchBox from './SearchBox.vue';
export default {
    name: 'BaseHeader',
    components: {
        Link,
        CartHeader,
        SearchBox
    },
    props: {
        user: Object,
        back : String,
        title : String,
    },
    data () {
        return {
            baseSearchTerm: '',
        }
    },
    mounted(){
        // console.log(document.referrer.indexOf(window.location.host));
    },
    computed: {
        showSearch(){
            if(this.$root.window.mobile){
                const showIn = [
                    'home',
                    'product.index',
                    'product.search',
                    'product.category',
                    'product.show',
                ]
                return showIn.includes(this.route().current());
            }
            return true;
        },
    },
    methods :{
        routeBack(){
            if(window.history.length > 1 && document.referrer.indexOf(window.location.host) !== -1) {
                return window.history.back();
            }else {
                if(this.back){
                    return window.location.href = this.route(this.back);
                }

                return window.location.href = this.route('home');
            }
        }
    }
}

</script>
