<template>
    <user-layout>
        <div class="content-heading pt-0 mb-3">
            Detail Transaksi
        </div>
        <div class="block block-rounded block-bordered block-shadow">
            <div class="block-header border-bottom border-2x py-3">
                <div class="block-title d-flex">
                    <div class="font-w600">{{ data.ref }}</div>
                    <div class="border-2x border-left font-w600 ml-2 pl-2">{{ data.date }}</div>
                </div>
                <div class="block-options">
                    <span class="badge badge-warning p-2" v-if="data.status == 'pending'">Menunggu Konfirmasi</span>
                    <span class="badge badge-info p-2" v-else-if="data.status == 'shipping'">Dikirim</span>
                    <span class="badge badge-success p-2" v-else-if="data.status == 'done'">Selesai</span>
                    <span class="badge badge-danger p-2" v-else>Batal</span>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                        <h5 class="font-weight-bold">Info Pengiriman</h5>
                        <div class="font-w700">{{ data.delivery.reciever }}</div>
                        <div class="">{{ data.delivery.phone }}</div>
                        <address>{{ data.delivery.address }}<br>
                            {{ data.delivery.area_text }}, {{ data.delivery.postal_code }}
                        </address>
                    </div>
                </div>
            </div>
            <div class="block-content p-0">
                <div class="order-content" v-for="(d, i) in data.line" :key="i">
                    <div class="order-items">
                        <div class="product-info">
                            <div class="product-img">
                                <img :src="d.product.main_image" class="img-fluid">
                            </div>
                            <div class="product-detail">
                                <div class="product_name">
                                    {{ d.product.name }}
                                </div>
                                <div class="product_price">
                                    {{ d.qty }} x {{ currency(d.unit_price) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-sum">
                        <p class="mb-0 font-w700">{{ currency(d.sub_total) }}</p>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="order-row">
                        <div class="col-title">
                            <span>Subtotal Produk</span>
                        </div>
                        <div class="col-val">
                            <div>{{ currency(data.total) }}</div>
                        </div>
                    </div>
                    <div class="order-row">
                        <div class="col-title">
                            <span>Ongkos Kirim</span>
                        </div>
                        <div class="col-val">
                            <div>{{ currency(data.shipping_cost) }}</div>
                        </div>
                    </div>
                    <div class="order-row">
                        <div class="col-title">
                            <span>Biaya Layanan</span>
                        </div>
                        <div class="col-val">
                            <div>{{ currency(data.payment[0].code) }}</div>
                        </div>
                    </div>
                    <div class="order-row">
                        <div class="col-title">
                            <span>Total Belanja</span>
                        </div>
                        <div class="col-val">
                            <div>{{ currency(data.grand_total) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-layout>
</template>

<style lang="scss">

.order-content {
    display: flex;
    border-top: 1px solid #E5E7E9;

    .order-items  {
        -webkit-box-flex: 1;
        flex-grow: 1;
        width: calc(100% - 180px);

        .product-info {
            display: flex;
            width: 100%;
            padding: 10px;

            .product-img {
                flex-shrink: 0;
                margin-right: 16px;
                width: 50px;
                height: 50px;
                border-radius: 4px;
            }

            .product-detail{
                -webkit-box-flex: 1;
                flex-grow: 1;
            }
        }
    }

    .order-sum {
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
        width: 180px;
        -webkit-box-pack: start;
        justify-content: flex-start;
        padding-left: 24px;
        border-left: 1px solid #E5E7E9;
    }
}

.order-footer{
    border-top: 1px solid rgba(0,0,0,.09);

    .order-row {
        padding: 0 24px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        border-bottom: 1px dotted rgba(0,0,0,.09);

        .col-title  {
            padding: 13px 10px;
            font-size: 1rem;
        }

        .col-val  {    
            padding: 13px 0 13px 10px;
            width: 156px;
            border-left: 1px solid #E5E7E9;
            justify-content: flex-end;
            word-wrap: break-word;
        }
    }
}
</style>

<script>

import UserLayout from "@/Layouts/Frontend/UserLayout";
export default {
    components :{
        UserLayout,
    },
    props :{
        data : Object
    }
}
</script>