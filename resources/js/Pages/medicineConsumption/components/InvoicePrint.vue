<template>
    <div class="medicine-consumption">
        <div class="row mb-5">
            <div class="col-4"><h5>Medicine Consumption</h5></div>
            <div class="col-4"><h4>{{ settings.title }}</h4></div>
            <div class="col-4"><h5>Date: {{ item[0].date | myDateFormat }}</h5></div>
        </div>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Product Name</th>
                    <th>Consumption Qty</th>
                    <th>Consumption For</th>
                    <th>Remarks</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row,index) in item" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ row.medicine.name }}</td>
                    <td>{{ row.quantity }}</td>
                    <td>{{ row.product_for }}</td>
                    <td>{{ row.remarks }}</td>
                    <td>{{ row.date | myDateFormat }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    components: {},
    props: ['item'],
    computed: {
        settings() {
            return this.$page.appSettings
        }
    },
    methods: {}
}
</script>
<style>
    @media screen {
        #printSection {
            display: none;
        }
    }
    @media print {
        body * {
            visibility:hidden;
        }
        #printSection, #printSection * {
            visibility:visible;
        }
        #printSection {
            position:absolute;
            left:0;
            top:0;
        }
    }
</style>