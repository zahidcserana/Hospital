<template>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <tr>
                <td colspan="2">
                    <img src="assets/img/logo-dark.png" class="inv-logo" alt="">
                </td>
                <td colspan="2"><h4>{{ settings.title }}</h4></td>
            </tr>
            <tr>
                <td colspan="2">
                    <h5>Requisition From:</h5>
                    <ul class="list-unstyled">
                        <li>
                            <h5><strong>{{ item.hospital.name }} ({{ item.hospital.code }})</strong></h5>
                        </li>
                        <li><span>{{ item.hospital.district }}</span></li>
                        <li>{{ item.hospital.address }}</li>
                        <li>{{ item.hospital.email }}</li>
                        <li>{{ item.hospital.contact_person_mobile }}</li>
                        <li><a :href="item.hospital.website_url">{{ item.hospital.website_url }}</a></li>
                    </ul>
                </td>
                <td colspan="2">
                    <div class="invoice-details">
                        <h3 class="text-uppercase">Invoice: {{ item.id }}</h3>
                        <ul class="list-unstyled">
                            <li>Date: <span>{{ item.created_at | myDateFormat }}</span></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th v-if="item.type==1" class="price-column">Unit Price</th>
                    <th class="table-column">Quantity</th>
                    <th class="table-column">ROL(Pcs)</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row,index) in item.items" :key="index">
                    <td>{{ index+1 }}</td>
                    <td>{{ row.product.name }}</td>
                    <td v-if="item.type==1" class="price-column">{{ row.tp }}</td>
                    <td class="table-column">{{ row.expected_quantity }}</td>
                    <td class="table-column">{{ row.reorder_label }}</td>
                    <td>{{ row.remarks }}</td>
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
    .price-column {
        text-align: right;
    }
    .table-column {
        text-align: center;
    }

    #requisition-print {
        display: none;
    }
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