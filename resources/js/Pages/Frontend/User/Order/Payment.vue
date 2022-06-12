<template>
    <UserLayout title="Detail Pembayaran">
        <div class="row justify-content-center" v-if="order.payment_status == 'unpaid'">
            <div class="col-lg-8">
                <h2 class="h3 text-center">Menunggu Pembayaran</h2>
                <p class="font-size-md text-center">Segera Selesaikan Pembayaran Sebelum Stok Habis</p>
                <div class="block block-rounded block-shadow block-bordered">
                    <div class="block-content block-content-full">
                        <p class="font-size-md text-center">Segera Lakukan Pembayaran Sebelum</p>
                        <div class="text-center font-size-lg">
                        <vue-countdown :time="paymentTimeout" v-slot="{ hours, minutes, seconds }">
                            <b>{{ hours }}</b> Jam : <b>{{ minutes }}</b> Menit : <b>{{ seconds }}</b> seconds.
                        </vue-countdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-heading pt-0 mb-0 border-0 font-size-md">
            Cara Pembayaran
        </div>
        <div class="block block-bordered block-shadow block-rounded">
            <div class="block-content block-content-full">
                <div class="row mb-15">
                    <div class="col-lg-6">
                        <div class="font-size-sm">
                            Nomor Rekening
                        </div>
                        <div class="font-w700">{{ order.payment_method.account_no }}</div>
                        <div class="font-size-sm">{{ order.payment_method.account_name }}</div>
                    </div>
                    <div class="col-lg-6">
                        <div class="font-size-sm">Total Pembayaran</div>
                        <div class="font-w700">{{ currency(order.grand_total) }}</div>
                    </div>
                </div>
                <div class="alert alert-primary mb-0" role="alert">
                    <p class="mb-0 text-center">
                      Penting! Transfer dengan jumlah yang tepat hingga 3 digit terakhir.
                    </p>
                  </div>
            </div>
            <div class="block-content block-content-full border-top border-2x" v-if="order.payment_status == 'unpaid'">
                <button type="button" class="btn btn-lg btn-primary btn-noborder btn-block" @click="updatePayment">
                    Saya Sudah Transfer
                </button>
                <p class="my-3 nice-copy text-center">
                    Upload Bukti Pembayaran Agar Proses Verifikasi Pembayaran Lebih Cepat<br/>Ukuran File Maksimal 2MB
                </p>
                <div class="upload-btn-wrapper w-100">
                    <label class="btn btn-lg btn-outline-primary btn-block" for="file-upload">
                        <i class="fa fa-camera mr-1"></i>
                        Upload Bukti Pembayaran
                    </label>
                    <input type="file" id="file-upload" accept="image/*" @change="uploadBukti">
                </div>
            </div>
        </div>
        <div class="content-heading pt-0 mb-0 border-0 font-size-md">
            Detail Pembayaran
        </div>
        <div class="block block-bordered block-shadow block-rounded">
            <div class="block-content block-content-full">
                <div class="d-flex justify-content-between mb-5">
                    <div class="font-size-sm">Total Harga</div>
                    <div class="font-size-sm  font-w600">{{ currency(order.total) }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="font-size-sm">Ongkos Kirim</div>
                    <div class="font-size-sm  font-w600">{{ currency(order.shipping_cost) }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="font-size-sm">Biaya Layanan</div>
                    <div class="font-size-sm  font-w600">{{ currency(order.code) }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="font-size-lg font-w700">Total Bayar</div>
                    <div class="font-size-lg font-w700">{{ currency(order.grand_total) }}</div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
<style lang="scss">
.upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}
.upload-btn-wrapper input[type=file] {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}
</style>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import PasswordInput from '@/Components/Form/PasswordInput';
import axios from 'axios';
import moment from 'moment';
import VueCountdown from '@chenfengyuan/vue-countdown';

export default {
    components: {
        UserLayout,
        PasswordInput,
        VueCountdown
    },
    props : {
        order : Object,
        errors : Object,
    },
    data(){
        return {
            file : null,
        }
    },
    computed : {
        paymentTimeout(){
            moment.locale('id');
            var created  = moment(String(this.order.created_at));
            var now  = moment(String(this.order.payment_due));
            var duration = moment.duration(now.diff(created));
            // console.log(duration);
            return duration.asMilliseconds();
        },
    },
    mounted(){
    },
    methods : {
        uploadBukti(e){
            const file = e.target.files[0];
            if (file.size > 2000000) {
                e.preventDefault();
                this.$swal.fire({
                    icon: 'error',
                    // title: 'Berhasil',
                    text: `Ukuran File Maksimal 2MB!`,
                    showConfirmButton: false,
                    timer : 1500,
                });
                return;
            }
            this.file = file;
            this.updatePayment();
        },
        updatePayment: function () {
            let form = this.$inertia.form({
                payment_id : this.order.payment_id,
                order_id : this.order.sale_id,
                file : this.file,
            });
            let url = this.route("user.order.payment.update");
            form.post(url, {
                preserveScroll: true,
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
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Konfirmasi Pembayaran Berhasil!`,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer : 1500,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
    }
 }

</script>
