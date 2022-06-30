<template>
    <BaseLayout>
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Piutang
                
                <div class="float-right">
                    <button type="button" class="btn btn-primary btn-noborder btn-sm mr-2" @click="confirmPayment" v-if="selected.length">
                        <i class="si si-check mr-1"></i>
                        Confirm {{ selected.length }} data
                    </button>

                    <b-dropdown id="download" size="sm">
                    <template #button-content>
                        Download
                    </template>
                    <b-dropdown-item :href="route('admin.accounting.piutang.export', {'type' : 'excel'})" target="_blank">
                        To Excel
                    </b-dropdown-item>
                    <b-dropdown-item :href="route('admin.accounting.piutang.export', {'type' : 'pdf'})" target="_blank">
                        To PDF
                    </b-dropdown-item>
                </b-dropdown>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                </th>
                                <th width="10%">Customer</th>
                                <th width="18%">Total</th>
                                <th width="15%">Last Transaction</th>
                                <th width="9%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="6">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <template v-if="Object.values(dataList.data).length">
                                <tr v-for="(data, i) in dataList.data" :key="i">
                                    <td>
                                        <b-form-checkbox
                                            :id="'data-'+data.customer_id"
                                            v-model="selected"
                                            :name="'data-'+data.customer_id"
                                            :value="data.customer_id"
                                            >
                                        </b-form-checkbox>
                                    </td> 
                                    <td>{{ data.customer.name }}</td>
                                    <td>{{ currency(data.grand_total) }}</td>
                                    <td>{{ format_date(data.date) }}</td>
                                    <th>
                                        <a :href="route('admin.accounting.piutang.show', { id : data.customer_id})" class="btn btn-secondary btn-sm">
                                            <i class="si si-magnifier"></i>
                                            Detail
                                        </a>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td colspan="5">
                                        <div class="text-center">
                                            <img class="img-fluid" :src="asset('images/not_found.png')">
                                            <h3 class="font-size-24 font-w600 mt-3">Data Not Found</h3>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import _ from 'lodash';
import moment from 'moment';
export default {
    components: {
        BaseLayout,
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == '' ? '' : this.route().params.search
        } 
    },
    props: {
        dataList : Object
    },
    methods :{
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch : _.throttle(function(){
            this.$inertia.get(this.route('admin.accounting.piutang.index', { search : this.search }))
        }, 200),
        select() {
			this.selected = [];
			if (!this.selectAll) {
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                    
                });
			}
		},
        confirmPayment(){
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let data = {
                ids : this.selected,
                from : "index",
            }
            let form = this.$inertia.form(data)
            let url = this.route("admin.accounting.piutang.confirm");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Payment confirmed!`,
                        showConfirmButton: false,
                    });
                },
                onFinish: () => {
                    this.$swal.close();
                },
            });
        }
    }
}
</script>
