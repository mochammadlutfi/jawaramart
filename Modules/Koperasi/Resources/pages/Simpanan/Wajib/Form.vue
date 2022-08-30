<template>
    <BaseLayout title="Detail Sale">
        <div class="content">
            <div class="content-heading pt-0 mb-3">

                <div class="float-right">
                </div>
            </div>
            <div class="block block-rounded block-shadow mb-10">
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Anggota</label>
                                <anggota-select @done="(value) => anggota = value" :errors="errors.anggota_id"/>
                            </div>
                            <div class="form-group">
                                <label>Periode Bulan</label>
                                <div class="has-search">
                                    <i class="si si-calendar"></i>
                                    <flat-pickr ref="periode" :config="periodeConfig" class="form-control bg-white" :class="{'is-invalid' : errors.periode}" v-model="form.periode" :disabled="!form.anggota_id"></flat-pickr>
                                </div>
                                <div class="invalid-feedback" v-if="errors.periode">{{ errors.periode[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                               <CurrencyInput :modelValue="form.amount" class="form-control"/>
                                <div class="invalid-feedback" v-if="errors.wajib">{{ errors.wajib[0] }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Transaction No</label>
                                <input type="text" class="form-control" v-model="form.kd_transaksi"/>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <flat-pickr class="form-control bg-white" :config="transactionDateConfig" v-model="form.tgl"></flat-pickr>
                            </div>
                            <div class="form-group">
                                <label>Payment Method</label>
                                <PaymentMethodSelect />
                                <div class="invalid-feedback" v-if="errors.wajib">{{ errors.wajib[0] }}</div>
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
import flatPickr from 'vue-flatpickr-component';
import moment from 'moment';
import AnggotaSelect from '../../Components/AnggotaSelect.vue';
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import PaymentMethodSelect  from '@/components/Form/PaymentMethodSelect.vue';
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect/index.js'
import 'flatpickr/dist/plugins/monthSelect/style.css';
import axios from 'axios';
export default {
    components: {
        BaseLayout,
        AnggotaSelect,
        CurrencyInput,
        flatPickr,
        monthSelectPlugin,
        PaymentMethodSelect
    },
    props: {
        data : Object,
        payment : Array,
        errors : Object,
    },
    data(){
        return {
            duePayment : 0,
            anggota : null,
            amount : null,
            form : {
                id : null,
                anggota_id : null,
                kd_transaksi : null,
                amount : 0,
                tgl : new Date(),
                periode : null,
                payment_method_id : null,
                note : null,
            },
            transactionDateConfig: {
                altFormat: 'j F Y',
                altInput: true,
                dateFormat: 'd-m-Y',  
            },
            periodeConfig: {
                minDate: new moment().startOf('year').format('Y-M'),
                maxDate: new moment().endOf('year').format('Y-M'),
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "F Y",
                    })
                ],
            },
        }
    },
    watch : {
        anggota(val){
            if(val){
                this.form.anggota_id = val.anggota_id;
                this.getPeriode(val.anggota_id);
                this.setAmount(val.golongan);
            }
        },
    },
    methods :{
        submit: function(){
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            if(this.editMode){
                var url = this.route('simpanan.wajib.update');
            }else{
                var url = this.route('simpanan.wajib.store');
            }
            axios.post(url, this.form)
            .then((res) => {
                if(res.data.fail){
                    this.errors = res.data.errors;
                    this.$swal.close();
                }else{
                    this.$swal.close();
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        html: `Setoran Simpanan Wajib Berhasil!
                        <br><br>
                            <a href="${ this.route('simpanan.wajib.create') }" class="btn btn-outline-primary">
                                <i class="si si-plus mr-1"></i>Tambah Lainnya
                            </a> 
                            <a href="${ this.route('simpanan.wajib.index') }" class="btn btn-primary">
                                <i class="si si-action-undo mr-1"></i>Kembali
                            </a>`,
                    })
                }
            })
            .catch((error) => {
                // console.log(error);
            });
        },

        getPeriode(value){
            axios.get(this.route('admin.kop.simpanan.wajib.paid', value))
            .then((res) => {
                var disabled = res.data.date;
                
                var fp = this.$refs.periode.fp;
                var rContainer = fp.rContainer;
                var fpHead = fp.monthNav.children;
                fpHead[0].classList.add('flatpickr-disabled');
                fpHead[2].classList.add('flatpickr-disabled');
                var monthsContainer = rContainer.children[0];
                var monthsElements = monthsContainer.children;

                if (disabled) {
                    disabled.forEach((monthIndex) => {
                        var month = moment(String(monthIndex)).format('M');
                        setTimeout(() => {
                            var monthEl = monthsElements[parseInt(month) - 1];
                            monthEl.classList.add('disabled');
                        }, 100);
                    });
                    }
            })
        },
        editModeActive(){
            this.title = 'Ubah Setoran Simpanan Wajib';
            this.anggotaSelected = this.data.anggota;
            this.form.id = this.data.id;
            this.form.anggota_id = this.data.anggota_id;
            this.form.kd_transaksi = this.data.nomor;
            this.form.wajib = this.data.total;
            this.form.tgl = moment(String(this.data.tgl)).format('D MMMM YYYY');
            this.form.periode = moment(String(this.data.simkop[0].periode)).format('MMMM YYYY');
            this.form.note = this.data.note;
        },
        setAmount(gol){
            var amount = 0;
            if(gol == 1){
                amount = 20000;
            }else if(gol == 2){
                amount = 25000;
            }else if(gol == 3){
                amount = 50000;
            }else if(gol == 4){
                amount = 100000;
            }else if(gol == 5){
                amount = 150000;
            }else if(gol == 6){
                amount = 200000;
            }else if(gol == 7){
                amount = 250000;
            }else if(gol == 8){
                amount = 300000;
            }else{
                amount = 20000;
            }
            this.form.amount = amount;
            this.amount = amount;
        }
    }
}
</script>
