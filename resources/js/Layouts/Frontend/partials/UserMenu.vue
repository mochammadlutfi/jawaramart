<template>
    <ul class="nav-main">
        <li>
            <a :class="(route().current('user.dashboard')) ? 'active' : ''" :href="route('user.dashboard')">
                <i class="fi fi-rs-home"></i><span class="sidebar-mini-hide">Dashboard</span>
            </a>
        </li>
        <li :class="subIsActive('user.order.*') ? 'open' : false">
            <a class="nav-submenu" href="#" @click="linkClicked">
                <i class="fi fi-rs-notebook"></i>
                <span class="sidebar-mini-hide">Pesanan Saya</span>
            </a>
            <ul>
                <li>
                    <a :class="(route().current('user.order.payment')) ? 'active' : ''" :href="route('user.order.payment')">Menunggu Pembayaran</a>
                </li>
                <li>
                    <a :class="(route().current('user.order.index')) ? 'active' : ''" :href="route('user.order.index')">Daftar Pesanan</a>
                </li>
            </ul>
        </li>
        <li>
            <a :class="(route().current('user.address.*')) ? 'active' : ''" :href="route('user.address.index')">
                <i class="si si-map"></i>Buku Alamat
            </a>
        </li>
        <li>
            <a :class="(route().current('user.wishlist.*')) ? 'active' : ''" :href="route('user.wishlist.index')">
                <i class="fi fi-rs-heart"></i>Wishlist
            </a>
        </li>
        <li>
            <a :class="(route().current('user.settings.*')) ? 'active' : ''" :href="route('user.settings.index')">
                <i class="fi fi-rs-settings"></i>Pengaturan
            </a>
        </li>
    </ul>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue';
    export default {
        name: 'UserMenu',
        components: {
            Link
        },
        props: {
            nodes: {
                type: Array,
                description: 'The nodes of the navigation'
            },
            subMenu: {
                type: Boolean,
                default: false,
                description: 'If true, a submenu will be rendered'
            },
            dark: {
                type: Boolean,
                default: false,
                description: 'Dark mode for menu'
            },
            horizontal: {
                type: Boolean,
                default: false,
                description: 'Horizontal menu in large screen width'
            },
            horizontalHover: {
                type: Boolean,
                default: false,
                description: 'Hover mode for horizontal menu'
            },
            horizontalCenter: {
                type: Boolean,
                default: false,
                description: 'Center mode for horizontal menu'
            },
            horizontalJustify: {
                type: Boolean,
                default: false,
                description: 'Justify mode for horizontal menu'
            }
        },
        computed: {
            classContainer() {
                return {
                    'nav-main': !this.subMenu,
                    'nav-main-submenu': this.subMenu,
                    'nav-main-dark': this.dark,
                    'nav-main-horizontal': this.horizontal,
                    'nav-main-hover': this.horizontalHover,
                    'nav-main-horizontal-center': this.horizontalCenter,
                    'nav-main-horizontal-justify': this.horizontalJustify
                }
            }
        },
        methods: {
            subIsActive(paths) {
                const activePaths = Array.isArray(paths) ? paths : [paths]
                return this.route().current(paths);
            },
            linkClicked(e) {
                // Get window width
                let windowW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
                let el = e.target.closest('li')
                if (!(windowW > 991 && this.horizontal && this.horizontalHover)) {
                    if (el.classList.contains('open')) {
                        el.classList.remove('open')
                    } else {
                        var children = Array.prototype.slice.call(el.closest('ul').children);
                        children.forEach(element => {
                            element.classList.remove('open')
                        })
                        el.classList.add('open')
                    }
                }
            }
        }
    }

</script>
