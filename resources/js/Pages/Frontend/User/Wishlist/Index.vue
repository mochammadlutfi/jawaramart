<template>
    <UserLayout title="Menunggu Pembayaran">
        <div>
            <div class="content-heading pt-3 mb-3">
                <i class="fi-rs-home-location mr-1"></i> Wishlist
            </div>
            
            <template v-if="dataList.data.length">
                <div class="row mx-lg-0">
                    <div class="col-6 col-lg-3 col-md-2 mb-15 px-5" v-for="data in dataList.data" :key="data.id">
                        <ProductItem :product="data.product" :id="data.id"/>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="text-center">
                    <img class="img-fluid" :src="asset('images/not_found.png')">
                    <h3 class="h4 my-10">Data Tidak Ditemukan</h3>
                </div>
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

.payment_method {
    display: flex;

    img {
        width: 80px;
    }

    .payment_method_info {
        padding-left: 15px;
    }
}
</style>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import vSelect from 'vue-select';
import _ from 'lodash';
import moment from 'moment';
import ProductItem from './ProductItem.vue';
export default {
    components: {
        UserLayout,
        vSelect,
        ProductItem,
    },
    props : ['dataList', 'errors'],
    data(){
        return {
            title : 'Tambah Alamat Baru',
        }
    },
    watch :{
    },
    methods :{
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        format_date(value){
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD MMMM YYYY h:mm')
            }
        },
        timeout(value){
            moment.locale('id');
            var created  = moment(String(value));
            return moment(created).format('DD MMM YYYY h:mm');
        },
    }
 }

</script>
