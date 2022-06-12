<template>
    <b-modal id="pos-report" ref="pos-report" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">Current Register 
                    <!-- {{  }} -->
                </h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" @click="$bvModal.hide('pos-report')">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="block-content">
                
                <div class="gutters-tiny row">
                    <div class="col-lg-3">Opened At</div>
                    <div class="col-lg-9 value">
                        <span class="font-w700">{{ format_date(register.opened_at) }} WIB</span>
                    </div>
                </div>

                <div class="gutters-tiny row">
                    <div class="col-lg-3">Opened Cash</div>
                    <div class="col-lg-9 value">
                        <span class="font-w700">{{ currency(parseFloat(register.opened_amount)) }}</span>
                    </div>
                </div>

                <div class="gutters-tiny row">
                    <div class="col-lg-3">Staff</div>
                    <div class="col-lg-9 value">
                        <span class="font-w700">{{ register.name }}</span>
                    </div>
                </div>

                <table class="table table-bordered table-sm mt-10">
                    <template v-if="payment">
                        <thead>
                            <th>Payment Method</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                            <tr>

                            </tr>
                            <tr v-for="p in payment" :key="p.id">
                                <td>{{ p.name }}</td>
                                <td>{{ currency((p.payment_count) ? p.payment_count : 0) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Sales</td>
                                <td>{{ currency(total_sales) }}</td>
                            </tr>
                            <tr>
                                <td>Total Payment</td>
                                <td>{{ currency(total_payment) }}</td>
                            </tr>
                        </tfoot>
                    </template>
                </table>

                <div class="form-group">
                    <label for="closing-note">Closing Note</label>
                    <textarea class="form-control" v-model="closing_note" id="closing-note" rows="4"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-noborder btn-block" @click="closeRegister">
                <i class="fa fa-check"></i> Close Register
            </button>
        </div>
    </b-modal>
</template>
<script>
import axios from 'axios';
import moment from 'moment';
export default {
    name : 'pos-report-modal',
    data(){
        return {
            payment : null,
            total_payment : 0,
            total_sales : 0,
            type : "monitor",
            closing_note : null,
            on_hand : 0,
        }
    },
    props :{
        register : Object,
    },
    methods : {
        openModal(){
            this.fetch();
            this.$bvModal.show('pos-report');
        },
        async fetch(){
            const self = this;
            await axios.get(this.route("admin.sale.pos.report"),{

            })
            .then(function (response) {
                self.payment = response.data.payment;
                // self.register = response.data.register;
                self.total_payment = response.data.total_payment;
                self.total_sales = response.data.total_sales;
                self.on_hand = response.data.on_hand;
            })
            .catch(function (error) {
                
            })
        },
        closeRegister(){
            this.$swal.fire({
                title: 'Please Wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            this.$inertia.post(this.route('admin.sale.pos.close'), {
                    amount : this.on_hand,
                    id : this.register.id
                }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$bvModal.hide('pos-report');
                    this.$emit('closed', true)
                    this.$swal.close();
                },
                onError:() => {
                    this.$swal.close();
                }
            });
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MM YYYY hh:mm')
            }
        },
    }
}
</script>