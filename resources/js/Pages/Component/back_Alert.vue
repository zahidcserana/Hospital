<template>
    <div class="row">
        <div class="col-sm-4 col-3">
            <h4 class="page-title">
                {{ title ? title : 'AFC Hospital' }}
            </h4>
            <div v-if="flash.message" :type="flash.message | success">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ flash.message }}
                </div>
            </div>
            <div v-if="Object.keys(errors).length" :type="errors | error">
                <ul class="alert alert-danger alert-dismissible fade show">
                    <li v-for="(item, i) in errors" :key="i">
                        {{ item[0] }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-8 col-9 text-right m-b-20">
            <jet-responsive-nav-link
                :href="route(link)"
                :active="route().current(link)"
                class="btn btn-primary btn-rounded float-right"
            >
                <i class="fa fa-arrow-left new-btn" aria-hidden="true"></i>
                <span class="new-btn"> &nbsp; {{ label }} </span>
            </jet-responsive-nav-link>
        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import swal from 'sweetalert2'
window.Swal = swal

export default {
    components: {
        JetResponsiveNavLink
    },
    props: ['errors', 'title', 'flash', 'link', 'label'],
    filters: {
        success (status) {
            Swal.fire({
                icon: 'success',
                title: status,
                showConfirmButton: false,
                timer: 1500
            })
        },
        error (errors) {
            let list = []
            $.each(errors, function (key, value) {
                list.push(value)
            })

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: list[0],
                showConfirmButton: false,
                timer: 1500
            })
        }
    }
}
</script>
