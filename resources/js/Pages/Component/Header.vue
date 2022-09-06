<template>
    <div class="row">
        <div class="col-sm-4 col-3">
            <h4 class="page-title">
                {{ title ? title : 'AFC Hospital' }}
            </h4>
            <div v-if="flash.message" :type="flash.message | success"></div>
            <div v-if="Object.keys(errors).length" :type="errors | error"></div>
        </div>
        <div class="col-sm-8 col-9 text-right m-b-20">
            <a
                :href="route(link)"
                :active="route().current(link)"
                class="btn btn-primary btn-rounded float-right"
            >
                <i class="fa fa-arrow-left new-btn" aria-hidden="true"></i>
                <span class="new-btn"> &nbsp; {{ label }} </span>
            </a>
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
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: toast => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: status
            })
        },
        error (errors) {

        }
    }
}
</script>
