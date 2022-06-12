<template>
    <UserLayout title="Daftar Transaksi">
        <div>
            <template v-if="!$root.window.mobile">
                <div class="content-heading pt-3 mb-3">
                    <i class="fi-rs-home-location mr-1"></i> Daftar Pesanan
                </div>
                <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                    <ul class="nav nav-tabs nav-tabs-alt nav-fill">
                        <li class="nav-item">
                            <a class="nav-link" v-bind:class="{ 'active' : (status == 'berlangsung') ? true : false }" :href="route('user.order.index', { status : 'berlangsung' })">Berlangsung
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" v-bind:class="{ 'active' :  (status == 'selesai') ? true : false }" :href="route('user.order.index', { status : 'selesai' })">Selesai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" v-bind:class="{ 'active' :  (status == 'cancel') ? true : false }" :href="route('user.order.index', { status : 'cancel' })">Dibatalkan
                            </a>
                        </li>
                    </ul>
                    <div class="block-content p-2">
                        <div class="row justify-content-between">
                            <div class="col-4">
                                <div class="has-search">
                                    <i class="fa fa-search"></i>
                                    <input type="search" class="form-control" id="search-data-list" name="keyword">
                                </div>
                            </div>
                            <div class="col-4">
                            </div>
                            <div class="col-4">
                                <div class="d-flex float-right">
                                    <div class="my-auto px-3">
                                        <span>{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                                    </div>
                                    <div class="pt-25 pl-0">
                                        <button @click="prevPage" class="btn btn-alt-secondary mx-1" type="button"
                                        :disabled="checkPaginate('prev')">
                                            <i class="fa fa-chevron-left fa-fw"></i>
                                        </button>
                                        <button @click="nextPage" class="btn btn-alt-secondary mx-1" type="button"
                                        :disabled="checkPaginate('next')">
                                            <i class="fa fa-chevron-right fa-fw"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-if="dataList.data.length">
                <div class="block block-rounded block-shadow block-bordered mb-5" v-for="d in dataList.data" :key="d.id">
                    <div class="block-header border-3x border-bottom p-10">
                        <h3 class="block-title">{{ format_date(d.created_at) }}</h3>
                        <div class="block-options">
                            <div class="block-options-item">
                                <span class="badge badge-warning" v-if="d.status == 'pending' && d.payment_status == 'pending'">Menunggu Konfirmasi</span>
                                <span class="badge badge-warning" v-else-if="d.status == 'pending' && d.payment_status == 'paid'">Diproses</span>
                                <span class="badge badge-info" v-else-if="d.status == 'delivery' && d.payment_status == 'paid'">Dikirim</span>
                                <span class="badge badge-primary" v-else-if="d.status == 'done' && d.payment_status == 'paid'">Selesai</span>
                                <span class="badge badge-primary" v-else>Batal</span>
                            </div>
                        </div>
                    </div>
                    <div class="block-content py-10">
                        <div class="order-content">
                            <div class="order-items">
                                <div class="product-info">
                                    <div class="product-img">
                                        <img :src="d.line[0].product.main_image" class="img-fluid">
                                    </div>
                                    <div class="product-detail">
                                        <div class="product_name">
                                            {{ d.line[0].product.name }}
                                        </div>
                                        <div class="product_price">
                                            {{ d.line[0].qty }} x {{ currency(d.line[0].unit_price) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-sum">
                                <div>
                                    <p class="mb-0" style="font-size: 0.857143rem;">Total Belanja</p>
                                    <p class="mb-0 font-w700" style="font-size: 1rem;">{{ currency(d.grand_total) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-right border-top">
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="recieve(d.id)" v-if="d.status == 'delivery'">
                            Terima Pesanan
                        </button>
                        <a :href="route('user.order.show', { order : d.id} )" class="btn btn-sm btn-outline-primary">
                            Detail Pesanan
                        </a>
                    </div>
                </div>
            </template>
            <template v-else>
                Tidak ada Data
            </template>
        </div>
    </UserLayout>
</template>
<style lang="scss">
.vs__selected .selected {
    max-width: 420px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.order-content {
    display: flex;

    .order-items  {
        -webkit-box-flex: 1;
        flex-grow: 1;
        width: calc(100% - 180px);

        .product-info {
            display: flex;
            width: 100%;

            .product-img {
                flex-shrink: 0;
                margin-right: 16px;
                width: 60px;
                height: 60px;
                background: var(--N50,#F3F4F5);
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
        border-left: 1px solid var(--N75,#E5E7E9);
    }
}
</style>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import vSelect from 'vue-select';
import _ from 'lodash';
import axios from 'axios';
import moment from 'moment';
export default {
    components: {
        UserLayout,
        vSelect
    },
    props : ['dataList', 'errors'],
    data(){
        return {
            status : this.route().params.status == undefined ? 'berlangsung' : this.route().params.status,
            loading: false,
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
        }
    },
    watch :{
    },
    methods :{
        checkPaginate(type){
            const vm = this;
            if(vm.dataList){
                if(type == 'next'){
                    return (vm.dataList.next_page_url) ? false : true;
                }else{
                    return (vm.dataList.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
        },
        nextPage: function() {
            if(this.currentPage < this.dataList.total){
                this.currentPage++;

                let params = {
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('user.order.index', params), {
                    preserveScroll : true,
                });
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.currentPage--;

                let params = {
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('user.order.index', params), {
                    preserveScroll : true,
                });
            }
        },
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        detail(id){
            if(this.$root.window.mobile){
                alert('detail')
            }else{
                alert('detail')
            }
        },
        recieve(value){
            this.$swal.fire({
                icon: 'warning',
                title: 'Pastikan Produk Sudah Sesuai',
                text: `Dengan Klik "Selesai Pesanan" maka pesanan sudah selesai`,
                showCancelButton: true,
                cancelButtonText : 'Kembali',
                confirmButtonText: "Selesai",
                confirmButtonColor: '#3f9ce8',
                cancelButtonColor: '#af1310',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.post(this.route('user.order.confirm'), {
                            order_id : value,
                        }, {
                        onProgress: ()=> {
                            this.$swal.fire({
                                title: 'Tunggu Sebentar...',
                                text: '',
                                imageUrl: window._asset + 'media/loading.gif',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                            });
                        },
                        onSuccess: () => {
                            this.$swal.close();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Pesanan Selesai',
                                text: "Terimakasih Sudah Berbelanja",
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer : 3000,
                            });
                        },
                        onError:() => {
                            this.$swal.Close();
                        }
                    })
                }
            })
        }
    }
 }

</script>
