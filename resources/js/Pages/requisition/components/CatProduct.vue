<template>
<div class="invoice">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="card-title d-inline-block"><b>Requisition Vs Stock</b></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive" v-for="(cat,key,index) in item" :key="index">
                        <table class="table table-striped table-hover">
                            <tr style="background: aquamarine">
                                <td colspan="2">Category Name: <b>{{ key }}</b></td>
                                <td colspan="1" class="float-right">Total Quantity: <b>{{ cat.qty }}</b></td>
                            </tr>
                        </table>

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th class="table-column">Expected Qty</th>
                                    <th class="table-column">Stock Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row,index) in cat.product" :key="index">
                                    <td>{{ index+1 }}</td>
                                    <td>{{ row.product.name }}</td>
                                    <td class="table-column">{{ row.total_expected }}</td>
                                    <td class="table-column">{{ row.stock }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

export default {
    props: ['item'],
    methods: {
    }
}
</script>
<style>
    .price-column {
        text-align: right;
    }
    .table-column {
        text-align: center;
    }
    .vm--modal {
        top: 0px;
        left: 274px;
        width: 1283.1px;
        height: auto!important;
    }
</style>