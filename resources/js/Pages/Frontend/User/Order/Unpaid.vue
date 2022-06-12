<template>
    <UserLayout title="Menunggu Pembayaran">
        <div>
            <div class="content-heading pt-3 mb-3">
                <i class="fi-rs-home-location mr-1"></i> Menunggu Pembayaran
            </div>
            
            <template v-if="dataList.length">
                <div class="block block-rounded block-shadow block-bordered" v-for="d in dataList" :key="d.id">
                    <div class="block-content border-3x border-bottom p-10">
                        <div class="row">
                            <div class="col-6">
                                {{ format_date(d.date) }} WIB
                            </div>
                            <div class="col-6 text-right">
                                Bayar Sebelum <i class="si si-clock mr-1"></i><span class="font-w700">{{ format_date(d.payment_due) }} WIB</span>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="row">
                            <div class="col-lg-6 payment_method">
                                <img src="https://ecs7.tokopedia.net/img/icon-bca.png" alt="gateway">
                                <div class="payment_method_info">
                                    <div class="font-w700">Metode Pembayaran</div>
                                    <div class="font-w600" data-testid="pms-payment-method">Transfer Manual</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="">
                                    <div class="css-2qg9nh">Total Pembayaran</div>
                                    <div class="css-gcdhmf">{{ currency(d.grand_total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm text-right">
                        <a :href="route('user.order.payment.show', { order : d.id})" class="btn btn-sm btn-outline-primary">
                            Detail Pembayaran
                        </a>
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
