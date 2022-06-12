<template>
    <BaseLayout title="Metode Pembayaran">
        <div class="content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="content-heading pt-0 mb-3font-size-md" v-if="!$root.window.mobile">
                        Metode Pembayaran
                    </div>
                    <div class="block block-bordered block-rounded block-shadow">
                        <div class="block-content p-0">
                            <ul class="payment-method-list">
                                <li class="list_item">
                                    <label class="css-control css-control-primary css-radio payment-wrap">
                                        <div class="payment__logo">
                                            <img src="https://ecs7.tokopedia.net/img/toppay/sprites/bca.png" class="img-fluid" alt="gateway">
                                        </div>
                                        <div class="payment__content">
                                            <div class="payment__text">
                                                <div class="mr-4 font-weight-bold">Transfer Bank BCA</div>
                                            </div>
                                        </div>
                                        <div class="payment__icon">
                                            <input type="radio" class="css-control-input" name="payment_method" value="3" v-model="paymentMethod">
                                            <span class="css-control-indicator"></span>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="block block-bordered block-shadow block-rounded">
                        <div class="block-content block-content-full">
                            <h6 class="font-size-h5">Ringkasan belanja</h6>
                            <div class="d-flex justify-content-between mb-5">
                                <div class="font-size-sm">Total Harga ( barang)</div>
                                <div class="font-size-sm  font-w600">{{ currency(data.sub_total) }}</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="font-size-sm">Biaya Kirim</div>
                                <div class="font-size-sm  font-w600">{{ currency(data.shipping.price) }}</div>
                            </div>
                            <hr/>
                            <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" @click="submit">
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>
<style>
.section-label {
    font-size: 18px;
    margin-bottom: 20px;
    line-height: 20px;
}

ul.payment-method-list {
    margin: 0px;
    padding: 0px;
    list-style: none;
}

ul.payment-method-list li.list_item {
    display: flex;
    position: relative;
    padding: 16px;
    margin-left: 0;
    border-bottom: 0.5px solid rgb(224, 224, 224);
    -webkit-box-align: center;
    align-items: center;
}

ul.payment-method-list li.list_item .payment-wrap{
    display: flex;
    -webkit-box-pack: start;
    justify-content: flex-start;
    -webkit-box-align: center;
    align-items: center;
    flex: 1 1 0%;
    min-height: 38px;
    max-width: 100%;
    position: relative;
    cursor: pointer;
}

.payment-wrap .payment__logo {
    width: 52px;
    padding-right: 12px;
    text-align: center;
    line-height: 0;
}

.payment-wrap .payment__logo img {
    width: auto;
    height: auto;
    min-width: 32px;
    max-width: 100%;
}

.payment-wrap .payment__content {
    display: flex;
    flex: 1 1 0%;
    flex-direction: column;
    padding-right: 8px;
    text-overflow: ellipsis;
    overflow: hidden;
}

.payment-wrap .payment__content .payment__text{
    display: flex;
    -webkit-box-pack: start;
    justify-content: flex-start;
    -webkit-box-align: center;
    align-items: center;
}


.payment-wrap .payment__select {
    max-width: 50px;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
}

.payment-wrap .payment__icon {
    padding: 0px;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
    min-width: 8px;
    min-height: 13px;
    color: rgb(66, 181, 73);
    font-size: 12px;
    line-height: 16px;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 168px;
    opacity: 1;
}

</style>

<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import ProductQty from "@/components/Product/ProductQty.vue";
import vSelect from 'vue-select';
import moment from 'moment';
export default {
    components: {
        BaseLayout,
        ProductQty,
        vSelect
    },
    props : ['data','errors'],
    data() {
        return {
            paymentMethod : 3,
            status : false,
        }
    },
    computed :{
    },
    watch : {
    },
    methods :{
        submit(){
            const form = this.$inertia.form({
                payment_method : this.paymentMethod
            });

            form.post(this.route('checkout.store'), {
                preserveScroll: true,
            });
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
    }

 }

</script>
