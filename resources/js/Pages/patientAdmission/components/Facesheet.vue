<template>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title pull-left" id="exampleModalLongTitle"><button type="button" @click="printElement()" class="btn btn-primary">Print</button></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-if="admission.id > 0">
                <div class="wrapper " >
                    <!-- Main content -->
                    <section class="invoice"  id="printSection" style="width: 1000px">
                        <section class="invoice" style="padding: 0 5%">
                            <!-- title row -->
                            <div class="row border-dark border-bottom pb-2">
                                <div class="col-12">
                                    <h2 class="page-header" style="text-align: center">
                                        <p style="font-size: 20px">{{ admission.hospital.name }}</p>
                                        <p style="font-size: 18px">{{ admission.hospital.address }}</p>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- title row -->
                            <div class="row border-dark border-bottom pb-2">
                                <div class="col-12">
                                    <h2 class="page-header" style="text-align: center">
                                        <p style="font-size: 18px">Admission Form</p>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- info row -->
                            <div class="row invoice-info mt-4 border-dark border-bottom">
                                <div class="col-sm-2 invoice-col patient-div">
                                    <p>Patient Reg. ID</p>
                                    <p>Patient IPID</p>
                                    <p>Patient Name</p>
                                    <p>Age</p>
                                    <p>Religion</p>
                                    <p>Gender</p>
                                    <p>Father's Name</p>
                                    <p>Husband Name</p>
                                    <p>Occupation</p>
                                </div>
                                <div class="col-sm-4 invoice-col patient-details">
                                    <p>: {{ admission.bill_no }}</p>
                                    <p>: {{ admission.bill_no }}</p>
                                    <p>: {{ admission.patient.name }}</p>
                                    <p>: {{ admission.patient.age }}</p>
                                    <p>: {{ admission.patient.religion }}</p>
                                    <p>: {{ admission.patient.gender }}</p>
                                    <p>: {{ admission.patient.father_name }}</p>
                                    <p>: {{ admission.patient.husband_name }}</p>
                                    <p>: {{ admission.patient.occupation }}</p>
                                </div>
                                <div class="col-sm-2 invoice-col patient-div">
                                    <p>Adm.Date</p>
                                    <p>Time</p>
                                    <p>Admitted From</p>
                                    <p>Telephone</p>
                                    <p>Present Address</p>
                                    <p>Relative's Name</p>
                                    <p>Relation</p>
                                    <p>Relative's Mobile No. :</p>
                                </div>
                                <div class="col-sm-4 invoice-col patient-details">
                                    <p>: {{ admission.admission_date | formatDateTime }}</p>
                                    <p>: {{ admission.created_at | formatTime }}</p>
                                    <p>: {{ admission.admitted_from }}</p>
                                    <p>: {{ admission.patient.mobile }}</p>
                                    <p>: {{ admission.patient.address }}</p>
                                    <p>: {{ admission.patient.contact_person_name }}</p>
                                    <p>: {{ admission.patient.contact_person_relation }}</p>
                                    <p>: {{ admission.patient.contact_person_mobile }}</p>
                                </div>
                            </div>
                            <!-- /.row -->

                            <!-- title row -->
                            <div class="row border-dark border-bottom pb-2">
                                <div class="col-12">
                                    <h2 class="page-header" style="text-align: center">
                                        <p style="font-size: 18px">Bed Information</p>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- info row -->
                            <div class="row invoice-info mt-4 border-dark border-bottom">
                                <div class="col-sm-4 invoice-col patient-div">
                                    <p>Admitted for Medical Management/ Package</p>
                                </div>
                                <div class="col-sm-4 invoice-col patient-details">
                                    <p>: {{ admission.package ? admission.package.name : 'N/A' }}</p>
                                </div>
                                <div class="col-sm-1 invoice-col patient-div">
                                    <p>Bed No.</p>
                                </div>
                                <div class="col-sm-3 invoice-col patient-details">
                                    <p>: {{ admission.room }}</p>
                                </div>
                            </div>
                            <!-- /.row -->

                            <!-- title row -->
                            <div class="row border-dark border-bottom pb-2">
                                <div class="col-12">
                                    <h2 class="page-header" style="text-align: center">
                                        <p style="font-size: 18px">Doctor's Information</p>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- info row -->
                            <div class="row invoice-info mt-4 border-dark border-bottom">

                                <div class="col-sm-2 invoice-col patient-div">
                                    <p>Under Dr</p>
                                    <p>Remarks</p>
                                </div>
                                <div class="col-sm-4 invoice-col patient-details">
                                    <p>: {{ admission.doctor.name }}</p>
                                    <p>: {{ admission.description }}</p>
                                </div>
                                <div class="col-sm-2 invoice-col patient-div">
                                    <p>Referred By</p>
                                    <p>Referred Personal</p>
                                </div>
                                <div class="col-sm-2 invoice-col patient-details">
                                    <p>: {{ admission.referred_by }}</p>
                                    <p>: {{ admission.referred_personal ? admission.referred_personal.name : null }}</p>
                                </div>



                            </div>
                            <!-- /.row -->


                            <div class="row mt-4 border-dark border-bottom">
                                <!-- accepted payments column -->
                                <div class="col-12">
                                    <p>I / We do hereby abide by the rules and regulation of the Hospital. The above information given by myself is true and if
                                        my information proven to be false i shall be liable for that. I also know about treatment procedure and expenses,which
                                        are explained both oral &amp; written form.</p>
                                </div>
                            </div>

                            <div class="row mt-4 border-dark border-bottom">
                                <!-- accepted payments column -->
                                <div class="col-4">
                                    <p>
                                        ---------------------------------- <br>
                                        Full Name in Block Letter &amp; Signature of EMO
                                    </p>
                                </div>
                                <div class="col-4">
                                    <p>
                                        ----------------------------------- <br>
                                        Signature of Guardian
                                    </p>
                                </div>
                                <div class="col-4">
                                    <p><strong>Admitted By : </strong>{{admission.user ? admission.user.name : null}} </p>
                                </div>

                            </div>
                            <!-- /.row -->
                        </section>
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
    props: ['admission'],
    data() {
        return {
            bill_date: moment(this.admission.created_at).format('YYYY-MM-DD')
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
