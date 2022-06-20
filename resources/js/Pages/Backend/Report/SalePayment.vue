<template>
    <BaseLayout title="Admin | Report Profit / Loss">
        <div class="content">
            <h2 class="content-heading">Profit / Loss Report</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="block block-shadow-2 block-rounded">
                        <div class="block-content block-content-full text-right">
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Gross Profit
                            </div>
                            <div class="font-size-h2 font-w700">
                                {{ currency(data.gross_profit) }}
                            </div>
                        </div>
                    </div>

                    <div class="block">
                        <div class="block-content p-0">
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        Opening Stock
                                        <small class="text-muted">(By purchase price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.opening_stock) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Opening Stock
                                        <small class="text-muted">(By sell price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.opening_stock_by_sp) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase Discount
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase_discount) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sale Return
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale_return) }}
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        Total Stock Adjustment
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_stock_adjustment) }}
                                    </td>
                                </tr> -->
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="block block-shadow-2 block-rounded">
                        <div class="block-content block-content-full text-right">
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Net Profit
                            </div>
                            <div class="font-size-h2 font-w700">
                                {{ currency(data.gross_profit) }}
                            </div>
                        </div>
                    </div>
                    <div class="block">
                        <div class="block-content p-0">
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        Closing Stock
                                        <small class="text-muted">(By purchase price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.closing_stock) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Closing Stock
                                        <small class="text-muted">(By sell price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.closing_stock_by_sp) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sales
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sale Delivery Charge
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale_shipping) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase Return
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase_return) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
export default {
    components: {
        BaseLayout,
    },
    props : {
        data : Object,
    },
}
</script>
