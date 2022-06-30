<template>
    <BaseLayout>
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Detail Piutang <small>{{ customer.name }}</small>
                
                <div class="float-right">
                    <a :href="route('admin.accounting.piutang.pdf', {id : customer.id })" class="btn btn-sm btn-secondary" target="_blank">Print PDF</a>
                </div>
            </div>
            <div class="block block-rounded block-shadow">
                <div class="block-content">
                    <div>Name :  <span class="font-w700">{{ customer.name }}</span></div>
                    <div>NIP :  <span class="font-w700">{{ customer.nip }}</span></div>

                    <hr class="border-2x">
                    <table class="table table-bordered table-sm">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Amount Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(d, i) in data" :key="i">
                                <td class="text-center">{{ i+1 }}</td>
                                <td>
                                    <a :href="route('admin.sale.order.show', {id : d.id})">{{ d.ref }}</a>
                                </td>
                                <td>{{ format_date(d.date) }}</td>
                                <td>{{ currency(d.grand_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
export default {
    components: {
        BaseLayout,
    },
    props : {
        customer : Object,
        data : Array,
    },
    methods : {
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMMM YYYY')
            }
        },
    }
}
</script>
