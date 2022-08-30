<template>
    <BaseLayout title="Detail Sale">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Transaction Detail <small>{{ data.name }}</small>

                <div class="float-right">
                </div>
            </div>
            <div class="block block-rounded block-shadow mb-10">
                <div class="block-content">
                    <div class="row">
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Transaction No</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ data.name }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Transaction Date</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ format_date(data.date) }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Transaction Type</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ data.service }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Payment Status</div>
                                <div class="col-lg-9 value">
                                    <span class="badge badge-primary" v-if="data.payment_status == 'paid'">Paid</span>
                                    <span class="badge badge-info" v-else-if="data.payment_status == 'partial'">Partial</span>
                                    <span class="badge badge-warning" v-else-if="data.payment_status == 'pending'">Pending</span>
                                    <span class="badge badge-danger" v-else>Unpaid</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 col-sm-12 col-md-6 col-lg-6">
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Transaction Period</div>
                                <div class="col-lg-9 value">
                                    <span class="font-w700">{{ format_date(data.date) }}</span>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">ID Anggota</div>
                                <div class="col-lg-9 value">
                                    <a href="#">{{ data.anggota_id }}</a>
                                </div>
                            </div>
                            <div class="gutters-tiny row">
                                <div class="col-lg-3">Nama Anggota</div>
                                <div class="col-lg-9 value">
                                    <a href="#">{{ data.anggota_name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Line -->
        </div>

    </BaseLayout>
</template>
<style>
.value::before{
    content: ': ';
    font-weight: normal;
}
</style>
<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
export default {
    components: {
        BaseLayout,
    },
    props: {
        data : Object,
        payment : Array,
        errors : Object,
    },
    data(){
        return {
            duePayment : 0,
        }
    },
    methods :{
        format_date(value){
            if (value) {

                return moment(String(value)).format('DD MMMM YYYY hh:mm')
            }
        },
        updateStatus(value){
            let form = this.$inertia.form({
                id : this.data.id,
                status : value,
            });
            let url = this.route("admin.sale.order.update_status");
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
                        text: `Status Sale Order Berhasil Di Perbaharui!`,
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
        destroy: function (id) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.sale.order.delete', id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.reset();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Sale order deleted!`,
                                showConfirmButton : false,
                                showCancelButton: false,
                                timer : 1500,
                            });
                        },
                    })
                }
            })
        },
    }
}
</script>
