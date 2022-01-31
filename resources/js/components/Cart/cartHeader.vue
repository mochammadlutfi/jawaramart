<template>
    <div @mouseover="onOver" @mouseleave="onLeave" class="mr-20">
        <b-dropdown size="sm" variant="dual" ref="cartHead" class="cart-head d-inline-block" menu-class="cart-dropdown p-0 border-0 font-size-sm" right no-caret>
            <template #button-content>
                <i class="fi-rs-shopping-cart"></i>
                <span class="cart-count">{{ $page.props.cart.length }}</span>
            </template>
            <li>
                <template  v-if="$page.props.cart.length">
                    <div class="py-2 rounded-top d-flex justifycontent-center">
                        <h5 class="dropdown-header text-uppercase">Keranjang ({{ $page.props.cart.length }})</h5>
                    </div>
                    <div class="cart-list">
                        <div class="cart-item" v-for="(item, index) in $page.props.cart" :key="`cart-${index}`">
                            <div class="cart-image">
                                <img :src="item.product.main_image" :alt="item.title"/>
                            </div>
                            <div class="cart-info">
                                <div class="pdp-cart-name">{{ item.product.name }}</div>
                                <div class="pdp-cart-qty">
                                    {{ item.qty }} Barang x {{ currency(item.unit_price) }}
                                </div>
                            </div>
                            <div class="cart-subtotal">
                                {{ currency(item.unit_price * item.qty) }}
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="py-20 px-10 text-center">
                        <img :src="asset('media/placeholder/cart_is_empty.png')" class="w-50"/>
                        <div>
                            <div class="font-size-h5 font-w700 mt-20">Yah keranjang belanjaanmu kosong!</div>
                        </div>
                    </div>
                </template>
                <div class="p-5" v-if="$page.props.cart.length">
                    <a :href="route('cart.index')" class="btn btn-primary btn-block btn-noborder">Lihat Semua</a>
                </div>
            </li>
        </b-dropdown>
    </div>
</template>

<style>

</style>

<script>
import axios from 'axios';
export default {
    name: 'cartHeader',
    data() {
        return {
            baseSearchTerm: '',
        }
    },
    methods: {
        onOver() {
            this.$refs.cartHead.visible = true;
        },
        onLeave() {
            this.$refs.cartHead.visible = false;
        },
        async getCart() {
                const self = this;
                await axios.get(this.route('cart.data'))
                .then(function (response) {
                    // self.cart = response.data;
                    // 
                    response.data.forEach((value, index) => {
                        // this.form.images.push({
                        //     'id' : id,
                        //     'image' : value.path,
                        // });
                        self.cart.push({
                            name : value.product.name,
                            image : value.product.main_image,
                            qty : value.qty,
                            price : value.unit_price,
                        });
                    });
                });
        },
    }
}
</script>