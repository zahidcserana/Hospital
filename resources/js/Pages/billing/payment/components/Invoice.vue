<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle"><button type="button" @click="printElement()" class="btn btn-primary">Print</button></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"  v-if="invoice.id">
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
                                    <small class="float-right">Date: {{ invoice.created_at | formatDateTime }}</small>
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
                            <h3>MONEY RECEIPT</h3>
                            </p>
                        </div>
                        <div class="row invoice-info mt-2 border-dark border-bottom">
                            <table class="table" width="100">
                                <tr>
                                    <td>
                                        <address>
                                            <strong>Patient Details:</strong><br>
                                            Patient Code: {{ invoice.ipd_billing ? invoice.ipd_billing.bill_no : invoice.patient.code }} <br>
                                            Patient ID: {{ invoice.patient.id }} <br>
                                            Name: {{ invoice.patient.name }} <br>
                                            Age: {{ invoice.patient.age }} <br>
                                            Sex: {{ invoice.patient.gender }} <br>
                                            Phone: {{ invoice.patient.mobile }} <br>
                                            Address: {{ invoice.patient.address }}
                                        </address>
                                    </td>
                                    <td>
                                        <b>Receipt ID:</b> {{ invoice.id }}<br>
                                        <b>Date:</b> {{ invoice.created_at | formatDateTime }}<br>
                                        <b>Payment Status:</b> {{ invoice.status }}<br>
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
                                        <th scope="col">Bill No.</th>
                                        <th scope="col">Payment Amount</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ invoice.billing_type }}</td>
                                        <td>{{invoice.bill.bill_no}}</td>
                                        <td>{{invoice.payment_amount}} tk</td>
                                        <td>{{invoice.payment_amount}} tk</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Total:</td>
                                        <td>{{invoice.payment_amount}} tk</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Total Bill Amount:</td>
                                        <td>{{ invoice.bill.total_bill_amount }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Total Paid Amount:</td>
                                        <td>{{ invoice.bill.total_paid_amount }} tk</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Due Amount:</td>
                                        <td>{{ invoice.bill.total_bill_amount - invoice.bill.total_paid_amount }} tk</td>
                                    </tr>

                                    <tr  v-if="invoice.bill.refund_status == 1">
                                        <td colspan="2"></td>
                                        <td>Refund Amount:</td>
                                        <td>{{ invoice.bill.refund_amount }} tk</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>

                        <div class="col-12">
                            <p><strong>Receipt Prepared By : </strong>{{invoice.created_by_user ? invoice.created_by_user.name : null}} </p>
                        </div>
                        <!-- /.row -->

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
import moment from 'moment'
export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['invoice'],
    data() {
        return {
            date: moment(this.invoice.created_at).format('DD-MMM-YYYY hh:mm a')
        }
    },
    methods: {
        printElement: function () {
            let elem = document.getElementById("printSection")
            var domClone = elem.cloneNode(true);
            //
            var $printSection = document.getElementById("printSections");
            //
            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            $printSection.innerHTML = "";
            $printSection.appendChild(domClone);
            window.print();
            $printSection.innerHTML = "";
        }
    }
}
</script>
<style>

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
