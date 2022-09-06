<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle"><button type="button" @click="printElement()" class="btn btn-primary">Print</button></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-if="bill.id">
                <div class="wrapper " >
                    <!-- Main content -->
                    <section class="invoice"  id="printSection" style="width: 1000px">
                        <!-- title row -->
                        <div class="row border-dark border-bottom pb-2">
                            <div class="col-12">
                                <h3 class="page-header">
                                <span class="float-left">
<!--                                     <img src="assets/img/logo.jpg" style="height: 45px;">-->
                                    <img src="assets/img/logo.jpg"   style="height: 45px;">
                                    <span style="vertical-align: -webkit-baseline-middle">Manipal AFC Hospitals</span>
                                </span>
                                    <small class="float-right">Date: {{ bill.created_at | formatDateTime }}</small>
                                </h3>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- info row -->
                        <div style="display: block!important;text-align: center;" class="row mt-1">
                            <p style="font-size: medium;">
                                <strong>Manipal AFC Hospitals</strong>,
                                Cumilla
                            </p>
                            <p>
                            <h3>BILL INVOICE</h3>
                            </p>
                        </div>
                        <div class="row invoice-info mt-2 border-dark border-bottom">
                            <table class="table" width="100">
                                <tr>
                                    <td>
                                        <address>
                                            <strong>Patient Details:</strong><br>
                                            Patient ID: {{ bill.patient.id }} <br>
                                            Name: {{ bill.patient.name }} <br>
                                            Age: {{ bill.patient.age }} <br>
                                            Sex: {{ bill.patient.gender }} <br>
                                            Phone: {{ bill.patient.mobile }} <br>
                                            Address: {{ bill.patient.address }}
                                        </address>
                                    </td>
                                    <td>
                                        <b>Bill No:</b> {{ bill.bill_no }}<br>
                                        <b>Date:</b> {{ bill.created_at | formatDateTime }}<br>
                                        <b>Payment Status:</b> {{ bill.status }}<br>
                                        <br>
                                        <br>
                                    </td>
                                    <td>
                                        <address>
                                            <strong>Manipal AFC Hospitals,</strong><br>
                                            Cumilla<br>
                                            Phone: 01755-668882, 01708-151493<br>
                                            <br>

                                            <br>
                                            <br>
                                        </address>
                                    </td>
                                </tr>
                            </table>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- Table row -->
                        <div class="row mt-4 border-dark border-bottom">
                            <div class="col-12 table-responsive">
                                <table id="product_table" class="table table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Service Code</th>
                                        <th scope="col">Service</th>
                                        <th scope="col">Tariff</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Discount</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr  v-for="row in bill.bill_details" :key="row.id">
                                        <td>{{row.item_type}}</td>
                                        <td>{{row.item ? row.item.code : null}}</td>
                                        <td>{{row.item_name}}</td>
                                        <td>{{row.unit_tarrif}} tk</td>
                                        <td>{{row.item_qty}}</td>
                                        <td>{{row.discount}} tk</td>
                                        <td>{{row.total_tarrif}} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Total Bill Amount:</td>
                                        <td>{{ bill_amount() }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Discount Amount:</td>
                                        <td>{{ bill.total_discount_amount }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Net Payable Amount:</td>
                                        <td>{{ bill.total_bill_amount }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Total Paid Amount:</td>
                                        <td>{{ bill.total_paid_amount }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Due Amount:</td>
                                        <td>{{ bill.total_bill_amount - bill.total_paid_amount }} tk</td>
                                    </tr>
                                    <tr  v-if="bill.refund_status == 1">
                                        <td colspan="5"></td>
                                        <td>Refund Amount:</td>
                                        <td>{{ bill.refund_amount }} tk</td>
                                    </tr>
                                    <tr  v-if="bill.refund_status == 1">
                                        <td colspan="5"></td>
                                        <td>Refund Remark:</td>
                                        <td>{{ bill.refund_remarks }} </td>
                                    </tr>
                                    <tr  v-if="bill.refund_status == 1">
                                        <td colspan="5"></td>
                                        <td>Net Bill Amount:</td>
                                        <td>{{ bill.total_bill_amount - bill.refund_amount }} tk</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="row mt-4 border-dark border-bottom">
                            <!-- accepted payments column -->
                            <div class="col-6 pt-5 pb-5" style="border-right: 1px solid #000 !important;">

                                <p class="well well-sm font-italic font-weight-bold" style="margin-top: 10px;">
                                    <i class="fa fa-star"><b>Remarks :</b></i> {{ bill.remarks }}
                                </p>
                            </div>
                            <!-- /.col -->
                            <div class="col-6">
                                <p><b>Consultation Details:</b> <br/>
                                    {{ bill.doctor.name }}
                                </p>

                                <p><b>Refereed Physician:</b><br/>
                                    Name: {{ bill.referred_personal ? bill.referred_personal.name : null }}<br/>
                                    ID: {{ bill.referred_personal ? bill.referred_personal.id : null }}
                                </p>

                            </div>
                            <div class="col-12">
                                <p><strong>Bill Prepared By : </strong>{{bill.user ? bill.user.name : null}} </p>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- ./wrapper -->
                <div id="printSections"></div>
            </div>
        </div>
    </div>

</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import {HTTP} from '@/utils/http-common'

import moment from 'moment'
export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['bill'],
    data() {
        return {
            bill_date: moment(this.bill.created_at).format('DD-MMM-YYYY hh:MM a')
        }
    },
    methods: {
        bill_amount(){
            return parseFloat(this.bill.total_bill_amount) + parseFloat(this.bill.total_discount_amount)
        },
        printElement: function () {
            this.$htmlToPaper('printSection');
            // let elem = document.getElementById("printSection")
            // var domClone = elem.cloneNode(true);
            // //
            // var $printSection = document.getElementById("printSections");
            // //
            // if (!$printSection) {
            //     var $printSection = document.createElement("div");
            //     $printSection.id = "printSection";
            //     document.body.appendChild($printSection);
            // }
            //
            // $printSection.innerHTML = "";
            // $printSection.appendChild(domClone);
            // window.print();
            // $printSection.innerHTML = "";
        }
    }
}
</script>
<style>
.modal-content {
    font-size: 15px;
}
.modal-dialog {
    max-width: 1050px;
    /*margin: 1.75rem auto;*/
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
