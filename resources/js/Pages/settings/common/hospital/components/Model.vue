<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Name</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.name"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital District</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.district"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Contact</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.contact"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Email</label>
                                    <input
                                        class="form-control"
                                        type="email"
                                        v-model="form.email"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Mobile No.</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.mobile_no"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital TNT No.</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.tnt_no"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Website URL</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.website_url"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Contact Person Name</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.contact_person_name"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Contact Person Mobile</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="form.contact_person_mobile"
                                    />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Hospital Address</label>
                                    <textarea
                                        v-model="form.address"
                                        cols="30"
                                        rows="4"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Hospital Status</label><br />
                                <div class="form-check form-check-inline">
                                    <input
                                        v-model="form.status"
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        value="ACTIVE"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="product_active"
                                    >
                                        Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        v-model="form.status"
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        value="INACTIVE"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="product_inactive"
                                    >
                                        Inactive
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="m-t-20 text-center">
                            <button
                                type="button"
                                class="btn btn-primary submit-btn"
                                wire:click.prevent="store()"
                                v-show="!editMode"
                                @click="save(form)"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                class="btn btn-primary submit-btn"
                                wire:click.prevent="store()"
                                v-show="editMode"
                                @click="update(form)"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'

export default {
    components: {
        Alert
    },
    props: ['param', 'editMode'],
    data () {
        return {
            form: {
                id: undefined,
                name: null,
                district: null,
                contact: null,
                email: null,
                website_url: null,
                mobile_no: null,
                tnt_no: null,
                contact_person_name: null,
                contact_person_mobile: null,
                address: null,
                status: 'ACTIVE'
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form.id = this.param.data.id
            this.form.name = this.param.data.name
            this.form.district = this.param.data.district
            this.form.address = this.param.data.address
            this.form.contact = this.param.data.contact
            this.form.email = this.param.data.email
            this.form.website_url = this.param.data.website_url
            this.form.mobile_no = this.param.data.mobile_no
            this.form.tnt_no = this.param.data.tnt_no
            this.form.contact_person_name = this.param.data.contact_person_name
            this.form.contact_person_mobile = this.param.data.contact_person_mobile
            this.form.status = this.param.data.status
        }
    },
    methods: {
        reset: function () {
            this.form = {
                name: null,
                district: null,
                address: null,
                contact: null,
                email: null,
                website_url: null,
                mobile_no: null,
                tnt_no: null,
                contact_person_name: null,
                contact_person_mobile: null,
                status: 'ACTIVE'
            }
        },
        save: function (data) {
            this.$inertia.post('/hospitals', data)
        },
        update: function (data) {
            data._method = 'PUT'
            this.$inertia.post('/hospitals/' + data.id, data)
        }
    }
}
</script>
