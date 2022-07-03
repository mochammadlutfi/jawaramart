<template>
    <BaseLayout title="Customer List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Customer List
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger" >
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                </div>
            </div>

            <base-table :values="dataList" :columns="columns" checkbox>
                <template #rowCheck>
                    <b-form-checkbox id="selectAll" name="selectAll" @change="selectAll"></b-form-checkbox>
                </template>
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>
                            <b-form-checkbox
                                :id="'data-'+value.id"
                                v-model="selected"
                                :name="'data-'+value.id"
                                :value="value.id"
                                >
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div class="d-flex">
                                <img class="img-avatar  img-avatar32" :src="value.avatar_url" style="max-width:45px">
                                <div class="pl-3">
                                    {{ value.name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ value.email }}
                        </td>
                        <td>
                            {{ value.status }}
                        </td>
                        <td>{{ currency(parseFloat(value.sale_count)) }}</td>
                        <td>
                            <a :href="route('admin.customer.show', { id : value.id})" class="btn btn-sm btn-secondary">
                                <i class="si si-eye mr-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                </template>
            </base-table>

        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import BaseTable from '@/components/Table/BaseTable.vue';

export default {
    components: {
        BaseLayout,
        BaseTable
    },
    data(){
        return {
            columns : [
                {
                    name : "Name",
                    field : "name",
                    width : "40%",
                },
                {
                    name : 'Email',
                    field : 'email',
                    width : "20%",
                },
                {
                    name : "Status",
                    field : 'status',
                    width : "15%",
                },
                {
                    name : "Total Spending",
                    field : 'total_spending',
                    width : "15%",
                }
            ],
            selected : [],
        } 
    },
    watch: {
        search: function () {
            this.doSearch() 
        },
        selectAll: function(val){
			this.selected = [];
            if(val){
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                });
            }
        }
    },
    props: {
        dataList: Object,
    },
    methods :{
        selectAll(e){
            if(e){
                this.dataList.data.forEach((v, i) => {
                    this.selected.push(v.id)
                });
            }else{ 
                this.selected = [];
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
    }
}
</script>
