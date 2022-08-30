<template>
    <BaseLayout title="Product List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Products
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger">
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <a class="btn btn-sm btn-secondary" :href="route('admin.product.create')" v-if="hasPermission('Product', 'create')">
                        <i class="si si-plus"></i>
                        Create
                    </a>
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
                                <img class="media-object" :src="value.main_image" style="max-width:45px">
                                <div class="pl-3">
                                    <div class="font-w600">{{ value.name }}</div>
                                    {{ value.category.name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ currency(value.sell_price) }} 
                            <template v-if="value.variant.length > 1 && value.sell_price != value.sell_max_price">
                            ~ {{ currency(value.sell_max_price) }}
                            </template>
                        </td>
                        <td>
                            {{ currency(value.purchase_price) }} 
                        </td>
                        <td>{{ (value.stock) ? value.stock : 0 }}</td>
                        <td>{{ (value.sold) }}</td>
                        <td>
                            <b-dropdown text="Action" class="m-md-2"  size="sm">
                                <a :href="route('admin.product.show', {id : value.id})" class="dropdown-item">
                                    <i class="si si-eye mr-3"></i>
                                    Detail
                                </a>
                                <a :href="route('admin.product.edit', {id : value.id})" class="dropdown-item">
                                    <i class="si si-note mr-3"></i>
                                    Edit
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item" @click="updateStock(value)">
                                    <i class="si si-note mr-3"></i>
                                    Update Stock
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item" @click="remove(value.id)">
                                    <i class="si si-trash mr-3"></i>
                                    Delete
                                </a>
                            </b-dropdown>
                        </td>
                    </tr>
                </template>
            </base-table>
        </div>
        <product-stock-update ref="ProductStockUpdate"/>
    
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import _ from 'lodash';
import BaseTable from '@/components/Table/BaseTable.vue';
import ProductStockUpdate from './UpdateStock.vue';
export default {
    components: {
        BaseLayout,
        BaseTable,
        Link,
        ProductStockUpdate,
    },
    data(){
        return {
            loading : false,
            selected: [],
            columns : [
                {
                    name : "Product",
                    field : "name",
                    width : "46%",
                },
                {
                    name : 'Sell Price',
                    field : 'sell_price',
                    width : "15%",
                },
                {
                    name : "Purchase Price",
                    field : 'purchase_price',
                    width : "15%",
                },
                {
                    name : "Stock",
                    field : 'stock',
                    width : "10%",
                },
                {
                    name : "Total Sold",
                    field : 'sold',
                    width : "17%",
                },
            ],
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
        updateStock(product){
            this.$refs.ProductStockUpdate.openModal(product);
        },
        remove(id){
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.product.delete', {id : id}), {
                        preserveScroll: true
                    })
                }
            })
        },
    }
}
</script>
