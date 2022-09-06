<template>
<div class="invoice">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>
                                    <img src="assets/img/logo-dark.png" class="inv-logo" alt="">
                                </td>
                                <td colspan="3" class="text-left"><h4>{{ settings.title }}</h4></td>
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
                                <td colspan="1" style="padding-left: 15%;">
                                    <h5>Created By:</h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <h5>Name: <strong>{{ item.user.name }}</strong></h5>
                                        </li>
                                        <li>Email: {{ item.user.email }}</li>
                                        <li>Mobile: {{ item.user.mobile }}</li>
                                    </ul>
                                </td>
                                <td colspan="1">
                                    <div class="invoice-details">
                                        <h4 class="text-uppercase">Invoice: {{ item.requisition_no }}</h4>
                                        <ul class="list-unstyled">
                                            <li>Date: <span>{{ item.created_at | myDateFormat }}</span></li>
                                            <li v-if="item.type=='1'">Purchase From: <span>{{ item.purchase_from == 'HEAD_OFFICE' ? 'Head Office' : 'Local' }}</span></li>
                                            <li v-if="item.type=='1' && item.purchase_type">Purchase Type: <span>{{ item.purchase_type == 'WORK_ORDER' ? 'Work Order' : 'Cash' }}</span></li>
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
                                    <th v-if="item.type==1" class="price-column">Unit Cost</th>
                                    <th class="table-column">Exp Qty</th>
                                    <th class="table-column">Delivered Qty</th>
                                    <th class="table-column">ROL(Pcs)</th>
                                    <th class="table-column">Received Qty</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row,index) in item.items" :key="index">
                                    <td>{{ index+1 }}</td>
                                    <td>{{ row.product.name }}</td>
                                    <td v-if="item.type==1" class="price-column">{{ row.tp }}</td>
                                    <td class="table-column">{{ row.expected_quantity }}</td>
                                    <td class="table-column">{{ row.delivered_quantity }}</td>
                                    <td class="table-column">{{ row.reorder_label }}</td>
                                    <td class="table-column">{{ row.received_quantity }}</td>
                                    <td>
                                        <div v-if="row.remarks && row.remarks.length > 8" class="readmore">
                                            <span>{{ row.remarks.substring(0,8) }}</span>
                                            <span class="ellipsis">...</span>
                                            <span class="moreText">{{ row.remarks.substring(8) }}</span>
                                            <a class="more read-more" :id="'more_' + row.id" @click="getFullText(row.id)">show more</a>
                                        </div>
                                        <div v-else>
                                            {{ row.remarks }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="custom-badge status-orange" v-if="row.status==='CANCELED'">{{ row.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="print-btn">
                <div v-if="item.nextStatus && item.statusAccess">
                    <button v-if="item.nextStatus.key !== 'RECEIVED'" type="button" @click="submitRequisition(item.id, item.nextStatus.key)" class="btn btn-success"> {{ item.nextStatus.value }} </button>
                    <button v-if="$page.auth.user.role=='GM'" type="button" @click="localPurchase(item.id)" class="btn btn-info">Authorized for Local Purchase </button>
                </div>
                <button type="button" @click="printElement()" class="btn btn-primary btn-status">Print</button>
            </div>
        </div>
    </div>

    <div id="requisition-print">
        <InvoicePrint id="printThis" style="width: 250%" :item="item" />
    </div>
</div>
</template>

<script>
import InvoicePrint from './InvoicePrint'

export default {
    components: { InvoicePrint },
    props: ['item'],
    data () {
        return {
            form: {
                status: null,
                expected_date: undefined,
                supplier_id: '',
                remarks: null,
            }
        }
    },
    computed: {
        settings() {
            const settings = this.$page.appSettings
            return settings
        }
    },
    methods: {
        printElement: function () {
            let elem = document.getElementById("printThis")
            var domClone = elem.cloneNode(true);

            var $printSection = document.getElementById("printSection");

            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            $printSection.innerHTML = "";
            $printSection.appendChild(domClone);
            window.print();
        },
        submitRequisition: function (id, status) {
            this.form._method = 'PUT'
            this.form.id = id
            this.form.status = status
            this.$inertia.post('/requisitions/' + id, this.form)
        },
        localPurchase: function (id) {
            this.form._method = 'PUT'
            this.form.id = id
            this.form.purchase_from = 'LOCAL'
            this.form.status = 'LOCAL_PURCHASE'
            this.$inertia.post('/requisitions/' + id, this.form)
        },
        getFullText(divId) {
            var $parent = $('#more_'+divId).parent();

            if($parent.data('visible')) {
                $parent.data('visible', false).find('.ellipsis').show()
                .end().find('.moreText').hide()
                .end().find('a.more').text('show more');
            } else {
                $parent.data('visible', true).find('.ellipsis').hide()
                .end().find('.moreText').show()
                .end().find('a.more').text('show less');
            }
        }
    }
}
</script>
<style>
    .read-more{
        text-decoration: underline!important;
        color: darkcyan!important;
        cursor: pointer;
    }
    .readmore {
        width:200px;
    }
    .readmore .moreText {
        display:none;
    }
    .readmore a.more {
        display:block;
    }

    /* Model-view */
    .vm--modal{
        max-height: calc(100vh - 210px);
        overflow-y: auto;
    }
    .price-column {
        text-align: right;
    }
    .table-column {
        text-align: center;
    }

    #requisition-print {
        display: none;
    }
    #print-btn {
        text-align: center;
    }
    .btn-status{
        float: right;
        margin-right: 5px;
        margin-bottom: 5px;
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